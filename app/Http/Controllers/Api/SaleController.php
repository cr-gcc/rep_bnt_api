<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\DataBasesServices;
use App\Helpers\CampaignsHelper;
use App\Http\Requests\Sales\GetDatesRequest;
use App\Http\Requests\Sales\SearchRequest;

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
		$campaigns = CampaignsHelper::campaignToBase('all');
		//	Todos los registros
		foreach ($campaigns as $camp) {
			$db = app(DataBasesServices::class)->connectionTo($camp);
			//	Check de conexion
			if (!$db) {
				$results[$camp] = ['error' => 'No existe la conexión'];
				continue;
			}
			$db_sales = $db->table('ventas')
				->whereBetween('fecha_venta', [$start_date, $end_date])
				->get();
			$name = CampaignsHelper::baseToCampaign($camp);
			$colleccion_campaigns->put($name, collect($db_sales));
		}
		//	Filtrado
		$totales = $colleccion_campaigns->map(function ($sales, $name) {
			$pen  = $sales->where('validacion1', 0)->count();
			$ap   = $sales->where('validacion1', 1)->count();
			$nap  = $sales->where('validacion1', 2)->count();
			$hold = $sales->where('validacion1', 3)->count();
			$aop  = $sales->where('validacion1', 4)->count();
			$rec  = $sales->where('validacion1', 5)->count();
			return [
				'campaign' => strtoupper($name),
				'pen'  => $pen,
				'ap'   => $ap,
				'nap'  => $nap,
				'hold' => $hold,
				'aop'  => $aop,
				'rec'  => $rec,
				'total' => $pen + $ap + $nap + $hold + $aop + $rec
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

		$db_campaigns = CampaignsHelper::campaignsById($campaigns);


		var_dump($db_campaigns);
	}
}
