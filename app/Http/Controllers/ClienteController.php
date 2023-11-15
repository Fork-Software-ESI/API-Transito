<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paquete;
use App\Models\Forma;
use App\Models\LoteCamion;
use App\Models\Camion;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function verPaquete(Request $request)
    {
        $paquete = Paquete::where('ID_Cliente', $request->id)->get();
        $lote = Forma::whereIn('ID_Paquete', $paquete->pluck('ID'))->pluck('ID_Lote');
        $camion = LoteCamion::whereIn('ID_Lote', $lote)->pluck('ID_Camion');
        $matricula = Camion::whereIn('ID', $camion)->pluck('Matricula');
        $responseArray = [
            'paquete' => $paquete,
            'matricula' => $matricula
        ];
        return response()->json(['paquete' => $responseArray], 200);
    }

    public function buscarPaquete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Codigo' => 'required|string|max:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $paquete = Paquete::where('Codigo', $request->Codigo)->first();

        return response()->json(['paquete' => $paquete], 200);
    }
}
