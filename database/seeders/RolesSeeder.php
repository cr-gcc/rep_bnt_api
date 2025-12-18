<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
	public function run(): void
	{
		$roles = ['Super-Admin', 'CSO-Custodio', 'CSO-Informes', 'Coordinador-A', 'Coordinador-B', 'Va-Cal', 'Validación ', 'Calidad', 'Supervisor'];

		foreach ($roles as $role) {
			Role::firstOrCreate(['name' => $role]);
		}
	}
}
