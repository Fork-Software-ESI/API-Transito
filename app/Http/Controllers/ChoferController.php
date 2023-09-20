<?php

namespace App\Http\Controllers;

use App\Models\Chofer;
use App\Models\ChoferCamion;
use App\Models\LoteCamion;
use App\Models\Paquete;
use App\Models\Forma;
use App\Models\Lote;
use Illuminate\Http\Request;

class ChoferController extends Controller
{

    public function registrarEntrega(Request $request)
    {
        $chofer = new Chofer();
        $chofer->nombre = $request->nombre;
        $chofer->direccion_entrega = $request->direccion_entrega;
        $chofer->lote_entregado = true;
        $chofer->save();

        return response()->json(['message' => 'Entrega registrada con éxito'], 201);
    }

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
        $lote = Forma::where('ID_Paquete', $paquete->ID_Paquete)->first();
        dd($lote);
        if ($request->estado == 'Entregado' || $request->estado == 'No entregado') {
            $paquete->estado = $request->estado;
        } else {
            return response()->json(['message' => 'Estado inválido'], 400);
        }
        $lote->estado = $request->estado;
        $paquete->save();
        $lote->save();
        return response()->json(['message' => 'Estado del paquete actualizado con éxito'], 200);
    }
}