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
use Illuminate\Support\Facades\Response;
use Carbon\Carbon as CarbonClass;



class BusquedaController extends Controller
{
    //vista
    public function index(Request $request)
    {
        $user = Auth::user();
        if($user->hasPermission('Vista Busquedas')){
        $partidaId = 1;
        $partidasAreas = DB::table('partidasof_areas')
        ->where('PartidasOF_id', $partidaId)  
        ->get();
        return view('layouts.busquedas', compact('partidasAreas'));
        }else
        return redirect()->route('error.');
    
    }
    // Controlador para las órdenes de Venta
    public function obtenerOrdenesVenta(Request $request)
    {
        $search = $request->input('search');
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

    //boton detalles de la orden de venta        
    public function detallesventa(Request $request)
    {
        $idVenta = $request->input('id');
        $total = DB::table('ordenventa')
            ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
            ->where('ordenventa.OrdenVenta', $idVenta)
            ->select(
                'ordenventa.OrdenVenta',
                DB::raw('MAX(ordenfabricacion.OrdenFabricacion) as OrdenFabricacion'),
                DB::raw('SUM(ordenfabricacion.CantidadTotal) as CantidadTotal')
            )
            ->groupBy('ordenventa.OrdenVenta')
            ->first();

        $partidasAreas = DB::table('ordenventa')
            ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->join('areas', 'partidasof_areas.Areas_id', '=', 'areas.id')
            ->where('ordenventa.OrdenVenta', $idVenta)
            ->whereIn('partidasof_areas.Areas_id', [9])
            ->select('partidasof_areas.NumeroEtiqueta', 'partidasof_areas.Cantidad', 'partidasof_areas.PartidasOF_id', 'ordenventa.OrdenVenta', 'ordenfabricacion.OrdenFabricacion')
            ->get();

        $cantidadPartidas = $partidasAreas->sum('Cantidad');
        $cantidadTotal = $total->CantidadTotal ?? 1; // Evita división por cero
        $porcentaje = ($cantidadPartidas / $cantidadTotal) * 100;

        return response()->json([
            "OrdenVenta" => $total->OrdenVenta,
            "OrdenFabricacion" => $total->OrdenFabricacion,
            "CantidadTotal" => $cantidadTotal,
            "Cantidad" => $cantidadPartidas,
            "Porcentaje" => round($porcentaje, 2), // Sin símbolo "%"
            "partidasAreas" => $partidasAreas
        ]);

    }

    
    //progreso de stage
    public function GraficarOROF(Request $request)
    {
        $idVenta = $request->input('id');  // Obtenemos el ID de la orden de venta
        $stage = $request->input('stage'); // Obtenemos la etapa

        // Consulta base sin duplicados
       $query = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
            ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->select(
                'ordenventa.OrdenVenta',
                'ordenfabricacion.CantidadTotal',
                'ordenfabricacion.OrdenFabricacion',
                'partidasof.id as PartidaOF_ID',
                DB::raw('GROUP_CONCAT(DISTINCT ordenfabricacion.OrdenFabricacion ORDER BY ordenfabricacion.OrdenFabricacion ASC SEPARATOR ", ") as OrdenesFabricacion'),
                DB::raw('SUM(partidasof_areas.cantidad) as SumaTotalPartidas'),
                DB::raw('ROUND((SUM(partidasof_areas.cantidad) / NULLIF(ordenfabricacion.CantidadTotal, 0)) * 100, 2) as Progreso')
            )
            ->groupBy('partidasof.id', 'ordenventa.OrdenVenta', 'ordenfabricacion.CantidadTotal', 'ordenfabricacion.OrdenFabricacion');
           // dd($query);

        // Consulta para los cortes
        $cortes = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
        ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
        ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
        ->select(
            'ordenventa.OrdenVenta',
            'ordenfabricacion.CantidadTotal',
            'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal',
            DB::raw('ordenfabricacion.OrdenFabricacion as OrdenesFabricacion'),
            DB::raw('GROUP_CONCAT(DISTINCT partidasof.id ORDER BY partidasof.id ASC SEPARATOR ", ") as PartidaOF_ID'),
            DB::raw('SUM(partidasof.cantidad_partida) as SumaTotalPartidas'),
            DB::raw('ROUND(((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100), 2) as Progreso')
        )
        ->groupBy('ordenventa.OrdenVenta', 'ordenfabricacion.CantidadTotal', 'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal');
        //->get();
    
            //dd($cortes);

        // Aplicación del filtro según la etapa
        if ($stage == 'stage3') {
            $query->where('partidasof_areas.Areas_id', 3);
        } elseif ($stage == 'stage4') {
            $query->where('partidasof_areas.Areas_id', 4);
        } elseif ($stage == 'stage5') {
            $query->where('partidasof_areas.Areas_id', 5);
        } elseif ($stage == 'stage6') {
            $query->where('partidasof_areas.Areas_id', 6);
        } elseif ($stage == 'stage7') {
            $query->where('partidasof_areas.Areas_id', 7);
        } elseif ($stage == 'stage8') {
            $query->where('partidasof_areas.Areas_id', 8);
        } elseif ($stage == 'stage9') {
            $query->where('partidasof_areas.Areas_id', 9);
        }

        // Obtener resultados
        if ($stage == 'stage2') {
            $OR = $cortes->get();
        } else {
            $OR = $query->get();
        }

        return response()->json($OR);
    }

    //graficadores
    public function Graficador(Request $request)
    {
        $idVenta = $request->input('id');
        $tipo = $request->input('tipo');
        $areaIds = [
            'cortes' => 0,
            'suministros' => 3,
            'preparado' => 4,
            'ensamble' => 5,
            'pulido' => 6,
            'medicion' => 7,
            'visualizacion' => 8,
            'empaque' => 9,
        ];
        if (!isset($areaIds[$tipo])) {
            return response()->json(['error' => 'Tipo inválido'], 400);
        }
        $ordenesfabricacion = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
            ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
            ->select(
                DB::raw("GROUP_CONCAT(ordenfabricacion.OrdenFabricacion ORDER BY ordenfabricacion.OrdenFabricacion ASC) as OrdenFabricacion"),
                DB::raw("SUM(CantidadTotal) as SumaTotalCantidadTotal")
            )
            ->groupBy('ordenventa.id')
            ->get();
        $cortesorden = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
            ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'ordenventa.OrdenVenta',
                DB::raw("GROUP_CONCAT(ordenfabricacion.OrdenFabricacion ORDER BY ordenfabricacion.OrdenFabricacion ASC) as OrdenFabricacion"),
                DB::raw("SUM(partidasof.cantidad_partida) as SumaCantidadPartidaTotal"),
                DB::raw("SUM(CantidadTotal) as SumaTotalCantidadTotal")
            )
            ->groupBy('ordenventa.id', 'ordenventa.OrdenVenta')
            ->get();
        $result = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
            ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->where('partidasof_areas.Areas_id', $areaIds[$tipo])
            ->select(
                'ordenventa.OrdenVenta',
                'ordenfabricacion.OrdenFabricacion',
                DB::raw('SUM(partidasof_areas.Cantidad) as SumaTotalPartidas'),
                'ordenfabricacion.CantidadTotal',
                DB::raw('GROUP_CONCAT(partidasof_areas.id) as id')
            )
            ->groupBy('ordenventa.OrdenVenta', 'ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
            ->get();
        $data = ($tipo === 'cortes') ? $cortesorden : $result;
        if ($ordenesfabricacion->isNotEmpty() && $data->isNotEmpty()) {
            $totalCantidad = (int) $ordenesfabricacion->sum('SumaTotalCantidadTotal') ?? 0;
            $totalPartidas = ($tipo === 'cortes') 
                ? (int) $cortesorden->sum('SumaCantidadPartidaTotal') ?? 0
                : (int) $result->sum('SumaTotalPartidas') ?? 0;
            $Progreso = ($totalCantidad > 0) ? ($totalPartidas / $totalCantidad) * 100 : 0;
            $progresoConOrdenVenta = [
                'OrdenVenta' => $idVenta,
                'Progreso' => round($Progreso, 2),
            ];
            $response = [
                'result' => $data,
                'ordenesfabricacion' => $ordenesfabricacion,
                'Progreso' => $progresoConOrdenVenta,
            ];
        } else {
            $response = [
                'result' => [],
                'ordenesfabricacion' => [],
                'Progreso' => ['OrdenVenta' => null, 'Progreso' => 0],
            ];
        }
        return response()->json($response);
    }
    // Controlador para las órdenes de fabricación
    public function obtenerOrdenesFabricacion(Request $request)
    {
        $search = $request->input('search');
        $ordenesFabricacion = DB::table('ordenfabricacion')
        ->select(
            'ordenfabricacion.OrdenFabricacion', 
            'ordenfabricacion.Articulo', 
            'ordenfabricacion.Descripcion',
            'ordenfabricacion.CantidadTotal'
        )
        ->when($search, function ($query, $search) {
            return $query->where('ordenfabricacion.OrdenFabricacion', 'like', "%$search%")
                        ->orWhere('ordenfabricacion.Articulo', 'like', "%$search%")
                        ->orWhere('ordenfabricacion.Descripcion', 'like', "%$search%")
                        ->orWhere('ordenfabricacion.CantidadTotal', 'like', "%$search%");
        })
        ->distinct()
        ->get();
       

        return response()->json($ordenesFabricacion);
    }

    //detalles OF
    public function DetallesOF(Request $request)
    {
            $idFabricacion = $request->input('id');
            
            // Obtener las partidas y sus estados
            $ordenfabricacion = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                ->join('areas', 'partidasOF_areas.Areas_id', '=', 'areas.id') 
                ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion) 
                ->whereIn('partidasof_areas.Areas_id', [9])
                ->select('partidasof_areas.PartidasOF_id', 'areas.nombre as Estado', 'ordenfabricacion.OrdenFabricacion') 
                ->get();
            
            // Obtener el progreso de fabricación
            $progreso = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                ->join('areas', 'partidasof_areas.Areas_id', '=', 'areas.id') 
                ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion) 
                ->whereIn('partidasof_areas.Areas_id', [9])
                ->select(
                    'partidasof_areas.PartidasOF_id',
                    'partidasof_areas.id',
                    'partidasof_areas.Areas_id',
                    'partidasof_areas.Cantidad',
                    'ordenfabricacion.OrdenFabricacion',
                    'ordenfabricacion.CantidadTotal',
                    DB::raw('SUM(partidasof_areas.cantidad) as cantidad_total'),
                    DB::raw('COALESCE(ordenfabricacion.CantidadTotal, 1) as CantidadTotal')
                )
                ->groupBy(
                    'ordenfabricacion.OrdenFabricacion',
                    'ordenfabricacion.CantidadTotal',
                    'partidasof_areas.PartidasOF_id',
                    'partidasof_areas.Areas_id', 
                    'partidasof_areas.id',
                    'partidasof_areas.Cantidad'
                )
                ->get();
            
