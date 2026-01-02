<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$statuses = [
			['name' => 'Pendientes', 'code' => 0, 'slug' => 'pen'],
			['name' => 'Aprobadas', 'code' => 1, 'slug' => 'ap'],
			['name' => 'Rechazadas', 'code' => 2, 'slug' => 'nap'],
			['name' => 'Hold', 'code' => 3, 'slug' => 'hold'],
			['name' => 'Aprovadas AOP', 'code' => 4, 'slug' => 'aop'],
			['name' => 'Recuperadas', 'code' => 5, 'slug' => 'rec'],
		];

		foreach ($statuses as $status) {
			Status::firstOrCreate(
				[
					'name' => $status['name'],
					'code' => $status['code'],
					'slug' => $status['slug'],
				]
			);
		}
	}
}
