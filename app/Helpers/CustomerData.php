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

		static function getCDBySale($db_name)
		{
			if ($db_name == 'banorte_captacion_pvh' || 
				$db_name == 'banorte_consumo_pvh' || 
				$db_name == 'banorte_nomina_pvh' || 
				$db_name == 'banorte_plenitud' ||
				$db_name == 'banorte_pvh') {
				return self::PVH_SALES;
			}
			else if ($db_name == 'banorte_auto_seguro' || 
				$db_name == 'banorte_clientes_auto') {
				return self::AUTO_SALES;
			}
			else if ($db_name == 'banorte_rastreator') {
				return self::RASTREITOR_SALES;
			}
			else if ($db_name == 'banorte_tdc_arquetipos') {
				return self::TDC_SALES;
			}
			else if ($db_name == 'banorte_tdc_arquetipos') {
				return self::TDC_SALES;
			}
			else if ($db_name == 'banorte_rtmk') {
				return self::RTKM_SALES;
			}
			else if ($db_name == 'banorte_segurosdeautomexico') {
				return self::SAM_MEXICO_SALES;
			}
			else {
				return [];
			}
		}
	}