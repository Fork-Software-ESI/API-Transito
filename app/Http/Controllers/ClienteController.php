<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paquete;
use App\Models\Forma;
use App\Models\LoteCamion;
use App\Models\Camion;
use Illuminate\Support\Facades\Validator;
use App\Models\GerentePaquete;
use App\Models\GerenteAlmacen;
use App\Models\Almacen;

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
            'codigo' => 'required|string|max:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();

        $paquete = Paquete::where('Codigo', $validatedData['codigo'])->first();

        if (!$paquete) {
            return response()->json(['error' => 'No existe un paquete con ese código'], 400);
        }

        return response()->json(['paquete' => $paquete], 200);

    }

    public function almacenPaquete(Request $request) {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();

        $paquete = Paquete::where('Codigo', $validatedData['codigo'])->first();

        if (!$paquete) {
            return response()->json(['error' => 'No existe un paquete con ese código'], 400);
        }

        $gerentePaquete = GerentePaquete::where('ID_Paquete', $paquete->ID)->whereNull('deleted_at')->first();

        if (!$gerentePaquete) {
            return response()->json(['error' => 'Paquete mal registrado'], 400);
        }

        $gerenteAlmacen = GerenteAlmacen::where('ID_Gerente', $gerentePaquete->ID_Gerente)->whereNull('deleted_at')->first();

        if (!$gerenteAlmacen) {
            return response()->json(['error' => 'Gerente mal registrado'], 400);
        }

        $almacen = Almacen::where('ID', $gerenteAlmacen->ID_Almacen)->whereNull('deleted_at')->first();

        if (!$almacen) {
            return response()->json(['error' => 'Almacen mal registrado'], 400);
        }

        return response()->json(['almacen' => $almacen], 200);
    }
}
