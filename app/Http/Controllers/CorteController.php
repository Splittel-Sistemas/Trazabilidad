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
        // Recuperamos los datos y los relacionamos correctamente
        $data = OrdenFabricacion::with('OrdenVenta')->get(); 

        // Regresamos los datos en formato JSON
        return response()->json($data);
    }
}
