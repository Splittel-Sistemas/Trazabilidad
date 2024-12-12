<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenVenta;
use App\Models\OrdenFabricacion;

class CorteController extends Controller
{
    // Muestra la vista principal
    public function index()
    {
        return view('Estaciones.cortes');
    }

    // Devuelve los datos para el DataTable
    public function getData()
    {
        // Recupera todos los datos de OrdenFabricacion con la relación OrdenVenta
        $data = OrdenFabricacion::with('OrdenVenta')->get();

        // Devuelve los datos en formato JSON para el DataTable
        return response()->json(['data' => $data]);
    }

    // Devuelve los detalles de una orden específica
    public function getDetalles($id)
    {
        // Encuentra la orden de fabricación específica
        $orden = OrdenFabricacion::with('OrdenVenta')->find($id);

        // Verifica si la orden existe
        if (!$orden) {
            return response()->json(['error' => 'Orden no encontrada'], 404);
        }

        // Devuelve los detalles de la orden
        return response()->json($orden);
    }
}
