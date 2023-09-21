<?php

namespace App\Http\Controllers;

use App\Models\Chofer;
use App\Models\ChoferCamion;
use App\Models\LoteCamion;
use App\Models\Paquete;
use App\Models\Forma;
use App\Models\CamionLlevaLote;
use App\Models\CamionPlataformaSalida;
use App\Models\CamionPlataforma;
use App\Models\ChoferCamionManeja;

use Illuminate\Http\Request;

class ChoferController extends Controller
{
    /* public function registrarEntrega(Request $request)
    {
        $chofer = new Chofer();
        $chofer->nombre = $request->nombre;
        $chofer->direccion_entrega = $request->direccion_entrega;
        $chofer->lote_entregado = true;
        $chofer->save();

        return response()->json(['message' => 'Entrega registrada con éxito'], 201);
    } */

    public function verPaquetes(Request $request)
    {
        $chofer = Chofer::find($request->id);
        $camion = ChoferCamion::where('ID_Chofer', $chofer->ID)->first();
        $lotes = LoteCamion::where('ID_Camion', $camion->ID_Camion)->get();
        $paquetes = Forma::whereIn('ID_Lote', $lotes->pluck('ID_Lote'))->get();
        return response()->json(['paquetes' => $paquetes], 200);
    }

    public function cambiarEstadoPaquete(Request $request)
    {
        $paquete = Paquete::find($request->id);
        $lote = Forma::where('ID_Paquete', $paquete->ID)->get();
        $camionLote = LoteCamion::where('ID_Lote', $lote->pluck('ID_Lote'))->first();
        $camionLlevaLote = CamionLlevaLote::where('ID_Lote', $lote->pluck('ID_Lote'))->first();
        if ($request->estado == 'Entregado' || $request->estado == 'No entregado') {
            $paquete->Estado = $request->estado;
            $paquete->save();
            $paquetes = Paquete::whereIn('ID', Forma::where('ID_Lote', $lote->pluck('ID_Lote'))->pluck('ID_Paquete'))->get();
            $todosEntregados = $paquetes->every(function ($paquete) {
                return $paquete->Estado == "Entregado";
            });
            if ($todosEntregados) {
                $camionLote->Estado = 'Entregado';
                $camionLote->save();
                $camionLlevaLote->Fecha_Hora_Fin = now();
            }
        } else {
            return response()->json(['message' => 'Estado inválido'], 400);
        }
        $responseArray = [
            'camionLote' => $camionLote,
            'paquete' => $paquete,
            'camionLlevaLote' => $camionLlevaLote
        ];

        return response()->json($responseArray, 200);
    }

    public function manejaCamion(Request $request){
        $chofer = Chofer::find($request->ID);
        $camion = ChoferCamion::where('ID_Chofer', $chofer->ID)->first();
        $accion = $request -> input('accion');
        if($accion == 'inicio'){
            $camion -> Fecha_Hora_Inicio = now();
            $camion -> save();
            return response()->json(['camion' => $camion], 200);
        }else if($accion == 'fin'){
            $maneja = ChoferCamionManeja::where('ID_Camion', $camion->ID_Camion)->first();
            $maneja -> Fecha_Hora_Fin = now();
            $maneja -> save();
            return response()->json(['maneja' => $maneja], 200);
        }
    }

    public function plataforma(Request $request)
    {
        $chofer = Chofer::find($request->ID);
        $camion = ChoferCamion::where('ID_Chofer', $chofer->ID)->first();
        $accion = $request -> input('accion');
        if($accion == 'llegada'){
            $plataforma = CamionPlataforma::where('ID_Camion', $camion->ID_Camion)->first();
            $plataforma -> Fecha_Hora_Llegada = now();
            $plataforma -> save();
        }else if($accion == 'salida'){
            $plataforma = CamionPlataformaSalida::where('ID_Camion', $camion->ID_Camion)->first();
            $plataforma -> Fecha_Hora_Salida = now();
            $plataforma -> save();
        }
        return response()->json(['plataforma' => $plataforma], 200);
    }
}
