<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DashboardControlle extends Controller
{
    //
    public function index()
    {

        return view('layouts.principal');
    }

    public function Ordenes()
    {
        
        $totalOrdenes = DB::table('ordenfabricacion')->count();
        if ($totalOrdenes === 0) {
            return response()->json([
                'retrabajo' => 0
            ]);
        }
        
        $ordenesRetrabajo = DB::table('ordenfabricacion')
            ->where('EstatusEntrega', 1)
            ->count();
       
        $retrabajoPorcentaje = round(($ordenesRetrabajo / $totalOrdenes) * 100, 2);

        return response()->json([
            'retrabajo' => $retrabajoPorcentaje
        ]);
        
    }
  
    public function cerradas()
    {
        // Definir correctamente la variable $totalOrdenes
        $totalOrdenes = DB::table('ordenfabricacion')->count();
    
        // Obtener las órdenes cerradas
        $ordenes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
            ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
            ->where('partidas_areas.Areas_id', 9)
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.Articulo',
                'ordenfabricacion.Descripcion',
                'ordenfabricacion.CantidadTotal',
                'partidasof.cantidad_partida'
            )
            ->distinct()
            ->get();
    
        // Obtener los tiempos de las etapas
        $tiempos = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
            ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                //fecha de inicio
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 2 THEN partidas_areas.FechaComienzo END) as TiempoCorte"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 3 THEN partidas_areas.FechaComienzo END) as TiempoSuministro"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 4 THEN partidas_areas.FechaComienzo END) as TiempoPreparado"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 5 THEN partidas_areas.FechaComienzo END) as TiempoEnsamble"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 6 THEN partidas_areas.FechaComienzo END) as TiempoPulido"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 7 THEN partidas_areas.FechaComienzo END) as TiempoMedicion"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 8 THEN partidas_areas.FechaComienzo END) as TiempoVisualizacion"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 9 THEN partidas_areas.FechaComienzo END) as TiempoAbierto"),
                // Fecha final
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 2 THEN partidas_areas.FechaTermina END) as FinCorte"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 3 THEN partidas_areas.FechaTermina END) as FinSuministro"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 4 THEN partidas_areas.FechaTermina END) as FinPreparado"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 5 THEN partidas_areas.FechaTermina END) as FinEnsamble"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 6 THEN partidas_areas.FechaTermina END) as FinPulido"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 7 THEN partidas_areas.FechaTermina END) as FinMedicion"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 8 THEN partidas_areas.FechaTermina END) as FinVisualizacion"),
                DB::raw("MAX(CASE WHEN partidas_areas.Areas_id = 9 THEN partidas_areas.FechaTermina END) as FinAbierto")
            )
            ->groupBy('ordenfabricacion.OrdenFabricacion')
            ->get();

           
    
        // Combina los resultados de las órdenes con los tiempos
        $ordenesConTiempos = $ordenes->map(function($orden) use ($tiempos) {
            
            $tiempo = $tiempos->firstWhere('OrdenFabricacion', $orden->OrdenFabricacion);
            //inicio de tiempos 
            $orden->TiempoCorte = $tiempo ? $tiempo->TiempoCorte : "";
            $orden->TiempoSuministro = $tiempo ? $tiempo->TiempoSuministro : "";
            $orden->TiempoPreparado = $tiempo ? $tiempo->TiempoPreparado : "";
            $orden->TiempoEnsamble = $tiempo ? $tiempo->TiempoEnsamble : "";
            $orden->TiempoPulido = $tiempo ? $tiempo->TiempoPulido : "";
            $orden->TiempoMedicion = $tiempo ? $tiempo->TiempoMedicion : "";
            $orden->TiempoVisualizacion = $tiempo ? $tiempo->TiempoVisualizacion : "";
            $orden->TiempoAbierto = $tiempo ? $tiempo->TiempoAbierto : "";
            
           //final de tiempos
            $orden->FinCorte = $tiempo ? $tiempo->FinCorte : "";
            $orden->FinSuministro = $tiempo ? $tiempo->FinSuministro : "";
            $orden->FinPreparado = $tiempo ? $tiempo->FinPreparado : "";
            $orden->FinEnsamble = $tiempo ? $tiempo->FinEnsamble : "";
            $orden->FinPulido = $tiempo ? $tiempo->FinPulido : "";
            $orden->FinMedicion = $tiempo ? $tiempo->FinMedicion : "";
            $orden->FinVisualizacion = $tiempo ? $tiempo->FinVisualizacion : "";
            $orden->FinAbierto = $tiempo ? $tiempo->FinAbierto : "";
            
            return $orden;
        });
        
    
        // Calcular el porcentaje de órdenes cerradas
        $ordenesCerradasCount = $ordenesConTiempos->count();
        $porcentajeCerradas = $totalOrdenes > 0 ? ($ordenesCerradasCount / $totalOrdenes) * 100 : 0;
    
        // Retornar los datos en formato JSON
        return response()->json([
            'retrabajo' => round($porcentajeCerradas, 2),
            'ordenes' => $ordenesConTiempos
        ]);
    }
    
    public function abiertas()
    {
        $totalOrdenes = DB::table('ordenfabricacion')->count();
        
        $ordenesAbiertas = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
                ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
                ->where('partidas_areas.Areas_id', 2)
                ->select('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.Articulo', 'ordenfabricacion.Descripcion', 'ordenfabricacion.CantidadTotal', 'partidasof.cantidad_partida')
                ->distinct()
                ->get(); // Obtiene los datos

        // Para contar el número de resultados
        $ordenesAbiertasCount = $ordenesAbiertas->count();
        
        $porcentajeAbiertas = $totalOrdenes > 0 ? ($ordenesAbiertasCount / $totalOrdenes) * 100 : 0;
        
        return response()->json([
            'retrabajo' => round($porcentajeAbiertas, 2),
            'ordenes' => $ordenesAbiertas
        ]);
    }
    
    public function graficas()
    {
        // Órdenes por día
        $ordenesPorDia = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
            ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
            ->where('partidas_areas.Areas_id', 9)
            ->selectRaw('DATE_FORMAT(ordenfabricacion.created_at, "%Y-%m-%d") as dia, COUNT(DISTINCT ordenfabricacion.id) as total')
            ->groupBy('dia')
            ->orderBy('dia', 'asc')
            ->get();
    
        // Órdenes por semana
        $ordenesPorSemana = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
            ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
            ->where('partidas_areas.Areas_id', 9)
            ->selectRaw('YEARWEEK(ordenfabricacion.created_at) as semana, COUNT(DISTINCT ordenfabricacion.id) as total')
            ->groupBy('semana')
            ->orderBy('semana', 'asc')
            ->get();
    
        // Órdenes por mes
        $ordenesPorMes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
            ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
            ->where('partidas_areas.Areas_id', 9)
            ->selectRaw('DATE_FORMAT(ordenfabricacion.created_at, "%Y-%m") as mes, COUNT(DISTINCT ordenfabricacion.id) as total')
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();
    
        return response()->json([
            'ordenesPorDia' => $ordenesPorDia,
            'ordenesPorSemana' => $ordenesPorSemana,
            'ordenesPorMes' => $ordenesPorMes,
        ]);
    }

    public function progreso()
    {
        $totalCantidad = DB::table('ordenfabricacion')
            ->leftJoin('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->leftJoin('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
            ->leftJoin('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
            ->select('ordenfabricacion.CantidadTotal')
            ->distinct()  
            ->sum('ordenfabricacion.CantidadTotal');  

        // Áreas que estás utilizando
        $areas = ['2', '3', '4', '5', '6', '7', '8', '9'];

        $progreso = [];

        foreach ($areas as $area) {
            
            $cantidadPorArea = DB::table('partidas_areas')
                ->join('partidas', 'partidas_areas.Partidas_id', '=', 'partidas.id')
                ->join('partidasof', 'partidas.PartidasOf_id', '=', 'partidasof.id')
                ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
                ->where('partidas_areas.Areas_id', $area)  
                ->sum('partidas_areas.Cantidad');  
            $porcentaje = ($totalCantidad > 0) ? ($cantidadPorArea / $totalCantidad) * 100 : 0;
            $progreso[$area] = number_format($porcentaje, 2, '.', '');  
        }
        
        return response()->json([
            'progreso' => $progreso
        ]);
    }


}


