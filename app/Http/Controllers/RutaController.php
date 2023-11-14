<?php

namespace App\Http\Controllers;

use App\Models\Paquete;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\LoteCamion;
use App\Models\Forma;
use App\Models\CamionPlataforma;
use App\Models\Almacen;

class RutaController extends Controller
{
    /*private function generarRuta($direcciones)
    {
        $appId = env('HEREMAPS_APP_ID');
        $appCode = env('HEREMAPS_APP_CODE');

        // Convertir direcciones a coordenadas
        $coordenadas = [];
        foreach ($direcciones as $direccion) {
            $response = $this->obtenerCoordenadas($direccion, $appId, $appCode);
            $coordenadas[] = $response['Response']['View'][0]['Result'][0]['Location']['DisplayPosition'];
        }

        // Generar ruta utilizando la matriz de coordenadas
        $url = "https://router.hereapi.com/v8/routes?apiKey=" . $appId;
        $client = new Client();

        $response = $client->request('POST', $url, [
            'json' => [
                'waypoint' => $coordenadas,
                'mode' => 'fastest;car',
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data;
    }*/

    private function obtenerCoordenadas($direccion)
    {
        $apiKey = env('HEREMAPS_APP_CODE');
        $countryName = "Uruguay";
        $url = "https://geocode.search.hereapi.com/v1/geocode?q=$direccion&country=$countryName&apiKey=$apiKey";

        $client = new Client();
        $response = $client->get($url);
        $data = json_decode($response->getBody(), true);
        return $data;
    }

    public function calcularRuta(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Camion' => 'required|exists:chofer_camion,ID_Camion',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $validatedData = $validator->validated();

        $camionPlataforma = CamionPlataforma::where('ID_Camion', $validatedData['ID_Camion'])->whereNull('deleted_at')->first();
        $almacen = Almacen::where('ID', $camionPlataforma->ID_Almacen)->whereNull('deleted_at')->first();
        $lotesCamion = LoteCamion::where('ID_Camion', $validatedData['ID_Camion'])->whereNull('deleted_at')->get()->pluck('ID_Lote');
        $forma = Forma::whereIn('ID_Lote', $lotesCamion)->whereNull('deleted_at')->get();
        $paquetes = Paquete::whereIn('ID', $forma->pluck('ID_Paquete'))->whereNull('deleted_at')->get();
        $direcciones = $paquetes->pluck('Destino');

        $coordenadas = [];
        $coordenadas[] = $this->obtenerCoordenadas($almacen->Direccion);
        foreach ($direcciones as $direccion) {
            $coordenadas[] = $this->obtenerCoordenadas($direccion);
        }

        return response()->json($coordenadas, 200);
    }
}
