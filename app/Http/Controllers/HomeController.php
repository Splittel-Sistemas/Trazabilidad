<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\PorcentajePlaneacion;
use App\Models\OrdenFabricacion;
use App\Models\Areas;
use App\Models\Partidasof_Areas;
use Illuminate\Support\Facades\Log;
use App\Models\Linea;

use function GuzzleHttp\json_decode;

class HomeController extends Controller
{ 
    private $AreaEspecialEmpaque=17;
    private $AreaEspecialCorte=2;
   // HomeController.php
    /*public function Home()
    {
        return view('Home');
    }*/
    public function index(){   
        $user = Auth::user();
        if ($user->hasPermission('Vista Dashboard')) {
            $user = Auth::user();
            if (!$user || !$user->active) {
                Auth::logout();
                return redirect()->route('login_view')->withErrors(['email' => 'Tu cuenta ha sido desactivada.']);
            }
            // Si el usuario está autenticado y su cuenta está activa
            //$OFDia = $this->Dia();
            //$OFDia = $OFDia->getData();
            $FechaHoy = date('Y-m-d');
            $FechaAyer = date('Y-m-d', strtotime('-1 day'));
            return view('Home', compact('FechaHoy','FechaAyer')); // O la vista que corresponda
        } else {
            return redirect()->route('index.operador');
        }
    }
    public function Dia(){
        //Día de hoy
        $FechaHoy = date('Y-m-d');
        $FechaAyer = date('Y-m-d', strtotime('-1 day'));
        $OFDia = OrdenFabricacion::where('FechaEntrega',$FechaHoy)->get();
        $OFAbierta = OrdenFabricacion::where('FechaEntrega',$FechaHoy)->where('Cerrada',1)->get();
        $OFCerrada = OrdenFabricacion::where('FechaEntrega',$FechaHoy)->where('Cerrada',0)->get();
        $OFAbiertaCant = $OFAbierta->count();
        $OFCerradaCant = $OFCerrada->count();
        $OFAbiertaAyer = OrdenFabricacion::where('FechaEntrega',$FechaAyer)->where('Cerrada',1)->get();
        $OFCerradaAyer = OrdenFabricacion::where('FechaEntrega',$FechaAyer)->where('Cerrada',0)->get();
        $OFAbiertaCantAyer = $OFAbiertaAyer->count();
        $OFCerradaCantAyer = $OFCerradaAyer->count();
        //Orden de Fabricacion
        if($OFAbiertaCantAyer == 0){
            $PorcentajeAvanceA = $OFAbiertaCant*100;
        }else{
            $PorcentajeAvanceA = (($OFAbiertaCant-$OFAbiertaCantAyer)/$OFAbiertaCantAyer)*100;
        }
        if($OFCerradaCantAyer == 0){
            $PorcentajeAvanceC = $OFCerradaCant*100;
        }else{
            $PorcentajeAvanceC = (($OFCerradaCant-$OFCerradaCantAyer)/$OFCerradaCantAyer)*100;
        }
        if($PorcentajeAvanceA>=0){
            $PorcentajeAvanceA = "+".$PorcentajeAvanceA; 
        }else{
            $PorcentajeAvanceA = $PorcentajeAvanceA; 
        }
        if($PorcentajeAvanceC>=0){
            $PorcentajeAvanceC = "+".$PorcentajeAvanceC; 
        }else{
            $PorcentajeAvanceC = $PorcentajeAvanceC; 
        }
        //Estaciones
        $Estaciones = Areas::where('id','!=',1)->where('id','!=',19)->where('id','!=',20)->where('id','!=',21)->get();
        $Programadas = 0;
        $Pendientes = 0;
        $EnProceso = 0;
        $Terminadas = 0;
        $PromedioPieza = 0;
        $PiezasAsignadasLinea = 0;
        $PiezasFaltanteLinea = 0;
        //Asignadas, Pendientes Línea
        foreach ($OFDia as $key1=>$OFD) {
            $OFDP = $OFD->partidasOF->first();
            //Asignadas, Pendientes Línea
            ($OFDP->Areas()->where('Areas_id', 18)->COUNT() > 0)?$PiezasAsignadasLinea += 1:$PiezasFaltanteLinea += 1;
            ($OFDP->Areas()->where('Areas_id', 18)->COUNT() > 0)?$OFDia[$key1]['LineasOriginal']=$OFDP->Areas()->first()->id: $OFDia[$key1]['LineasOriginal']=0;
        }
        foreach($Estaciones as $key => $Estac){
            //Programadas
            $Programadas = 0;
            $Pendientes = 0;
            $EnProceso = 0;
            $Terminadas = 0;
            foreach ($OFDia as $key => $OFD) {
                //Programadas
                if($OFD->LineasOriginal != 0){
                    $LineasOriginal = Linea::find($OFD->LineasOriginal);
                    $LineasOriginal = array_map('intval', explode(',', $LineasOriginal->AreasPosibles));
                    if($Estac->id == 2){
                        if($OFD->Corte != 0){
                            if (in_array($Estac->id, $LineasOriginal)) {
                                $Programadas+=1;
                            }
                        }
                    }else{
                        if (in_array($Estac->id, $LineasOriginal)) {
                            $Programadas+=1;
                        }
                    }
                }else{
                    if($Estac->id == 2){
                        if($OFD->Corte != 0){
                            $Programadas+=1;
                        }
                    }elseif($Estac->id == 3){
                        $Programadas+=1;
                    }
                }
                //Pendientes y terminadas
                if($Programadas>0){
                    $OFDP = $OFD->partidasOF->first();
                    if($OFDP->Areas()->where('Areas_id', $Estac->id)->COUNT() == 0){
                        if($Estac->id == 2){
                            if($OFD->Corte != 0){
                                $Pendientes += 1;
                            }
                        }else{
                            $Pendientes += 1;
                        }
                    }else{
                        if($this->NumeroCompletadas($OFD->OrdenFabricacion,$Estac->id) >= $OFD->CantidadTotal){
                            if($Estac->id == 2){
                                if($OFD->Corte == 0){
                                    $Terminadas += 1;   
                                }
                            }else{
                                $Terminadas += 1;
                            }
                        }else{
                            $EnProceso += 1;
                        }
                    }
                }
            }
            $Estac['PorcentajeTerminadas'] = round(($Terminadas== 0)?0:($Programadas * 100) / $Terminadas);
            $Estac['Programadas'] = $Programadas;
            $Estac['Pendientes'] = $Pendientes;
            $Estac['EnProceso'] = $EnProceso;
            $Estac['Terminadas'] = $Terminadas;
        }
        /*foreach($OFDia as $OFD){
                foreach( $OFD->PartidasOF as $OFDP){

                    if($Estac == 2 || $Estac == 3){

                    }else{

                    }
                    //Programadas
                        return$OFDP->Areas()->where('Areas_id',$Estac);
                    //$TotalCantidadProgramadas $OFDP->Areas()->where('Areas_id',$Estac)->get();
                }
            }
            //Programadas
                //return$OFDia;
            //Pendientes
            //En proceso
            //Terminadas
            //Promedio por pieza (Tiempo)
        }*/
        $Lineas = Linea::where('id','!=',1)->get();
        foreach($Estaciones as $Estac){
            //if()
            $Estac;
        }
        return response()->json([
            "OFAbiertaCant" => $OFAbiertaCant,
            "OFCerradaCant" => $OFCerradaCant,
            "PorcentajeAvanceA" => round($PorcentajeAvanceA,2),
            "PorcentajeAvanceC" => round($PorcentajeAvanceC,2),
            "Estaciones" => $Estaciones,
        ]);
    }
    public function Semana(){
        // 1 Semana hacia Atras
        $FechaHoy = date('Y-m-d');
        $FechaSemana = date('Y-m-d', strtotime('-1 week'));
        $FechaSemanaAtras = date('Y-m-d', strtotime('-2 week'));
        $OFAbierta = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaSemana,$FechaHoy])->get();//->where('Cerrada',1)->get();
        $OFCerrada = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaSemana,$FechaHoy])->where('Cerrada',0)->get();
        $OFAbiertaCant = $OFAbierta->count();
        $OFCerradaCant = $OFCerrada->count();
        $OFAbiertaAyer = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaSemanaAtras,$FechaSemana])->where('Cerrada',1)->get();
        $OFCerradaAyer = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaSemanaAtras,$FechaSemana])->where('Cerrada',0)->get();
        $OFAbiertaCantAyer = $OFAbiertaAyer->count();
        $OFCerradaCantAyer = $OFCerradaAyer->count();
        if($OFAbiertaCantAyer == 0){
            $PorcentajeAvanceA = $OFAbiertaCant*100;
        }else{
            $PorcentajeAvanceA = (($OFAbiertaCant-$OFAbiertaCantAyer)/$OFAbiertaCantAyer)*100;
        }
        if($OFCerradaCantAyer == 0){
            $PorcentajeAvanceC = $OFCerradaCant*100;
        }else{
            $PorcentajeAvanceC = (($OFCerradaCant-$OFCerradaCantAyer)/$OFCerradaCantAyer)*100;
        }
        if($PorcentajeAvanceA>=0){
            $PorcentajeAvanceA = "+".$PorcentajeAvanceA; 
        }else{
            $PorcentajeAvanceA = $PorcentajeAvanceA; 
        }
        if($PorcentajeAvanceC>=0){
            $PorcentajeAvanceC = "+".$PorcentajeAvanceC; 
        }else{
            $PorcentajeAvanceC = $PorcentajeAvanceC; 
        }
        $Estaciones = Areas::where('id','!=',19)->where('id','!=',20)->where('id','!=',21)->get();
        return response()->json([
            "OFAbiertaCant" => $OFAbiertaCant,
            "OFCerradaCant" => $OFCerradaCant,
            "PorcentajeAvanceA" => round($PorcentajeAvanceA,2),
            "PorcentajeAvanceC" => round($PorcentajeAvanceC,2),
            "Estaciones" => $Estaciones,
        ]);
    }
    public function Mes(){
        //Este mes
        $FechaMesInicio = date('Y-m-01');
        $FechaMes = date('Y-m-d', strtotime('-1 month'));
        $FechaMesAtras = date('Y-m-d', strtotime('-2 month'));
        $OFAbierta = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaMes,$FechaMesInicio])->get();//->where('Cerrada',1)->get();
        $OFCerrada = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaMes,$FechaMesInicio])->where('Cerrada',0)->get();
        $OFAbiertaCant = $OFAbierta->count();
        $OFCerradaCant = $OFCerrada->count();
        $OFAbiertaAyer = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaMesAtras,$FechaMes])->where('Cerrada',1)->get();
        $OFCerradaAyer = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaMesAtras,$FechaMes])->where('Cerrada',0)->get();
        $OFAbiertaCantAyer = $OFAbiertaAyer->count();
        $OFCerradaCantAyer = $OFCerradaAyer->count();
        if($OFAbiertaCantAyer == 0){
            $PorcentajeAvanceA = $OFAbiertaCant*100;
        }else{
            $PorcentajeAvanceA = (($OFAbiertaCant-$OFAbiertaCantAyer)/$OFAbiertaCantAyer)*100;
        }
        if($OFCerradaCantAyer == 0){
            $PorcentajeAvanceC = $OFCerradaCant*100;
        }else{
            $PorcentajeAvanceC = (($OFCerradaCant-$OFCerradaCantAyer)/$OFCerradaCantAyer)*100;
        }
        if($PorcentajeAvanceA>=0){
            $PorcentajeAvanceA = "+".$PorcentajeAvanceA; 
        }else{
            $PorcentajeAvanceA = $PorcentajeAvanceA; 
        }
        if($PorcentajeAvanceC>=0){
            $PorcentajeAvanceC = "+".$PorcentajeAvanceC; 
        }else{
            $PorcentajeAvanceC = $PorcentajeAvanceC; 
        }
        $Estaciones = Areas::where('id','!=',19)->where('id','!=',20)->where('id','!=',21)->get();
        return response()->json([
            "OFAbiertaCant" => $OFAbiertaCant,
            "OFCerradaCant" => $OFCerradaCant,
            "PorcentajeAvanceA" => round($PorcentajeAvanceA,2),
            "PorcentajeAvanceC" => round($PorcentajeAvanceC,2),
            "Estaciones" => $Estaciones,
        ]);
    }
    public function DashboardPrincipal(Request $request){
        $LapsoTiempo = $request->LapsoTiempo;
        $FechaInicio = $request->FechaInicio;
        $FechaFin = $request->FechaFin;
        if($LapsoTiempo == 'D'){
            return $this-> Dia();
        }else if($LapsoTiempo == 'S'){
            return $this-> Semana();
        }else{
            return $this-> Mes();
        }

    }
    /*public function CapacidadProductiva()
    {
        $fecha=date('y-m-d 00:00:00');
        $fechaFin= date('y-m-d 23:59:59');
        $PorcentajePlaneacion=PorcentajePlaneacion::where('FechaPlaneacion',$fecha)->first();
        if($PorcentajePlaneacion==""){
            $NumeroPersonas=20;
            $PiezasPorPersona=50;
            $PlaneadoPorDia=Partidasof_Areas::where('Areas_id',9)
                                                ->where('FechaComienzo','>=',$fecha)
                                                ->where('FechaComienzo','<=',$fechaFin)
                                                ->get()->SUM('Cantidad');
            $CantidadEstimadaDia=$NumeroPersonas*$PiezasPorPersona;
            $PorcentajePlaneada=number_format($PlaneadoPorDia/$CantidadEstimadaDia*100,2);
            $PorcentajeFaltante=number_format(100-$PorcentajePlaneada,2);
        }else{
            $NumeroPersonas=$PorcentajePlaneacion->NumeroPersonas;
            $PiezasPorPersona=$PorcentajePlaneacion->CantidadPlaneada/$PorcentajePlaneacion->NumeroPersonas;
            $PlaneadoPorDia=Partidasof_Areas::where('Areas_id',9)
                                                ->where('FechaComienzo','>=',$fecha)
                                                ->where('FechaComienzo','<=',$fechaFin)
                                                ->get()->SUM('Cantidad');
            $CantidadEstimadaDia=$NumeroPersonas*$PiezasPorPersona;
            $PorcentajePlaneada=str_replace(',','',number_format($PlaneadoPorDia/$CantidadEstimadaDia*100,2));
            $PorcentajeFaltante=number_format(100-$PorcentajePlaneada,2);
        }
        return response()->json([
                'PorcentajePlaneada' => $PorcentajePlaneada,
                'PorcentajeFaltante' => $PorcentajeFaltante,
                'NumeroPersonas' => $NumeroPersonas,
                'PlaneadoPorDia'=>$PlaneadoPorDia,
                'CantidadEstimadaDia'=>$CantidadEstimadaDia,
                'Piezasfaltantes'=>($CantidadEstimadaDia-$PlaneadoPorDia),
                'Fecha_Grafica'=>Carbon::parse($fecha)->translatedFormat('d \d\e F \d\e Y'),

        ]);
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
        
    /*}
    public function graficasmes()
    {
        $fechaInicio = now()->startOfMonth(); // Primer día del mes actual
        $fechaFin = now()->endOfMonth(); // Último día del mes actual
        $area9 = DB::table('partidasof_areas')
        ->join('partidasof', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
        ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
        ->whereBetween('ordenfabricacion.FechaEntrega', [$fechaInicio, $fechaFin]) // Filtrar por el mes actual
        ->where('partidasof_areas.Areas_id', 9)
        ->where('ordenfabricacion.Cerrada', 0)
        ->select(
            'ordenfabricacion.OrdenFabricacion',
            'ordenfabricacion.CantidadTotal',
            'partidasof_areas.Areas_id',
            DB::raw('GROUP_CONCAT(partidasof_areas.id) as id'),
            'ordenfabricacion.FechaEntrega',
            DB::raw('SUM(partidasof_areas.Cantidad) as Cantidad'),
            DB::raw('ROUND(LEAST((SUM(partidasof_areas.Cantidad) / ordenfabricacion.CantidadTotal) * 100, 100), 2) as Progreso')
        )
        ->groupBy('ordenfabricacion.OrdenFabricacion', 'partidasof_areas.Areas_id', 'ordenfabricacion.CantidadTotal', 'ordenfabricacion.FechaEntrega')
        ->get();

        $areas = DB::table('partidasof_areas')
            ->join('partidasof', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
            ->whereBetween('ordenfabricacion.FechaEntrega', [$fechaInicio, $fechaFin]) // Filtrar por el mes actual
            ->whereIn('partidasof_areas.Areas_id', [3, 4, 5, 6, 7, 8])
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal',
                'partidasof_areas.Areas_id',
                DB::raw('GROUP_CONCAT(partidasof_areas.id) as id'),
                'ordenfabricacion.FechaEntrega',
                DB::raw('SUM(partidasof_areas.Cantidad) as Cantidad'),
                DB::raw('ROUND(LEAST((SUM(partidasof_areas.Cantidad) / ordenfabricacion.CantidadTotal) * 100, 100), 2) as Progreso')
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
                DB::raw('ROUND(LEAST((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100, 100), 2) as Progreso'),
                DB::raw('ROUND(SUM(partidasof.cantidad_partida) - ordenfabricacion.CantidadTotal, 0) as retrabajo')
            )
            ->whereBetween('ordenfabricacion.FechaEntrega', [$fechaInicio, $fechaFin]) // Filtrar por el mes actual
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal', 'ordenfabricacion.FechaEntrega')
            ->get();

        $totalOrdenes = DB::table('ordenfabricacion')
            ->whereBetween('FechaEntrega', [$fechaInicio, $fechaFin]) // Filtrar por el mes actual
            ->count();

        $estacionesAreas = [
            3 => 'plemasSuministro',
            4 => 'plemasPreparado',
            5 => 'plemasEnsamble',
            6 => 'plemasPulido',
            7 => 'plemasMedicion',
            8 => 'plemasVisualizacion',
            
        ];

        $estacionesCortes = [
            2 => 'plemasCorte'
        ];
        $estacionEmpacado = [
            9 => 'plemasEmpaque'
        ];

        $datos = [];

        foreach (array_merge($estacionesAreas, $estacionesCortes, $estacionEmpacado) as $estacion) {
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
          // Procesar área 9 (Empaque)
          foreach ($area9 as $empacado) {
            if (isset($estacionEmpacado[$empacado->Areas_id])) {
                $nombreEstacion = $estacionEmpacado[$empacado->Areas_id];
    
                if ((float)$empacado->Progreso == 100.00) {
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
        $fechaInicio = now()->startOfMonth(); // Primer día del mes actual
        $fechaFin = now()->endOfMonth(); // Último día del mes actual
    
        // Ordenes Completadas (Cerradas)
        $ordenesCompletadas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->where('partidasof_areas.Areas_id', 9) // Solo cerradas
            ->whereBetween('ordenfabricacion.FechaEntrega', [$fechaInicio, $fechaFin]) // Filtrar solo el mes actual
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
            ->whereBetween('ordenfabricacion.FechaEntrega', [$fechaInicio, $fechaFin]) // Filtrar solo el mes actual
            ->get();
    
        // Total de Ordenes
        $totalOrdenes = DB::table('ordenfabricacion')
            ->whereBetween('FechaEntrega', [$fechaInicio, $fechaFin]) // Filtrar solo el mes actual
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
                'partidasof_areas.FechaTermina',
                DB::raw('SUM(partidasof_areas.Cantidad) as SumaTotalcantidad_partida')
            )
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal', 'partidasof_areas.Areas_id', 'partidasof_areas.FechaComienzo', 'partidasof_areas.FechaTermina')
            ->get();

        // Obtener los datos de Cortes (Área ID 2) de las últimas 24 horas
        $DiasCortes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->where('partidasof.FechaComienzo', '>=', $hace24Horas)  // Filtrar por fecha
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal',
                'partidasof.FechaComienzo',
                'partidasof.FechaFinalizacion',
                DB::raw('SUM(partidasof.Cantidad_partida) as SumaTotalcantidad_partida')
            )
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal', 'partidasof.FechaComienzo', 'partidasof.FechaFinalizacion')
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

        // Inicializamos los datos para el gráfico con los segundos
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
            $start = Carbon::parse($corte->FechaComienzo);
            $end = Carbon::parse($corte->FechaFinalizacion);
            $durationInSeconds = $start->diffInSeconds($end); // Diferencia en segundos

            $hour = Carbon::parse($corte->FechaComienzo)->hour; 
            $serieIndex = array_search('Cortes', array_column($series, 'name'));
            $series[$serieIndex]['data'][$hour] += $durationInSeconds;
        }

        // Para otras áreas (3-9)
        foreach ($DiasAreas as $area) {
            $hour = Carbon::parse($area->FechaComienzo)->hour;
            $areaName = $areasMap[$area->Areas_id] ?? null;

            if ($areaName && $hour !== null) {
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
            'series' => $series,  // Los datos de las series agrupados por hora
            'fecha' => Carbon::now()->translatedFormat('d \d\e F \d\e\l Y') // Formato en español
        ]);
    }  
    public function wizarpdia()
    {
        $fechaLimite = now()->setTimezone('America/Mexico_City'); // Cambiar la zona horaria
       
        
        // Obtiene las órdenes del día anterior
        
        // Ordenes Completadas (cerradas) por día
        $ordenesCompletadas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->where('partidasof_areas.Areas_id', 9)
            ->where('ordenfabricacion.Cerrada', 0)
            ->whereDate('ordenfabricacion.FechaEntrega', '=', $fechaLimite) // Filtra por la fecha exacta
            ->select(
                'ordenfabricacion.CantidadTotal',
                DB::raw('SUM(partidasof_areas.Cantidad) as Cantidad'),
                'partidasof_areas.PartidasOF_id',
                DB::raw('GROUP_CONCAT(partidasof_areas.id) as ids')
            )
            ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.CantidadTotal')
            ->havingRaw('SUM(partidasof_areas.Cantidad) = ordenfabricacion.CantidadTotal')
            ->get();
        
        // Ordenes Abiertas por día
        $ordenesAbiertas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->whereDate('ordenfabricacion.FechaEntrega', '=', $fechaLimite) // Filtra por la fecha exacta
            ->select('partidasof.OrdenFabricacion_id', 'partidasof.cantidad_partida')
            ->get();
        
        // Total de Ordenes por día
        $totalOrdenes = DB::table('ordenfabricacion')
            ->whereDate('FechaEntrega', '=', $fechaLimite)
            ->select('ordenfabricacion.Ordenfabricacion') // Filtra por la fecha exacta
            ->count();
        
        $ordenesAbiertasCount = $totalOrdenes - $ordenesCompletadas->count();

        return response()->json([
            'ordenesCompletadas' => $ordenesCompletadas->count(),
            'ordenesAbiertas' => $ordenesAbiertasCount,
            'totalOrdenes' => $totalOrdenes
        ]);
    } 
    public function wizarp()
    {
        // Calculando las fechas de inicio y fin de la semana actual
        $inicioSemana = now()->startOfWeek();  // Empieza el lunes
        $finSemana = now()->endOfWeek();  // Termina el domingo
    
        // Ordenes Completadas (cerradas) en la semana
        $ordenesCompletadas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->where('partidasof_areas.Areas_id', 9)
            ->where('ordenfabricacion.Cerrada', 0)
            ->whereBetween('ordenfabricacion.FechaEntrega', [$inicioSemana, $finSemana])
            ->select(
                'ordenfabricacion.CantidadTotal',
                DB::raw('SUM(partidasof_areas.Cantidad) as Cantidad'),
                'partidasof_areas.PartidasOF_id',
                DB::raw('GROUP_CONCAT(partidasof_areas.id) as ids')
            )
            ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.CantidadTotal')
            ->havingRaw('SUM(partidasof_areas.Cantidad) = ordenfabricacion.CantidadTotal')
            ->get();
            
    
        // Ordenes Abiertas en la semana
        $ordenesAbiertas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->whereBetween('ordenfabricacion.FechaEntrega', [$inicioSemana, $finSemana])
            ->select('partidasof.OrdenFabricacion_id', 'partidasof.cantidad_partida')
            ->get();
    
        // Total de Ordenes
        $totalOrdenes = DB::table('ordenfabricacion')
        ->whereBetween('ordenfabricacion.FechaEntrega', [$inicioSemana, $finSemana])
        ->count();
     
        
        $ordenesAbiertasCount = $totalOrdenes - $ordenesCompletadas->count();
     
    
        return response()->json([
            'ordenesCompletadas' => $ordenesCompletadas->count(),
            'ordenesAbiertas' => $ordenesAbiertasCount,
            'totalOrdenes' => $totalOrdenes
        ]);
    }
    public function wizarpmes()
    {
        $fechaActual = now(); // Obtiene la fecha actual
        $mesActual = $fechaActual->month;
        $anioActual = $fechaActual->year;
    
        // Obtiene el primer y último día del mes
        $primerDiaDelMes = now()->startOfMonth(); // Primer día del mes
        $ultimoDiaDelMes = now()->endOfMonth(); // Último día del mes
    
        // Ordenes Completadas (cerradas) por mes
        $ordenesCompletadas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->where('partidasof_areas.Areas_id', 9)
            ->where('ordenfabricacion.Cerrada', 0)
            ->whereBetween('ordenfabricacion.FechaEntrega', [$primerDiaDelMes, $ultimoDiaDelMes]) // Filtra por el rango de fechas del mes
            ->select(
                'ordenfabricacion.CantidadTotal',
                DB::raw('SUM(partidasof_areas.Cantidad) as Cantidad'),
                'partidasof_areas.PartidasOF_id',
                DB::raw('GROUP_CONCAT(partidasof_areas.id) as ids')
            )
            ->groupBy('partidasof_areas.PartidasOF_id', 'ordenfabricacion.CantidadTotal')
            ->havingRaw('SUM(partidasof_areas.Cantidad) = ordenfabricacion.CantidadTotal')
            ->get();
    
        // Ordenes Abiertas por mes
        $ordenesAbiertas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->whereBetween('ordenfabricacion.FechaEntrega', [$primerDiaDelMes, $ultimoDiaDelMes]) // Filtra por el rango de fechas del mes
            ->select('partidasof.OrdenFabricacion_id', 'partidasof.cantidad_partida')
            ->get();
    
        // Total de Ordenes por mes
        $totalOrdenes = DB::table('ordenfabricacion')
            ->whereBetween('FechaEntrega', [$primerDiaDelMes, $ultimoDiaDelMes]) // Filtra por el rango de fechas del mes
            ->count();
    
        $ordenesAbiertasCount = $totalOrdenes - $ordenesCompletadas->count();
    
        return response()->json([
            'ordenesCompletadas' => $ordenesCompletadas->count(),
            'ordenesAbiertas' => $ordenesAbiertasCount,
            'totalOrdenes' => $totalOrdenes
        ]);
    }
    public function graficasdia()
    {
        $fechaLimite = now()->setTimezone('America/Mexico_City')->toDateString();
    
        $area9 = DB::table('partidasof_areas')
            ->join('partidasof', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
            ->whereDate('partidasof_areas.FechaComienzo', '>=', $fechaLimite) 
            ->where('partidasof_areas.Areas_id', 9)
            ->where('ordenfabricacion.Cerrada', 0)
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal',
                'partidasof_areas.Areas_id',
                DB::raw('GROUP_CONCAT(partidasof_areas.id) as id'),
                'ordenfabricacion.FechaEntrega',
                DB::raw('SUM(partidasof_areas.Cantidad) as Cantidad'),
                DB::raw('ROUND(LEAST((SUM(partidasof_areas.Cantidad) / ordenfabricacion.CantidadTotal) * 100, 100), 2) as Progreso')
            )
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'partidasof_areas.Areas_id', 'ordenfabricacion.CantidadTotal', 'ordenfabricacion.FechaEntrega')
            ->get();
    
        $areas = DB::table('partidasof_areas')
            ->join('partidasof', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
            ->whereDate('partidasof_areas.FechaComienzo', '>=', $fechaLimite) 
            ->whereIn('partidasof_areas.Areas_id', [3, 4, 5, 6, 7, 8])
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal',
                'partidasof_areas.Areas_id',
                DB::raw('GROUP_CONCAT(partidasof_areas.id) as id'),
                'ordenfabricacion.FechaEntrega',
                DB::raw('SUM(partidasof_areas.Cantidad) as Cantidad'),
                DB::raw('ROUND(LEAST((SUM(partidasof_areas.Cantidad) / ordenfabricacion.CantidadTotal) * 100, 100), 2) as Progreso')
            )
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'partidasof_areas.Areas_id', 'ordenfabricacion.CantidadTotal', 'ordenfabricacion.FechaEntrega')
            ->get();
    
        $cortes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                DB::raw('GROUP_CONCAT(partidasof.id) as id'),
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal',
                DB::raw('MIN(partidasof.FechaComienzo) as FechaComienzo'),
                DB::raw('SUM(partidasof.cantidad_partida) as SumaTotalcantidad_partida'),
                DB::raw('ROUND(LEAST((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100, 100), 2) as Progreso')
            )
            ->whereDate('ordenfabricacion.FechaEntrega', '=', $fechaLimite)
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal')
            ->get();
    
        $totalOrdenes = DB::table('ordenfabricacion')
            ->whereDate('FechaEntrega', '=', $fechaLimite)
            ->select('ordenfabricacion.Ordenfabricacion')
            ->count();
    
        $estacionesAreas = [
            3 => 'plemasSuministrodia',
            4 => 'plemasPreparadodia',
            5 => 'plemasEnsambledia',
            6 => 'plemasPulidodia',
            7 => 'plemasMediciondia',
            8 => 'plemasVisualizaciondia',
        ];
    
        $estacionesCortes = [
            2 => 'plemasCortedia'
        ];
    
        $estacionEmpacado = [
            9 => 'plemasEmpaquedia'
        ];
    
        $datos = [];
    
        foreach (array_merge($estacionesAreas, $estacionesCortes, $estacionEmpacado) as $estacion) {
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
    
        // Procesar área 9 (Empaque)
        foreach ($area9 as $empacado) {
            if (isset($estacionEmpacado[$empacado->Areas_id])) {
                $nombreEstacion = $estacionEmpacado[$empacado->Areas_id];
    
                if ((float)$empacado->Progreso == 100.00) {
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
    
        $datos['plemasCortedia']['completado'] = $completadosCorte;
        $datos['plemasCortedia']['pendiente'] = $pendientesCorte;
    
        return response()->json($datos);
    }
    public function graficasemana()
    {
        $fechaInicioSemana = now()->startOfWeek()->toDateString();
        $fechaFinSemana = now()->endOfWeek()->toDateString();

        $area9 = DB::table('partidasof_areas')
        ->join('partidasof', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
        ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
        ->whereBetween('ordenfabricacion.FechaEntrega', [$fechaInicioSemana, $fechaFinSemana]) 
        ->where('partidasof_areas.Areas_id', 9)
        ->where('ordenfabricacion.Cerrada', 0)
        ->select(
            'ordenfabricacion.OrdenFabricacion',
            'ordenfabricacion.CantidadTotal',
            'partidasof_areas.Areas_id',
            DB::raw('GROUP_CONCAT(partidasof_areas.id) as id'),
            'ordenfabricacion.FechaEntrega',
            DB::raw('SUM(partidasof_areas.Cantidad) as Cantidad'),
            DB::raw('ROUND(LEAST((SUM(partidasof_areas.Cantidad) / ordenfabricacion.CantidadTotal) * 100, 100), 2) as Progreso')
        )
        ->groupBy('ordenfabricacion.OrdenFabricacion', 'partidasof_areas.Areas_id', 'ordenfabricacion.CantidadTotal', 'ordenfabricacion.FechaEntrega')
        ->get();
            
        $areas = DB::table('partidasof_areas')
            ->join('partidasof', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
            ->whereBetween('ordenfabricacion.FechaEntrega', [$fechaInicioSemana, $fechaFinSemana]) 
            ->whereIn('partidasof_areas.Areas_id', [3, 4, 5, 6, 7, 8])
            ->select(
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal',
                'partidasof_areas.Areas_id',
                DB::raw('GROUP_CONCAT(partidasof_areas.id) as id'),
                'ordenfabricacion.FechaEntrega',
                DB::raw('SUM(partidasof_areas.Cantidad) as Cantidad'),
                DB::raw('ROUND(LEAST((SUM(partidasof_areas.Cantidad) / ordenfabricacion.CantidadTotal) * 100, 100), 2) as Progreso')
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
                DB::raw('ROUND(LEAST((SUM(partidasof.cantidad_partida) / ordenfabricacion.CantidadTotal) * 100, 100), 2) as Progreso'),
                DB::raw('ROUND(SUM(partidasof.cantidad_partida) - ordenfabricacion.CantidadTotal, 0) as retrabajo')
            )
            ->whereBetween('ordenfabricacion.FechaEntrega', [$fechaInicioSemana, $fechaFinSemana]) 
            ->groupBy('ordenfabricacion.OrdenFabricacion', 'ordenfabricacion.CantidadTotal', 'ordenfabricacion.FechaEntrega')
            ->get();
        
        $totalOrdenes = DB::table('ordenfabricacion')
            ->whereBetween('FechaEntrega', [$fechaInicioSemana, $fechaFinSemana]) 
            ->count();
        
        $estacionesAreas = [
            3 => 'plemasSuministrosemana',
            4 => 'plemasPreparadosemana',
            5 => 'plemasEnsamblesemana',
            6 => 'plemasPulidosemana',
            7 => 'plemasMedicionsemana',
            8 => 'plemasVisualizacionsemana',
        
        ];
        
        $estacionesCortes = [
            2 => 'plemasCortesemana'
        ];
        $estacionesEmpacado = [
            9 => 'plemasEmpaquesemana'  // Esta es la estación de Empaque (Estación 9)
        ];

        $datos = [];
        
        foreach (array_merge($estacionesAreas, $estacionesCortes, $estacionesEmpacado) as $estacion) {
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
        // Procesar área 9 (Empaque)
        foreach ($area9 as $empacado) {
            if (isset($estacionesEmpacado[$empacado->Areas_id])) {
                $nombreEstacion = $estacionesEmpacado[$empacado->Areas_id];

                if ((float)$empacado->Progreso == 100.00) {
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
        
        $datos['plemasCortesemana']['completado'] = $completadosCorte;
        $datos['plemasCortesemana']['pendiente'] = $pendientesCorte;

        return response()->json($datos);
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
    public function Dasboardindicadordia()
    {
        
        $NumeroPersonas = DB::table('porcentajeplaneacion')
            ->join('linea', 'porcentajeplaneacion.Linea_id', '=', 'linea.id')
            ->whereDate('porcentajeplaneacion.FechaPlaneacion', today())
            ->where('linea.active', 1)
            ->select('linea.id as LineaId', 'linea.Nombre', DB::raw('SUM(porcentajeplaneacion.NumeroPersonas) as TotalNumeroPersonas'), DB::raw('SUM(porcentajeplaneacion.CantidadPlaneada) as TotalCantidadPlaneada'))
            ->groupBy('linea.id', 'linea.Nombre')
            ->get();

        $personal = [
            'LineaId' => $NumeroPersonas->pluck('LineaId')->implode(','),
            'Nombre' => $NumeroPersonas->pluck('Nombre')->implode(','),
            'NumeroPersonas' => $NumeroPersonas->sum('TotalNumeroPersonas'),
            'CantidadPlaneada' => $NumeroPersonas->sum('TotalCantidadPlaneada'),
        ];

        $total = DB::table('porcentajeplaneacion')
            ->join('linea', 'porcentajeplaneacion.Linea_id', '=', 'linea.id')
            ->whereDate('porcentajeplaneacion.FechaPlaneacion', today())
            ->where('linea.active', 1)
            ->select('linea.id as LineaId', 'linea.Nombre', DB::raw('SUM(porcentajeplaneacion.NumeroPersonas) as TotalNumeroPersonas'), DB::raw('SUM(porcentajeplaneacion.CantidadPlaneada) as TotalCantidadPlaneada'))
            ->groupBy('linea.id', 'linea.Nombre')
            ->get();

        $TotalOfTotal = [
            'LineaId' => $total->pluck('LineaId')->implode(','),
            'Nombre' => $total->pluck('Nombre')->implode(','),
            'NumeroPersonas' => $total->sum('TotalNumeroPersonas'),
            'CantidadTotal' => $total->sum('TotalCantidadPlaneada'),
        ];
        $indicador = DB::table('ordenfabricacion')
        ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
        ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
        ->whereDate('ordenfabricacion.FechaEntrega', today())
        ->where('partidasof_areas.Areas_id', 9)
        ->select(
            'OrdenFabricacion',
            'OrdenVenta_id',
            'partidasof_areas.Cantidad',
            'ordenfabricacion.Cerrada',
            'partidasof_areas.Areas_id',
            DB::raw('SUM(partidasof_areas.Cantidad) as SumaCantidad')
        )
        ->groupBy('OrdenFabricacion', 'OrdenVenta_id', 'partidasof_areas.Cantidad', 'ordenfabricacion.Cerrada', 'partidasof_areas.Areas_id')
        ->get();

        // Calcular correctamente TotalOFcompletadas
        $totalOFcompletadas = $indicador->where('Cerrada', 1)->sum(function ($item) {
            return $item->SumaCantidad;  // Sumar SumaCantidad en lugar de Cantidad
        });

        $totalSumaCantidad = $indicador->sum('SumaCantidad'); // Sumar todas las SumaCantidad

        $porcentajeCompletadas = $TotalOfTotal['CantidadTotal'] > 0 ? ($totalOFcompletadas / $TotalOfTotal['CantidadTotal']) * 100 : 0;
        $faltanteTotal = $TotalOfTotal['CantidadTotal'] - $totalSumaCantidad;
        $porcentajeCerradas = $TotalOfTotal['CantidadTotal'] > 0 ? ($faltanteTotal / $TotalOfTotal['CantidadTotal']) * 100 : 0;

        return response()->json([
            'Cantidadpersonas' => $personal['NumeroPersonas'] ?? 0,
            'Estimadopiezas' => $personal['CantidadPlaneada'] ?? 0,
            'indicador' => $indicador,
            'TotalOFcompletadas' => $totalOFcompletadas,
            'TotalOfTotal' => (int) $TotalOfTotal['CantidadTotal'],
            'faltanteTotal' => $faltanteTotal,
            'PorcentajeCompletadas' => round($porcentajeCompletadas, 2),
            'porcentajeCerradas' => round($porcentajeCerradas, 2),
        ]);

       

    }
    public function tiempopromedio() 
    {
        $startOfDay = Carbon::now()->startOfDay();
        $endOfDay = Carbon::now()->endOfDay();
        $FechaInicio = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->where('ordenfabricacion.Escaner', 0)
            ->whereIn('partidasof_areas.Areas_id', [4,5,6,7,8])
            ->whereBetween('partidasof_areas.FechaComienzo', [$startOfDay, $endOfDay])
            ->select(
                'partidasof_areas.PartidasOF_id',
                'partidasof_areas.Areas_id',
                'partidasof_areas.Cantidad',
                'partidasof_areas.FechaComienzo',
                'partidasof_areas.FechaTermina'
            )
            ->get();
        $FechaFin = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->where('ordenfabricacion.Escaner', 0)
            ->whereIn('partidasof_areas.Areas_id', [4,5,6,7,8])
            ->whereBetween('partidasof_areas.FechaTermina', [$startOfDay, $endOfDay])
            ->select(
                'partidasof_areas.PartidasOF_id',
                'partidasof_areas.Areas_id',
                'partidasof_areas.Cantidad',
                'partidasof_areas.FechaComienzo',
                'partidasof_areas.FechaTermina'
            )
            ->get();
        $filteredFechaFin = $FechaFin->filter(function($item) {
            return !is_null($item->FechaTermina);
        });
        $groupedFechaFin = $filteredFechaFin->groupBy(function($item) {
            return $item->PartidasOF_id . '-' . $item->Areas_id;
        });
        $result = [];
        foreach ($groupedFechaFin as $key => $group) {
            $firstFechaInicio = $FechaInicio->first(function($item) use ($group) {
                return $item->PartidasOF_id == $group->first()->PartidasOF_id && $item->Areas_id == $group->first()->Areas_id;
            });
            $last = $group->last();
            $totalQuantity = $group->sum('Cantidad');
            $fechaComienzo = Carbon::parse($firstFechaInicio->FechaComienzo);
            $fechaTermina = Carbon::parse($last->FechaTermina);
            $diffInSeconds = $fechaComienzo->diffInSeconds($fechaTermina);
    
            $result[] = [
                [
                    'PartidasOF_id' => $firstFechaInicio->PartidasOF_id,
                    'Areas' => $firstFechaInicio->Areas_id,
                    'Cantidad' => $totalQuantity,
                    'FechaComienzo' => $firstFechaInicio->FechaComienzo,
                    'FechaTermina' => $last->FechaTermina,
                    'TiempoEnSegundos' => $diffInSeconds,
                    'Tiempopieza' => round($diffInSeconds / $totalQuantity, 2),
                ]
            ];
        }
        $TiempoCortes = DB::table('partidasof')
            ->whereBetween('partidasof.FechaComienzo', [$startOfDay, $endOfDay])
            ->whereBetween('partidasof.FechaFinalizacion', [$startOfDay, $endOfDay])
            ->selectRaw('MIN(FechaComienzo) as FechaComienzo, MAX(FechaFinalizacion) as FechaTermina, 
                        SUM(cantidad_partida) as Cantidad, 
                        TIMESTAMPDIFF(SECOND, MIN(FechaComienzo), MAX(FechaFinalizacion)) as TotalSegundos,
                        TIMESTAMPDIFF(SECOND, MIN(FechaComienzo), MAX(FechaFinalizacion)) / SUM(cantidad_partida) as SegundosPorUnidad')
            ->get();
        $Tiempo = DB::table('partidasof_areas')
            ->whereBetween('FechaComienzo', [$startOfDay, $endOfDay])
            ->whereBetween('FechaTermina', [$startOfDay, $endOfDay])
            ->select('Cantidad', 'Areas_id', 'FechaComienzo', 'FechaTermina',
                     DB::raw('TIMESTAMPDIFF(SECOND, FechaComienzo, FechaTermina) as TotalSegundos'))
            ->get();
        $finalResult = $Tiempo->groupBy('Areas_id')->map(function($items) {
            $totalSegundos = $items->sum('TotalSegundos');
            $totalCantidad = $items->sum('Cantidad');
            $tiempopiezas = $totalSegundos / $totalCantidad; 
            return [
                'Areas' => $items->first()->Areas_id,
                'Cantidad' => $totalCantidad,
                'total' => $totalSegundos,
                'tiempopiezas' => round($tiempopiezas, 2)
            ];
        })->values();
        return response()->json([
            'finalResult' => $finalResult,
            'TiempoCortes' => $TiempoCortes,
            'result' => $result
        ]);
    }
    public function guardarDasboard(Request $request)
    {
        try {
            Log::info('Datos recibidos:', $request->all());
            return response()->json(['success' => true, 'message' => 'Datos guardados correctamente.']);
        } catch (\Exception $e) {
            Log::error('Error al guardar datos: ' . $e->getMessage());

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    public function graficastiempo(Request $request)
    { 
        $hoy = Carbon::now()->toDateString();
        $Porcentaje = DB::table('porcentajeplaneacion')
        ->whereDate('Porcentajeplaneacion.FechaPlaneacion', '=', $hoy) 
        ->select(
            'Porcentajeplaneacion.NumeroPersonas',
            'Porcentajeplaneacion.CantidadPlaneada'

        )
        ->get();
    
        // Obtener los datos de la base de datos
        $DiasAreas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->whereIn('partidasof_areas.Areas_id', [3, 4, 5, 6, 7, 8, 9])
            ->whereDate('partidasof_areas.FechaComienzo', '=', $hoy) 
            ->select(
                'partidasof_areas.PartidasOF_id',
                'partidasof_areas.Areas_id',
                'partidasof_areas.FechaComienzo',
                'partidasof_areas.FechaTermina',
                'partidasof_areas.Cantidad',
                DB::raw("TIMESTAMPDIFF(SECOND, partidasof_areas.FechaComienzo, partidasof_areas.FechaTermina) as TotalSegundos")
            )
            ->get()
            ->map(function ($item) {
                $horas = floor($item->TotalSegundos / 3600);
                $minutos = floor(($item->TotalSegundos % 3600) / 60);
                $segundos = $item->TotalSegundos % 60;
                $item->TiempoTotal = "{$horas} horas {$minutos} minutos {$segundos} segundos";
                return $item;
            });
    
        // Mapeo de las áreas
        $areasMap = [
            3 => 'Suministro',
            4 => 'Preparado',
            5 => 'Ensamble',
            6 => 'Pulido',
            7 => 'Medición',
            8 => 'Visualización',
            9 => 'Empacado'
        ];
    
        // Sumar el tiempo total por área
        $totalSegundosPorArea = [
            3 => 0, // Suministro
            4 => 0, // Preparado
            5 => 0, // Ensamble
            6 => 0, // Pulido
            7 => 0, // Medición
            8 => 0, // Visualización
            9 => 0  // Empacado
        ];
    
        // Recorrer las áreas y acumular el tiempo por cada área
        foreach ($DiasAreas as $item) {
            $totalSegundosPorArea[$item->Areas_id] += $item->TotalSegundos;
        }
    
        // Generar los datos para el gráfico con el formato de tiempo
        $graficoData = [];
        foreach ($totalSegundosPorArea as $areaId => $totalSegundos) {
            if ($totalSegundos > 0) {  // Solo mostrar áreas con tiempo registrado
                // Convertir los segundos a horas, minutos y segundos
                $horas = floor($totalSegundos / 3600);
                $minutos = floor(($totalSegundos % 3600) / 60);
                $segundos = $totalSegundos % 60;

                // Formatear el tiempo
                $tiempoFormateado = "{$horas} horas {$minutos} minutos {$segundos} segundos";

                $graficoData[] = [
                    'name' => $areasMap[$areaId],
                    'value' => $totalSegundos,  // Usar los segundos reales para el gráfico
                    'formatted' => $tiempoFormateado  // Pasar el tiempo formateado para mostrar en el tooltip
                ];
            }
        }
    
        // Calcular el tiempo total y promedio
        $totalSegundos = array_sum($totalSegundosPorArea);
        $horasTotal = floor($totalSegundos / 3600);
        $minutosTotal = floor(($totalSegundos % 3600) / 60);
        $segundosTotal = $totalSegundos % 60;
    
        $tiempodeareas = "{$horasTotal} horas {$minutosTotal} minutos {$segundosTotal} segundos";
        $tiempodeareasSegundos = $totalSegundos; // Total en segundos
    
        // Calcular el tiempo promedio por pieza
        $tiempoprmedioPiezas = $totalSegundos > 0 ? $tiempodeareasSegundos / $DiasAreas->sum('Cantidad') : 0;
    
       
        return response()->json([
            'areasMap' => $areasMap,
            'graficoData' => $graficoData,  // Aquí ya tenemos los datos listos para el gráfico
            'tiempodeareas' => $tiempodeareas,
            'tiempodeareasSegundos' => $tiempodeareasSegundos,
            'tiempoprmedioPiezas' => $tiempoprmedioPiezas,
            'cantidadTotal' => $DiasAreas->sum('Cantidad'), 
        ]);
    
    }
    public function graficastiempoMuerto(Request $request)
    {
        $hoy = Carbon::now()->toDateString();
        $areas = [
            3 => 'Suministro',
            4 => 'Preparado',
            5 => 'Ensamble',
            6 => 'Pulido',
            7 => 'Medicion',
            8 => 'Visualizacion',
            //9 => 'Empaque'
        ];
        $Cortesarea = [
            2 => 'Corte'
        ];
        $TotalPiezas = DB::table('ordenfabricacion')
            ->whereDate('FechaEntrega', '=', $hoy)
            ->where('ordenfabricacion.Escaner', 1)
            ->sum('CantidadTotal');

        $piezasinicadas= DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->where('ordenfabricacion.Escaner', 1)
            ->whereDate('FechaComienzo', '=', $hoy)
            ->sum('partidasof.cantidad_partida');
        
        $produccioncortes = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->where('ordenfabricacion.Escaner', 1)
            ->whereDate('partidasof.FechaComienzo', '=', $hoy)
            ->select(
                'partidasof.OrdenFabricacion_id',
                'partidasof.cantidad_partida',
                DB::raw("SUM(IFNULL(TIMESTAMPDIFF(SECOND, partidasof.FechaComienzo, partidasof.FechaFinalizacion), 0)) as tiempoProduccionActual")
            )
            ->groupBy('partidasof.OrdenFabricacion_id', 'partidasof.cantidad_partida')
            ->get()
            ->map(function ($item) use ($Cortesarea) {
                $item->area = $Cortesarea[2]; 
                return $item;
            });

    
        // Obtener la producción
        $produccion = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->where('ordenfabricacion.Escaner', 1)
            ->whereIn('partidasof_areas.Areas_id', array_keys($areas))
            ->whereDate('partidasof_areas.FechaComienzo', '=', $hoy)
            ->select(
                'partidasof_areas.Areas_id',
                'partidasof_areas.PartidasOf_id',
                DB::raw("SUM(TIMESTAMPDIFF(SECOND, partidasof_areas.FechaComienzo, partidasof_areas.FechaTermina)) as tiempoProduccionActual")
            )
            ->groupBy('partidasof_areas.Areas_id', 'partidasof_areas.PartidasOf_id')
            ->get();
    
        // Obtener las piezas procesadas
        $piezas = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->where('ordenfabricacion.Escaner', 1)
            ->whereIn('partidasof_areas.Areas_id', array_keys($areas))
            ->whereDate('partidasof_areas.FechaComienzo', '=', $hoy)
            ->where('partidasof_areas.TipoPartida', 'N')
            ->select(
                'partidasof_areas.PartidasOF_id',
                'partidasof_areas.Areas_id',
                DB::raw('SUM(partidasof_areas.Cantidad) as TotalCantidad'),
                DB::raw('GROUP_CONCAT(partidasof_areas.NumeroEtiqueta ORDER BY partidasof_areas.NumeroEtiqueta ASC) as NumeroEtiquetas')
            )
            ->groupBy('partidasof_areas.PartidasOF_id', 'partidasof_areas.Areas_id')
            ->get();
        $tiemposPorPieza = [];
        foreach ($produccion as $prod) {
            $pieza = $piezas->firstWhere('PartidasOF_id', $prod->PartidasOf_id);
            
            if ($pieza) {
                $tiempoPorPieza = $prod->tiempoProduccionActual / $pieza->TotalCantidad;
                $tiemposPorPieza[] = [
                    'Area' => $areas[$prod->Areas_id],
                    'TiempoPorPieza' => number_format($tiempoPorPieza,2)
                ];
            }
        }
        $TiemposMuertos = DB::table('ordenfabricacion')
        ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
        ->where('ordenfabricacion.Escaner', 1)
        ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
        ->whereIn('partidasof_areas.Areas_id', array_keys($areas))
        ->where('partidasof_areas.TipoPartida', 'N')
        ->whereDate('partidasof_areas.FechaComienzo', '=', $hoy)
        ->select(
            'partidasof_areas.PartidasOf_id',
            'partidasof_areas.Areas_id',
            'partidasof_areas.NumeroEtiqueta',
            'partidasof_areas.FechaComienzo',
            'partidasof_areas.FechaTermina'
        )
        ->orderBy('partidasof_areas.NumeroEtiqueta')
        ->orderBy('partidasof_areas.Areas_id')
        ->get();
        $result = [];
        $previousArea = null;
        foreach ($TiemposMuertos as $item) {
            if ($previousArea !== null && $previousArea->NumeroEtiqueta == $item->NumeroEtiqueta) {
                $startTime = new \Carbon\Carbon($previousArea->FechaTermina);
                $endTime = new \Carbon\Carbon($item->FechaComienzo);
                $timeDifference = $startTime->diffInSeconds($endTime); 

                if ($previousArea->Areas_id + 1 == $item->Areas_id) {
                    $key = $previousArea->Areas_id . ',' . $item->Areas_id;
                    if (isset($result[$key])) {
                        $result[$key]['TiempoMuerto'] += $timeDifference;
                        $etiquetas = explode(',', $result[$key]['NumeroEtiqueta']);
                        $etiquetas = array_unique(array_merge($etiquetas, [$item->NumeroEtiqueta]));
                        $result[$key]['NumeroEtiqueta'] = implode(',', $etiquetas);
                    } else {
                        $result[$key] = [
                            'PartidasOf_id' => $previousArea->PartidasOf_id, 
                            'Areas_id' => $previousArea->Areas_id . ',' . $item->Areas_id,
                            'NumeroEtiqueta' => $previousArea->NumeroEtiqueta . ',' . $item->NumeroEtiqueta,
                            'TiempoMuerto' => $timeDifference
                        ];
                    }
                }
            }
            $previousArea = $item;
        }
        $finalResult = array_values($result);
        $numeroPRO = DB::table('ordenfabricacion')
        ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
        ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
        ->where('ordenfabricacion.Escaner', 1)
        ->whereIn('partidasof_areas.Areas_id', array_keys($areas))
        ->where('partidasof_areas.TipoPartida', 'N')
        ->whereDate('partidasof_areas.FechaComienzo', '=', $hoy)
        ->select(
            DB::raw('GROUP_CONCAT(DISTINCT partidasof_areas.partidasOF_id ORDER BY partidasof_areas.partidasOF_id) as partidasOF_id'),
            'partidasof_areas.Areas_id',
            DB::raw('SUM(partidasof_areas.cantidad) as cantidad'),
            DB::raw('GROUP_CONCAT(partidasof_areas.NumeroEtiqueta ORDER BY partidasof_areas.NumeroEtiqueta) as NumeroEtiqueta')
        )
        ->groupBy('partidasof_areas.Areas_id')
        ->get();  
        $tiemposProduccionData = [];
        $tiemposMuertosData = [];

        foreach ($numeroPRO as $item) {
            $areaName = $areas[$item->Areas_id] ?? 'Desconocido';
            $tiemposProduccionData[$areaName] = $item->cantidad;
        }
        return response()->json([
            'produccion' => $produccion,
            'finalResult' => $finalResult,
            'tiemposPorPieza' => $tiemposPorPieza,
            'TotalPiezas' => $TotalPiezas,
            'piezasinicadas'=> $piezasinicadas,
            'numeroPRO'=>$tiemposProduccionData
        ]);
        
        /*
      [
    {
        "partidasOF_id": 31,32
        "Areas_id": 4,
        "cantidad": "5",
        "NumeroEtiqueta": "1,1,2,3,4"
        },

        {
        "partidasOF_id": 31,
        "Areas_id": 6,
        "cantidad": "4",
        "NumeroEtiqueta": "1,2,3,4"
        },
    $TiemposMuertos = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->whereIn('partidasof_areas.Areas_id', array_keys($areas))
            ->where('partidasof_areas.TipoPartida', 'N')
            ->whereDate('partidasof_areas.FechaComienzo', '=', $hoy)
            ->select(
                'partidasof_areas.PartidasOf_id',
                'partidasof_areas.Areas_id',
                'partidasof_areas.NumeroEtiqueta',
                'partidasof_areas.FechaComienzo',
                'partidasof_areas.FechaTermina'
            )
            ->orderBy('partidasof_areas.NumeroEtiqueta')
            ->orderBy('partidasof_areas.Areas_id')
            ->get();
            $result = [];
            $previousArea = null;
            foreach ($TiemposMuertos as $item) {
                if ($previousArea !== null && $previousArea->NumeroEtiqueta == $item->NumeroEtiqueta) {
                    $startTime = new \Carbon\Carbon($previousArea->FechaTermina);
                    $endTime = new \Carbon\Carbon($item->FechaComienzo);
                    $timeDifference = $startTime->diffInSeconds($endTime); 
            
                    if ($previousArea->Areas_id + 1 == $item->Areas_id) {
                        $key = $previousArea->Areas_id . ',' . $item->Areas_id;
            
                        if (isset($result[$key])) {
                            $result[$key]['TiempoMuerto'] += $timeDifference;
                            $etiquetas = explode(',', $result[$key]['NumeroEtiqueta']);
                            $etiquetas = array_unique(array_merge($etiquetas, [$item->NumeroEtiqueta]));
                            $result[$key]['NumeroEtiqueta'] = implode(',', $etiquetas);
                        } else {
                            $result[$key] = [
                                'PartidasOf_id' => $previousArea->PartidasOf_id, 
                                'Areas_id' => $previousArea->Areas_id . ',' . $item->Areas_id,
                                'NumeroEtiqueta' => $previousArea->NumeroEtiqueta . ',' . $item->NumeroEtiqueta,
                                'TiempoMuerto' => $timeDifference
                            ];
                        }
                    }
                }
                $previousArea = $item;
            }
            $finalResult = array_values($result);























                
            $primerYUltimo = [];
            foreach ($areas as $areaId => $areaNombre) {
                $primerArea = $diasAreas->where('Areas_id', $areaId)->sortBy('NumeroEtiqueta')->first();
                $ultimoArea = $diasAreas->where('Areas_id', $areaId)->sortByDesc('NumeroEtiqueta')->first();
                $tiempoProduccion = 0;
                if ($primerArea && $ultimoArea) {
                    $fechaComienzoPrimer = Carbon::parse($primerArea->FechaComienzo);
                    $fechaTerminaUltimo = Carbon::parse($ultimoArea->FechaTermina);
                    $tiempoProduccion = $fechaComienzoPrimer->diffInSeconds($fechaTerminaUltimo);  
                }
        
                $primerYUltimo[$areaNombre] = [
                    'primer' => $primerArea,
                    'ultimo' => $ultimoArea,
                    'tiempoProduccion' => $tiempoProduccion  
                ];
            }
            $tiemposMuertosPorTransicion = [];
            $tiemposProduccionPorTransicion = [];
            $tiemposPorPieza = [];
            $areaKeys = array_keys($areas);
            for ($i = 0; $i < count($areaKeys) - 1; $i++) {
                $areaActual = $areaKeys[$i];
                $areaSiguiente = $areaKeys[$i + 1];
        
                $tiempoMuertoArea = 0;
                $tiempoProduccionAreaActual = 0;
                $tiempoProduccionAreaSiguiente = 0;
        
                if ($primerYUltimo[$areas[$areaActual]]['ultimo'] && $primerYUltimo[$areas[$areaSiguiente]]['primer']) {
                    $fechaUltimaAreaActual = Carbon::parse($primerYUltimo[$areas[$areaActual]]['ultimo']->FechaTermina);
                    $fechaPrimeraAreaSiguiente = Carbon::parse($primerYUltimo[$areas[$areaSiguiente]]['primer']->FechaComienzo);
                    Log::info("Última fecha de {$areas[$areaActual]}: " . $fechaUltimaAreaActual);
                    Log::info("Primera fecha de {$areas[$areaSiguiente]}: " . $fechaPrimeraAreaSiguiente);
                    $diferenciaTiempo = $fechaUltimaAreaActual->diffInSeconds($fechaPrimeraAreaSiguiente, false);
                    Log::info("Diferencia de tiempo entre {$areas[$areaActual]} y {$areas[$areaSiguiente]}: " . $diferenciaTiempo);
                    $tiempoMuertoArea = max(0, $diferenciaTiempo);
                }
                if ($primerYUltimo[$areas[$areaActual]]['primer'] && $primerYUltimo[$areas[$areaActual]]['ultimo']) {
                    $fechaComienzoPrimerAreaActual = Carbon::parse($primerYUltimo[$areas[$areaActual]]['primer']->FechaComienzo);
                    $fechaTerminaUltimaAreaActual = Carbon::parse($primerYUltimo[$areas[$areaActual]]['ultimo']->FechaTermina);
                    $tiempoProduccionAreaActual = $fechaComienzoPrimerAreaActual->diffInSeconds($fechaTerminaUltimaAreaActual);
                }
                if ($primerYUltimo[$areas[$areaSiguiente]]['primer'] && $primerYUltimo[$areas[$areaSiguiente]]['ultimo']) {
                    $fechaComienzoPrimerAreaSiguiente = Carbon::parse($primerYUltimo[$areas[$areaSiguiente]]['primer']->FechaComienzo);
                    $fechaTerminaUltimaAreaSiguiente = Carbon::parse($primerYUltimo[$areas[$areaSiguiente]]['ultimo']->FechaTermina);
                    $tiempoProduccionAreaSiguiente = $fechaComienzoPrimerAreaSiguiente->diffInSeconds($fechaTerminaUltimaAreaSiguiente);
                }
                $produccionAreaActual = $diasAreas->where('Areas_id', $areaActual)->sum('TotalSegundos');
                $produccionAreaSiguiente = $diasAreas->where('Areas_id', $areaSiguiente)->sum('TotalSegundos');
                $cantidadAreaActual = $diasAreas->where('Areas_id', $areaActual)->sum('Cantidad');
                $cantidadAreaSiguiente = $diasAreas->where('Areas_id', $areaSiguiente)->sum('Cantidad');
                
                $tiempoPorPiezaActual = $cantidadAreaActual > 0 ? $tiempoProduccionAreaActual / $cantidadAreaActual : 0;
                $tiempoPorPiezaSiguiente = $cantidadAreaSiguiente > 0 ? $produccionAreaSiguiente / $cantidadAreaSiguiente : 0;
                $tiemposPorPieza["{$areas[$areaActual]}"] = [
                    'tiempoPorPiezaActual' => $tiempoPorPiezaActual,
                    'tiempoPorPiezaSiguiente' => $tiempoPorPiezaSiguiente
                ];
        
                $tiemposMuertosPorTransicion["{$areas[$areaActual]}-{$areas[$areaSiguiente]}"] = $tiempoMuertoArea;
                $tiemposProduccionPorTransicion["{$areas[$areaActual]}-{$areas[$areaSiguiente]}"] = [
                    'produccionActual' => $produccionAreaActual,
                    'produccionSiguiente' => $produccionAreaSiguiente,
                    'tiempoProduccionActual' => $tiempoProduccionAreaActual,
                    'tiempoProduccionSiguiente' => $tiempoProduccionAreaSiguiente
                ];
                Log::info("Producción en {$areas[$areaActual]}: " . $produccionAreaActual);
                Log::info("Producción en {$areas[$areaSiguiente]}: " . $produccionAreaSiguiente);
                Log::info("Tiempo de producción en {$areas[$areaActual]}: " . $tiempoProduccionAreaActual);
                Log::info("Tiempo de producción en {$areas[$areaSiguiente]}: " . $tiempoProduccionAreaSiguiente);
            }
            return response()->json([
                'tiemposMuertosPorTransicion' => $tiemposMuertosPorTransicion,
                'tiemposProduccionPorTransicion' => $tiemposProduccionPorTransicion,
                'tiemposPorPieza' => $tiemposPorPieza,
                'TotalPiezas' => $TotalPiezas
            ]);






            $hoy = Carbon::now()->toDateString();
            $areas = [
                4 => 'Preparadodia',
                5 => 'Ensambledia',
                6 => 'Pulidodia',
                7 => 'Mediciondia',
                8 => 'Visualizaciondia',
                9 => 'Empaquedia'
            ];
            
            $TotalPiezas = DB::table('ordenfabricacion')
                ->whereDate('FechaEntrega', '=', $hoy)
                ->sum('CantidadTotal');
        
            $diasAreas = DB::table('ordenfabricacion')
                ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->leftJoin('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
                ->whereIn('partidasof_areas.Areas_id', array_keys($areas))  
                ->whereDate('partidasof_areas.FechaComienzo', '=', $hoy)
                ->select(
                    'partidasof_areas.NumeroEtiqueta',
                    'partidasof_areas.PartidasOF_id',
                    'partidasof_areas.Areas_id',
                    'partidasof_areas.FechaComienzo',
                    'partidasof_areas.FechaTermina',
                    DB::raw("TIMESTAMPDIFF(SECOND, partidasof_areas.FechaComienzo, partidasof_areas.FechaTermina) as TotalSegundos")
                )
                ->get();
        
            $primerYUltimo = [];
            foreach ($areas as $areaId => $areaNombre) {
                $primerArea = $diasAreas->where('Areas_id', $areaId)->sortBy('NumeroEtiqueta')->first();
                $ultimoArea = $diasAreas->where('Areas_id', $areaId)->sortByDesc('NumeroEtiqueta')->first();
                
                // Calcular la diferencia entre FechaComienzo y FechaTermina
                $tiempoProduccion = 0;
                if ($primerArea && $ultimoArea) {
                    $fechaComienzoPrimer = Carbon::parse($primerArea->FechaComienzo);
                    $fechaTerminaUltimo = Carbon::parse($ultimoArea->FechaTermina);
                    $tiempoProduccion = $fechaComienzoPrimer->diffInSeconds($fechaTerminaUltimo);
                }
        
                $primerYUltimo[$areaNombre] = [
                    'primer' => $primerArea,
                    'ultimo' => $ultimoArea,
                    'tiempoProduccion' => $tiempoProduccion  // Tiempo total de producción en segundos
                ];
            }
        
            return response()->json($primerYUltimo);


        */

    //}
    public function NumeroCompletadas($OrdenFabricacion,$Area){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $OrdenFabricacion=OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first();
        $PartidasOF=$OrdenFabricacion->PartidasOF;
        $Suma=0;
        if($OrdenFabricacion->Escaner == 1){
            foreach($PartidasOF as $Partidas){
                $PartidasOFAreasCompN=$Partidas->Areas()->where('Areas_id',$Area)->whereNotNull('FechaTermina')->where('TipoPartida','N')->SUM('Cantidad');
                $PartidasOFAreasCompRI=$Partidas->Areas()->where('Areas_id',$Area)->whereNull('FechaTermina')->where('TipoPartida','R')->SUM('Cantidad');
                $Suma+=$PartidasOFAreasCompN-$PartidasOFAreasCompRI;
            }
        }else{
            if($Area!=$this->AreaEspecialEmpaque ){
                foreach($PartidasOF as $Partidas){
                    $Suma +=$Partidas->Areas()->where('Areas_id', $Area)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                            ($Partidas->Areas()->where('Areas_id', $Area)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                           - $Partidas->Areas()->where('Areas_id', $Area)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                }
            }else{
                foreach($PartidasOF as $Partidas){
                    $PartidasOFAreasCompN=$Partidas->Areas()->where('Areas_id',$Area)->whereNotNull('FechaTermina')->where('TipoPartida','N')->SUM('Cantidad');
                    $PartidasOFAreasCompRI=$Partidas->Areas()->where('Areas_id',$Area)->whereNull('FechaTermina')->where('TipoPartida','R')->SUM('Cantidad');
                    $Suma+=$PartidasOFAreasCompN-$PartidasOFAreasCompRI;
                }
            }
        }
        return $Suma;
    }
    //dasboard operador 
    public function indexoperador(Request $request)
    {
        $manana = Carbon::now()->addDay()->format('Y-m-d H:i:s'); 
        $hoy = Carbon::now()->format('Y-m-d H:i:s');
        $avisos = DB::table('avisos')
            //->where('created_at','>=',$hoy)
            //->where('created_at','<=',$manana)
            ->whereBetween('fecha_envio', [$hoy, $manana])
            ->orderBy('created_at', 'desc')
            ->get();
        $linea = Linea::where('active', 1)->get();
        $user = Auth::user();
    
        if (!$user || !$user->active) {
            Auth::logout();
            return redirect()->route('login_view')->withErrors(['email' => 'Tu cuenta ha sido desactivada.']);
        }
    
        return view('HomeOperador', compact('user', 'linea', 'avisos'));
    }
    //vista sin permisos
    public function error(Request $request)
    {
        return view('Error');
            
        
    }
    public function lineas(Request $request)
    {
        $personal = DB::table('porcentajeplaneacion')
            ->join('linea', 'porcentajeplaneacion.Linea_id', '=', 'linea.id')
            ->whereDate('porcentajeplaneacion.FechaPlaneacion', today())
            ->where('linea.active', 1)
            ->select('linea.id as LineaId', 'linea.Nombre', 'porcentajeplaneacion.NumeroPersonas', 'porcentajeplaneacion.CantidadPlaneada')
            ->get();

        $TotalOfTotal = DB::table('porcentajeplaneacion')
            ->join('linea', 'porcentajeplaneacion.Linea_id', '=', 'linea.id')
            ->whereDate('FechaPlaneacion', today())
            ->where('linea.active', 1)
            ->select(
                'Linea_id',
                DB::raw('COALESCE(NumeroPersonas, 20) as NumeroPersonas'),
                DB::raw('COALESCE(CantidadPlaneada, 100) as CantidadTotal')
            )
            ->get();

        $indicador = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->whereDate('ordenfabricacion.FechaEntrega', today())
            ->where('partidasof_areas.Areas_id', 9)
            ->select(
                'ordenfabricacion.Linea_id',
                'OrdenFabricacion',
                'OrdenVenta_id',
                'partidasof_areas.Cantidad',
                'ordenfabricacion.Cerrada',
                'partidasof_areas.Areas_id',
                DB::raw('COALESCE(SUM(partidasof_areas.Cantidad), 0) as SumaCantidad')
            )
            ->groupBy('OrdenFabricacion', 'OrdenVenta_id', 'partidasof_areas.Cantidad', 'ordenfabricacion.Cerrada', 'partidasof_areas.Areas_id', 'ordenfabricacion.Linea_id')
            ->get();

        $lineas = DB::table('linea')
            ->where('active', 1)
            ->get();

        $totalOFcompletadas = 0;
        $totalSumaCantidad = 0;
        $totalFaltante = 0;
        $lineasData = [];

        foreach ($lineas as $linea) {
            $lineaId = $linea->id;
            $personalLinea = $personal->firstWhere('LineaId', $lineaId);
            $cantidadPersonas = $personalLinea->NumeroPersonas ?? 0;
            $cantidadPlaneada = $personalLinea->CantidadPlaneada ?? 0;

            $indicadorLinea = $indicador->where('Linea_id', $lineaId);
            $totalOFcompletadasLinea = $indicadorLinea->where('Cerrada', 1)->sum('SumaCantidad') ?? 0;
            $totalSumaCantidadLinea = $indicadorLinea->sum('SumaCantidad') ?? 0;
            $faltanteTotalLinea = max(($cantidadPlaneada - $totalSumaCantidadLinea), 0);

            $totalOFcompletadas += $totalOFcompletadasLinea;
            $totalSumaCantidad += $totalSumaCantidadLinea;
            $totalFaltante += $faltanteTotalLinea;

            $lineasData[] = [
                'id' => $lineaId,
                'cantidad_personas' => $cantidadPersonas,
                'estimado_piezas' => $cantidadPlaneada,
                'piezas_completadas' => $totalOFcompletadasLinea,
                'piezas_faltantes' => $faltanteTotalLinea,
                'porcentaje_completadas' => $cantidadPlaneada > 0 ? round(($totalOFcompletadasLinea / $cantidadPlaneada) * 100, 2) : 0,
                'porcentaje_faltantes' => $cantidadPlaneada > 0 ? round(($faltanteTotalLinea / $cantidadPlaneada) * 100, 2) : 0
            ];
        }

        $porcentajeCompletadas = $totalSumaCantidad > 0 ? round(($totalOFcompletadas / $totalSumaCantidad) * 100, 2) : 0;
        $porcentajeCerradas = $totalSumaCantidad > 0 ? round(($totalFaltante / $totalSumaCantidad) * 100, 2) : 0;

        return response()->json([
            'lineas' => $lineasData,
            'TotalOFcompletadas' => $totalOFcompletadas,
            'TotalOfTotal' => (int) $totalSumaCantidad,
            'faltanteTotal' => $totalFaltante,
            'PorcentajeCompletadas' => $porcentajeCompletadas,
            'porcentajeCerradas' => $porcentajeCerradas
        ]);
    }
    public function guardarAviso(Request $request)
    {
        // Valida la entrada del usuario
        $request->validate([
            'titulo' => 'nullable|string|max:255', 
            'aviso' => 'required|string',
        ]);

        // Inserta el aviso en la base de datos
        //Se le aumentan 24 de la fecha para saber cuanto va durar, solo dura 1 dia
        $Fecha = $request->input('fecha').' '.date('H:i:d');
        $Fecha = Carbon::parse($Fecha);
        $Fecha = $Fecha->addHours(24);
        $Fecha = $Fecha->format('Y-m-d H:i:s');
        DB::table('avisos')->insert([
            'titulo' => $request->input('titulo'),
            'contenido' => $request->input('aviso'),
            'fecha_envio' => $Fecha,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirige con mensaje de éxito
        return redirect()->back()->with('success', 'Aviso enviado correctamente.');
    }
    //Revisa la sesión y la cooki de sesion
    public function UpdateSession(){
        if(session()->get('inicio_sesion')){
            Session::put('last_activity', now());
            //Session::regenerate();
        }
        return session()->get('inicio_sesion');
        //return response()->json(['isValid' => Auth::check()]);
    }

}

    
    
 