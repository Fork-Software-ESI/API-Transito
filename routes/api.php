<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChoferController;
use App\Http\Controllers\ClienteController; 

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

Route::post('/paquete', [ChoferController::class, 'cambiarEstadoPaquete'])->name('chofer.cambiarEstado');

Route::get('/paquete', [ChoferController::class, 'verPaquetes'])->name('chofer.verPaquete');

Route::post('/plataforma', [ChoferController::class, 'plataforma'])->name('chofer.plataforma');

Route::post('/maneja', [ChoferController::class, 'manejaCamion'])->name('chofer.manejaCamion');

Route::get('/cliente', [ClienteController::class, 'verPaquete'])->name('cliente.verPaquete');