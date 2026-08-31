<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Helpers\CustomerData;
use App\Services\DataBasesServices;
use App\Models\Campaign;
use App\Models\Status;
use App\Models\LogDeletedSale;
use App\Http\Requests\Sales\GetDatesRequest;
use App\Http\Requests\Sales\SearchRequest;
use App\Http\Requests\Sales\DeleteRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SaleController extends Controller
{
	public function getGeneralCounts(GetDatesRequest $request)
	{
		$start_date = $request->start_date;
		$end_date =  $request->end_date;
		$colleccion_campaigns = collect();
		//
		$user = auth()->user();
		//	Seleccion de campañas
		//	Asumiendo 1 rol por usuario
		$role = $user->roles->first();
		$campaigns = $role ? $role->campaigns->where('active', true) : collect();
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
		$campaign_counts = $colleccion_campaigns->map(function ($data, $name) {
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


		$tmp_totales = [
			'pen' => $campaign_counts->sum('pen'),
			'ap' => $campaign_counts->sum('ap'),
			'nap' => $campaign_counts->sum('nap'),
			'hold' => $campaign_counts->sum('hold'),
			'aop' => $campaign_counts->sum('aop'),
			'rec' => $campaign_counts->sum('rec'),
			'total' => $campaign_counts->sum('total')
		];
		$pnap_total = ($tmp_totales['total'] > 0 && $tmp_totales['nap'] > 0) ? round(($tmp_totales['nap'] / $tmp_totales['total']) * 100, 2) : 0;
		$tmp_totales['percent_nap'] = $pnap_total;
		//
		$data = [
			'counts' => $campaign_counts,
			'totals' => $tmp_totales
		];
		return response()->json($data, 200);
	}

	public function search(SearchRequest $request)
	{
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
				'clientes.id_predictivo as lead_id',
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
		$delete_list = $request->input('list', []);
		$errors = [];
		$messages = [];
		$status = 400;

		// Agrupacion de certificados por base de datos
		$grouped_by_db = [];
		foreach ($delete_list as $item) {
			$db_name = $item[0];
			$certificado = $item[1];

			if (!isset($grouped_by_db[$db_name])) {
				$grouped_by_db[$db_name] = [];
			}
			$grouped_by_db[$db_name][] = $certificado;
		}

		// Procesamiento de cada base de datos
		foreach ($grouped_by_db as $db_name => $certificados) {
			$db = app(DataBasesServices::class)->connectionTo($db_name);
			if (!$db) {
				foreach ($certificados as $certificado) {
					$errors[] = 'No se pudo conectar a la base de datos para el certificado [' . $certificado . '].';
				}
				continue;
			}
			// Procesamiento de cada certificado para esta base de datos
			foreach ($certificados as $certificado) {
				$certificado = (int)$certificado;
				try {
					// Hacer una copia de la venta
					$backup = $db->table('ventas')->where('certificado', $certificado)->first();
					if ($backup) {
						$backupArray = (array) $backup;
						//
						if (isset($backupArray['indicador_venta'])) {
							unset($backupArray['indicador_venta']);
						}
						if (isset($backupArray['certificado_venta_principal'])) {
							unset($backupArray['certificado_venta_principal']);
						}
						if (isset($backupArray['fecha_envio']) && $backupArray['fecha_envio'] === '0000-00-00') {
							$backupArray['fecha_envio'] = null;
						}
						// Insertar en ventas_eliminadas
						$db->table('ventas_eliminadas')->insert($backupArray);
						// Eliminar de la tabla ventas
						$db->table('ventas')
							->where('certificado', $certificado)
							->delete();
						$messages[] = 'Venta [' . $certificado . '] fue eliminada de la base [' . $db_name . '].';
						// Insertar en log_deleted_sales
						LogDeletedSale::create([
							'user_id' => auth()->user()->id,
							'data_base' => $db_name,
							'certificate' => $certificado,
						]);
					} else {
						$errors[] = 'Venta [' . $certificado . '] no encontrada en la base  [' . $db_name . '].';
					}
					$status = 200;
				} catch (\Exception $e) {
					$errors[] = 'Venta [' . $certificado . '] de la base [' . $db_name . ']' . 'no se pudo eliminar.';
				}
			}
		}

		return response()->json([
			'messages' => $messages,
			'errors' => $errors,
		], $status);
	}
}
