<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$permissions = [
			//	Perfil
			'ver-perfil',
			//	Usuarios
			'ver-usuarios',
			'crear-usuarios',
			'editar-usuarios',
			'borrar-usuarios',
			'reset-password',
			//	Roles y Permisos
			'ver-accesos',
			'ver-roles',
			'crear-roles',
			'editar-roles',
			'borrar-roles',
			'ver-permisos',
			'crear-permisos',
			'editar-permisos',
			'borrar-permisos',
			//	Conteos
			'ver-conteos',
			'buscar-conteos',
			//	Ventas
			'ver-ventas',
			'buscar-ventas',
			'borrar-ventas',
			//	Reportes
			'crear-reportes',
			'ver-reportes',
			'ver-layout-int',
			'descargar-layout-int',
			'programar-layout-int',
			'borrar-layout-int',
			'ver-nps',
			'descargar-nps',
			'programar-nps',
			'borrar-nps'
		];
		//
		foreach ($permissions as $permission) {
			Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
		}
	}
}
