<?php

namespace App\Helpers;

class CustomerData
{
	//	CAPTACION, CONSUMO, NOMINA, PLENITUD, PVH
	const PVH_SALES = [
		"cliente" => "CONCAT_WS(' ', ventas.nombres, ventas.paterno, ventas.materno) as cliente",
		"asegurado" => "CONCAT_WS(' ', ventas.nombre_adi, ventas.paterno_adi, ventas.materno_adi) as asegurado",
	];
	//	TDC 
	const TDC_SALES = [
		"cliente" => "CONCAT_WS(' ', ventas.nombre, ventas.paterno, ventas.materno) as cliente",
		"asegurado" => "'N/A' as asegurado",
	];
	//	AUTO, SEGURO
	const AUTO_SALES = [
		"cliente" => "ventas.nombre as cliente",
		"asegurado" => "ventas.conductor as asegurado",
	];
	//	RASTREITOR
	const RASTREITOR_SALES = [
		"cliente" => "CONCAT_WS(' ', clientes.nombre, clientes.paterno, clientes.materno) as cliente",
		"asegurado" => "ventas.conductor as asegurado",
	];
	//	SAM MEXICO
	const SAM_MEXICO_SALES = [
		"cliente" => "ventas.nombre as cliente",
		"asegurado" => "ventas.benneficiario as asegurado",
	];
	//	RTKM
	const RTKM_SALES = [
		"cliente" => "CONCAT_WS(' ', clientes.nombre, clientes.paterno) as cliente",
		"asegurado" => "'GATGETS/MASCOTAS' as asegurado",
	];

	/**
	 * Get customer data configuration by database name
	 * 
	 * @param string $db_name The database name
	 * @return array The customer data configuration
	 */
	static function getCDBySale($db_name)
	{
		return DBNameMapper::getCustomerDataByDB($db_name);
	}
}
