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
use App\Models\Comentarios;
use App\Models\Linea;
use App\Models\Areas;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon as CarbonClass;



class BusquedaController extends Controller
{
    protected $AreaClasificacion;
    protected $AreaArmado;
    protected $AreaPulido;
    protected $AreaEmpaque;
    protected $ObjBusqueda;
    public function __construct(){
        $this->AreaClasificacion=18;
        $this->AreaPulido=9;
        $this->AreaArmado=16;
        $this->AreaEmpaque=17;
    }
    //vista
    public function index(Request $request)
    {
        $user = Auth::user();
        if($user->hasPermission('Vista Progreso')){
        $partidaId = 1;
        $partidasAreas = DB::table('partidasof_areas')
        ->where('PartidasOF_id', $partidaId)  
        ->get();
        return view('Busqueda.Progreso', compact('partidasAreas'));
        }else
        return redirect()->route('error.');
    
    }
    //Nuevos Metodos
    public function TipoOrden(Request $request){
        $NumeroOrden = $request->NumeroOrden;
        $TipoOrden = $request->TipoOrden;
        $Ordenes = '';
        $Lista = '';
        if($TipoOrden == 'OF'){
            $Ordenes=OrdenFabricacion::where('OrdenFabricacion', 'like', '%' . $NumeroOrden . '%')->orderBy('OrdenFabricacion', 'asc')->get();
            foreach($Ordenes as $key=>$Orden){
                if($key==0){
                    $Lista.='<a class="list-group-item list-group-item-action p-1 m-0 active" onclick="SeleccionarNumOrden('.$Orden->OrdenFabricacion.')">'.$Orden->OrdenFabricacion.'</a>';
                }else{
                    $Lista.='<a class="list-group-item list-group-item-action p-1 m-0 " onclick="SeleccionarNumOrden('.$Orden->OrdenFabricacion.')">'.$Orden->OrdenFabricacion.'</a>';
                }
            }
        }else{
            $Ordenes=OrdenVenta::where('OrdenVenta', 'like', '%' . $NumeroOrden . '%')->orderBy('OrdenVenta', 'asc')->get();
            foreach($Ordenes as $key=>$Orden){
                if($key==0){
                    $Lista.='<a class="list-group-item list-group-item-action p-1 m-0 active" onclick="SeleccionarNumOrden('.$Orden->OrdenVenta.')">'.$Orden->OrdenVenta.'</a>';
                }else{
                    $Lista.='<a class="list-group-item list-group-item-action p-1 m-0 " onclick="SeleccionarNumOrden('.$Orden->OrdenVenta.')">'.$Orden->OrdenVenta.'</a>';
                }
            }
        }
        return $Lista;

    }
    //End nuevos metodos
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

        $estatus = DB::table('ordenventa')
        ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
        ->where('ordenventa.OrdenVenta', $idVenta)
        ->select(
            'ordenfabricacion.OrdenFabricacion',
            DB::raw("CASE WHEN ordenfabricacion.Cerrada = 1 THEN 'Abierta' ELSE 'Cerrada' END as Estado")
        )
        ->get();
    