            // Sumar las cantidades de todas las partidas
            $cantidad_total_suma = $progreso->sum('cantidad_total');
            
            // Calcular el progreso con la suma de las cantidades
            $progreso_total = $progreso->isEmpty() ? 0 : round(($cantidad_total_suma / $progreso->first()->CantidadTotal) * 100);
        
            // Si no se obtiene ningún progreso, asigna 0
            $progresoValor = $progreso->isEmpty() ? 0 : $progreso_total;
        
            // Si no se encontraron partidas, devuelve una respuesta vacía o null
            $partidasAreas = $ordenfabricacion->isEmpty() ? null : $ordenfabricacion;
            
            // Asegúrate de que la respuesta siempre incluya una propiedad, incluso si no hay datos
            return response()->json([
                'partidasAreas' => $partidasAreas,
                'progreso' => $progresoValor
            ]);
    }


    
    //graficador OF
    public function GraficadorFabricacion(Request $request)
    {
        $idFabricacion = $request->input('id');
        $tipoOF = $request->input('tipo'); 
        $OrdenFabricacion=OrdenFabricacion::where('OrdenFabricacion', $idFabricacion)->first();
        $TotalCompletadosCortes=0;
        $TotalOrdenFabricacion=0;
        if(!($OrdenFabricacion=="" OR $OrdenFabricacion==null)){
            $TotalCompletadosCortes=$OrdenFabricacion->PartidasOF()->where('TipoPartida','N')->whereNotNull('FechaFinalizacion')->get()->SUM('cantidad_partida')-
                                $OrdenFabricacion->PartidasOF()->where('TipoPartida','N')->whereNull('FechaFinalizacion')->get()->SUM('cantidad_partida');
        }
        /*$OrdenFabricacion=OrdenFabricacion::where('OrdenFabricacion', $idFabricacion)
                                                ->join('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                                                ->join('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                                                ->where('OrdenFabricacion.OrdenFabricacion', $idFabricacion)
                                                ->get(); */

        if (!empty($idFabricacion)) {
            // Definir las consultas para cada tipo (como ya lo tienes en tu código)
            if ($tipoOF === 'plemasCorte') {
                $resultOF = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                ->select(
                    'ordenfabricacion.OrdenFabricacion',
                    DB::raw('MIN(partidasof.FechaComienzo) as FechaComienzo'), // Obtener la fecha más temprana
                    DB::raw('MAX(partidasof.FechaFinalizacion) as FechaFinalizacion'), // Obtener la fecha más tardía
                    DB::raw('ordenfabricacion.CantidadTotal'),
                    DB::raw('SUM(partidasof.cantidad_partida) as TotalPartidas'),
                    DB::raw('ROUND((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100) as Progreso')
                )
                ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
                ->first(); // Obtener un solo registro en lugar de una colección
            
            // Procesar los resultados si hay datos
            if ($resultOF) {
                $fechaComienzo = Carbon::parse($resultOF->FechaComienzo);
                $fechaFinalizacion = Carbon::parse($resultOF->FechaFinalizacion);
            
                // Calcular la diferencia en días, horas y minutos
                $diferencia = $fechaComienzo->diff($fechaFinalizacion);
                $dias = $diferencia->days;
                $horas = $diferencia->h;
                $minutos = $diferencia->i;
            
                // Agregar el tiempo transcurrido al resultado
                $resultOF->TiempoTranscurrido = "{$dias} días, {$horas} horas, {$minutos} minutos";
            }
            
                
            } elseif ($tipoOF === 'plemasSuministro') {
                // Similar a lo anterior
                $resultOF = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                    ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', 3)
                    ->select(
                        'ordenfabricacion.OrdenFabricacion',
                        DB::raw('MIN(partidasof_areas.FechaComienzo) as FechaComienzo'), // Fecha más temprana
                        DB::raw('MAX(partidasof_areas.FechaTermina) as FechaTermina'),   // Fecha más tardía
                        DB::raw('GROUP_CONCAT(partidasof_areas.id ORDER BY partidasof_areas.id ASC) as id'), // Concatenar los IDs
                        'ordenfabricacion.CantidadTotal',
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) / ordenfabricacion.CantidadTotal) * 100) as Progreso')
                    )
                    ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
                    ->first();

                // Si hay un resultado, calcular el tiempo transcurrido
                if ($resultOF) {
                    $fechaComienzo = Carbon::parse($resultOF->FechaComienzo);
                    $fechaTermina = Carbon::parse($resultOF->FechaTermina);

                    // Calcular la diferencia en días, horas y minutos
                    $diferencia = $fechaComienzo->diff($fechaTermina);
                    $dias = $diferencia->days;
                    $horas = $diferencia->h;
                    $minutos = $diferencia->i;

                    // Crear la cadena de tiempo transcurrido
                    $resultOF->TiempoTranscurrido = "{$dias} días, {$horas} horas, {$minutos} minutos";
                }
    
    
            } elseif ($tipoOF === 'plemasPreparado') {
                // Procesar la estación de preparación
                $resultOF = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', 4)
                    ->select(
                        'ordenfabricacion.OrdenFabricacion',
                        DB::raw('MIN(partidasof_areas.FechaComienzo) as FechaComienzo'), // Fecha más temprana
                        DB::raw('MAX(partidasof_areas.FechaTermina) as FechaTermina'),   // Fecha más tardía
                        DB::raw('GROUP_CONCAT(partidasof_areas.id ORDER BY partidasof_areas.id ASC) as id'), // Concatenar los IDs
                        'ordenfabricacion.CantidadTotal',
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) / ordenfabricacion.CantidadTotal) * 100) as Progreso')
                    )
                    ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
                    ->first();

                // Si hay un resultado, calcular el tiempo transcurrido
                if ($resultOF) {
                    $fechaComienzo = Carbon::parse($resultOF->FechaComienzo);
                    $fechaTermina = Carbon::parse($resultOF->FechaTermina);

                    // Calcular la diferencia en días, horas y minutos
                    $diferencia = $fechaComienzo->diff($fechaTermina);
                    $dias = $diferencia->days;
                    $horas = $diferencia->h;
                    $minutos = $diferencia->i;

                    // Crear la cadena de tiempo transcurrido
                    $resultOF->TiempoTranscurrido = "{$dias} días, {$horas} horas, {$minutos} minutos";
                }
    
            }elseif ($tipoOF === 'plemasEnsamble') {
                // Procesar la estación de preparación
                $resultOF = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                    ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', 5)
                    ->select(
                        'ordenfabricacion.OrdenFabricacion',
                        DB::raw('MIN(partidasof_areas.FechaComienzo) as FechaComienzo'), // Fecha más temprana
                        DB::raw('MAX(partidasof_areas.FechaTermina) as FechaTermina'),   // Fecha más tardía
                        DB::raw('GROUP_CONCAT(partidasof_areas.id ORDER BY partidasof_areas.id ASC) as id'), // Concatenar los IDs
                        'ordenfabricacion.CantidadTotal',
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) / ordenfabricacion.CantidadTotal) * 100) as Progreso')
                    )
                    ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
                    ->first();

                // Si hay un resultado, calcular el tiempo transcurrido
                if ($resultOF) {
                    $fechaComienzo = Carbon::parse($resultOF->FechaComienzo);
                    $fechaTermina = Carbon::parse($resultOF->FechaTermina);

                    // Calcular la diferencia en días, horas y minutos
                    $diferencia = $fechaComienzo->diff($fechaTermina);
                    $dias = $diferencia->days;
                    $horas = $diferencia->h;
                    $minutos = $diferencia->i;

                    // Crear la cadena de tiempo transcurrido
                    $resultOF->TiempoTranscurrido = "{$dias} días, {$horas} horas, {$minutos} minutos";
                }
    
            }elseif ($tipoOF === 'plemasPulido') {
                // Procesar la estación de preparación
                $resultOF = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                    ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', 6)
                    ->select(
                        'ordenfabricacion.OrdenFabricacion',
                        DB::raw('MIN(partidasof_areas.FechaComienzo) as FechaComienzo'), // Fecha más temprana
                        DB::raw('MAX(partidasof_areas.FechaTermina) as FechaTermina'),   // Fecha más tardía
                        DB::raw('GROUP_CONCAT(partidasof_areas.id ORDER BY partidasof_areas.id ASC) as id'), // Concatenar los IDs
                        'ordenfabricacion.CantidadTotal',
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) / ordenfabricacion.CantidadTotal) * 100) as Progreso')
                    )
                    ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
                    ->first();

                // Si hay un resultado, calcular el tiempo transcurrido
                if ($resultOF) {
                    $fechaComienzo = Carbon::parse($resultOF->FechaComienzo);
                    $fechaTermina = Carbon::parse($resultOF->FechaTermina);

                    // Calcular la diferencia en días, horas y minutos
                    $diferencia = $fechaComienzo->diff($fechaTermina);
                    $dias = $diferencia->days;
                    $horas = $diferencia->h;
                    $minutos = $diferencia->i;

                    // Crear la cadena de tiempo transcurrido
                    $resultOF->TiempoTranscurrido = "{$dias} días, {$horas} horas, {$minutos} minutos";
                }
    
            }elseif ($tipoOF === 'plemasMedicion') {
                // Procesar la estación de preparación
                $resultOF = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                    ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', 7)
                    ->select(
                        'ordenfabricacion.OrdenFabricacion',
                        DB::raw('MIN(partidasof_areas.FechaComienzo) as FechaComienzo'), // Fecha más temprana
                        DB::raw('MAX(partidasof_areas.FechaTermina) as FechaTermina'),   // Fecha más tardía
                        DB::raw('GROUP_CONCAT(partidasof_areas.id ORDER BY partidasof_areas.id ASC) as id'), // Concatenar los IDs
                        'ordenfabricacion.CantidadTotal',
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) / ordenfabricacion.CantidadTotal) * 100) as Progreso')
                    )
                    ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
                    ->first();

                // Si hay un resultado, calcular el tiempo transcurrido
                if ($resultOF) {
                    $fechaComienzo = Carbon::parse($resultOF->FechaComienzo);
                    $fechaTermina = Carbon::parse($resultOF->FechaTermina);

                    // Calcular la diferencia en días, horas y minutos
                    $diferencia = $fechaComienzo->diff($fechaTermina);
                    $dias = $diferencia->days;
                    $horas = $diferencia->h;
                    $minutos = $diferencia->i;

                    // Crear la cadena de tiempo transcurrido
                    $resultOF->TiempoTranscurrido = "{$dias} días, {$horas} horas, {$minutos} minutos";
                }
    
            }elseif ($tipoOF === 'plemasVisualizacion') {
                // Procesar la estación de preparación
                $resultOF = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                    ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', 8)
                    ->select(
                        'ordenfabricacion.OrdenFabricacion',
                        DB::raw('MIN(partidasof_areas.FechaComienzo) as FechaComienzo'), // Fecha más temprana
                        DB::raw('MAX(partidasof_areas.FechaTermina) as FechaTermina'),   // Fecha más tardía
                        DB::raw('GROUP_CONCAT(partidasof_areas.id ORDER BY partidasof_areas.id ASC) as id'), // Concatenar los IDs
                        'ordenfabricacion.CantidadTotal',
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) / ordenfabricacion.CantidadTotal) * 100) as Progreso')
                    )
                    ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
                    ->first();

                // Si hay un resultado, calcular el tiempo transcurrido
                if ($resultOF) {
                    $fechaComienzo = Carbon::parse($resultOF->FechaComienzo);
                    $fechaTermina = Carbon::parse($resultOF->FechaTermina);

                    // Calcular la diferencia en días, horas y minutos
                    $diferencia = $fechaComienzo->diff($fechaTermina);
                    $dias = $diferencia->days;
                    $horas = $diferencia->h;
                    $minutos = $diferencia->i;

                    // Crear la cadena de tiempo transcurrido
                    $resultOF->TiempoTranscurrido = "{$dias} días, {$horas} horas, {$minutos} minutos";
                }
    
            }elseif ($tipoOF === 'plemasEmpaque') {
                // Procesar la estación de preparación
                $resultOF = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                    ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', 9)
                    ->select(
                        'ordenfabricacion.OrdenFabricacion',
                        DB::raw('MIN(partidasof_areas.FechaComienzo) as FechaComienzo'), // Fecha más temprana
                        DB::raw('MAX(partidasof_areas.FechaTermina) as FechaTermina'),   // Fecha más tardía
                        DB::raw('GROUP_CONCAT(partidasof_areas.id ORDER BY partidasof_areas.id ASC) as id'), // Concatenar los IDs
                        'ordenfabricacion.CantidadTotal',
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) / ordenfabricacion.CantidadTotal) * 100) as Progreso')
                    )
                    ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
                    ->first();

                // Si hay un resultado, calcular el tiempo transcurrido
                if ($resultOF) {
                    $fechaComienzo = Carbon::parse($resultOF->FechaComienzo);
                    $fechaTermina = Carbon::parse($resultOF->FechaTermina);

                    // Calcular la diferencia en días, horas y minutos
                    $diferencia = $fechaComienzo->diff($fechaTermina);
                    $dias = $diferencia->days;
                    $horas = $diferencia->h;
                    $minutos = $diferencia->i;

                    // Crear la cadena de tiempo transcurrido
                    $resultOF->TiempoTranscurrido = "{$dias} días, {$horas} horas, {$minutos} minutos";
                }
    
            }
    
            // Aquí continúa con el resto de estaciones, usando la misma lógica.
    
            return response()->json($resultOF);
        } else {
            return response()->json([], 204); 
        }
    }
    //tiempos de orden de fabricacion
    public function tiempoS(Request $request)
    {
        $idFabricacion = $request->input('id');
      //dd($idFabricacion);
        
     // Tiempo de cortes
        $tiemposcortes = DB::table('ordenfabricacion')
        ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
        ->select(
            'ordenfabricacion.OrdenFabricacion',
            'partidasof.FechaComienzo',
            'partidasof.FechaFinalizacion', 
            DB::raw('GROUP_CONCAT(partidasof.OrdenFabricacion_id) as ids'),
            DB::raw('MIN(partidasof.FechaComienzo) as FechaComienzo'),
            DB::raw('MAX(partidasof.FechaFinalizacion) as FechaTermina'),
            DB::raw('SUM(TIMESTAMPDIFF(MINUTE, partidasof.FechaComienzo, partidasof.FechaFinalizacion)) as TotalMinutos')
        )
        ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
        ->groupBy('ordenfabricacion.OrdenFabricacion', 'partidasof.FechaComienzo', 'partidasof.FechaFinalizacion')
        ->get()
        ->map(function ($item) {
            if ($item->TotalMinutos == 0) {
                $item->Duracion = "0 días, 0 horas, 0 minutos";
            } else {
                $FechaComienzo = Carbon::parse($item->FechaComienzo);
                $FechaFinalizacion = Carbon::parse($item->FechaFinalizacion);
                
                $diffDias = floor($FechaComienzo->diffInHours($FechaFinalizacion) / 24);
                $diffHoras = $FechaComienzo->diffInHours($FechaFinalizacion) % 24;
                $diffMinutos = $FechaComienzo->diffInMinutes($FechaFinalizacion) % 60;

                $item->Duracion = "{$diffDias} días, {$diffHoras} horas, {$diffMinutos} minutos";
            }
            return $item;
        });

        // Tiempo por áreas
        $tiemposareas = DB::table('ordenfabricacion')
        ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
        ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
        ->select(
            'ordenfabricacion.OrdenFabricacion',
            'partidasof_areas.PartidasOf_id',
            'partidasof_areas.Areas_id',
            DB::raw('GROUP_CONCAT(partidasof_areas.id) as ids'),
            DB::raw('MIN(partidasof_areas.FechaComienzo) as FechaComienzo'),
            DB::raw('MAX(partidasof_areas.FechaTermina) as FechaTermina'),
            DB::raw('SUM(TIMESTAMPDIFF(MINUTE, partidasof_areas.FechaComienzo, partidasof_areas.FechaTermina)) as TotalMinutos')
        )
        ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
        ->groupBy('ordenfabricacion.OrdenFabricacion', 'partidasof_areas.PartidasOf_id', 'partidasof_areas.Areas_id')
        ->get()
        ->map(function ($item) {
            if ($item->TotalMinutos == 0) {
                $item->DuracionTotal = "0 días, 0 horas, 0 minutos";
            } else {
                $totalMinutos = $item->TotalMinutos;
                $dias = floor($totalMinutos / (60 * 24));
                $horas = floor(($totalMinutos % (60 * 24)) / 60);
                $minutos = $totalMinutos % 60;

                $duracion = [];
                if ($dias > 0) $duracion[] = "{$dias} días";
                if ($horas > 0) $duracion[] = "{$horas} horas";
                if ($minutos > 0) $duracion[] = "{$minutos} minutos";
                $item->DuracionTotal = implode(", ", $duracion);
            }
            $item->ids = explode(',', $item->ids);
            return $item;
        });

        return response()->json([
        'tiemposcortes' => $tiemposcortes,
        'tiemposareas' => $tiemposareas
        ]);

    }
    
}
















