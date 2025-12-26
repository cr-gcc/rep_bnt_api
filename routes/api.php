<?php

use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\RolesAndPermissionsController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/version', [AuthController::class, 'version'])->name('version');
Route::post('/login', [AuthController::class, 'login']);
//	Rutas Protegidas
Route::middleware('auth:api')->group(function () {
	//	Autenticacion
	Route::get('/me', [AuthController::class, 'me']);
	Route::post('/logout', [AuthController::class, 'logout']);
	Route::get('/password/reset/{user_id}', [AuthController::class, 'resetPassword']);
	Route::post('/password/change', [AuthController::class, 'changePassword']);
	//	Roles-Permisos
	Route::get('/roles', [RolesAndPermissionsController::class, 'roles']);
	Route::get('/permissions', [RolesAndPermissionsController::class, 'permissions']);
	Route::get('/permissions-by-group', [RolesAndPermissionsController::class, 'permissionsByGroup']);
	Route::get('/role-access/{role_id}', [RolesAndPermissionsController::class, 'roleAccess']);
	Route::post('/access-control', [RolesAndPermissionsController::class, 'accessControl']);
	//	Ventas
	Route::post('/sales/get-general-counts', [SaleController::class, 'getGeneralCounts']);
	Route::post('/sales/search', [SaleController::class, 'search']);
	Route::post('/sales/delete', [SaleController::class, 'delete']);
	//	Campañas
	Route::get('/campaigns', [CampaignController::class, 'index']);
	//	Estatus
	Route::get('/statuses', [StatusController::class, 'index']);
	//	Usuarios
	Route::resource('/users', UserController::class);
});