        return response()->json([
            "OrdenVenta" => $total->OrdenVenta,
            "OrdenFabricacion" => $total->OrdenFabricacion,
            "CantidadTotal" => $cantidadTotal,
            "Cantidad" => $cantidadPartidas,
            "Porcentaje" => round($porcentaje, 2), // Sin símbolo "%"
            "partidasAreas" => $partidasAreas,
            "Estatus" => $estatus,// Aquí agregamos el estado
        ]);
        

    }
    //progreso de stage
    public function GraficarOROF(Request $request)
    {
        $idVenta = $request->input('id');  // ID de la orden de venta
        $stage = $request->input('stage'); // Etapa
    
        // Consulta base (sin ejecutarla aún)
        $query = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
            ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->where('partidasof_areas.TipoPartida', 'N')
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
    
        // Consulta de cortes
        $cortes = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
            ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'ordenventa.OrdenVenta',
                'ordenfabricacion.CantidadTotal',
                'ordenfabricacion.OrdenFabricacion',
                DB::raw('ordenfabricacion.OrdenFabricacion as OrdenesFabricacion'),
                DB::raw('GROUP_CONCAT(DISTINCT partidasof.id ORDER BY partidasof.id ASC SEPARATOR ", ") as PartidaOF_ID'),
                DB::raw('SUM(partidasof.cantidad_partida) as SumaTotalPartidas'),
                DB::raw('ROUND(((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100), 2) as Progreso')
            )
            ->groupBy('ordenventa.OrdenVenta', 'ordenfabricacion.CantidadTotal', 'ordenfabricacion.OrdenFabricacion');
    
        // Filtro por etapa
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
        } elseif ($stage == 'stage10') {
            $query->where('partidasof_areas.Areas_id', 10);
        } elseif ($stage == 'stage11') {
            $query->where('partidasof_areas.Areas_id', 11);
        } elseif ($stage == 'stage12') {
            $query->where('partidasof_areas.Areas_id', 12);
        } elseif ($stage == 'stage13') {
            $query->where('partidasof_areas.Areas_id', 13);
        } elseif ($stage == 'stage14') {
            $query->where('partidasof_areas.Areas_id', 14);
        } elseif ($stage == 'stage15') {
            $query->where('partidasof_areas.Areas_id', 15);
        } elseif ($stage == 'stage16') {
            $query->where('partidasof_areas.Areas_id', 16);
        } elseif ($stage == 'stage17') {
            $query->where('partidasof_areas.Areas_id', 17);
        }
    
        // Obtener los resultados solo al final
        $OR = ($stage == 'stage2') ? $cortes->get() : $query->get();
    
        return response()->json($OR);
    }
    
    //graficadores
    public function Graficador(Request $request)
    {
        $idVenta = $request->input('id');
        $escaner=DB::table('ordenVenta')
        ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
        ->where('ordenventa.OrdenVenta', $idVenta)
        
        ->value('Escaner'); 
        //return( $escaner);
        // Verificar el valor de Escaner
        if ($escaner == 1) {

                // Definir los IDs de las áreas
                $areaIds = [
                    'cortes' => 0,
                    'suministros' => 3,
                    'transicion'=> 4,
                    'preparado' => 5,
                    'ribonizado'=>6,
                    'ensamble' => 7,
                    'cortesFibra'=>8,
                    'pulido' =>9,
                    'armado'=>10,
                    'inspeccion'=>11,
                    'polaridad'=>12,
                    'crimpado'=>13,
                    'medicion' => 14,
                    'visualizacion' => 15,
                    'montaje'=>16,
                    'empaque' => 17,
                ];

                // Obtener las órdenes de fabricación
                $ordenesfabricacion = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
                    ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
                    ->select(
                        DB::raw("GROUP_CONCAT(ordenfabricacion.OrdenFabricacion ORDER BY ordenfabricacion.OrdenFabricacion ASC) as OrdenFabricacion"),
                        DB::raw("SUM(CantidadTotal) as SumaTotalCantidadTotal")
                    )
                    ->groupBy('ordenventa.id')
                    ->get();

                // Si no hay órdenes de fabricación, devolvemos una respuesta vacía
                if ($ordenesfabricacion->isEmpty()) {
                    return response()->json([
                        'ordenesfabricacion' => [],
                        'Progreso' => ['OrdenVenta' => null, 'Progreso' => 0],
                    ]);
                }
                // Obtener los datos de 'cortes' por separado porque tiene una estructura diferente
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

                // Calcular el progreso para cada tipo
                $data = [];

                foreach ($areaIds as $tipo => $areaId) {
                    if ($tipo === 'cortes') {
                        // Calcular progreso para 'cortes'
                        $totalCantidad = (int) $ordenesfabricacion->sum('SumaTotalCantidadTotal') ?? 0;
                        $totalPartidas = (int) $cortesorden->sum('SumaCantidadPartidaTotal') ?? 0;
                    } else {
                        // Obtener datos de otras áreas
                        $result = OrdenVenta::where('ordenventa.OrdenVenta', $idVenta)
                            ->join('ordenfabricacion', 'ordenventa.id', '=', 'ordenfabricacion.OrdenVenta_id')
                            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                            ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                            ->where('partidasof_areas.Areas_id', $areaId)
                            ->select(
                                'ordenventa.OrdenVenta',
                                'ordenfabricacion.OrdenFabricacion',
                                DB::raw('SUM(partidasof_areas.Cantidad) as SumaTotalPartidas'),
                                'ordenfabricacion.CantidadTotal',
                                DB::raw('GROUP_CONCAT(partidasof_areas.id) as id')
                            )
                            ->groupBy('ordenventa.OrdenVenta', 'ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
                            ->get();

                        // Calcular progreso
                        $totalCantidad = (int) $ordenesfabricacion->sum('SumaTotalCantidadTotal') ?? 0;
                        $totalPartidas = (int) $result->sum('SumaTotalPartidas') ?? 0;
                    }

                    $progreso = ($totalCantidad > 0) ? ($totalPartidas / $totalCantidad) * 100 : 0;

                    // Guardar en la respuesta
                    $data[$tipo] = [
                        'result' => ($tipo === 'cortes') ? $cortesorden : $result,
                        'Progreso' => round($progreso, 2),
                    ];
                }

                // Respuesta final
                return response()->json([
                    'ordenesfabricacion' => $ordenesfabricacion,
                    'data' => $data,
                ]);
            } elseif ($escaner == 0) {
            

            }
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
    // Graficador OF
    public function GraficadorFabricacion(Request $request) 
    {
        $idFabricacion = $request->input('id');
        $result = [];
        $escaner = DB::table('ordenfabricacion')
            ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
            ->value('Escaner'); 
        // Verificar el valor de Escaner
        if ($escaner == 0) {
            $estacionesAreas = [
                4 => 'plemasTransiciondia',
                5 => 'plemasPreparadodia',
                6 => 'plemasRibonizadodia',
                7 => 'plemasEnsambledia',
                8 => 'plemasCortesFibradia',
                9 => 'plemasPulidodia',
                10 => 'plemasArmadodia',
                11 => 'plemasInspecciondia',
                12 => 'plemasPolaridaddia',
                13 => 'plemasCrimpadodia',
                14 => 'plemasMediciondia',
                15 => 'plemasVisualizaciondia',
                16 => 'plemasMontajedia',
               
            ];
            $estacionArea2 = [
                2 => 'plemasCorte',
            ];
            $estacionesArea3= [
                3 => 'plemasSuministrodia',

            ];
            $estacionArea9 = [
                17 => 'plemasEmpaque',
            ];
            
            // Procesar las estaciones del primer conjunto (estacionesAreas)
            foreach ($estacionesAreas as $areaId => $areaName) {
                // Obtener la cantidad total de la orden
                $total = DB::table('ordenfabricacion')
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->select('ordenfabricacion.CantidadTotal')
                    ->first();
                $cantidadTotal = $total ? (int)$total->CantidadTotal : 0;
    
                // Obtener el total de partidas finales (TipoPartida = 'F')
                $resultOF = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', $areaId)
                    ->where('partidasof_areas.TipoPartida', 'F')
                    ->select(DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'))
                    ->first();
                $totalF = $resultOF ? (int)$resultOF->TotalPartidas : 0;
    
                // Obtener los datos de retrabajo (TipoPartida = 'R')
                $resultadosR_F = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', $areaId)
                    ->whereIn('partidasof_areas.TipoPartida', ['R']) 
                    ->select(
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('SUM(CASE WHEN partidasof_areas.FechaTermina IS NOT NULL THEN partidasof_areas.cantidad ELSE 0 END) as TotalPartidasTerminadas'),
                        DB::raw('SUM(CASE WHEN partidasof_areas.FechaTermina IS NULL THEN partidasof_areas.cantidad ELSE 0 END) as TotalPartidasPendientes'),
                        DB::raw('MIN(partidasof_areas.FechaComienzo) as FechaComienzo'),
                        DB::raw('MAX(partidasof_areas.FechaTermina) as FechaTermina')
                    )
                    ->groupBy('partidasof_areas.Areas_id')
                    ->first();
    
                $result[$areaName] = [];

                if (!$resultadosR_F) {
                    $result[$areaName][] = [
                        'nombre' => $areaName,
                        'diferencia' => $totalF,
                        'totalF' => $totalF,
                        'totalR' => 0,
                        'cantidadTotal' => $cantidadTotal,
                        'porcentaje' => $cantidadTotal > 0 ? ($totalF / $cantidadTotal) * 100 : 0,
                        'FechaComienzo' => null,
                        'FechaTermina' => null
                    ];
                } else {
                    $totalR = (int)$resultadosR_F->TotalPartidas;
                    $totalTerminadas = (int)$resultadosR_F->TotalPartidasTerminadas;
                    $totalPendientes = (int)$resultadosR_F->TotalPartidasPendientes;
                    $diferencia = $totalPendientes;
                    $porcentaje = $cantidadTotal > 0 ? (($totalF - $totalPendientes) / $cantidadTotal) * 100 : 0;
                    $result[$areaName][] = [
                        'nombre' => $areaName,
                        'diferencia' => $diferencia,
                        'totalF' => $totalF,
                        'totalR' => $totalR,
                        'cantidadTotal' => $cantidadTotal,
                        'porcentaje' => $porcentaje,
                        'FechaComienzo' => $resultadosR_F->FechaComienzo,
                        'FechaTermina' => $resultadosR_F->FechaTermina ?? null
                    ];
    
                    if ($totalPendientes > 0) {
                        $result[$areaName][] = [
                            'nombre' => $areaName,
                            'diferencia' => $totalPendientes,
                            'totalF' => $totalF,
                            'totalR' => $totalR,
                            'cantidadTotal' => $cantidadTotal,
                            'porcentaje' => $porcentaje,
                            'FechaComienzo' => $resultadosR_F->FechaComienzo,
                            'FechaTermina' => null
                        ];
                    }
                }
            }
            foreach ($estacionArea9 as $areaId => $areaName) {
                // Obtener la cantidad total de la orden
                $total = DB::table('ordenfabricacion')
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->select('ordenfabricacion.CantidadTotal')
                    ->first();
                $cantidadTotal = $total ? (int)$total->CantidadTotal : 0;
    
                // Obtener el total de partidas finales (TipoPartida = 'F')
                $resultOF = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', $areaId)
                    ->where('partidasof_areas.TipoPartida', 'N')
                    ->select(DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'))
                    ->first();
                $totalN = $resultOF ? (int)$resultOF->TotalPartidas : 0;
    
                // Obtener los datos de retrabajo (TipoPartida = 'R')
                $resultadosR_F = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', $areaId)
                    ->whereIn('partidasof_areas.TipoPartida', ['R']) 
                    ->select(
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('SUM(CASE WHEN partidasof_areas.FechaTermina IS NOT NULL THEN partidasof_areas.cantidad ELSE 0 END) as TotalPartidasTerminadas'),
                        DB::raw('SUM(CASE WHEN partidasof_areas.FechaTermina IS NULL THEN partidasof_areas.cantidad ELSE 0 END) as TotalPartidasPendientes'),
                        DB::raw('MIN(partidasof_areas.FechaComienzo) as FechaComienzo'),
                        DB::raw('MAX(partidasof_areas.FechaTermina) as FechaTermina')
                    )
                    ->groupBy('partidasof_areas.Areas_id')
                    ->first();
    
                $result[$areaName] = [];

                if (!$resultadosR_F) {
                    $result[$areaName][] = [
                        'nombre' => $areaName,
                        'diferencia' => $totalF,
                        'totalN' => $totalN,
                        'totalR' => 0,
                        'cantidadTotal' => $cantidadTotal,
                        'porcentaje' => $cantidadTotal > 0 ? ($totalN / $cantidadTotal) * 100 : 0,
                        'FechaComienzo' => null,
                        'FechaTermina' => null
                    ];
                } else {
                    $totalR = (int)$resultadosR_F->TotalPartidas;
                    $totalTerminadas = (int)$resultadosR_F->TotalPartidasTerminadas;
                    $totalPendientes = (int)$resultadosR_F->TotalPartidasPendientes;
                    $diferencia = $totalPendientes;
                    $porcentaje = $cantidadTotal > 0 ? (($totalF - $totalPendientes) / $cantidadTotal) * 100 : 0;
                    $result[$areaName][] = [
                        'nombre' => $areaName,
                        'diferencia' => $diferencia,
                        'totalN' => $totalN,
                        'totalR' => $totalR,
                        'cantidadTotal' => $cantidadTotal,
                        'porcentaje' => $porcentaje,
                        'FechaComienzo' => $resultadosR_F->FechaComienzo,
                        'FechaTermina' => $resultadosR_F->FechaTermina ?? null
                    ];
    
                    if ($totalPendientes > 0) {
                        $result[$areaName][] = [
                            'nombre' => $areaName,
                            'diferencia' => $totalPendientes,
                            'totalN' => $totalN,
                            'totalR' => $totalR,
                            'cantidadTotal' => $cantidadTotal,
                            'porcentaje' => $porcentaje,
                            'FechaComienzo' => $resultadosR_F->FechaComienzo,
                            'FechaTermina' => null
                        ];
                    }
                }
            }
            foreach ($estacionArea2 as $areaId => $areaName) {
                $area2 = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->select(
                        DB::raw('MIN(partidasof.FechaComienzo) as FechaComienzo'),
                        DB::raw('MAX(partidasof.FechaFinalizacion) as FechaFinalizacion'),
                        DB::raw('ordenfabricacion.CantidadTotal'),
                        DB::raw('SUM(partidasof.cantidad_partida) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100) as porcentaje')
                    )
                    ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
                    ->first();
        
                $result[$areaName] = [];
        
                if (!$area2) {
                    $result[$areaName][] = [
                        'nombre' => $areaName,
                        'diferencia' => max(0, $totalF),
                        'totalF' => $totalF,
                        'cantidadTotal' => $cantidadTotal,
                        'porcentaje' => $cantidadTotal > 0 ? ($totalF / $cantidadTotal) * 100 : 0,
                        'FechaComienzo' => null,
                        'FechaTermina' => null
                    ];
                } else {
                    $result[$areaName][] = [
                        'nombre' => $areaName,
                        'diferencia' => max(0, $totalF - (int)$area2->TotalPartidas),
                        'totalF' => $totalF,
                        'cantidadTotal' => $cantidadTotal,
                        'porcentaje' => $area2->porcentaje,
                        'FechaComienzo' => $area2->FechaComienzo,
                        'FechaTermina' => $area2->FechaFinalizacion
                    ];
                }
            }
            foreach ($estacionesArea3 as $areaId => $areaName) {
                // Obtener la cantidad total de la orden
                $total = DB::table('ordenfabricacion')
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->select('ordenfabricacion.CantidadTotal')
                    ->first();
                $cantidadTotal = $total ? (int)$total->CantidadTotal : 0;
                // Obtener el total de partidas finales (TipoPartida = 'F')
                $resultOF = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', $areaId)
                    ->where('partidasof_areas.TipoPartida', 'N')
                    ->select(DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'))
                    ->first();
                $totalN = $resultOF ? (int)$resultOF->TotalPartidas : 0;
    
                // Obtener los datos de retrabajo (TipoPartida = 'R')
                $resultadosR_F = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', $areaId)
                    ->whereIn('partidasof_areas.TipoPartida', ['R']) 
                    ->select(
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('SUM(CASE WHEN partidasof_areas.FechaTermina IS NOT NULL THEN partidasof_areas.cantidad ELSE 0 END) as TotalPartidasTerminadas'),
                        DB::raw('SUM(CASE WHEN partidasof_areas.FechaTermina IS NULL THEN partidasof_areas.cantidad ELSE 0 END) as TotalPartidasPendientes'),
                        DB::raw('MIN(partidasof_areas.FechaComienzo) as FechaComienzo'),
                        DB::raw('MAX(partidasof_areas.FechaTermina) as FechaTermina')
                    )
                    ->groupBy('partidasof_areas.Areas_id')
                    ->first();
    
                $result[$areaName] = [];

                if (!$resultadosR_F) {
                    $result[$areaName][] = [
                        'nombre' => $areaName,
                        'diferencia' => $totalF,
                        'totalN' => $totalN,
                        'totalR' => 0,
                        'cantidadTotal' => $cantidadTotal,
                        'porcentaje' => $cantidadTotal > 0 ? ($totalN / $cantidadTotal) * 100 : 0,
                        'FechaComienzo' => null,
                        'FechaTermina' => null
                    ];
                } else {
                    $totalR = (int)$resultadosR_F->TotalPartidas;
                    $totalTerminadas = (int)$resultadosR_F->TotalPartidasTerminadas;
                    $totalPendientes = (int)$resultadosR_F->TotalPartidasPendientes;
                    $diferencia = $totalPendientes;
                    $porcentaje = $cantidadTotal > 0 ? (($totalF - $totalPendientes) / $cantidadTotal) * 100 : 0;
                    $result[$areaName][] = [
                        'nombre' => $areaName,
                        'diferencia' => $diferencia,
                        'totalN' => $totalN,
                        'totalR' => $totalR,
                        'cantidadTotal' => $cantidadTotal,
                        'porcentaje' => $porcentaje,
                        'FechaComienzo' => $resultadosR_F->FechaComienzo,
                        'FechaTermina' => $resultadosR_F->FechaTermina ?? null
                    ];
    
                    if ($totalPendientes > 0) {
                        $result[$areaName][] = [
                            'nombre' => $areaName,
                            'diferencia' => $totalPendientes,
                            'totalN' => $totalN,
                            'totalR' => $totalR,
                            'cantidadTotal' => $cantidadTotal,
                            'porcentaje' => $porcentaje,
                            'FechaComienzo' => $resultadosR_F->FechaComienzo,
                            'FechaTermina' => null
                        ];
                    }
                }
            }
        } elseif ($escaner == 1) {
            $estacionesAreas = [
                3 => 'plemasSuministrodia',
                4 => 'plemasTransiciondia',
                5 => 'plemasPreparadodia',
                6 => 'plemasRibonizadodia',
                7 => 'plemasEnsambledia',
                8 => 'plemasCortesFibradia',
                9 => 'plemasPulidodia',
                10 => 'plemasArmadodia',
                11 => 'plemasInspecciondia',
                12 => 'plemasPolaridaddia',
                13 => 'plemasCrimpadoddia',
                14 => 'plemasMediciondia',
                15 => 'plemasVisualizaciondia',
                16 => 'plemasMontaje',
                17 => 'plemasEmpaque',
            ];
            $estacionArea2 = [
                2 => 'plemasCorte',
            ];
            foreach ($estacionesAreas as $areaId => $areaName) {
                $total = DB::table('ordenfabricacion')
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->select('ordenfabricacion.CantidadTotal')
                    ->first();
                $cantidadTotal = $total ? (int)$total->CantidadTotal : 0;
                $resultOF = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', $areaId)
                    ->where('partidasof_areas.TipoPartida', 'N')
                    ->select(DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'))
                    ->first();
                $totalN = $resultOF ? (int)$resultOF->TotalPartidas : 0;
                $resultadosR = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->where('partidasof_areas.Areas_id', $areaId)
                    ->where('partidasof_areas.TipoPartida', 'R')
                    ->select(
                        DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                        DB::raw('SUM(CASE WHEN partidasof_areas.FechaTermina IS NOT NULL THEN partidasof_areas.cantidad ELSE 0 END) as TotalPartidasTerminadas'),
                        DB::raw('SUM(CASE WHEN partidasof_areas.FechaTermina IS NULL THEN partidasof_areas.cantidad ELSE 0 END) as TotalPartidasPendientes'),
                        DB::raw('MIN(partidasof_areas.FechaComienzo) as FechaComienzo'),
                        DB::raw('MAX(partidasof_areas.FechaTermina) as FechaTermina')
                    )
                    ->groupBy('partidasof_areas.Areas_id')
                    ->first();
                $result[$areaName] = [];
                if (!$resultadosR) {
                    $result[$areaName][] = [
                        'nombre' => $areaName,
                        'diferencia' => $totalN,
                        'totalN' => $totalN,
                        'totalR' => 0,
                        'cantidadTotal' => $cantidadTotal,
                        'porcentaje' => $cantidadTotal > 0 ? ($totalN / $cantidadTotal) * 100 : 0,
                        'FechaComienzo' => null,
                        'FechaTermina' => null
                    ];
                } else {
                    $totalR = (int)$resultadosR->TotalPartidas;
                    $totalTerminadas = (int)$resultadosR->TotalPartidasTerminadas;
                    $totalPendientes = (int)$resultadosR->TotalPartidasPendientes;
                    $diferencia = $totalPendientes;
                    $porcentaje = $cantidadTotal > 0 ? (($totalN - $totalPendientes) / $cantidadTotal) * 100 : 0;
    
                    $result[$areaName][] = [
                        'nombre' => $areaName,
                        'diferencia' => $diferencia,
                        'totalN' => $totalN,
                        'totalR' => $totalR,
                        'cantidadTotal' => $cantidadTotal,
                        'porcentaje' => $porcentaje,
                        'FechaComienzo' => $resultadosR->FechaComienzo,
                        'FechaTermina' => $resultadosR->FechaTermina ?? null
                    ];
    
                    if ($totalPendientes > 0) {
                        $result[$areaName][] = [
                            'nombre' => $areaName,
                            'diferencia' => $totalPendientes,
                            'totalN' => $totalN,
                            'totalR' => $totalR,
                            'cantidadTotal' => $cantidadTotal,
                            'porcentaje' => $porcentaje,
                            'FechaComienzo' => $resultadosR->FechaComienzo,
                            'FechaTermina' => null
                        ];
                    }
                }
            }
            foreach ($estacionArea2 as $areaId => $areaName) {
                $area2 = DB::table('ordenfabricacion')
                    ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                    ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                    ->select(
                        DB::raw('MIN(partidasof.FechaComienzo) as FechaComienzo'),
                        DB::raw('MAX(partidasof.FechaFinalizacion) as FechaFinalizacion'),
                        DB::raw('ordenfabricacion.CantidadTotal'),
                        DB::raw('SUM(partidasof.cantidad_partida) as TotalPartidas'),
                        DB::raw('ROUND((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100) as porcentaje')
                    )
                    ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
                    ->first();
        
                $result[$areaName] = [];
                //return $result;
                if (!$area2) {
                    $result[$areaName][] = [
                        'nombre' => $areaName,
                        'diferencia' => max(0, $totalN),
                        'totalN' => $totalN,
                        'cantidadTotal' => $cantidadTotal,
                        'porcentaje' => $cantidadTotal > 0 ? ($totalN / $cantidadTotal) * 100 : 0,
                        'FechaComienzo' => null,
                        'FechaTermina' => null
                    ];
                } else {
                    $result[$areaName][] = [
                        'nombre' => $areaName,
                        'diferencia' => max(0, $totalN - (int)$area2->TotalPartidas),
                        'totalN' => $totalN,
                        'cantidadTotal' => $cantidadTotal,
                        'porcentaje' => $area2->porcentaje,
                        'FechaComienzo' => $area2->FechaComienzo,
                        'FechaTermina' => $area2->FechaFinalizacion
                    ];
                }
                
            }
        }
        return response()->json(['estaciones' => $result]);

        // Graficador OF
        /*
        {
        $idFabricacion = $request->input('id');
        $estacionesAreas = [
            3 => 'plemasSuministrodia',
            4 => 'plemasPreparadodia',
            5 => 'plemasEnsambledia',
            6 => 'plemasPulidodia',
            7 => 'plemasMediciondia',
            8 => 'plemasVisualizaciondia',
            9 => 'plemasEmpaque',
        ];
        $estacionArea2 = [
            2 => 'plemasCorte',
        ];
        $result = [];

        return$escanernoecaner = DB::table('ordenfabricacion')
        ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
        ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
        ->select(
            'ordenfabricacion.OrdenFabricacion','ordenfabricacion.Escaner'

        )
        ->first();

        foreach ($estacionesAreas as $areaId => $areaName) {
            $total = DB::table('ordenfabricacion')
                ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                ->select('ordenfabricacion.CantidadTotal')
                ->first();
            $cantidadTotal = $total ? (int)$total->CantidadTotal : 0;

            $resultOF = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                ->where('partidasof_areas.Areas_id', $areaId)
                ->where('partidasof_areas.TipoPartida', 'F')
                ->select(DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'))
                ->first();
            $totalF = $resultOF ? (int)$resultOF->TotalPartidas : 0;
            $resultadosR_F = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                ->where('partidasof_areas.Areas_id', $areaId)
                ->whereIn('partidasof_areas.TipoPartida', ['R']) 
                ->select(
                    DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                    DB::raw('SUM(CASE WHEN partidasof_areas.FechaTermina IS NOT NULL THEN partidasof_areas.cantidad ELSE 0 END) as TotalPartidasTerminadas'),
                    DB::raw('SUM(CASE WHEN partidasof_areas.FechaTermina IS NULL THEN partidasof_areas.cantidad ELSE 0 END) as TotalPartidasPendientes'),
                    DB::raw('MIN(partidasof_areas.FechaComienzo) as FechaComienzo'),
                    DB::raw('MAX(partidasof_areas.FechaTermina) as FechaTermina')
                )
                ->groupBy('partidasof_areas.Areas_id')
                ->first();

            $result[$areaName] = [];

            // Verificar si no existen datos de retrabajo o finalizados y agregar resultados
            if (!$resultadosR_F) {
                $result[$areaName][] = [
                    'nombre' => $areaName,
                    'diferencia' => $totalF,
                    'totalF' => $totalF,
                    'totalR' => 0,
                    'cantidadTotal' => $cantidadTotal,
                    'porcentaje' => $cantidadTotal > 0 ? ($totalF / $cantidadTotal) * 100 : 0,
                    'FechaComienzo' => null,
                    'FechaTermina' => null
                ];
            } else {
                $totalR = (int)$resultadosR_F->TotalPartidas;
                $totalTerminadas = (int)$resultadosR_F->TotalPartidasTerminadas;
                $totalPendientes = (int)$resultadosR_F->TotalPartidasPendientes;
                $diferencia = $totalPendientes;
                $porcentaje = $cantidadTotal > 0 ? (($totalF - $totalPendientes) / $cantidadTotal) * 100 : 0;
                $result[$areaName][] = [
                    'nombre' => $areaName,
                    'diferencia' => $diferencia,
                    'totalF' => $totalF,
                    'totalR' => $totalR,
                    'cantidadTotal' => $cantidadTotal,
                    'porcentaje' => $porcentaje,
                    'FechaComienzo' => $resultadosR_F->FechaComienzo,
                    'FechaTermina' => $resultadosR_F->FechaTermina ?? null
                ];

                if ($totalPendientes > 0) {
                    $result[$areaName][] = [
                        'nombre' => $areaName,
                        'diferencia' => $totalPendientes,
                        'totalF' => $totalF,
                        'totalR' => $totalR,
                        'cantidadTotal' => $cantidadTotal,
                        'porcentaje' => $porcentaje,
                        'FechaComienzo' => $resultadosR_F->FechaComienzo,
                        'FechaTermina' => null
                    ];
                }
            }
        }

        return response()->json(['estaciones' => $result]);
        */

        /*
        $idFabricacion = $request->input('id'); 
        $estacionesAreas = [ 
            3 => 'plemasSuministrodia', 
            4 => 'plemasPreparadodia', 
            5 => 'plemasEnsambledia', 
            6 => 'plemasPulidodia', 
            7 => 'plemasMediciondia', 
            8 => 'plemasVisualizaciondia', 
            9 => 'plemasEmpaque', 
        ]; 
        $estacionArea2 = [ 
            2 => 'plemasCorte', 
        ]; 
        $result = []; 

        // Procesar las estaciones del primer conjunto (estacionesAreas)
        foreach ($estacionesAreas as $areaId => $areaName) {
            // Obtener la cantidad total de la orden
            $total = DB::table('ordenfabricacion')
                ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                ->select('ordenfabricacion.CantidadTotal')
                ->first();
            $cantidadTotal = $total ? (int)$total->CantidadTotal : 0;

            // Obtener el total de partidas normales (TipoPartida = 'N')
            $resultOF = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                ->where('partidasof_areas.Areas_id', $areaId)
                ->where('partidasof_areas.TipoPartida', 'N')
                ->select(DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'))
                ->first();
            $totalN = $resultOF ? (int)$resultOF->TotalPartidas : 0;

            // Obtener los datos de retrabajo (TipoPartida = 'R')
            $resultadosR = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                ->where('partidasof_areas.Areas_id', $areaId)
                ->where('partidasof_areas.TipoPartida', 'R')
                ->select(
                    DB::raw('SUM(partidasof_areas.cantidad) as TotalPartidas'),
                    DB::raw('SUM(CASE WHEN partidasof_areas.FechaTermina IS NOT NULL THEN partidasof_areas.cantidad ELSE 0 END) as TotalPartidasTerminadas'),
                    DB::raw('SUM(CASE WHEN partidasof_areas.FechaTermina IS NULL THEN partidasof_areas.cantidad ELSE 0 END) as TotalPartidasPendientes'),
                    DB::raw('MIN(partidasof_areas.FechaComienzo) as FechaComienzo'),
                    DB::raw('MAX(partidasof_areas.FechaTermina) as FechaTermina')
                )
                ->groupBy('partidasof_areas.Areas_id')
                ->first();

            $result[$areaName] = [];

            // Verificar si no existen datos de retrabajo y agregar resultados
            if (!$resultadosR) {
                $result[$areaName][] = [
                    'nombre' => $areaName,
                    'diferencia' => $totalN,
                    'totalN' => $totalN,
                    'totalR' => 0,
                    'cantidadTotal' => $cantidadTotal,
                    'porcentaje' => $cantidadTotal > 0 ? ($totalN / $cantidadTotal) * 100 : 0,
                    'FechaComienzo' => null,
                    'FechaTermina' => null
                ];
            } else {
                $totalR = (int)$resultadosR->TotalPartidas;
                $totalTerminadas = (int)$resultadosR->TotalPartidasTerminadas;
                $totalPendientes = (int)$resultadosR->TotalPartidasPendientes;
                $diferencia = $totalPendientes;
                $porcentaje = $cantidadTotal > 0 ? (($totalN - $totalPendientes) / $cantidadTotal) * 100 : 0;
                $result[$areaName][] = [
                    'nombre' => $areaName,
                    'diferencia' => $diferencia,
                    'totalN' => $totalN,
                    'totalR' => $totalR,
                    'cantidadTotal' => $cantidadTotal,
                    'porcentaje' => $porcentaje,
                    'FechaComienzo' => $resultadosR->FechaComienzo,
                    'FechaTermina' => $resultadosR->FechaTermina ?? null
                ];

                if ($totalPendientes > 0) {
                    $result[$areaName][] = [
                        'nombre' => $areaName,
                        'diferencia' => $totalPendientes,
                        'totalN' => $totalN,
                        'totalR' => $totalR,
                        'cantidadTotal' => $cantidadTotal,
                        'porcentaje' => $porcentaje,
                        'FechaComienzo' => $resultadosR->FechaComienzo,
                        'FechaTermina' => null
                    ];
                }
            }
        }

        // Procesar el segundo conjunto de estaciones (estacionArea2)
        foreach ($estacionArea2 as $areaId => $areaName) {
            $area2 = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
                ->select(
                    DB::raw('MIN(partidasof.FechaComienzo) as FechaComienzo'),
                    DB::raw('MAX(partidasof.FechaFinalizacion) as FechaFinalizacion'),
                    DB::raw('ordenfabricacion.CantidadTotal'),
                    DB::raw('SUM(partidasof.cantidad_partida) as TotalPartidas'),
                    DB::raw('ROUND((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100) as porcentaje')
                )
                ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
                ->first();

            $result[$areaName] = [];

            if (!$area2) {
                $result[$areaName][] = [
                    'nombre' => $areaName,
                    'diferencia' => max(0, $totalN),
                    'totalN' => $totalN,
                    'cantidadTotal' => $cantidadTotal,
                    'porcentaje' => $cantidadTotal > 0 ? ($totalN / $cantidadTotal) * 100 : 0,
                    'FechaComienzo' => null,
                    'FechaTermina' => null
                ];
            } else {
                $result[$areaName][] = [
                    'nombre' => $areaName,
                    'diferencia' => max(0, $totalN - (int)$area2->TotalPartidas),
                    'totalN' => $totalN,
                    'cantidadTotal' => $cantidadTotal,
                    'porcentaje' => $area2->porcentaje,
                    'FechaComienzo' => $area2->FechaComienzo,
                    'FechaTermina' => $area2->FechaFinalizacion
                ];
            }
        }

        return response()->json(['estaciones' => $result]);
        */
            /*
            $idFabricacion = $request->input('id');
            $tipoOF = $request->input('tipo'); 
            $OrdenFabricacion=OrdenFabricacion::where('OrdenFabricacion', $idFabricacion)->first();
            $TotalCompletadosCortes=0;
            $TotalOrdenFabricacion=0;
            if(!($OrdenFabricacion=="" OR $OrdenFabricacion==null)){
                $TotalCompletadosCortes=$OrdenFabricacion->PartidasOF()->where('TipoPartida','N')->whereNotNull('FechaFinalizacion')->get()->SUM('cantidad_partida')-
                                    $OrdenFabricacion->PartidasOF()->where('TipoPartida','N')->whereNull('FechaFinalizacion')->get()->SUM('cantidad_partida');
            }

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
            }*/
    }
    //detalles OF
    public function DetallesOF(Request $request){
        $idFabricacion = $request->input('id');
        $OrdenFabricacion = $request->input('id');
        $OrdenFabricacion = Ordenfabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first();
        $RequiereCorte = $OrdenFabricacion->Corte;//0 es igual a si, 1 es a no
        $PartidasOF = $OrdenFabricacion->PartidasOF->first();
        $Estaciones=[];
        $Areas =[];
        if($OrdenFabricacion == "" || $PartidasOF==""){
            return response()->json([
                'Areas' => "",
                'partidasAreas' => 0,
                'progreso' => 0,
                "Estatus" => "Error",
                "Message" => "La orden de Fabricación no existe!"
            ]);
        }
        //Estatus
            $estatus = $OrdenFabricacion->Cerrada;
            if($estatus == 1){$estatus="Abierta";}
            else{$estatus="Cerrada";}
        //Linea a la que pertenece, 18 es el Area de Clasificacion
            $Linea = $PartidasOF->Areas()->where('Areas_id',$this->AreaClasificacion)->first();
            if($Linea == ""){
                $Areas = '2,3';
                $Area = $array = explode(",", $Areas);
            }else{
                $Linea = Linea::find($Linea['pivot']->Linea_id);
                $Areas = $Linea->AreasPosibles;
                if($Areas == ""){
                    return response()->json([
                        'Areas' => "",
                        "TiempoDuracion" => 0,
                        'partidasAreas' => "",
                        "TiempoProductivo" => 0,
                        'progreso' => 0,
                        "TiempoTotal" => 0,
                        "TiempoMuerto"=> 0,
                        "Estaciones"=>$Estaciones,
                        "Estatus" => $estatus,
                    ]);
                }
                $Area = $array = explode(",", $Areas);
            }
            $Progreso=0;
            $FechaUltima = "";
            if (in_array($this->AreaArmado, $array)) {
                if($OrdenFabricacion->Escaner == 1){
                    $Progreso = $PartidasOF->Areas()->where('Areas_id',$this->AreaArmado)->where('TipoPartida','N')->get()->whereNotNull('pivot.Fechatermina')->SUM('pivot.Cantidad')
                                - $PartidasOF->Areas()->where('Areas_id',$this->AreaArmado)->where('TipoPartida','R')->get()->whereNotNull('pivot.Fechatermina')->SUM('pivot.Cantidad');
                }else{
                    $Progreso = $PartidasOF->Areas()->where('Areas_id',$this->AreaArmado)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad')
                                -$PartidasOF->Areas()->where('Areas_id',$this->AreaArmado)->where('TipoPartida','R')->get()->SUM('pivot.Cantidad');
                }
                $FechaUltima = $PartidasOF->Areas()->whereNotNull('FechaTermina')->where('Areas_id',$this->AreaArmado)->orderBy('FechaTermina', 'desc')->first();
            }else if(in_array($this->AreaEmpaque, $array)){
                $Progreso = $PartidasOF->Areas()->where('Areas_id',$this->AreaEmpaque)->get()->SUM('pivot.Cantidad');
                $FechaUltima = $PartidasOF->Areas()->whereNotNull('FechaTermina')->where('Areas_id',$this->AreaEmpaque)->orderBy('FechaTermina', 'desc')->first();
            }
        //Progreso
            $Progreso = ($Progreso/$OrdenFabricacion->CantidadTotal)*100;
            $Progreso = (fmod($Progreso, 1) == 0) ?$Progreso:number_format(($Progreso),2);
        //Tiempos Duracion, Tiempo Total, Tiempo Productivo, Tiempo Mu3rto
            $TiempoDuracion=0;
            $TiempoPromedioSeg=0;
            $FechaPrimera = $OrdenFabricacion->created_at;
            if($Progreso >= 100){
                if($FechaUltima!=""){
                    $TiempoDuracion = $FechaPrimera->diff($FechaUltima['pivot']->FechaTermina);
                    $TiempoPromedioSeg = $this->Fechas(($FechaPrimera->diffInSeconds($FechaUltima['pivot']->FechaTermina))/$OrdenFabricacion->CantidadTotal);
                }
            }
            $TiempoTotal=0;
            $TiempoProductivo=0;
            $TiempoMuerto=0;
            if($OrdenFabricacion->Escaner == 1){
                //Tiempo Productivo
                    $PartidasTiempoProductivo = $PartidasOF->Areas()->whereNotNull('FechaComienzo')->whereNotNull('FechaTermina') 
                                    ->get();
                    foreach($PartidasTiempoProductivo as $PTT){
                        if($PTT['pivot']['Areas_id']!= $this->AreaPulido){
                            $FechaPrimera=Carbon::parse($PTT['pivot']->FechaComienzo);
                            $FechaTermina=Carbon::parse($PTT['pivot']->FechaTermina);
                            $TiempoProductivo+=$FechaPrimera->diffInSeconds($FechaTermina);
                        }
                    }
                    $TiempoProductivoEstacionAreas = $PartidasOF->Areas()->where('Areas_id',$this->AreaPulido)->get()->GroupBy('pivot.NumeroBloque');
                    foreach($TiempoProductivoEstacionAreas as $NumE){
                        $MenorFecha = $NumE->min(fn($item) => $item->pivot->FechaComienzo);
                        $MayorFecha = $NumE->max(fn($item) => $item->pivot->FechaTermina);
                        if($MayorFecha != "" AND $MenorFecha!=""){
                            $FechaPrimera=Carbon::parse($MenorFecha);
                            $FechaTermina=Carbon::parse($MayorFecha);
                            $TiempoProductivo+=$FechaPrimera->diffInSeconds($FechaTermina);
                        }
                    }
                    $TiempoProductivoS=$TiempoProductivo;
                    if($TiempoProductivo!=0){
                        $horas = floor($TiempoProductivo / 3600);
                        $minutos = floor(($TiempoProductivo % 3600) / 60);
                        $segundos = $TiempoProductivo % 60;
                        if($horas!=0){$TiempoProductivo = sprintf("%02d Horas %02d Minutos %02d Segundos", $horas, $minutos, $segundos);}
                        elseif($minutos!=0){$TiempoProductivo = sprintf("%02d Minutos %02d Segundos", $minutos, $segundos);}
                        elseif($segundos!=0){$TiempoProductivo = sprintf("%02d Segundos",$segundos);}
                        else{$TiempoProductivo = 0;}
                    }
                //END Tiempo Productivo
                //Tiempo Total
                    $TiempoTotalS=0;
                    $FechaPrimera = $OrdenFabricacion->created_at;
                    $NumEtiquetas = $PartidasOF->Areas()->where('Areas_id',$this->AreaClasificacion)->get()->SUM('pivot.Cantidad');
                    for($i=0;$i<$NumEtiquetas;$i++){
                        $MayorFecha=$PartidasOF->Areas()->whereNotNull('FechaTermina')->OrderBy('FechaTermina', 'desc')->get()->where('pivot.NumeroEtiqueta',$i+1)->first();
                        if($MayorFecha != ""){
                            $FechaPrimera=Carbon::parse($FechaPrimera);
                            $FechaTermina=Carbon::parse($MayorFecha['pivot']->FechaTermina);
                            $TiempoTotal+=$FechaPrimera->diffInSeconds($FechaTermina);
                        }
                    }
                    $TiempoTotalS=$TiempoTotal;
                    if($TiempoTotal !=0){
                        $horas = floor($TiempoTotal / 3600);
                        $minutos = floor(($TiempoTotal % 3600) / 60);
                        $segundos = $TiempoTotal % 60;
                        if($horas!=0){$TiempoTotal = sprintf("%02d Horas %02d Minutos %02d Segundos", $horas, $minutos, $segundos);}
                        elseif($minutos!=0){$TiempoTotal = sprintf("%02d Minutos %02d Segundos", $minutos, $segundos);}
                        elseif($segundos!=0){$TiempoTotal = sprintf("%02d Segundos",$segundos);}
                        else{$TiempoTotal = 0;}
                    }
                //END Tiempo Total
                //Tiempo Muerto
                    $TiempoMuerto=$TiempoTotalS-$TiempoProductivoS;
                    if($TiempoMuerto >0){
                        $horas = floor($TiempoMuerto / 3600);
                        $minutos = floor(($TiempoMuerto % 3600) / 60);
                        $segundos = $TiempoMuerto % 60;
                        if($horas!=0){$TiempoMuerto = sprintf("%02d Horas %02d Minutos %02d Segundos", $horas, $minutos, $segundos);}
                        elseif($minutos!=0){$TiempoMuerto = sprintf("%02d Minutos %02d Segundos", $minutos, $segundos);}
                        elseif($segundos!=0){$TiempoMuerto = sprintf("%02d Segundos",$segundos);}
                        else{$TiempoMuerto = 0;}
                    }
                //END Tiempo Muerto 
                //Tiempo Promedio
                    //Hora preomedio

                    //Duracion promedio

                //END Tiempo Promedio
            }else{
                if($OrdenFabricacion->Corte == 1){
                    $FechaPrimerRegistro = $PartidasOF->Areas()->where('Areas_id','!=',18)->first();
                }else{
                    $FechaPrimerRegistro = $PartidasOF->Areas()->where('Areas_id','!=',18)->where('Areas_id','!=',2)->first();
                }
                if($FechaPrimerRegistro != ""){
                    $FechaPrimera=Carbon::parse($FechaPrimera);
                    $FechaTermina=Carbon::parse($FechaPrimerRegistro['pivot']->FechaComienzo);
                    $TiempoTotal+=$FechaPrimera->diffInSeconds($FechaTermina);
                }
            }
            $Estaciones=[];
            //Tiempo Productivo por estacion
                $TiempoTotalEstacion=0;
                $TiempoProductivoEstacion=0;
                if($OrdenFabricacion->Escaner == 1){
                    foreach($Area as $index => $AreaPosible){
                        $TiempoEstacionSegundos = 0;
                        if($AreaPosible != $this->AreaPulido){
                            //Tipo de Trabajo
                                $Retrabajo=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->where('TipoPartida','R')->get()->SUM('pivot.Cantidad');
                                $Normales=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad');
                            //Porcentaje Actual por Area
                                $AP=$AreaPosible;
                                $PorcentajeActual=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->whereNotNull('FechaTermina')->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')
                                                    -$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->whereNull('FechaTermina')->where('TipoPartida','R')->get()->SUM('pivot.Cantidad');
                                $PorcentajeActual = ($PorcentajeActual/$OrdenFabricacion->CantidadTotal)*100;
                                $PorcentajeActual = (fmod($PorcentajeActual, 1) == 0) ?$PorcentajeActual:number_format($PorcentajeActual,2);
                            //Tiempo Total
                                $TiempoTotalEstacion=0;
                                $MayorFecha=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->OrderBy('FechaTermina', 'desc')->first();
                                $MenorFecha=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->OrderBy('FechaComienzo', 'asc')->first();
                                if($MayorFecha != "" AND $MenorFecha!=""){
                                        $FechaPrimera=Carbon::parse($MenorFecha['pivot']->FechaComienzo);
                                        $FechaTermina=Carbon::parse($MayorFecha['pivot']->FechaTermina);
                                        $TiempoTotalEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                }
                                /*$NumEtiquetas = $PartidasOF->Areas()->where('Areas_id',$this->AreaClasificacion)->get()->SUM('pivot.Cantidad');
                                $TiempoTotalEstacion=0;
                                for($i=0;$i<$NumEtiquetas;$i++){
                                    $MayorFecha=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->OrderBy('FechaTermina', 'desc')->get()->where('pivot.NumeroEtiqueta',$i+1)->first();
                                    $MenorFecha=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->OrderBy('FechaComienzo', 'asc')->get()->where('pivot.NumeroEtiqueta',$i+1)->first();
                                    if($MayorFecha != "" AND $MenorFecha!=""){
                                        $FechaPrimera=Carbon::parse($MenorFecha['pivot']->FechaComienzo);
                                        $FechaTermina=Carbon::parse($MayorFecha['pivot']->FechaTermina);
                                        $TiempoTotalEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                    }
                                }*/
                                $TiempoEstacionSegundos = $TiempoTotalEstacion;
                                if($TiempoTotalEstacion >0){
                                    $TiempoTotalEstacion=$this->Fechas($TiempoTotalEstacion);
                                }
                            //Tiempo Productivo por Estacion
                                $TiempoProductivoEstacionAreas = $PartidasOF->Areas()->whereNotNull('FechaTermina')->where('Areas_id',$AreaPosible)->get();
                                $TiempoProductivoEstacion=0;
                                foreach($TiempoProductivoEstacionAreas as $TPEA){
                                    $FechaPrimera=Carbon::parse($TPEA['pivot']->FechaComienzo);
                                    $FechaTermina=Carbon::parse($TPEA['pivot']->FechaTermina);
                                    $TiempoProductivoEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                }
                                if($TiempoProductivoEstacion>0){
                                    $TiempoProductivoEstacion=$this->Fechas($TiempoProductivoEstacion);
                                }
                                $TiempoOrdenes=$TiempoTotalEstacion;
                        }else{
                            //Tipo de Trabajo
                                $Retrabajo=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->where('TipoPartida','R')->get()->SUM('pivot.Cantidad');
                                $Normales=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad');
                            //Porcentaje Actual por Area
                                $AP=$AreaPosible;
                                $PorcentajeActual=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->whereNotNull('FechaTermina')->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')
                                                    -$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->whereNull('FechaTermina')->where('TipoPartida','R')->get()->SUM('pivot.Cantidad');
                                $PorcentajeActual =($PorcentajeActual/$OrdenFabricacion->CantidadTotal)*100;
                                $PorcentajeActual = (fmod($PorcentajeActual, 1) == 0) ?$PorcentajeActual:number_format($PorcentajeActual,2);
                            //Tiempo Total
                                $TiempoTotalEstacion=0;
                                $MayorFecha=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->OrderBy('FechaTermina', 'desc')->first();
                                $MenorFecha=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->OrderBy('FechaComienzo', 'asc')->first();
                                if($MayorFecha != "" AND $MenorFecha!=""){
                                        $FechaPrimera=Carbon::parse($MenorFecha['pivot']->FechaComienzo);
                                        $FechaTermina=Carbon::parse($MayorFecha['pivot']->FechaTermina);
                                        $TiempoTotalEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                }
                                /*$NumEtiquetas = $PartidasOF->Areas()->where('Areas_id',$this->AreaClasificacion)->get()->SUM('pivot.Cantidad');
                                $TiempoTotalEstacion=0;
                                for($i=0;$i<$NumEtiquetas;$i++){
                                    $MayorFecha=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->OrderBy('FechaTermina', 'desc')->get()->where('pivot.NumeroEtiqueta',$i+1)->first();
                                    $MenorFecha=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->OrderBy('FechaComienzo', 'asc')->get()->where('pivot.NumeroEtiqueta',$i+1)->first();
                                    if($MayorFecha != "" AND $MenorFecha!=""){
                                        $FechaPrimera=Carbon::parse($MenorFecha['pivot']->FechaComienzo);
                                        $FechaTermina=Carbon::parse($MayorFecha['pivot']->FechaTermina);
                                        $TiempoTotalEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                    }
                                }*/
                                $TiempoEstacionSegundos = $TiempoTotalEstacion;
                                if($TiempoTotalEstacion >0){

                                    $TiempoTotalEstacion=$this->Fechas($TiempoTotalEstacion);
                                }
                            //Tiempo Productivo por Estacion
                                $TiempoProductivoEstacionAreas = $PartidasOF->Areas()->where('Areas_id',$this->AreaPulido)->get()->GroupBy('pivot.NumeroBloque');
                                $TiempoProductivoEstacion=0;
                                foreach($TiempoProductivoEstacionAreas as $NumE){
                                    $MenorFecha = $NumE->min(fn($item) => $item->pivot->FechaComienzo);
                                    $MayorFecha = $NumE->max(fn($item) => $item->pivot->FechaTermina);
                                    if($MayorFecha != "" AND $MenorFecha!=""){
                                        $FechaPrimera=Carbon::parse($MenorFecha);
                                        $FechaTermina=Carbon::parse($MayorFecha);
                                        $TiempoProductivoEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                    }
                                }
                                if($TiempoProductivoEstacion>0){
                                    $TiempoProductivoEstacion=$this->Fechas($TiempoProductivoEstacion);
                                }
                                $TiempoOrdenes=$TiempoTotalEstacion;
                        }
                        $AreaDatos=Areas::find($AreaPosible);
                        $Estaciones[$index] = ['PorcentajeActual' => $PorcentajeActual, 'Retrabajo' => $Retrabajo, 'Normales' => $Normales, 'TiempoOrdenes' => $TiempoOrdenes, 'AP' => $AP
                                            ,'NombreArea'=>$AreaDatos->nombre,"TiempoProductivoEstacion"=>$TiempoProductivoEstacion, "TiempoEstacionSegundos"=>$TiempoEstacionSegundos ];
                    }
                }else{
                    foreach($Area as $index => $AreaPosible){
                        $TiempoEstacionSegundos = 0;
                        if($AreaPosible != $this->AreaPulido){
                            //Tipo de Trabajo
                                $Retrabajo=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->where('TipoPartida','R')->get()->SUM('pivot.Cantidad');
                                $Normales=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad');
                            //Porcentaje Actual por Area
                                $AP=$AreaPosible;
                                if($AreaPosible == 2 || $AreaPosible == 3 || $AreaPosible == 17){
                                    $PorcentajeActual=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->whereNotNull('FechaTermina')->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')
                                        -$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->whereNull('FechaTermina')->where('TipoPartida','R')->get()->SUM('pivot.Cantidad');
                                }else{
                                    $PorcentajeActual=$PartidasOF->Areas()->where('Areas_id', $AreaPosible)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                        ($PartidasOF->Areas()->where('Areas_id', $AreaPosible)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                        - $PartidasOF->Areas()->where('Areas_id', $AreaPosible)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                                }
                                $PorcentajeActual = ($PorcentajeActual/$OrdenFabricacion->CantidadTotal)*100;
                                $PorcentajeActual = (fmod($PorcentajeActual, 1) == 0) ?$PorcentajeActual:number_format($PorcentajeActual,2);
                            //Tiempo Total
                                $TiempoTotalEstacion=0;
                                $MayorFecha=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->whereNotNull('FechaTermina')->OrderBy('FechaTermina', 'desc')->first();
                                $MenorFecha=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->whereNotNull('FechaComienzo')->OrderBy('FechaComienzo', 'asc')->first();
                                if($MayorFecha != "" AND $MenorFecha!=""){
                                        $FechaPrimera=Carbon::parse($MenorFecha['pivot']->FechaComienzo);
                                        $FechaTermina=Carbon::parse($MayorFecha['pivot']->FechaTermina);
                                        $TiempoTotalEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                }
                                $TiempoEstacionSegundos = $TiempoTotalEstacion;
                                if($TiempoTotalEstacion >0){
                                    $TiempoTotalEstacion=$this->Fechas($TiempoTotalEstacion);
                                }
                            //Tiempo Productivo por Estacion
                                $TiempoProductivoEstacion=0;
                                $TiempoProductivoEstacions=0;
                                if($AreaPosible == 2 || $AreaPosible == 3){
                                    $TiempoProductivoEstacionAreas = $PartidasOF->Areas()->whereNotNull('FechaTermina')->where('Areas_id',$AreaPosible)->get();
                                    foreach($TiempoProductivoEstacionAreas as $TPEA){
                                        $FechaPrimera=Carbon::parse($TPEA['pivot']->FechaComienzo);
                                        $FechaTermina=Carbon::parse($TPEA['pivot']->FechaTermina);
                                        $TiempoProductivoEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                    }
                                }else{
                                    $TiempoProductivoEstacionAreasA = $PartidasOF->Areas()->where('TipoPartida','!=','F')->where('Areas_id',$AreaPosible)->get();
                                    $TiempoProductivoEstacionAreasF= $PartidasOF->Areas()->where('TipoPartida','=','F')->where('Areas_id',$AreaPosible)->get();
                                    foreach($TiempoProductivoEstacionAreasA as $keyA => $TPEAA){
                                        foreach($TiempoProductivoEstacionAreasF as $keyF => $TPEAF){
                                            $CantidadA=$TPEAA['pivot']->Cantidad;
                                            $CantidadF=$TPEAF['pivot']->Cantidad;
                                            if($CantidadA == $CantidadF){
                                                $FechaPrimera=Carbon::parse($TPEAA['pivot']->FechaComienzo);
                                                $FechaTermina=Carbon::parse($TPEAF['pivot']->FechaTermina);
                                                $TiempoProductivoEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                                unset($TiempoProductivoEstacionAreasF[$keyF]);
                                                break;
                                            }elseif($CantidadA > $CantidadF){
                                                $FechaPrimera=Carbon::parse($TPEAA['pivot']->FechaComienzo);
                                                $FechaTermina=Carbon::parse($TPEAF['pivot']->FechaTermina);
                                                $TiempoProductivoEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                                $TPEAA['pivot']->Cantidad -= $CantidadF;
                                                unset($TiempoProductivoEstacionAreasF[$keyF]);
                                            }else{
                                                $FechaPrimera=Carbon::parse($TPEAA['pivot']->FechaComienzo);
                                                $FechaTermina=Carbon::parse($TPEAF['pivot']->FechaTermina);
                                                $TiempoProductivoEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                                $TPEAF['pivot']->Cantidad -= $CantidadA;
                                                unset($TiempoProductivoEstacionAreasA[$keyA]);
                                                break;
                                            }
                                        }
                                    }
                                }
                                if($TiempoProductivoEstacion>0){
                                    $TiempoProductivoEstacions=$TiempoProductivoEstacion;
                                    $TiempoProductivoEstacion=$this->Fechas($TiempoProductivoEstacion);
                                }
                                $TiempoOrdenes=$TiempoTotalEstacion;
                        }else{
                            //Tipo de Trabajo
                                $Retrabajo=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->where('TipoPartida','R')->get()->SUM('pivot.Cantidad');
                                $Normales=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad');
                            //Porcentaje Actual por Area
                                $AP=$AreaPosible;
                                $PorcentajeActual=$PorcentajeActual=$PartidasOF->Areas()->where('Areas_id', $AreaPosible)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                                ($PartidasOF->Areas()->where('Areas_id', $AreaPosible)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                                - $PartidasOF->Areas()->where('Areas_id', $AreaPosible)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                                $PorcentajeActual =($PorcentajeActual/$OrdenFabricacion->CantidadTotal)*100;
                                $PorcentajeActual = (fmod($PorcentajeActual, 1) == 0) ?$PorcentajeActual:number_format($PorcentajeActual,2);
                            //Tiempo Total
                                $TiempoTotalEstacion=0;
                                $MayorFecha=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->whereNotNull('FechaTermina')->OrderBy('FechaTermina', 'desc')->first();
                                $MenorFecha=$PartidasOF->Areas()->where('Areas_id',$AreaPosible)->whereNotNull('FechaComienzo')->OrderBy('FechaComienzo', 'asc')->first();
                                if($MayorFecha != "" AND $MenorFecha!=""){
                                        $FechaPrimera=Carbon::parse($MenorFecha['pivot']->FechaComienzo);
                                        $FechaTermina=Carbon::parse($MayorFecha['pivot']->FechaTermina);
                                        $TiempoTotalEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                }
                                $TiempoEstacionSegundos = $TiempoTotalEstacion;
                                if($TiempoTotalEstacion >0){
                                    $TiempoTotalEstacion=$this->Fechas($TiempoTotalEstacion);
                                }
                            //Tiempo Productivo por Estacion
                                $TiempoProductivoEstacion=0;
                                $TiempoProductivoEstacions=0;
                                $TiempoProductivoEstacionAreasA = $PartidasOF->Areas()->where('TipoPartida','!=','F')->where('Areas_id',$AreaPosible)->get()->GroupBy('pivot.NumeroBloque');
                                $TiempoProductivoEstacionAreasF= $PartidasOF->Areas()->where('TipoPartida','=','F')->where('Areas_id',$AreaPosible)->get()->GroupBy('pivot.NumeroBloque');
                                    foreach($TiempoProductivoEstacionAreasA as $keyA => $TPEAA){
                                        $PrimerTPEAA = $TPEAA->first();
                                        foreach($TiempoProductivoEstacionAreasF as $keyF => $TPEAF){
                                            $PrimerTPEAF = $TPEAF->first();
                                            $CantidadA=$PrimerTPEAA['pivot']->Cantidad;
                                            $CantidadF=$PrimerTPEAF['pivot']->Cantidad;
                                            if($CantidadA == $CantidadF){
                                                $FechaPrimera=Carbon::parse($PrimerTPEAA['pivot']->FechaComienzo);
                                                $FechaTermina=Carbon::parse($PrimerTPEAF['pivot']->FechaTermina);
                                                $TiempoProductivoEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                                unset($TiempoProductivoEstacionAreasF[$keyF]);
                                                break;
                                            }elseif($CantidadA > $CantidadF){
                                                $FechaPrimera=Carbon::parse($PrimerTPEAA['pivot']->FechaComienzo);
                                                $FechaTermina=Carbon::parse($PrimerTPEAF['pivot']->FechaTermina);
                                                $TiempoProductivoEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                                $TPEAA['pivot']->Cantidad -= $CantidadF;
                                                unset($TiempoProductivoEstacionAreasF[$keyF]);
                                            }else{
                                                $FechaPrimera=Carbon::parse($PrimerTPEAA['pivot']->FechaComienzo);
                                                $FechaTermina=Carbon::parse($PrimerTPEAF['pivot']->FechaTermina);
                                                $TiempoProductivoEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                                $PrimerTPEAF['pivot']->Cantidad -= $CantidadA;
                                                unset($TiempoProductivoEstacionAreasA[$keyA]);
                                                break;
                                            }
                                        }
                                    }
                                    $TiempoProductivoEstacions = $TiempoProductivoEstacion;
                                    if($TiempoProductivoEstacion>0){
                                        $TiempoProductivoEstacion=$this->Fechas($TiempoProductivoEstacion);
                                    }
                                    $TiempoOrdenes=$TiempoTotalEstacion;
                        }
                        $AreaDatos=Areas::find($AreaPosible);
                        $Estaciones[$index] = ['PorcentajeActual' => $PorcentajeActual, 'Retrabajo' => $Retrabajo, 'Normales' => $Normales, 'TiempoOrdenes' => $TiempoOrdenes, 'AP' => $AP
                                            ,'NombreArea'=>$AreaDatos->nombre,"TiempoProductivoEstacion"=>$TiempoProductivoEstacion, "TiempoEstacionSegundos"=>$TiempoEstacionSegundos ];
                        $TiempoTotal+=$TiempoProductivoEstacions;
                        $TiempoProductivo+=$TiempoProductivoEstacions;
                    }
                }
        // Asegúra que la respuesta siempre incluya una propiedad, incluso si no hay datos
        if($estatus =="Abierta"){
            $TiempoTotal = 0;
            $TiempoProductivo = 0;
            $TiempoMuerto = 0;
        }
        if($OrdenFabricacion->Escaner == 0){
            $TiempoMuerto = $this->ConversionSegDias($TiempoTotal-$TiempoProductivo);
            $TiempoTotal = $this->ConversionSegDias($TiempoTotal);
            $TiempoProductivo = $this->ConversionSegDias($TiempoProductivo);
        }
        //return$Comentario = Comentarios::all();
        //return $OrdenFabricacion->id;
        //return$comentarios = Comentarios::where('Comentario', 'like', '%finalizó%')->where('OrdenFabricacion_id',$OrdenFabricacion->id)->get();
        return response()->json([
            'progreso' => round($Progreso,2),
            "Estatus" => $estatus,
            "Tiempototal" => $TiempoTotal,
            "TiempoDuracion" => $TiempoDuracion,
            "TiempoProductivo" => $TiempoProductivo,
            "TiempoTotal" => $TiempoTotal,
            "TiempoMuerto"=> $TiempoMuerto,
            "Estaciones"=>$Estaciones,
            "TiempoPromedioSeg"=>$TiempoPromedioSeg,
            "RequiereCorte"=>$RequiereCorte,
        ]);
    }
    function Fechas($TiempoTotalEstacion){
        $horas = floor($TiempoTotalEstacion / 3600);
        $minutos = floor(($TiempoTotalEstacion % 3600) / 60);
        $segundos = $TiempoTotalEstacion % 60;
        if($horas!=0){$TiempoTotalEstacion = sprintf("%02d Horas %02d Minutos %02d Segundos", $horas, $minutos, $segundos);}
        elseif($minutos!=0){$TiempoTotalEstacion = sprintf("%02d Minutos %02d Segundos", $minutos, $segundos);}
        elseif($segundos!=0){$TiempoTotalEstacion = sprintf("%02d Segundos",$segundos);}
        else{$TiempoTotalEstacion = 0;}
        return $TiempoTotalEstacion;
    }
    function ConversionSegDias($Tiempo){
        //Tiempo tiene que ser en segundos
        if($Tiempo>0){
            $horas = floor($Tiempo / 3600);
            $minutos = floor(($Tiempo % 3600) / 60);
            $segundos = $Tiempo % 60;
            if($horas!=0){$Tiempo = sprintf("%02d Horas %02d Minutos %02d Segundos", $horas, $minutos, $segundos);}
            elseif($minutos!=0){$Tiempo = sprintf("%02d Minutos %02d Segundos", $minutos, $segundos);}
            elseif($segundos!=0){$Tiempo = sprintf("%02d Segundos",$segundos);}
            else{$Tiempo = 0;}
        }
        return $Tiempo;
    }
    public function tiempoS(Request $request){
        $idFabricacion = $request->input('id');
        $duracionFinalCortes =  DB::table('ordenfabricacion')
        ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
        ->select(
            'ordenfabricacion.OrdenFabricacion',
            'partidasof.FechaComienzo',
            DB::raw('GROUP_CONCAT(partidasof.OrdenFabricacion_id) as ids'),
            DB::raw('MIN(partidasof.FechaComienzo) as FechaComienzo'),
            DB::raw('MAX(partidasof.FechaFinalizacion) as FechaTermina'),
            DB::raw('SUM(TIMESTAMPDIFF(MINUTE, partidasof.FechaComienzo, partidasof.FechaFinalizacion)) as TotalMinutos')
        )
        ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
        ->groupBy('ordenfabricacion.OrdenFabricacion', 'partidasof.FechaComienzo', 'partidasof.FechaFinalizacion')
        ->get();
        $duracionFinal9 = DB::table('ordenfabricacion')
        ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
        ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
        ->where('partidasof_areas.Areas_id', 17)
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
        
        $fechaComienzoTotal = $duracionFinalCortes->pluck('FechaComienzo')->min();
        $fechaTerminaTotal = $duracionFinal9->pluck('FechaTermina')->max();

        $fechaComienzoCarbon = Carbon::parse($fechaComienzoTotal);
        $fechaTerminaCarbon = Carbon::parse($fechaTerminaTotal);
        $diferencia = $fechaComienzoCarbon->diff($fechaTerminaCarbon);

        $duracion = [];

        if ($diferencia->d > 0) $duracion[] = "{$diferencia->d} días";
        if ($diferencia->h > 0) $duracion[] = "{$diferencia->h} horas";
        if ($diferencia->i > 0) $duracion[] = "{$diferencia->i} minutos";

        // Si no hay diferencia de tiempo, mostrar "0 minutos"
        $DuracionTotal = !empty($duracion) ? implode(", ", $duracion) : "0 minutos";

        $tiempototal = [
            'FechaComienzo' => $fechaComienzoCarbon->format('Y-m-d H:i'),
            'FechaTermina' => $fechaTerminaCarbon->format('Y-m-d H:i'),
            'DuracionTotal' => $DuracionTotal,
        ];

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
            DB::raw('SUM(TIMESTAMPDIFF(MINUTE, partidasof.FechaComienzo, partidasof.FechaFinalizacion)) as TotalMinutos'),
            DB::raw('SUM(TIMESTAMPDIFF(SECOND, partidasof.FechaComienzo, partidasof.FechaFinalizacion)) as TotalSegundos')
        )
        ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
        ->groupBy('ordenfabricacion.OrdenFabricacion', 'partidasof.FechaComienzo', 'partidasof.FechaFinalizacion')
        ->get()
        ->map(function ($item) {
            if ($item->TotalSegundos == 0) {
                $item->Duracion = "0 minutos";
            } else {
                $FechaComienzo = Carbon::parse($item->FechaComienzo);
                $FechaFinalizacion = Carbon::parse($item->FechaFinalizacion);
                
                $diffDias = floor($FechaComienzo->diffInHours($FechaFinalizacion) / 24);
                $diffHoras = $FechaComienzo->diffInHours($FechaFinalizacion) % 24;
                $diffMinutos = $FechaComienzo->diffInMinutes($FechaFinalizacion) % 60;
                $diffSegundos = $FechaComienzo->diffInSeconds($FechaFinalizacion) % 60;
    
                $duracion = [];
    
                if ($diffDias > 0) $duracion[] = "{$diffDias} días";
                if ($diffHoras > 0) $duracion[] = "{$diffHoras} horas";
                if ($diffMinutos > 0) $duracion[] = "{$diffMinutos} minutos";
                if ($diffSegundos > 0) $duracion[] = "{$diffSegundos} segundos";
    
                $item->Duracion = !empty($duracion) ? implode(", ", $duracion) : "0 minutos";
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
            DB::raw('MIN(partidasof_areas.FechaComienzo) as FechaComienzo'), // Primer FechaComienzo
            DB::raw('MAX(partidasof_areas.FechaTermina) as FechaTermina') // Último FechaTermina
        )
        ->where('ordenfabricacion.OrdenFabricacion', $idFabricacion)
        ->groupBy('ordenfabricacion.OrdenFabricacion', 'partidasof_areas.PartidasOf_id', 'partidasof_areas.Areas_id')
        ->get()
        ->map(function ($item) {
        // Si no hay FechaTermina, utilizamos FechaComienzo como valor de referencia
        $fechaTermina = $item->FechaTermina ? $item->FechaTermina : $item->FechaComienzo;

        // Cálculo de la diferencia en minutos y segundos
        $fechaInicio = new \Carbon\Carbon($item->FechaComienzo);
        $fechaFin = new \Carbon\Carbon($fechaTermina);
        
        // Calcular la duración en minutos y segundos
        $totalMinutos = $fechaInicio->diffInMinutes($fechaFin);
        $totalSegundos = $fechaInicio->diffInSeconds($fechaFin);

        // Guardar los valores calculados
        $item->TotalMinutos = $totalMinutos;
        $item->TotalSegundos = $totalSegundos;

        // Calcular la duración total en formato legible
        if ($totalSegundos == 0) {
            $item->DuracionTotal = "0 minutos";
        } else {
            $dias = floor($totalMinutos / (60 * 24));
            $horas = floor(($totalMinutos % (60 * 24)) / 60);
            $minutos = $totalMinutos % 60;
            $segundos = $totalSegundos % 60;

            $duracion = [];
            if ($dias > 0) $duracion[] = "{$dias} días";
            if ($horas > 0) $duracion[] = "{$horas} horas";
            if ($minutos > 0) $duracion[] = "{$minutos} minutos";
            if ($segundos > 0) $duracion[] = "{$segundos} segundos";

            $item->DuracionTotal = !empty($duracion) ? implode(", ", $duracion) : "0 minutos";
        }
        $item->ids = explode(',', $item->ids);
        return $item;
        });


            // Sumar los segundos de ambos conjuntos de datos
            $totalSegundosCortes = $tiemposcortes->pluck('TotalSegundos')->map(function($segundos) {
                return (int) $segundos; // Convertir los segundos a enteros
            })->sum();
            $totalSegundosAreas = $tiemposareas->pluck('TotalSegundos')->map(function($segundos) {
                return (int) $segundos; // Convertir los segundos a enteros
            })->sum();
            $totalSegundos = $totalSegundosCortes + $totalSegundosAreas;
            $duracionTotalSegundos = ($diferencia->d * 86400) + ($diferencia->h * 3600) + ($diferencia->i * 60); 

            $TiempoMuerto = $duracionTotalSegundos - $totalSegundos;
            
            // Convertir los segundos restantes en días, horas, minutos y segundos
            $diasMuertos = floor($TiempoMuerto / 86400);
            $horasMuertas = floor(($TiempoMuerto % 86400) / 3600);
            $minutosMuertos = floor(($TiempoMuerto % 3600) / 60);
            $segundosMuertos = $TiempoMuerto % 60;
            
            // Construir la duración omitiendo valores en 0
            $duracion = [];
            
            if ($diasMuertos > 0) $duracion[] = "{$diasMuertos} días";
            if ($horasMuertas > 0) $duracion[] = "{$horasMuertas} horas";
            if ($minutosMuertos > 0) $duracion[] = "{$minutosMuertos} minutos";
            if ($segundosMuertos > 0) $duracion[] = "{$segundosMuertos} segundos";
            
            // Si no hay tiempo muerto, mostrar "0 segundos"
            $TiempoMuertoFormato = !empty($duracion) ? implode(", ", $duracion) : "0 segundos";
            
            return response()->json([
                'tiemposcortes' => $tiemposcortes,
                'tiemposareas' => $tiemposareas,
                'tiempototal' => $tiempototal,
                'totalSegundos' => $totalSegundos . ' segundos',
                'TiempoMuertoFormato' => $TiempoMuertoFormato
            ]);
            
    } 
    public function EstatusOrdenesFabricacion($FechaInicio = null, $FechaFin = null,$Estatus = null){
        $user = Auth::user();
        if($user->hasPermission('Vista Estatus Orden Fabricación')){
            if(!isset($FechaInicio)){
                $FechaInicio = now()->subWeek()->toDateString();
            }
            if(!isset($FechaFin)){
                $FechaFin = now()->toDateString();
            }
        $BodyTable = "";
            if($Estatus=='Abiertas'){
                $OrdenFabricacionAbiertas = OrdenFabricacion::where('Cerrada','1')->whereBetween('FechaEntrega', [$FechaInicio, $FechaFin])->orderBy('OrdenFabricacion', 'asc')->get();
                foreach($OrdenFabricacionAbiertas as $key=>$OFA){
                    $Usuario = "SIN CORTE";
                    $Escaner = "Masivo";
                    $OrdenVenta = $OFA->OrdenVenta->OrdenVenta;
                    if($OFA->Escaner == 1){
                        $Escaner = "Uno a uno";
                    }
                    $Urgencia = "Urgente";
                    if($OFA->Urgencia == 'N'){
                        $Urgencia = "Normal";
                    }
                    $LLC = "Si";
                    if($OFA->LLC == 0){
                        $LLC = "No";
                    }
                    if($OFA->ResponsableUser_id){
                        $user= User::find($OFA->ResponsableUser_id);
                        $Usuario = $user->name." ".$user->apellido;
                    }
                    $PartidaOF = $OFA->PartidasOF()->first();
                    $UltimaEstacion = "Planeación";
                    if($PartidaOF != ""){
                        $PartidaOFAreas = $PartidaOF->Areas()->where('Areas_id','!=',$this->AreaClasificacion)->orderBy('Areas_id','desc')->get();
                        if($PartidaOFAreas->count() > 0 ){
                            $UltimaEstacion  = $PartidaOFAreas->first();
                            $UltimaEstacion = $UltimaEstacion['pivot']->Areas_id;
                            $UltimaEstacion = $this->AreaNombre($UltimaEstacion);
                        }
                    }
                    $BodyTable .= '<tr>
                                    <td class="text-center">'.($key+1).'</td>
                                    <td class="text-center">'. $OFA->OrdenFabricacion.'</td>
                                    <td class="text-center">'. $OrdenVenta .'</td>';
                                    if(Auth::user()->hasPermission("Vista Planeacion")){
                                        $BodyTable .= '<td><div class="badge badge-phoenix fs--2 badge-phoenix-info"><span class="fw-bold">'.$Usuario.'</span></div></td>';
                                    }
                    $BodyTable .= '<td class="text-center">'. $OFA->Articulo.'</td>
                                    <td class="text-center"> 
                                        <div class="MostrarMenos" id="collapseA'. $OFA->OrdenFabricacion.'">
                                            '.$OFA->Descripcion.'
                                        </div>
                                        <a  onclick="MostrarMas(\'collapseA'. $OFA->OrdenFabricacion.'\',this);" class="btn btn-sm btn-link">Ver más</a>
                                    </td>
                                    <td class="text-center">'. $OFA->CantidadTotal .'</td>
                                    <td class="text-center">'. $OFA->FechaEntrega .'</td>
                                    <td class="text-center">'. $OFA->FechaEntregaSAP .'</td>
                                    <td class="text-center">'.$Escaner.'</td>
                                    <td class="text-center">'.$Urgencia.'</td>
                                    <td class="text-center">'. $LLC.'</td>
                                    <td class="text-center">'.$UltimaEstacion.'</td>
                                    <td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Abierta</span></div></td>
                                </tr>';
                }
                 return response()->json([
                    'BodyTabla' => $BodyTable,
                    "status" => 'success',
                ]);
            }elseif($Estatus=='Cerradas'){
                $OrdenFabricacionCerradas = OrdenFabricacion::where('Cerrada','0')->whereBetween('FechaEntrega', [$FechaInicio, $FechaFin])->orderBy('OrdenFabricacion', 'asc')->get();
                foreach($OrdenFabricacionCerradas as $key => $OFC){
                    $OrdenVenta = $OFC->OrdenVenta->OrdenVenta;
                    $Usuario = "SIN CORTE";
                    $Escaner = "Masivo";
                    if($OFC->Escaner == 1){
                        $Escaner = "Uno a uno";
                    }
                    $Urgencia = "Urgente";
                    if($OFC->Urgencia == 'N'){
                        $Urgencia = "Normal";
                    }
                    $LLC = "Si";
                    if($OFC->LLC == 0){
                        $LLC = "No";
                    }
                    if($OFC->ResponsableUser_id){
                        $user= User::find($OFC->ResponsableUser_id);
                        $Usuario = $user->name." ".$user->apellido;
                    }
                    $PartidaOF = $OFC->PartidasOF()->first();
                    $UltimaEstacion = "Planeación";
                    if($PartidaOF != ""){
                        $PartidaOFAreas = $PartidaOF->Areas()->where('Areas_id','!=',$this->AreaClasificacion)->orderBy('Areas_id','desc')->get();
                        if($PartidaOFAreas->count() > 0 ){
                            $UltimaEstacion  = $PartidaOFAreas->first();
                            $UltimaEstacion = $UltimaEstacion['pivot']->Areas_id;
                            $UltimaEstacion = $this->AreaNombre($UltimaEstacion);
                        }
                    }
                    $BodyTable .= '<tr>
                                    <td class="text-center">'.($key+1).'</td>
                                    <td class="text-center">'. $OFC->OrdenFabricacion.'</td>
                                    <td class="text-center">'. $OrdenVenta.'</td>';
                                    if(Auth::user()->hasPermission("Vista Planeacion")){
                                        $BodyTable .= '<td><div class="badge badge-phoenix fs--2 badge-phoenix-info"><span class="fw-bold">'.$Usuario.'</span></div></td>';
                                    }
                    $BodyTable .= ' <td class="text-center">'. $OFC->Articulo .'</td>
                                    <td class="text-center"> 
                                        <div class="MostrarMenos" id="collapseC'. $OFC->OrdenFabricacion.'">
                                            '.$OFC->Descripcion.'
                                        </div>
                                        <a  onclick="MostrarMas(\'collapseC'. $OFC->OrdenFabricacion.'\',this);" class="btn btn-sm btn-link">Ver más</a>
                                    </td>
                                    <td class="text-center">'. $OFC->CantidadTotal .'</td>
                                    <td class="text-center">'. $OFC->FechaEntrega .'</td>
                                    <td class="text-center">'. $OFC->FechaEntregaSAP .'</td>
                                    <td class="text-center">'.$Escaner.'</td>
                                    <td class="text-center">'.$Urgencia.'</td>
                                    <td class="text-center">'. $LLC.'</td>
                                    <td class="text-center">'.$UltimaEstacion.'</td>
                                    <td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-primary"><span class="fw-bold">Cerrada</span></div></td>
                                </tr>';
                }
                return response()->json([
                    'BodyTabla' => $BodyTable,
                    "status" => 'success',
                ]);
            }else{
                $OrdenFabricacionAbiertas = OrdenFabricacion::where('Cerrada','1')->whereBetween('FechaEntrega', [$FechaInicio, $FechaFin])->orderBy('OrdenFabricacion', 'asc')->get();
                foreach($OrdenFabricacionAbiertas as $OFA){
                    $PartidaOF = $OFA->PartidasOF()->first();
                    $OrdenVenta = $OFA->OrdenVenta->OrdenVenta;
                    $UltimaEstacion = "Planeación";
                    if($PartidaOF != ""){
                        $PartidaOFAreas = $PartidaOF->Areas()->where('Areas_id','!=',$this->AreaClasificacion)->orderBy('Areas_id','desc')->get();
                        if($PartidaOFAreas->count() > 0 ){
                            $UltimaEstacion  = $PartidaOFAreas->first();
                            $UltimaEstacion = $UltimaEstacion['pivot']->Areas_id;
                            $UltimaEstacion = $this->AreaNombre($UltimaEstacion);
                        }
                    }
                    if($OFA->ResponsableUser_id){
                        $user= User::find($OFA->ResponsableUser_id);
                        $OFA['ResponsableUser'] = $user->name."  ".$user->apellido;
                    }else{
                        $OFA['ResponsableUser'] = null;
                    }
                    $OFA['UltimaEstacion'] = $UltimaEstacion;
                    $OFA['OrdenVenta'] = ($OrdenVenta == '00000')?"Sin Orden Venta":$OrdenVenta;
                }
                $OrdenFabricacionCerradas = OrdenFabricacion::where('Cerrada','0')->whereBetween('FechaEntrega', [$FechaInicio, $FechaFin])->orderBy('OrdenFabricacion', 'asc')->get();
                foreach($OrdenFabricacionCerradas as $OFC){
                    $PartidaOF = $OFC->PartidasOF()->first();
                    $OrdenVenta = $OFC->OrdenVenta->OrdenVenta;
                    $UltimaEstacion = "Planeación";
                    if($PartidaOF != ""){
                        $PartidaOFAreas = $PartidaOF->Areas()->where('Areas_id','!=',$this->AreaClasificacion)->orderBy('Areas_id','desc')->get();
                        if($PartidaOFAreas->count() > 0 ){
                            $UltimaEstacion  = $PartidaOFAreas->first();
                            $UltimaEstacion = $UltimaEstacion['pivot']->Areas_id;
                            $UltimaEstacion = $this->AreaNombre($UltimaEstacion);
                        }
                    }
                    if($OFC->ResponsableUser_id){
                        $user= User::find($OFC->ResponsableUser_id);
                        $OFC['ResponsableUser'] = $user->name."  ".$user->apellido;
                    }
                    else{$OFC['ResponsableUser'] = null;
                    }
                    $OFC['UltimaEstacion'] = $UltimaEstacion;
                    $OFC['OrdenVenta'] = ($OrdenVenta == '00000')?"Sin Orden Venta":$OrdenVenta;
                }
                return view('Busqueda.EstatusOrdenesFabricacion',compact('OrdenFabricacionAbiertas','OrdenFabricacionCerradas','FechaInicio','FechaFin'));
            }
        }else
        return redirect()->route('error.');
    }
    public function AreaNombre($IdArea){
        $Area = Areas::find($IdArea); 
        return $Area->nombre;
    }

}