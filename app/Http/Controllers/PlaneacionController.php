<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


/*class PlaneacionController extends Controller
{
    //ñ
    public function getOrdersFromSAP()
    {
        $client = new Client([
            'base_uri' => env('HN_OPTRONICS'),
            'auth' => [env('USR_LECTURA'), env('SPL.Lectura202xx4.')],
        ]);

        try {
            $response = $client->get('/odata/Orders', [
                'query' => [
                    // Agregar filtros si son necesarios, ejemplo:
                    '$filter' => 'OrderDate ge 2024-11-01',
                    '$top' => 10,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            return response()->json([
                'status' => 'success',
                'data' => $data['value'] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}*/
