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
    /*
        $areas = ['3', '4', '5', '6', '7', '8', '9']; // No filtrar por área '2'
        $progreso = [];
    
        // Consulta para el área 2 (Cortes)


        $cortes = DB::table('ordenfabricacion')
        ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
        ->select(
            'ordenfabricacion.OrdenFabricacion',
            DB::raw('ordenfabricacion.CantidadTotal'),
            DB::raw('SUM(partidasof.cantidad_partida) as TotalPartidas'),
            DB::raw('ROUND((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100) as Progreso')
        )
        ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
        ->get();

       
        //dd($cortes);
    

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
        dd($progreso);
        return response()->json([
            'progreso' => $progreso
        ]);*/
        
    }
    public function graficasdia()
    {
        $fechaLimite = now()->subMonth(); // Obtiene la fecha de hace un mes desde hoy
        
    
        $areas = DB::table('partidasof_areas')
        ->join('partidasof', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
        ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
        ->where('ordenfabricacion.FechaEntrega', '>=', $fechaLimite) 
        ->whereIn('partidasof_areas.Areas_id', [3, 4, 5, 6, 7, 8, 9])
        ->select(
            'ordenfabricacion.OrdenFabricacion',
            'ordenfabricacion.CantidadTotal',
            'partidasof_areas.Areas_id',
            DB::raw('GROUP_CONCAT(partidasof_areas.id) as id'),  // Concatenar los IDs
            'ordenfabricacion.FechaEntrega',
            DB::raw('SUM(partidasof_areas.Cantidad) as Cantidad'),  // Sumar las cantidades
            DB::raw('ROUND(LEAST((SUM(partidasof_areas.Cantidad) / ordenfabricacion.CantidadTotal) * 100, 100), 2) as Progreso')  // Calcular progreso
        )
        ->groupBy('ordenfabricacion.OrdenFabricacion', 'partidasof_areas.Areas_id', 'ordenfabricacion.CantidadTotal', 'ordenfabricacion.FechaEntrega')
        ->get();
            $cortes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal', 
                'ordenfabricacion.FechaEntrega',
                DB::raw('SUM(partidasof.cantidad_partida) as SumaTotalcantidad_partida'),
                DB::raw('ROUND(LEAST((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100, 100), 2) as Progreso'),  // Limitar a 100
                DB::raw('ROUND(SUM(partidasof.cantidad_partida) - ordenfabricacion.CantidadTotal, 0) as retrabajo')
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
        
            // Ordenes Completadas (Cerradas)
            $ordenesCompletadas = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                ->where('partidasof_areas.Areas_id', 9) // Solo cerradas
                ->where('ordenfabricacion.FechaEntrega', '>=', $fechaLimite)
                ->select(
                    'ordenfabricacion.CantidadTotal',
                    DB::raw('SUM(partidasof_areas.Cantidad) as Cantidad'),
                    'partidasof_areas.PartidasOF_id',
                    DB::raw('GROUP_CONCAT(partidasof_areas.id) as ids')
                )
                ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.CantidadTotal')
                ->havingRaw('SUM(partidasof_areas.Cantidad) = ordenfabricacion.CantidadTotal')
                ->get();
        
            // Ordenes Abiertas
            $ordenesAbiertas = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->select(
                    'partidasof.OrdenFabricacion_id', 'partidasof.cantidad_partida'
                )
              
                ->get();
        
            // Total de Ordenes
            $totalOrdenes = DB::table('ordenfabricacion')
                ->count();
        
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

        // Obtener la fecha de inicio y fin de la semana actual
        $inicioSemana = Carbon::now()->startOfWeek(); // Lunes de la semana actual
        $finSemana = Carbon::now()->endOfWeek(); // Domingo de la semana actual

        // Formatear el rango de la semana en español
        $rangoSemana = $inicioSemana->format('d') . ' al ' . $finSemana->format('d') . ' de ' . $finSemana->translatedFormat('F');

        // Obtener datos de áreas (para todos los procesos excepto Cortes)
        $DiazAreas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->whereIn('partidasof_areas.Areas_id', [3, 4, 5, 6, 7, 8, 9]) // Excluimos el área de Cortes (ID 2)
            ->whereBetween('partidasof_areas.FechaComienzo', [$inicioSemana, $finSemana])
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'partidasof_areas.Areas_id',
                'ordenfabricacion.CantidadTotal',
                'partidasof_areas.FechaComienzo',
                DB::raw('SUM(partidasof_areas.Cantidad) as SumaTotalcantidad_partida')
            )
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal', 'partidasof_areas.Areas_id', 'partidasof_areas.FechaComienzo')
            ->get();

        // Obtener los datos de Cortes (Área ID 2)
        $DiasCortes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->whereBetween('partidasof.FechaComienzo', [$inicioSemana, $finSemana])
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal',
                'partidasof.FechaComienzo',
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

        // Inicializamos los datos para el gráfico con días de la semana en español
        $labels = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $series = array_map(function ($name) {
            return ['name' => $name, 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => [0, 0, 0, 0, 0, 0, 0]];
        }, array_values($areasMap));

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

        // Procesar datos de Cortes
        foreach ($DiasCortes as $corte) {
            $dayNameInSpanish = $dayMap[Carbon::parse($corte->FechaComienzo)->format('l')] ?? null;
            if ($dayNameInSpanish) {
                $index = array_search($dayNameInSpanish, $labels);
                if ($index !== false) {
                    $serieIndex = array_search('Cortes', array_column($series, 'name'));
                    $series[$serieIndex]['data'][$index] += $corte->SumaTotalcantidad_partida;
                }
            }
        }

        // Procesar datos de otras áreas
        foreach ($DiazAreas as $area) {
            $dayNameInSpanish = $dayMap[Carbon::parse($area->FechaComienzo)->format('l')] ?? null;
            if ($dayNameInSpanish) {
                $index = array_search($dayNameInSpanish, $labels);
                $areaName = $areasMap[$area->Areas_id] ?? null;
                if ($areaName && $index !== false) {
                    foreach ($series as &$serie) {
                        if ($serie['name'] == $areaName) {
                            $serie['data'][$index] += $area->SumaTotalcantidad_partida;
                        }
                    }
                }
            }
        }

        // Devolver los datos incluyendo el rango de la semana
        return response()->json([
            'labels' => $labels,
            'series' => $series,
            'rangoSemana' => 'Semana del ' . $rangoSemana
        ]);
    }

    public function tablasMes()
    {
        $carbon = Carbon::now()->locale('es');
        $mesActual = ucfirst($carbon->monthName); // Nombre del mes con la primera letra en mayúscula
        $anioActual = $carbon->year; // Año actual
        $diasEnMes = $carbon->daysInMonth;

        // Definir el mapeo de áreas
        $areasMap = [
            2 => 'Cortes', 3 => 'Suministro', 4 => 'Preparado', 5 => 'Ensamble',
            6 => 'Pulido', 7 => 'Medicion', 8 => 'Visualizacion', 9 => 'Empacado'
        ];

        // Obtener datos de áreas excepto Cortes
        $MesAreas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->whereIn('partidasof_areas.Areas_id', array_keys($areasMap))
            ->whereMonth('partidasof_areas.FechaComienzo', $carbon->month)
            ->whereYear('partidasof_areas.FechaComienzo', $anioActual)
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal', 'partidasof_areas.Areas_id', 'partidasof_areas.FechaComienzo')
            ->select(
                'ordenfabricacion.OrdenFabricacion', 'partidasof_areas.Areas_id',
                'ordenfabricacion.CantidadTotal', 'partidasof_areas.FechaComienzo',
                DB::raw('SUM(partidasof_areas.Cantidad) as SumaTotalcantidad_partida')
            )
            ->get();

        // Obtener datos para el área de Cortes (Área 2)
        $MesCortes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->whereMonth('partidasof.FechaComienzo', $carbon->month)
            ->whereYear('partidasof.FechaComienzo', $anioActual)
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal', 'partidasof.FechaComienzo')
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                DB::raw('2 as Areas_id'), // ID fijo para Cortes
                'ordenfabricacion.CantidadTotal', 'partidasof.FechaComienzo',
                DB::raw('SUM(partidasof.Cantidad_partida) as SumaTotalcantidad_partida')
            )
            ->get();

        // Unir los datos de ambas consultas
        $datos = $MesAreas->merge($MesCortes);

        // Inicializar las series manualmente
        $series = [
            ['name' => 'Cortes', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, $diasEnMes, 0)],
            ['name' => 'Suministro', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, $diasEnMes, 0)],
            ['name' => 'Preparado', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, $diasEnMes, 0)],
            ['name' => 'Ensamble', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, $diasEnMes, 0)],
            ['name' => 'Pulido', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, $diasEnMes, 0)],
            ['name' => 'Medicion', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, $diasEnMes, 0)],
            ['name' => 'Visualizacion', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, $diasEnMes, 0)],
            ['name' => 'Empacado', 'type' => 'line', 'stack' => 'Total', 'areaStyle' => [], 'data' => array_fill(0, $diasEnMes, 0)]
        ];

        // Procesar los datos
        foreach ($datos as $dato) {
            if (!empty($dato->FechaComienzo)) {
                try {
                    $dia = Carbon::parse($dato->FechaComienzo)->day - 1; // Índice del día
                    foreach ($series as &$serie) {
                        if ($serie['name'] === ($areasMap[$dato->Areas_id] ?? '')) {
                            $serie['data'][$dia] += $dato->SumaTotalcantidad_partida;
                        }
                    }
                } catch (\Exception $e) {
                    continue; // Saltar valores inválidos
                }
            }
        }

        return response()->json([
            'labels' => range(1, $diasEnMes), // Generar los días del mes como etiquetas
            'series' => $series, // Devolver series indexadas
            'mes' => "Mes $mesActual $anioActual" // Formato corregido
        ]);
    }

    public function tablasHoras()
    {
        // Obtener la fecha y hora actuales y la fecha y hora de hace 24 horas
        $hace24Horas = Carbon::now()->subDay();
    
        // Suma de la cantidad total general solo para los registros de las últimas 24 horas
        $SumaCantidadTotalGeneral = DB::table('ordenfabricacion')
            ->where('created_at', '>=', $hace24Horas)  // Filtrar por fecha
            ->sum('CantidadTotal');
    
        // Obtener datos de áreas (para todos los procesos excepto Cortes) de las últimas 24 horas
        $DiasAreas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->whereIn('partidasof_areas.Areas_id', [3, 4, 5, 6, 7, 8, 9]) // Excluimos el área de Cortes (ID 2)
            ->where('partidasof_areas.FechaComienzo', '>=', $hace24Horas)  // Filtrar por fecha
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'partidasof_areas.Areas_id',
                'ordenfabricacion.CantidadTotal', 
                'partidasof_areas.FechaComienzo',
                DB::raw('SUM(partidasof_areas.Cantidad) as SumaTotalcantidad_partida')
            )
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal', 'partidasof_areas.Areas_id', 'partidasof_areas.FechaComienzo')
            ->get();
    
        // Obtener los datos de Cortes (Área ID 2) de las últimas 24 horas
        $DiasCortes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->where('partidasof.FechaComienzo', '>=', $hace24Horas)  // Filtrar por fecha
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal', 
                'partidasof.FechaComienzo',
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
        foreach ($DiasAreas as $area) {
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
       //dd($series);
        //dd($labels);
    
        // Devuelve los datos para el gráfico
        return response()->json([
            'labels' => $labels,  // Las horas del día (0:00, 1:00, etc.)
            'series' => $series,  // Los datos de las series agrupados por hora
            'fecha' => Carbon::now()->translatedFormat('d \d\e F \d\e\l Y') // Formato en español
        ]);
        
    } 

    public function wizarp()
    {
        $fechaLimite = now()->subMonth();
        
        // Ordenes Completadas (cerradas)
        $ordenesCompletadas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->where('partidasof_areas.Areas_id', 9) // Solo cerradas
            ->where('ordenfabricacion.FechaEntrega', '>=', $fechaLimite)
            ->select(
                'ordenfabricacion.CantidadTotal',
                DB::raw('SUM(partidasof_areas.Cantidad) as Cantidad'),
                'partidasof_areas.PartidasOF_id',
                DB::raw('GROUP_CONCAT(partidasof_areas.id) as ids')
            )
            ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.CantidadTotal')
            ->havingRaw('SUM(partidasof_areas.Cantidad) = ordenfabricacion.CantidadTotal')
            ->get();
        
        // Ordenes Abiertas
        $ordenesAbiertas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select('partidasof.OrdenFabricacion_id', 'partidasof.cantidad_partida')
            ->get();
        
        // Total de Ordenes
        $totalOrdenes = DB::table('ordenfabricacion')->count();
        
        // Calcular las órdenes abiertas (restar las cerradas del total)
        $ordenesAbiertasCount = $totalOrdenes - $ordenesCompletadas->count();

        return response()->json([
            'ordenesCompletadas' => $ordenesCompletadas->count(),
            'ordenesAbiertas' => $ordenesAbiertasCount,
            'totalOrdenes' => $totalOrdenes
        ]);
    }

}    
