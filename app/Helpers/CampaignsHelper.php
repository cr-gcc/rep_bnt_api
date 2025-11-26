<?php

namespace App\Helpers;

use App\Models\Campaign;

class CampaignsHelper
{
	public function __construct()
	{
		//
	}

	public static function campaignToBase($campaign)
	{
		if ($campaign == "all") {
			return Campaign::where('active', true)->pluck('db_name')->toArray();
		}

		$db = Campaign::where('name', $campaign)->where('active', true)->first();
		return $db ? [$db->db_name] : [];
	}

	public static function baseToCampaign($db_name)
	{
		$campaign = Campaign::where('db_name', $db_name)->first();
		return $campaign ? $campaign->name : null;
	}

	public static function campaignsById($campaigns)
	{
		// Si el primer elemento es 0, devolvemos todas las campañas activas
		if (!empty($campaigns) && (int)$campaigns[0] === 0) {
			return Campaign::where('active', true)->pluck('db_name')->toArray();
		}

		// Filtramos por ID y devolvemos los nombres de base de datos
		return Campaign::whereIn('id', $campaigns)
			->where('active', true)
			->pluck('db_name')
			->toArray();
	}
}
