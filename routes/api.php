<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/version', [AuthController::class, 'version'])->name('version');

Route::post('/login', [AuthController::class, 'login']);
//	Rutas protegidas
Route::middleware('auth:api')->group(function () {
  Route::get('/me', [AuthController::class, 'me']);
  Route::post('/logout', [AuthController::class, 'logout']);
});
