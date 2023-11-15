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


Route::post('/paquetes', [ChoferController::class, 'cambiarEstadoPaquete']);
Route::get('/paquetes', [ChoferController::class, 'verPaquetes']);
Route::post('/plataforma', [ChoferController::class, 'plataforma']);
Route::post('/camion', [ChoferController::class, 'manejaCamion']);

Route::get('/paquetes/chofer', [ClienteController::class, 'verPaquete']);
Route::get('/paquetes/codigo' , [ClienteController::class, 'buscarPaquete']);

Route::post('/ruta', [RutaController::class, 'calcularRuta']);

