<?php

namespace App\Helpers;

class DBNameMapper
{
	/**
	 * Mapping of database names to their corresponding customer data constants
	 * 
	 * @var array
	 */
	private static $dbMapping = [
		// PVH Sales databases
		'banorte_captacion_pvh' => 'PVH_SALES',
		'banorte_consumo_pvh' => 'PVH_SALES',
		'banorte_nomina_pvh' => 'PVH_SALES',
		'banorte_plenitud' => 'PVH_SALES',
		'banorte_pvh' => 'PVH_SALES',
		
		// Auto Sales databases
		'banorte_auto_seguro' => 'AUTO_SALES',
		'banorte_clientes_auto' => 'AUTO_SALES',
		
		// Rastreator Sales
		'banorte_rastreator' => 'RASTREITOR_SALES',
		
		// TDC Sales
		'banorte_tdc_arquetipos' => 'TDC_SALES',
		
		// RTKM Sales
		'banorte_rtmk' => 'RTKM_SALES',
		
		// SAM Mexico Sales
		'banorte_segurosdeautomexico' => 'SAM_MEXICO_SALES',
	];

	/**
	 * Get the customer data configuration for a given database name
	 * 
	 * @param string $db_name The database name
	 * @return array The customer data configuration array
	 */
	public static function getCustomerDataByDB($db_name)
	{
		// Get the constant name from the mapping
		$constantName = self::$dbMapping[$db_name] ?? null;
		
		// If no mapping found, return empty array
		if ($constantName === null) {
			return [];
		}
		
		// Get the constant value from CustomerData class
		$constantPath = "App\\Helpers\\CustomerData::{$constantName}";
		return constant($constantPath);
	}

	/**
	 * Check if a database name exists in the mapping
	 * 
	 * @param string $db_name The database name
	 * @return bool
	 */
	public static function hasMapping($db_name)
	{
		return isset(self::$dbMapping[$db_name]);
	}

	/**
	 * Get all database names for a specific sales type
	 * 
	 * @param string $salesType The sales type constant name (e.g., 'PVH_SALES')
	 * @return array Array of database names
	 */
	public static function getDBNamesBySalesType($salesType)
	{
		return array_keys(array_filter(self::$dbMapping, function($type) use ($salesType) {
			return $type === $salesType;
		}));
	}

	/**
	 * Get all available database mappings
	 * 
	 * @return array
	 */
	public static function getAllMappings()
	{
		return self::$dbMapping;
	}
}
