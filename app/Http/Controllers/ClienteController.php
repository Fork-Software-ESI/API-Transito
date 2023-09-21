<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paquete;
use App\Models\Forma;
use App\Models\LoteCamion;
use App\Models\Camion;  

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
}
