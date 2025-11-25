<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DataBasesServices
{
	public function connectionTo(string $database)
	{
		Config::set('database.connections.mysql_multi.database', $database);
		DB::purge('mysql_multi');
		DB::reconnect('mysql_multi');
		return DB::connection('mysql_multi');
	}
}