/* public function tiemposOrden(Request $request)
{
    $ordenfabricacion = $request->input('ordenfabricacion');

    $tiempos = DB::table('ordenfabricacion')
        ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
        ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
        ->where('ordenfabricacion.OrdenFabricacion', $ordenfabricacion)  // Filtrar por ordenfabricacion
        ->select(
            'ordenfabricacion.OrdenFabricacion',
            DB::raw("MAX(partidasof.FechaComienzo) AS TiempoCorte"),
            DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 3 THEN partidasof_areas.FechaComienzo END) as TiempoSuministro"),
            DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 4 THEN partidasof_areas.FechaComienzo END) as TiempoPreparado"),
            DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 5 THEN partidasof_areas.FechaComienzo END) as TiempoEnsamble"),
            DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 6 THEN partidasof_areas.FechaComienzo END) as TiempoPulido"),
            DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 7 THEN partidasof_areas.FechaComienzo END) as TiempoMedicion"),
            DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 8 THEN partidasof_areas.FechaComienzo END) as TiempoVisualizacion"),
            DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 9 THEN partidasof_areas.FechaComienzo END) as TiempoEmpaque"),
            DB::raw("MAX(partidasof.FechafINALIZACION) AS FinCorte"),
            DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 3 THEN partidasof_areas.FechaTermina END) as FinSuministro"),
            DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 4 THEN partidasof_areas.FechaTermina END) as FinPreparado"),
            DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 5 THEN partidasof_areas.FechaTermina END) as FinEnsamble"),
            DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 6 THEN partidasof_areas.FechaTermina END) as FinPulido"),
            DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 7 THEN partidasof_areas.FechaTermina END) as FinMedicion"),
            DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 8 THEN partidasof_areas.FechaTermina END) as FinVisualizacion"),
            DB::raw("MAX(CASE WHEN partidasof_areas.Areas_id = 9 THEN partidasof_areas.FechaTermina END) as FinEmpaque")
        )
        ->groupBy('ordenfabricacion.OrdenFabricacion')
        ->get();

    // Crear un array separado por cada fase (Corte, Suministro, etc.)
    $resultados = [];

    // Si hay datos en $tiempos, separarlos por fase
    foreach ($tiempos as $tiempo) {
        // Corte
        $resultados[] = [
            'fase' => 'Corte',
            'Tiempoinicio' => $tiempo->TiempoCorte,
            'Tiempofin' => $tiempo->FinCorte,
        ];

        // Suministro
        $resultados[] = [
            'fase' => 'Suministro',
            'Tiempoinicio' => $tiempo->TiempoSuministro,
            'Tiempofin' => $tiempo->FinSuministro,
        ];

        // Preparado
        $resultados[] = [
            'fase' => 'Preparado',
            'Tiempoinicio' => $tiempo->TiempoPreparado,
            'Tiempofin' => $tiempo->FinPreparado,
        ];

        // Ensamble
        $resultados[] = [
            'fase' => 'Ensamble',
            'Tiempoinicio' => $tiempo->TiempoEnsamble,
            'Tiempofin' => $tiempo->FinEnsamble,
        ];

        // Pulido
        $resultados[] = [
            'fase' => 'Pulido',
            'Tiempoinicio' => $tiempo->TiempoPulido,
            'Tiempofin' => $tiempo->FinPulido,
        ];

        // Medición
        $resultados[] = [
            'fase' => 'Medición',
            'Tiempoinicio' => $tiempo->TiempoMedicion,
            'Tiempofin' => $tiempo->FinMedicion,
        ];

        // Visualización
        $resultados[] = [
            'fase' => 'Visualización',
            'Tiempoinicio' => $tiempo->TiempoVisualizacion,
            'Tiempofin' => $tiempo->FinVisualizacion,
        ];

        // Empaque
        $resultados[] = [
            'fase' => 'Empaque',
            'Tiempoinicio' => $tiempo->TiempoEmpaque,
            'Tiempofin' => $tiempo->FinEmpaque,
        ];
    }

    return Response::json($resultados);
}


*/
////
    
