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
//comentario porque el commit se buguea al carajo

use Illuminate\Http\Request;

class ChoferController extends Controller
{
    public function verPaquetes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Chofer' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Datos inválidos'], 400);
        }

        $validatedData = $validator->validated();
        $chofer = Chofer::find($validatedData['ID_Chofer']);

        if (!$chofer) {
            return response()->json(['message' => 'Chofer no encontrado'], 404);
        }

        $camion = ChoferCamion::where('ID_Chofer', $chofer->ID)->whereNull('deleted_at')->first();

        if (!$camion) {
            return response()->json(['message' => 'Camion no encontrado'], 404);
        }

        $lotes = LoteCamion::where('ID_Camion', $camion->ID_Camion)->pluck('ID_Lote');

        $paquetes = Forma::whereIn('ID_Lote', $lotes)->get();

        // Agregar información del destino a cada paquete
        $paquetesConDestino = $paquetes->map(function ($paquete) {
            $destino = Paquete::where('ID', $paquete->ID_Paquete)->value('Destino');
            $paquete->Destino = $destino;
            return $paquete;
        });

        return response()->json(['paquetes' => $paquetesConDestino], 200);
    }



    public function marcarEntregado(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Paquete' => 'required|integer',
            'ID_Chofer' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Datos inválidos'], 400);
        }

        $validatedData = $validator->validated();

        $chofer = Chofer::find($validatedData['ID_Chofer']);

        if (!$chofer) {
            return response()->json(['message' => 'Chofer no encontrado'], 404);
        }

        $camion = ChoferCamion::where('ID_Chofer', $chofer->ID)->whereNull('deleted_at')->first();

        if (!$camion) {
            return response()->json(['message' => 'Camion no encontrado'], 404);
        }

        $paquete = Paquete::find($validatedData['ID_Paquete']);

        if (!$paquete) {
            return response()->json(['message' => 'Paquete no encontrado'], 404);
        }

        $paquetesQueLlevaChofer = $this->verPaquetes($request)->getData()->paquetes;

        $forma = Forma::where('ID_Paquete', $paquete->ID)->first();
        $paqueteEncontrado = false;
        foreach ($paquetesQueLlevaChofer as $paqueteQueLlevaChofer) {
            if ($paqueteQueLlevaChofer->ID_Paquete == $paquete->ID) {
                $paqueteEncontrado = true;
                break;
            }
        }

        if ($paquete->ID_Estado == 4) {
            return response()->json(['message' => 'Paquete ya entregado'], 400);
        }

        if ($paqueteEncontrado) {
            $paquete->ID_Estado = 4;
            $paquete->save();
            $forma->ID_Estado = 3;
            $forma->save();

            $todosEntregados = Forma::where('ID_Lote', $forma->ID_Lote)->where('ID_Estado', '<>', 3)->count() == 0;

            if ($todosEntregados) {
                $lote = LoteCamion::where('ID_Lote', $forma->ID_Lote)->first();
                $lote->ID_Estado = 3;
                $lote->save();
            }

            return response()->json(['paquete' => "Paquete ID: " . $paquete->ID . " entregado"], 200);
        } else {
            return response()->json(['message' => 'Paquete no encontrado'], 404);
        }
    }

    public function manejaCamion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Chofer' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Datos inválidos'], 400);
        }

        $validatedData = $validator->validated();

        $chofer = Chofer::findOrFail($validatedData['ID_Chofer']);

        // ChoferCamion donde deleted at null e ID_Estado no es 5
        $camion = ChoferCamion::where('ID_Chofer', $chofer->ID)->whereNull('deleted_at')->where('ID_Estado', '<>', 5)->first();

        if (!$camion) {
            return response()->json(['message' => 'No tienes camion asignado'], 404);
        }

        return response()->json(['camion' => $camion], 200);
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
