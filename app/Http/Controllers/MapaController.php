<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MapaController extends Controller
{
    //
    public function getCoordenadas(Request $request)
    {
        $apiKey = '7a6TfdGhaJbpPMG2ehCfSExHYsnzdkIb5a0YlJzjU5U';
        $address = urlencode($request->direccion);
        $countryName = "Uruguay";
        $url = "https://geocode.search.hereapi.com/v1/geocode?apiKey=$apiKey&q=$address&country=$countryName";

        $response = @file_get_contents($url);

        if ($response === false) {
            return 'Error: No se pudo realizar la solicitud.';
        }

        $data = json_decode($response);

        if ($data === null || empty($data->items)) {
            return 'Error: No se encontraron resultados.';
        }

        return $data->items[0]->position;
    }
}
