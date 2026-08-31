<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Services\DataBasesServices;
use App\Http\Requests\Email\GetReportRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EmailController extends Controller
{
	public function emailsSentStats(GetReportRequest $request)
	{
		$start_date = $request->start_date;
		$end_date = $request->end_date;
		$schedule = $request->schedule;
		$campaigns = $request->campaigns;
		$type = $request->type;
		$data_email_list = collect();
		$data_email_totals = [];
		$data = [];
		$tables = [];
		//
		$start_date .= ' 00:00:00';
		$end_date .= ' 23:59:59';
		//
		$all_campaigns = (int)$campaigns[0];
		if ($all_campaigns === 0) {
			$query_campaigns = Campaign::where('active', true)
				->where('id', '<', 7)
				->get();
		} else {
			$query_campaigns = Campaign::whereIn('id', $campaigns)->get();
		}
		//
		$tables = [
			'24' => ['log_email_validacion'],
			'48' => ['log_email_automatico'],
			'0' => ['log_email_validacion', 'log_email_automatico']
		];
		//
		foreach ($query_campaigns as $campaign) {
			$system_name = $campaign->system_name . ' - ' . $campaign->name;
			$db = app(DataBasesServices::class)->connectionTo($campaign->db_name);
			if (!$db) {
				continue;
			}
			foreach ($tables[$schedule] as $table) {
				$email_type = $table == 'log_email_validacion' ? '24' : '48';
				// Base query
				$query = $db->table($table)
					->leftJoin('ventas', 'ventas.certificado', '=', $table . '.id_certificado')
					->whereBetween($table . '.updated_at', [$start_date, $end_date]);
				if ($email_type == '48') {
					$query->where($table . '.code', 200);
				}
				// Disting between list and total
				if ($type === 'list') {
					$query->select(
						$table . '.id_cliente as cliente',
						$table . '.id_certificado as certificado',
						$table . '.updated_at as fecha',
						'ventas.paterno as paterno',
						'ventas.materno as materno',
						'ventas.certificado as certificado_venta',
						'ventas.id_cliente as id_cliente_venta',
						DB::raw("'" . $system_name . "' as camp"),
						DB::raw("'" . $email_type . "' as tipo")
					);
					if ($campaign->db_name == 'banorte_tdc_arquetipos') {
						$query->addSelect('ventas.nombre as nombre',);
						$query->addSelect('ventas.correo as correo');
					} else {
						$query->addSelect('ventas.nombres as nombre',);
						$query->addSelect(
							DB::raw("CONCAT(ventas.mail1, '@', ventas.mail2) as correo")
						);
					}
					$data_email_list[] = $query->get();
				} else {
					$query->select(DB::raw('COUNT(*) as total'));
					$count = $query->count();
					$key = $table == 'log_email_validacion' ? '24' : '48';
					if (!isset($data_email_totals[$system_name][$key])) {
						$data_email_totals[$system_name][$key] = 0;
					}
					$data_email_totals[$system_name][$key] += $count;
				}
			}
		}
		// Merge data for list
		if ($type == 'list') {
			$data = collect($data_email_list)->flatten(1);
		} else {
			// Prepare data for totals
			foreach ($data_email_totals as $system => $values) {
				$row = [
					'name' => $system,
					'e24' => $values['24'] ?? 0,
					'e48' => $values['48'] ?? 0,
					'total' => ($values['24'] ?? 0) + ($values['48'] ?? 0)
				];
				$data[] = $row;
			}
		}
		//
		return response()->json($data, 200);
	}
}
