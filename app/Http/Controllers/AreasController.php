<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\FuncionesGeneralesController;
use App\Models\OrdenFabricacion;
use App\Models\PartidasOF;
use App\Models\Partidas;
use App\Models\Emision;
use App\Models\Linea;
use App\Models\Partidasof_Areas;
use App\Models\Comentarios;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;


//use function PHPUnit\Framework\returnValue;
class AreasController extends Controller
{
    protected $funcionesGenerales;
    //Estas Areas se tratan de Manera diferente, por lo cual se definen aquí
    protected $AreaEspecialCorte;
    protected $AreaEspecialSuministro;
    protected $AreaEspecialTransicion;
    protected $AreaEspecialPulido;
    protected $AreaEspecialMontaje;
    protected $AreaEspecialEmpaque;
    protected $AreaEspecialClasificacion;
    public function __construct(FuncionesGeneralesController $funcionesGenerales){
        $this->funcionesGenerales = $funcionesGenerales;
        $this->AreaEspecialCorte = 2;//Corte
        $this->AreaEspecialSuministro = 3;//Suministro
        $this->AreaEspecialTransicion = 4;//Transición
        $this->AreaEspecialPulido = 9;//Pulido
        $this->AreaEspecialMontaje= 16;//Montaje
        $this->AreaEspecialEmpaque = 17;//Empaque
        $this->AreaEspecialClasificacion = 18;//Empaque
    }
    //Area 3 Suministro
    public function Suministro(){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        //EstatusEntrega==0 aun no iniciado; 1 igual a terminado
        $fecha = date('Y-m-d');
        $fechaAtras=date('Y-m-d', strtotime('-1 week', strtotime($fecha)));
        $fecham = Carbon::parse($fecha)->addDay();
        $Area=3;
         //Consulta traer Ordenes abiertas en esta estacion
        $PartidasOFA=PartidasOF::where('EstatusPartidaOFSuministro','0')->get();
        foreach($PartidasOFA as $key1=>$orden) {
            $ordenFabri=$orden->ordenFabricacion;
            if($ordenFabri->Cerrada == 1){
                $ordenesSAP1=$this->funcionesGenerales->Emisiones($ordenFabri->OrdenFabricacion);
                $ordenesSAP = array_filter($ordenesSAP1, function($item) {
                    return $item['Cantidad'] !== null;
                });
                $ordenesLocal=$ordenFabri->Emisions()->get();
                foreach($ordenesLocal as $ordenesLoc){
                    foreach ($ordenesSAP as $key => $item) {
                        if ($item["NoEmision"] == $ordenesLoc->NumEmision) {
                            unset($ordenesSAP[$key]);  // Eliminar el elemento del array
                            break;  // Detener el bucle después de eliminar
                        }
                    }
                }
                $orden['OrdenFaltantes']=count($ordenesSAP);
                $orden['idEncript'] = $this->funcionesGenerales->encrypt($orden->id);
                $orden['OrdenFabricacion']=$ordenFabri->OrdenFabricacion;
                $orden['Urgencia']=$ordenFabri->Urgencia;
                //$orden['TotalPartida']=$ordenFabri->PartidasOF->whereNotNull('FechaFinalizacion')->SUM('cantidad_partida')-$ordenFabri->PartidasOF->where('TipoPartida','R')->SUM('cantidad_partida');
                $orden['TotalPartida']=$ordenFabri->CantidadTotal;
                $Normal=0;
                $Retrabajo=0;
                $ordenFabri->PartidasOF;
                foreach($ordenFabri->PartidasOF as $partidasOF){
                    $Normal+=$partidasOF->Areas()->where('Areas_id',3)->where('TipoPartida','N')->SUM('Cantidad');
                    $Retrabajo+=$partidasOF->Areas()->where('Areas_id',3)->where('TipoPartida','R')->SUM('Cantidad');
                }
                $orden['Normal']=$Normal;
                $orden['Retrabajo']=$Retrabajo;
                $orden['Articulo']=$ordenFabri->Articulo;
                $orden['Descripcion']=$ordenFabri->Descripcion;
                $orden->id="";
           }else{
                unset($PartidasOFA[$key1]);
           }
        }
        //Consulta traer Ordenes cerradas en esta estacion
        /*$PartidasOFC = PartidasOF::Join('partidasof_areas','partidasof.id','=','partidasof_areas.PartidasOF_id')
                        ->select('partidasof_areas.*','partidasof.*')
                        ->where('partidasof_areas.Areas_id', 3)
                        ->where('EstatusPartidaOFSuministro','1')
                        ->orderByDesc('partidasof_areas.FechaTermina')
                        ->whereBetween('FechaTermina', [$fechaAtras.' 00:00:00', $fecham.' 00:00:00'])
                        ->get()
                        ->unique('PartidasOF_id');
        foreach($PartidasOFC as $key1=>$orden) {
            $ordenFabri=$orden->ordenFabricacion;
            $orden['idEncript'] = $this->funcionesGenerales->encrypt($orden->id);
            $orden['OrdenFabricacion']=$ordenFabri->OrdenFabricacion;
            $TotalCortes = $ordenFabri->PartidasOF->first();
            $TotalCortes = $TotalCortes->Areas()->where('Areas_id',2)->get();
            $TotalCortes = $TotalCortes->whereNotNull('pivot.FechaTermina')->SUM('pivot.Cantidad');
            $orden['TotalPartida']=$TotalCortes;//$ordenFabri->CantidadTotal;//PartidasOF->whereNotNull('FechaFinalizacion')->SUM('cantidad_partida')-$ordenFabri->PartidasOF->where('TipoPartida','R')->SUM('cantidad_partida');
            $orden['Articulo']=$ordenFabri->Articulo;
            $orden['Descripcion']=$ordenFabri->Descripcion;
            $orden->id="";
            $Normal=0;
            $Retrabajo=0;
            foreach($ordenFabri->PartidasOF as $partidasOF){
                $Normal+=$partidasOF->Areas()->where('Areas_id',3)->where('TipoPartida','N')->SUM('Cantidad');
                $Retrabajo+=$partidasOF->Areas()->where('Areas_id',3)->where('TipoPartida','R')->SUM('Cantidad');
            }
            $orden['Normal']=$Normal;
            $orden['Retrabajo']=$Retrabajo;
        }*/
        $Area=$this->funcionesGenerales->encrypt(3);
        $user = Auth::user();
        if ($user->hasPermission('Vista Suministro')) {
           
            return view('Areas.Suministro',compact('Area','PartidasOFA','fecha','fechaAtras'));
        }else {
           
            return redirect()->route('error.');
        }
    }
    public function SuministroGuardar(Request $request){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        //Verificar si todo corre de acuerdo a lo planeado, si no modificar esta funcion
        $id=$this->funcionesGenerales->decrypt($request->id);
        $emision=$request->emision;
        $retrabajo=$request->retrabajo;
        $Cantitadpiezas=$request->Cantitadpiezas;
        $OrdenFabricacion=OrdenFabricacion::find($id);
        if($OrdenFabricacion=="" OR $OrdenFabricacion==null){
            return response()->json([
                'status' => 'error',
                'message' =>'La Partida de  la Orden de Fabricación no existe!',
            ], 200);
        }else{
            if($emision=="" OR $emision==null){
                return response()->json([
                    'status' => 'errorEmision',
                    'message' =>'Partida no guardada, La orden de Emision es requerida!',
                ], 200);
            }
        }
        $contartotal=0;
        $PartidasOF = $OrdenFabricacion->PartidasOF()->first();
        foreach($OrdenFabricacion->PartidasOF()->get() as $PartidasOF){
            $Partidas=$PartidasOF->Areas()->where('Areas_id',3)->get();
            $contartotal+=$Partidas->where('pivot.TipoPartida','N')->SUM('pivot.Cantidad');
        }
        $contartotal+=$Cantitadpiezas;
        if($retrabajo=="false"){//Normal
            //Comprueba que no sobrepase el numero de piezas a de la PartidaOF solo si es Normal
            //Sumamos para ver la cantidad de cortes, se maneja como una partida normal si el número de cortes alcansa para lo suministrado
            $CantidadCortes = $OrdenFabricacion->CantidadTotal;
            $CantidadSuministro =  $PartidasOF->Areas()->where('TipoPartida','N')->where('Areas_id',$this->AreaEspecialSuministro)->get()->SUM('pivot.Cantidad');//whereNotNull('pivot.FechaTermina')->SUM('pivot.Cantidad');$OrdenFabricacion->CantidadTotal;
            $CantidadSuministro += $Cantitadpiezas;
            if($CantidadCortes<$CantidadSuministro){
                    return response()->json([
                        'status' => 'errorCantidada',
                        'message' =>'Partida no guardada, Cantidad solicitada no disponible!, por favor revisa si es partida normal o retrabajo.',
                    ], 200);
            }
            $TipoPartida='N';
        }else{//Retrabajo
            //Comprueba que no sobrepase el numero de piezas terminadas actualmente a de la PartidaOF solo si es Retrabajo
            $TipoPartida='R';
            $contartotalR=0;
            foreach($OrdenFabricacion->PartidasOF()->get() as $PartidasOF){
                $Partidas=$PartidasOF->Areas()->where('Areas_id',3)->get();
                $contartotalR+=($Partidas->where('pivot.TipoPartida','N')->whereNotNull('pivot.FechaTermina')->SUM('pivot.Cantidad'))-($Partidas->where('pivot.TipoPartida','R')->whereNull('pivot.FechaTermina')->SUM('pivot.Cantidad'));
            }
            $contartotalR;
            if($contartotalR<$Cantitadpiezas){
                return response()->json([
                    'status' => 'errorCantidada',
                    'message' =>'Partida no guardada, Cantidad solicitada no disponible, la cantidad solicitada tiene que ser menor a la cantidad de partidas terminadas!',
                ], 200);
            }
        }
        $data = [
            'Cantidad' => $Cantitadpiezas,
            'TipoPartida' => $TipoPartida, // N = Normal
            'FechaComienzo' => now(),
            'Linea_id' => 1,
            'NumeroEtiqueta' => 1,
            'Users_id' => $this->funcionesGenerales->InfoUsuario(),
        ];
        $PartidasOF->Areas()->attach(3, $data);
        $idPartidaOFArea=$PartidasOF->Areas()->orderBy('id','desc')->first();
        $emisionRegistro=Emision::where('NumEmision',$emision)->first();
        if($emisionRegistro==""){
            $PartidasOF->OrdenFabricacion->first()->id;
            $emisionregistro=new Emision();
            $emisionregistro->NumEmision=$emision;
            $emisionregistro->OrdenFabricacion_id=$id;//$PartidasOF->OrdenFabricacion->first()->id;
            $emisionregistro->Etapaid=$idPartidaOFArea['pivot']->id;
            $emisionregistro->EtapaEmision='S';
            $emisionregistro->save();
        }else{
            $emisionRegistro->OrdenFabricacion_id=$id;//$PartidasOF->OrdenFabricacion->first()->id;
            $emisionRegistro->Etapaid=$idPartidaOFArea['pivot']->id;
            $emisionRegistro->EtapaEmision='S';
            $emisionRegistro->save();
        }
        foreach($OrdenFabricacion->PartidasOF as $partida){
            $partida->EstatusPartidaOF=0;
            $partida->save();
        }
        $PartidasOF->EstatusPartidaOFSuministro=0;
        $PartidasOF->save();
        return response()->json([
            'status' => 'success',
            'message' =>'Partida guardada correctamente!',
            'OF' => $this->funcionesGenerales->encrypt($PartidasOF->id)
        ], 200);
    }
    public function SuministroRecargarTabla(){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        //PartidasOF->EstatusPartidaOF==0 aun no iniciado; 1 igual a terminado
        try {
            $PartidasOF=PartidasOF::where('EstatusPartidaOFSuministro','0')->get();
            $tabla="";
            foreach($PartidasOF as $key1=>$orden) {
                $ordenFabri=$orden->ordenFabricacion;
                if($ordenFabri->Cerrada == 1){
                        $ordenesSAP1=$this->funcionesGenerales->Emisiones($ordenFabri->OrdenFabricacion);
                        $ordenesSAP = array_filter($ordenesSAP1, function($item) {
                            return $item['Cantidad'] !== null;
                        });
                        $ordenesLocal=$ordenFabri->Emisions()->get();
                        foreach($ordenesLocal as $ordenesLoc){
                            foreach ($ordenesSAP as $key => $item) {
                                if ($item["NoEmision"] == $ordenesLoc->NumEmision) {
                                    unset($ordenesSAP[$key]);  // Eliminar el elemento del array
                                    break;  // Detener el bucle después de eliminar
                                }
                            }
                        }
                        $TotalPartida=$ordenFabri->CantidadTotal;//($ordenFabri->PartidasOF->whereNotNull('FechaFinalizacion')->SUM('cantidad_partida'))-($ordenFabri->PartidasOF->where('TipoPartida','R')->SUM('cantidad_partida'));
                        $tabla.='<tr';
                        if($ordenFabri->Urgencia=='U'){
                        $tabla.=' style="background:#8be0fc;" ';   
                        }
                        $tabla.='>
                                <td>'. $ordenFabri->OrdenFabricacion .'</td>
                                <td>'. $ordenFabri->Articulo .'</td>
                                <td>'. $ordenFabri->Descripcion.'</td>
                                <td>'.count($ordenesSAP).'</td>';
                                $Normal=0;
                                $Retrabajo=0;
                                foreach($ordenFabri->PartidasOF as $partidas){
                                    $Normal+=$partidas->Areas()->where('Areas_id',3)->where('TipoPartida','N')->SUM('Cantidad');
                                    $Retrabajo+=$partidas->Areas()->where('Areas_id',3)->where('TipoPartida','R')->SUM('Cantidad');
                                }
                                
                        $tabla.='<td>'. $Normal.'</td>
                                <td>'. $Retrabajo.'</td>
                                <td>'. $TotalPartida .'</td>
                                <td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Abierta</span></div></td>
                                <td><button class="btn btn-sm btn-outline-primary" onclick="Planear(\''.$this->funcionesGenerales->encrypt($orden->id).'\')">Detalles</button></td>
                            </tr>';

                }else{
                    unset($PartidasOFA[$key1]);
                }
            }
            return response()->json([
                    'status' => 'success',
                    'table' => $tabla,
                ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'table' => "",
            ], 500);
        }
    }
    public function SuministroRecargarTablaCerrada(Request $request){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        //PartidasOF->EstatusPartidaOF==0 aun no iniciado; 1 igual a terminado
        $fechaAtras=$request->fechainicio;
        $fecham=$request->fechafin;
        $fecham = Carbon::parse($fecham)->addDay();
        try {
            //Consulta traer Ordenes cerradas en esta estacion
            $PartidasOF = PartidasOF::Join('partidasof_areas','partidasof.id','=','partidasof_areas.PartidasOF_id')
                ->select('partidasof_areas.*','partidasof.*')
                ->where('partidasof_areas.Areas_id', 3)
                ->where('EstatusPartidaOFSuministro','1')
                ->orderByDesc('partidasof_areas.FechaTermina')
                ->whereBetween('FechaTermina', [$fechaAtras.' 00:00:00', $fecham.' 00:00:00'])
                ->get()
                ->unique('PartidasOF_id');
            $tabla="";
            foreach($PartidasOF as $orden) {
                $ordenFabri=$orden->ordenFabricacion;
                $Part = $ordenFabri->PartidasOF->first();

                $TotalCortes = $Part->Areas()->where('Areas_id',2)->get();
                $TotalCortes = $TotalCortes->whereNotNull('pivot.FechaTermina')->SUM('pivot.Cantidad');
                $orden['TotalPartida']=$TotalCortes;
                $Normal=0;
                $Retrabajo=0;
                foreach($ordenFabri->PartidasOF as $partidasOF){
                    $Normal+=$partidasOF->Areas()->where('Areas_id',3)->where('TipoPartida','N')->SUM('Cantidad');
                    $Retrabajo+=$partidasOF->Areas()->where('Areas_id',3)->where('TipoPartida','R')->SUM('Cantidad');
                }
                $orden['Normal']=$Normal;
                $orden['Retrabajo']=$Retrabajo;
                $TotalPartida=$Part->Areas()->where('Areas_id',3)->get()->count();
                $tabla.='<tr>
                        <td>'. $ordenFabri->OrdenFabricacion .'</td>
                        <td class="text-center">'. $TotalPartida .'</td>
                        <td>'. $ordenFabri->Articulo .'</td>
                        <td>'. $ordenFabri->Descripcion.'</td>
                        <td>'. $Normal.'</td>
                        <td>'. $Retrabajo.'</td>
                        <td>'. $TotalCortes .'</td>
                        <td>'. $orden->FechaTermina.'</td>
                        <td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-danger"><span class="fw-bold">Cerrada</span></div></td>
                        <td><button class="btn btn-sm btn-outline-primary" onclick="Detalles(\''.$this->funcionesGenerales->encrypt($orden->id).'\')">Detalles</button></td>
                    </tr>';
            }
            return response()->json([
                    'status' => 'success',
                    'table' => $tabla,
                ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'table' => "",
            ], 500);
        }
    }
    public function SuministroDatosModal(Request $request){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        //Partidas->Estatus==1 aun no iniciado; 2 igual a terminado
        $id=$this->funcionesGenerales->decrypt($request->id);
        $Area=3;
        $PartidasOF = PartidasOF::find($id);
        $OrdenFabricacion=OrdenFabricacion::find($PartidasOF->OrdenFabricacion_id);
        $Ordenfabricacionpartidas='<table id="TablePartidasModal" class="table table-sm fs--1 mb-0" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" colspan="8">Partidas</th>
                            </tr>
                            <tr>
                                <th class="text-center">Número Partida</th>
                                 <th class="text-center">Unidades Suministradas</th>
                                <th class="text-center">Tipo Partida</th>
                                <th class="text-center">Estatus</th>
                                <th class="text-center" colspan="2">Acciones</th>
                                <th class="text-center" colspan="2">Etiquetas</th>
                            </tr>
                        </thead>
                        <tbody>';
        $Ordenfabricacioninfo='<table class="table table-sm fs--1 mb-0" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" colspan="4">Orden de Fabricacion:  '.$OrdenFabricacion->OrdenFabricacion.'</th>
                            </tr>
                            <tr>
                                <th class="text-center" colspan="4">Orden de Venta:  '.$OrdenFabricacion->ordenVenta->OrdenVenta.'</th>
                            </tr>
                        </thead>
                        <tbody>';
        if(!($OrdenFabricacion==null || $OrdenFabricacion=="")){
            $id=$this->funcionesGenerales->encrypt($OrdenFabricacion->id);
            //$Partidas=$PartidasOF->Areas()->where('Areas_id',3)->get();
            $PartidaNormal=0;
            $PartidaRetrabajo=0;
            $PartidaTotal=0;
            $PartidasContar=0;
            $TotalCompletadas=0;
            foreach($OrdenFabricacion->PartidasOF as $Partida){
                $Partidas=$Partida->Areas()->where('Areas_id',3)->get();
                $PartidasContar+=$Partidas->count();
                $PartidaNormal+=$Partidas->where('pivot.TipoPartida','N')->whereNotNull('pivot.FechaTermina')->SUM('pivot.Cantidad');
                $PartidaRetrabajo+=$Partidas->where('pivot.TipoPartida','R')->whereNotNull('pivot.FechaTermina')->SUM('pivot.Cantidad');
                $TotalCortes  = $Partida->Areas()->where('Areas_id',2)->get();
                $TotalCortes  =$TotalCortes->whereNotNull('pivot.FechaTermina')->SUM('pivot.Cantidad');
            }
            $TotalCompletadas+=$PartidaNormal+$PartidaRetrabajo;
             if($OrdenFabricacion->Cerrada == 0){
                $Ordenfabricacioninfo.='<tr><th class="text-center" colspan="4">
                                        <div class="alert alert-danger d-flex align-items-center p-0 mx-0" role="alert">
                                            <span class="fas fa-times-circle text-danger fs-3 me-2"></span>
                                            <p class="mb-0 flex-1">La Orden de Fabricacion se cerro de manera manual.</p>
                                        </div>
                                    </th></tr>'; 
            }
            $Ordenfabricacioninfo.='
                            <tr>
                                <th class="table-active">Articulo</th>
                                <th class="text-center">'.$OrdenFabricacion->Articulo.'</th>
                                <th class="table-active" colspan="1">Fecha Planeación</th>
                                <td class="text-center" colspan="3">'.$OrdenFabricacion->FechaEntrega.'</td>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="1">Cantidad Total Orden Fabricación</th>
                                <th class="text-center" colspan="1">'.$OrdenFabricacion->CantidadTotal.'</th>
                                <th class="table-active" colspan="1">Piezas completadas </th>
                                <th class="text-center" colspan="1">'.$TotalCompletadas.'</th>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="1">Piezas Entrada Normal</th>
                                <th class="text-center" colspan="1">'.$PartidaNormal.'</th>
                                <th class="table-active" colspan="1" style="background-color: rgb(255, 59, 59); color: white;">Piezas Entrada Retrabajo</th>
                                <th class="text-center" colspan="1">'.$PartidaRetrabajo.'</th>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="1">Descripción</th>
                                <td class="text-center" colspan="3">'.$OrdenFabricacion->Descripcion.'</td>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="1">Número Actual de Cortes</th>
                                <th class="text-center" colspan="3">'.$TotalCortes.'</th>
                            </tr>
                            ';
        
            if($PartidasContar==0){
                $Ordenfabricacionpartidas.='<tr>
                                            <td class="text-center" colspan="7">Aún no existen partidas creadas</td>
                                        </tr>';
            }else{
                foreach($OrdenFabricacion->PartidasOF as $key=>$Partida){
                    $Partida=$Partida->Areas()->where('Areas_id',3)->get();
                    foreach($Partida as $key=>$parti){
                        $Ordenfabricacionpartidas.='<tr>
                        <th class="text-center">'.($key+1).'</th>';
                        $Ordenfabricacionpartidas.='<td class="text-center">'.$parti['pivot']->Cantidad.'</td>';
                        if($parti['pivot']->TipoPartida=='R'){
                            $Ordenfabricacionpartidas.='<td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Retrabajo</span></div></td>';
                        }else{
                            $Ordenfabricacionpartidas.='<td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Normal</span></div></td>';
                        }
                        if(!($parti['pivot']->FechaTermina=='' || $parti['pivot']->FechaTermina==null)){
                            $Ordenfabricacionpartidas.='<td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-danger"><span class="fw-bold">Cerrada</span></div></td>';
                        }else{
                            $Ordenfabricacionpartidas.='<td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Abierta</span></div></td>';
                        }
                        if ($request->has('detalles')) {
                            $Ordenfabricacionpartidas.=' <td class="text-center"></td><td></td>';
                        }else{
                            // Mostrar botones solo si FechaTermina es NULL o está vacío
                            if($parti['pivot']->FechaTermina == '' || $parti['pivot']->FechaTermina == null){
                                $Ordenfabricacionpartidas .= '<td class="text-center">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill me-1 mb-1 px-3 py-1" 
                                        type="button" 
                                        onclick="Cancelar(\'' . $this->funcionesGenerales->encrypt($parti['pivot']->id) . '\')">
                                        Cancelar
                                    </button>
                                </td>            
                                <td>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill me-1 mb-1 px-3 py-1" 
                                        type="button" 
                                        onclick="Finalizar(\'' . $this->funcionesGenerales->encrypt($parti['pivot']->id) . '\')">
                                        Finalizar
                                    </button>
                                </td>
                                ';
                            }else{
                                $Ordenfabricacionpartidas .= ' <td class="text-center"></td><td></td>';
                            }
                        }
                        if($OrdenFabricacion->Corte==0){
                            $Ordenfabricacionpartidas.='<td ><button class="btn btn-link" onclick="etiquetaColor(\''.$this->funcionesGenerales->encrypt($Partida[$key]['pivot']->id).'\',1)" type="button">40-4.5X2.5 <i class="fas fa-download"></i></button></td>
                                                        <td ><button class="btn btn-link" onclick="etiquetaColor(\''.$this->funcionesGenerales->encrypt($Partida[$key]['pivot']->id).'\',3)" type="button">1-11X2 <i class="fas fa-download"></i></button></td>';
                        }else{$Ordenfabricacionpartidas.='<td class="text-center"></td><td class="text-center"></td>';}
                        $Ordenfabricacionpartidas.='</tr>';
                    }
                }
            }
            
        }else{
            $Ordenfabricacioninfo.='<tr>
                                        <td class="text-center" colspan="7">Datos no encontrados</td>
                                    </tr>';
            $Ordenfabricacionpartidas.='<tr>
                                        <td class="text-center" colspan="7">Aún no existen partidas creadas</td>
                                    </tr>';
        }
        $Ordenfabricacioninfo.='</tbody></table>';
        return response()->json([
            'status' => 'success',
            'statusOF' =>$OrdenFabricacion->Cerrada,
            'Ordenfabricacioninfo' => $Ordenfabricacioninfo,
            'Ordenfabricacionpartidas' => $Ordenfabricacionpartidas,
            'idPartidaOF' => $this->funcionesGenerales->encrypt($OrdenFabricacion->id)
        ], 200);
    }
    public function SuministroEmision(Request $request){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $id=$this->funcionesGenerales->decrypt($request->id);
        $OrdenFabricacion=OrdenFabricacion::where('id','=',$id)->first();
        if($OrdenFabricacion==""){
            return response()->json([
                'status' => 'empty',
                'table' => '<option selected="" disabled>Orden Fabricacion no encontrada</option>',
            ], 500);
        }else{
            $opciones='<option selected disabled>Selecciona una Emisión de producción</option>';
            $Emisiones=$this->funcionesGenerales->Emisiones($OrdenFabricacion->OrdenFabricacion);
                foreach ($Emisiones as $Emision){
                    $Emisiondatos=$OrdenFabricacion->Emisions()->where('NumEmision',$Emision['NoEmision'])->where('EtapaEmision','S')->first();
                        if($Emisiondatos==""){
                            $Cantidad=isset($Emision['Cantidad'])?$Emision['Cantidad']:0;
                            if($Cantidad!=0){
                                $opciones.='<option value="'.$Emision['NoEmision'].'" data-cantidad="'.$Cantidad.'">'.$Emision['NoEmision'].'</option>';
                            }
                        }
                }
            return response()->json([
                'status' => 'success',
                'opciones' => $opciones,
            ], 200);
        }
    }
    public function SuministroCancelar(Request $request){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $id=$this->funcionesGenerales->decrypt($request->id);
        $PartidaOF_Areas=DB::table('partidasof_areas')->where('id', $id)->first();
        if($PartidaOF_Areas==""){
            return response()->json([
                'status' => 'errornoexiste',
                'message' =>'Error al cancelar,No existe la Partida!',
            ], 200);
        }
        if(!($PartidaOF_Areas->FechaTermina=="" OR $PartidaOF_Areas->FechaTermina==null)){
            return response()->json([
                'status' => 'errorfinalizada',
                'message' =>'Error al Cancelar,La partida ya se encuentra finalizada!',
            ], 200);
        }
        $Emision=Emision::where('EtapaEmision','S')->where('Etapaid',$id)->first();
        if($Emision!=""){
            $Emision->delete();
        }
        DB::table('partidasof_areas')->where('id', $id)->delete();
        $PartidaOF=PartidasOF::find($PartidaOF_Areas->PartidasOF_id);
        $OrdenFabricacion=$PartidaOF->OrdenFabricacion;
        $CantidadTotal=$OrdenFabricacion->PartidasOF->SUM('cantidad_partida');
        $PartidasOF_AreasN=0;
        $PartidasOF_Areas=0;
        foreach($OrdenFabricacion->PartidasOF as $partida){
            $PartidasOF_AreasN+=$partida->Areas()->where('Areas_id',3)->where('TipoPartida','N')->SUM('Cantidad');
            $PartidasOF_Areas+=$partida->Areas()->where('Areas_id',3)->whereNull('FechaTermina')->count();
        }
        if($PartidasOF_Areas==0 && $PartidasOF_AreasN==$CantidadTotal){
            foreach($OrdenFabricacion->PartidasOF as $partida){
                $partida->EstatusPartidaOF=1;
                $partida->save();
            }
            $PartidasOF = $OrdenFabricacion->PartidasOF->first();
            $PartidasOF->EstatusPartidaOFSuministro = '1';
            $PartidasOF->save();

        }
        return response()->json([
            'status' => 'success',
            'message' =>'Partida Cancelada correctamente!',
            'OF' => $this->funcionesGenerales->encrypt($PartidaOF_Areas->PartidasOF_id)
        ], 200);
    }
    public function SuministroFinalizar(Request $request){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $id=$this->funcionesGenerales->decrypt($request->id);
        $PartidaOF_Areas=Partidasof_Areas::where('id', $id)->first();
        if($PartidaOF_Areas==""){
            return response()->json([
                'status' => 'errornoexiste',
                'message' =>'Error no se puede Finalizar, No existe la Partida!',
            ], 200);
        }
        if(!($PartidaOF_Areas->FechaTermina == "" || $PartidaOF_Areas->FechaTermina == null)){
            return response()->json([
                'status' => 'errorfinalizada',
                'message' =>'Ocurrio un error!, La partida ya se encuentra finalizada',
            ]);
        }
        $fechaHoy=date('Y-m-d H:i:s');
        //DB::table('partidasof_areas')->where('id', $id)->update(['FechaTermina' => $fechaHoy]);
        $Partidasof_Areas = Partidasof_Areas::find($id);
        $Partidasof_Areas->FechaTermina = $fechaHoy;
        $Partidasof_Areas->save();
        $PartidaOF=PartidasOF::where('id',$PartidaOF_Areas->PartidasOF_id)->first();
        $OrdenFabricacion=$PartidaOF->OrdenFabricacion;

        $CantidadTotal=$OrdenFabricacion->PartidasOF->SUM('cantidad_partida')-$OrdenFabricacion->PartidasOF->where('TipoPartida','R')->SUM('cantidad_partida');
        $PartidasOF_AreasN=0;
        $PartidasOF_Areas=0;
        foreach($OrdenFabricacion->PartidasOF as $partida){
            $PartidasOF_AreasN+=$partida->Areas()->where('Areas_id',3)->where('TipoPartida','N')->SUM('Cantidad');
            $PartidasOF_Areas+=$partida->Areas()->where('Areas_id',3)->whereNull('FechaTermina')->count();
        }
        if($PartidasOF_Areas==0 && $PartidasOF_AreasN>=$CantidadTotal){
            foreach($OrdenFabricacion->PartidasOF as $partida){
                $partida->EstatusPartidaOF=1;
                $partida->save();
            }
            $PartidasOF=$OrdenFabricacion->PartidasOF->first();
            $PartidasOF->EstatusPartidaOFSuministro = 1;
            $PartidasOF->save();
            //
        }
        return response()->json([
            'DatosEjemplo' => $PartidasOF_AreasN."   ".$PartidasOF_Areas,
            'status' => 'success',
            'message' =>'Partida finalizada correctamente!',
            'OF' => $this->funcionesGenerales->encrypt($PartidaOF_Areas->PartidasOF_id)
        ], 200);
    }
    public function BuscarSuministro(Request $request){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $OF=$request->OF;
        if(!$OF==""){
            $BuscarSuministro = OrdenFabricacion::where('OrdenFabricacion', 'LIKE', '%'.$OF.'%')->get();
        }
        $opciones="";
        if($BuscarSuministro->count()==0){
            $opciones.='<a class="list-group-item list-group-item-action">Orden de Fabricacion no encontrada</a>';
        }else{
            foreach($BuscarSuministro as $ofSuministro){
                $ofNumPartida=$ofSuministro->PartidasOF->unique('OrdenFabricacion_id');
                    foreach($ofNumPartida as $key=>$NumPartida){
                        if($key==0){
                            $opciones.='<a href="#" class="m-0 p-0 list-group-item list-group-item-action active" onclick="RetrabajoMostrarOFBuscarModal(\''.$this->funcionesGenerales->encrypt($NumPartida->id).'\')">'.$ofSuministro->OrdenFabricacion.'</a>';
                        }else{
                            $opciones.='<a href="#" class="m-0 p-0 list-group-item list-group-item-action" onclick="RetrabajoMostrarOFBuscarModal(\''.$this->funcionesGenerales->encrypt($NumPartida->id).'\')">'.$ofSuministro->OrdenFabricacion.'</a>';
                        }
                    }
            }
        }
        return $opciones;
    }
    //Area 4 Preparado
    /*public function Preparado(){
        $user = Auth::user();
        // Verifica si el usuario tiene el permiso necesario
        if ($user->hasPermission('Vista Preparado')) {
            $AreaOriginal = 4;
            $Area = $this->funcionesGenerales->encrypt($AreaOriginal);
            $Registros = $this->OrdenFabricacionPendiente($AreaOriginal - 1);
            foreach ($Registros as $key => $registro) {
                $Area4 = PartidasOF::find($registro->partidasOF_id);
                if($registro->Escaner==1){
                    $NumeroActuales = $Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') -
                                        $Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                    if ($NumeroActuales == $Area4->cantidad_partida) {
                        unset($Registros[$key]);
                    }
                }else{
                    //if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=', 'F')->count()!=0){
                    $NumeroActuales =$Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                        ($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                       - $Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                    //}
                    if ($NumeroActuales==$Area4->cantidad_partida) {
                        unset($Registros[$key]);
                    }
                }
            }
            foreach ($Registros as $key => $registro) {
                $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
                $Linea = $OrdenFabricacion->Linea()->first();
                $SumarActual = 0;
                $TotalPendiente = 0;
                $NumeroPartidasTodas = 0;
                $banderaSinRegistros=0;
                foreach ($OrdenFabricacion->PartidasOF as $Partidas) {
                    if($OrdenFabricacion->Escaner==1){
                        $SumarActual += $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaOriginal - 1)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaOriginal - 1)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                    }else{
                        //En esta parte el codigo es diferente al de las demas
                        //Sacamos la cantidad total de las piezas que ya pasaron
                        $SumarActual +=$Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                     ($Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                    - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        //Cantidad Pendiente que paso de una Area anterior
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaOriginal - 1)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                    - $Partidas->Areas()->where('Areas_id', $AreaOriginal - 1)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','=', 'F')->count()==0){
                            $banderaSinRegistros=1;
                        }
                    }
                }
                $registro['NumeroActuales'] = $SumarActual;
                $registro['TotalPendiente'] = $TotalPendiente;
                $registro['Linea'] = $Linea->NumeroLinea;
                $registro['ColorLinea'] = $Linea->ColorLinea;
                if ($SumarActual == $OrdenFabricacion->CantidadTotal AND $banderaSinRegistros==0) {
                    unset($Registros[$key]);
                }
            }
            return view('Areas.Preparado', compact('Area', 'Registros'));
        } else {
            return redirect()->route('error.');
        }
    }*/
    //Transicion 4
    public function Transicion(){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        // Verifica si el usuario tiene el permiso necesario
        if ($user->hasPermission('Vista Transición')) {
            $AreaOriginal = 4;
            $Area = $this->funcionesGenerales->encrypt($AreaOriginal);
            /*$Registros = $this->OrdenFabricacionPendiente($AreaOriginal - 1);
            foreach ($Registros as $key => $registro) {
                $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
                $Linea = $OrdenFabricacion->Linea()->first();
                $SumarActual = 0;
                $TotalPendiente = 0;
                $NumeroPartidasTodas = 0;
                $banderaSinRegistros=0;
                $AreaAnterior=$this->AreaAnteriorregistros($AreaOriginal, $OrdenFabricacion->OrdenFabricacion);
                foreach ($OrdenFabricacion->PartidasOF as $Partidas) {
                    $Area4 = PartidasOF::find($Partidas->id);
                    if($AreaAnterior==$this->AreaEspecialSuministro){
                        if($OrdenFabricacion->Escaner==1){
                            $SumarActual += $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        }else{
                            //En esta parte el codigo es diferente al de las demas
                            //Sacamos la cantidad total de las piezas que ya pasaron
                            $SumarActual +=$Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                         ($Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                        - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            //Cantidad Pendiente que paso de una Area anterior
                            $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                        - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','=', 'F')->count()==0){
                                $banderaSinRegistros=1;
                            }
                        }
                    }else{
                        if($OrdenFabricacion->Escaner==1){
                            //$banderaSinRegistros=0;
                            $SumarActual += $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        }else{
                            //Sacamos la cantidad total de las piezas que ya pasaron
                            $SumarActual +=$Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                         ($Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                        - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            //Cantidad Pendiente que paso de una Area anterior
                            $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                                ($Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                                - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','=', 'F')->count()==0){
                                $banderaSinRegistros=10;
                            }
                        }
                    }
                }
                $registro['NumeroActuales'] = $SumarActual;
                $registro['TotalPendiente'] = $TotalPendiente;
                $registro['Linea'] = $Linea->NumeroLinea;
                $registro['ColorLinea'] = $Linea->ColorLinea;
                $registro['Area'] = $AreaAnterior;
                if($TotalPendiente==0){
                    unset($Registros[$key]);
                }
                if ($SumarActual == $TotalPendiente AND $banderaSinRegistros==0) {
                    unset($Registros[$key]);
                }
            }
            foreach($Registros as $key => $registro){
                $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
                $PartidasOF = $OrdenFabricacion->PartidasOF()->first();
                $PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
                if($AreaAnterior <=  $this->AreaEspecialSuministro){
                    $PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
                }else{$PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$AreaAnterior)->get();}
                if($PartidasOFArea->count() == 0){
                    unset($Registros[$key]);
                }else{
                    $PartidasOFAreaUnique = $PartidasOFArea->unique('pivot.Linea_id');//Sacamos las Lineas en las que tiene registros esta Area  
                    foreach($PartidasOFAreaUnique as $key1 => $POFA){
                        if($AreaAnterior <= $this->AreaEspecialSuministro){
                            $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id',$this->AreaEspecialClasificacion)->get()->sum('pivot.Cantidad');
                            if($OrdenFabricacion->Escaner==1){
                                $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                    - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');;
                            }else{
                                $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                    ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                    - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            }
                            $POFA['Anterior'] = $SumarAnterior;
                            $POFA['Actual'] = $SumarActual;
                            $Linea = Linea::find($POFA['pivot']->Linea_id);
                            $POFA['Linea'] = $Linea->NumeroLinea;
                            $POFA['ColorLinea'] = $Linea->ColorLinea;
                            if($SumarAnterior<=$SumarActual){
                                unset($PartidasOFAreaUnique[$key1]);
                            }
                        }else{
                            if($OrdenFabricacion->Escaner==1){
                                $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                    - $Partidas->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                                $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                    - $Partidas->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            }else{
                                $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                             ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                            - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                                //Cantidad Pendiente que paso de una Area anterior
                                $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                                    ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                                    - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            }
                            $POFA['Anterior'] = $SumarAnterior;
                            $POFA['Actual'] = $SumarActual;
                            $Linea = Linea::find($POFA['pivot']->Linea_id);
                            $POFA['Linea'] = $Linea->NumeroLinea;
                            $POFA['ColorLinea'] = $Linea->ColorLinea;
                            if($SumarAnterior<=$SumarActual){
                                unset($PartidasOFAreaUnique[$key1]);
                            }
                        }
                        if($PartidasOFAreaUnique->count()==0){
                            unset($Registros[$key]);
                        }
                    }
                    if($PartidasOFAreaUnique->count()>0){
                        $Registros[$key]['PartidasOFFaltantes']=$PartidasOFAreaUnique;
                    }
                    //return$PartidasOFComp = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
                }
            }*/
            $Lineas=Linea::where('id','!=',1)->get();
            return view('Areas.Transicion', compact('Area','Lineas'));
        } else {
            return redirect()->route('error.');
        }
    }
    //Preparado 5
    public function Preparado(){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if ($user->hasPermission('Vista Preparado')) {
        $AreaOriginal=5;
        $Area = $this->funcionesGenerales->encrypt($AreaOriginal);
        /*$Registros = $this->OrdenFabricacionPendiente($AreaOriginal - 1);
        foreach ($Registros as $key => $registro) {
            $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
            $Linea = $OrdenFabricacion->Linea()->first();
            $SumarActual = 0;
            $TotalPendiente = 0;
            $NumeroPartidasTodas = 0;
            $banderaSinRegistros=0;
            $AreaAnterior=$this->AreaAnteriorregistros($AreaOriginal, $OrdenFabricacion->OrdenFabricacion);
            foreach ($OrdenFabricacion->PartidasOF as $Partidas) {
                $Area4 = PartidasOF::find($Partidas->id);
                if($AreaAnterior==$this->AreaEspecialSuministro){
                    if($OrdenFabricacion->Escaner==1){
                        $SumarActual += $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                    }else{
                        //En esta parte el codigo es diferente al de las demas
                        //Sacamos la cantidad total de las piezas que ya pasaron
                        $SumarActual +=$Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                     ($Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                    - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        //Cantidad Pendiente que paso de una Area anterior
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                    - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','=', 'F')->count()==0){
                            $banderaSinRegistros=1;
                        }
                    }
                }else{
                    if($OrdenFabricacion->Escaner==1){
                        //$banderaSinRegistros=0;
                        $SumarActual += $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                    }else{
                        //Sacamos la cantidad total de las piezas que ya pasaron
                        $SumarActual +=$Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                     ($Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                    - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        //Cantidad Pendiente que paso de una Area anterior
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                            ($Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                            - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','=', 'F')->count()==0){
                            $banderaSinRegistros=10;
                        }
                    }
                }
            }
            $registro['NumeroActuales'] = $SumarActual;
            $registro['TotalPendiente'] = $TotalPendiente;
            $registro['Linea'] = $Linea->NumeroLinea;
            $registro['ColorLinea'] = $Linea->ColorLinea;
            $registro['Area'] = $AreaAnterior;
            if($TotalPendiente==0){
                unset($Registros[$key]);
            }
            if ($SumarActual == $TotalPendiente AND $banderaSinRegistros==0) {
                unset($Registros[$key]);
            }
        }
        foreach($Registros as $key => $registro){
            $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
            $PartidasOF = $OrdenFabricacion->PartidasOF()->first();
            $PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
            if($AreaAnterior <=  $this->AreaEspecialSuministro){
                $PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
            }else{$PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$AreaAnterior)->get();}
            if($PartidasOFArea->count() == 0){
                unset($Registros[$key]);
            }else{
                $PartidasOFAreaUnique = $PartidasOFArea->unique('pivot.Linea_id');//Sacamos las Lineas en las que tiene registros esta Area  
                foreach($PartidasOFAreaUnique as $key1 => $POFA){
                    if($AreaAnterior <= $this->AreaEspecialSuministro){
                        $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id',$this->AreaEspecialClasificacion)->get()->sum('pivot.Cantidad');
                        if($OrdenFabricacion->Escaner==1){
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');;
                        }else{
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        }
                        $POFA['Anterior'] = $SumarAnterior;
                        $POFA['Actual'] = $SumarActual;
                        $Linea = Linea::find($POFA['pivot']->Linea_id);
                        $POFA['Linea'] = $Linea->NumeroLinea;
                        $POFA['ColorLinea'] = $Linea->ColorLinea;
                        if($SumarAnterior<=$SumarActual){
                            unset($PartidasOFAreaUnique[$key1]);
                        }
                    }else{
                        if($OrdenFabricacion->Escaner==1){
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        }else{
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                         ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                        - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            //Cantidad Pendiente que paso de una Area anterior
                            $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                                ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                                - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        }
                        $POFA['Anterior'] = $SumarAnterior;
                        $POFA['Actual'] = $SumarActual;
                        $Linea = Linea::find($POFA['pivot']->Linea_id);
                        $POFA['Linea'] = $Linea->NumeroLinea;
                        $POFA['ColorLinea'] = $Linea->ColorLinea;
                        if($SumarAnterior<=$SumarActual){
                            unset($PartidasOFAreaUnique[$key1]);
                        }
                    }
                    if($PartidasOFAreaUnique->count()==0){
                        unset($Registros[$key]);
                    }
                }
                if($PartidasOFAreaUnique->count()>0){
                    $Registros[$key]['PartidasOFFaltantes']=$PartidasOFAreaUnique;
                }
                //return$PartidasOFComp = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
            }
        }*/
            $Lineas=Linea::where('id','!=',1)->get();
            return view('Areas.Preparado',compact('Area','Lineas'));
        //return view('Areas.Preparado',compact('Area','Registros','Lineas'));
        }else{
            return redirect()->route('error.');
        }
    }
    /*public function Preparado(){
        $user = Auth::user();
        // Verifica si el usuario tiene el permiso necesario
        if ($user->hasPermission('Vista Preparado')) {
            $AreaOriginal = 4;
            $Area = $this->funcionesGenerales->encrypt($AreaOriginal);
            $Registros = $this->OrdenFabricacionPendiente($AreaOriginal - 1);
            /*foreach ($Registros as $key => $registro) {
                $Area4 = PartidasOF::find($registro->partidasOF_id);
                if($registro->Escaner==1){
                    $NumeroActuales = $Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') -
                                        $Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                    if ($NumeroActuales == $Area4->cantidad_partida) {
                        unset($Registros[$key]);
                    }
                }else{
                    //if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=', 'F')->count()!=0){
                    $NumeroActuales =$Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                        ($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                       - $Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                    //}
                    if ($NumeroActuales==$Area4->cantidad_partida) {
                        unset($Registros[$key]);
                    }
                }
            }*/
            /*foreach ($Registros as $key => $registro) {
                $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
                $Linea = $OrdenFabricacion->Linea()->first();
                $SumarActual = 0;
                $TotalPendiente = 0;
                $NumeroPartidasTodas = 0;
                $banderaSinRegistros=0;
                $AreaAnterior=$this->AreaAnteriorregistros($AreaOriginal, $OrdenFabricacion->OrdenFabricacion);
                foreach ($OrdenFabricacion->PartidasOF as $Partidas) {
                    $Area4 = PartidasOF::find($Partidas->id);
                    if($OrdenFabricacion->Escaner==1){
                        $SumarActual += $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                    }else{
                        //En esta parte el codigo es diferente al de las demas
                        //Sacamos la cantidad total de las piezas que ya pasaron
                        $SumarActual +=$Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                     ($Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                    - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        //Cantidad Pendiente que paso de una Area anterior
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                    - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','=', 'F')->count()==0){
                            $banderaSinRegistros=1;
                        }
                    }
                }
                $registro['NumeroActuales'] = $SumarActual;
                $registro['TotalPendiente'] = $TotalPendiente;
                $registro['Linea'] = $Linea->NumeroLinea;
                $registro['ColorLinea'] = $Linea->ColorLinea;
                if($TotalPendiente==0){
                    unset($Registros[$key]);
                }
                if ($SumarActual == $OrdenFabricacion->CantidadTotal AND $banderaSinRegistros==0) {
                    unset($Registros[$key]);
                }
            }
            return view('Areas.Preparado', compact('Area', 'Registros'));
        } else {
            return redirect()->route('error.');
        }
    }*/
    public function PreparadoBuscar(Request $request){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if ($request->has('Confirmacion')) {
            $confirmacion=1;
        }else{
            $confirmacion=0;
        }
        $Codigo = $request->Codigo;
        $Inicio = $request->Inicio;
        $Finalizar = $request->Finalizar;
        $NumeroLinea = $request->Linea;
        $NumeroLinea = Linea::where('NumeroLinea',$NumeroLinea)->first();
        $TipoEscaneo = "";
        if($NumeroLinea == ""){
            return response()->json([
                'tabla' => "",
                'Escaner' => "",
                'status' => "ErrorLínea",
                'CantidadTotal' => "",
                'CantidadCompletada' => 4,
                'OF' => ''

            ]);
        }
        $NumeroLinea = $NumeroLinea->id;
        $Area = $this->funcionesGenerales->decrypt($request->Area);
        $CodigoPartes = explode("-", $Codigo);
        $CodigoTam = count($CodigoPartes);
        $TipoEscanerrespuesta=0;
        $menu="";
        $Escaner="";
        $CantidadCompletada=0;
        $EscanerExiste=0;
        $NumeroBloque=0;
        $EstatusBloque = "";
        $PartidasFaltantesList='<li class="list-group-item d-flex justify-content-between align-items-center">Núm. Línea<span class="badge badge-light-danger rounded-pill">Faltantes</span><span class="badge badge-light-success rounded-pill">Completadas</span><span class="badge badge-light-primary rounded-pill">Total</span></li>';
        //Valida si el codigo es aceptado tiene que ser mayor a 2
        //if(($CodigoTam==3 && !($CodigoPartes[2]=="" || $CodigoPartes[2]==0)) || $CodigoTam==2){
        if($CodigoTam==3 || $CodigoTam==2){
            $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->first();
            if($datos=="" OR $datos==null){
                return response()->json([
                    'tabla' => $menu,
                    'Escaner' => $Escaner,
                    'status' => "empty",
                    'CantidadTotal' => "",
                    'CantidadCompletada' => $CantidadCompletada,
                    'OF' => $CodigoPartes[0]
        
                ]);
            }else{
                if($Area != $this->AreaEspecialMontaje){
                    if($datos->Cerrada == 0){//Valida que la Orden de fabricacion no se encuentre cerrada
                        return response()->json([
                            'tabla' => $menu,
                            'Escaner' => "",
                            'status' => "Cerrada",
                            'CantidadTotal' => "",
                            'CantidadCompletada' => 4,
                            'OF' => $CodigoPartes[0]
            
                        ]);
                    }
                }
                $status = 'success'; 
                $CantidadTotal=$datos->CantidadTotal;
                //Variable  guarda el valor de Escaner para saber si es no 0=No escaner 1=escaner
                $Escaner=$datos->Escaner;
                if($CodigoTam==3 || $CodigoTam==2){
                    //Comprobamos que la etiqueta si coincida con su numero de parte
                    if($Escaner==1 && isset($CodigoPartes[2])){
                        $CodigoValido=$this->ComprobarNumEtiqueta($CodigoPartes,$Area);
                        if($CodigoValido==0){
                            return response()->json([
                                'tabla' => $menu,
                                'Escaner' => "",
                                'status' => "NoExiste",
                                'CantidadTotal' => "",
                                'CantidadCompletada' => 4,
                                'OF' => $CodigoPartes[0]
                
                            ]);
                        }
                        if($Inicio==1){ 
                            $TipoEscanerrespuesta=$this->CompruebaAreasPosteriortodas($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada);
                            $CantidadArea=$this->ComprobarCantidadArea($CodigoPartes,$Area,$NumeroLinea);
                            if($CantidadArea=='ErrorLinea'){
                                return response()->json([
                                    'tabla' => $menu,
                                    'Escaner' => "",
                                    'status' => "ErrorLinea",
                                    'CantidadTotal' => "",
                                    'CantidadCompletada' => "",
                                    'OF' => $CodigoPartes[0]
                    
                                ]);
                            }elseif($CantidadArea=='ErrorLineaCodigo'){
                                return response()->json([
                                    'tabla' => $menu,
                                    'Escaner' => "",
                                    'status' => "ErrorLineaCodigo",
                                    'CantidadTotal' => "",
                                    'CantidadCompletada' => "",
                                    'OF' => $CodigoPartes[0],
                                    'Linea' => $NumeroLinea
                                ]);
                            }
                            if($TipoEscanerrespuesta!=6 AND $CantidadArea>=1){
                                if($Area!=4){//Si el area es diferente de Suministro 4
                                    $TipoEscanerrespuesta=$this->CompruebaAreasAnteriortodas($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada);
                                    if($TipoEscanerrespuesta != 5){
                                        $retrabajo=$request->Retrabajo;
                                        if($Area==$this->AreaEspecialTransicion){
                                            $TipoEscanerrespuesta=$this->GuardarPartida($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada,$retrabajo,$NumeroLinea);
                                        }else{
                                            $TipoEscanerrespuesta=$this->ValidarPasoUnaVezAA($Area,$CodigoPartes);
                                            if($TipoEscanerrespuesta>0){
                                                $TipoEscanerrespuesta=$this->GuardarPartida($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada,$retrabajo,$NumeroLinea);
                                            }else{$TipoEscanerrespuesta=5;}
                                        }
                                    }
                                }else{
                                    $retrabajo=$request->Retrabajo;
                                    $TipoEscanerrespuesta=$this->GuardarPartida($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada,$retrabajo,$NumeroLinea);
                                }
                            }
                            if($CantidadArea<1){
                                    $ComprobarAreaClasificacion = $datos->PartidasOF->first();
                                    $ComprobarAreaClasificacion = $ComprobarAreaClasificacion->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get()->count();
                                    if($ComprobarAreaClasificacion == 0){
                                        $status = 'ErrorNoAsignado';
                                    }else{
                                        $status = 'ErrorLineaComplete'; 
                                    }
                            }
                        }else{
                                $TipoEscanerrespuesta=$this->FinalizarPartida($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada);
                        }
                    }else if($Escaner==0){
                        $TipoManualrespuesta=$datos->partidasOF()->where('NumeroPartida','=',$CodigoPartes[1])->first();
                        if(!($TipoManualrespuesta=="" || $TipoManualrespuesta==null)){
                            $EscanerExiste = 1;
                        }else{
                            $EscanerExiste = 0;
                        }
                        if($Inicio==1){
                            $CantidadArea=$this->ComprobarCantidadArea($CodigoPartes,$Area,$NumeroLinea);
                            if($CantidadArea<1){
                                $ComprobarAreaClasificacion = $datos->PartidasOF->first();
                                $ComprobarAreaClasificacion = $ComprobarAreaClasificacion->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get()->count();
                                if($ComprobarAreaClasificacion == 0){
                                    $status = 'ErrorNoAsignado';
                                }
                            }
                        }
                    }
                    if(!isset($CodigoPartes[2])){
                        $TipoEscanerrespuesta=7;
                    }
                }
                $CantidadCompletada=$this->NumeroCompletadas($CodigoPartes,$Area);
                if($CantidadCompletada<0){
                    $CantidadCompletada=0; 
                }
                $IniciadosMostrar = 0; 
                if($Escaner==1){
                    $TipoEscaneo = "1 a 1";
                    //Opciones de la tabla
                    $Opciones='<option selected="" value="">Todos</option>
                        <option value="Abierta">Abiertas</option>
                        <option value="Cerrada">Cerradas</option>';
                    //Mostrar las partidas    
                    $partidas = $datos->partidasOF()->OrderBy('id','desc')->get();
                            foreach( $partidas as $PartidasordenFabricacion){
                                if($Area == $this->AreaEspecialPulido){
                                    $NumeroBloqueDatos = $PartidasordenFabricacion->Areas()->where('Areas_id',$this->AreaEspecialPulido)->where('Linea_id',$NumeroLinea)->orderBy('FechaComienzo','desc')->first();
                                    if($NumeroBloqueDatos==""){
                                        $NumeroBloqueDatos = [];
                                    }else{
                                        $NumeroBloqueDatos = Partidasof_Areas::select('*')->where('Areas_id',$this->AreaEspecialPulido)
                                        ->where('NumeroBloque',$NumeroBloqueDatos['pivot']->NumeroBloque)
                                        ->addSelect(DB::raw('GREATEST(FechaComienzo, COALESCE(FechaTermina, "1000-01-01")) AS FechaMayor'))
                                        ->orderBy(DB::raw('GREATEST(FechaComienzo, COALESCE(FechaTermina, "1000-01-01"))'), 'desc')
                                        ->get();
                                    }
                                    foreach($NumeroBloqueDatos as $PartdaAr){
                                        $PartidasPulido = PartidasOF::find($PartdaAr->PartidasOF_id);
                                        $OrdenFabricacionPulido = $PartidasPulido->OrdenFabricacion;
                                        $menu.='<tr>
                                                <td class="align-middle ps-3 NumParte">'.$OrdenFabricacionPulido->OrdenFabricacion.'-'.$PartidasPulido->NumeroPartida.'-'.$PartdaAr->NumeroEtiqueta.'</td>
                                                <td class="align-middle text-center Cantidad">'.$PartdaAr->Cantidad.'</td>';
                                                if($PartdaAr->TipoPartida=="R"){
                                                    $menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Retrabajo</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                }else{$menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Normal</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                }
                                                
                                        $menu.='<td class="align-middle Inicio">'.$PartdaAr->FechaComienzo.'</td>
                                                <td class="align-middle Fin">'.$PartdaAr->FechaTermina.'</td>';
                                                if($PartdaAr->FechaTermina==""){
                                                    $menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Abierta</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                }else{$menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Cerrada</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                }
                                        $Linea = Linea::find($PartdaAr->Linea_id);
                                                $menu.='<td class="align-middle text-center Linea"><h5 class="text-light text-center p-0 mx-1" style="background:'.$Linea->ColorLinea.'">'.$Linea->NumeroLinea.'</h5></td></tr>';
                                        $NumeroBloque = $PartdaAr->NumeroBloque;
                                        $EstatusBloque = $PartdaAr->CerrarBloque;
                                    }
                                    if($EstatusBloque != 1){
                                        $EstatusBloque = '<div class="badge badge-phoenix fs--2 badge-phoenix-info"><span class="fw-bold">Plato Abierto</span></div>';
                                    }else{$EstatusBloque = '<div class="badge badge-phoenix fs--2 badge-phoenix-danger"><span class="fw-bold">Plato Cerrado</span></div>';}
                                    //Mostrar Partidas
                                    $registrosporLinea = $PartidasordenFabricacion->Areas()->where('Areas_id', $this->AreaEspecialClasificacion)->get()->unique('pivot.Linea_id');
                                    foreach($registrosporLinea as $MostrarPartidas){
                                        $LineaMostrar = Linea::find($MostrarPartidas['pivot']->Linea_id)->NumeroLinea;
                                        $TotalMostrar = Partidasof_Areas::where('PartidasOF_id',$MostrarPartidas['pivot']->PartidasOF_id)->where('Areas_id', $this->AreaEspecialClasificacion)->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->get()->SUM('Cantidad');
                                        $CantidadCompletadaPartidas=$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->whereNotNull('FechaTermina')->where('TipoPartida','N')->SUM('Cantidad')
                                        -$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)->whereNull('FechaTermina')->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->where('TipoPartida','R')->SUM('Cantidad');
    
                                        $PartidasFaltantesList.='<li class="list-group-item d-flex justify-content-between align-items-center"><span class="text" style="font-size:0.8em;">'.$LineaMostrar.'</span><span class="badge badge-light-danger rounded-pill">'.$TotalMostrar-$CantidadCompletadaPartidas.'</span><span class="badge badge-light-success rounded-pill">'.$CantidadCompletadaPartidas.'</span><span class="badge badge-light-primary rounded-pill">'.$TotalMostrar.'</span></li>';
                                    }
                                }else{
                                    $PartdaArea=$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)
                                    ->addSelect(DB::raw('GREATEST(FechaComienzo, COALESCE(FechaTermina, "1000-01-01")) AS FechaMayor'))
                                    ->orderBy(DB::raw('GREATEST(FechaComienzo, COALESCE(FechaTermina, "1000-01-01"))'), 'desc')
                                    ->get();
                                    foreach($PartdaArea as $PartdaAr){
                                        $menu.='<tr>
                                                <td class="align-middle ps-3 NumParte">'.$datos->OrdenFabricacion.'-'.$PartidasordenFabricacion->NumeroPartida.'-'.$PartdaAr['pivot']->NumeroEtiqueta.'</td>
                                                <td class="align-middle text-center Cantidad">'.$PartdaAr['pivot']->Cantidad.'</td>';
                                                if($PartdaAr['pivot']->TipoPartida=="R"){
                                                    $menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Retrabajo</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                }else{$menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Normal</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                }
                                                
                                        $menu.='<td class="align-middle Inicio">'.$PartdaAr['pivot']->FechaComienzo.'</td>
                                                <td class="align-middle Fin">'.$PartdaAr['pivot']->FechaTermina.'</td>';
                                                if($PartdaAr['pivot']->FechaTermina==""){
                                                    $menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Abierta</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                }else{$menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Cerrada</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                }
                                        $Linea = Linea::find($PartdaAr['pivot']->Linea_id);
                                                $menu.='<td class="align-middle text-center Linea"><h5 class="text-light text-center p-0 mx-1" style="background:'.$Linea->ColorLinea.'">'.$Linea->NumeroLinea.'</h5></td></tr>';
                                    }
                                    //Mostrar Partidas
                                    $registrosporLinea = $PartidasordenFabricacion->Areas()->where('Areas_id', $this->AreaEspecialClasificacion)->get()->unique('pivot.Linea_id');
                                    foreach($registrosporLinea as $MostrarPartidas){
                                        $LineaMostrar = Linea::find($MostrarPartidas['pivot']->Linea_id)->NumeroLinea;
                                        $TotalMostrar = Partidasof_Areas::where('PartidasOF_id',$MostrarPartidas['pivot']->PartidasOF_id)->where('Areas_id', $this->AreaEspecialClasificacion)->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->get()->SUM('Cantidad');
                                        $CantidadCompletadaPartidas=$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->whereNotNull('FechaTermina')->where('TipoPartida','N')->SUM('Cantidad')
                                        -$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)->whereNull('FechaTermina')->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->where('TipoPartida','R')->SUM('Cantidad');
    
                                        $PartidasFaltantesList.='<li class="list-group-item d-flex justify-content-between align-items-center"><span class="text" style="font-size:0.8em;">'.$LineaMostrar.'</span><span class="badge badge-light-danger rounded-pill">'.$TotalMostrar-$CantidadCompletadaPartidas.'</span><span class="badge badge-light-success rounded-pill">'.$CantidadCompletadaPartidas.'</span><span class="badge badge-light-primary rounded-pill">'.$TotalMostrar.'</span></li>';
                                    }
                                }
                            }
                    $IniciadosMostrar = $partidas->first();
                    $IniciadosMostrar = $IniciadosMostrar->Areas()->whereNull('FechaTermina')->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->get()->SUM('pivot.Cantidad');//->get();
                }else{
                    $TipoEscaneo = "Masivo";
                    $Opciones='<option selected="" value="">Todos</option>
                        <option value="Iniciado">Iniciado</option>
                        <option value="Finalizado">Finalizado</option>';
                            //Mostrar las partidas    
                            $partidas = $datos->partidasOF()->OrderBy('id','desc')->get();
                            foreach( $partidas as $PartidasordenFabricacion){
                                if($Area == $this->AreaEspecialPulido){
                                    $NumeroBloqueDatos = $PartidasordenFabricacion->Areas()->where('Areas_id',$this->AreaEspecialPulido)->where('Linea_id',$NumeroLinea)->orderBy('FechaComienzo','desc')->first();
                                    if($NumeroBloqueDatos==""){
                                        $NumeroBloqueDatos = [];
                                    }else{
                                        $NumeroBloqueDatos = Partidasof_Areas::select('*')->where('Areas_id',$this->AreaEspecialPulido)
                                        ->where('NumeroBloque',$NumeroBloqueDatos['pivot']->NumeroBloque)
                                        ->addSelect(DB::raw('GREATEST(FechaComienzo, COALESCE(FechaTermina, "1000-01-01")) AS FechaMayor'))
                                        ->orderBy(DB::raw('GREATEST(FechaComienzo, COALESCE(FechaTermina, "1000-01-01"))'), 'desc')
                                        ->get();
                                        foreach($NumeroBloqueDatos as $PartdaAr){
                                            $PartidasPulido = PartidasOF::find($PartdaAr->PartidasOF_id);
                                            $OrdenFabricacionPulido = $PartidasPulido->OrdenFabricacion;
                                            $menu.='<tr>
                                                    <td class="align-middle ps-3 NumParte">'.$OrdenFabricacionPulido->OrdenFabricacion.'-'.$PartidasPulido->NumeroPartida.'-'.$PartdaAr->NumeroEtiqueta.'</td>
                                                    <td class="align-middle text-center Cantidad">'.$PartdaAr->Cantidad.'</td>';
                                                    if($PartdaAr->TipoPartida=="R"){
                                                        $menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Retrabajo</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                    }else{$menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Normal</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                    }
                                                    
                                            $menu.='<td class="align-middle Inicio">'.$PartdaAr->FechaComienzo.'</td>
                                                    <td class="align-middle Fin">'.$PartdaAr->FechaTermina.'</td>';
                                                    if($PartdaAr->FechaTermina==""){
                                                        $menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Abierta</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                    }else{$menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Cerrada</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                    }
                                            $Linea = Linea::find($PartdaAr->Linea_id);
                                                    $menu.='<td class="align-middle text-center Linea"><h5 class="text-light text-center p-0 mx-1" style="background:'.$Linea->ColorLinea.'">'.$Linea->NumeroLinea.'</h5></td></tr>';
                                            $NumeroBloque = $PartdaAr->NumeroBloque;
                                            $EstatusBloque = $PartdaAr->CerrarBloque;
                                        }
                                        if($EstatusBloque != 1){
                                            $EstatusBloque = '<div class="badge badge-phoenix fs--2 badge-phoenix-info"><span class="fw-bold">Plato Abierto</span></div>';
                                        }else{$EstatusBloque = '<div class="badge badge-phoenix fs--2 badge-phoenix-danger"><span class="fw-bold">Plato Cerrado</span></div>';}
                                        $registrosporLinea = $PartidasordenFabricacion->Areas()->where('Areas_id', $this->AreaEspecialClasificacion)->get()->unique('pivot.Linea_id');
                                        foreach($registrosporLinea as $MostrarPartidas){
                                            $LineaMostrar = Linea::find($MostrarPartidas['pivot']->Linea_id)->NumeroLinea;
                                            $TotalMostrar = Partidasof_Areas::where('PartidasOF_id',$MostrarPartidas['pivot']->PartidasOF_id)->where('Areas_id', $this->AreaEspecialClasificacion)->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->get()->SUM('Cantidad');
                                            //$CantidadCompletadaPartidas=$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->whereNotNull('FechaTermina')->where('TipoPartida','N')->SUM('Cantidad')
                                            //-$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)->whereNull('FechaTermina')->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->where('TipoPartida','R')->SUM('Cantidad');
                                            $CantidadCompletadaPartidas=$this->NumeroCompletadas($CodigoPartes,$Area);
                                            $PartidasFaltantesList.='<li class="list-group-item d-flex justify-content-between align-items-center"><span class="text" style="font-size:0.8em;">'.$LineaMostrar.'</span><span class="badge badge-light-danger rounded-pill">'.$TotalMostrar-$CantidadCompletadaPartidas.'</span><span class="badge badge-light-success rounded-pill">'.$CantidadCompletadaPartidas.'</span><span class="badge badge-light-primary rounded-pill">'.$TotalMostrar.'</span></li>';
                                        }
                                    }
                                }else{
                                    $PartdaArea=$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)->OrderBy('pivot_id','desc')->get();
                                    foreach($PartdaArea as $PartdaAr){
                                        $menu.='<tr>
                                                <td class="align-middle ps-3 NumParte">'.$datos->OrdenFabricacion.'-'.$PartidasordenFabricacion->NumeroPartida.'-0'.'</td>
                                                <td class="align-middle text-center Cantidad">'.$PartdaAr['pivot']->Cantidad.'</td>';
                                                if($PartdaAr['pivot']->TipoPartida=="R"){
                                                    $menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Retrabajo</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                }else{$menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Normal</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                }
                                                
                                        $menu.='<td class="align-middle Inicio">'.$PartdaAr['pivot']->FechaComienzo.'</td>
                                                <td class="align-middle Fin">'.$PartdaAr['pivot']->FechaTermina.'</td>';
                                                if($PartdaAr['pivot']->FechaTermina==""){
                                                    $menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">iniciado</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                }else{$menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-danger"><span class="fw-bold">finalizado</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                }
                                        $Linea = Linea::find($PartdaAr['pivot']->Linea_id);
                                                $menu.='<td class="align-middle text-center Linea"><h5 class="text-light text-center p-0 mx-1" style="background:'.$Linea->ColorLinea.'">'.$Linea->NumeroLinea.'</h5></td></tr>';
                                    }
                                    //Mostrar Partidas
                                    $registrosporLinea = $PartidasordenFabricacion->Areas()->where('Areas_id', $this->AreaEspecialClasificacion)->get()->unique('pivot.Linea_id');
                                    foreach($registrosporLinea as $MostrarPartidas){
                                        $CantidadCompletadaPartidas=$this->NumeroCompletadas($CodigoPartes,$Area);//$PartidasordenFabricacion->Areas()->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->where('Areas_id', $Area)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                        ($PartidasordenFabricacion->Areas()->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->where('Areas_id', $Area)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                        - $PartidasordenFabricacion->Areas()->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->where('Areas_id', $Area)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                                        $LineaMostrar = Linea::find($MostrarPartidas['pivot']->Linea_id)->NumeroLinea;
                                        $TotalMostrar = Partidasof_Areas::where('PartidasOF_id',$MostrarPartidas['pivot']->PartidasOF_id)->where('Areas_id', $this->AreaEspecialClasificacion)->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->get()->SUM('Cantidad');
                                        $PartidasFaltantesList.='<li class="list-group-item d-flex justify-content-between align-items-center"><span class="text" style="font-size:0.8em;">'.$LineaMostrar.'</span><span class="badge badge-light-danger rounded-pill">'.$TotalMostrar.'</span><span class="badge badge-light-success rounded-pill">'.$CantidadCompletadaPartidas.'</span><span class="badge badge-light-primary rounded-pill">'.$TotalMostrar.'</span></li>';
                                    }
                                }
                            }
                            $IniciadosMostrar = $partidas->first();
                            $IniciadosMostrar = $IniciadosMostrar->Areas()->whereNotNull('FechaComienzo')->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->get()->SUM('pivot.Cantidad')
                                                - $IniciadosMostrar->Areas()->whereNull('FechaComienzo')->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->get()->SUM('pivot.Cantidad');//->get();
                }
                $menu='<div class="card-body">
                    <div id="ContainerTableSuministros" class="table-list">
                        <div class="row">
                            <h5 class="text-center text-secondary p-0 m-0">Tipo de Escaneo : '.$TipoEscaneo.'</h5>
                            <div class="col-6">
                                <div class="row justify-content-start g-0">
                                    <div class="col-auto px-3">
                                        <h6 class="text-center">Orden de Fabricación '.$datos->OrdenFabricacion.'</h6> 
                                        <div class="badge badge-phoenix fs--4 badge-phoenix-secondary"><span class="fw-bold">Piezas Completadas </span>'.$CantidadCompletada.'/'.$CantidadTotal.'<span class="ms-1 fas fa-stream"></span></div>
                                        <br><div class="badge badge-phoenix fs--4 badge-phoenix-info"><span class="fw-bold"><span class="ms-1 fas fa-angle-double-right"></span> Pendientes de finalizar </span>'.$IniciadosMostrar.'</div>
                                     </div>
                                     <div class="col-auto px-3" style="transform:scale(1.4);">
                                    '.$EstatusBloque.'
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="row justify-content-end g-0">
                                    <div class="col-auto px-3">
                                        <div class="dropdown font-sans-serif d-inline-block">
                                            <button class="btn btn-phoenix-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">Partidas <i class="fas fa-grip-horizontal"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-auto px-3">
                                        <select class="form-select form-select-sm mb-3" data-list-filter="data-list-filter">
                                        '.$Opciones.'
                                        </select>
                                    </div>
                                    <div class="collapse p-1" id="collapseExample">
                                        <ul class="list-group">
                                        '.$PartidasFaltantesList.'    
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="table-responsive scrollbar mb-3">
                        <table id="TableSuministros" class="table table-striped table-sm fs--1 mb-0 overflow-hidden">
                            <thead>
                                <tr class="bg-primary text-white">
                                <th class="sort border-top ps-3" data-sort="NumParte">Codigo</th>
                                    <th class="sort border-top" data-sort="Cantidad">Cantidad</th>
                                    <th class="sort border-top" data-sort="TipoPartida">Tipo Partida</th>
                                    <th class="sort border-top" data-sort="Inicio">Fecha Inicio</th>
                                    <th class="sort border-top" data-sort="Fin">Fecha Fin</th>
                                    <th class="sort border-top" data-sort="Estatus">Estatus</th>
                                    <th class="sort border-top ps-3" data-sort="Linea">Linea</th>
                                
                                </tr>
                            </thead>
                            <tbody class="list" id="TablaBody">
                                '.$menu.'
                            </tbody>
                        </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                            <ul class="mb-0 pagination"></ul>
                            <button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                        </div>
                    </div>';
                    $BanderaFinalizar=1;
                    if($Area==$this->AreaEspecialMontaje){
                        if($Inicio==1 && $Escaner==1){
                            $datos->Cerrada = 1; 
                            $datos->save();
                        }elseif($Inicio==0 && $Escaner==1){
                            $CantidadTotalCorte = $datos->PartidasOF->first();
                            $CantidadTotalCorte = $CantidadTotalCorte->Areas()->where('Areas_id',$this->AreaEspecialCorte)->get()->SUM('pivot.Cantidad');
                            if(($CantidadTotal-$CantidadCompletada)==0 || ($CantidadTotalCorte-$CantidadCompletada)==0){
                                $datos->Cerrada = 0; 
                                $datos->save();
                                $BanderaFinalizar=0;
                            }
                        }
                    }
                return response()->json([
                    'tabla' => $menu,
                    'Escaner' => $Escaner,
                    'EscanerExiste' => $EscanerExiste,
                    'status' => $status,
                    'CantidadTotal' => $CantidadTotal,
                    'Inicio' => $Inicio,
                    'Finalizar' =>$Finalizar,
                    'BanderaFinalizar' => $BanderaFinalizar,
                    'TipoEscanerrespuesta'=>$TipoEscanerrespuesta,
                    'CantidadCompletada' => $CantidadCompletada,
                    'OF' => $CodigoPartes[0],
                    'NumeroBloque' =>$NumeroBloque,
        
                ]);
            }
        }else{
            return response()->json([
                'tabla' => $menu,
                'Escaner' => "",
                'status' => "NoExiste",
                'CantidadTotal' => "",
                'CantidadCompletada' => 4,
                'OF' => $CodigoPartes[0]
            ]);
        }
    }
    /*
    public function PreparadoBuscar(Request $request){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if ($request->has('Confirmacion')) {
            $confirmacion=1;
        }else{
            $confirmacion=0;
        }
        $Codigo = $request->Codigo;
        $Inicio = $request->Inicio;
        $Finalizar = $request->Finalizar;
        $NumeroLinea = $request->Linea;
        $NumeroLinea = Linea::where('NumeroLinea',$NumeroLinea)->first();
        $TipoEscaneo = "";
        if($NumeroLinea == ""){
            return response()->json([
                'tabla' => "",
                'Escaner' => "",
                'status' => "ErrorLínea",
                'CantidadTotal' => "",
                'CantidadCompletada' => 4,
                'OF' => ''

            ]);
        }
        $NumeroLinea = $NumeroLinea->id;
        $Area = $this->funcionesGenerales->decrypt($request->Area);
        $CodigoPartes = explode("-", $Codigo);
        $CodigoTam = count($CodigoPartes);
        $TipoEscanerrespuesta=0;
        $menu="";
        $Escaner="";
        $CantidadCompletada=0;
        $EscanerExiste=0;
        $NumeroBloque=0;
        $EstatusBloque = "";
        $PartidasFaltantesList='<li class="list-group-item d-flex justify-content-between align-items-center">Núm. Línea<span class="badge badge-light-danger rounded-pill">Faltantes</span><span class="badge badge-light-success rounded-pill">Completadas</span><span class="badge badge-light-primary rounded-pill">Total</span></li>';
        //Valida si el codigo es aceptado tiene que ser mayor a 2
        //if(($CodigoTam==3 && !($CodigoPartes[2]=="" || $CodigoPartes[2]==0)) || $CodigoTam==2){
        if($CodigoTam==3 || $CodigoTam==2){
            $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->first();
            if($datos=="" OR $datos==null){
                return response()->json([
                    'tabla' => $menu,
                    'Escaner' => $Escaner,
                    'status' => "empty",
                    'CantidadTotal' => "",
                    'CantidadCompletada' => $CantidadCompletada,
                    'OF' => $CodigoPartes[0]
        
                ]);
            }else{
                if($Area != $this->AreaEspecialMontaje){
                    if($datos->Cerrada == 0){//Valida que la Orden de fabricacion no se encuentre cerrada
                        return response()->json([
                            'tabla' => $menu,
                            'Escaner' => "",
                            'status' => "Cerrada",
                            'CantidadTotal' => "",
                            'CantidadCompletada' => 4,
                            'OF' => $CodigoPartes[0]
            
                        ]);
                    }
                }
                $status = 'success'; 
                $CantidadTotal=$datos->CantidadTotal;
                //Variable  guarda el valor de Escaner para saber si es no 0=No escaner 1=escaner
                $Escaner=$datos->Escaner;
                if($CodigoTam==3 || $CodigoTam==2){
                    //Comprobamos que la etiqueta si coincida con su numero de parte
                    if($Escaner==1 && isset($CodigoPartes[2])){
                        $CodigoValido=$this->ComprobarNumEtiqueta($CodigoPartes,$Area);
                        if($CodigoValido==0){
                            return response()->json([
                                'tabla' => $menu,
                                'Escaner' => "",
                                'status' => "NoExiste",
                                'CantidadTotal' => "",
                                'CantidadCompletada' => 4,
                                'OF' => $CodigoPartes[0]
                
                            ]);
                        }
                        if($Inicio==1){ 
                            $TipoEscanerrespuesta=$this->CompruebaAreasPosteriortodas($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada);
                            $CantidadArea=$this->ComprobarCantidadArea($CodigoPartes,$Area,$NumeroLinea);
                            if($CantidadArea=='ErrorLinea'){
                                return response()->json([
                                    'tabla' => $menu,
                                    'Escaner' => "",
                                    'status' => "ErrorLinea",
                                    'CantidadTotal' => "",
                                    'CantidadCompletada' => "",
                                    'OF' => $CodigoPartes[0]
                    
                                ]);
                            }elseif($CantidadArea=='ErrorLineaCodigo'){
                                return response()->json([
                                    'tabla' => $menu,
                                    'Escaner' => "",
                                    'status' => "ErrorLineaCodigo",
                                    'CantidadTotal' => "",
                                    'CantidadCompletada' => "",
                                    'OF' => $CodigoPartes[0],
                                    'Linea' => $NumeroLinea
                                ]);
                            }
                            if($TipoEscanerrespuesta!=6 AND $CantidadArea>=1){
                                if($Area!=4){//Si el area es diferente de Suministro 4
                                    $TipoEscanerrespuesta=$this->CompruebaAreasAnteriortodas($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada);
                                    if($TipoEscanerrespuesta != 5){
                                        $retrabajo=$request->Retrabajo;
                                        if($Area==$this->AreaEspecialTransicion){
                                            $TipoEscanerrespuesta=$this->GuardarPartida($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada,$retrabajo,$NumeroLinea);
                                        }else{
                                            $TipoEscanerrespuesta=$this->ValidarPasoUnaVezAA($Area,$CodigoPartes);
                                            if($TipoEscanerrespuesta>0){
                                                $TipoEscanerrespuesta=$this->GuardarPartida($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada,$retrabajo,$NumeroLinea);
                                            }else{$TipoEscanerrespuesta=5;}
                                        }
                                    }
                                }else{
                                    $retrabajo=$request->Retrabajo;
                                    $TipoEscanerrespuesta=$this->GuardarPartida($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada,$retrabajo,$NumeroLinea);
                                }
                            }
                            if($CantidadArea<1){
                                    $ComprobarAreaClasificacion = $datos->PartidasOF->first();
                                    $ComprobarAreaClasificacion = $ComprobarAreaClasificacion->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get()->count();
                                    if($ComprobarAreaClasificacion == 0){
                                        $status = 'ErrorNoAsignado';
                                    }else{
                                        $status = 'ErrorLineaComplete'; 
                                    }
                            }
                        }else{
                                $TipoEscanerrespuesta=$this->FinalizarPartida($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada);
                        }
                    }else if($Escaner==0){
                        $TipoManualrespuesta=$datos->partidasOF()->where('NumeroPartida','=',$CodigoPartes[1])->first();
                        if(!($TipoManualrespuesta=="" || $TipoManualrespuesta==null)){
                            $EscanerExiste = 1;
                        }else{
                            $EscanerExiste = 0;
                        }
                        if($Inicio==1){
                            $CantidadArea=$this->ComprobarCantidadArea($CodigoPartes,$Area,$NumeroLinea);
                            if($CantidadArea<1){
                                $ComprobarAreaClasificacion = $datos->PartidasOF->first();
                                $ComprobarAreaClasificacion = $ComprobarAreaClasificacion->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get()->count();
                                if($ComprobarAreaClasificacion == 0){
                                    $status = 'ErrorNoAsignado';
                                }
                            }
                        }
                    }
                    if(!isset($CodigoPartes[2])){
                        $TipoEscanerrespuesta=7;
                    }
                }
                $CantidadCompletada=$this->NumeroCompletadas($CodigoPartes,$Area);
                if($CantidadCompletada<0){
                    $CantidadCompletada=0; 
                }
                $IniciadosMostrar = 0; 
                if($Escaner==1){
                    $TipoEscaneo = "1 a 1";
                    //Opciones de la tabla
                    $Opciones='<option selected="" value="">Todos</option>
                        <option value="Abierta">Abiertas</option>
                        <option value="Cerrada">Cerradas</option>';
                    //Mostrar las partidas    
                    $partidas = $datos->partidasOF()->OrderBy('id','desc')->get();
                            foreach( $partidas as $PartidasordenFabricacion){
                                if($Area == $this->AreaEspecialPulido){
                                    $NumeroBloqueDatos = $PartidasordenFabricacion->Areas()->where('Areas_id',$this->AreaEspecialPulido)->where('Linea_id',$NumeroLinea)->orderBy('FechaComienzo','desc')->first();
                                    if($NumeroBloqueDatos==""){
                                        $NumeroBloqueDatos = [];
                                    }else{
                                        $NumeroBloqueDatos = Partidasof_Areas::select('*')->where('Areas_id',$this->AreaEspecialPulido)
                                        ->where('NumeroBloque',$NumeroBloqueDatos['pivot']->NumeroBloque)
                                        ->addSelect(DB::raw('GREATEST(FechaComienzo, COALESCE(FechaTermina, "1000-01-01")) AS FechaMayor'))
                                        ->orderBy(DB::raw('GREATEST(FechaComienzo, COALESCE(FechaTermina, "1000-01-01"))'), 'desc')
                                        ->get();
                                    }
                                    foreach($NumeroBloqueDatos as $PartdaAr){
                                        $PartidasPulido = PartidasOF::find($PartdaAr->PartidasOF_id);
                                        $OrdenFabricacionPulido = $PartidasPulido->OrdenFabricacion;
                                        $menu.='<tr>
                                                <td class="align-middle ps-3 NumParte">'.$OrdenFabricacionPulido->OrdenFabricacion.'-'.$PartidasPulido->NumeroPartida.'-'.$PartdaAr->NumeroEtiqueta.'</td>
                                                <td class="align-middle text-center Cantidad">'.$PartdaAr->Cantidad.'</td>';
                                                if($PartdaAr->TipoPartida=="R"){
                                                    $menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Retrabajo</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                }else{$menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Normal</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                }
                                                
                                        $menu.='<td class="align-middle Inicio">'.$PartdaAr->FechaComienzo.'</td>
                                                <td class="align-middle Fin">'.$PartdaAr->FechaTermina.'</td>';
                                                if($PartdaAr->FechaTermina==""){
                                                    $menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Abierta</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                }else{$menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Cerrada</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                }
                                        $Linea = Linea::find($PartdaAr->Linea_id);
                                                $menu.='<td class="align-middle text-center Linea"><h5 class="text-light text-center p-0 mx-1" style="background:'.$Linea->ColorLinea.'">'.$Linea->NumeroLinea.'</h5></td></tr>';
                                        $NumeroBloque = $PartdaAr->NumeroBloque;
                                        $EstatusBloque = $PartdaAr->CerrarBloque;
                                    }
                                    if($EstatusBloque != 1){
                                        $EstatusBloque = '<div class="badge badge-phoenix fs--2 badge-phoenix-info"><span class="fw-bold">Plato Abierto</span></div>';
                                    }else{$EstatusBloque = '<div class="badge badge-phoenix fs--2 badge-phoenix-danger"><span class="fw-bold">Plato Cerrado</span></div>';}
                                    //Mostrar Partidas
                                    $registrosporLinea = $PartidasordenFabricacion->Areas()->where('Areas_id', $this->AreaEspecialClasificacion)->get()->unique('pivot.Linea_id');
                                    foreach($registrosporLinea as $MostrarPartidas){
                                        $LineaMostrar = Linea::find($MostrarPartidas['pivot']->Linea_id)->NumeroLinea;
                                        $TotalMostrar = Partidasof_Areas::where('PartidasOF_id',$MostrarPartidas['pivot']->PartidasOF_id)->where('Areas_id', $this->AreaEspecialClasificacion)->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->get()->SUM('Cantidad');
                                        $CantidadCompletadaPartidas=$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->whereNotNull('FechaTermina')->where('TipoPartida','N')->SUM('Cantidad')
                                        -$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)->whereNull('FechaTermina')->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->where('TipoPartida','R')->SUM('Cantidad');
    
                                        $PartidasFaltantesList.='<li class="list-group-item d-flex justify-content-between align-items-center"><span class="text" style="font-size:0.8em;">'.$LineaMostrar.'</span><span class="badge badge-light-danger rounded-pill">'.$TotalMostrar-$CantidadCompletadaPartidas.'</span><span class="badge badge-light-success rounded-pill">'.$CantidadCompletadaPartidas.'</span><span class="badge badge-light-primary rounded-pill">'.$TotalMostrar.'</span></li>';
                                    }
                                }else{
                                    $PartdaArea=$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)
                                    ->addSelect(DB::raw('GREATEST(FechaComienzo, COALESCE(FechaTermina, "1000-01-01")) AS FechaMayor'))
                                    ->orderBy(DB::raw('GREATEST(FechaComienzo, COALESCE(FechaTermina, "1000-01-01"))'), 'desc')
                                    ->get();
                                    foreach($PartdaArea as $PartdaAr){
                                        $menu.='<tr>
                                                <td class="align-middle ps-3 NumParte">'.$datos->OrdenFabricacion.'-'.$PartidasordenFabricacion->NumeroPartida.'-'.$PartdaAr['pivot']->NumeroEtiqueta.'</td>
                                                <td class="align-middle text-center Cantidad">'.$PartdaAr['pivot']->Cantidad.'</td>';
                                                if($PartdaAr['pivot']->TipoPartida=="R"){
                                                    $menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Retrabajo</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                }else{$menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Normal</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                }
                                                
                                        $menu.='<td class="align-middle Inicio">'.$PartdaAr['pivot']->FechaComienzo.'</td>
                                                <td class="align-middle Fin">'.$PartdaAr['pivot']->FechaTermina.'</td>';
                                                if($PartdaAr['pivot']->FechaTermina==""){
                                                    $menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Abierta</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                }else{$menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Cerrada</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                }
                                        $Linea = Linea::find($PartdaAr['pivot']->Linea_id);
                                                $menu.='<td class="align-middle text-center Linea"><h5 class="text-light text-center p-0 mx-1" style="background:'.$Linea->ColorLinea.'">'.$Linea->NumeroLinea.'</h5></td></tr>';
                                    }
                                    //Mostrar Partidas
                                    $registrosporLinea = $PartidasordenFabricacion->Areas()->where('Areas_id', $this->AreaEspecialClasificacion)->get()->unique('pivot.Linea_id');
                                    foreach($registrosporLinea as $MostrarPartidas){
                                        $LineaMostrar = Linea::find($MostrarPartidas['pivot']->Linea_id)->NumeroLinea;
                                        $TotalMostrar = Partidasof_Areas::where('PartidasOF_id',$MostrarPartidas['pivot']->PartidasOF_id)->where('Areas_id', $this->AreaEspecialClasificacion)->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->get()->SUM('Cantidad');
                                        $CantidadCompletadaPartidas=$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->whereNotNull('FechaTermina')->where('TipoPartida','N')->SUM('Cantidad')
                                        -$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)->whereNull('FechaTermina')->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->where('TipoPartida','R')->SUM('Cantidad');
    
                                        $PartidasFaltantesList.='<li class="list-group-item d-flex justify-content-between align-items-center"><span class="text" style="font-size:0.8em;">'.$LineaMostrar.'</span><span class="badge badge-light-danger rounded-pill">'.$TotalMostrar-$CantidadCompletadaPartidas.'</span><span class="badge badge-light-success rounded-pill">'.$CantidadCompletadaPartidas.'</span><span class="badge badge-light-primary rounded-pill">'.$TotalMostrar.'</span></li>';
                                    }
                                }
                            }
                    $IniciadosMostrar = $partidas->first();
                    $IniciadosMostrar = $IniciadosMostrar->Areas()->whereNull('FechaTermina')->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->get()->SUM('pivot.Cantidad');//->get();
                }else{
                    $TipoEscaneo = "Masivo";
                    $Opciones='<option selected="" value="">Todos</option>
                        <option value="Iniciado">Iniciado</option>
                        <option value="Finalizado">Finalizado</option>';
                            //Mostrar las partidas    
                            $partidas = $datos->partidasOF()->OrderBy('id','desc')->get();
                            foreach( $partidas as $PartidasordenFabricacion){
                                if($Area == $this->AreaEspecialPulido){
                                    $NumeroBloqueDatos = $PartidasordenFabricacion->Areas()->where('Areas_id',$this->AreaEspecialPulido)->where('Linea_id',$NumeroLinea)->orderBy('FechaComienzo','desc')->first();
                                    if($NumeroBloqueDatos==""){
                                        $NumeroBloqueDatos = [];
                                    }else{
                                        $NumeroBloqueDatos = Partidasof_Areas::select('*')->where('Areas_id',$this->AreaEspecialPulido)
                                        ->where('NumeroBloque',$NumeroBloqueDatos['pivot']->NumeroBloque)
                                        ->addSelect(DB::raw('GREATEST(FechaComienzo, COALESCE(FechaTermina, "1000-01-01")) AS FechaMayor'))
                                        ->orderBy(DB::raw('GREATEST(FechaComienzo, COALESCE(FechaTermina, "1000-01-01"))'), 'desc')
                                        ->get();
                                        foreach($NumeroBloqueDatos as $PartdaAr){
                                            $PartidasPulido = PartidasOF::find($PartdaAr->PartidasOF_id);
                                            $OrdenFabricacionPulido = $PartidasPulido->OrdenFabricacion;
                                            $menu.='<tr>
                                                    <td class="align-middle ps-3 NumParte">'.$OrdenFabricacionPulido->OrdenFabricacion.'-'.$PartidasPulido->NumeroPartida.'-'.$PartdaAr->NumeroEtiqueta.'</td>
                                                    <td class="align-middle text-center Cantidad">'.$PartdaAr->Cantidad.'</td>';
                                                    if($PartdaAr->TipoPartida=="R"){
                                                        $menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Retrabajo</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                    }else{$menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Normal</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                    }
                                                    
                                            $menu.='<td class="align-middle Inicio">'.$PartdaAr->FechaComienzo.'</td>
                                                    <td class="align-middle Fin">'.$PartdaAr->FechaTermina.'</td>';
                                                    if($PartdaAr->FechaTermina==""){
                                                        $menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Abierta</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                    }else{$menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Cerrada</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                    }
                                            $Linea = Linea::find($PartdaAr->Linea_id);
                                                    $menu.='<td class="align-middle text-center Linea"><h5 class="text-light text-center p-0 mx-1" style="background:'.$Linea->ColorLinea.'">'.$Linea->NumeroLinea.'</h5></td></tr>';
                                            $NumeroBloque = $PartdaAr->NumeroBloque;
                                            $EstatusBloque = $PartdaAr->CerrarBloque;
                                        }
                                        if($EstatusBloque != 1){
                                            $EstatusBloque = '<div class="badge badge-phoenix fs--2 badge-phoenix-info"><span class="fw-bold">Plato Abierto</span></div>';
                                        }else{$EstatusBloque = '<div class="badge badge-phoenix fs--2 badge-phoenix-danger"><span class="fw-bold">Plato Cerrado</span></div>';}
                                        $registrosporLinea = $PartidasordenFabricacion->Areas()->where('Areas_id', $this->AreaEspecialClasificacion)->get()->unique('pivot.Linea_id');
                                        foreach($registrosporLinea as $MostrarPartidas){
                                            $LineaMostrar = Linea::find($MostrarPartidas['pivot']->Linea_id)->NumeroLinea;
                                            $TotalMostrar = Partidasof_Areas::where('PartidasOF_id',$MostrarPartidas['pivot']->PartidasOF_id)->where('Areas_id', $this->AreaEspecialClasificacion)->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->get()->SUM('Cantidad');
                                            //$CantidadCompletadaPartidas=$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->whereNotNull('FechaTermina')->where('TipoPartida','N')->SUM('Cantidad')
                                            //-$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)->whereNull('FechaTermina')->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->where('TipoPartida','R')->SUM('Cantidad');
                                            $CantidadCompletadaPartidas=$this->NumeroCompletadas($CodigoPartes,$Area);
                                            $PartidasFaltantesList.='<li class="list-group-item d-flex justify-content-between align-items-center"><span class="text" style="font-size:0.8em;">'.$LineaMostrar.'</span><span class="badge badge-light-danger rounded-pill">'.$TotalMostrar-$CantidadCompletadaPartidas.'</span><span class="badge badge-light-success rounded-pill">'.$CantidadCompletadaPartidas.'</span><span class="badge badge-light-primary rounded-pill">'.$TotalMostrar.'</span></li>';
                                        }
                                    }
                                }else{
                                    $PartdaArea=$PartidasordenFabricacion->Areas()->where('Areas_id',$Area)->OrderBy('pivot_id','desc')->get();
                                    foreach($PartdaArea as $PartdaAr){
                                        $menu.='<tr>
                                                <td class="align-middle ps-3 NumParte">'.$datos->OrdenFabricacion.'-'.$PartidasordenFabricacion->NumeroPartida.'-0'.'</td>
                                                <td class="align-middle text-center Cantidad">'.$PartdaAr['pivot']->Cantidad.'</td>';
                                                if($PartdaAr['pivot']->TipoPartida=="R"){
                                                    $menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Retrabajo</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                }else{$menu.='<td class="align-middle TipoPartida"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Normal</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                }
                                                
                                        $menu.='<td class="align-middle Inicio">'.$PartdaAr['pivot']->FechaComienzo.'</td>
                                                <td class="align-middle Fin">'.$PartdaAr['pivot']->FechaTermina.'</td>';
                                                if($PartdaAr['pivot']->FechaTermina==""){
                                                    $menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">iniciado</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                                }else{$menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-danger"><span class="fw-bold">finalizado</span><span class="ms-1 fas fa-check"></span></div></td>';
                                                }
                                        $Linea = Linea::find($PartdaAr['pivot']->Linea_id);
                                                $menu.='<td class="align-middle text-center Linea"><h5 class="text-light text-center p-0 mx-1" style="background:'.$Linea->ColorLinea.'">'.$Linea->NumeroLinea.'</h5></td></tr>';
                                    }
                                    //Mostrar Partidas
                                    $registrosporLinea = $PartidasordenFabricacion->Areas()->where('Areas_id', $this->AreaEspecialClasificacion)->get()->unique('pivot.Linea_id');
                                    foreach($registrosporLinea as $MostrarPartidas){
                                        $CantidadCompletadaPartidas=$this->NumeroCompletadas($CodigoPartes,$Area);//$PartidasordenFabricacion->Areas()->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->where('Areas_id', $Area)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                        ($PartidasordenFabricacion->Areas()->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->where('Areas_id', $Area)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                        - $PartidasordenFabricacion->Areas()->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->where('Areas_id', $Area)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                                        $LineaMostrar = Linea::find($MostrarPartidas['pivot']->Linea_id)->NumeroLinea;
                                        $TotalMostrar = Partidasof_Areas::where('PartidasOF_id',$MostrarPartidas['pivot']->PartidasOF_id)->where('Areas_id', $this->AreaEspecialClasificacion)->where('Linea_id', $MostrarPartidas['pivot']->Linea_id)->get()->SUM('Cantidad');
                                        $PartidasFaltantesList.='<li class="list-group-item d-flex justify-content-between align-items-center"><span class="text" style="font-size:0.8em;">'.$LineaMostrar.'</span><span class="badge badge-light-danger rounded-pill">'.$TotalMostrar.'</span><span class="badge badge-light-success rounded-pill">'.$CantidadCompletadaPartidas.'</span><span class="badge badge-light-primary rounded-pill">'.$TotalMostrar.'</span></li>';
                                    }
                                }
                            }
                            $IniciadosMostrar = $partidas->first();
                            $IniciadosMostrar = $IniciadosMostrar->Areas()->whereNotNull('FechaComienzo')->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->get()->SUM('pivot.Cantidad')
                                                - $IniciadosMostrar->Areas()->whereNull('FechaComienzo')->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->get()->SUM('pivot.Cantidad');//->get();
                }
                $menu='<div class="card-body">
                    <div id="ContainerTableSuministros" class="table-list">
                        <div class="row">
                            <h5 class="text-center text-secondary p-0 m-0">Tipo de Escaneo : '.$TipoEscaneo.'</h5>
                            <div class="col-6">
                                <div class="row justify-content-start g-0">
                                    <div class="col-auto px-3">
                                        <h6 class="text-center">Orden de Fabricación '.$datos->OrdenFabricacion.'</h6> 
                                        <div class="badge badge-phoenix fs--4 badge-phoenix-secondary"><span class="fw-bold">Piezas Completadas </span>'.$CantidadCompletada.'/'.$CantidadTotal.'<span class="ms-1 fas fa-stream"></span></div>
                                        <br><div class="badge badge-phoenix fs--4 badge-phoenix-info"><span class="fw-bold"><span class="ms-1 fas fa-angle-double-right"></span> Pendientes de finalizar </span>'.$IniciadosMostrar.'</div>
                                     </div>
                                     <div class="col-auto px-3" style="transform:scale(1.4);">
                                    '.$EstatusBloque.'
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="row justify-content-end g-0">
                                    <div class="col-auto px-3">
                                        <div class="dropdown font-sans-serif d-inline-block">
                                            <button class="btn btn-phoenix-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">Partidas <i class="fas fa-grip-horizontal"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-auto px-3">
                                        <select class="form-select form-select-sm mb-3" data-list-filter="data-list-filter">
                                        '.$Opciones.'
                                        </select>
                                    </div>
                                    <div class="collapse p-1" id="collapseExample">
                                        <ul class="list-group">
                                        '.$PartidasFaltantesList.'    
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="table-responsive scrollbar mb-3">
                        <table id="TableSuministros" class="table table-striped table-sm fs--1 mb-0 overflow-hidden">
                            <thead>
                                <tr class="bg-primary text-white">
                                <th class="sort border-top ps-3" data-sort="NumParte">Codigo</th>
                                    <th class="sort border-top" data-sort="Cantidad">Cantidad</th>
                                    <th class="sort border-top" data-sort="TipoPartida">Tipo Partida</th>
                                    <th class="sort border-top" data-sort="Inicio">Fecha Inicio</th>
                                    <th class="sort border-top" data-sort="Fin">Fecha Fin</th>
                                    <th class="sort border-top" data-sort="Estatus">Estatus</th>
                                    <th class="sort border-top ps-3" data-sort="Linea">Linea</th>
                                
                                </tr>
                            </thead>
                            <tbody class="list" id="TablaBody">
                                '.$menu.'
                            </tbody>
                        </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                            <ul class="mb-0 pagination"></ul>
                            <button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                        </div>
                    </div>';
                    $BanderaFinalizar=1;
                    if($Area==$this->AreaEspecialMontaje){
                        if($Inicio==1 && $Escaner==1){
                            $datos->Cerrada = 1; 
                            $datos->save();
                        }elseif($Inicio==0 && $Escaner==1){
                            $CantidadTotalCorte = $datos->PartidasOF->first();
                            $CantidadTotalCorte = $CantidadTotalCorte->Areas()->where('Areas_id',$this->AreaEspecialCorte)->get()->SUM('pivot.Cantidad');
                            if(($CantidadTotal-$CantidadCompletada)==0 || ($CantidadTotalCorte-$CantidadCompletada)==0){
                                $datos->Cerrada = 0; 
                                $datos->save();
                                $BanderaFinalizar=0;
                            }
                        }
                    }
                return response()->json([
                    'tabla' => $menu,
                    'Escaner' => $Escaner,
                    'EscanerExiste' => $EscanerExiste,
                    'status' => $status,
                    'CantidadTotal' => $CantidadTotal,
                    'Inicio' => $Inicio,
                    'Finalizar' =>$Finalizar,
                    'BanderaFinalizar' => $BanderaFinalizar,
                    'TipoEscanerrespuesta'=>$TipoEscanerrespuesta,
                    'CantidadCompletada' => $CantidadCompletada,
                    'OF' => $CodigoPartes[0],
                    'NumeroBloque' =>$NumeroBloque,
        
                ]);
            }
        }else{
            return response()->json([
                'tabla' => $menu,
                'Escaner' => "",
                'status' => "NoExiste",
                'CantidadTotal' => "",
                'CantidadCompletada' => 4,
                'OF' => $CodigoPartes[0]
            ]);
        }
    }
    */
    public function CompruebaAreasAnteriortodas($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $OrdenFabricacion=OrdenFabricacion::where('OrdenFabricacion',$CodigoPartes[0])->first();
        $PartidasOF=$OrdenFabricacion->PartidasOF->where('NumeroPartida',$CodigoPartes[1])->first();
        $PartidasOFAreas=$PartidasOF->Areas()->where('Areas_id','<',$Area)->whereNull('FechaTermina')->where('NumeroEtiqueta',$CodigoPartes[2])->count();
        if($PartidasOFAreas!=0){
            return 5;
        }else{
            return 1;
        }
    }
    public function CompruebaAreasPosteriortodas($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $OrdenFabricacion=OrdenFabricacion::where('OrdenFabricacion',$CodigoPartes[0])->first();
        $PartidasOF=$OrdenFabricacion->PartidasOF->where('NumeroPartida',$CodigoPartes[1])->first();
        $PartidasOFAreas=$PartidasOF->Areas()->where('Areas_id','>',$Area)->whereNull('FechaTermina')->where('NumeroEtiqueta',$CodigoPartes[2])->count();
        if($PartidasOFAreas!=0){
            return 6;
        }else{
            return 1;
        }
    }
    public function GuardarPartida($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada,$retrabajo,$Lineaid){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $OrdenFabricacion=OrdenFabricacion::where('OrdenFabricacion',$CodigoPartes[0])->first();
        $PartidasOF=$OrdenFabricacion->PartidasOF->where('NumeroPartida',$CodigoPartes[1])->first();
        $PartidasOFAreasAbierto=$PartidasOF->Areas()->where('Areas_id',$Area)->whereNull('FechaTermina')->where('NumeroEtiqueta',$CodigoPartes[2])->first();
        //Verifica si ya esta iniciado
        if($PartidasOFAreasAbierto!=""){
            return 2;
        }
        $PartidasOFAreasCerrada=$PartidasOF->Areas()->where('Areas_id',$Area)->whereNotNull('FechaTermina')->where('NumeroEtiqueta',$CodigoPartes[2])->first();
        //Verifica si ya existe
        if($Area!=$this->AreaEspecialEmpaque ){
            if($Area != $this->AreaEspecialPulido){
                $NumeroBloque = null;
            }else{//Esto solo aplica a Pulido
                $NumeroBloque = Partidasof_areas::whereNotNull('NumeroBloque')->OrderBy('NumeroBloque','desc')->first();
                if($NumeroBloque==""){
                    $NumeroBloque = 1;
                }else{
                    $NumeroBloque = $this->NumeroBloque($Lineaid);
                }
            }
            if($PartidasOFAreasCerrada==""){
                $data = [
                    'Cantidad' => 1,
                    'TipoPartida' => 'N', // N = Normal
                    'FechaComienzo' => now(),
                    'NumeroEtiqueta' =>$CodigoPartes[2],
                    'Linea_id' => $Lineaid,
                    "NumeroBloque" => $NumeroBloque,
                    'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                ];
                $PartidasOF->Areas()->attach($Area, $data);
                if($Area==$this->AreaEspecialMontaje){
                    $OrdenFabricacion->Cerrada = 1; 
                    $OrdenFabricacion->save();
                }
                return 1;
            }else{
                if($retrabajo=="si"){
                    $data = [
                        'Cantidad' => 1,
                        'TipoPartida' => 'R', // R = Retrabajo
                        'FechaComienzo' => now(),
                        'NumeroEtiqueta' =>$CodigoPartes[2],
                        'Linea_id' => $Lineaid,
                        "NumeroBloque" => $NumeroBloque,
                        'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                    ];
                    $PartidasOF->Areas()->attach($Area, $data);
                    if($Area==$this->AreaEspecialMontaje){
                        $OrdenFabricacion->Cerrada = 1; 
                        $OrdenFabricacion->save();
                    }
                    return 1;
                }
                return 3;
            }
        }else{
            if (!$PartidasOFAreasCerrada) {
                $data = [
                    'Cantidad' => 1,
                    'TipoPartida' => 'N', // N = Normal
                    'FechaComienzo' => now(),
                    'FechaTermina' => now(), // Guardamos la fecha de finalización
                    'NumeroEtiqueta' => $CodigoPartes[2],
                    'Linea_id' => $Lineaid,
                    'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                ];
                $PartidasOF->Areas()->attach($Area, $data);
                return 1;
            } else{
                return 2;
            }
        }
    }
    public function FinalizarPartida($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $OrdenFabricacion=OrdenFabricacion::where('OrdenFabricacion',$CodigoPartes[0])->first();
        if($OrdenFabricacion==""){
            return 3;
        }
        $PartidasOF=$OrdenFabricacion->PartidasOF->where('NumeroPartida',$CodigoPartes[1])->first();
        if($PartidasOF==""){
            return 3;
        }
        $PartidasOFAreasAbierto=$PartidasOF->Areas()->where('Areas_id',$Area)->whereNull('FechaTermina')->where('NumeroEtiqueta',$CodigoPartes[2])->first();
        if($PartidasOFAreasAbierto==""){
            return 2;
        }
        DB::table('partidasof_areas')->where('id', $PartidasOFAreasAbierto['pivot']->id)->update(['FechaTermina' => now()]);
        return 1; 
    }
    public function NumeroCompletadas($CodigoPartes,$Area){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $OrdenFabricacion=OrdenFabricacion::where('OrdenFabricacion',$CodigoPartes[0])->first();
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
    public function TipoNoEscaner(Request $request){ 
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        //N= Normal, R = Retrabajo, F = Finalizada
        $request->validate([
            'Codigo' => 'required|string',
            'Cantidad' => 'required|Integer|min:1',
            'Linea' =>'required',
        ]);
        //Desencripta el Area
        $Area =$this->funcionesGenerales->decrypt($request->Area);
        $Codigo = $request->Codigo;
        $Cantidad = $request->Cantidad;
        $Retrabajo = $request->Retrabajo;
        $Estatus=1;
        $Inicio = $request->Inicio;
        $Fin = $request->Fin;
        $TipoAccion=0;
        if($Inicio==1){
            $Estatus = ($Retrabajo == "true") ? 2 : 1;
        }else{
            $Estatus=0;
        }
        $ContarPartidas=0;
        $CodigoPartes = explode("-", $Codigo);
        //Valida que el codigo este completo
        if(count($CodigoPartes)<2){
            return response()->json([
                'Inicio'=>$Inicio,
                'Fin'=>$Fin,
                'status' => "dontexist",
                'CantidadTotal' => "",
                'CantidadCompletada' => "",
                'OF' => $CodigoPartes[0],       
            ]);
        }
        $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->first();
        $AreaAnterior=$this->AreaAnteriorregistros($Area,$datos->OrdenFabricacion);
        //La orden de Fabricacion No existe
        if($datos==""){
            return response()->json([
                'Inicio'=>$Inicio,
                'Fin'=>$Fin,
                'status' => "empty",
                'CantidadTotal' => "",
                'CantidadCompletada' => "",
                'OF' => $CodigoPartes[0],      
            ]);
        }
        $NumeroLinea = $request->Linea;
        $NumeroLinea = Linea::where('NumeroLinea',$NumeroLinea)->first();
        if($NumeroLinea == ""){
            return response()->json([
                'Inicio'=>$Inicio,
                'Fin'=>$Fin,
                'status' => "empty",
                'CantidadTotal' => "",
                'CantidadCompletada' => "",
                'OF' => $CodigoPartes[0],      
            ]);
        }
        $NumeroLinea = $NumeroLinea->id;
        $partidasOF=$datos->partidasOF()->where('NumeroPartida','=',$CodigoPartes[1])->first();
        //La partida Of No existe
        if($partidasOF=="" OR $partidasOF==null){
            return response()->json([
                'Inicio'=>$Inicio,
                'Fin'=>$Fin,
                'status' => "dontexist",
                'CantidadTotal' => "",
                'CantidadCompletada' => "",
                'OF' => $CodigoPartes[0],       
            ]);
        }
        if($Inicio==1){
                if($Area==$this->AreaEspecialTransicion){
                    if($Retrabajo=="false"){
                        $NumeroPartidasTodasAnterior = $partidasOF->Areas()->where('Linea_id',$NumeroLinea)->where('Areas_id',$this->AreaEspecialClasificacion)->get()->SUM('pivot.Cantidad');
                        //Numero de piezasen Area actual
                        $NumeroPartidasAbiertas = $partidasOF->Areas()->where('Linea_id',$NumeroLinea)->where('Areas_id',$Area)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad');
                        $NumeroPartidasAbiertas=$NumeroPartidasAbiertas+$Cantidad;
                        if($NumeroPartidasAbiertas>$NumeroPartidasTodasAnterior){
                            return response()->json([
                                'Inicio'=>$Inicio,
                                'Fin'=>$Fin,
                                'status' => "PasBackerror",
                                'CantidadTotal' => "",
                                'CantidadCompletada' => "",
                                'OF' => $CodigoPartes[0],       
                            ]);
                        }
                        $data = [
                            'Cantidad' => $Cantidad,
                            'TipoPartida' => 'N', // N = Normal
                            'FechaComienzo' => now(),
                            'NumeroEtiqueta' =>0,
                            'Linea_id' => $NumeroLinea,
                            'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                        ];
                        $partidasOF->Areas()->attach($Area, $data);
                        return response()->json([
                            'Inicio'=>$Inicio,
                            'Fin'=>$Fin,
                            'status' => "success",
                            'OF' => $CodigoPartes[0],       
                        ]);
                    }else{
                        //Numero actual de terminados
                        $NumeroPartidasTodas = $partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida', '!=','F')->get()->SUM('pivot.Cantidad')
                                                    -$partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida', '=','F')->get()->SUM('pivot.Cantidad');

                        $NumeroPartidasTodasFinalizadas = $partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-$NumeroPartidasTodas;
                        if($NumeroPartidasTodasFinalizadas<$Cantidad){
                            return response()->json([
                                'Inicio'=>$Inicio,
                                'Fin'=>$Fin,
                                'status' => "SurplusRetrabajo",
                                'OF' => $CodigoPartes[0],       
                            ]);  
                        }
                        $data = [
                            'Cantidad' => $Cantidad,
                            'TipoPartida' => 'R', // N = Normal
                            'FechaComienzo' => now(),
                            'NumeroEtiqueta' =>0,
                            'Linea_id' => $NumeroLinea,
                            'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                        ];
                        $partidasOF->Areas()->attach($Area, $data);
                        return response()->json([
                            'Inicio'=>$Inicio,
                            'Fin'=>$Fin,
                            'status' => "success",
                            'OF' => $CodigoPartes[0],       
                        ]);
                    }
                }elseif($Area==$this->AreaEspecialEmpaque){
                    //Total Finalizadas que paso del area anterior
                    $NumeroPartidasTodas = $partidasOF->Areas()->where('Areas_id',$AreaAnterior)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad');
                    $NumeroPartidasAbiertas = $partidasOF->Areas()->where('Areas_id',$Area)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad');
                    $NumeroPartidasAbiertas=$NumeroPartidasAbiertas+$Cantidad;
                    if($NumeroPartidasAbiertas > $datos->CantidadTotal){
                         return response()->json([
                            'Inicio'=>$Inicio,
                            'Fin'=>$Fin,
                            'status' => "Excede",
                            'CantidadTotal' => "",
                            'CantidadCompletada' => "",
                            'OF' => $CodigoPartes[0],       
                        ]);
                    }
                    if($NumeroPartidasAbiertas>$NumeroPartidasTodas){
                        return response()->json([
                            'Inicio'=>$Inicio,
                            'Fin'=>$Fin,
                            'status' => "PasBackerror",
                            'CantidadTotal' => "",
                            'CantidadCompletada' => "",
                            'OF' => $CodigoPartes[0],       
                        ]);
                    }
                    $data = [
                        'Cantidad' => $Cantidad,
                        'TipoPartida' => 'N', // N = Normal
                        'FechaComienzo' => now(),
                        'FechaTermina' => now(),
                        'NumeroEtiqueta' =>0,
                        'Linea_id' => $NumeroLinea,
                        'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                    ];
                    $partidasOF->Areas()->attach($Area, $data);
                    $Partidastodas = $datos->partidasOF;
                    $SumarActual=0;
                    foreach($Partidastodas as $CantidadTotal){
                        $SumarActual = $CantidadTotal->Areas()->where('Areas_id',$Area)->get()->SUM('pivot.Cantidad');
                    }
                    $Terminada= $datos->CantidadTotal-$SumarActual;
                    return response()->json([
                        'Inicio'=>$Inicio,
                        'Fin'=>$Fin,
                        'status' => "success",
                        'Terminada'=>$Terminada,
                        'OrdenFabricacion'=>$datos->OrdenFabricacion,
                        'OF' => $CodigoPartes[0],       
                    ]);
                }else{//Todas las Areas excepto 4 y 17
                    $NumeroBloque = null;
                    if($Area != $this->AreaEspecialPulido){
                        $NumeroBloque = null;
                    }else{//Esto solo aplica a Pulido
                        $NumeroBloque = Partidasof_areas::whereNotNull('NumeroBloque')->OrderBy('NumeroBloque','desc')->first();
                        if($NumeroBloque==""){
                            $NumeroBloque = 1;
                        }else{
                            $NumeroBloque = $this->NumeroBloque($NumeroLinea);//$this->NumeroBloque($NumeroLinea);
                        }
                    }
                    if($AreaAnterior==3){
                        if($Retrabajo=="false"){
                            $NumeroPartidasTodasAnterior = $partidasOF->Areas()->where('Linea_id',$NumeroLinea)->where('Areas_id',$this->AreaEspecialClasificacion)->get()->SUM('pivot.Cantidad');
                            //Numero de piezasen Area actual
                            $NumeroPartidasAbiertas = $partidasOF->Areas()->where('Linea_id',$NumeroLinea)->where('Areas_id',$Area)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad');
                            $NumeroPartidasAbiertas=$NumeroPartidasAbiertas+$Cantidad;
                            if($NumeroPartidasAbiertas>$NumeroPartidasTodasAnterior){
                                return response()->json([
                                    'Inicio'=>$Inicio,
                                    'Fin'=>$Fin,
                                    'status' => "PasBackerror",
                                    'CantidadTotal' => "",
                                    'CantidadCompletada' => "",
                                    'OF' => $CodigoPartes[0],       
                                ]);
                            }
                            $data = [
                                'Cantidad' => $Cantidad,
                                'TipoPartida' => 'N', // N = Normal
                                'FechaComienzo' => now(),
                                'NumeroEtiqueta' =>0,
                                'Linea_id' => $NumeroLinea,
                                'NumeroBloque' => $NumeroBloque,
                                'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                            ];
                            $partidasOF->Areas()->attach($Area, $data);
                            return response()->json([
                                'Inicio'=>$Inicio,
                                'Fin'=>$Fin,
                                'status' => "success",
                                'OF' => $CodigoPartes[0],       
                            ]);
                        }else{
                            //Numero actual de terminados
                            $NumeroPartidasTodas = $partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida', '!=','F')->get()->SUM('pivot.Cantidad')
                                                        -$partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida', '=','F')->get()->SUM('pivot.Cantidad');
    
                            $NumeroPartidasTodasFinalizadas = $partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-$NumeroPartidasTodas;
                            if($NumeroPartidasTodasFinalizadas<$Cantidad){
                                return response()->json([
                                    'Inicio'=>$Inicio,
                                    'Fin'=>$Fin,
                                    'status' => "SurplusRetrabajo",
                                    'OF' => $CodigoPartes[0],       
                                ]);  
                            }
                            $data = [
                                'Cantidad' => $Cantidad,
                                'TipoPartida' => 'R', // N = Normal
                                'FechaComienzo' => now(),
                                'NumeroEtiqueta' =>0,
                                'Linea_id' => $NumeroLinea,
                                'NumeroBloque' => $NumeroBloque,
                                'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                            ];
                            $partidasOF->Areas()->attach($Area, $data);
                            if($Area==$this->AreaEspecialMontaje){
                                $datos->Cerrada = 1; 
                                $datos->save();
                            }
                            return response()->json([
                                'Inicio'=>$Inicio,
                                'Fin'=>$Fin,
                                'status' => "success",
                                'OF' => $CodigoPartes[0],       
                            ]);
                        }
                    }else{
                        if($Retrabajo=="false"){
                            $NumeroPartidasTodas = $partidasOF->Areas()->where('Areas_id',$AreaAnterior)->where('Linea_id',$NumeroLinea)->where('TipoPartida', '!=','F')->get()->SUM('pivot.Cantidad')
                                                        -$partidasOF->Areas()->where('Areas_id',$AreaAnterior)->where('Linea_id',$NumeroLinea)->where('TipoPartida', '=','F')->get()->SUM('pivot.Cantidad');
                            $NumeroPartidasTodasAnterior = $partidasOF->Areas()->where('Areas_id',$AreaAnterior)->where('Linea_id',$NumeroLinea)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-$NumeroPartidasTodas;
                            $NumeroPartidasAbiertas = $partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida','N')->whereNull('Fechatermina')->get()->SUM('pivot.Cantidad');
                            $NumeroPartidasAbiertas=$NumeroPartidasAbiertas+$Cantidad;
                            if($NumeroPartidasAbiertas>$NumeroPartidasTodasAnterior){
                                return response()->json([
                                    'Inicio'=>$Inicio,
                                    'Fin'=>$Fin,
                                    'status' => "PasBackerror",
                                    'CantidadTotal' => "",
                                    'CantidadCompletada' => "",
                                    'OF' => $CodigoPartes[0],       
                                ]);
                            }
                            $data = [
                                'Cantidad' => $Cantidad,
                                'TipoPartida' => 'N', // N = Normal
                                'FechaComienzo' => now(),
                                'NumeroEtiqueta' =>0,
                                'Linea_id' => $NumeroLinea,
                                'NumeroBloque' => $NumeroBloque,
                                'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                            ];
                            $partidasOF->Areas()->attach($Area, $data);
                            return response()->json([
                                'Inicio'=>$Inicio,
                                'Fin'=>$Fin,
                                'status' => "success",
                                'OF' => $CodigoPartes[0],   
                                'EstatusFinalizar'=> 'Hola22'    
                            ]);
                        }else{
                            //Numero actual de terminados
                            $NumeroPartidasTodas = $partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida', '!=','F')->get()->SUM('pivot.Cantidad')
                                                        -$partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida', '=','F')->get()->SUM('pivot.Cantidad');
                            $NumeroPartidasTodasFinalizadas = $partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-$NumeroPartidasTodas;
                            if($NumeroPartidasTodasFinalizadas<$Cantidad){
                                return response()->json([
                                    'Inicio'=>$Inicio,
                                    'Fin'=>$Fin,
                                    'status' => "SurplusRetrabajo",
                                    'OF' => $CodigoPartes[0],       
                                ]);  
                            }
                            $data = [
                                'Cantidad' => $Cantidad,
                                'TipoPartida' => 'R', // N = Normal
                                'FechaComienzo' => now(),
                                'NumeroEtiqueta' =>0,
                                'Linea_id' => $NumeroLinea,
                                'NumeroBloque' => $NumeroBloque,
                                'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                            ];
                            $partidasOF->Areas()->attach($Area, $data);
                            if($Area==$this->AreaEspecialMontaje){
                                $datos->Cerrada = 1; 
                                $datos->save();
                            }
                            return response()->json([
                                'Inicio'=>$Inicio,
                                'Fin'=>$Fin,
                                'status' => "success",
                                'OF' => $CodigoPartes[0],       
                            ]);
                        }
                    }
                }
        }else if($Fin==1){
            if($Area==$this->AreaEspecialTransicion){
                    $NumeroPartidasAbiertas = $partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida','!=','F')->get();
                    $NumeroPartidasAbiertas = $NumeroPartidasAbiertas->SUM('pivot.Cantidad');
                    $NumeroPartidasCerradas = $partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida','F')->get();
                    $NumeroPartidasCerradas = $NumeroPartidasCerradas->SUM('pivot.Cantidad');
                    $NumeroPartidasAbiertas = $NumeroPartidasAbiertas-$NumeroPartidasCerradas;
                    if($NumeroPartidasAbiertas<$Cantidad){
                        return response()->json([
                            'Inicio'=>$Inicio,
                            'Fin'=>$Fin,
                            'status' => "SurplusFin",
                            'CantidadTotal' => "",
                            'CantidadCompletada' => "",
                            'OF' => $CodigoPartes[0],       
                        ]);
                    }
                    $data = [
                        'Cantidad' => $Cantidad,
                        'TipoPartida' => 'F', // F = Finalizada
                        'FechaTermina' => now(),
                        'NumeroEtiqueta' =>0,
                        'Linea_id' => $NumeroLinea,
                        'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                    ];
                    $partidasOF->Areas()->attach($Area, $data);
                    return response()->json([
                        'Inicio'=>$Inicio,
                        'Fin'=>$Fin,
                        'status' => "success",
                        'OF' => $CodigoPartes[0],       
                    ]);
            }else if($Area==$this->AreaEspecialMontaje){
                    $NumeroPartidasAbiertas = $partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida','!=','F')->get();//->where()->where('Linea_id',$NumeroLinea)->whereNull('Fechatermina')->get();
                    $NumeroPartidasAbiertas = $NumeroPartidasAbiertas->SUM('pivot.Cantidad');
                    $NumeroPartidasCerradas = $partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida','F')->get();
                    $NumeroPartidasCerradas = $NumeroPartidasCerradas->SUM('pivot.Cantidad');
                    $NumeroPartidasAbiertas = $NumeroPartidasAbiertas-$NumeroPartidasCerradas;
                    if($NumeroPartidasAbiertas<$Cantidad){
                        return response()->json([
                            'Inicio'=>$Inicio,
                            'Fin'=>$Fin,
                            'status' => "SurplusFin",
                            'CantidadTotal' => "",
                            'CantidadCompletada' => "",
                            'OF' => $CodigoPartes[0],       
                        ]);
                    }
                    $data = [
                        'Cantidad' => $Cantidad,
                        'TipoPartida' => 'F', // F = Finalizada
                        'FechaTermina' => now(),
                        'NumeroEtiqueta' =>0,
                        'Linea_id' => $NumeroLinea,
                        'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                    ];
                    $partidasOF->Areas()->attach($Area, $data);
                    $CantidadCompletada=($partidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get()->SUM('pivot.Cantidad'))-($this->NumeroCompletadas($CodigoPartes,$Area));
                    if($CantidadCompletada==0){
                        $datos->Cerrada = 0; 
                        $datos->save();
                    }
                    return response()->json([
                        'Inicio'=>$Inicio,
                        'Fin'=>$Fin,
                        'status' => "success",
                        'OF' => $CodigoPartes[0],   
                        'BanderaFinalizar'=>  $CantidadCompletada,  
                    ]);
            }else{
                    $NumeroBloque = null;
                    //Puede servir en algun momento
                    /*if($Area != $this->AreaEspecialPulido){
                        $NumeroBloque = null;
                    }else{//Esto solo aplica a Pulido
                        $Abiertas = $partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida','!=','F')->get();
                        $Cerradas = $partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida','=','F')->get()->SUM('pivot.Cantidad');
                        $CantidadActual = 0;
                        $CantidadAnterior = 0;
                        foreach($Abiertas as $A){
                            $CantidadActual += $A['pivot']->Cantidad;
                            if($Cerradas>$CantidadAnterior AND $Cerradas<=$CantidadActual){
                                return$NumeroBloque = $A['pivot']->NumeroBloque;
                                break;
                            }
                        }
                    }*/
                    $NumeroPartidasAbiertas = $partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida','!=','F')->get();//->whereNull('Fechatermina')->get();
                    $NumeroPartidasAbiertas = $NumeroPartidasAbiertas->SUM('pivot.Cantidad');
                    $NumeroPartidasCerradas = $partidasOF->Areas()->where('Areas_id',$Area)->where('Linea_id',$NumeroLinea)->where('TipoPartida','=','F')->get();
                    $NumeroPartidasCerradas = $NumeroPartidasCerradas->SUM('pivot.Cantidad');
                    $NumeroPartidasAbiertas = $NumeroPartidasAbiertas-$NumeroPartidasCerradas;
                    if($NumeroPartidasAbiertas<$Cantidad){
                        return response()->json([
                            'Inicio'=>$Inicio,
                            'Fin'=>$Fin,
                            'status' => "SurplusFin",
                            'CantidadTotal' => "",
                            'CantidadCompletada' => "",
                            'OF' => $CodigoPartes[0],       
                        ]);
                    }
                    $data = [
                        'Cantidad' => $Cantidad,
                        'TipoPartida' => 'F', // F = Finalizada
                        'FechaTermina' => now(),
                        'NumeroEtiqueta' =>0,
                        'Linea_id' => $NumeroLinea,
                        'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                    ];
                    $partidasOF->Areas()->attach($Area, $data);
                    return response()->json([
                        'Inicio'=>$Inicio,
                        'Fin'=>$Fin,
                        'status' => "success",
                        'OF' => $CodigoPartes[0],       
                    ]);
            }
        }
    }
    public function ValidarPasoUnaVezAA($Area,$CodigoPartes){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $OrdenFabricacion=OrdenFabricacion::where('OrdenFabricacion',$CodigoPartes[0])->first();
        return$AreaAnterior = $this->AreaAnteriorregistros($Area,$OrdenFabricacion->OrdenFabricacion);
        $PartidasOF=$OrdenFabricacion->PartidasOF->where('NumeroPartida',$CodigoPartes[1])->first();
        $PasoUnaVezAA=$PartidasOF->Areas()->where('Areas_id',$AreaAnterior)->whereNotNull('FechaTermina')->where('NumeroEtiqueta',$CodigoPartes[2])->count();
        if($AreaAnterior==3){
            $PasoUnaVezAA=1;
        }
        return $PasoUnaVezAA;
    }
    //Comprueba si el codigo proporcionado se encuentra dentro del Rango de etiquetas
    public function ComprobarNumEtiqueta($CodigoPartes,$Area){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->first();
        if($datos==""){
            return 0;
        }
        $partidasOF=$datos->partidasOF()->first();
        //Return 0:No es el codigo; 1:Si es el codigo; 2:Aun no ha pasado por el area Anterior
        //Comprueba si los datos que vienen en el codigo Existen
        //$NumeroEtiquetas = $partidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get()->sum('pivot.Cantidad');
        $NumeroEtiquetas = $partidasOF->Areas()->where('Areas_id',$this->AreaEspecialCorte)->get()->sum('pivot.Cantidad');
        if($CodigoPartes[2]>$NumeroEtiquetas){
            return 0;
        }else{
            return 1;
        }
    }
    //Comprueba que la cantidad por Linea, Si es un retrabajo y tambien si el retrabajo lo quieren realizar en la misma linea
    public function ComprobarCantidadArea($CodigoPartes,$Area,$NumeroLinea){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->first();
        if($datos==""){
            return 0;
        }
        $partidasOF=$datos->partidasOF()->first();
        //Return 0:No es el codigo; 1:Si es el codigo; 2:Aun no ha pasado por el area Anterior
        //Comprueba si los datos que vienen en el codigo Existen
        $NumeroMaximo = $partidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->where('Linea_id',$NumeroLinea)->get()->SUM('pivot.Cantidad');//->where('Areas_id',$this->AreaEspecialCorte)->sum('pivot.Cantidad');
        $NumeroActual =$partidasOF->Areas()->where('Areas_id',$Area)->whereNull('FechaTermina')->where('Linea_id',$NumeroLinea)->where('TipoPartida','N')->SUM('Cantidad')
                            +$partidasOF->Areas()->where('Areas_id',$Area)->whereNotNull('FechaTermina')->where('Linea_id',$NumeroLinea)->where('TipoPartida','N')->SUM('Cantidad')
                            -$partidasOF->Areas()->where('Areas_id',$Area)->whereNull('FechaTermina')->where('Linea_id',$NumeroLinea)->where('TipoPartida','R')->SUM('Cantidad');
        $AreaAnterior = $this->AreaAnteriorregistros($Area,$datos->OrdenFabricacion);
        if($AreaAnterior != $this->AreaEspecialSuministro){
            $ExisteRegistro1 = $partidasOF->Areas()->where('Areas_id',$AreaAnterior)->OrderBy('id','desc')->get()->where('pivot.NumeroEtiqueta',$CodigoPartes[2]);
            if($ExisteRegistro1->count()>0){
                $ExisteRegistro1 = $ExisteRegistro1->first();
                if(!$ExisteRegistro1['pivot']->Linea_id==$NumeroLinea){
                    return 'ErrorLinea'; 
                }
            }else{
                return 'ErrorLinea';
            }
        }
        $ExisteRegistro = $partidasOF->Areas()->where('Areas_id',$Area)->get()->where('pivot.NumeroEtiqueta',$CodigoPartes[2]);
        if($ExisteRegistro->count()>0){
            $ExisteRegistro = $ExisteRegistro->first();
            if($ExisteRegistro['pivot']->Linea_id==$NumeroLinea){
                return $ExisteRegistro->count(); 
            }else{
                return 'ErrorLinea'; 
            }
        }
        $ExisteRegistroAnterior = $partidasOF->Areas()->where('Areas_id','<',$Area)->where('Areas_id','!=',$this->AreaEspecialCorte)
                ->where('Areas_id','!=',$this->AreaEspecialSuministro)
                ->where('Areas_id','!=',$this->AreaEspecialClasificacion)
                ->where('TipoPartida','N')->get()->where('pivot.NumeroEtiqueta',$CodigoPartes[2])->first();
        if($ExisteRegistroAnterior!= ""){
            if($ExisteRegistroAnterior['pivot']->Linea_id != $NumeroLinea){
                return 'ErrorLineaCodigo'; 
            }
        }
        return $NumeroMaximo-$NumeroActual;
    }
    //Estaciones metodos generales
    public function SuministroBuscar(Request $request){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if ($request->has('Confirmacion')) {
            $confirmacion=1;
        }else{
            $confirmacion=0;
        }
        $Codigo = $request->Codigo;
        $Inicio = $request->Inicio;
        $Finalizar = $request->Finalizar;
        $Area = $this->funcionesGenerales->decrypt($request->Area);
        $CodigoPartes = explode("-", $Codigo);
        $CodigoTam = count($CodigoPartes);
        $TipoEscanerrespuesta=0;
        $menu="";
        $Escaner="";
        $CantidadCompletada=0;
        $EscanerExiste=0;
        //Valida si el codigo es aceptado tiene que ser mayor a 2
        if($CodigoTam==3 && $CodigoPartes[2]!=""){
            $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->first();
            if($datos=="" OR $datos==null){
                return response()->json([
                    'tabla' => $menu,
                    'Escaner' => $Escaner,
                    'status' => "empty",
                    'CantidadTotal' => "",
                    'CantidadCompletada' => $CantidadCompletada,
                    'OF' => $CodigoPartes[0]
        
                ]);
            }else{
                $PartidasFaltantesList='';
                $CantidadTotal=$datos->CantidadTotal;
                //Variable  guarad el valor de Escaner para saber si es no 0=No escaner 1=escaner
                $Escaner=$datos->Escaner;
                if($CodigoTam==3){
                    if($Escaner==1){
                        if($Inicio==1){
                            //Comprobar si ya paso el paso anterior
                            return$TipoEscanerrespuesta=$this->ComprobarAreaAnterior($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada);
                            if($TipoEscanerrespuesta!=5){
                                //Comprobar si se encuentra iniciada en el paso posterior
                                $TipoEscanerrespuestaPosterior=$this->ComprobarAreaPosterior($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada);
                                if($TipoEscanerrespuestaPosterior!=6){
                                    $TipoEscanerrespuesta=$this->TipoEscanerGuardar($CodigoPartes,$CodigoTam,$Area,$confirmacion);
                                }
                            }
                        }else{
                            $TipoEscanerrespuesta=$this->TipoEscanerAreasFinalizar($CodigoPartes,$CodigoTam,$Area,$confirmacion);
                        }
                    }else if($Escaner==0){
                        $TipoManualrespuesta=$datos->partidasOF()->where('id','=',$CodigoPartes[1])->first();
                        if(!($TipoManualrespuesta=="" || $TipoManualrespuesta==null)){
                            $EscanerExiste = 1;
                        }else{
                            $EscanerExiste = 0;
                        }
                    }
                }
                if($Area==3){
                    if($Escaner==1){
                        foreach( $datos->partidasOF()->orderBy('id', 'desc')->get() as $PartidasordenFabricacion){
                            foreach( $PartidasordenFabricacion->Partidas()->orderBy('id', 'desc')->get() as $Partidas){
                                if(!($Partidas->Areas()->where('Areas_id','=',$Area)->first() =="" || $Partidas->Areas()->where('Areas_id','=',$Area)->first() == null )){
                                    $menu.='<tr>
                                    <td class="align-middle ps-3 NumParte">'.$Partidas->NumParte.'</td>
                                    <td class="align-middle text-center Cantidad">'.$Partidas->CantidadaPartidas.'</td>
                                    <td class="align-middle Inicio">'.$Partidas->Areas()->first()->pivot->FechaComienzo.'</td>
                                    <td class="align-middle Fin">'.$Partidas->Areas()->first()->pivot->FechaTermina.'</td>';
                                    if($Partidas->Areas()->first()->pivot->FechaTermina==""){
                                        $menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">En proceso</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                        if(!($Partidas->Areas()->first()->pivot->FechaTermina=="" || $Partidas->Areas()->first()->pivot->FechaTermina==null)){ //&& $Partidas->TipoAccion==0){
                                            $CantidadCompletada+=$Partidas->CantidadaPartidas;
                                        }
                                    }else{$menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Completado</span><span class="ms-1 fas fa-check"></span></div></td>';
                                        if(!($Partidas->Areas()->first()->pivot->FechaTermina=="" || $Partidas->Areas()->first()->pivot->FechaTermina==null)){ //&& $Partidas->TipoAccion==0){
                                            $CantidadCompletada+=$Partidas->CantidadaPartidas;
                                        }
                                    }
                                    $menu.='<td class="align-middle text-center Linea">'.$Partidas->Areas()->first()->pivot->Linea.'</td></tr>';
                                    if($Partidas->Estatus==2){
                                        $CantidadCompletada-=$Partidas->CantidadaPartidas;
                                    }
                                }
                            }
                        } 
                    }else{
                        foreach( $datos->partidasOF()->orderBy('id', 'desc')->get() as $PartidasordenFabricacion){
                            foreach( $PartidasordenFabricacion->Partidas()->orderBy('id', 'desc')->get() as $Partidas){
                                if(!($Partidas->Areas()->where('Areas_id','=',$Area)->first() =="" || $Partidas->Areas()->where('Areas_id','=',$Area)->first() == null )){
                                    $menu.='<tr>
                                    <td class="align-middle ps-3 NumParte">No especificado</td>
                                    <td class="align-middle Cantidad text-center">'.$Partidas->CantidadaPartidas.'</td>
                                    <td class="align-middle Inicio">'.$Partidas->Areas()->first()->pivot->FechaComienzo.'</td>
                                    <td class="align-middle Fin">'.$Partidas->Areas()->first()->pivot->FechaTermina.'</td>';
                                    if($Partidas->Areas()->first()->pivot->FechaTermina==""){
                                        if($Partidas->Estatus==1){$texto="Iniciado";$color="success";$icono="cog";}
                                        elseif($Partidas->Estatus==2){$texto="Retrabajo";$color="warning";$icono="cogs"; $CantidadCompletada-=$Partidas->CantidadaPartidas;}
                                        $menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-'.$color.'"><span class="fw-bold">'.$texto.'</span><span class="ms-1 fas fa-'.$icono.'"></span></div></td>';
                                        if(!($Partidas->Areas()->first()->pivot->FechaTermina=="" || $Partidas->Areas()->first()->pivot->FechaTermina==null) && $Partidas->TipoAccion==0){
                                            $CantidadCompletada+=$Partidas->CantidadaPartidas;
                                        }
                                    }else{$menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-primary"><span class="fw-bold">Finalizado</span><span class="ms-1 fas fa-check"></span></div></td>';
                                        if(!($Partidas->Areas()->first()->pivot->FechaTermina=="" || $Partidas->Areas()->first()->pivot->FechaTermina==null) && $Partidas->TipoAccion==0){
                                            $CantidadCompletada+=$Partidas->CantidadaPartidas;
                                        }
                                    }
                                    $menu.='<td class="align-middle Linea text-center">'.$Partidas->Areas()->first()->pivot->Linea_id.'</td>
                                        
                                        </tr>';
                                }
                            }
                        }
                    }
                }else{
                    $ArrayNumParte=[];
                    if($Escaner==1){
                        $PartidasFaltantesList="";
                        $partidas = $datos->partidasOF()
                        ->join('partidas', 'partidasOF.id', '=', 'partidas.PartidasOF_id')  // JOIN entre PartidasOF y Partidas
                        ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')  // JOIN con la tabla pivote (ajustar nombre de la tabla)
                        ->join('areas', 'partidas_areas.Areas_id', '=', 'areas.id')  // JOIN con Areas a través de la tabla pivote
                        ->where('areas.id', '=', $Area)  // Filtro por un área específica
                        ->orderBy('partidas.id', 'desc')  // Ordenar las partidas
                        ->select('partidasOF.*', 'partidas.*', 'areas.*','partidas_areas.*')  // Seleccionar todas las columnas de las tres tablas
                        ->get();
                        foreach( $partidas as $PartidasordenFabricacion){
                            $ArrayNumParte[]=$PartidasordenFabricacion->NumParte;
                            $menu.='<tr>
                                    <td class="align-middle ps-3 NumParte">'.$PartidasordenFabricacion->NumParte.'</td>
                                    <td class="align-middle text-center Cantidad">'.$PartidasordenFabricacion->CantidadaPartidas.'</td>
                                    <td class="align-middle Inicio">'.$PartidasordenFabricacion->FechaComienzo.'</td>
                                    <td class="align-middle Fin">'.$PartidasordenFabricacion->FechaTermina.'</td>';
                                    if($PartidasordenFabricacion->FechaTermina==""){
                                        $menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">En proceso</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                        if(!($PartidasordenFabricacion->FechaTermina=="" || $PartidasordenFabricacion->FechaTermina==null)){ //&& $Partidas->TipoAccion==0){
                                            $CantidadCompletada+=$PartidasordenFabricacion->CantidadaPartidas;
                                        }
                                    }else{$menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Completado</span><span class="ms-1 fas fa-check"></span></div></td>';
                                        if(!($PartidasordenFabricacion->FechaTermina=="" || $PartidasordenFabricacion->FechaTermina==null)){ //&& $Partidas->TipoAccion==0){
                                            $CantidadCompletada+=$PartidasordenFabricacion->CantidadaPartidas;
                                        }
                                    }
                                    $menu.='<td class="align-middle text-center Linea">'.$PartidasordenFabricacion->Linea.'</td></tr>';
                            $PartidasFaltantesList.='<li class="p-0 m-0 list-group-item d-flex justify-content-between align-items-center">'.$CodigoPartes[0]-$CodigoPartes[1].'<span class="badge badge-light-primary rounded-pill">14</span></li>';         
                        }
                    }else{
                        $PartidasFaltantesList="";
                        foreach( $datos->partidasOF()->orderBy('id', 'desc')->get() as $PartidasordenFabricacion){
                            foreach( $PartidasordenFabricacion->Partidas()->orderBy('id', 'desc')->get() as $Partidas){
                                if(!($Partidas->Areas()->where('Areas_id','=',$Area)->first() =="" || $Partidas->Areas()->where('Areas_id','=',$Area)->first() == null )){
                                    $menu.='<tr>
                                    <td class="align-middle ps-3 NumParte">No especificado</td>
                                    <td class="align-middle Cantidad text-center">'.$Partidas->CantidadaPartidas.'</td>
                                    <td class="align-middle Inicio">'.$Partidas->Areas()->first()->pivot->FechaComienzo.'</td>
                                    <td class="align-middle Fin">'.$Partidas->Areas()->first()->pivot->FechaTermina.'</td>';
                                    if($Partidas->Areas()->first()->pivot->FechaTermina==""){
                                        if($Partidas->Estatus==1){$texto="Iniciado";$color="success";$icono="cog";}
                                        elseif($Partidas->Estatus==2){$texto="Retrabajo";$color="warning";$icono="cogs"; $CantidadCompletada-=$Partidas->CantidadaPartidas;}
                                        $menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-'.$color.'"><span class="fw-bold">'.$texto.'</span><span class="ms-1 fas fa-'.$icono.'"></span></div></td>';
                                        if(!($Partidas->Areas()->first()->pivot->FechaTermina=="" || $Partidas->Areas()->first()->pivot->FechaTermina==null) && $Partidas->TipoAccion==0){
                                            $CantidadCompletada+=$Partidas->CantidadaPartidas;
                                        }
                                    }else{$menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-primary"><span class="fw-bold">Finalizado</span><span class="ms-1 fas fa-check"></span></div></td>';
                                        if(!($Partidas->Areas()->first()->pivot->FechaTermina=="" || $Partidas->Areas()->first()->pivot->FechaTermina==null) && $Partidas->TipoAccion==0){
                                            $CantidadCompletada+=$Partidas->CantidadaPartidas;
                                        }
                                    }
                                    $menu.='<td class="align-middle Linea text-center">'.$Partidas->Areas()->first()->pivot->Linea_id.'</td>
                                        </tr>';
                                }
                            }
                            $PartidasFaltantesList.='<li class="p-0 m-0 list-group-item d-flex justify-content-between align-items-center">'.$CodigoPartes[0]-$CodigoPartes[1].'<span class="badge badge-light-primary rounded-pill">14</span></li>';
                        }
                    }
                    $ArrayNumParte=array_count_values($ArrayNumParte);
                    foreach ($ArrayNumParte as $key => $item) {
                        if($item>1){
                            $CantidadCompletada-=($item-1);
                        }
                    }
                }
                if($CantidadCompletada<0){
                    $CantidadCompletada=0; 
                }
                if($Escaner==0){
                    $Opciones='<option selected="" value="">Todos</option>
                        <option value="Iniciado">Iniciado</option>
                        <option value="Retrabajo">Retrabajo</option>
                        <option value="Finalizado">Finalizado</option>';
                        
                }else{
                    $Opciones='<option selected="" value="">Todos</option>
                        <option value="En proceso">En proceso</option>
                        <option value="Completado">Completado</option>';
                }
                $menu='<div class="card-body">
                    <div id="ContainerTableSuministros" class="table-list">
                        <div class="row justify-content-start g-0">
                            <div class="col-auto px-3">
                            <div class="badge badge-phoenix fs--4 badge-phoenix-secondary"><span class="fw-bold">Piezas Completadas </span>'.$CantidadCompletada.'/'.$CantidadTotal.'<span class="ms-1 fas fa-stream"></span></div>
                            </div>
                        </div>
                        <div class="row justify-content-end g-0">
                            <div class="col-auto px-3">
                                <div class="dropdown font-sans-serif d-inline-block">
                                    <button class="btn btn-phoenix-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">Partidas <i class="fas fa-grip-horizontal"></i></button>
                                </div>
                            </div>
                            <div class="col-auto px-3">
                                <select class="form-select form-select-sm mb-3" data-list-filter="data-list-filter">
                                '.$Opciones.'
                                </select>
                            </div>
                            <div class="collapse p-1" id="collapseExample">
                                <ul class="list-group">
                                '.$PartidasFaltantesList.'    
                                </ul>
                            </div>
                        </div>
                        <div class="table-responsive scrollbar mb-3">
                        <table id="TableSuministros" class="table table-striped table-sm fs--1 mb-0 overflow-hidden">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th class="sort border-top ps-3" data-sort="NumParte">Num. Parte</th>
                                    <th class="sort border-top" data-sort="Cantidad">Cantidad</th>
                                    <th class="sort border-top" data-sort="Inicio">Inicio</th>
                                    <th class="sort border-top" data-sort="Fin">Fin</th>
                                    <th class="sort border-top" data-sort="Estatus">Estatus</th>
                                    <th class="sort border-top ps-3" data-sort="Linea">Linea</th>
                                
                                </tr>
                            </thead>
                            <tbody class="list" id="TablaBody">
                                '.$menu.'
                            </tbody>
                        </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                            <ul class="mb-0 pagination"></ul>
                            <button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                        </div>
                    </div>';
                return response()->json([
                    'tabla' => $menu,
                    'Escaner' => $Escaner,
                    'EscanerExiste' => $EscanerExiste,
                    'status' => "success",
                    'CantidadTotal' => $CantidadTotal,
                    'Inicio' => $Inicio,
                    'Finalizar' =>$Finalizar,
                    'TipoEscanerrespuesta'=>$TipoEscanerrespuesta,
                    'CantidadCompletada' => $CantidadCompletada,
                    'OF' => $CodigoPartes[0]
        
                ]);
            }
        }else{
            return response()->json([
                'tabla' => $menu,
                'Escaner' => "",
                'status' => "NoExiste",
                'CantidadTotal' => "",
                'CantidadCompletada' => 4,
                'OF' => $CodigoPartes[0]

            ]);
        }
    }
    public function TablaOrdenesActivasEstacion($Area){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if($Area==$this->AreaEspecialTransicion){
            $NumeroArea4=0;
            $ordenes4 = OrdenFabricacion::with('PartidasOF')
                            ->where('OrdenFabricacion.Cerrada','1')
                            ->whereHas('PartidasOF') 
                            ->get();
            foreach($ordenes4 as $ordenes){
                foreach($ordenes->partidasOF as $areas){
                    return$NumeroArea4=$areas;//->Areas->where('Areas_id',$Area);//->SUM('Cantidad');
                }
            }
            return $NumeroArea4;
        }
       //return$PartidasArea=Partidasof::get();
        //$Area=((int)$Area)-1;
    }
    //Area 6 Ribonizado
    public function Ribonizado(){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if($user->hasPermission('Vista Ribonizado')){
        $AreaOriginal=6;
        $Area = $this->funcionesGenerales->encrypt($AreaOriginal);
        /*$Registros = $this->OrdenFabricacionPendiente($AreaOriginal - 1);
        foreach ($Registros as $key => $registro) {
            $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
            $Linea = $OrdenFabricacion->Linea()->first();
            $SumarActual = 0;
            $TotalPendiente = 0;
            $NumeroPartidasTodas = 0;
            $banderaSinRegistros=0;
            $AreaAnterior=$this->AreaAnteriorregistros($AreaOriginal, $OrdenFabricacion->OrdenFabricacion);
            foreach ($OrdenFabricacion->PartidasOF as $Partidas) {
                $Area4 = PartidasOF::find($Partidas->id);
                if($AreaAnterior==$this->AreaEspecialSuministro){
                    if($OrdenFabricacion->Escaner==1){
                        $SumarActual += $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                    }else{
                        //En esta parte el codigo es diferente al de las demas
                        //Sacamos la cantidad total de las piezas que ya pasaron
                        $SumarActual +=$Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                     ($Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                    - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        //Cantidad Pendiente que paso de una Area anterior
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                    - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','=', 'F')->count()==0){
                            $banderaSinRegistros=1;
                        }
                    }
                }else{
                    if($OrdenFabricacion->Escaner==1){
                        //$banderaSinRegistros=0;
                        $SumarActual += $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                    }else{
                        //Sacamos la cantidad total de las piezas que ya pasaron
                        $SumarActual +=$Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                     ($Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                    - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        //Cantidad Pendiente que paso de una Area anterior
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                            ($Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                            - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','=', 'F')->count()==0){
                            $banderaSinRegistros=10;
                        }
                    }
                }
            }
            $registro['NumeroActuales'] = $SumarActual;
            $registro['TotalPendiente'] = $TotalPendiente;
            $registro['Linea'] = $Linea->NumeroLinea;
            $registro['ColorLinea'] = $Linea->ColorLinea;
            $registro['Area'] = $AreaAnterior;
            if($TotalPendiente==0){
                unset($Registros[$key]);
            }
            if ($SumarActual == $TotalPendiente AND $banderaSinRegistros==0) {
                unset($Registros[$key]);
            }
        }
        foreach($Registros as $key => $registro){
            $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
            $PartidasOF = $OrdenFabricacion->PartidasOF()->first();
            $PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
            if($AreaAnterior <=  $this->AreaEspecialSuministro){
                $PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
            }else{$PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$AreaAnterior)->get();}
            if($PartidasOFArea->count() == 0){
                unset($Registros[$key]);
            }else{
                $PartidasOFAreaUnique = $PartidasOFArea->unique('pivot.Linea_id');//Sacamos las Lineas en las que tiene registros esta Area  
                foreach($PartidasOFAreaUnique as $key1 => $POFA){
                    if($AreaAnterior <= $this->AreaEspecialSuministro){
                        $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id',$this->AreaEspecialClasificacion)->get()->sum('pivot.Cantidad');
                        if($OrdenFabricacion->Escaner==1){
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');;
                        }else{
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        }
                        $POFA['Anterior'] = $SumarAnterior;
                        $POFA['Actual'] = $SumarActual;
                        $Linea = Linea::find($POFA['pivot']->Linea_id);
                        $POFA['Linea'] = $Linea->NumeroLinea;
                        $POFA['ColorLinea'] = $Linea->ColorLinea;
                        if($SumarAnterior<=$SumarActual){
                            unset($PartidasOFAreaUnique[$key1]);
                        }
                    }else{
                        if($OrdenFabricacion->Escaner==1){
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        }else{
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                         ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                        - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            //Cantidad Pendiente que paso de una Area anterior
                            $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                                ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                                - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        }
                        $POFA['Anterior'] = $SumarAnterior;
                        $POFA['Actual'] = $SumarActual;
                        $Linea = Linea::find($POFA['pivot']->Linea_id);
                        $POFA['Linea'] = $Linea->NumeroLinea;
                        $POFA['ColorLinea'] = $Linea->ColorLinea;
                        if($SumarAnterior<=$SumarActual){
                            unset($PartidasOFAreaUnique[$key1]);
                        }
                    }
                    if($PartidasOFAreaUnique->count()==0){
                        unset($Registros[$key]);
                    }
                }
                if($PartidasOFAreaUnique->count()>0){
                    $Registros[$key]['PartidasOFFaltantes']=$PartidasOFAreaUnique;
                }
                //return$PartidasOFComp = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
            }
        }*/
            $Lineas=Linea::where('id','!=',1)->get();
        return view('Areas.Ribonizado',compact('Area','Lineas'));
        }else{
            return redirect()->route('error.');
        }
    }
    //Area 7 Ensamble
    public function Ensamble(){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if($user->hasPermission('Vista Ensamble')){
        $AreaOriginal=7;
        $Area = $this->funcionesGenerales->encrypt($AreaOriginal);
            /*$Registros = $this->OrdenFabricacionPendiente($AreaOriginal - 1);
            foreach ($Registros as $key => $registro) {
                $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
                $Linea = $OrdenFabricacion->Linea()->first();
                $SumarActual = 0;
                $TotalPendiente = 0;
                $NumeroPartidasTodas = 0;
                $banderaSinRegistros=0;
                $AreaAnterior=$this->AreaAnteriorregistros($AreaOriginal, $OrdenFabricacion->OrdenFabricacion);
                foreach ($OrdenFabricacion->PartidasOF as $Partidas) {
                    $Area4 = PartidasOF::find($Partidas->id);
                    if($AreaAnterior==$this->AreaEspecialSuministro){
                        if($OrdenFabricacion->Escaner==1){
                            $SumarActual += $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        }else{
                            //En esta parte el codigo es diferente al de las demas
                            //Sacamos la cantidad total de las piezas que ya pasaron
                            $SumarActual +=$Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                         ($Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                        - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            //Cantidad Pendiente que paso de una Area anterior
                            $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                        - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','=', 'F')->count()==0){
                                $banderaSinRegistros=1;
                            }
                        }
                    }else{
                        if($OrdenFabricacion->Escaner==1){
                            //$banderaSinRegistros=0;
                            $SumarActual += $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        }else{
                            //Sacamos la cantidad total de las piezas que ya pasaron
                            $SumarActual +=$Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                         ($Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                        - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            //Cantidad Pendiente que paso de una Area anterior
                            $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                                ($Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                                - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','=', 'F')->count()==0){
                                $banderaSinRegistros=10;
                            }
                        }
                    }
                }
                $registro['NumeroActuales'] = $SumarActual;
                $registro['TotalPendiente'] = $TotalPendiente;
                $registro['Linea'] = $Linea->NumeroLinea;
                $registro['ColorLinea'] = $Linea->ColorLinea;
                $registro['Area'] = $AreaAnterior;
                if($TotalPendiente==0){
                    unset($Registros[$key]);
                }
                if ($SumarActual == $TotalPendiente AND $banderaSinRegistros==0) {
                    unset($Registros[$key]);
                }
            }
            foreach($Registros as $key => $registro){
                $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
                $PartidasOF = $OrdenFabricacion->PartidasOF()->first();
                $PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
                if($AreaAnterior <=  $this->AreaEspecialSuministro){
                    $PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
                }else{$PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$AreaAnterior)->get();}
                if($PartidasOFArea->count() == 0){
                    unset($Registros[$key]);
                }else{
                    $PartidasOFAreaUnique = $PartidasOFArea->unique('pivot.Linea_id');//Sacamos las Lineas en las que tiene registros esta Area  
                    foreach($PartidasOFAreaUnique as $key1 => $POFA){
                        if($AreaAnterior <= $this->AreaEspecialSuministro){
                            $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id',$this->AreaEspecialClasificacion)->get()->sum('pivot.Cantidad');
                            if($OrdenFabricacion->Escaner==1){
                                $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                    - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');;
                            }else{
                                $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                    ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                    - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            }
                            $POFA['Anterior'] = $SumarAnterior;
                            $POFA['Actual'] = $SumarActual;
                            $Linea = Linea::find($POFA['pivot']->Linea_id);
                            $POFA['Linea'] = $Linea->NumeroLinea;
                            $POFA['ColorLinea'] = $Linea->ColorLinea;
                            if($SumarAnterior<=$SumarActual){
                                unset($PartidasOFAreaUnique[$key1]);
                            }
                        }else{
                            if($OrdenFabricacion->Escaner==1){
                                $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                    - $Partidas->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                                $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                    - $Partidas->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            }else{
                                $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                             ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                            - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                                //Cantidad Pendiente que paso de una Area anterior
                                $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                                    ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                                    - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            }
                            $POFA['Anterior'] = $SumarAnterior;
                            $POFA['Actual'] = $SumarActual;
                            $Linea = Linea::find($POFA['pivot']->Linea_id);
                            $POFA['Linea'] = $Linea->NumeroLinea;
                            $POFA['ColorLinea'] = $Linea->ColorLinea;
                            if($SumarAnterior<=$SumarActual){
                                unset($PartidasOFAreaUnique[$key1]);
                            }
                        }
                        if($PartidasOFAreaUnique->count()==0){
                            unset($Registros[$key]);
                        }
                    }
                    if($PartidasOFAreaUnique->count()>0){
                        $Registros[$key]['PartidasOFFaltantes']=$PartidasOFAreaUnique;
                    }
                    //return$PartidasOFComp = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
                }
            }*/
            $Lineas=Linea::where('id','!=',1)->get();
            
        return view('Areas.Ensamble',compact('Area','Lineas'));
        }else{
            return redirect()->route('error.');
        }
    }
    //Area 8 Pulido
    public function Cortedefibra(){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if($user->haspermission('Vista Corte de fibra')){
        $AreaOriginal=8;
        $Area = $this->funcionesGenerales->encrypt($AreaOriginal);
        /*$Registros = $this->OrdenFabricacionPendiente($AreaOriginal - 1);
        foreach ($Registros as $key => $registro) {
            $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
            $Linea = $OrdenFabricacion->Linea()->first();
            $SumarActual = 0;
            $TotalPendiente = 0;
            $NumeroPartidasTodas = 0;
            $banderaSinRegistros=0;
            $AreaAnterior=$this->AreaAnteriorregistros($AreaOriginal, $OrdenFabricacion->OrdenFabricacion);
            foreach ($OrdenFabricacion->PartidasOF as $Partidas) {
                $Area4 = PartidasOF::find($Partidas->id);
                if($AreaAnterior==$this->AreaEspecialSuministro){
                    if($OrdenFabricacion->Escaner==1){
                        $SumarActual += $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                    }else{
                        //En esta parte el codigo es diferente al de las demas
                        //Sacamos la cantidad total de las piezas que ya pasaron
                        $SumarActual +=$Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                     ($Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                    - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        //Cantidad Pendiente que paso de una Area anterior
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                    - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','=', 'F')->count()==0){
                            $banderaSinRegistros=1;
                        }
                    }
                }else{
                    if($OrdenFabricacion->Escaner==1){
                        //$banderaSinRegistros=0;
                        $SumarActual += $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                    }else{
                        //Sacamos la cantidad total de las piezas que ya pasaron
                        $SumarActual +=$Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                     ($Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                    - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        //Cantidad Pendiente que paso de una Area anterior
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                            ($Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                            - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','=', 'F')->count()==0){
                            $banderaSinRegistros=10;
                        }
                    }
                }
            }
            $registro['NumeroActuales'] = $SumarActual;
            $registro['TotalPendiente'] = $TotalPendiente;
            $registro['Linea'] = $Linea->NumeroLinea;
            $registro['ColorLinea'] = $Linea->ColorLinea;
            $registro['Area'] = $AreaAnterior;
            if($TotalPendiente==0){
                unset($Registros[$key]);
            }
            if ($SumarActual == $TotalPendiente AND $banderaSinRegistros==0) {
                unset($Registros[$key]);
            }
        }
        foreach($Registros as $key => $registro){
            $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
            $PartidasOF = $OrdenFabricacion->PartidasOF()->first();
            $PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
            if($AreaAnterior <=  $this->AreaEspecialSuministro){
                $PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
            }else{$PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$AreaAnterior)->get();}
            if($PartidasOFArea->count() == 0){
                unset($Registros[$key]);
            }else{
                $PartidasOFAreaUnique = $PartidasOFArea->unique('pivot.Linea_id');//Sacamos las Lineas en las que tiene registros esta Area  
                foreach($PartidasOFAreaUnique as $key1 => $POFA){
                    if($AreaAnterior <= $this->AreaEspecialSuministro){
                        $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id',$this->AreaEspecialClasificacion)->get()->sum('pivot.Cantidad');
                        if($OrdenFabricacion->Escaner==1){
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');;
                        }else{
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        }
                        $POFA['Anterior'] = $SumarAnterior;
                        $POFA['Actual'] = $SumarActual;
                        $Linea = Linea::find($POFA['pivot']->Linea_id);
                        $POFA['Linea'] = $Linea->NumeroLinea;
                        $POFA['ColorLinea'] = $Linea->ColorLinea;
                        if($SumarAnterior<=$SumarActual){
                            unset($PartidasOFAreaUnique[$key1]);
                        }
                    }else{
                        if($OrdenFabricacion->Escaner==1){
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        }else{
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                         ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                        - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            //Cantidad Pendiente que paso de una Area anterior
                            $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                                ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                                - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        }
                        $POFA['Anterior'] = $SumarAnterior;
                        $POFA['Actual'] = $SumarActual;
                        $Linea = Linea::find($POFA['pivot']->Linea_id);
                        $POFA['Linea'] = $Linea->NumeroLinea;
                        $POFA['ColorLinea'] = $Linea->ColorLinea;
                        if($SumarAnterior<=$SumarActual){
                            unset($PartidasOFAreaUnique[$key1]);
                        }
                    }
                    if($PartidasOFAreaUnique->count()==0){
                        unset($Registros[$key]);
                    }
                }
                if($PartidasOFAreaUnique->count()>0){
                    $Registros[$key]['PartidasOFFaltantes']=$PartidasOFAreaUnique;
                }
                //return$PartidasOFComp = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
            }
        }*/
        $Lineas=Linea::where('id','!=',1)->get();
        return view('Areas.Cortedefibra',compact('Area','Lineas'));
        }else{
            return redirect()->route('error.');
        }
    }
    //Area 9 Pulido
    public function Pulido(){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if($user->haspermission('Vista Pulido')){
        $AreaOriginal=9;
        $Area = $this->funcionesGenerales->encrypt($AreaOriginal);
        /*$Registros = $this->OrdenFabricacionPendiente($AreaOriginal - 1);
        foreach ($Registros as $key => $registro) {
            $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
            $Linea = $OrdenFabricacion->Linea()->first();
            $SumarActual = 0;
            $TotalPendiente = 0;
            $NumeroPartidasTodas = 0;
            $banderaSinRegistros=0;
            $AreaAnterior=$this->AreaAnteriorregistros($AreaOriginal, $OrdenFabricacion->OrdenFabricacion);
            foreach ($OrdenFabricacion->PartidasOF as $Partidas) {
                $Area4 = PartidasOF::find($Partidas->id);
                if($AreaAnterior==$this->AreaEspecialSuministro){
                    if($OrdenFabricacion->Escaner==1){
                        $SumarActual += $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                    }else{
                        //En esta parte el codigo es diferente al de las demas
                        //Sacamos la cantidad total de las piezas que ya pasaron
                        $SumarActual +=$Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                     ($Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                    - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        //Cantidad Pendiente que paso de una Area anterior
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                    - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','=', 'F')->count()==0){
                            $banderaSinRegistros=1;
                        }
                    }
                }else{
                    if($OrdenFabricacion->Escaner==1){
                        //$banderaSinRegistros=0;
                        $SumarActual += $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                            - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                    }else{
                        //Sacamos la cantidad total de las piezas que ya pasaron
                        $SumarActual +=$Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                     ($Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                    - $Partidas->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        //Cantidad Pendiente que paso de una Area anterior
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                            ($Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                            - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        if($Area4->Areas()->where('Areas_id', $AreaOriginal)->where('TipoPartida','=', 'F')->count()==0){
                            $banderaSinRegistros=10;
                        }
                    }
                }
            }
            $registro['NumeroActuales'] = $SumarActual;
            $registro['TotalPendiente'] = $TotalPendiente;
            $registro['Linea'] = $Linea->NumeroLinea;
            $registro['ColorLinea'] = $Linea->ColorLinea;
            $registro['Area'] = $AreaAnterior;
            if($TotalPendiente==0){
                unset($Registros[$key]);
            }
            if ($SumarActual == $TotalPendiente AND $banderaSinRegistros==0) {
                unset($Registros[$key]);
            }
        }
        foreach($Registros as $key => $registro){
            $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
            $PartidasOF = $OrdenFabricacion->PartidasOF()->first();
            $PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
            if($AreaAnterior <=  $this->AreaEspecialSuministro){
                $PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
            }else{$PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$AreaAnterior)->get();}
            if($PartidasOFArea->count() == 0){
                unset($Registros[$key]);
            }else{
                $PartidasOFAreaUnique = $PartidasOFArea->unique('pivot.Linea_id');//Sacamos las Lineas en las que tiene registros esta Area  
                foreach($PartidasOFAreaUnique as $key1 => $POFA){
                    if($AreaAnterior <= $this->AreaEspecialSuministro){
                        $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id',$this->AreaEspecialClasificacion)->get()->sum('pivot.Cantidad');
                        if($OrdenFabricacion->Escaner==1){
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');;
                        }else{
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        }
                        $POFA['Anterior'] = $SumarAnterior;
                        $POFA['Actual'] = $SumarActual;
                        $Linea = Linea::find($POFA['pivot']->Linea_id);
                        $POFA['Linea'] = $Linea->NumeroLinea;
                        $POFA['ColorLinea'] = $Linea->ColorLinea;
                        if($SumarAnterior<=$SumarActual){
                            unset($PartidasOFAreaUnique[$key1]);
                        }
                    }else{
                        if($OrdenFabricacion->Escaner==1){
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        }else{
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                         ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                        - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaOriginal)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            //Cantidad Pendiente que paso de una Area anterior
                            $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                                ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                                - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        }
                        $POFA['Anterior'] = $SumarAnterior;
                        $POFA['Actual'] = $SumarActual;
                        $Linea = Linea::find($POFA['pivot']->Linea_id);
                        $POFA['Linea'] = $Linea->NumeroLinea;
                        $POFA['ColorLinea'] = $Linea->ColorLinea;
                        if($SumarAnterior<=$SumarActual){
                            unset($PartidasOFAreaUnique[$key1]);
                        }
                    }
                    if($PartidasOFAreaUnique->count()==0){
                        unset($Registros[$key]);
                    }
                }
                if($PartidasOFAreaUnique->count()>0){
                    $Registros[$key]['PartidasOFFaltantes']=$PartidasOFAreaUnique;
                }
                //return$PartidasOFComp = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
            }
        }*/
        $Lineas=Linea::where('id','!=',1)->get();
        return view('Areas.Pulido',compact('Area','Lineas'));
        }else{
            return redirect()->route('error.');
        }
    }
    public function PulidoCerrarPlato(Request $request){
        if($request->Area== "" || $request->NumBloque==""){
            return response()->json([
                'status' => "Error",
                'message' => "Datos no validos.",
            ]);
        }
        $NumBloque = $request->NumBloque;
        $Area = $this->funcionesGenerales->decrypt($request->Area);
        $Partidas = Partidasof_Areas::where('NumeroBloque',$request->NumBloque)->get();
        if($Partidas->count() == 0){
            return response()->json([
                'status' => "Error",
                'message' => "No hay Datos por cerrar en este bloque ",
            ]);
        }
        if($Partidas[0]->CerrarBloque == 1){
            return response()->json([
                'status' => "Error",
                'message' => "No hay Datos por cerrar en este bloque ",
            ]);
        }
        $Codigo = "";
        foreach($Partidas as $P){
            $PartidasOF = PartidasOF::find($P->PartidasOF_id);
            $OrdenFabricacion = $PartidasOF->OrdenFabricacion;
            if($OrdenFabricacion->Escaner == 1){
                $P->FechaTermina  = now();
                $P->save();
            }else{
                $data = [
                    'Cantidad' => $P->Cantidad,
                    'TipoPartida' => 'F', // F = Finalizada
                    'FechaTermina' => now(),
                    'NumeroEtiqueta' =>0,
                    'Linea_id' => $P->Linea_id,
                    'NumeroBloque' => $P->NumeroBloque,
                    'CerrarBloque' => 1,
                    'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                ];
                $PartidasOF->Areas()->attach($Area, $data);
            }
            $P->CerrarBloque=1;
            $P->save();
            $Codigo = $OrdenFabricacion->OrdenFabricacion.'-1-'.$P->NumeroEtiqueta;
        }
        return response()->json([
            'status' => "Success",
            'message' => "Datos del Plato guardados correctamente.",
            'Codigo' => $Codigo
        ]);
    }
    public function PulidoLinea(Request $request){
        $Linea = Linea::where('NumeroLinea',$request->Linea)->first();
        if($Linea==""){
            return 0;
        }
        $PartidasAreas = Partidasof_Areas::where('Linea_id',$Linea->id)->where('Areas_id',$this->AreaEspecialPulido)->whereNull('CerrarBloque')->orderBy('FechaTermina','desc')->get();
        if($PartidasAreas->count() == 0){
            return 0;
        }
        $PartidasAreas = $PartidasAreas->first();
        $PartidaOF = PartidasOF::find($PartidasAreas->PartidasOF_id);
        $OrdenFabricacion = $PartidaOF->OrdenFabricacion->OrdenFabricacion;
        return $OrdenFabricacion.'-1-'.$PartidasAreas->NumeroEtiqueta;
    }
    //Area 10 Pulido
    public function Armado(){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if($user->haspermission('Vista Armado')){
        $AreaOriginal=10;
        $Area = $this->funcionesGenerales->encrypt($AreaOriginal);
        $Lineas=Linea::where('id','!=',1)->get();
        return view('Areas.Armado',compact('Area','Lineas'));
        }else{
            return redirect()->route('error.');
        }
    }
    //Area 11 Inspeccion
    public function Inspeccion(){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if($user->haspermission('Vista Inspección')){
        $AreaOriginal=11;
        $Area = $this->funcionesGenerales->encrypt($AreaOriginal);
        $Lineas=Linea::where('id','!=',1)->get();
        return view('Areas.Inspeccion',compact('Area','Lineas'));
        }else{
            return redirect()->route('error.');
        }
    }
    //Area 12 Polaridad
    public function Polaridad(){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if($user->haspermission('Vista Polaridad')){
        $AreaOriginal=12;
        $Area = $this->funcionesGenerales->encrypt($AreaOriginal);
        $Lineas=Linea::where('id','!=',1)->get();
        return view('Areas.Polaridad',compact('Area','Lineas'));
        }else{
            return redirect()->route('error.');
        }
    }
    //Area 13 Crimpado
    public function Crimpado(){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if($user->haspermission('Vista Crimpado')){
        $AreaOriginal=13;
        $Area = $this->funcionesGenerales->encrypt($AreaOriginal);
        $Lineas=Linea::where('id','!=',1)->get();
        return view('Areas.Crimpado',compact('Area','Lineas'));
        }else{
            return redirect()->route('error.');
        }
    }
    //Area 14 Medicion
    public function Medicion(){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if($user->hasPermission('Vista Medicion')){
        $AreaOriginal=14;
        $Area = $this->funcionesGenerales->encrypt($AreaOriginal);
        $Lineas=Linea::where('id','!=',1)->get();
        return view('Areas.Medicion',compact('Area','Lineas'));
        }else{
            return redirect()->route('error.');
        }
    }
    //Area 15 Visualizacion
    public function Visualizacion(){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if($user->hasPermission('Vista Visualizacion')){
        $AreaOriginal=15;
        $Area = $this->funcionesGenerales->encrypt($AreaOriginal);
        $Lineas=Linea::where('id','!=',1)->get();
        return view('Areas.Visualizacion',compact('Area','Lineas'));
        }else{
            return redirect()->route('error.');
        }
    }
    //Area 16 Montaje
    public function Montaje(){
       $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if($user->hasPermission('Vista Montaje')){
        $AreaOriginal=16;
        $Area = $this->funcionesGenerales->encrypt($AreaOriginal);
        $Lineas=Linea::where('id','!=',1)->get();
        return view('Areas.Montaje',compact('Area','Lineas'));
        }else{
            return redirect()->route('error.');
        }
    }
    //Area 17 Empaquetado
    public function Empaquetado(){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if($user->hasPermission('Vista Empaquetado')){
          $Area=$this->funcionesGenerales->encrypt(17);
          return view('Areas.Empacado',compact('Area')); 
        }else{
            return redirect()->route('error.');
        }
    }
    // Area Clasificacion
    public function Clasificacion(Request $request){
        $user= Auth::user();
        $FechaFin= date('d-m-Y');
        if (!$user) {
            return redirect()->route('login');
        }
        if($user->hasPermission('Vista Clasificación')){
        $Lineas = Linea::where('active','1')->where('id','!=',1)->orderBy('Nombre', 'asc')->get();
        return view('Areas.Clasificacion',compact('Lineas','FechaFin')); 
        }else{
            return redirect()->route('error.');
        }
    }
    public function ClasificacionRecargarTabla(Request $request){
        $user= Auth::user();
        if (!$user) {
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }
        if($user->hasPermission('Vista Clasificación')){
            $start = $request->input('start');
            $length = $request->input('length');
            $search = $request->input('search.value');
            //Cerrada = 1 es abierta
            $OrdenFabricacion = OrdenFabricacion::where('Cerrada','1')->get();
            $data = []; 
            foreach($OrdenFabricacion as $key=>$OrdenFab){
                $Partida = $OrdenFab->PartidasOF->first();
                $ContarPartidas = $Partida->Areas()->where('Areas_id',3)->get()->whereNotNull('pivot.FechaTermina')->where('pivot.TipoPartida','N')->sum('pivot.Cantidad');
                $ContarPartidasClasificacion = $Partida->Areas()->where('Areas_id',18)->get()->sum('pivot.Cantidad');
                $OrdenFab['CantidadSuministro'] = $ContarPartidas-$ContarPartidasClasificacion;
                $OrdenFab['idEncriptOF'] = $this->funcionesGenerales->encrypt($OrdenFab->id);
                $OrdenFab['EscanerDisabled'] = $Partida->Areas()->where('Areas_id','!=',$this->AreaEspecialClasificacion)->where('Areas_id','>',$this->AreaEspecialSuministro)->count();
                if($ContarPartidas == 0 || ($ContarPartidasClasificacion!=0 AND $ContarPartidasClasificacion==$ContarPartidas)){
                    unset($OrdenFabricacion[$key]);
                }elseif($ContarPartidas == 0){
                    unset($OrdenFabricacion[$key]);
                }else{
                    $data[] = [
                        'OrdenFabricacion' => $OrdenFab->OrdenFabricacion,
                        'Articulo' => $OrdenFab->Articulo,
                        'Descripcion' => $OrdenFab->Descripcion,
                        'CantidadPendiente' => ($OrdenFab->CantidadTotal - $OrdenFab->CantidadSuministro),
                        'CantidadTotal' => $OrdenFab->CantidadTotal,
                        'Escaner' => $OrdenFab->EscanerDisabled == 0 ? '<input type="checkbox" onchange="Escaner(this,\''.$OrdenFab->idEncriptOF.'\')" '.($OrdenFab->Escaner == 1 ? 'checked' : '').'>' : '<input type="checkbox" disabled>',
                        'Asignar' => '<button class="btn btn-sm btn-outline-info px-3 py-2" onclick="AsignarLinea(\''.$OrdenFab->idEncriptOF.'\')">Asignar</button>',
                        'Finalizar' => '<button class="btn btn-sm btn-outline-danger px-3 py-2" onclick="FinalizarOrdenFabricacion(\''.$OrdenFab->idEncriptOF.'\')">Finalizar</button>',
                        'Urgencia' => $OrdenFab->Urgencia
                    ];
                }
            }
            return response()->json([
                'data' => $data,
            ], 200);
        }else{
            return "";
        }
    }
    //Descomentar en caso de que no funcionen los cambios
    /*public function ClasificacionRecargarTabla(Request $request){
        $user= Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if($user->hasPermission('Vista Clasificación')){
            //Cerrada = 1 es abierta
            $OrdenFabricacion = OrdenFabricacion::where('Cerrada','1')->get()OrdenFabricacion::where('Cerrada','1')->get();
            foreach($OrdenFabricacion as $key=>$OrdenFab){
                $Partida = $OrdenFab->PartidasOF->first();
                $ContarPartidas = $Partida->Areas()->where('Areas_id',3)->get()->whereNotNull('pivot.FechaTermina')->where('pivot.TipoPartida','N')->sum('pivot.Cantidad');
                $ContarPartidasClasificacion = $Partida->Areas()->where('Areas_id',18)->get()->sum('pivot.Cantidad');
                $OrdenFab['CantidadSuministro'] = $ContarPartidas-$ContarPartidasClasificacion;
                $OrdenFab['idEncriptOF'] = $this->funcionesGenerales->encrypt($OrdenFab->id);
                $OrdenFab['EscanerDisabled'] = $Partida->Areas()->where('Areas_id','!=',$this->AreaEspecialClasificacion)->where('Areas_id','>',$this->AreaEspecialSuministro)->get()->count();
                if($ContarPartidas == 0 || ($ContarPartidasClasificacion!=0 AND $ContarPartidasClasificacion==$ContarPartidas)){
                    unset($OrdenFabricacion[$key]);
                }
            }
            $TablaBody="";
            foreach($OrdenFabricacion as $key=>$OrdenFab){
                $TablaBody.=' <tr ';
                if($OrdenFab->Urgencia=='U'){
                    $TablaBody.=' style="background:#8be0fc;"';
                }
                $TablaBody.=' id="Fila_'.$OrdenFab->OrdenFabricacion.'">
                            <td class="text-center">'.$OrdenFab->OrdenFabricacion.'</td>
                            <td>'.$OrdenFab->Articulo.'</td>
                            <td>'.$OrdenFab->Descripcion.'</td>
                            <td class="text-center">'.$OrdenFab->CantidadSuministro.'</td>
                            <td class="text-center">'.$OrdenFab->CantidadTotal.'</td>
                            <td class="text-center"><input type="checkbox" class="" ';
                            if($OrdenFab->EscanerDisabled==0){
                                $TablaBody.=' onchange="Escaner(this,\''.$OrdenFab->idEncriptOF.'\')" ';
                            }else{
                                $TablaBody.=' disabled ';
                            }
                if($OrdenFab->Escaner == 1){
                    $TablaBody.=' checked ';
                }
                $TablaBody.=' ></td>
                            <td>
                                <button class="btn btn-sm btn-outline-info px-3 py-2" onclick="AsignarLinea(\''.$OrdenFab->idEncriptOF.'\')">Asignar</button>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger px-3 py-2" onclick="FinalizarOrdenFabricacion(\''.$OrdenFab->idEncriptOF.'\')">Finalizar</button>
                            </td>
                        </tr>';
            }
            return response()->json([
                'status' => 'success',
                'table' => $TablaBody,
            ], 200);
        }else{
            return "";
        }
    }*/
    public function ClasificacionInfoModal(Request $request){
        $id=$this->funcionesGenerales->decrypt($request->id);
        $OrdenFabricacion = OrdenFabricacion::where('id','=',$id)->first();
        $Lineas = Linea::where('active','1')->where('id','!=',1)->orderBy('Nombre', 'asc')->get();
        if($OrdenFabricacion == ""){
            return response()->json([
                'status' => 'error',
            ], 200);
        }
        $PartidasOF = $OrdenFabricacion->PartidasOF->first();
        $PartidasClasificacion = $PartidasOF->Areas()->where('Areas_id',18)->get();
        //Valores disponibles
        $ContarPartidas = $PartidasOF->Areas()->where('Areas_id',3)->get()->whereNotNull('pivot.FechaTermina')->where('pivot.TipoPartida','N')->sum('pivot.Cantidad');
        $ContarPartidasClasificacion = $PartidasOF->Areas()->where('Areas_id',18)->get()->sum('pivot.Cantidad');
        $PartidasIniciadas = $PartidasOF->Areas()->where('Areas_id','>',3)->where('Areas_id','!=',18)->get();
        $Ordenfabricacionpartidas='<div class="col-12"><button class="btn btn-sm btn-outline-danger px-3 py-2 float-end"';
        $InputsBloqueo="";
        if($OrdenFabricacion->Cerrada == 0){
            $Ordenfabricacionpartidas.=' disabled';
            $InputsBloqueo.= ' disabled ';
        }else{
            $Ordenfabricacionpartidas.='  onclick="FinalizarOrdenFabricacion(\''.$request->id.'\')" ';
        }
        $Ordenfabricacionpartidas.='>Finalizar</button></div><h5 class="text-center mb-1">Orden de Fabricación <br>'.$OrdenFabricacion->OrdenFabricacion.'</h5>';
        if($OrdenFabricacion->Cerrada == 0){
                $Ordenfabricacionpartidas.='
                                        <div class="alert alert-danger d-flex align-items-center p-1 mx-0" role="alert">
                                            <span class="fas fa-times-circle text-white fs-2 me-2"></span>
                                            <p class="mb-0 flex-1 text-white">La Orden de Fabricacion se cerro de manera manual.</p>
                                        </div>'; 
        }
        if($ContarPartidas <= 0){
                $Ordenfabricacionpartidas.='
                                        <div class="alert alert-warning d-flex align-items-center p-1 mx-0" role="alert">
                                            <i class="fas fa-times-circle text-white fs-2 me-2"></i>
                                            <p class="mb-0 flex-1 text-white">Importante!  La Orden de Fabricación no ha sido asignada en estación anterior, Corte o Suministro.</p>
                                        </div>'; 
        }
        $Ordenfabricacionpartidas.='<div class="row mx-6 my-2">
                                        <div class="col-6 border bg-light rounded-left">
                                        Cantidad disponible para asignar
                                        </div>
                                        <div class="col-6 border rounded-right">
                                        '.$ContarPartidas-$ContarPartidasClasificacion.'
                                        </div>
                                    </div>
                                    <div class="row mx-6">
                                    <div class="col-6">
                                        <div class="mb-0">
                                        <label for="organizerSingle">Ingresa el numero de piezas</label>
                                        <input class="form-control form-control-sm" autocomplete="off" oninput="RegexNumeros(this);" id="CantidadModal" type="text" placeholder="0" '.$InputsBloqueo.' />
                                        </div>
                                        <small class="text-danger" id="ErrorCantidadModal"></small>
                                    </div>
                                    <div class="col-6">
                                    <label for="organizerSingle">Selecciona Línea</label>
                                    <select id="LineaModal" class="form-select form-select-sm" aria-label="" '.$InputsBloqueo.'>
                                        <option selected="" disabled>Selecciona una L&iacute;nea</option>';
                                        foreach($Lineas as $L){
                                            $Ordenfabricacionpartidas.='<option value="'.$L->id.'">'.$L->Nombre.'</option>';
                                        }
        $Ordenfabricacionpartidas.='</select><small class="text-danger" id="ErrorLineaModal"></small>
                                    <button class="btn btn-sm btn-success m-2 float-end"'.$InputsBloqueo.' onclick="GuardarAsignacion(\''.$this->funcionesGenerales->encrypt($OrdenFabricacion->id).'\',this);" type="button">Guardar</button>
                                    </div></div>';
        $Ordenfabricacionpartidas.='<table id="TablePartidasModal" class="table table-sm fs--1 mb-0" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" colspan="5">Partidas</th>
                            </tr>
                            <tr>
                                <th class="text-center" style="width:20%">Número Partida</th>
                                <th class="text-center" style="width:20%">Piezas Asignadas</th>
                                <th class="text-center" style="width:20%">Linea</th>
                                <th class="text-center" style="width:20%">Fecha de Ingreso a Línea</th>
                                <th class="text-center" style="width:20%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>';
        if($PartidasClasificacion->count()>0){
            foreach($PartidasClasificacion as $key=>$Partida){
                $Linea = Linea::find($Partida['pivot']->Linea_id);
                $Ordenfabricacionpartidas.='<tr>
                    <td class="text-center">'.($key+1).'</td>
                    <td class="text-center">'.$Partida['pivot']->Cantidad.'</td>
                    <td class="text-center"><span class="text-white p-1" style="background:'.$Linea->ColorLinea.';">'.$Linea->Nombre.'</span></td>
                    <td class="text-center">'.$Partida['pivot']->FechaComienzo.'</td>
                    <td class="text-center"><button class="btn btn-sm btn-outline-danger" ';
                if($PartidasIniciadas->count()==0){
                    $Ordenfabricacionpartidas.=' onclick="EliminarPartida(\''. $this->funcionesGenerales->encrypt($Partida['pivot']->id).'\','.($key+1).',\''.$request->id.'\')">Eliminar</button> ';
                }else{
                    $Ordenfabricacionpartidas.=' disabled >Eliminar</button>';
                } 
                    $Ordenfabricacionpartidas.='</tr>';
            }
        }else{
            $Ordenfabricacionpartidas.='<tr>
                                        <td class="text-center" colspan="5">Aún no existen partidas asignadas</td>
                                    </tr>';
        }
        $Ordenfabricacionpartidas.='</tbody></table>';
        return response()->json([
            'status' => 'success',
            'Ordenfabricacionpartidas' => $Ordenfabricacionpartidas,
            'id' => $id
        ], 200);
    }
    public function ClasificacionAsignar(Request $request){
        try {
            $id = $this->funcionesGenerales->decrypt($request->id);
            $Cantidad = $request->CantidadModal;
            $LineaId = $request->LineaModal;
            if($id=="" || $Cantidad=="" || $LineaId==""){
                return response()->json([
                    'status' => 'error',
                    'message' => 'ocurrio un error, los datos son incorrectos',
                ], 200);
            }
            $Linea=Linea::where('active',1)->find( $LineaId);
            if($Linea==""){
                return response()->json([
                    'status' => 'error',
                    'message' => 'ocurrio un error, la Línea no existe o se encuentra desactivada, si el problema persiste contacta a TI',
                ], 200);
            }
            $OrdenFabricacion = OrdenFabricacion::find($id);
            $PartidasOF = $OrdenFabricacion->PartidasOF->first();
            $ContarPartidas = $PartidasOF->Areas()->where('Areas_id',3)->get()->whereNotNull('pivot.FechaTermina')->where('pivot.TipoPartida','N')->sum('pivot.Cantidad');
            $ContarPartidasClasificacion = $PartidasOF->Areas()->where('Areas_id',18)->get()->sum('pivot.Cantidad');

            $ContarPartidas = $ContarPartidas-$ContarPartidasClasificacion;
            if($ContarPartidas<$Cantidad){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ocurrió un error, la cantidad solicitada es mayor a la cantidad disponible',
                ], 200);
            }
            $data = [
                'Cantidad' => $Cantidad,
                'TipoPartida' => 'N', // N = Normal
                'FechaComienzo' => now(),
                'Linea_id' => $LineaId,
                'Users_id' => $this->funcionesGenerales->InfoUsuario(),
            ];
            $PartidasOF->Areas()->attach($this->AreaEspecialClasificacion, $data);
            return response()->json([
                    'status' => 'success',
                    'idOF' => $this->funcionesGenerales->encrypt($id),
                ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => "Ocurrio un Error, ".$e,
            ], 500);
        }
    }
    public function ClasificacionBusqueda(Request $request){
        $OrdenFabricacion = $request->OrdenFabricacion;
        $OrdenesFabricacion = OrdenFabricacion::where('OrdenFabricacion', 'LIKE', '%'.$OrdenFabricacion.'%')->get();
        $Lista = '<ul class="list-group">';
        foreach($OrdenesFabricacion as $key=>$OF){
            $id = $this->funcionesGenerales->encrypt($OF->id);
            if($key==0){
                $Lista .= '<li class="list-group-item list-group-item-action m-0 p-0 fs-0 active" onclick="AsignarLinea(\''.$id.'\');BorrarContenedor();">'.$OF->OrdenFabricacion.'</li>';
            }else{
                $Lista .= '<li class="list-group-item list-group-item-action m-0 p-0 fs-0" onclick="AsignarLinea(\''.$id.'\');BorrarContenedor();">'.$OF->OrdenFabricacion.'</li>';
            }
        }
        return$Lista .= '</ul>';
    }
    public function FinalizarOrdenFabricacion(Request $request){
        $Estacion = isset($request->Estacion)?$request->Estacion:0;
        $id = $this->funcionesGenerales->decrypt($request->idOF);
        $Motivo = $request->Motivo;
        $OrdenFabricacion = OrdenFabricacion::find($id);
        if($OrdenFabricacion == ''){
            return 0;
        }else{
            $OrdenFabricacion->Cerrada = 0;
            $OrdenFabricacion->FechaFin = now();
            $OrdenFabricacion->save();
            $Comentarios = new Comentarios();
            $Comentarios->OrdenFabricacion_id = $id;
            $Comentarios->Partida_id = 1;
            $Comentarios->Areas_id = 18;
            $Comentarios->Usuario_id =  $this->funcionesGenerales->InfoUsuario();
            $Comentarios->Fecha =  now();
            $Comentarios->Comentario =  "Se finalizó la Orden de Fabricación  ".$OrdenFabricacion->OrdenFabricacion." de manera manual devido a: ".$Motivo;
            $Comentarios->save();
            if($Estacion == 1){
                $OrdenFabricacion->EstatusEntrega = 1;
                $OrdenFabricacion->save();
                $PartidasOF = $OrdenFabricacion->PartidasOF->first();
                $PartidasOF->EstatusPartidaOF = 1;
                $PartidasOF->EstatusPartidaOFSuministro = 1;
                $PartidasOF->save();
            }
            return 1;
        }
    }
    public function EliminarAsignacion(Request $request){
        $id = $this->funcionesGenerales->decrypt($request->idOF);
        $Partida = Partidasof_Areas::find($id);
        $Delete = $Partida->delete();
        if ($Delete) {
            return 1;
        }else{
            return 2;
        }
    }

    //Empaquetado
    public function EmpaquetadoBuscar(Request $request){
        if ($request->has('Confirmacion')) {
            $confirmacion=1;
        }else{
            $confirmacion=0;
        }
        $Codigo = $request->Codigo;
        $Inicio = $request->Inicio;
        $Finalizar = $request->Finalizar;
        $Area = $this->funcionesGenerales->decrypt($request->Area);
        $CodigoPartes = explode("-", $Codigo);
        $CodigoTam = count($CodigoPartes);
        $TipoEscanerrespuesta=0;
        $menu="";
        $Escaner="";
        $CantidadCompletada=0;
        $EscanerExiste=0;
        $Terminada=1;
        $NumeroLinea = $request->Linea;
        $NumeroLinea = Linea::where('NumeroLinea',$NumeroLinea)->first();
        if($NumeroLinea == ""){
            return response()->json([
                'tabla' => "",
                'Escaner' => "",
                'status' => "ErrorLínea",
                'CantidadTotal' => "",
                'CantidadCompletada' => 4,
                'OF' => ''
            ]);
        }
        $NumeroLinea = $NumeroLinea->id;
        //Valida si el codigo es aceptado tiene que ser mayor a 2
        if($CodigoTam<=3 && $CodigoTam>=2){
            $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->first();
            if($datos=="" OR $datos==null){
                return response()->json([
                    'tabla' => $menu,
                    'Escaner' => $Escaner,
                    'status' => "empty",
                    'CantidadTotal' => "",
                    'CantidadCompletada' => $CantidadCompletada,
                    'OF' => $CodigoPartes[0]
        
                ]);
            }else{
                $Escaner=$datos->Escaner;
                $CantidadTotal=$datos->CantidadTotal;
                //Variable  guarda el valor de Escaner para saber si es no 0=No escaner 1=escaner
                if($CodigoTam<=3 && $CodigoTam>=2){
                    //Comprobamos que la etiqueta si coincida con su numero de parte
                    if($Escaner==1){
                        $CodigoValido=$this->ComprobarNumEtiqueta($CodigoPartes,$Area);
                        if($CodigoValido==0){
                            return response()->json([
                                'tabla' => $menu,
                                'Escaner' => "",
                                'status' => "NoExiste",
                                'CantidadTotal' => "",
                                'CantidadCompletada' => 4,
                                'OF' => $CodigoPartes[0]
                
                            ]);
                        }
                        if($request->has('Accion') && $request->Accion!="Cancelar"){
                            if($Inicio==1){
                                $TipoEscanerrespuesta=$this->CompruebaAreasPosteriortodas($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada);
                                if($TipoEscanerrespuesta!=6){
                                    if($Area!=4){//Si el area es diferente de Suministro 4
                                        $TipoEscanerrespuesta=$this->CompruebaAreasAnteriortodas($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada);
                                        if($TipoEscanerrespuesta != 5){
                                            $retrabajo=$request->Retrabajo;
                                            if($Area==$this->AreaEspecialTransicion){
                                                $TipoEscanerrespuesta=$this->GuardarPartida($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada,$retrabajo,$NumeroLinea);
                                            }else{
                                                $TipoEscanerrespuesta=$this->ValidarPasoUnaVezAA($Area,$CodigoPartes);
                                                if($TipoEscanerrespuesta>0){
                                                    $TipoEscanerrespuesta=$this->GuardarPartida($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada,$retrabajo,$NumeroLinea);
                                                }else{$TipoEscanerrespuesta=5;}
                                            }
                                        }
                                    }else{
                                        $retrabajo=$request->Retrabajo;
                                        $TipoEscanerrespuesta=$this->GuardarPartida($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada,$retrabajo,$NumeroLinea);
                                    }
                                }
                            }else{
                                    $TipoEscanerrespuesta=$this->FinalizarPartida($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada);
                            }
                        }
                        $SumarActual=0;
                        $Partidastodas = $datos->PartidasOF()->get();
                        foreach($Partidastodas as $CantidadTotal){
                            $SumarActual += $CantidadTotal->Areas()->where('Areas_id',$Area)->get()->SUM('pivot.Cantidad');
                        }
                        $Terminada= $datos->CantidadTotal-$SumarActual;
                        $CantidadTotal=$datos->CantidadTotal;
                    }else if($Escaner==0){
                        $TipoManualrespuesta=$datos->partidasOF()->where('NumeroPartida','=',$CodigoPartes[1])->first();
                        if(!($TipoManualrespuesta=="" || $TipoManualrespuesta==null)){
                            $EscanerExiste = 1;
                        }else{
                            $EscanerExiste = 0;
                        }
                    }
                }
                $CantidadCompletada=$this->NumeroCompletadas($CodigoPartes,$Area);
                if($CantidadCompletada<0){
                    $CantidadCompletada=0; 
                }
                if($Escaner==1){
                    //Mostrar las partidas    
                    $partidas = $datos->partidasOF;
                    foreach ($partidas as $PartidasordenFabricacion) {
                        $PartdaArea = $PartidasordenFabricacion->Areas()->where('Areas_id', $Area)->get();
                        foreach ($PartdaArea as $PartdaAr) {
                            $menu .= '<tr>
                                        <td class=" ps-3 NumParte">' . $datos->OrdenFabricacion . '-' . $PartidasordenFabricacion->NumeroPartida . '-' . $PartdaAr['pivot']->NumeroEtiqueta . '</td>
                                        <td class="ps-3   Cantidad">' . $PartdaAr['pivot']->Cantidad . '</td>
                                       <td class="ps-3 Regresar">
                                            <button class="btn btn-primary btn-sm m-0 p-1" onclick="CancelarPartida('.$PartdaAr['pivot']->id.',\''.$datos->OrdenFabricacion . '-' . $PartidasordenFabricacion->NumeroPartida . '-' . $PartdaAr['pivot']->NumeroEtiqueta . '\')">Cancelar</button>
                                        </td>
                                    </tr>';
                            }
                        }
                }else{
                    $partidas = $datos->partidasOF;
                    foreach ($partidas as $PartidasordenFabricacion) {
                        $PartdaArea = $PartidasordenFabricacion->Areas()->where('Areas_id', $Area)->get();
                        foreach ($PartdaArea as $PartdaAr) {
                            $menu .= '<tr>
                                        <td class=" ps-3 NumParte">' . $datos->OrdenFabricacion . '-' . $PartidasordenFabricacion->NumeroPartida . '-' . $PartdaAr['pivot']->NumeroEtiqueta . '</td>
                                        <td class="ps-3   Cantidad">' . $PartdaAr['pivot']->Cantidad . '</td>
                                        <td class="ps-3 Regresar">
                                            <button class="btn btn-primary btn-sm m-0 p-1" onclick="CancelarPartida('.$PartdaAr['pivot']->id.',\''.$datos->OrdenFabricacion . '-' . $PartidasordenFabricacion->NumeroPartida . '-' . $PartdaAr['pivot']->NumeroEtiqueta . '\')">Cancelar</button>
                                        </td>
                                    </tr>';
                            }
                    }
                }
                $menu = '<div class="card-body">
                    <div id="ContainerTableSuministros" class="table-list">
                        <div class="row justify-content-start g-0">
                            <div class="col-auto px-0">
                                <h6 class="">Orden de Fabricación <strong>' . $datos->OrdenFabricacion . '</strong></h6>
                                <div class="badge badge-phoenix fs--4 badge-phoenix-secondary">
                                    <span class="fw-bold">Piezas Completadas </span>' . $CantidadCompletada . '/' . $CantidadTotal . '<span class="ms-1 fas fa-stream"></span>
                                </div>
                            </div>
                        </div>
                         <div id="ContainerTableEmpaque" class="table-responsive scrollbar">
                            <table id="TableSuministros" class="table table-striped table-sm fs--1 mb-0 overflow-hidden">
                                <thead>
                                    <tr class="bg-light">
                                        <th class="sort border-top ps-3" data-sort="NumParte">Codigo</th>
                                        <th class="sort border-top" data-sort="Cantidad">Cantidad</th>
                                        <th class="sort border-top" data-sort="Regresar">Accion</th>
                                    </tr>
                                </thead>
                                <tbody class="list" id="TablaBody">
                                    '.$menu.'
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>';
                return response()->json([
                    'tabla' => $menu,
                    'Escaner' => $Escaner,
                    'EscanerExiste' => $EscanerExiste,
                    'status' => "success",
                    'Terminada'=> $Terminada,
                    'CantidadTotal' => $CantidadTotal,
                    'Inicio' => $Inicio,
                    'Finalizar' =>$Finalizar,
                    'OrdenFabricacion'=>$datos->OrdenFabricacion,
                    'TipoEscanerrespuesta'=>$TipoEscanerrespuesta,
                    'CantidadCompletada' => $CantidadCompletada,
                    'OF' => $CodigoPartes[0]
        
                ]);
            }
        }else{
            return response()->json([
                'tabla' => $menu,
                'Escaner' => "",
                'status' => "NoExiste",
                'CantidadTotal' => "",
                'CantidadCompletada' => 4,
                'OF' => $CodigoPartes[0]

            ]);
        }
    }
    public function tablaEmpacado(){
        return$areas = DB::table('partidasof_areas')
            ->join('partidasof', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
            ->join('ordenventa', 'ordenfabricacion.OrdenVenta_id', '=', 'ordenventa.id')
            ->whereIn('partidasof_areas.Areas_id', [15, $this->AreaEspecialEmpaque]) 
            ->where('ordenfabricacion.Cerrada', '!=', 0)
            ->select(
                'ordenfabricacion.Cerrada',
                'ordenfabricacion.Urgencia',
                'ordenventa.OrdenVenta',
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal',
                'ordenfabricacion.FechaEntrega',
                'partidasof_areas.Areas_id',
                DB::raw('SUM(partidasof_areas.Cantidad) as CantidadTotalArea') 
            )
            ->groupBy(
                'ordenfabricacion.Cerrada',
                'ordenfabricacion.Urgencia',
                'ordenventa.OrdenVenta',
                'ordenfabricacion.OrdenFabricacion',
                'ordenfabricacion.CantidadTotal',
                'ordenfabricacion.FechaEntrega',
                'partidasof_areas.Areas_id'
            )
            ->get()
            ->groupBy(fn($item) => $item->OrdenVenta . '-' . $item->OrdenFabricacion)
            ->map(function ($items) {
                $area17 = $items->firstWhere('Areas_id', $this->AreaEspecialEmpaque );
                if ($area17) {
                    return $area17;
                } else {
                    $base = $items->first();
                    return (object) [
                        'OrdenVenta' => $base->OrdenVenta,
                        'Urgencia' => $base->Urgencia,
                        'OrdenFabricacion' => $base->OrdenFabricacion,
                        'CantidadTotal' => $base->CantidadTotal,
                        'FechaEntrega' => $base->FechaEntrega,
                        'CantidadTotalArea' => $base->CantidadTotalArea, 
                        'Cerrada' => $base->Cerrada
                    ];
                }
            })
            ->values();
        return response()->json($areas);
    }
    //Recargar Tabla de Area Pendiente
    public function OrdenFabricacionPendiente($Area){
        /*return$Registros = OrdenFabricacion::select('OrdenFabricacion.*','OrdenFabricacion.id AS OrdenFabricacion_id', 'partidasOF.id AS partidasOF_id', 'partidasof_Areas.id AS partidasof_Areas_id',
            'OrdenFabricacion','CantidadTotal AS OrdenFabricacionCantidad','cantidad_partida AS PartidasOFCantidad','partidasOF.NumeroPartida' )
            ->join('partidasOF', 'OrdenFabricacion.id', '=', 'partidasOF.OrdenFabricacion_id') // Relación entre OrdenFabricacion y partidasOF
            ->join('partidasof_Areas', 'partidasOF.id', '=', 'partidasof_Areas.PartidasOF_id') // Relación entre partidasOF y partidasof_Areas
            ->where('OrdenFabricacion.Cerrada', 1) // Filtra las órdenes que aún están abiertas
            ->where('partidasof_Areas.Areas_id','<=', $Area) // Filtra por el área 3 (Suministro)
            ->where('partidasof_Areas.Areas_id','!=', 2) // Que el Area sea diferente de 2 para que no tome los de Suministro
            ->whereNotNull('partidasof_Areas.FechaTermina') // Asegura que la columna FechaTermina no sea NULL
            ->get()
            ->unique('partidasOF_id')
            ->unique('OrdenFabricacion');*/
        $Registros = OrdenFabricacion::select('OrdenFabricacion.*','OrdenFabricacion.id AS OrdenFabricacion_id', 'partidasOF.id AS partidasOF_id', 'partidasof_Areas.id AS partidasof_Areas_id',
            'OrdenFabricacion','CantidadTotal AS OrdenFabricacionCantidad','cantidad_partida AS PartidasOFCantidad','partidasOF.NumeroPartida' )
            ->join('partidasOF', 'OrdenFabricacion.id', '=', 'partidasOF.OrdenFabricacion_id') // Relación entre OrdenFabricacion y partidasOF
            ->join('partidasof_Areas', 'partidasOF.id', '=', 'partidasof_Areas.PartidasOF_id') // Relación entre partidasOF y partidasof_Areas
            ->where('OrdenFabricacion.Cerrada', 1) // Filtra las órdenes que aún están abiertas
            ->where('partidasof_Areas.Areas_id','<=', $Area) // Filtra por el área 3 (Suministro)
            ->where('partidasof_Areas.Areas_id','!=', 2) // Que el Area sea diferente de 2 para que no tome los de Suministro
            ->whereNotNull('partidasof_Areas.FechaTermina') // Asegura que la columna FechaTermina no sea NULL
            ->get()
            ->unique('partidasOF_id')
            ->unique('OrdenFabricacion');
        return $Registros; 
    }
    /*public function OrdenFabricacionPendienteTabla($Area){
            $Registros = OrdenFabricacion::select('OrdenFabricacion.*','OrdenFabricacion.id AS OrdenFabricacion_id', 'partidasOF.id AS partidasOF_id', 'partidasof_Areas.id AS partidasof_Areas_id',
            'OrdenFabricacion','CantidadTotal AS OrdenFabricacionCantidad','cantidad_partida AS PartidasOFCantidad','partidasOF.NumeroPartida' )
            ->join('partidasOF', 'OrdenFabricacion.id', '=', 'partidasOF.OrdenFabricacion_id') // Relación entre OrdenFabricacion y partidasOF
            ->join('partidasof_Areas', 'partidasOF.id', '=', 'partidasof_Areas.PartidasOF_id') // Relación entre partidasOF y partidasof_Areas
            ->where('OrdenFabricacion.Cerrada', 1) // Filtra las órdenes que aún están abiertas
            ->where('partidasof_Areas.Areas_id','<=', $Area) // Filtra por el área 3 (Suministro)
            ->whereNotNull('partidasof_Areas.FechaTermina') // Asegura que la columna FechaTermina no sea NULL
            ->get()
            ->unique('partidasOF_id')
            ->unique('OrdenFabricacion');
            foreach($Registros as $key1=>$registro){
                $Area4=PartidasOF::find($registro->partidasOF_id);
                if($registro->Escaner==1){
                    //Verifica si hay Ordenes con Escaner 1 pendientes
                    $NumeroActuales=$Area4->Areas()->where('Areas_id',$Area+1)->where('TipoPartida','N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad')-
                                    $Area4->Areas()->where('Areas_id',$Area+1)->where('TipoPartida','R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                    if($NumeroActuales == $Area4->cantidad_partida){
                        unset($Registros[$key1]);
                    }
                }else{
                    //Verifica si hay Ordenes con Escaner 0 pendientes
                    $NumeroActuales =$Area4->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                            ($Area4->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                           - $Area4->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                    if ($NumeroActuales==$Area4->cantidad_partida) {
                        unset($Registros[$key1]);
                    }
                }
            }
            //
            foreach ($Registros as $key => $registro) {
                $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
                $Linea = $OrdenFabricacion->Linea()->first();
                $SumarActual = 0;
                $TotalPendiente = 0;
                $NumeroPartidasTodas = 0;
                $banderaSinRegistros=0;
                if($Area+1==4 && $registro->Escaner==0){//Aplica para el Area 4 y que sea no Escaneado
                    foreach ($OrdenFabricacion->PartidasOF as $Partidas) {
                        //$banderaSinRegistros=0;
                            //Sacamos la cantidad total de las piezas que ya pasaron
                            $SumarActual +=$Partidas->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                        ($Partidas->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                        - $Partidas->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            //Cantidad Pendiente que paso de una Area anterior
                            $TotalPendiente += $Partidas->Areas()->where('Areas_id', $Area)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                        - $Partidas->Areas()->where('Areas_id', $Area)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            if($Area4->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','=', 'F')->count()==0){
                                $banderaSinRegistros=1;
                            }
                    }
                    $registro['NumeroActuales'] = $SumarActual;
                    $registro['TotalPendiente'] = $TotalPendiente;
                    $registro['Linea'] = $Linea->NumeroLinea;
                    $registro['ColorLinea'] = $Linea->ColorLinea;
                    if ($SumarActual == $OrdenFabricacion->CantidadTotal AND $banderaSinRegistros==0) {
                        unset($Registros[$key]);
                    }
                }elseif($Area+1!=4 && $registro->Escaner==0){//Aplica para el Area diferente a 4 y que sea no Escaneado
                    foreach ($OrdenFabricacion->PartidasOF as $Partidas) {
                            //Sacamos la cantidad total de las piezas que ya pasaron
                            $SumarActual +=$Partidas->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                        ($Partidas->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                        - $Partidas->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            //Cantidad Pendiente que paso de una Area anterior
                            $TotalPendiente += $Partidas->Areas()->where('Areas_id', $Area)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                                ($Partidas->Areas()->where('Areas_id', $Area)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                                - $Partidas->Areas()->where('Areas_id', $Area)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                          
                    }
                    $registro['NumeroActuales'] = $SumarActual;
                    $registro['TotalPendiente'] = $TotalPendiente;
                    $registro['Linea'] = $Linea->NumeroLinea;
                    $registro['ColorLinea'] = $Linea->ColorLinea;
                    //return $SumarActual ."   ". $TotalPendiente;
                    if ($SumarActual == $TotalPendiente) {
                        unset($Registros[$key]);
                    }
                }else{//Aplica para todos los Escaneados
                    foreach($OrdenFabricacion->PartidasOF as $Partidas){
                            $SumarActual+=$Partidas->Areas()->where('Areas_id',$Area+1)->where('TipoPartida','N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad')-
                                            $Partidas->Areas()->where('Areas_id',$Area+1)->where('TipoPartida','R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            $TotalPendiente+=$Partidas->Areas()->where('Areas_id',$Area)->where('TipoPartida','N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad')-
                                            $Partidas->Areas()->where('Areas_id',$Area)->where('TipoPartida','R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                    }
                    $registro['NumeroActuales'] = $SumarActual;//-$Totalretrabajos;
                    $registro['TotalPendiente'] = $TotalPendiente;
                    $registro['Linea'] = $Linea->NumeroLinea;
                    $registro['ColorLinea'] = $Linea->ColorLinea;
                    if($SumarActual>=$TotalPendiente){
                        unset($Registros[$key]);
                    }
                    
                }
            }
        return $Registros; 
    }*/
    /*public function OrdenFabricacionPendienteTabla($Area){
        $Registros = DB::select("
            SELECT of.*,of.id AS OrdenFabricacion_id, pof.id AS partidasOF_id, pof.id AS partidasof_Areas_id,
            OrdenFabricacion, CantidadTotal AS OrdenFabricacionCantidad, cantidad_partida AS PartidasOFCantidad, pof.NumeroPartida,
            (SELECT poa.Areas_id FROM partidasof_areas poa WHERE poa.Areas_id < ".($Area+1)." ORDER BY poa.Areas_id DESC LIMIT 1 /*Ultima estacion*/
		   /* ) AS UltimaEstacion,  
            COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
			    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = ".($Area+1)." AND TipoPartida = \"N\" /*CantidadActualN*/
		    /*),0) AS SumarActualN ,
            COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
			WHERE poa.partidasof_id = pof.id AND poa.Areas_id = UltimaEstacion AND TipoPartida = \"N\" /*CantidadActualNoescN*/
		    /*),0) AS NESumarActualN ,
		    COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
			    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = UltimaEstacion AND TipoPartida = \"F\" /*CantidadActualNoescF*/
		    /*),0) AS NESumarActualF ,
		    COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
			    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = UltimaEstacion AND TipoPartida != \"F\" /*CantidadActualNoescDifF*/
		    /*),0) AS NESumarActualDifF,
		    COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
			    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = ".($Area+1)." AND TipoPartida = \"F\" /*CantidadActualF*/
		    /*),0) AS SumarActualF ,
		    COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
			    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = ".($Area+1)." AND TipoPartida != \"F\" /*CantidadActualDifF*/
		    /*),0) AS SumarActualDifF,
		    COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
			    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = UltimaEstacion AND TipoPartida = \"N\" /*CantidadAnteriorN*/
			/*    AND FechaTermina IS NOT NULL
		    ),0) AS SumarAnteriorN,
		    COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
			    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = UltimaEstacion AND TipoPartida = \"R\" /*CantidadAnteriorR*/
			/*    AND FechaTermina IS NULL
		    ),0) AS SumarAnteriorR,
            COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
			    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = ".($Area+1)." AND TipoPartida = \"N\" /*CantidadAnteriorN*/
			/*    AND FechaTermina IS NOT NULL
		    ),0) AS SumarPosteriorN,
		    COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
			    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = ".($Area+1)." AND TipoPartida = \"R\" /*CantidadAnteriorR*/
			/*    AND FechaTermina IS NULL
		    ),0) AS SumarPosteriorR
            FROM OrdenFabricacion of
                JOIN partidasOF pof ON of.id = pof.OrdenFabricacion_id
                JOIN (
                    SELECT  PartidasOF_id,
                    MIN(id) AS id,
                    MIN(Areas_id) AS Areas_id,
                    MIN(FechaTermina) AS FechaTermina
                    FROM partidasof_Areas
                    WHERE FechaTermina IS NOT NULL AND Areas_id <= $Area AND Areas_id != 2
                    GROUP BY PartidasOF_id
                ) poa ON poa.PartidasOF_id = pof.id
            WHERE of.Cerrada = 1
        ");
        foreach ($Registros as $key => $registro) {
            $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
            $Area4=PartidasOF::find($registro->partidasOF_id);
            $Linea = $OrdenFabricacion->Linea()->first();
            $SumarActual = 0;
            $TotalPendiente = 0;
            $NumeroPartidasTodas = 0;
            $banderaSinRegistros = 0;
            $AreaAnterior = $this->AreaAnteriorregistros($Area+1, $OrdenFabricacion->OrdenFabricacion);
            //Descomentar en caso de que falle
            /*if($Area+1==4 && $registro->Escaner==0){//Aplica para el Area 4 y que sea no Escaneado
                foreach ($OrdenFabricacion->PartidasOF as $Partidas) {
                    //$banderaSinRegistros=0;
                        //Sacamos la cantidad total de las piezas que ya pasaron
                        $SumarActual +=$Partidas->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                    ($Partidas->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                    - $Partidas->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        //Cantidad Pendiente que paso de una Area anterior
                        $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                    - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        if($Area4->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','=', 'F')->count()==0){
                            $banderaSinRegistros=1;
                        }
                }
                $registro->NumeroActuales = $SumarActual;
                $registro->TotalPendiente = $TotalPendiente;
                $registro->Linea = $Linea->NumeroLinea;
                $registro->ColorLinea = $Linea->ColorLinea;
                if($TotalPendiente==0){
                    unset($Registros[$key]);
                }
                if ($SumarActual == $TotalPendiente AND $banderaSinRegistros==0) {
                    unset($Registros[$key]);
                }
            }*//*if($Area+1==4 && $registro->Escaner==0){
                //$banderaSinRegistros=0;
                $SumarActual = ($registro->SumarActualN - $registro->SumarActualF - $registro->SumarActualDifF);
                $TotalPendiente = ($registro->SumarAnteriorN - $registro->SumarAnteriorR);
                $registro->NumeroActuales = $SumarActual;
                $registro->TotalPendiente = $TotalPendiente;
                $registro->Linea = $Linea->NumeroLinea;
                $registro->ColorLinea = $Linea->ColorLinea;
                /*if($Area4->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','=', 'F')->count()==0){
                    $banderaSinRegistros=1;
                }*/
                /*if($TotalPendiente==0){
                    unset($Registros[$key]);
                }
                if ($SumarActual == $TotalPendiente AND ($SumarActual==0 AND $TotalPendiente = 0)) {
                    unset($Registros[$key]);
                }
            }elseif($Area+1!=4 && $registro->Escaner==0){//Aplica para el Area diferente a 4 y que sea no Escaneado
                if($AreaAnterior==3){
                    $SumarActual = ($registro->SumarActualN - $registro->SumarActualF - $registro->SumarActualDifF);
                    $TotalPendiente = ($registro->SumarAnteriorN - $registro->SumarAnteriorR);
                    $registro->NumeroActuales = $SumarActual;
                    $registro->TotalPendiente = $TotalPendiente;
                    $registro->Linea = $Linea->NumeroLinea;
                    $registro->ColorLinea = $Linea->ColorLinea;
                    if ($SumarActual == $TotalPendiente AND ($SumarActual==0 AND $TotalPendiente = 0)) {
                        unset($Registros[$key]);
                    }
                }else{
                    $SumarActual = ($registro->SumarActualN - $registro->SumarActualF - $registro->SumarActualDifF);
                    $TotalPendiente = ($registro->NESumarActualN - $registro->NESumarActualF - $registro->NESumarActualDifF);
                    $registro->NumeroActuales = $SumarActual;
                    $registro->TotalPendiente = $TotalPendiente;
                    $registro->Linea = $Linea->NumeroLinea;
                    $registro->ColorLinea = $Linea->ColorLinea;
                    if ($SumarActual == $TotalPendiente AND ($SumarActual==0 AND $TotalPendiente = 0)) {
                        unset($Registros[$key]);
                    }
                }
                /*foreach ($OrdenFabricacion->PartidasOF as $Partidas) {
                        if($AreaAnterior==3){
                            $SumarActual +=$Partidas->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                            ($Partidas->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                            - $Partidas->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            //Cantidad Pendiente que paso de una Area anterior
                            $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                        - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            if($Area4->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','=', 'F')->count()==0){
                                $banderaSinRegistros=1;
                            }

                        }else{
                            //Sacamos la cantidad total de las piezas que ya pasaron
                            $SumarActual +=$Partidas->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                        ($Partidas->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                        - $Partidas->Areas()->where('Areas_id', $Area+1)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            //Cantidad Pendiente que paso de una Area anterior
                            $TotalPendiente += $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                                ($Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                                - $Partidas->Areas()->where('Areas_id', $AreaAnterior)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        }
                }*/
                /*$registro->NumeroActuales = $SumarActual;
                $registro->TotalPendiente = $TotalPendiente;
                $registro->Linea = $Linea->NumeroLinea;
                $registro->ColorLinea = $Linea->ColorLinea;*/
                //return $SumarActual ."   ". $TotalPendiente;
                /*if($TotalPendiente==0){
                    unset($Registros[$key]);
                }
                /*if ($SumarActual == $TotalPendiente) {
                    unset($Registros[$key]);
                }*/
            /*}else{//Aplica para todos los Escaneados
                foreach($OrdenFabricacion->PartidasOF as $Partidas){
                        $SumarActual+=$registro->SumarPosteriorN-$registro->SumarPosteriorR;/*$Partidas->Areas()->where('Areas_id',$Area+1)->where('TipoPartida','N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad')-
                                        $Partidas->Areas()->where('Areas_id',$Area+1)->where('TipoPartida','R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');*/
                        /*$TotalPendiente+=$registro->SumarAnteriorN-$registro->SumarAnteriorR;/*$Partidas->Areas()->where('Areas_id',$AreaAnterior)->where('TipoPartida','N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad')-
                                        $Partidas->Areas()->where('Areas_id',$AreaAnterior)->where('TipoPartida','R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');*/
                /*}
                $registro->NumeroActuales = $SumarActual;//-$Totalretrabajos;
                $registro->TotalPendiente = $TotalPendiente;
                $registro->Linea = $Linea->NumeroLinea;
                $registro->ColorLinea = $Linea->ColorLinea;
                if($SumarActual>=$TotalPendiente){
                    unset($Registros[$key]);
                }
                
            }
        }
        foreach($Registros as $key => $registro){
            $OrdenFabricacion = OrdenFabricacion::find($registro->OrdenFabricacion_id);
            $Area4=PartidasOF::find($registro->partidasOF_id);
            $PartidasOF = $OrdenFabricacion->PartidasOF()->first();
            $PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
            $AreaAnterior=$this->AreaAnteriorregistros($Area+1, $OrdenFabricacion->OrdenFabricacion);
            if($AreaAnterior <=  $this->AreaEspecialSuministro){
                $PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialClasificacion)->get();
            }else{$PartidasOFArea = $PartidasOF->Areas()->where('Areas_id',$AreaAnterior)->get();}
            if($PartidasOFArea->count() == 0){
                unset($Registros[$key]);
            }else{
                $PartidasOFAreaUnique = $PartidasOFArea->unique('pivot.Linea_id');//Sacamos las Lineas en las que tiene registros esta Area
                foreach($PartidasOFAreaUnique as $key1 => $POFA){
                    if($AreaAnterior <= $this->AreaEspecialSuministro){
                        $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id',$this->AreaEspecialClasificacion)->get()->sum('pivot.Cantidad');
                        if($OrdenFabricacion->Escaner==1){
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $Area+1)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $Area+1)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');;
                        }else{
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $Area+1)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $Area+1)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $Area+1)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        }
                        $POFA['Anterior'] = $SumarAnterior;
                        $POFA['Actual'] = $SumarActual;
                        $Linea = Linea::find($POFA['pivot']->Linea_id);
                        $POFA['Linea'] = $Linea->NumeroLinea;
                        $POFA['ColorLinea'] = $Linea->ColorLinea;
                        if($SumarAnterior<=$SumarActual){
                            unset($PartidasOFAreaUnique[$key1]);
                        }
                    }else{
                        if($OrdenFabricacion->Escaner==1){
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $Area+1)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $Area+1)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                            $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad') 
                                - $Partidas->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida', 'R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
                        }else{
                            $SumarActual = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $Area+1)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                         ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $Area+1)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                        - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $Area+1)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                            //Cantidad Pendiente que paso de una Area anterior
                            $SumarAnterior = $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                                                ($PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                                                - $PartidasOF->Areas()->where('Linea_id',$POFA['pivot']->Linea_id)->where('Areas_id', $AreaAnterior)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                        }
                        $POFA->Anterior = $SumarAnterior;
                        $POFA->Actual = $SumarActual;
                        $Linea = Linea::find($POFA['pivot']->Linea_id);
                        $POFA->Linea = $Linea->NumeroLinea;
                        $POFA->ColorLinea = $Linea->ColorLinea;
                        if($SumarAnterior<=$SumarActual){
                            unset($PartidasOFAreaUnique[$key1]);
                        }
                    }
                    if($PartidasOFAreaUnique->count()==0){
                        unset($Registros[$key]);
                    }
                }
                if($PartidasOFAreaUnique->count()>0){
                    $Registros[$key]->PartidasOFFaltantes=$PartidasOFAreaUnique;
                }
            }
        }
        $Lineas=Linea::where('id','!=',1)->get();
        return $Registros; 
    }*/
    public function OrdenFabricacionPendienteTabla($Area){
        //$Area = $Area-1
        $Registros = DB::select("
             SELECT of.*,of.id AS OrdenFabricacion_id, pof.id AS partidasOF_id, poalin.Linea_id, lin.NumeroLinea, lin.ColorLinea,
                COALESCE((SELECT poa.Areas_id FROM partidasof_areas poa WHERE poa.PartidasOF_id = pof.id  AND poa.Areas_id < ".($Area+1)." AND poa.Linea_id = poalin.Linea_id ORDER BY poa.Areas_id DESC LIMIT 1 /*Ultima estacion*/
		        ),3) AS UltimaEstacion, 
                COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
			        WHERE poa.partidasof_id = pof.id AND poa.Areas_id = ".($Area+1)." AND TipoPartida = \"N\"  AND poa.Linea_id = poalin.Linea_id/*CantidadActualN*/
		        ),0) AS SumarActualN ,
                COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
                    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = ".($Area+1)." AND TipoPartida = \"F\" AND poa.Linea_id = poalin.Linea_id/*CantidadActualF*/
                ),0) AS SumarActualF ,
                COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
                    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = ".($Area+1)." AND TipoPartida = \"R\" AND poa.Linea_id = poalin.Linea_id/*CantidadActualDifF*/
                ),0) AS SumarActualDifF,
                COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
                    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = UltimaEstacion AND TipoPartida = \"N\" AND poa.Linea_id = poalin.Linea_id/*CantidadActualNoesN*/
                ),0) AS NESumarActualN ,
                COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
                    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = UltimaEstacion AND TipoPartida = \"F\" AND poa.Linea_id = poalin.Linea_id/*CantidadActualNoescF*/
                ),0) AS NESumarActualF ,
                COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
                    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = UltimaEstacion AND TipoPartida = \"R\" AND poa.Linea_id = poalin.Linea_id/*CantidadActualNoescDifF*/
                ),0) AS NESumarActualDifF,
                COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
                            WHERE poa.partidasof_id = pof.id AND poa.Areas_id = UltimaEstacion AND TipoPartida = \"N\" AND poa.Linea_id = poalin.Linea_id/*CantidadAnteriorN*/
                            AND FechaTermina IS NOT NULL
                        ),0)AS SumarAnteriorN,
                COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
                    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = UltimaEstacion AND TipoPartida = \"R\" AND poa.Linea_id = poalin.Linea_id/*CantidadAnteriorR*/
                    AND FechaTermina IS NULL
                ),0) AS SumarAnteriorR,
                COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
                    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = ".($Area+1)." AND TipoPartida = \"N\" AND poa.Linea_id = poalin.Linea_id/*CantidadAnteriorN*/
                    AND FechaTermina IS NOT NULL
                ),0) AS SumarPosteriorN,
                COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
                    WHERE poa.partidasof_id = pof.id AND poa.Areas_id = ".($Area+1)." AND TipoPartida = \"R\" AND poa.Linea_id = poalin.Linea_id/*CantidadAnteriorR*/
                    AND FechaTermina IS NULL
                ),0) AS SumarPosteriorR,
                COALESCE((SELECT SUM(poa.Cantidad) FROM partidasof_areas poa
			        WHERE poa.partidasof_id = pof.id AND poa.Areas_id = ".$this->AreaEspecialClasificacion." AND TipoPartida = \"N\" AND poa.Linea_id = poalin.Linea_id/*CantidadAnteriorN*/
		        ),0) AS SumarAnterior3N ,
                (SELECT COUNT(*) FROM partidasof_areas WHERE PartidasOF_id = pof.id  AND Areas_id > ".($Area+1)." AND Areas_id != ".$this->AreaEspecialClasificacion." AND Linea_id = poalin.Linea_id
                ) AS RegistroAreaPosterior
            FROM OrdenFabricacion OF
            JOIN partidasOF pof ON of.id = pof.OrdenFabricacion_id
            JOIN (
                  SELECT  PartidasOF_id, Linea_id
                  FROM partidasof_Areas
                  WHERE Areas_id = ".$this->AreaEspecialClasificacion." /*Es el id de asignacion*/
                  group BY PartidasOF_id,Linea_id
            ) poalin ON poalin.PartidasOF_id = pof.id
            JOIN (
                  SELECT  PartidasOF_id,
                  MIN(id) AS id,
                  MIN(Areas_id) AS Areas_id,
                  MIN(FechaTermina) AS FechaTermina
                  FROM partidasof_Areas
                  WHERE FechaTermina IS NOT NULL AND Areas_id <= ".($Area+1)." AND Areas_id > ".$this->AreaEspecialCorte." /*5 es el Area actual y 2 es Area de corte*/
                  GROUP BY PartidasOF_id
            ) poa ON poa.PartidasOF_id = pof.id
            JOIN linea lin ON lin.id = poalin.Linea_id
            WHERE of.Cerrada = 1
            ORDER BY of.id;
        ");
        foreach($Registros as $key=>$registro){
            if($Area+1==4 && $registro->Escaner==0){
                    $SumarActual = ($registro->SumarActualF-$registro->SumarActualDifF);
                    $TotalPendiente = $registro->SumarAnterior3N;
                    $registro->NumeroActuales = $SumarActual;
                    $registro->TotalPendiente = $TotalPendiente;
                    $registro->Actual = $SumarActual;
                    $registro->Anterior= $TotalPendiente;
                    $registro->Linea = $registro->NumeroLinea;
                    $registro->ColorLinea = $registro->ColorLinea;
                    if($SumarActual>=$TotalPendiente AND !($SumarActual==0 AND $TotalPendiente==0)){
                        unset($Registros[$key]);
                    }
                    if($registro->RegistroAreaPosterior > 0 AND $SumarActual == 0){
                        unset($Registros[$key]);
                    }
            }elseif($Area+1!=4 && $registro->Escaner==0){
                    if($registro->UltimaEstacion==3){
                        $SumarActual = ($registro->SumarActualF-$registro->SumarActualDifF);
                        $TotalPendiente = $registro->SumarAnterior3N;//($registro->SumarAnteriorN - $registro->SumarAnteriorR);
                        $registro->NumeroActuales = $SumarActual;
                        $registro->TotalPendiente = $TotalPendiente;
                        $registro->Linea = $registro->NumeroLinea;
                        $registro->ColorLinea = $registro->ColorLinea;
                        if($SumarActual>=$TotalPendiente AND !($SumarActual==0 AND $TotalPendiente==0)){
                            unset($Registros[$key]);
                        }
                        if($registro->RegistroAreaPosterior > 0 AND $SumarActual == 0){
                            unset($Registros[$key]);
                        }
                    }else{
                        $SumarActual = $registro->SumarActualF-$registro->SumarActualDifF;//($registro->SumarActualN - $registro->SumarActualF - $registro->SumarActualDifF);
                        $TotalPendiente = $registro->NESumarActualF-$registro->NESumarActualDifF;//($registro->NESumarActualN - $registro->NESumarActualF - $registro->NESumarActualDifF);
                        $registro->NumeroActuales = $SumarActual;
                        $registro->TotalPendiente = $TotalPendiente;
                         $registro->Linea = $registro->NumeroLinea;
                        $registro->ColorLinea = $registro->ColorLinea;
                        if($SumarActual>=$TotalPendiente ){//AND !($SumarActual==0 AND $TotalPendiente==0)){
                            unset($Registros[$key]);
                        }
                        if($registro->RegistroAreaPosterior > 0 AND $SumarActual == 0){
                            unset($Registros[$key]);
                        }
                    }
                    $registro->Actual = $SumarActual;
                    $registro->Anterior= $TotalPendiente;
            }else{
                if($registro->UltimaEstacion == 3){
                    $SumarActual = ($registro->SumarPosteriorN - $registro->SumarPosteriorR);
                    $TotalPendiente = $registro->SumarAnterior3N;
                    $registro->NumeroActuales = $SumarActual;//-$Totalretrabajos;
                    $registro->TotalPendiente = $TotalPendiente;
                    $registro->Linea = $registro->NumeroLinea;
                    $registro->ColorLinea = $registro->ColorLinea;
                    $registro->Actual = $SumarActual;
                    $registro->Anterior= $TotalPendiente;
                }else{
                    $SumarActual = ($registro->SumarPosteriorN - $registro->SumarPosteriorR);
                    $TotalPendiente = $registro->SumarAnteriorN - $registro->SumarAnteriorR;
                    $registro->NumeroActuales = $SumarActual;//-$Totalretrabajos;
                    $registro->TotalPendiente = $TotalPendiente;
                    $registro->Linea = $registro->NumeroLinea;
                    $registro->ColorLinea = $registro->ColorLinea;
                    $registro->Actual = $SumarActual;
                    $registro->Anterior= $TotalPendiente;
                }
                if($SumarActual>=$TotalPendiente AND !($SumarActual==0 AND $TotalPendiente==0)){
                    unset($Registros[$key]);
                }
                if($SumarActual == 0 AND $registro->RegistroAreaPosterior > 0){
                    unset($Registros[$key]);
                }
            }
        }
        return $Registros;
    }
    Public function AreaTablaPendientes(Request $request){
        $Area=$this->funcionesGenerales->decrypt($request->Area);
        $OrdenFabricacionPendiente=$this->OrdenFabricacionPendienteTabla($Area-1);
        $tabla='';
        foreach($OrdenFabricacionPendiente as $partida){
            //foreach($partida->PartidasOFFaltantes as $PartidaArea){
                $tabla.='<tr';
                if($partida->Urgencia == 'U'){
                   $tabla.=' style="background:#8be0fc;"';
                }
                $tabla.='>
                            <td class="text-center">'.$partida->OrdenFabricacion.'</td>
                            <td>'.$partida->Articulo .'</td>
                            <td>'.$partida->Descripcion.'</td>
                            <td class="text-center">'.$partida->Actual.'</td>
                            <td class="text-center">'.$partida->Anterior-$partida->Actual .'</td>
                            <td class="text-center">'.$partida->Anterior  .'</td>
                            <td class="text-center">'.$partida->CantidadTotal.'</td>
                            <td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Abierta</span></div></td>
                            <td><h5 class="text-light text-center p-0" style="background: '.$partida->ColorLinea .';">'.$partida->Linea .'</h5></td>
                            </tr>';
            //}
        }
        return$tabla;
    }
    public function TipoEscaner($CodigoPartes,$CodigoTam,$Area,$confirmacion){
        // Respuestas 0=Error, 1=Guardado, 2=Ya existe, 3=Retrabajo,4=No existe, 5=Aun no pasa el proceso Anterior
        $FechaHoy=date('Y-m-d H:i:s');
        if($CodigoTam!=3 || $CodigoPartes[0]=="" || $CodigoPartes[1]=="" || $CodigoPartes[2]=="" ){
            return 0;
        }else{
            //Comprueba si el codigo pertenece a la partida
            $ComprobarNumEtiqueta=$this->ComprobarNumEtiqueta($CodigoPartes,$Area);
            if($ComprobarNumEtiqueta==0){
                    return 4;
            }
            if($ComprobarNumEtiqueta==2){
                    return 5;
            }
            //Comprueba si existe la Orden  de Fabricacion
            $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->first();
            if($datos->count()==0){
                return 0;
            }
            //return$datos->CantidadTotal." ".$CodigoPartes[2];
            if($datos->CantidadTotal<$CodigoPartes[2]){
                return 0;
            }
            //Comprueba si existe el id de la partida
            $datos= $datos->PartidasOF()->where('id',"=",$CodigoPartes[1])->first();
            if($datos==null){
                return 0;
            }
            //Comprobamos si existe la Orden de fabricacion con la partida y el numero de parte ya creado
            /*$datosPartidas=$datos->Partidas()->where('NumParte','=',$CodigoPartes[2])->orderBy('id', 'desc')->get();
                if($datosPartidas->count()==0){*/
            $datosPartidas=$datos->Partidas()->where('NumParte','=',$CodigoPartes[2])->orderBy('id', 'desc')->first();
                if($datosPartidas==null){
                    $Partidas = new Partidas();
                    $Partidas->PartidasOF_id=$datos->id;
                    $Partidas->CantidadaPartidas=1;
                    $Partidas->TipoAccion=0;
                    $Partidas->Estatus=1;
                    $Partidas->NumParte=$CodigoPartes[2];
                    $pivotData = [
                        'FechaComienzo' => $FechaHoy,
                        'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                        'Linea_id' => $datos->Linea_id,
                    ];
                    if ($Partidas->save()) {
                        $Partidas->Areas()->attach($Area,$pivotData);
                        return 1;
                    } else {
                        return 0;
                    }
                }else{
                    if(!($datosPartidas->Areas()->where('Areas_id','=',$Area)->first() =="" || $datosPartidas->Areas()->where('Areas_id','=',$Area)->first() == null)){
                        if($datosPartidas->Areas()->first()->pivot->FechaTermina==null || $datosPartidas->Areas()->first()->pivot->FechaTermina==""){
                            return 2;
                        }else{
                            if ($confirmacion==1) {
                                $Partidas = new Partidas();
                                $Partidas->PartidasOF_id=$datos->id;
                                $Partidas->CantidadaPartidas=1;
                                $Partidas->TipoAccion=1;
                                $Partidas->Estatus=2;
                                $Partidas->NumParte=$CodigoPartes[2];
                                $pivotData = [
                                    'FechaComienzo' => $FechaHoy,
                                    'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                                    'Linea_id' => $datos->Linea_id,
                                ];
                                if ($Partidas->save()) {
                                    $Partidas->Areas()->attach($Area,$pivotData);
                                    return 3;
                                } else {
                                    return 0;
                                }
                            }else{
                                return 3;
                            }
                        }
                    }else{
                        $Partidas = new Partidas();
                            $Partidas->PartidasOF_id=$datos->id;
                            $Partidas->CantidadaPartidas=1;
                            $Partidas->NumParte=$CodigoPartes[2];
                            $Partidas->Estatus=1;
                            $Partidas->TipoAccion=0;
                            $pivotData = [
                                'FechaComienzo' => $FechaHoy,
                                'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                                'Linea_id' => $datos->Linea_id,
                            ];
                            if ($Partidas->save()) {
                                $Partidas->Areas()->attach($Area,$pivotData);
                                return 3;
                            } else {
                                return 0;
                            }
                    }
                }
        }
    }
    public function TipoEscanerAreas($CodigoPartes,$CodigoTam,$Area,$confirmacion){
        // Respuestas 0=Error, 1=Guardado, 2=Ya existe, 3=Retrabajo,4=No existe, 5=Aun no pasa el proceso Anterior,6=Ya se encuentra iniciada en el Area posterior
        $FechaHoy=date('Y-m-d H:i:s');
        if($CodigoTam!=3 || $CodigoPartes[0]=="" || $CodigoPartes[1]=="" || $CodigoPartes[2]=="" ){
            return 0;
        }else{
            //Comprueba si el codigo pertenece a la partida
            $ComprobarNumEtiqueta=$this->ComprobarNumEtiqueta($CodigoPartes,$Area);
            if($ComprobarNumEtiqueta==0){
                    return 4;
            }
            if($ComprobarNumEtiqueta==2){
                    return 5;
            }
            //Comprueba si existe la Orden  de Fabricacion
            $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->first();
            if($datos->count()==0){
                return 0;
            }
            if($datos->CantidadTotal<$CodigoPartes[2]){
                return 0;
            }
            //Comprueba si existe el id de la partida
            $datos= $datos->PartidasOF()->where('id',"=",$CodigoPartes[1])->first();
            if($datos==null){
                return 0;
            }
            //Comprobamos si existe la Orden de fabricacion con la partida y el numero de parte ya creado
            $datosPartidas=$datos->Partidas()->where('NumParte','=',$CodigoPartes[2])->orderBy('id', 'desc')->first();
            $datosPartidasAreas=$datosPartidas->Areas->where('id','=',$Area);
            if($datosPartidasAreas->count()==0){
                $pivotData = [
                    'FechaComienzo' => $FechaHoy,
                    'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                    'Linea_id' => $datos->Linea_id,
                ];
                $datosPartidas->Areas()->attach($Area,$pivotData);
                return 1;
            }else{
                $bandera=0;
                foreach ($datosPartidasAreas as $key => $item) {
                    if($item->pivot->FechaTermina=="" OR $item->pivot->FechaTermina==null){
                        $bandera=1;
                    }
                }
                if($bandera==1){
                    return 2;
                }else{
                    if($confirmacion==0){
                        return 3;
                    }else{
                        $pivotData = [
                            'FechaComienzo' => $FechaHoy,
                            'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                            'Linea_id' => $datos->Linea_id,
                        ];
                        $datosPartidas->Areas()->attach($Area,$pivotData);
                        return 1;
                    }
                }
            }
        }
    }
    public function TipoEscanerAreasFinalizar($CodigoPartes,$CodigoTam,$Area){
        // Respuestas 0=Error, 1=Finalizado, 2=No se ha iniciado,3= No se encontro
        $FechaHoy=date('Y-m-d H:i:s');
        if($CodigoTam!=3 || $CodigoPartes[0]=="" || $CodigoPartes[1]=="" || $CodigoPartes[2]=="" ){
            return 0;
        }else{
            //Comprueba si el codigo pertenece a la partida
            $ComprobarNumEtiqueta=$this->ComprobarNumEtiqueta($CodigoPartes,$Area);
            if($ComprobarNumEtiqueta==0){
                return 3;
            }
            //Comprueba si existe la Orden  de Fabricacion
            $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->first();
            if($datos->count()==0){
                return 0;
            }
            //return$datos->CantidadTotal." ".$CodigoPartes[2];
            if($datos->CantidadTotal<$CodigoPartes[2]){
                return 0;
            }
            //Comprueba si existe el id de la partida
            $datos= $datos->PartidasOF()->where('id',"=",$CodigoPartes[1])->first();
            if($datos==null){
                return 0;
            }
            //Comprobamos si existe la Orden de fabricacion con la partida y el numero de parte ya creado
            $datosPartidas=$datos->Partidas()->where('NumParte','=',$CodigoPartes[2])->orderBy('id', 'desc')->first();
            if(!$datosPartidas==null){
                $datosPartidasArea = $datosPartidas->Areas()->where('areas.id','=',$Area)->get();
                $bandera=0;
                foreach ($datosPartidasArea as $item) {
                    if($item->pivot->FechaTermina==null OR $item->pivot->FechaTermina==""){
                        $item->pivot->FechaTermina = $FechaHoy;
                        $item->pivot->save();
                        $bandera=1;
                        break;
                    }
                }
                if($bandera==1){
                    return 1;
                }else{return 2;}
            }else{
                return 3;
            }
        }
    }
    public function TipoNoEscanerAreas($request){
         $request->validate([
            'Codigo' => 'required|string|max:255',
            'Cantidad' => 'required|Integer|min:1',
        ]);
        //Desencripta el Area
        $Area =$this->funcionesGenerales->decrypt($request->Area);
        $Codigo = $request->Codigo;
        $Cantidad = $request->Cantidad;
        $Retrabajo = $request->Retrabajo;
        $Estatus=1;
        $Inicio = $request->Inicio;
        $Fin = $request->Fin;
        $TipoAccion=0;
        if($Inicio==1){
            $Estatus = ($Retrabajo == "true") ? 2 : 1;
        }else{
            $Estatus=0;
        }
        $ContarPartidas=0;
        $CodigoPartes = explode("-", $Codigo);
        //Valida que el codigo este completo
        if(count($CodigoPartes)!=3){
            return response()->json([
                'Inicio'=>$Inicio,
                'Fin'=>$Fin,
                'status' => "dontexist",
                'CantidadTotal' => "",
                'CantidadCompletada' => "",
                'OF' => $CodigoPartes[0],       
            ]);
        }
        $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->first();
        //La orden de Fabricacion No existe
        if($datos==""){
            return response()->json([
                'Inicio'=>$Inicio,
                'Fin'=>$Fin,
                'status' => "empty",
                'CantidadTotal' => "",
                'CantidadCompletada' => "",
                'OF' => $CodigoPartes[0],      
            ]);
        }
        $partidasOF=$datos->partidasOF()->where('id','=',$CodigoPartes[1])->first();
        //La partida Of No existe
        if($partidasOF=="" OR $partidasOF==null){
            return response()->json([
                'Inicio'=>$Inicio,
                'Fin'=>$Fin,
                'status' => "dontexist",
                'CantidadTotal' => "",
                'CantidadCompletada' => "",
                'OF' => $CodigoPartes[0],       
            ]);
        }
        //Valida que ya se haya Completado su paso Anterior
        if($partidasOF->FechaFinalizacion=="" OR $partidasOF->FechaFinalizacion==null){
            return response()->json([
                'Inicio'=>$Inicio,
                'Fin'=>$Fin,
                'status' => "PasBackerror",
                'CantidadTotal' => "",
                'CantidadCompletada' => "",
                'OF' => $CodigoPartes[0],       
            ]);
        }
        //1 = iniciado, 0 = Finalizado, 2 = Retrabajo
        $Partidas=$partidasOF->Partidas()->where('Estatus','=','1')
            ->whereHas('Areas', function ($query) use($Area) {
                $query->where('areas.id', '=', $Area); 
            })->get();
        foreach ($Partidas as $key => $item) {
            $ContarPartidas+=$item->CantidadaPartidas;
        }
        $FechaHoy=date('Y-m-d H:i:s');
        $ContarPartidas+=$Cantidad;
        $PartidasAnterioresTer=$partidasOF->Partidas()->where('Estatus','=','0')
        ->whereHas('Areas', function ($query) use($Area) {
            $query->where('areas.id', '=', $Area-1); 
        })->get()->sum('CantidadaPartidas');
        $PartidasAnterioresretra=$partidasOF->Partidas()->where('Estatus','=','2')
        ->whereHas('Areas', function ($query) use($Area) {
            $query->where('areas.id', '=', $Area-1); 
        })->get()->sum('CantidadaPartidas');
        $anterioresFinalizadas=$PartidasAnterioresTer-$PartidasAnterioresretra;
        if($Estatus==2){
            $TipoAccion=1;
        }
        if($anterioresFinalizadas<$Cantidad){
                    return response()->json([
                        'Inicio'=>$Inicio,
                        'Fin'=>$Fin,
                        'status' => "SurplusInicioAnt",
                        'CantidadTotal' => "",
                        'CantidadCompletada' => "",
                        'OF' => $CodigoPartes[0],       
                    ]);
        }else{
                if($Estatus==2){
                    $PartidasInicio=$partidasOF->Partidas()->where('Estatus','=','1')
                    ->whereHas('Areas', function ($query) use($Area) {
                        $query->where('areas.id', '=', $Area); 
                    })->get()->sum('CantidadaPartidas');
                    $PartidasFin=$partidasOF->Partidas()->where('Estatus','=','0')
                    ->whereHas('Areas', function ($query) use($Area) {
                        $query->where('areas.id', '=', $Area); 
                    })->get()->sum('CantidadaPartidas');
                    $PartidasRetrabajo=$partidasOF->Partidas()->where('Estatus','=','2')
                    ->whereHas('Areas', function ($query) use($Area) {
                        $query->where('areas.id', '=', $Area); 
                    })->get()->sum('CantidadaPartidas');
                    if($partidasOF->cantidad_partida<$Cantidad ||($PartidasFin-$PartidasRetrabajo)<$Cantidad){
                            return response()->json([
                                'Inicio'=>$Inicio,
                                'Fin'=>$Fin,
                                'status' => "SurplusRetrabajo",
                                'OF' => $CodigoPartes[0],       
                            ]);  
                    }
                }
                if($Estatus==2 || $Fin==1){
                    $ContarPartidas=0;
                }
                if($Fin==1){
                    $PartidasInicio=$partidasOF->Partidas()->where('Estatus','=','1')
                    ->whereHas('Areas', function ($query) use($Area) {
                        $query->where('areas.id', '=', $Area); 
                    })->get()->sum('CantidadaPartidas');
                    $PartidasFin=$partidasOF->Partidas()->where('Estatus','=','0')
                    ->whereHas('Areas', function ($query) use($Area) {
                        $query->where('areas.id', '=', $Area); 
                    })->get()->sum('CantidadaPartidas');
                    $PartidasRetrabajo=$partidasOF->Partidas()->where('Estatus','=','2')
                    ->whereHas('Areas', function ($query) use($Area) {
                        $query->where('areas.id', '=', $Area); 
                    })->get()->sum('CantidadaPartidas');
                    //return $PartidasInicio." ".$PartidasFin." ".$PartidasRetrabajo;
                    if(($PartidasInicio+$PartidasRetrabajo-$PartidasFin)<$Cantidad){
                        return response()->json([
                            'Inicio'=>$Inicio,
                            'Fin'=>$Fin,
                            'status' => "SurplusFin",
                            'OF' => $CodigoPartes[0],       
                        ]);
                    }
                }
                if($ContarPartidas<=$partidasOF->cantidad_partida){
                        $Partidasg = new Partidas();
                        $Partidasg->PartidasOF_id=$partidasOF->id;
                        $Partidasg->CantidadaPartidas=$Cantidad;
                        $Partidasg->TipoAccion=$TipoAccion;
                        $Partidasg->Estatus=$Estatus;
                        $Partidasg->NumParte=0;
                        if($Inicio==1){
                            $pivotData = [
                                'FechaComienzo' => $FechaHoy,
                                'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                                'Linea_id' => $datos->Linea_id,
                            ];
                        }else{
                            $pivotData = [
                                'FechaTermina' => $FechaHoy,
                                'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                                'Linea_id' => $datos->Linea_id,
                            ];
                        }
                        if ($Partidasg->save()) {
                            $Partidasg->Areas()->attach($Area,$pivotData);
                            return response()->json([
                                'Inicio'=>$Inicio,
                                'Fin'=>$Fin,
                                'status' => "success",
                                'OF' => $CodigoPartes[0],       
                            ]);
                        } else {
                            return response()->json([
                                'Inicio'=>$Inicio,
                                'Fin'=>$Fin,
                                'status' => "error",
                                'OF' => $CodigoPartes[0],       
                            ]);
                        }
                }else{
                    return response()->json([
                        'Inicio'=>$Inicio,
                        'Fin'=>$Fin,
                        'status' => "SurplusInicio",
                        'OF' => $CodigoPartes[0],       
                    ]);
                }
        }
    }
    public function TipoEscanerFinalizar($CodigoPartes,$CodigoTam,$Area){
        // Respuestas 0=Error, 1=Finalizado, 2=No se ha iniciado,3= No se encontro
        $FechaHoy=date('Y-m-d H:i:s');
        if($CodigoTam!=3 || $CodigoPartes[0]=="" || $CodigoPartes[1]=="" || $CodigoPartes[2]=="" ){
            return 0;
        }else{
            //Comprueba si el codigo pertenece a la partida
            $ComprobarNumEtiqueta=$this->ComprobarNumEtiqueta($CodigoPartes,$Area);
            if($ComprobarNumEtiqueta==0){
                return 3;
            }
            //Comprueba si existe la Orden  de Fabricacion
            $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->first();
            if($datos->count()==0){
                return 0;
            }
            //return$datos->CantidadTotal." ".$CodigoPartes[2];
            if($datos->CantidadTotal<$CodigoPartes[2]){
                return 0;
            }
            //Comprueba si existe el id de la partida
            $datos= $datos->PartidasOF()->where('id',"=",$CodigoPartes[1])->first();
            if($datos==null){
                return 0;
            }
            //Comprobamos si existe la Orden de fabricacion con la partida y el numero de parte ya creado
            $datosPartidas=$datos->Partidas()->where('NumParte','=',$CodigoPartes[2])->orderBy('id', 'desc')->first();
            if(!$datosPartidas==null){
                if(!($datosPartidas->Areas()->first()->pivot->FechaComienzo==null || $datosPartidas->Areas()->first()->pivot->FechaComienzo=="") && ($datosPartidas->Areas()->first()->pivot->FechaTermina==null || $datosPartidas->Areas()->first()->pivot->FechaTermina=="")){
                    $area = $datosPartidas->Areas()->first();
                    if ($area) {
                        $datosPartidas->Areas()->updateExistingPivot($area->id, [
                            'FechaTermina' => $FechaHoy
                        ]);
                        return 1; // Si la actualización se realizó correctamente
                    } else {
                        return 0; // Si no se encontró el área
                    }
                }else{return 2;}
            }else{
                return 3;
            }
        }
    }
    public function AreaPartidas(Request $request){
        $codigo=$request->codigo;
        if (strpos($codigo, '-') !== false){
            $partes = explode('-', $codigo);
            $codigo=$partes[0];
            $serie=$partes[1];
            $datos=OrdenFabricacion::where('OrdenFabricacion', 'like', "%$codigo%")->get();
        }else{
            $datos=OrdenFabricacion::where('OrdenFabricacion', 'like', "%$codigo%")->get();
        }
        return $datos;

    }
    public function ComprobarAreaAnterior($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada){
        //buscamos si existe la Partida
        $TipoEscanerrespuesta=0;
        $EMPartidasOF=$datos->partidasOF->where('NumeroPartida','=',$CodigoPartes[1])->first();
         if($EMPartidasOF=="" || $EMPartidasOF==null){
            return 'error';
        }
        if($Area==$this->AreaEspecialTransicion){
            $EMPartidasOFAreas=$EMPartidasOF->Areas()->where('Areas_id',$Area-1)->get();
            return $EMPartidasOFAreas->where('pivot.TipoPartida','N')->whereNotNull('pivot.FechaTermina');//->SUM('pivot.Cantidad');
        }
        $EMPartidas=$EMPartidasOF->Partidas()->where('NumParte','=',$CodigoPartes[2])
                    ->whereHas('Areas', function ($query) use($Area) {
                    $query->where('areas.id', '=', $Area-1); 
                    })->get();
        if($EMPartidas->count()==0){
            $TipoEscanerrespuesta=5;
        }else{
            foreach ($EMPartidas as $key => $item) {
                if($item->Areas()->first()->pivot->FechaTermina=="" OR $item->Areas()->first()->pivot->FechaTermina==null){
                        $TipoEscanerrespuesta=5;
                }
            }
        }
        return $TipoEscanerrespuesta;
    }
    public function ComprobarAreaPosterior($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada){
        $TipoEscanerrespuesta=0;
        $EMPartidasOF=$datos->partidasOF->where('id','=',$CodigoPartes[1])->first();
         if($EMPartidasOF=="" || $EMPartidasOF==null){
            return 'error';
        }
        $EMPartidas=$EMPartidasOF->Partidas()->where('NumParte','=',$CodigoPartes[2])
                    ->whereHas('Areas', function ($query) use($Area) {
                    $query->where('areas.id', '=', $Area+1); 
                    })->get();
        if(!($EMPartidas->count()==0)){
            foreach ($EMPartidas as $key => $item) {
                if($item->Areas()->first()->pivot->FechaTermina=="" OR $item->Areas()->first()->pivot->FechaTermina==null){
                        $TipoEscanerrespuesta=6;
                }
            }
        }
        return $TipoEscanerrespuesta;
    }
    //Consultas a SAP
    public function finProcesoEmpaque(Request $request){   
        $idFabricacion = $request->input('id'); 
        $OrdenFabricacion = Ordenfabricacion::where('OrdenFabricacion',$idFabricacion)->first(); 
        if (!$OrdenFabricacion) {
            return response()->json([
                'message' => 'Orden de fabricación No encontrada.',
                'codigo' => 'Error', //Cuando es error
            ], 200);
        }
        $PartidasOF=$OrdenFabricacion->PartidasOF()->get();
        $Totalcompletadas=0;
        //Aplica para diferenciar a Empaque de Montaje 
        if (!$request->has('Area')) {
            foreach($PartidasOF as $Partida){
                $Totalcompletadas+=$Partida->Areas()->where('Areas_id',$this->AreaEspecialEmpaque)->get()->SUM('pivot.Cantidad');
            }
            if($OrdenFabricacion->CantidadTotal > $Totalcompletadas){
                return response()->json([
                    'message' => 'No se puede cerrar la orden porque no se ha completado',
                    'codigo' => 'Error', //Cuando es error
                ], 200);
            }
        }
        $OrdenFabricacion->Cerrada = 0; 
        $OrdenFabricacion->FechaFin = now();
        $OrdenFabricacion->save();
        return response()->json([
            'message' => 'Finalizado correctamente!',
            'codigo' => 'Success', //Cuando  si se guardo
        ], 200);
    }
    public function RegresarProceso(Request $request){
        $partidaOfAreaId = $request->input('id'); 
        $Partidasof_Areas=Partidasof_Areas::where('id',$partidaOfAreaId)->first();
        $PartidasOF=PartidasOF::where('id',$Partidasof_Areas->PartidasOF_id)->first();
        $OrdenFabricacion=$PartidasOF->OrdenFabricacion;
        $deleted = $Partidasof_Areas->delete();
        // Devolvemos una respuesta JSON según el resultado de la eliminación
        if ($deleted) {
            $OrdenFabricacion->Cerrada=1;
            $OrdenFabricacion->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Partida eliminada exitosamente.',
                'OF' => $partidaOfAreaId, // O cualquier dato adicional que necesites
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo eliminar la partida.',
            ]);
        }
    }
    //Verifica cual area anterior tiene ordenes de cada Orden de Fabricación, //Tambien cual de las superiores
    public function AreaAnteriorregistros($Area,$OrdenFabricacion){
        $OF=OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first();
        $PartidasActuales=0;
        $PartidasPosteriores=0;
        $PartidasAnteriores=0;
        $AreaRetornar=0;
        foreach($OF->PartidasOF as $Partidas){
            $parti=$Partidas->Areas()->where('Areas_id','<',$Area)->OrderBy('Areas_id','Desc')->first();
            if($parti!=""){
                if($parti->pivot->Areas_id>$AreaRetornar){
                    $AreaRetornar=$parti->pivot->Areas_id;
                }
            }
            $PartidasActuales+=$Partidas->Areas()->where('Areas_id',$Area)->get()->count();
            $PartidasPosteriores+=$Partidas->Areas()->where('Areas_id','>',$Area)->where('Areas_id','!=',$this->AreaEspecialClasificacion)->get()->count();
            //$PartidasPosteriores+=$Partidas->Areas()->where('Areas_id','>',$Area)->where('Areas_id','>',$Area)->get()->count();
        }
        if($PartidasActuales== 0 AND $PartidasPosteriores>0){
                $AreaRetornar=1;
        }
        return$AreaRetornar;
    }
    //Valida el Numero de Bloque para el Area de Pulido
    public function NumeroBloque($Lineaid){
        $NumeroBloque = Partidasof_areas::whereNotNull('NumeroBloque')->OrderBy('NumeroBloque','desc')->first();
        if($NumeroBloque==""){
            $NumeroBloque = 1;
        }else{
            $NumeroBloqueLinea = Partidasof_areas::whereNotNull('NumeroBloque')->where('Linea_id',$Lineaid)->OrderBy('NumeroBloque','desc')->get()->first();
            if($NumeroBloqueLinea != ""){
                if($NumeroBloqueLinea->CerrarBloque == null || $NumeroBloqueLinea->CerrarBloque == ""){
                    $NumeroBloque = $NumeroBloqueLinea->NumeroBloque;
                }else{
                    $NumeroBloque = $NumeroBloque->NumeroBloque+1;
                }
            }else{
                $NumeroBloque = $NumeroBloque->NumeroBloque+1;
            }
        }
        return $NumeroBloque;
    }
}
