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
        ->select('OrdenVenta', 'NombreCliente')
        ->when($search, function ($query, $search) {
            return $query->where('ordenventa.OrdenVenta', 'like', "%$search%")
                         ->orWhere('ordenventa.NombreCliente', 'like', "%$search%");
                        })
        ->groupBy('ordenventa.OrdenVenta', 'ordenventa.NombreCliente')
        ->get();
       

        return response()->json($ordenesVenta);
    }
    // Controlador para las órdenes de fabricación
    public function obtenerOrdenesFabricacion(Request $request)
    {
        $search = $request->input('search');
    
        $ordenesFabricacion = DB::table('ordenfabricacion')
            ->leftJoin('partidasof', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
            ->select(
                'ordenfabricacion.OrdenFabricacion', 
                'ordenfabricacion.Articulo', 
                'ordenfabricacion.Descripcion',
                'ordenfabricacion.CantidadTotal',
                DB::raw('SUM(partidasof.cantidad_partida) as total_partidas'),
                DB::raw('ROUND((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100, 2) as progreso')
            )
            ->when($search, function ($query, $search) {
                return $query->where('ordenfabricacion.OrdenFabricacion', 'like', "%$search%")
                             ->orWhere('ordenfabricacion.Articulo', 'like', "%$search%")
                             ->orWhere('ordenfabricacion.Descripcion', 'like', "%$search%")
                             ->orWhere('ordenfabricacion.CantidadTotal', 'like', "%$search%");
            })
            ->groupBy(
                'ordenfabricacion.OrdenFabricacion', 
                'ordenfabricacion.Articulo',
                'ordenfabricacion.Descripcion',
                'ordenfabricacion.CantidadTotal'
            )
            ->get();
    
        return response()->json($ordenesFabricacion);
    }
    //boton detalles de la orden de venta        
    public function detallesventa(Request $request)
    {
    
        $idVenta = $request->input('id');

        $partidasAreas = DB::table('ordenventa')
            ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.id') 
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
            ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id') 
            ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id') 
            ->join('areas', 'partidas_areas.Areas_id', '=', 'areas.id') 
            ->where('ordenventa.OrdenVenta', $idVenta) 
            ->select('partidas_areas.Partidas_id', 'areas.nombre as Estado') 
            ->get();

    
        if ($partidasAreas->isEmpty()) {
        
            $partidasAreas = null; 
        }

        return response()->json([
            'partidasAreas' => $partidasAreas 
        ]);
    }
    //graficadores
    public function Graficador(Request $request)
    {
        $idVenta = $request->input('id');
        $tipo = $request->input('tipo'); 
        //para cargar los datos 
        if (!empty($idVenta)) {
            //estacion eccorte
            if ($tipo === 'cortes') {
                $result = DB::table('ordenventa')
                    ->join('ordenfabricacion', 'OrdenVenta.id', '=', 'ordenfabricacion.OrdenVenta_id') 
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                    ->where('ordenventa.OrdenVenta', $idVenta) 
                    ->select(
                        'ordenventa.OrdenVenta',
                        'ordenfabricacion.OrdenVenta_Id',
                        DB::raw('GROUP_CONCAT(ordenfabricacion.OrdenFabricacion) as OrdenesFabricacion'),
                        DB::raw('SUM(ordenfabricacion.CantidadTotal) as CantidadTotal'),
                        DB::raw('SUM(partidasof.cantidad_partida) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof.cantidad_partida) / SUM(ordenfabricacion.CantidadTotal)) * 100, 2) as Progreso')
                    )
                    ->groupBy('ordenventa.OrdenVenta', 'ordenfabricacion.OrdenVenta_ID')
                    ->get();
            //estacion suministros
            } elseif ($tipo === 'suministros') {
                $result = DB::table('ordenventa')
                    ->join('ordenfabricacion', 'OrdenVenta.id', '=', 'ordenfabricacion.OrdenVenta_id') 
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id') 
                    ->where('ordenventa.OrdenVenta', $idVenta)
                    ->select(
                        'ordenventa.OrdenVenta',
                        'ordenfabricacion.OrdenVenta_Id',
                        DB::raw('GROUP_CONCAT(ordenfabricacion.OrdenFabricacion) as OrdenesFabricacion'),
                        DB::raw('SUM(ordenfabricacion.CantidadTotal) as CantidadTotal'),
                        DB::raw('SUM(partidas.CantidadaPartidas) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidas.CantidadaPartidas) / SUM(ordenfabricacion.CantidadTotal)) * 100, 2) as Progreso')
                    )
                    ->groupBy('ordenventa.OrdenVenta', 'ordenfabricacion.OrdenVenta_ID')
                    ->get();
            //estacion preparado
            } elseif ($tipo === 'preparado') {
                $result = DB::table('ordenventa')
                    ->join('ordenfabricacion', 'OrdenVenta.id', '=', 'ordenfabricacion.OrdenVenta_id') 
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id') 
                    ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id') 
                    ->where('ordenventa.OrdenVenta', $idVenta)
                    ->where('partidas_areas.Areas_id', 4)
                    ->select(
                        'ordenventa.OrdenVenta',
                        'ordenfabricacion.OrdenVenta_Id',
                        DB::raw('GROUP_CONCAT(ordenfabricacion.OrdenFabricacion) as OrdenesFabricacion'),
                        DB::raw('SUM(ordenfabricacion.CantidadTotal) as CantidadTotal'),
                        DB::raw('SUM(partidas_areas.Cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidas_areas.Cantidad) / SUM(ordenfabricacion.CantidadTotal)) * 100, 2) as Progreso')
                    )
                    ->groupBy('ordenventa.OrdenVenta', 'ordenfabricacion.OrdenVenta_ID')
                    ->get();
            //estacion ensamble
            }elseif($tipo === 'ensamble'){
                $result= DB::table('ordenventa')
                    ->join('ordenfabricacion', 'OrdenVenta.id', '=', 'ordenfabricacion.OrdenVenta_id') 
                     ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id') 
                    ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id') 
                    ->where('ordenventa.OrdenVenta', $idVenta)
                    ->where('partidas_areas.Areas_id', 4)
                    ->select(
                        'ordenventa.OrdenVenta',
                        'ordenfabricacion.OrdenVenta_Id',
                        DB::raw('GROUP_CONCAT(ordenfabricacion.OrdenFabricacion) as OrdenesFabricacion'),
                        DB::raw('SUM(ordenfabricacion.CantidadTotal) as CantidadTotal'),
                        DB::raw('SUM(partidas_areas.Cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidas_areas.Cantidad) / SUM(ordenfabricacion.CantidadTotal)) * 100, 2) as Progreso')
                    )
                    ->groupBy('ordenventa.OrdenVenta', 'ordenfabricacion.OrdenVenta_ID')
                    ->get();
            //estacion pulido
            }elseif($tipo === 'pulido'){
                $result= DB::table('ordenventa')
                    ->join('ordenfabricacion', 'OrdenVenta.id', '=', 'ordenfabricacion.OrdenVenta_id') 
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id') 
                    ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id') 
                    ->where('ordenventa.OrdenVenta', $idVenta)
                    ->where('partidas_areas.Areas_id', 6)
                    ->select(
                        'ordenventa.OrdenVenta',
                        'ordenfabricacion.OrdenVenta_Id',
                        DB::raw('GROUP_CONCAT(ordenfabricacion.OrdenFabricacion) as OrdenesFabricacion'),
                        DB::raw('SUM(ordenfabricacion.CantidadTotal) as CantidadTotal'),
                        DB::raw('SUM(partidas_areas.Cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidas_areas.Cantidad) / SUM(ordenfabricacion.CantidadTotal)) * 100, 2) as Progreso')
                    )
                    ->groupBy('ordenventa.OrdenVenta', 'ordenfabricacion.OrdenVenta_ID')
                    ->get();
            //estacion medicion
            }elseif($tipo === 'medicion'){
                $result= DB::table('ordenventa')
                    ->join('ordenfabricacion', 'OrdenVenta.id', '=', 'ordenfabricacion.OrdenVenta_id') 
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id') 
                    ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id') 
                    ->where('ordenventa.OrdenVenta', $idVenta)
                    ->where('partidas_areas.Areas_id', 7)
                    ->select(
                        'ordenventa.OrdenVenta',
                        'ordenfabricacion.OrdenVenta_Id',
                        DB::raw('GROUP_CONCAT(ordenfabricacion.OrdenFabricacion) as OrdenesFabricacion'),
                        DB::raw('SUM(ordenfabricacion.CantidadTotal) as CantidadTotal'),
                        DB::raw('SUM(partidas_areas.Cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidas_areas.Cantidad) / SUM(ordenfabricacion.CantidadTotal)) * 100, 2) as Progreso')
                    )
                    ->groupBy('ordenventa.OrdenVenta', 'ordenfabricacion.OrdenVenta_ID')
                    ->get();
            //estacion visualizacion
            }elseif($tipo === 'visualizacion'){
                $result= DB::table('ordenventa')
                    ->join('ordenfabricacion', 'OrdenVenta.id', '=', 'ordenfabricacion.OrdenVenta_id') 
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id') 
                    ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id') 
                    ->where('ordenventa.OrdenVenta', $idVenta)
                    ->where('partidas_areas.Areas_id', 8)
                    ->select(
                        'ordenventa.OrdenVenta',
                        'ordenfabricacion.OrdenVenta_Id',
                        DB::raw('GROUP_CONCAT(ordenfabricacion.OrdenFabricacion) as OrdenesFabricacion'),
                        DB::raw('SUM(ordenfabricacion.CantidadTotal) as CantidadTotal'),
                        DB::raw('SUM(partidas_areas.Cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidas_areas.Cantidad) / SUM(ordenfabricacion.CantidadTotal)) * 100, 2) as Progreso')
                    )
                    ->groupBy('ordenventa.OrdenVenta', 'ordenfabricacion.OrdenVenta_ID')
                    ->get();
            //estacion empaque
            }elseif($tipo === 'empaque'){
                $result = DB::table('ordenventa')
                    ->join('ordenfabricacion', 'OrdenVenta.id', '=', 'ordenfabricacion.OrdenVenta_id') 
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id') 
                    ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id') 
                    ->where('ordenventa.OrdenVenta', $idVenta)
                    ->where('partidas_areas.Areas_id', 9)
                    ->select(
                        'ordenventa.OrdenVenta',
                        'ordenfabricacion.OrdenVenta_Id',
                        'partidas_areas.Areas_id',  
                        DB::raw('GROUP_CONCAT(ordenfabricacion.OrdenFabricacion) as OrdenesFabricacion'),
                        DB::raw('SUM(ordenfabricacion.CantidadTotal) as CantidadTotal'),
                        DB::raw('SUM(partidas_areas.Cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidas_areas.Cantidad) / SUM(ordenfabricacion.CantidadTotal)) * 100, 2) as Progreso')
                    )
                    ->groupBy('ordenventa.OrdenVenta', 'ordenfabricacion.OrdenVenta_ID', 'partidas_areas.Areas_id')  
                    ->get();

            } else {
                return response()->json([], 400); 
            }
           
            return response()->json($result);
        } else {
            return response()->json([], 204); 
        }
    }
}


