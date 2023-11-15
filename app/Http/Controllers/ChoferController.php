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
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class ChoferController extends Controller
{
    public function verPaquetes(Request $request)
    {
        $chofer = Chofer::find($request->id);
        $camion = ChoferCamion::where('ID_Chofer', $chofer->ID)->first();
        $lotes = LoteCamion::where('ID_Camion', $camion->ID_Camion)->pluck('ID_Lote');
        $paquetes = Forma::whereIn('ID_Lote', $lotes)->get();
        return response()->json(['paquetes' => $paquetes], 200);
    }

    public function cambiarEstadoPaquete(Request $request)
    {
        $paquete = Paquete::find($request->id);
        $lote = Forma::where('ID_Paquete', $paquete->ID)->pluck('ID_Lote');
        $camionLote = LoteCamion::where('ID_Lote', $lote)->first();
        $camionLlevaLote = CamionLlevaLote::where('ID_Lote', $lote)->first();
        if ($request->estado == 'Entregado' || $request->estado == 'No entregado') {
            $paquete->Estado = $request->estado;
            $paquete->save();
            $paquetes = Paquete::whereIn('ID', Forma::where('ID_Lote', $lote)->pluck('ID_Paquete'))->get();
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

    public function manejaCamion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Chofer' => 'required|integer',
            'accion' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Datos inválidos'], 400);
        }

        $validatedData = $validator->validated();
        $chofer = Chofer::findOrFail($validatedData['ID_Chofer']);
        $camion = ChoferCamion::where('ID_Chofer', $validatedData['ID_Chofer'])->first();
        $accion = $validatedData['accion'];
        if ($accion == 'inicio') {
            $camion->Fecha_Hora_Inicio = now();
            $camion->save();
            return response()->json(['camion' => $camion], 200);
        } else if ($accion == 'fin') {
            $maneja = ChoferCamionManeja::where('ID_Camion', $camion->ID_Camion)->first();
            $maneja->Fecha_Hora_Fin = now();
            $maneja->save();
            return response()->json(['maneja' => $maneja], 200);
        }
    }

    public function plataforma(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Chofer' => 'required|integer',
            'accion' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Datos inválidos'], 400);
        }

        $validatedData = $validator->validated();

        $chofer = Chofer::findOrFail($validatedData['ID_Chofer']);

        $camion = ChoferCamion::where('ID_Chofer', $chofer->ID)->first();

        if (!$camion) {
            return response()->json(['message' => 'Camion no encontrado'], 404);
        }

        $accion = $validatedData['accion'];
        if ($accion == 'llegada') {
            $plataforma = CamionPlataforma::where('ID_Camion', $camion->ID_Camion)->first();
            $plataforma->Fecha_Hora_Llegada = now();
            $plataforma->save();
        } else if ($accion == 'salida') {
            $plataforma = CamionPlataformaSalida::where('ID_Camion', $camion->ID_Camion)->first();
            $plataforma->Fecha_Hora_Salida = now();
            $plataforma->save();
        }
        return response()->json(['plataforma' => $plataforma], 200);
    }
}
