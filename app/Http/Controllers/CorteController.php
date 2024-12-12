<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenFabricacion;
use Illuminate\Support\Facades\DB;
use App\Models\PartidasOF;

class CorteController extends Controller
{
    //vista
    public function index()
    {
        return view('Estaciones.cortes');
    }

    public function getData()
    {
        $data = DB::table('OrdenFabricacion')  
                ->join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')  
                ->select(
                    'OrdenFabricacion.id', 
                    'OrdenFabricacion.OrdenVenta_id', 
                    'OrdenFabricacion.OrdenFabricacion', 
                    'OrdenFabricacion.Articulo', 
                    'OrdenFabricacion.Descripcion', 
                    'OrdenFabricacion.CantidadTotal', 
                    'OrdenFabricacion.FechaEntregaSAP', 
                    'OrdenFabricacion.FechaEntrega', 
                    'OrdenFabricacion.created_at', 
                    'OrdenFabricacion.updated_at'
                )
                ->get();
        
        //dd($data); 
    
        return response()->json(['data' => $data]);
    }
    //modal
    public function verDetalles($id)
    {
        $ordenFabricacion = DB::table('OrdenFabricacion')
                            ->select('OrdenFabricacion.OrdenFabricacion', 
                                        'OrdenFabricacion.FechaEntrega', 
                                        'OrdenFabricacion.Articulo', 
                                        'OrdenFabricacion.Estado')
                            ->where('OrdenFabricacion.id', $id) 
                            ->first();
                            

        if ($ordenFabricacion) {
            return response()->json(['orden_fabricacion' => $ordenFabricacion]);
        } else {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }
    }
    public function PlaneacionFF(Request $request)
{
    $fechaSeleccionada = $request->input('fecha'); // Recibimos solo una fecha

    $status = "error";
    $tablaOrdenes = '';

    // Si la fecha está presente, asignamos un estado de éxito
    if ($fechaSeleccionada) {
        $status = "success";
    } else {
        $status = "empty";
    }

    return response()->json([
        'status' => $status,
        'data' => $tablaOrdenes,
        'fechaSeleccionada' => $fechaSeleccionada
    ]);
}


}
    





    
