<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChoferController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\RutaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/paquete', [ChoferController::class, 'cambiarEstadoPaquete']);

Route::get('/paquete', [ChoferController::class, 'verPaquetes']);

Route::post('/plataforma', [ChoferController::class, 'plataforma']);

Route::post('/maneja', [ChoferController::class, 'manejaCamion']);

Route::get('/cliente', [ClienteController::class, 'verPaquete']);

Route::post('/ruta', [RutaController::class, 'calcularRuta']);

