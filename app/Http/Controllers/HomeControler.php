<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

class HomeControler extends Controller
{
    public function  Home(){
        return view('Home');
    }
    public function index()
    {
        // Verificar si el usuario está autenticado y activo
        $user = Auth::user();

        if (!$user || !$user->active) {
            // Si el usuario no está autenticado o está desactivado, lo redirigimos al login
            Auth::logout();  // Cerrar la sesión si el usuario está desactivado
            return redirect()->route('login_view')->withErrors(['email' => 'Tu cuenta ha sido desactivada.']);
        }
        // Si el usuario está activo, continua con la carga de la página
        return view('home');  // Ajusta esto al nombre de tu vista Home
    }
   
    public function graficas()
    {
        // Órdenes por día
        $ordenesPorDia = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
            ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
            ->where('partidasOF_areas.Areas_id', 9)
            ->selectRaw('DATE_FORMAT(ordenfabricacion.created_at, "%Y-%m-%d") as dia, COUNT(DISTINCT ordenfabricacion.id) as total')
            ->groupBy('dia')
            ->orderBy('dia', 'asc')
            ->get();
    
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
        ]);
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
    

        $progreso['2'] = $cortes->avg('Progreso');  
    
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
            $progreso[$area] = $cantidadPorArea->avg('Progreso'); 
        }
    
        return response()->json([
            'progreso' => $progreso
        ]);
    }

    public function graficasdia()
    {
        $fechaLimite = now()->subMonth(); // Obtiene la fecha de hace un mes desde hoy
        
    
        $areas = DB::table('partidasof_areas')
            ->join('partidasof', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal',
                'partidasof_areas.Areas_id',
                'partidasof_areas.id',
                'ordenfabricacion.FechaEntrega',
                DB::raw('SUM(partidasof_areas.cantidad) as SumaTotalcantidad_partida'),
                DB::raw('ROUND((SUM(partidasof_areas.cantidad) / ordenfabricacion.CantidadTotal) * 100, 2) as Progreso')
            )
            ->where('ordenfabricacion.FechaEntrega', '>=', $fechaLimite) 
            ->whereIn('partidasof_areas.Areas_id', [3, 4, 5, 6, 7, 8, 9]) 
            ->groupBy('partidasof_areas.Areas_id', 'partidasof_areas.id', 'ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal', 'ordenfabricacion.FechaEntrega')
            ->get();
    
        $cortes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal', 
                'ordenfabricacion.FechaEntrega',
                DB::raw('SUM(partidasof.cantidad_partida) as SumaTotalcantidad_partida'),
                DB::raw('ROUND((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100, 2) as Progreso')
            )
            ->where('ordenfabricacion.FechaEntrega', '>=', $fechaLimite) 
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal', 'ordenfabricacion.FechaEntrega')
            ->get();
    
        $totalOrdenes = DB::table('ordenfabricacion')
            ->where('FechaEntrega', '>=', $fechaLimite) 
            ->count();
    
        $estacionesAreas = [
            3 => 'plemasSuministro',
            4 => 'plemasPreparado',
            5 => 'plemasEnsamble',
            6 => 'plemasPulido',
            7 => 'plemasMedicion',
            8 => 'plemasVisualizacion',
            9 => 'plemasEmpaque'
        ];
    
        $estacionesCortes = [
            2 => 'plemasCorte'
        ];
    
        $datos = [];
    
        foreach (array_merge($estacionesAreas, $estacionesCortes) as $estacion) {
            $datos[$estacion] = [
                'completado' => 0,
                'pendiente' => 0,
                'totalOrdenes' => $totalOrdenes 
            ];
        }
    
        foreach ($areas as $area) {
            if (isset($estacionesAreas[$area->Areas_id])) {
                $nombreEstacion = $estacionesAreas[$area->Areas_id];
    
                if ((float)$area->Progreso == 100.00) {
                    $datos[$nombreEstacion]['completado']++;
                } else {
                    $datos[$nombreEstacion]['pendiente']++;
                }
            }
        }
    
        $completadosCorte = 0;
        $pendientesCorte = 0;
    
        foreach ($cortes as $corte) {
            if ((float)$corte->Progreso == 100.00) {
                $completadosCorte++;
            } else {
                $pendientesCorte++;
            }
        }
    
        $datos['plemasCorte']['completado'] = $completadosCorte;
        $datos['plemasCorte']['pendiente'] = $pendientesCorte;
    
        return response()->json($datos);
    }
    

    public function cerradas()
    {
        $fechaLimite = now()->subMonth(); // Fecha actual menos un mes
    
        // Contar todas las órdenes en la tabla con la restricción de fecha
        $totalOrdenes = DB::table('ordenfabricacion')
            ->where('FechaEntrega', '>=', $fechaLimite)
            ->count();
    
        // Contar órdenes completadas (las que llegaron al Área 9) dentro del rango de fecha
        $ordenesCompletadas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->where('partidasof_areas.Areas_id', 9) // Solo cerradas
            ->where('ordenfabricacion.FechaEntrega', '>=', $fechaLimite) // Filtrar por la fecha
            ->distinct('ordenfabricacion.id')
            ->count();
    
        // Contar órdenes abiertas (sin contar las completadas)
        $ordenesAbiertas = $totalOrdenes - $ordenesCompletadas;
    
        return response()->json([
            'ordenesCompletadas' => $ordenesCompletadas,
            'ordenesAbiertas' => $ordenesAbiertas,
            'totalOrdenes' => $totalOrdenes,
        ]);
    }
    

    public function tablasAbiertas()
    {
        $ordenesAbiertas = DB::table('ordenfabricacion')
        ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
        ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
        ->where(function ($query) {
            $query->whereNull('partidasof_areas.Areas_id')  // Incluye registros sin relación en partidasof_areas
                ->orWhereNotIn('partidasof_areas.Areas_id', [9, 3, 4, 5, 6, 7, 8]); // Excluye estos valores
        })
        ->select(
            'ordenfabricacion.OrdenFabricacion', 
            'ordenfabricacion.Articulo', 
            'ordenfabricacion.Descripcion', 
            'ordenfabricacion.CantidadTotal', 
            DB::raw('SUM(partidasof.cantidad_partida) as SumaTotalcantidad_partida')
        )
        ->groupBy(
            'ordenfabricacion.OrdenFabricacion',
            'ordenfabricacion.Descripcion',  
            'ordenfabricacion.Articulo',  
            'ordenfabricacion.CantidadTotal'
        )
        ->get();

        
    
            //dd($ordenesAbiertas);
         
    
        $ordenesAbiertasCount = $ordenesAbiertas->count();
    
        // Calculamos el porcentaje de ordenes abiertas
       
        return response()->json([
            'ordenes' => $ordenesAbiertas,
            'ordenesAbiertasCount' => $ordenesAbiertasCount,
        ]);
    }

    public function tablasCompletadas()
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
                DB::raw("MAX(partidasof.FechaComienzo) AS TiempoCorte"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 3 THEN partidasof_areas.FechaComienzo END) as TiempoSuministro"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 4 THEN partidasof_areas.FechaComienzo END) as TiempoPreparado"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 5 THEN partidasof_areas.FechaComienzo END) as TiempoEnsamble"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 6 THEN partidasof_areas.FechaComienzo END) as TiempoPulido"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 7 THEN partidasof_areas.FechaComienzo END) as TiempoMedicion"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 8 THEN partidasof_areas.FechaComienzo END) as TiempoVisualizacion"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 9 THEN partidasof_areas.FechaComienzo END) as TiempoAbierto"),
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
            $orden->TiempoCorte = $tiempo ? $tiempo->TiempoCorte : "";
            $orden->TiempoSuministro = $tiempo ? $tiempo->TiempoSuministro : "";
            $orden->TiempoPreparado = $tiempo ? $tiempo->TiempoPreparado : "";
            $orden->TiempoEnsamble = $tiempo ? $tiempo->TiempoEnsamble : "";
            $orden->TiempoPulido = $tiempo ? $tiempo->TiempoPulido : "";
            $orden->TiempoMedicion = $tiempo ? $tiempo->TiempoMedicion : "";
            $orden->TiempoVisualizacion = $tiempo ? $tiempo->TiempoVisualizacion : "";
            $orden->TiempoAbierto = $tiempo ? $tiempo->TiempoAbierto : "";
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

        // Calcular la fracción de órdenes cerradas
        $ordenesCerradasCount = $ordenesConTiempos->count();
        $fraccionCerradas = $totalOrdenes > 0 ? "$ordenesCerradasCount/$totalOrdenes" : "0/$totalOrdenes";

        // Retornar los datos en formato JSON
        return response()->json([
            'retrabajo' => $fraccionCerradas,
            'ordenes' => $ordenesConTiempos
        ]);
    }
  
    public function tablasemana()
    {
        $SumaCantidadTotalGeneral = DB::table('ordenfabricacion')->sum('CantidadTotal');
        
        // Obtener datos de áreas (para todos los procesos excepto Cortes)
        $DiazAreas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->whereIn('partidasof_areas.Areas_id', [3, 4, 5, 6, 7, 8, 9]) // Excluimos el área de Cortes (ID 2)
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'partidasof_areas.Areas_id',
                'ordenfabricacion.CantidadTotal', 'partidasof_areas.FechaComienzo',
                DB::raw('SUM(partidasof_areas.Cantidad) as SumaTotalcantidad_partida')
            )
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal', 'partidasof_areas.Areas_id', 'partidasof_areas.FechaComienzo')
            ->get();
    
        // Obtener los datos de Cortes (Área ID 2)
        $DiasCortes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal','partidasof.FechaComienzo',
                DB::raw('SUM(partidasof.Cantidad_partida) as SumaTotalcantidad_partida'),
                DB::raw($SumaCantidadTotalGeneral . ' as SumaCantidadTotalGeneral')
            )
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal','partidasof.FechaComienzo')
            ->get();
    
        // Mapeo de las áreas a los nombres de los procesos
        $areasMap = [
            2 => 'Cortes',
            3 => 'Suministro',
            4 => 'Preparado',
            5 => 'Ensamble',
            6 => 'Pulido',
            7 => 'Medicion',
            8 => 'Visualizacion',
            9 => 'Empacado'
        ];
    
        // Inicializamos los datos para el gráfico con días en español
        $labels = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $series = [
            ['name' => 'Cortes', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => [0,0,0,0,0,0,0]],
            ['name' => 'Suministro', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => [0,0,0,0,0,0,0]],
            ['name' => 'Preparado', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => [0,0,0,0,0,0,0]],
            ['name' => 'Ensamble', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => [0,0,0,0,0,0,0]],
            ['name' => 'Pulido', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => [0,0,0,0,0,0,0]],
            ['name' => 'Medicion', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => [0,0,0,0,0,0,0]],
            ['name' => 'Visualizacion', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => [0,0,0,0,0,0,0]],
            ['name' => 'Empacado', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => [0,0,0,0,0,0,0]]
        ];
    
        // Mapeo de días en inglés a español
        $dayMap = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        ];
    
        // Para los cortes (Área ID 2)
        foreach ($DiasCortes as $corte) {
            $dayOfWeek = Carbon::parse($corte->FechaComienzo)->dayOfWeek;
            $dayName = Carbon::parse($corte->FechaComienzo)->format('l');
            $dayNameInSpanish = $dayMap[$dayName] ?? null;
            
            if ($dayNameInSpanish) {
                $index = array_search($dayNameInSpanish, $labels);
    
                // Solo asignar datos para el área 'Cortes'
                if ($index !== false) {
                    $serieIndex = array_search('Cortes', array_column($series, 'name'));
                    $series[$serieIndex]['data'][$index] += $corte->SumaTotalcantidad_partida;
                }
            }
        }
    
        // Para otras áreas (3-9)
        foreach ($DiazAreas as $area) {
            $dayOfWeek = Carbon::parse($area->FechaComienzo)->dayOfWeek;
            $dayName = Carbon::parse($area->FechaComienzo)->format('l');
            $dayNameInSpanish = $dayMap[$dayName] ?? null;
            
            if ($dayNameInSpanish) {
                $index = array_search($dayNameInSpanish, $labels);
                $areaName = $areasMap[$area->Areas_id] ?? null;
                
                if ($areaName && $index !== false) {
                    // Encontramos el índice de la serie correspondiente y sumamos la cantidad
                    foreach ($series as &$serie) {
                        if ($serie['name'] == $areaName) {
                            $serie['data'][$index] += $area->SumaTotalcantidad_partida;
                        }
                    }
                }
            }
        }
    
        // Ordenar las series por el valor máximo de cada día (de mayor a menor)
        foreach ($series as &$serie) {
            arsort($serie['data']); // Ordena el arreglo de datos en cada serie de mayor a menor
        }
    
        // Devuelve los datos para el gráfico
        return response()->json([
            'labels' => $labels,
            'series' => $series
        ]);
    }

    public function tablasMes()
    {
        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;
        $diasEnMes = Carbon::now()->daysInMonth;
    
        // Obtener datos de todas las áreas dentro del mes actual
        $datos = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->select(
                DB::raw('COALESCE(partidasof_areas.Areas_id, 2) as Areas_id'),
                DB::raw('COALESCE(partidasof_areas.FechaComienzo, partidasof.FechaComienzo) as FechaComienzo'),
                DB::raw('SUM(COALESCE(partidasof_areas.Cantidad, partidasof.Cantidad_partida)) as SumaTotalcantidad_partida')
            )
            ->whereMonth(DB::raw('COALESCE(partidasof_areas.FechaComienzo, partidasof.FechaComienzo)'), $mesActual)
            ->whereYear(DB::raw('COALESCE(partidasof_areas.FechaComienzo, partidasof.FechaComienzo)'), $anioActual)
            ->groupBy('Areas_id', 'FechaComienzo')
            ->get();
    
        // Mapeo de áreas
        $areasMap = [
            2 => 'Cortes',
            3 => 'Suministro',
            4 => 'Preparado',
            5 => 'Ensamble',
            6 => 'Pulido',
            7 => 'Medicion',
            8 => 'Visualizacion',
            9 => 'Empacado'
        ];
    
        // Etiquetas de días del mes
        $labels = range(1, $diasEnMes);
    
        // Inicializar series de datos
        $series = [];
        foreach ($areasMap as $area) {
            $series[$area] = array_fill(0, $diasEnMes, 0); // Inicializar cada día con 0
        }
    
        // Procesar datos y asignarlos a los días correspondientes
        foreach ($datos as $dato) {
            $dia = Carbon::parse($dato->FechaComienzo)->day - 1;
            $areaName = $areasMap[$dato->Areas_id] ?? null;
    
            if ($areaName && isset($series[$areaName])) {
                $series[$areaName][$dia] += $dato->SumaTotalcantidad_partida;
            }
        }
    
        // Convertir series a formato requerido
        $seriesData = [];
        foreach ($series as $nombreArea => $data) {
            $seriesData[] = [
                'name' => $nombreArea,
                'data' => array_values($data) // Asegurar estructura de datos
            ];
        }
    
        // Devolver JSON compatible con `generarGrafico()`
        return response()->json([
            'labels' => $labels,
            'series' => $seriesData
        ]);
    }
    

    public function tablasHoras()
    {
        $SumaCantidadTotalGeneral = DB::table('ordenfabricacion')->sum('CantidadTotal');
    
        // Obtener datos de áreas (para todos los procesos excepto Cortes)
        $DiazAreas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->whereIn('partidasof_areas.Areas_id', [3, 4, 5, 6, 7, 8, 9]) // Excluimos el área de Cortes (ID 2)
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'partidasof_areas.Areas_id',
                'ordenfabricacion.CantidadTotal', 'partidasof_areas.FechaComienzo',
                DB::raw('SUM(partidasof_areas.Cantidad) as SumaTotalcantidad_partida')
            )
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal', 'partidasof_areas.Areas_id', 'partidasof_areas.FechaComienzo')
            ->get();
    
        // Obtener los datos de Cortes (Área ID 2)
        $DiasCortes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal', 'partidasof.FechaComienzo',
                DB::raw('SUM(partidasof.Cantidad_partida) as SumaTotalcantidad_partida'),
                DB::raw($SumaCantidadTotalGeneral . ' as SumaCantidadTotalGeneral')
            )
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal', 'partidasof.FechaComienzo')
            ->get();
    
        // Mapeo de las áreas a los nombres de los procesos
        $areasMap = [
            2 => 'Cortes',
            3 => 'Suministro',
            4 => 'Preparado',
            5 => 'Ensamble',
            6 => 'Pulido',
            7 => 'Medicion',
            8 => 'Visualizacion',
            9 => 'Empacado'
        ];
    
        // Inicializamos los datos para el gráfico con las horas
        $labels = [];
        for ($i = 0; $i < 24; $i++) {
            $labels[] = $i . ':00';
        }
    
        $series = [
            ['name' => 'Cortes', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, 24, 0)],
            ['name' => 'Suministro', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, 24, 0)],
            ['name' => 'Preparado', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, 24, 0)],
            ['name' => 'Ensamble', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, 24, 0)],
            ['name' => 'Pulido', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, 24, 0)],
            ['name' => 'Medicion', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, 24, 0)],
            ['name' => 'Visualizacion', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, 24, 0)],
            ['name' => 'Empacado', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, 24, 0)]
        ];
    
        // Para los cortes (Área ID 2)
        foreach ($DiasCortes as $corte) {
            $hour = Carbon::parse($corte->FechaComienzo)->hour; 
            if ($hour !== null) {
                // Asignar datos para el área 'Cortes'
                $serieIndex = array_search('Cortes', array_column($series, 'name'));
                $series[$serieIndex]['data'][$hour] += $corte->SumaTotalcantidad_partida;
            }
        }
    
        // Para otras áreas (3-9)
        foreach ($DiazAreas as $area) {
            $hour = Carbon::parse($area->FechaComienzo)->hour; 
            $areaName = $areasMap[$area->Areas_id] ?? null;
            
            if ($areaName && $hour !== null) {
                // Encontramos el índice de la serie correspondiente y sumamos la cantidad
                foreach ($series as &$serie) {
                    if ($serie['name'] == $areaName) {
                        $serie['data'][$hour] += $area->SumaTotalcantidad_partida;
                    }
                }
            }
        }
    
        // Devuelve los datos para el gráfico
        return response()->json([
            'labels' => $labels,  // Las horas del día (0:00, 1:00, etc.)
            'series' => $series   // Los datos de las series agrupados por hora
        ]);
    }
    
    
    
}    
/* // Obtener datos de cortes
    $DiasCortes = DB::table('ordenfabricacion')
        ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
        ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
        ->select(
            'ordenfabricacion.OrdenFabricacion',
            'ordenfabricacion.CantidadTotal',
            DB::raw('SUM(partidasof.Cantidad_partida) as SumaTotalcantidad_partida'),
            DB::raw($SumaCantidadTotalGeneral . ' as SumaCantidadTotalGeneral')
        )
        ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
        ->get();*/
    
 /*public function cerradas()
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
                DB::raw("MAX(partidasof.FechaComienzo) AS TiempoCorte"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 3 THEN partidasof_areas.FechaComienzo END) as TiempoSuministro"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 4 THEN partidasof_areas.FechaComienzo END) as TiempoPreparado"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 5 THEN partidasof_areas.FechaComienzo END) as TiempoEnsamble"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 6 THEN partidasof_areas.FechaComienzo END) as TiempoPulido"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 7 THEN partidasof_areas.FechaComienzo END) as TiempoMedicion"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 8 THEN partidasof_areas.FechaComienzo END) as TiempoVisualizacion"),
                DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 9 THEN partidasof_areas.FechaComienzo END) as TiempoAbierto"),
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
            $orden->TiempoCorte = $tiempo ? $tiempo->TiempoCorte : "";
            $orden->TiempoSuministro = $tiempo ? $tiempo->TiempoSuministro : "";
            $orden->TiempoPreparado = $tiempo ? $tiempo->TiempoPreparado : "";
            $orden->TiempoEnsamble = $tiempo ? $tiempo->TiempoEnsamble : "";
            $orden->TiempoPulido = $tiempo ? $tiempo->TiempoPulido : "";
            $orden->TiempoMedicion = $tiempo ? $tiempo->TiempoMedicion : "";
            $orden->TiempoVisualizacion = $tiempo ? $tiempo->TiempoVisualizacion : "";
            $orden->TiempoAbierto = $tiempo ? $tiempo->TiempoAbierto : "";
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
    
        // Calcular la fracción de órdenes cerradas
        $ordenesCerradasCount = $ordenesConTiempos->count();
        $fraccionCerradas = $totalOrdenes > 0 ? "$ordenesCerradasCount/$totalOrdenes" : "0/$totalOrdenes";
    
        // Retornar los datos en formato JSON
        return response()->json([
            'retrabajo' => $fraccionCerradas,
            'ordenes' => $ordenesConTiempos
        ]);
    }
    
    public function abiertas()
    {
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
            ->select('partidasof.cantidad_partida', 'ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.Articulo', 'ordenfabricacion.Descripcion', 'ordenfabricacion.CantidadTotal', 'partidasof.cantidad_partida')
            ->distinct()
            ->get();
    
        $ordenesAbiertasCount = $ordenesAbiertas->count();
    
        // Calculamos el porcentaje de ordenes abiertas
        $porcentajeAbiertas = $totalOrdenes > 0 ? ($ordenesAbiertasCount / $totalOrdenes) * 100 : 0;
    
        return response()->json([
            'retrabajo' => round($porcentajeAbiertas, 2),
            'ordenes' => $ordenesAbiertas,
            'totalOrdenes' => $totalOrdenes,
            'ordenesAbiertasCount' => $ordenesAbiertasCount,
        ]);
    }
*/
    
    

    



        /*
        $totalOrdenes = DB::table('ordenfabricacion')->count();
        
        // Obtener las órdenes cerradas (completadas)
        $ordenesCompletadas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
            ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
            ->where('partidasof_areas.Areas_id', 9)  // Solo las cerradas
            ->count();
    
        // Obtener las órdenes en proceso (en progreso pero no cerradas)
        $ordenesEnProceso = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id')
            ->whereNull('partidasof_areas.Areas_id')  // Áreas que no han llegado a 9
            ->count();
    
        // Obtener las órdenes abiertas
        $ordenesAbiertas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('partidasof_areas')
                    ->whereRaw('partidasof_areas.PartidasOF_id = partidasof.id')
                    ->where('partidasof_areas.Areas_id', 9);  // No se ha cerrado
            })
            ->count();
        
        // Calcular el porcentaje de cada tipo de orden
        $porcentajeCompletadas = ($ordenesCompletadas / $totalOrdenes) * 100;
        $porcentajeEnProceso = ($ordenesEnProceso / $totalOrdenes) * 100;
        $porcentajeAbiertas = 100 - ($porcentajeCompletadas + $porcentajeEnProceso);  // El resto son abiertas
    
        return response()->json([
            'completadas' => $ordenesCompletadas,
            'enProceso' => $ordenesEnProceso,
            'abiertas' => $ordenesAbiertas,
            'porcentajes' => [
                'completadas' => $porcentajeCompletadas,
                'enProceso' => $porcentajeEnProceso,
                'abiertas' => $porcentajeAbiertas
            ],
            'totalOrdenes' => $totalOrdenes,
            'ordenes' => [
                'completadas' => $ordenesCompletadas,
                'enProceso' => $ordenesEnProceso,
                'abiertas' => $ordenesAbiertas
            ]
        ]);
        */
    
    
    

      
    
        

