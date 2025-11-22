<?php

use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/version', [AuthController::class, 'version'])->name('version');

Route::post('/login', [AuthController::class, 'login']);
//	Rutas Protegidas
Route::middleware('auth:api')->group(function () {
	//	Autenticacion
	Route::get('/me', [AuthController::class, 'me']);
	Route::post('/logout', [AuthController::class, 'logout']);
	//	Ventas
	Route::post('/sales/get-general-counts', [SaleController::class, 'getGeneralCounts']);
});
