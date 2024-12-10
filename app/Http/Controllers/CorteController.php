<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenVenta;
use App\Models\OrdenFabricacion;

class CorteController extends Controller
{
    public function index()
    {
        return view('Estaciones.cortes');
    }

    public function getData()
{
    $data = OrdenFabricacion::with('OrdenVenta')->get();
    dd($data); // Para ver la estructura de los datos
    return response()->json([
        'data' => $data
    ]);
}

    

}