/*
    public function GraficadorFabricacion(Request $request)
    {
        $idFabricacion = $request->input('id');
        
        $tipoOF = $request->input('tipo'); 
        //para cargar los datos 
        if (!empty($idFabricacion)) {
            //estacion eccorte
            if ($tipoOF === 'plemasCorte') {
                $resultOF = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                ->select(
                    'ordenfabricacion.OrdenFabricacion', 'partidasof.FechaComienzo', 'partidasof.FechaFinalizacion',
                    DB::raw('ordenfabricacion.CantidadTotal'),
                    DB::raw('SUM(partidasof.cantidad_partida) as TotalPartidas'),
                    DB::raw('ROUND((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100) as Progreso')
                )
                ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal', 'partidasof.FechaComienzo','partidasof.FechaFinalizacion',)
                ->get();
                foreach ($resultOF as &$item) {
                    $item->Duracion = "{$item->DiasTranscurridos} días {$item->HorasTranscurridas} horas {$item->MinutosTranscurridos} minutos";
                }
                dd($resultOF);

             
                
                    
            //estacion suministros
            } elseif ($tipoOF === 'plemasSuministro') {
                $resultOF = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', 3)
                    ->select(
                        'ordenfabricacion.OrdenFabricacion',
                        DB::raw('(ordenfabricacion.CantidadTotal)'), 'partidasof_areas.FechaComienzo', 'partidasof_areas.FechaTermina',
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) /(ordenfabricacion.CantidadTotal)) * 100 ) as Progreso')
                    )
                    ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal','partidasof_areas.FechaComienzo', 'partidasof_areas.FechaTermina')  
                    ->get();
                    //dd($resultOF);

            //estacion preparado
            } elseif ($tipoOF === 'plemasPreparado') {
                $resultOF = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id',4)
                    ->select(
                        'ordenfabricacion.OrdenFabricacion',
                        DB::raw('(ordenfabricacion.CantidadTotal)'),
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) /(ordenfabricacion.CantidadTotal)) * 100 ) as Progreso')
                    )
                    ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal')  
                    ->get();
            //estacion ensamble
            }elseif($tipoOF === 'plemasEnsamble'){
                $resultOF = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', 5)
                    ->select(
                        'ordenfabricacion.OrdenFabricacion',
                        DB::raw('(ordenfabricacion.CantidadTotal)'), 'partidasof_areas.FechaComienzo', 'partidasof_areas.FechaTermina',
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) /(ordenfabricacion.CantidadTotal)) * 100 ) as Progreso')
                    )
                    ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal','partidasof_areas.FechaComienzo', 'partidasof_areas.FechaTermina')  
                    ->get();
            //estacion pulido
            }elseif($tipoOF === 'plemasPulido'){
                $resultOF = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', 6)
                    ->select(
                        'ordenfabricacion.OrdenFabricacion',
                        DB::raw('(ordenfabricacion.CantidadTotal)'), 'partidasof_areas.FechaComienzo', 'partidasof_areas.FechaTermina',
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) /(ordenfabricacion.CantidadTotal)) * 100 ) as Progreso')
                    )
                    ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal','partidasof_areas.FechaComienzo', 'partidasof_areas.FechaTermina')  
                    ->get();
            //estacion medicion
            }elseif($tipoOF === 'plemasMedicion'){
                $resultOF = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', 7)
                    ->select(
                        'ordenfabricacion.OrdenFabricacion',
                        DB::raw('(ordenfabricacion.CantidadTotal)'), 'partidasof_areas.FechaComienzo', 'partidasof_areas.FechaTermina',
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) /(ordenfabricacion.CantidadTotal)) * 100 ) as Progreso')
                    )
                    ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal','partidasof_areas.FechaComienzo', 'partidasof_areas.FechaTermina')  
                    ->get();
            //estacion visualizacion
            }elseif($tipoOF === 'plemasVisualizacion'){
                $resultOF = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', 8)
                    ->select(
                        'ordenfabricacion.OrdenFabricacion',
                        DB::raw('(ordenfabricacion.CantidadTotal)'), 'partidasof_areas.FechaComienzo', 'partidasof_areas.FechaTermina',
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) /(ordenfabricacion.CantidadTotal)) * 100 ) as Progreso')
                    )
                    ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal','partidasof_areas.FechaComienzo', 'partidasof_areas.FechaTermina')  
                    ->get();
            //estacion empaque
            }elseif($tipoOF=== 'plemasEmpaque'){
                $resultOF = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', 9)
                    ->select(
                        'ordenfabricacion.OrdenFabricacion',
                        DB::raw('(ordenfabricacion.CantidadTotal)'), 'partidasof_areas.FechaComienzo', 'partidasof_areas.FechaTermina',
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) /(ordenfabricacion.CantidadTotal)) * 100 ) as Progreso')
                    )
                    ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal','partidasof_areas.FechaComienzo', 'partidasof_areas.FechaTermina')  
                    ->get();

            } else {
                return response()->json([], 400); 
            }
            //dd($resultOF);
            return response()->json($resultOF);
        } else {
            return response()->json([], 204); 
        }

    }

*/
   