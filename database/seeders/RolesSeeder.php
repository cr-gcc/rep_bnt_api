<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
	public function run(): void
	{
		$roles = [
			'Super-Admin',
			'Coordinador-CSO',
			'Coordinador-A',
			'Coordinador-B',
			'Coordinador-VC',
			'Validación ',
			'Calidad',
			'Supervisor',
			'Analista-CSO'
		];

		foreach ($roles as $role) {
			Role::firstOrCreate(['name' => $role]);
		}
	}
}
