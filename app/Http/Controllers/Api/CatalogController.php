<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DataBasesServices;
use App\Models\Campaign;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
	public function getTablesFromDB($db_id)
	{
		$data_base = Campaign::find($db_id)->db_name;
		$tables = app(DataBasesServices::class)->connectionTo($data_base)->select("SHOW TABLES");
		$data = [];
		$condition = "catalogo_";
		$token = "bk";
		$message = "";
		$error = "";
		$status = 200;

		foreach ($tables as $table) {
			$tableName = array_values((array)$table)[0];
			// Filtrar TABLAS que contengan $condition pero no contengan $token
			$hasCondition = stripos($tableName, $condition) !== false;
			$hasToken = stripos($tableName, $token) !== false;

			// Solo procesar tablas que cumplan con el filtro
			if ($hasCondition && !$hasToken) {
				$data[] = $tableName;
			}
		}

		if (empty($data)) {
			$error = "No tables found";
			$status = 404;
		}

		$data = [
			'data' => $data,
			'message' => $message,
			'error' => $error,
		];

		return response()->json($data, $status);
	}

	public function getTableFromDB($db_id, $table_name)
	{
		$data = [];
		$table_data = [];
		$message = "";
		$error = "";
		$status = 200;
		$campaing_base = Campaign::find($db_id)->db_name;

		try {
			$data_base = app(DataBasesServices::class)->connectionTo($campaing_base);
			$table_data = $data_base->table($table_name)->get();
		} catch (\Exception $e) {
			$error = $e->getMessage();
			$status = 500;
		}

		$data = [
			'data' => $table_data,
			'message' => $message,
			'error' => $error,
		];

		return response()->json($data, $status);
	}
}
