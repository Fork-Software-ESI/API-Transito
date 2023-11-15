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
    private function obtenerCoordenadas($direccion)
    {
        $apiKey = env('MAPS_API_KEY');
        $pais = "Uruguay";
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$direccion&components=country:$pais&key=$apiKey";

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

        $resultsWithUniqueCoordinates = [];

        foreach ($coordenadas as $result) {
            foreach ($result['results'] as $place) {
                $geometry = $place['geometry'];
                $location = $geometry['location'];
                $latitude = $location['lat'];
                $longitude = $location['lng'];

                $existingCoordinates = array_filter($resultsWithUniqueCoordinates, function ($coord) use ($latitude, $longitude) {
                    return $coord['latitude'] == $latitude && $coord['longitude'] == $longitude;
                });

                if (empty($existingCoordinates)) {
                    $resultsWithUniqueCoordinates[] = [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                    ];
                }
            }
        }



        return response()->json($resultsWithUniqueCoordinates, 200);
    }
}