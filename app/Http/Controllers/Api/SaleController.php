<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\DataBasesServices;
use App\Models\Campaign;
use App\Http\Requests\Sales\GetDatesRequest;
use App\Http\Requests\Sales\SearchRequest;

class SaleController extends Controller
{
	const STATUS_PEN = 0;
	const STATUS_AP = 1;
	const STATUS_NAP = 2;
	const STATUS_HOLD = 3;
	const STATUS_AOP = 4;
	const STATUS_REC = 5;

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
			$sales = $data['sales'];
			$app = strtoupper($data['system_name']);
			$pen  = $sales->where('validacion1', self::STATUS_PEN)->count();
			$ap   = $sales->where('validacion1', self::STATUS_AP)->count();
			$nap  = $sales->where('validacion1', self::STATUS_NAP)->count();
			$hold = $sales->where('validacion1', self::STATUS_HOLD)->count();
			$aop  = $sales->where('validacion1', self::STATUS_AOP)->count();
			$rec  = $sales->where('validacion1', self::STATUS_REC)->count();
			$total = $pen + $ap + $nap + $hold + $aop + $rec;
			$pnap = $pnap = ($total > 0 && $nap > 0) ? round(($nap / $total) * 100, 2) : 0;
			return [
				'campaign' => strtoupper($name),
				'app'  => $app,
				'pen'  => $pen,
				'ap'   => $ap,
				'nap'  => $nap,
				'hold' => $hold,
				'aop'  => $aop,
				'rec'  => $rec,
				'percent_nap' => $pnap,
				'total' => $total
			];
		})->values();

		return response()->json($totales, 200);
	}

	public function search(SearchRequest $request)
	{
		$request->validated();

		$start_date_sale = $request->input('start_date_sale', '');
		$end_date_sale = $request->input('end_date_sale', '');
		$start_date_val = $request->input('start_date_val', '');
		$end_date_val = $request->input('end_date_val', '');
		$campaigns = $request->input('campaigns', []);
		$status = $request->input('status', []);
		$sig = $request->input('sig', '');
		$policy = $request->input('policy', '');
	}
}
