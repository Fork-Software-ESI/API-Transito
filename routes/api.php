<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\ChoferController;

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

Route::post('/mapa', [MapaController::class, 'getCoordenadas']);

Route::post('/paquete', [ChoferController::class, 'cambiarEstadoPaquete'])->name('paquete.cambiarEstado');

Route::get('/paquete', [ChoferController::class, 'verPaquetes'])->name('paquete.verPaquete');