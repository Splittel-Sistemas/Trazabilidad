<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\PartidasOF;
use App\Models\OrdenFabricacion;
use App\Models\Partidas;
use App\Models\OrdenVenta;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use TCPDF;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;

class BusquedaController extends Controller
{
    //vista
    public function index(Request $request)
    {
        $partidaId = 1;
        $partidasAreas = DB::table('partidas_areas')
        ->where('Partidas_id', $partidaId)  
        ->get();

        return view('layouts.busquedas', compact('partidasAreas'));
    
    }
     // Controlador para las órdenes de Venta
    public function obtenerOrdenesVenta(Request $request)
    {
        $search = $request->input('search');
        // Orden de venta, nombre del cliente, artículo, cantidad total
        $ordenesVenta = DB::table('ordenventa')
            ->join('ordenfabricacion', 'OrdenVenta_id', '=', 'ordenfabricacion.OrdenVenta_id')
            ->select('ordenventa.OrdenVenta', 'ordenventa.NombreCliente', 'ordenfabricacion.Articulo', 'ordenfabricacion.CantidadTotal')
            ->when($search, function ($query, $search) {
                return $query->where('ordenventa.OrdenVenta', 'like', "%$search%")
                             ->orWhere('ordenventa.NombreCliente', 'like', "%$search%")
                             ->orWhere('ordenfabricacion.Articulo', 'like', "%$search%")
                             ->orWhere('ordenfabricacion.CantidadTotal', 'like', "%$search%");
                            })
            ->groupBy('ordenventa.OrdenVenta', 'ordenventa.NombreCliente', 'ordenfabricacion.Articulo', 'ordenfabricacion.CantidadTotal')
            ->get(); 
           

        return response()->json($ordenesVenta);
    }
    // Controlador para las órdenes de fabricación
    public function obtenerOrdenesFabricacion(Request $request)
    {
        $search = $request->input('search');
    
        $ordenesFabricacion = DB::table('ordenfabricacion')
            ->join('partidasof', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal',
                DB::raw('SUM(partidasof.cantidad_partida) as total_partidas'),
                DB::raw('ROUND((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100, 2) as progreso')
            )
            ->when($search, function ($query, $search) {
                return $query->where('ordenfabricacion.OrdenFabricacion', 'like', "%$search%")
                             ->orWhere('partidasof.cantidad_partida', 'like', "%$search%");
            })
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
            ->get();
    
        return response()->json($ordenesFabricacion);
    }
    //boton detalles de la orden de venta        
    public function detallesventa(Request $request)
{
    // Obtener el id de la venta desde la solicitud
    $idVenta = $request->input('id');
   

    
 
    $ventaDetalle = DB::table('ordenventa')
        ->join('ordenfabricacion', 'OrdenVenta_id', '=', 'ordenfabricacion.OrdenVenta_id')
        ->select('ordenventa.OrdenVenta', 'ordenventa.NombreCliente', 'ordenfabricacion.Articulo', 'ordenfabricacion.CantidadTotal')
        ->where('ordenventa.OrdenVenta', '=', $idVenta) 
        ->first(); 

        //dd($ventaDetalle);
   
    if (!$ventaDetalle) {
        return response()->json(['error' => 'Venta no encontrada']);
    }
    $proceso = $request->input('CantidadTotal');
   
    
    $partidasAreas = DB::table('ordenfabricacion')
    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
    ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
    ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
    ->join('areas', 'partidas_areas.Areas_id', '=', 'areas.id') // Unión con la tabla areas
    ->where('ordenfabricacion.cantidadtotal', $proceso)
    ->select('partidas_areas.Partidas_id', 'areas.nombre as Estado') // Selección de columnas específicas
    ->get(); // Obtiene los resultados como una colección






    /*
        $partidasAreas = DB::table('partidas_areas')
        ->join('areas', 'partidas_areas.Areas_id', '=', 'areas.id')  
        ->join('partidas', 'partidas_areas.Partidas_id', '=', 'partidas.id') 
        ->where('partidas_areas.Partidas_id', '=', 1)  
        ->select('partidas_areas.Partidas_id', 'areas.nombre as Estado')  
        ->get();*/
    
            
      


    if ($partidasAreas->isEmpty()) {
        return response()->json(['error' => 'No se encontraron partidas para esta venta']);
    }

    return response()->json([
        'ventaDetalle' => $ventaDetalle,
        'partidasAreas' => $partidasAreas
    ]);
}


/*$partidasAreas = DB::table('partidas_areas')
        ->join('partidas', 'partidas_areas.Partidas_id', '=', 'partidas.id') // Unir con la tabla 'partidas' mediante 'Partidas_id'
        ->join('partidasof', 'partidas.PartidasOF_id', '=', 'partidasof.id') // Unir con la tabla 'partidasof' mediante 'PartidasOF_'
        ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id') // Unir con la tabla 'ordenfabricacion' mediante 'OrdenFabricacion'
        ->join('areas', 'partidas_areas.Areas_id', '=', 'areas.id') // Unir con la tabla 'areas' para obtener el nombre de la etapa
        ->select('ordenfabricacion.id as OrdenFabricacion', 'areas.nombre as Estado') // Seleccionar la 'OrdenFabricacion' y 'Estado'
        ->where('ordenfabricacion.OrdenVenta_id', '=', 132033) // Filtrar por el ID de la venta
        ->get();*/
  


    
}
    



