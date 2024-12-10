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
        // Recupera datos con la relación cargada
        $data = OrdenFabricacion::with('OrdenVenta')->get();

        // Devuelve los datos en formato JSON
        return response()->json(['data' => $data]);
    }
}
