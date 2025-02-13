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
        $partidasAreas = DB::table('partidasof_areas')
        ->where('PartidasOF_id', $partidaId)  
        ->get();

        return view('layouts.busquedas', compact('partidasAreas'));
    
    }

///inicia orden venta

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

        $partidasAreas = DB::table('ordenventa')
            ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id') 
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
            ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
            ->join('areas', 'partidasof_areas.Areas_id', '=', 'areas.id') 
            ->where('ordenventa.OrdenVenta', $idVenta) 
            ->whereIn('partidasof_areas.Areas_id', [9])

            ->select('partidasof_areas.PartidasOF_id', 'areas.nombre as Estado', 'ordenventa.OrdenVenta') 
            ->get();
        if ($partidasAreas->isEmpty()) {
        
            $partidasAreas = null; 
        }

        return response()->json([
            'partidasAreas' => $partidasAreas 
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
            DB::raw('ROUND((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100, 2) as Progreso')
          
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
       
        //para cargar los datos 
        if (!empty($idVenta)) {
            //estacion eccorte
           
      
            if ($tipo === 'cortes') {
                $result = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
                ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->select(
                    DB::raw('GROUP_CONCAT(DISTINCT ordenfabricacion.OrdenFabricacion ORDER BY ordenfabricacion.OrdenFabricacion ASC SEPARATOR ", ") as OrdenesFabricacion'),
                    DB::raw('SUM(ordenfabricacion.CantidadTotal) as SumaCantidadTotal'),
                    DB::raw('SUM(partidasof.cantidad_partida) as SumaTotalPartidas'),
                    DB::raw('ROUND((SUM(partidasof.cantidad_partida) / SUM(ordenfabricacion.CantidadTotal)) * 100) as Progreso')
                )
                ->groupBy('ordenventa.id')
                ->get();
            
            //estacion suministros
            } elseif ($tipo === 'suministros') {
                $result = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
                ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id') 
                ->where('partidasof_areas.Areas_id', 3)
               
                ->select(
                    'ordenventa.OrdenVenta',
                    DB::raw('GROUP_CONCAT(DISTINCT ordenfabricacion.OrdenFabricacion ORDER BY ordenfabricacion.OrdenFabricacion ASC SEPARATOR ", ") as OrdenesFabricacion'),
                    DB::raw('SUM(ordenfabricacion.CantidadTotal) as SumaCantidadTotal'), // Remover DISTINCT en SUM
                    DB::raw('SUM(partidasof_areas.cantidad) as SumaTotalPartidas'),
                    DB::raw('ROUND((SUM(partidasof_areas.cantidad) / SUM(ordenfabricacion.CantidadTotal)) * 100) as Progreso') ,
                )
                    ->groupBy('ordenventa.OrdenVenta')
                    ->get();

                    // Ahora para calcular el porcentaje total ponderado
                    



                /* DB::raw('GROUP_CONCAT(DISTINCT ordenfabricacion.OrdenFabricacion ORDER BY ordenfabricacion.OrdenFabricacion ASC SEPARATOR ", ") as OrdenesFabricacion'),
                    DB::raw('SUM(ordenfabricacion.CantidadTotal) as SumaCantidadTotal'), // Remover DISTINCT en SUM
                    DB::raw('SUM(partidasof_areas.cantidad) as SumaTotalPartidas'),
                    DB::raw('ROUND((SUM(partidasof_areas.cantidad) / SUM(ordenfabricacion.CantidadTotal)) * 100) as Progreso') */
               
               
            //estacion preparado
            } elseif ($tipo === 'preparado') {
                $result = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
                ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id') 
                ->where('partidasof_areas.Areas_id', 4)
               
                ->select(
                    'ordenventa.OrdenVenta',
                    DB::raw('GROUP_CONCAT(DISTINCT ordenfabricacion.OrdenFabricacion ORDER BY ordenfabricacion.OrdenFabricacion ASC SEPARATOR ", ") as OrdenesFabricacion'),
                    DB::raw('SUM(ordenfabricacion.CantidadTotal) as SumaCantidadTotal'), // Remover DISTINCT en SUM
                    DB::raw('SUM(partidasof_areas.cantidad) as SumaTotalPartidas'),
                    DB::raw('ROUND((SUM(partidasof_areas.cantidad) / SUM(ordenfabricacion.CantidadTotal)) * 100) as Progreso') 
                )
                ->groupBy('ordenventa.OrdenVenta')
                ->get();
            //estacion ensamble
            }elseif($tipo === 'ensamble'){ 
                $result = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
                ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id') 
                ->where('partidasof_areas.Areas_id', 5)
                ->select(
                    'ordenventa.OrdenVenta',
                    DB::raw('GROUP_CONCAT(DISTINCT ordenfabricacion.OrdenFabricacion ORDER BY ordenfabricacion.OrdenFabricacion ASC SEPARATOR ", ") as OrdenesFabricacion'),
                    DB::raw('SUM(ordenfabricacion.CantidadTotal) as SumaCantidadTotal'), // Remover DISTINCT en SUM
                    DB::raw('SUM(partidasof_areas.cantidad) as SumaTotalPartidas'),
                    DB::raw('ROUND((SUM(partidasof_areas.cantidad) / SUM(ordenfabricacion.CantidadTotal)) * 100) as Progreso') 
                )
                ->groupBy('ordenventa.OrdenVenta')
                ->get();
            //estacion pulido
            }elseif($tipo === 'pulido'){
                $result = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
                ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id') 
                ->where('partidasof_areas.Areas_id', 6)
                ->select(
                    'ordenventa.OrdenVenta',
                    DB::raw('GROUP_CONCAT(DISTINCT ordenfabricacion.OrdenFabricacion ORDER BY ordenfabricacion.OrdenFabricacion ASC SEPARATOR ", ") as OrdenesFabricacion'),
                    DB::raw('SUM(ordenfabricacion.CantidadTotal) as SumaCantidadTotal'), // Remover DISTINCT en SUM
                    DB::raw('SUM(partidasof_areas.cantidad) as SumaTotalPartidas'),
                    DB::raw('ROUND((SUM(partidasof_areas.cantidad) / SUM(ordenfabricacion.CantidadTotal)) * 100) as Progreso') 
                )
                ->groupBy('ordenventa.OrdenVenta')
                ->get();
            //estacion medicion
            }elseif($tipo === 'medicion'){
                $result = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
                ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id') 
                ->where('partidasof_areas.Areas_id', 7)
                ->select(
                    'ordenventa.OrdenVenta',
                    DB::raw('GROUP_CONCAT(DISTINCT ordenfabricacion.OrdenFabricacion ORDER BY ordenfabricacion.OrdenFabricacion ASC SEPARATOR ", ") as OrdenesFabricacion'),
                    DB::raw('SUM(ordenfabricacion.CantidadTotal) as SumaCantidadTotal'), // Remover DISTINCT en SUM
                    DB::raw('SUM(partidasof_areas.cantidad) as SumaTotalPartidas'),
                    DB::raw('ROUND((SUM(partidasof_areas.cantidad) / SUM(ordenfabricacion.CantidadTotal)) * 100) as Progreso') 
                )
                ->groupBy('ordenventa.OrdenVenta')
                ->get();
            //estacion visualizacion
            }elseif($tipo === 'visualizacion'){
                $result = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
                ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id') 
                ->where('partidasof_areas.Areas_id', 8)
                ->select(
                    'ordenventa.OrdenVenta',
                    DB::raw('GROUP_CONCAT(DISTINCT ordenfabricacion.OrdenFabricacion ORDER BY ordenfabricacion.OrdenFabricacion ASC SEPARATOR ", ") as OrdenesFabricacion'),
                    DB::raw('SUM(ordenfabricacion.CantidadTotal) as SumaCantidadTotal'), // Remover DISTINCT en SUM
                    DB::raw('SUM(partidasof_areas.cantidad) as SumaTotalPartidas'),
                    DB::raw('ROUND((SUM(partidasof_areas.cantidad) / SUM(ordenfabricacion.CantidadTotal)) * 100) as Progreso') 
                )
                ->groupBy('ordenventa.OrdenVenta')
                ->get();
            //estacion empaque
            }elseif($tipo === 'empaque'){
                $result = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
                ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                ->where('partidasof_areas.Areas_id', 9)
                ->select(
                    'ordenventa.OrdenVenta',
                    DB::raw('GROUP_CONCAT(DISTINCT ordenfabricacion.OrdenFabricacion ORDER BY ordenfabricacion.OrdenFabricacion ASC SEPARATOR ", ") as OrdenesFabricacion'),
                    DB::raw('SUM(ordenfabricacion.CantidadTotal) as SumaCantidadTotal'), // Remover DISTINCT en SUM
                    DB::raw('SUM(partidasof_areas.cantidad) as SumaTotalPartidas'),
                    DB::raw('ROUND((SUM(partidasof_areas.cantidad) / SUM(ordenfabricacion.CantidadTotal)) * 100) as Progreso') 
                )
                ->groupBy('ordenventa.OrdenVenta')
                ->get();

            } else {
                return response()->json([], 400); 
            }
           
            return response()->json($result);
        } else {
            return response()->json([], 204); 
        }
    }
////
    
//inicia orden de frabicacion

    // Controlador para las órdenes de fabricación
    public function obtenerOrdenesFabricacion(Request $request)
    {
        $search = $request->input('search');
        $ordenesFabricacion = DB::table('ordenfabricacion')
        ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
        ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
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
        //para cargar los datos 
        if (!empty($idFabricacion)) {
            //estacion eccorte
            if ($tipoOF === 'plemasCorte') {
                $resultOF = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                
                ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                ->select(
                    'ordenfabricacion.OrdenFabricacion',
                    DB::raw('(ordenfabricacion.CantidadTotal)'),
                    DB::raw('SUM(partidasof.cantidad_partida) as TotalPartidas'),
                    DB::raw('ROUND((SUM(partidasof.cantidad_partida) /(ordenfabricacion.CantidadTotal)) * 100 ) as Progreso')
                )
                ->groupBy('partidasof.cantidad_partida', 'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal')  
                ->get();
                
                    
            //estacion suministros
            } elseif ($tipoOF === 'plemasSuministro') {
                $resultOF = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id') 
                ->join('partidasof_areas', 'PartidasOF.id', '=', 'partidasof_areas.PartidasOF_id') 
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', 3)
                    ->select(
                        'ordenfabricacion.OrdenFabricacion',
                        DB::raw('(ordenfabricacion.CantidadTotal)'),
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) /(ordenfabricacion.CantidadTotal)) * 100 ) as Progreso')
                    )
                    ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal')  
                    ->get();
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
                        DB::raw('(ordenfabricacion.CantidadTotal)'),
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) /(ordenfabricacion.CantidadTotal)) * 100 ) as Progreso')
                    )
                    ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal')  
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
                        DB::raw('(ordenfabricacion.CantidadTotal)'),
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) /(ordenfabricacion.CantidadTotal)) * 100 ) as Progreso')
                    )
                    ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal')  
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
                        DB::raw('(ordenfabricacion.CantidadTotal)'),
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) /(ordenfabricacion.CantidadTotal)) * 100 ) as Progreso')
                    )
                    ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal')  
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
                        DB::raw('(ordenfabricacion.CantidadTotal)'),
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) /(ordenfabricacion.CantidadTotal)) * 100 ) as Progreso')
                    )
                    ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal')  
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
                        DB::raw('(ordenfabricacion.CantidadTotal)'),
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof_areas.cantidad) /(ordenfabricacion.CantidadTotal)) * 100 ) as Progreso')
                    )
                    ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.OrdenFabricacion','ordenfabricacion.CantidadTotal')  
                    ->get();

            } else {
                return response()->json([], 400); 
            }
            return response()->json($resultOF);
        } else {
            return response()->json([], 204); 
        }

    }
////
    
}