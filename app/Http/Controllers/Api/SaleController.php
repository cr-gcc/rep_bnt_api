<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Helpers\CustomerData;
use App\Services\DataBasesServices;
use App\Models\Campaign;
use App\Models\Status;
use App\Http\Requests\Sales\GetDatesRequest;
use App\Http\Requests\Sales\SearchRequest;
use App\Http\Requests\Sales\DeleteRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SaleController extends Controller
{

	public function getGeneralCounts(GetDatesRequest $request)
	{
		$request->validated();
		//
		$start_date = $request->start_date;
		$end_date =  $request->end_date;
		$colleccion_campaigns = collect();
		$totales = collect();
		//	Seleccion de campañas
		$campaigns = Campaign::where('active', true)->get();
		//	Todos los registros
		foreach ($campaigns as $campaign) {
			$db = app(DataBasesServices::class)->connectionTo($campaign->db_name);
			//	Check de conexion
			if (!$db) {
				continue;
			}
			$db_sales = $db->table('ventas')
				->whereBetween('fecha_venta', [$start_date, $end_date])
				->get();

			$colleccion_campaigns->put($campaign->name, [
				'sales' => collect($db_sales),
				'system_name' => $campaign->system_name
			]);
		}
		//	Filtrado
		$totales = $colleccion_campaigns->map(function ($data, $name) {
			$statusMap = Status::pluck('slug', 'code')->toArray();
			$sales = $data['sales'];
			$app = $data['system_name'];
			// Inicializar contadores dinámicamente
			$counts = [];
			foreach ($statusMap as $code => $slug) {
				$counts[$slug] = $sales->where('validacion1', $code)->count();
			}
			$total = array_sum($counts);
			// Calcular porcentaje de NAP (rechazadas)
			$napCount = $counts['nap'] ?? 0;
			$pnap = ($total > 0 && $napCount > 0) ? round(($napCount / $total) * 100, 2) : 0;

			$result = [
				'campaign' => $name,
				'app'  => $app,
				'pen'  => $counts['pen'],
				'ap'   => $counts['ap'],
				'nap'  => $counts['nap'],
				'hold' => $counts['hold'],
				'aop'  => $counts['aop'],
				'rec'  => $counts['rec'],
				'percent_nap' => $pnap,
				'total' => $total
			];

			return $result;
		})->values();

		return response()->json($totales, 200);
	}

	public function search(SearchRequest $request)
	{
		$request->validated();
		$sale_date = $request->input('sale_date', '');
		$campaigns = $request->input('campaigns', []);
		$status = $request->input('status', []);
		$databasesRTMK = ['banorte_rtmk'];
		$sales_data = [];
		$sales = [];

		//	Obtener campañas
		$all_campaigns = (int)$campaigns[0];
		if ($all_campaigns == 0) {
			$query_campaigns = Campaign::where('active', true)->get();
		} else {
			$query_campaigns = Campaign::whereIn('id', $campaigns)->get();
		}
		//	Obtener estatus
		$all_status = (int)$status[0];
		if ($all_status == 0) {
			$query_status = Status::all();
		} else {
			$query_status = Status::whereIn('id', $status)->get();
		}

		// Crear mapeo de status: code => objeto Status
		$statusMap = $query_status->keyBy('code');

		// Lectura de base de datos
		foreach ($query_campaigns as $campaign) {
			$db = app(DataBasesServices::class)->connectionTo($campaign->db_name);
			if (!$db) {
				continue;
			}
			// Campos dinámicos de cliente / asegurado
			$data_client = CustomerData::getCDBySale($campaign->db_name);
			// Validar existencia de columna poliza_captura
			$isBntRtmk = in_array($campaign->db_name, $databasesRTMK);
			// Select dinámico
			$select = [
				'ventas.certificado as certificado',
				'clientes.id_cliente',
				'clientes.id_predictivo',
				'clientes.vendor_id as sig',
				'catalogo_calificaciones_validacion.descripcion_grupo as estatus',
				'catalogo_calificaciones_validacion.calificacion as calificacion',
				// Cliente / asegurado
				DB::raw($data_client['cliente']),
				DB::raw($data_client['asegurado']),
				// Agente
				DB::raw("CONCAT_WS(' ', usuarios.nombre, usuarios.paterno, usuarios.materno) as agente"),
				// Metadata
				DB::raw("'{$campaign->name}' as campaign"),
				DB::raw("'{$campaign->system_name}' as app"),
				DB::raw("'{$campaign->db_name}' as base"),
			];
			//
			if ($isBntRtmk) {
				$select[] = DB::raw("'' as poliza");
				$select[] = DB::raw("CONCAT_WS(' ', ventas.fecha_venta, ventas.hora) as fecha_venta");
			} else {
				$select[] = DB::raw("COALESCE(ventas.poliza_captura, '') as poliza");
				$select[] = DB::raw("CONCAT_WS(' ', ventas.fecha_venta, ventas.hora_venta) as fecha_venta");
			}
			// Query
			$db_sales = $db->table('ventas')
				->join('clientes', 'clientes.id_cliente', '=', 'ventas.id_cliente')
				->leftJoin('usuarios', 'usuarios.id_usuario', '=', 'ventas.id_usuario')
				->leftJoin('catalogo_calificaciones_validacion', 'catalogo_calificaciones_validacion.id_calificacion', '=', 'ventas.validacion2')
				->select($select)
				->where('ventas.fecha_venta', $sale_date);
			//
			if ($query_status) {
				$db_sales->whereIn(
					'ventas.validacion1',
					$query_status->pluck('code')->toArray()
				);
			}
			//
			$tmp = $db_sales->count();
			if ($tmp) {
				$sales_data[] = $db_sales->get();
			}
		}
	
		foreach ($sales_data as $items) {
			foreach ($items as $item) {
				$sales[] = $item;
			}
		}
		
		return response()->json($sales, 200);
	}

	public function delete(DeleteRequest $request)
	{
		$request->validated();

		$commond_fields = "id_producto, certificado, id_plan, id_cobertura, id_tipo_persona, precio_plan, poliza_bancomer, num_operacion, tipo_pago, paterno, materno, nombres, nombres2, fecha_nacimiento, id_estado_nac, id_nacionalidad, id_ocupacion_tipo, id_ocupacion, edad, sexo, estado_civil, id_parentesco, rfc, calle, numero_exterior, numero_interior, ecalle, colonia, ciudad, municipio_ciudad, estado, cp, id_horario, lada1, telefono1, lada2, telefono2, telcel, mail1, mail2, tdc, vencimiento_tdc, tipo_tdc, id_banco, codseg, titular_tdc, aplicaTDCadicional, tdc_adi, vencimiento_tdc_adi, tipo_tdc_adi, id_banco_adi, codseg_adi, titular_tdc_adi, account_type, fecha_venta, hora_venta, fecha_valida, hora_valida, fecha_envio, estatus_poliza, id_usuario, ip_usuario, id_validador, ip_validador, id_supervisor, id_cliente, aceptaVenta, tipo_venta, validacion1, validacion2, comentario_validacion, comentario_validacion_2, sip_extension, user_predictivo, aplicaAsegurado, nombre_adi, nombre_adi2, paterno_adi, materno_adi, fecha_nacimiento_adi, sexo_adi, edad_adi, rfc_adi, lada3, telefono3, lada4, telefono4, no_autorizacion, id_plaforma_cobro, estado_civil_ase, start_time, end_time, length_in_sec, filename, location, inicio_validacion, fchnactecleo, fchnacDigitos, fchnactecleo_motivo, utilizada, fecha_utilizada, tipo_venta_validacion, poliza_captura, llamada, recording_id, vicidial_id";
		$all_fields = "INSERT INTO ventas_eliminadas SELECT * FROM ventas WHERE certificado = ?";
		$select_fields = "INSERT INTO ventas_eliminadas (" . $commond_fields . ") SELECT " . $commond_fields . " FROM ventas WHERE certificado = ?";
		$all_fields_db = ['banorte_plenitud', 'banorte_tdc_arquetipos', 'banorte_rastreator', 'banorte_auto_seguro', 'banorte_clientes_auto', 'banorte_segurosdeautomexico'];
		$select_fields_db = ['banorte_captacion_pvh', 'banorte_consumo_pvh', 'banorte_nomina_pvh', 'banorte_pvh'];
		$fields = "";
		$ok_flag = true;
		$errors = [];
		$messages = [];

		$delete_list = $request->input('list', []);
		foreach ($delete_list as $item) {
			$db = app(DataBasesServices::class)->connectionTo($item[0]);
			//	Verificar si la base de datos es correcta
			if (in_array($item[0], $select_fields_db)) {
				$fields = $select_fields;
				$ok_flag = true;
			} else if (in_array($item[0], $all_fields_db)) {
				$fields = $all_fields;
				$ok_flag = true;
			} else {
				$ok_flag = false;
			}
			//	Si se puede eliminar	
			if ($ok_flag) {
				try {
					//	Insertar en la base de datos de eliminados
					$db->statement($fields, [$item[1]]);
					//	Eliminar de la base de datos
					$db->table('ventas')
						->where('certificado', $item[1])
						->delete();
					$messages[] = 'Venta con certificado [' . $item[1] . '] eliminada correctamente.';
				} catch (\Exception $e) {
					$errors[] = 'Venta con certificado [' . $item[1] . '] no se pudo eliminar.';
				}
			}
		}
		//
		return response()->json([
			'messages' => $messages,
			'errors' => $errors,
		]);
	}
}
