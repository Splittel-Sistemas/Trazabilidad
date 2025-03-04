<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DashboardControlle extends Controller
{
    // ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
    //->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
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
        ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
            ->where('partidasof_areas.Areas_id', 9)
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
            ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                //fecha de inicio
                DB::raw("MAX(partidasof.FechaComienzo) AS TiempoCorte"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 3 THEN partidasof_areas.FechaComienzo END) as TiempoSuministro"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 4 THEN partidasof_areas.FechaComienzo END) as TiempoPreparado"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 5 THEN partidasof_areas.FechaComienzo END) as TiempoEnsamble"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 6 THEN partidasof_areas.FechaComienzo END) as TiempoPulido"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 7 THEN partidasof_areas.FechaComienzo END) as TiempoMedicion"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 8 THEN partidasof_areas.FechaComienzo END) as TiempoVisualizacion"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 9 THEN partidasof_areas.FechaComienzo END) as TiempoAbierto"),
                // Fecha final
                DB::raw("MAX(partidasof.FechafINALIZACION) AS FinCorte"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 3 THEN partidasof_areas.FechaTermina END) as FinSuministro"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 4 THEN partidasof_areas.FechaTermina END) as FinPreparado"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 5 THEN partidasof_areas.FechaTermina END) as FinEnsamble"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 6 THEN partidasof_areas.FechaTermina END) as FinPulido"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 7 THEN partidasof_areas.FechaTermina END) as FinMedicion"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 8 THEN partidasof_areas.FechaTermina END) as FinVisualizacion"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 9 THEN partidasof_areas.FechaTermina END) as FinAbierto")
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
    {/*
        $totalOrdenes = DB::table('ordenfabricacion')->count();

        $ordenesAbiertas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('partidasof_areas')
                    ->whereRaw('partidasof_areas.PartidasOF_id = partidasof.id')
                    ->where('partidasof_areas.Areas_id', 9);
            })
            ->select('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.Articulo', 'ordenfabricacion.Descripcion', 'ordenfabricacion.CantidadTotal', 'partidasof.cantidad_partida')
            ->distinct()
            ->get();
        
        $ordenesAbiertasCount = $ordenesAbiertas->count();
        
        $porcentajeAbiertas = $totalOrdenes > 0 ? ($ordenesAbiertasCount / $totalOrdenes) * 100 : 0;
        dd($ordenesAbiertas);
        
        return response()->json([
            'retrabajo' => round($porcentajeAbiertas, 2),
            'ordenes' => $ordenesAbiertas
        ]);*/
        
    }

    public function graficas()
    {
        // Órdenes por día

        /*
        $ordenesPorDia = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
            ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
            ->where('partidasOF_areas.Areas_id', 9)
            ->selectRaw(

                'DATE_FORMAT(ordenfabricacion.created_at, "%Y-%m-%d") as dia, COUNT(DISTINCT ordenfabricacion.id) as total')
            ->groupBy('dia')
            ->orderBy('dia', 'asc')
            ->get();

            dd($ordenesPorDia);
    
        // Órdenes por semana
        $ordenesPorSemana = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
            ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
            ->where('partidasOF_areas.Areas_id', 9)
            ->selectRaw('YEARWEEK(ordenfabricacion.created_at) as semana, COUNT(DISTINCT ordenfabricacion.id) as total')
            ->groupBy('semana')
            ->orderBy('semana', 'asc')
            ->get();
    
        // Órdenes por mes
        $ordenesPorMes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
            ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
            ->where('partidasOF_areas.Areas_id', 9)
            ->selectRaw('DATE_FORMAT(ordenfabricacion.created_at, "%Y-%m") as mes, COUNT(DISTINCT ordenfabricacion.id) as total')
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();
    
        return response()->json([
            'ordenesPorDia' => $ordenesPorDia,
            'ordenesPorSemana' => $ordenesPorSemana,
            'ordenesPorMes' => $ordenesPorMes,
        ]);*/
    }


    public function progreso()
    {
    
        $areas = ['3', '4', '5', '6', '7', '8', '9']; // No filtrar por área '2'
        $progreso = [];
    
        // Consulta para el área 2 (Cortes)
        $cortes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'partidasof.OrdenFabricacion_id',
                DB::raw('SUM(partidasof.cantidad_partida) as TotalPartidas'),
                DB::raw('ROUND((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100, 2) as Progreso')
            )
            ->groupBy('partidasof.OrdenFabricacion_id', 'ordenfabricacion.CantidadTotal')
            ->get();
        // dd($cortes);
    
        // Si quieres solo un valor de progreso para el área 2, calculamos un promedio
        $progreso['2'] = $cortes->avg('Progreso');  // Promedio de todos los progresos de cortes
    
        // Cálculo del progreso para las demás áreas
        foreach ($areas as $area) {
            $cantidadPorArea = DB::table('partidasof_areas')
                ->join('partidasof', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
                ->where('partidasof_areas.Areas_id', $area)
                ->select(
                    'partidasof_areas.Areas_id',
                    'ordenfabricacion.CantidadTotal',
                    'partidasof_areas.Partidasof_id',
                    DB::raw('SUM(partidasof_areas.Cantidad) as TotalPartidas'),
                    DB::raw('ROUND((SUM(partidasof_areas.Cantidad) / ordenfabricacion.CantidadTotal) * 100, 2) as Progreso')
                )
                ->groupBy('partidasof_areas.Areas_id', 'ordenfabricacion.CantidadTotal', 'partidasof_areas.Partidasof_id')
                ->get();
    
            // Solo guardamos el progreso calculado en la consulta
            $progreso[$area] = $cantidadPorArea->avg('Progreso'); // Promedio del progreso de cada área
        }
    
        return response()->json([
            'progreso' => $progreso
        ]);
    }
    

    public function progresoof()
    {
        $ordenes = DB::table('ordenfabricacion')
            ->leftJoin('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->leftJoin('partidas', 'partidasof.id', '=', 'partidas.PartidasOf_id')
            ->leftJoin('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')
            ->select(
                'ordenfabricacion.id',
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal'
            )
            ->groupBy('ordenfabricacion.id', 'ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
            ->get();
    
        // Mapeo de área de la base de datos a nombre de área
        $areas = [
            '2' => 'Cortes', '3' => 'Suministro', '4' => 'Preparado', 
            '5' => 'Ensamble', '6' => 'Pulido', '7' => 'Medicion', '8' => 'Visualizacion', '9' => 'Abierto'
        ];
        $progreso = [];
        $totalProgresoAcumulado = 0;
        $contadorOrdenesConDatos = 0;
    
        foreach ($ordenes as $orden) {
            $totalCantidad = $orden->CantidadTotal;
            $ordenId = $orden->id;
            $progresoOrden = [];
            $sumaPorcentajes = 0;
            $cantidadAreasConDatos = 0;
            $avancesArea9 = 0;
    
            foreach ($areas as $areaId => $areaName) {
                $cantidadPorArea = DB::table('partidas_areas')
                    ->join('partidas', 'partidas_areas.Partidas_id', '=', 'partidas.id')
                    ->join('partidasof', 'partidas.PartidasOf_id', '=', 'partidasof.id')
                    ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
                    ->where('partidas_areas.Areas_id', $areaId)
                    ->where('ordenfabricacion.id', $ordenId)
                    ->sum('partidas_areas.Cantidad');
    
                $porcentaje = ($totalCantidad > 0) ? ($cantidadPorArea / max($totalCantidad, 1)) * 100 : 0;
                $progresoOrden[$areaName] = number_format($porcentaje, 2, '.', '');
    
                if ($porcentaje > 0) {
                    $sumaPorcentajes += $porcentaje;
                    $cantidadAreasConDatos++;
                }
    
                if ($areaId == '9' && $porcentaje > 0) {
                    $avancesArea9 = $porcentaje;
                }
            }
    
            $progresoTotal = ($avancesArea9 == 100) ? 100 : (($cantidadAreasConDatos > 0) ? ($sumaPorcentajes / $cantidadAreasConDatos) : 0);
            $totalProgresoAcumulado += $progresoTotal;
            $contadorOrdenesConDatos++;
    
            $progreso[$orden->OrdenFabricacion] = [
                'progreso_orden' => number_format($progresoTotal, 2, '.', ''),
                'Cantidad_total' => number_format($totalCantidad, 2, '.', ''),
                'detalle' => $progresoOrden
            ];
        }
    
        $progresoTotalFinal = ($contadorOrdenesConDatos > 0) ? ($totalProgresoAcumulado / $contadorOrdenesConDatos) : 0;
    
        return response()->json([
            'progreso' => $progreso
        ]);
    }
    
    


    
        /*

    // Contar el total de órdenes de fabricación
    $totalOf = $of->count();

    // Lista de áreas
    $areas = ['2', '3', '4', '5', '6', '7', '8', '9'];
    $progreso = [];

    foreach ($areas as $area) {
        // Obtener la cantidad total para el área
        $cantidadPorArea = DB::table('partidas_areas')
            ->join('partidas', 'partidas_areas.Partidas_id', '=', 'partidas.id')
            ->join('partidasof', 'partidas.PartidasOf_id', '=', 'partidasof.id')
            ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
            ->where('partidas_areas.Areas_id', $area)
            ->sum('partidas_areas.Cantidad');

        // Calcular el porcentaje
        $porcentaje = ($totalOf > 0) ? ($cantidadPorArea / $totalOf) * 100 : 0;
        $progreso[$area] = number_format($porcentaje, 2, '.', '');
    }
    dd($progreso);
    return response()->json([
        'progreso' => $progreso
    ]);*/
}



