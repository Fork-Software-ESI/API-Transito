<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Camion;
use App\Models\Forma;
use App\Models\LoteCamion;
use App\Models\Paquete;
use GuzzleHttp\Client;

class RutaController extends Controller
{
    public function calcularRuta(Request $request)
    {
        $matricula = $request->input('matricula');
        $camion = Camion::where('matricula', $matricula)->first();

        if (!$camion) {
            return response()->json(['error' => 'Camión no encontrado'], 404);
        }

        $loteCamion = Lotecamion::where('ID_Camion', $camion->ID)->first();
        
        if (!$loteCamion) {
            return response()->json(['error' => 'Camión no encontrado'], 404);
        }
        $forma = Forma::where('ID_Lote', $loteCamion->ID_Lote)->pluck('ID_Paquete');
        $paquete = Paquete::whereIn('ID', $forma)->pluck('Destino');

        $coordenadasDestinos = $this->geocodificarDestinos($paquete);

        if (count($coordenadasDestinos) === 0) {
            return response()->json(['error' => 'Error en la geocodificación de destinos'], 500);
        }

        // aca le mandamos la solicitud a la api
        $solicitud = [
            'waypoints' => implode(';', array_map(function ($coordenada) {
                return $coordenada['lat'] . ',' . $coordenada['lng'];
            }, $coordenadasDestinos)),
            'mode' => 'fastest;car;traffic:disabled',
        ];

        // Hacer la solicitud a la API de Routing de HERE
        $response = Http::get('https://router.hereapi.com/v8/routes', [
            'apiKey' => '7a6TfdGhaJbpPMG2ehCfSExHYsnzdkIb5a0YlJzjU5U',
            'transportMode' => 'car',
            'origin' => $coordenadasDestinos[0],
            'destination' => end($coordenadasDestinos),
            'return' => 'polyline,summary',
        ] + $solicitud);

        // Manejar la respuesta de la API y devolver la ruta
        $ruta = $this->procesarRespuesta($response);

        return response()->json(['ruta' => $ruta]);
    }

    private function geocodificarDestinos($destinos)
    {
        $cliente = new Client();
        $coordenadasDestinos = [];

        foreach ($destinos as $destino) {
            $coordenadasDestino = $this->geocodificarDestinoEjemplo($destino);

            if ($coordenadasDestino) {
                $coordenadasDestinos[] = $coordenadasDestino;
            }
        }

        return $coordenadasDestinos;
    }

    private function geocodificarDestinoEjemplo($destino)
    {
        $apiKey = '7a6TfdGhaJbpPMG2ehCfSExHYsnzdkIb5a0YlJzjU5U';
        $address = urlencode($destino);
        $url = 'https://geocode.search.hereapi.com/v1/geocode?q=?q=$address&apiKey=$apiKey';
        $response = Http::get($url);
        $data = $response->json();
        dd($data);
        $firstResult = $data['items'][0];
        $position = $firstResult['position'];

        return [
            'lat' => $position['lat'],
            'lng' => $position['lng'],
        ];
    }

    private function procesarRespuesta($response)
    {
        // tenemos que poner lo que nos devuelve la api de here 
        $ruta = $response['routes'][0]['sections'][0]['polyline'];

        return $ruta;
    }
}