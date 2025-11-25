<?php

namespace App\Helpers;

class CampaignsHelper
{
	public function __construct()
	{
		//
	}

	public static function campaignToBase($campign)
	{
		$response_camps = [];
		$camps = [
			'captacion' => 'banorte_captacion_pvh',
			'consumo' => 'banorte_consumo_pvh',
			'nomina' => 'banorte_nomina_pvh',
			'plenitud' => 'banorte_plenitud',
			'pvh' => 'banorte_pvh',
		];

		if ($campign == "all") {
			foreach ($camps as $camp) {
				$response_camps[] = $camp;
			}
		} else {
			$response_camps[] = $camps[$campign];
		}

		return $response_camps;
	}

	public static function baseToCampaign($db_name)
	{
		$camps = [
			'banorte_captacion_pvh' => 'captacion',
			'banorte_consumo_pvh' => 'consumo',
			'banorte_nomina_pvh' => 'nomina',
			'banorte_plenitud' => 'plenitud',
			'banorte_pvh' => 'pvh'
		];

		return $camps[$db_name];
	}

	public static function campaignsById($campaigns)
	{
		$camps = ['banorte_captacion_pvh', 'banorte_consumo_pvh', 'banorte_nomina_pvh', 'banorte_plenitud', 'banorte_pvh'];
		$response_camps = [];

		$val = (int)$campaigns[0];
		if ($val === 0) {
			$response_camps = $camps;
		} else {
			for ($i = 0; $i < count($campaigns); $i++) {
				$index = (int)$campaigns[$i] - 1;
				$response_camps[$i] = $camps[$index];
			}
		}
		return $response_camps;
	}
}
