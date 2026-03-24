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
use App\Models\OrdenFabricacionPrioridad;
use App\Models\Partidasof_Areas;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon as CarbonClass;
use DateTime;


class BusquedaController extends Controller
{
    protected $AreaMontaje;
    protected $AreaPulido;
    protected $ObjBusqueda;
    protected $AreaEspecialCorte;
    protected $AreaEspecialSuministro;
    protected $AreaEspecialEmpaque;
    protected $AreaEspecialAsignacion;
    public function __construct(){
        $this->AreaPulido=9;
        $this->AreaMontaje=16;
        $this->AreaEspecialCorte = 2;//Corte
        $this->AreaEspecialSuministro = 3;//Suministro
        $this->AreaEspecialEmpaque = 17;//Empaque
        $this->AreaEspecialAsignacion = 18;//Asignacion
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
        return redirect()->route('error');
    
    }
    //Lista dependiendo si es Orden de Fabricacion u orden de venta
    public function TipoOrden(Request $request){
        $NumeroOrden = $request->NumeroOrden;
        $TipoOrden = $request->TipoOrden;
        $Ordenes = '';
        $Lista = '';
        if($TipoOrden == 'OF'){
            $Ordenes = OrdenFabricacion::where('OrdenFabricacion', 'like', '%' . $NumeroOrden . '%')->orderBy('OrdenFabricacion', 'asc')->get();
            foreach($Ordenes as $key=>$Orden){
                $Cliente = $Orden->OrdenVenta->NombreCliente;
                if($key==0){
                    $Lista.='<a class="list-group-item list-group-item-action p-1 m-0 active" data-cantidad="'.$Orden->CantidadTotal.'" onclick="SeleccionarNumOrden('.$Orden->OrdenFabricacion.',\'OF\')">'.$Orden->OrdenFabricacion."-". $Cliente.'</a>';
                }else{
                    $Lista.='<a class="list-group-item list-group-item-action p-1 m-0 " data-cantidad="'.$Orden->CantidadTotal.'" onclick="SeleccionarNumOrden('.$Orden->OrdenFabricacion.',\'OF\')">'.$Orden->OrdenFabricacion."-". $Cliente.'</a>';
                }
            }
        }else{
            $Ordenes=OrdenVenta::where('OrdenVenta', 'like', '%' . $NumeroOrden . '%')->orderBy('OrdenVenta', 'asc')->get();
            foreach($Ordenes as $key=>$Orden){
                if($key==0){
                    $Lista.='<a class="list-group-item list-group-item-action p-1 m-0 active" onclick="SeleccionarNumOrden('.$Orden->OrdenVenta.',\'OF\')">'.$Orden->OrdenVenta."-".$Orden->NombreCliente.'</a>';
                }else{
                    $Lista.='<a class="list-group-item list-group-item-action p-1 m-0 " onclick="SeleccionarNumOrden('.$Orden->OrdenVenta.',\'OF\')">'.$Orden->OrdenVenta."-".$Orden->NombreCliente.'</a>';
                }
            }
        }
        if($Lista == ""){
            $Lista ='<a class="list-group-item list-group-item-action p-1 m-0 disabled" style="background:#CDCECF">Sin resultados</a>';
        }
        return $Lista;

    }
    //detalles OF
    public function DetallesOF(Request $request){
        $OrdenFabricacion_num = $request->input('id');
        $OrdenFabricacion = Ordenfabricacion::where('OrdenFabricacion',$OrdenFabricacion_num)->first();
        if($OrdenFabricacion == ""){
            return response()->json([
                'Areas' => "",
                'partidasAreas' => 0,
                'progreso' => 0,
                "Estatus" => "Error",
                "Message" => "La orden de Fabricación no existe!"
            ]);
        }
        //Info General
            $OrdenVenta = $OrdenFabricacion->OrdenVenta;        
            $RequiereCorte = $OrdenFabricacion->Corte;//0 es igual a si, 1 es a no
            $Estatus = ($OrdenFabricacion->Cerrada == 1)?$estatus="Abierta":"Cerrada";
            $ProgresoTotal = $this->Area_Actual($OrdenFabricacion_num,$this->AreaEspecialEmpaque);
            $CantidadTotal = $OrdenFabricacion->CantidadTotal;
            $ProgresoTotal = ($OrdenFabricacion->CantidadTotal != 0)?(100/$OrdenFabricacion->CantidadTotal)*$ProgresoTotal:0;
            $ProgresoCantidad = $this->Area_Actual($OrdenFabricacion_num,$this->AreaEspecialEmpaque);
            $Priorida = ($OrdenFabricacion->Urgencia == 'U')?'Urgente':'Normal';
            $Priorida = ($OrdenFabricacion->status == 0)?'Detenida':$Priorida;
            $OrdenFabricacion_Prioridad = OrdenFabricacionPrioridad::where('OrdenFabricacion_id',$OrdenFabricacion->id)->where('Prioridad',1)->first();
            $Priorida = ($OrdenFabricacion_Prioridad == "")?$Priorida:'Prioridad';
            $Priorida_Background = '#198754';
            $Prioridad_color = 'white';
            if($Priorida == 'Prioridad'){
                $Priorida_Background = 'rgb(255, 128, 44)';
                $Prioridad_color = 'white';
            }else if($Priorida == 'Detenida'){
                $Priorida_Background = 'rgb(252, 248, 139)';
                $Prioridad_color = 'black';
            }else if($Priorida == 'Urgente'){
                $Priorida_Background = 'rgb(139, 224, 252)';
                $Prioridad_color = 'black';
            }
        //Piezas
            $PartidasOF = $OrdenFabricacion->PartidasOF->first();
            $PiezasActual_corte = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialCorte)->where('TipoPartida','N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad')-$PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialCorte)->where('TipoPartida','R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
            $PiezasActual_suministro = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialSuministro)->where('TipoPartida','N')->whereNotNull('FechaTermina')->get()->SUM('pivot.Cantidad')-$PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialSuministro)->where('TipoPartida','R')->whereNull('FechaTermina')->get()->SUM('pivot.Cantidad');
            $PiezasActual_asignacion = $this->Area_Actual($OrdenFabricacion->OrdenFabricacion,$this->AreaEspecialAsignacion);
            $PiezasActual_empaque = $this->Area_Actual($OrdenFabricacion->OrdenFabricacion,$this->AreaEspecialEmpaque);
        //Fecha Inicio
            $FechaInicio_planeacion = $OrdenFabricacion->created_at;
            $FechaInicio_corte = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialCorte)->get();
            $FechaInicio_corte = ($FechaInicio_corte->count() >0)?$FechaInicio_corte[0]['pivot']->FechaComienzo:"";

            $FechaInicio_suministro = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialSuministro)->get();
            $FechaInicio_suministro = ($FechaInicio_suministro->count() >0)?$FechaInicio_suministro[0]['pivot']->FechaComienzo:"";

            $FechaInicio_asignacion = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialAsignacion)->get();
            $FechaInicio_asignacion = ($FechaInicio_asignacion->count() >0)?$FechaInicio_asignacion[0]['pivot']->FechaComienzo:"";
            
            $FechaInicio_empaque = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialEmpaque)->get();
            $FechaInicio_empaque = ($FechaInicio_empaque->count() >0)?$FechaInicio_empaque[0]['pivot']->FechaComienzo:"";
        //FechaFin
            $FechaFin_Corte = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialCorte)->whereNotNull('FechaTermina')->orderByPivot('FechaTermina', 'desc')->get();
            $FechaFin_Corte = ($FechaFin_Corte->count() >0)?$FechaFin_Corte[0]['pivot']->FechaTermina:"";
            $FechaFin_Corte = ($PiezasActual_corte>=$CantidadTotal)?$FechaFin_Corte:'';

            $FechaFin_suministro = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialSuministro)->whereNotNull('FechaTermina')->orderByPivot('FechaTermina', 'desc')->get();
            $FechaFin_suministro = ($FechaFin_suministro->count() >0)?$FechaFin_suministro[0]['pivot']->FechaTermina:"";
            $FechaFin_suministro = ($PiezasActual_suministro>=$CantidadTotal)?$FechaFin_suministro:'';

            $FechaFin_asignacion = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialAsignacion)->orderByPivot('FechaComienzo', 'desc')->get();
            $FechaFin_asignacion = ($FechaFin_asignacion->count() >0)?$FechaFin_asignacion[0]['pivot']->FechaComienzo:"";
            $FechaFin_asignacion = ($PiezasActual_asignacion>=$CantidadTotal)?$FechaFin_asignacion:'';

            $FechaFin_empaque = $PartidasOF->Areas()->where('Areas_id',$this->AreaEspecialEmpaque)->whereNotNull('FechaTermina')->orderByPivot('FechaTermina', 'desc')->get();
            $FechaFin_empaque = ($FechaFin_empaque->count()>0)?$FechaFin_empaque[0]['pivot']->FechaTermina:"";
            $FechaFin_empaque = ($CantidadTotal<$PiezasActual_empaque)?'':$FechaFin_empaque;
            $FechaFin_empaque = ($PiezasActual_empaque>=$CantidadTotal)?$FechaFin_empaque:'';
        //Tiempo Total por Estacion
            $TiempoTotalOrden = "";
            if($FechaFin_empaque != "" AND $FechaInicio_planeacion != ""){
                $fecha1 = new DateTime($FechaInicio_planeacion);
                $fecha2 = new DateTime($FechaFin_empaque);
                $diferencia = $fecha1->diff($fecha2);
                $TiempoTotalOrden =(($diferencia->m > 0)?$diferencia->m.' meses ':"").
                                    (($diferencia->d > 0)?$diferencia->d.' días ':"").
                                    (($diferencia->h > 0)?$diferencia->h.' horas ':"").
                                    (($diferencia->i > 0)?$diferencia->i.' minutos ':"").
                                    (($diferencia->s > 0)?$diferencia->s.' segundos ':"");
                if($TiempoTotalOrden=="")$TiempoTotalOrden='0 segundos';
            }
            $TiempoTotal_corte ="";
            if($FechaFin_Corte != "" AND $FechaInicio_corte != ""){
                $fecha1 = new DateTime($FechaInicio_corte);
                $fecha2 = new DateTime($FechaFin_Corte);
                $diferencia = $fecha1->diff($fecha2);
                $TiempoTotal_corte= (($diferencia->m > 0)?$diferencia->m.' meses ':"").
                                    (($diferencia->d > 0)?$diferencia->d.' días ':"").
                                    (($diferencia->h > 0)?$diferencia->h.' horas ':"").
                                    (($diferencia->i > 0)?$diferencia->i.' minutos ':"").
                                    (($diferencia->s > 0)?$diferencia->s.' segundos ':"");
                if($TiempoTotal_corte=="")$TiempoTotal_corte='0 segundos';
            }
            $TiempoTotal_suministro ="";
            if($FechaFin_suministro != "" AND $FechaInicio_suministro != ""){
                $fecha1 = new DateTime($FechaInicio_suministro);
                $fecha2 = new DateTime($FechaFin_suministro);
                $diferencia = $fecha1->diff($fecha2);
                $TiempoTotal_suministro = (($diferencia->m > 0)?$diferencia->m.' meses ':"").
                                    (($diferencia->d > 0)?$diferencia->d.' días ':"").
                                    (($diferencia->h > 0)?$diferencia->h.' horas ':"").
                                    (($diferencia->i > 0)?$diferencia->i.' minutos ':"").
                                    (($diferencia->s > 0)?$diferencia->s.' segundos ':"");
                if($TiempoTotal_suministro=="")$TiempoTotal_suministro='0 segundos';
            }
            $TiempoTotal_asignacion ="";
            if($FechaFin_asignacion != "" AND $FechaInicio_asignacion != ""){
                $fecha1 = new DateTime($FechaInicio_asignacion);
                $fecha2 = new DateTime($FechaFin_asignacion);
                $diferencia = $fecha1->diff($fecha2);
                $TiempoTotal_asignacion = (($diferencia->m > 0)?$diferencia->m.' meses ':"").
                                    (($diferencia->d > 0)?$diferencia->d.' días ':"").
                                    (($diferencia->h > 0)?$diferencia->h.' horas ':"").
                                    (($diferencia->i > 0)?$diferencia->i.' minutos ':"").
                                    (($diferencia->s > 0)?$diferencia->s.' segundos ':"");
                if($TiempoTotal_asignacion=="")$TiempoTotal_asignacion='0 segundos';
            }
            $TiempoTotal_empaque ="";
            if($FechaInicio_empaque != "" AND $FechaFin_empaque != ""){
                $fecha1 = new DateTime($FechaInicio_empaque);
                $fecha2 = new DateTime($FechaFin_empaque);
                $diferencia = $fecha1->diff($fecha2);
                $TiempoTotal_empaque = (($diferencia->m > 0)?$diferencia->m.' meses ':"").
                                    (($diferencia->d > 0)?$diferencia->d.' días ':"").
                                    (($diferencia->h > 0)?$diferencia->h.' horas ':"").
                                    (($diferencia->i > 0)?$diferencia->i.' minutos ':"").
                                    (($diferencia->s > 0)?$diferencia->s.' segundos ':"");
                if($TiempoTotal_empaque=="")$TiempoTotal_empaque='0 segundos';
            }
        //Usuarios por estación
        $UsuariosEstaciones = [
            'Corte' => [],
            'Suministro' => [],
            'Asignacion' => [], 
            'Empaque' => []
        ];
        $Partida_Usuarios = $PartidasOF->Areas()->get()->unique(function ($item) {
            // Esto crea una marca única: "AreaID-UsuarioID"
            // Ejemplo: "2-1" (Corte - Usuario 1)
            return $item->id . '-' . $item->pivot->Users_id;
        });
        foreach($Partida_Usuarios as $PU){
            $usuario_name = User::find($PU['pivot']->Users_id);
            $usuario_name = $usuario_name->name." ".$usuario_name->apellido;
            if($PU['pivot']->Areas_id == $this->AreaEspecialCorte){
                $UsuariosEstaciones['Corte'][] = $usuario_name;
            }elseif($PU['pivot']->Areas_id == $this->AreaEspecialSuministro){
                $UsuariosEstaciones['Suministro'][] = $usuario_name; 
            }elseif($PU['pivot']->Areas_id == $this->AreaEspecialAsignacion){
                $UsuariosEstaciones['Asignacion'][] = $usuario_name;
            }elseif($PU['pivot']->Areas_id == $this->AreaEspecialEmpaque){
                $UsuariosEstaciones['Empaque'][] = $usuario_name;
            }
        }
        $OV = ($OrdenVenta->OrdenVenta != 00000) ? $OrdenVenta->OrdenVenta: "N/A";
        $Cliente = ($OrdenVenta->OrdenVenta != 00000) ? $OrdenVenta->NombreCliente: "N/A";
        return response()->json([
            'CantidadTotal' => $CantidadTotal,
            'Estatus' => $Estatus,
            'RequiereCorte' =>$RequiereCorte,
            'OrdenFabricacion' =>$OrdenFabricacion_num,
            'OV' =>$OV,
            'Cliente' =>$Cliente,
            'ProgresoPorcentaje' => round($ProgresoTotal,2),
            'ProgresoCantidad' => $ProgresoCantidad,
            'Prioridad' => $Priorida,
            'Prioridad_color' => $Prioridad_color,
            'Prioridad_background' => $Priorida_Background,
            'PiezasActual_corte' => $PiezasActual_corte,
            'PiezasActual_suministro' => $PiezasActual_suministro,
            'PiezasActual_asignacion' => $PiezasActual_asignacion,
            'PiezasActual_empaque' => $PiezasActual_empaque,
            'FechaInicio_planeacion' => $FechaInicio_planeacion,
            'FechaInicio_corte' => $FechaInicio_corte,
            'FechaInicio_suministro' => $FechaInicio_suministro,
            'FechaInicio_asignacion' => $FechaInicio_asignacion,
            'FechaInicio_empaque' => $FechaInicio_empaque,
            'FechaFin_corte' => $FechaFin_Corte,
            'FechaFin_suministro' =>$FechaFin_suministro,
            'FechaFin_asignacion' => $FechaFin_asignacion,
            'FechaFin_empaque' => $FechaFin_empaque,
            'TiempoTotalOrden' => $TiempoTotalOrden,
            'TiempoTotal_corte' => $TiempoTotal_corte,
            'TiempoTotal_suministro' => $TiempoTotal_suministro,
            'TiempoTotal_asignacion' => $TiempoTotal_asignacion,
            'TiempoTotal_empaque' => $TiempoTotal_empaque, 
            'UsuariosEstaciones' => $UsuariosEstaciones,
            'OF_descripcion' => $OrdenFabricacion->Descripcion,
            'OF_FechaEntrega' => $OrdenFabricacion->FechaEntregaSAP,
            'OF_FechaFin' => $FechaFin_empaque,
        ]);
    }
    //Detalles OV
    public function DetallesOV(Request $request){
        $OV = $request->id;
        if($OV == "" OR $OV == '00000'){
            return response()->json([
            'Estatus' => "error",
            "message" => "La Orden de Venta no existe!"
            ]);
        }
        $OV_Datos = OrdenVenta::where('OrdenVenta',$OV)->first();
        if($OV_Datos == ""){
            return response()->json([
            'Estatus' => "error",
            "message" => "La Orden de Venta no existe!"
            ]);
        }
        //Ordenes Estatus Porcentaje de Orden de Fabricacion
        $OrdenesFabricacion = $OV_Datos->ordenesFabricacions;
        $OV_Estatus = 'Cerrada';
        $OV_Arr = array();
        foreach( $OrdenesFabricacion as $OsF){
            if($OsF->Cerrada == 1){
                $OV_Estatus = 'Abierta';
            }
            $ProgresoActual = $this->Area_Actual($OsF->OrdenFabricacion,$this->AreaEspecialEmpaque);
            $CantidadTotal = $OsF->CantidadTotal;
            $ProgresoTotal = ($OsF->CantidadTotal != 0)?(100/$OsF->CantidadTotal)*$ProgresoActual:0;
            $OV_Arr[] = [
                'OF' => $OsF->OrdenFabricacion,
                'OF_Articulo' => $OsF->Articulo,
                'OF_Cerrada' => $OsF->Cerrada,
                'OF_FechaEntregaSAP' => $OsF->FechaEntregaSAP,
                'OF' => $OsF->OrdenFabricacion,
                'OF_Descripcion' => $OsF->Descripcion,
                'OF_ProgresoTotal' => round($ProgresoTotal,2),
                'OF_CantidadActual' => $ProgresoActual,
                'OF_CantidadTotal' => $CantidadTotal,
            ];
        }
        return response()->json([
            'OV' => $OV_Datos->OrdenVenta,
            'OV_Cliente' => $OV_Datos->NombreCliente,
            'OV_Estatus' => $OV_Estatus,
            'Estatus' => "success",   
            'OV_Arr' => $OV_Arr      
        ]);
    }

    //Obtener fecha en formato dias horas minutos segundos
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
    //Obtener Tiempo en segundos
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
    //Estatus Ordenes de Fabricación
    public function EstatusOrdenesFabricacion($FechaInicio = null, $FechaFin = null,$Estatus = null){
        $user = Auth::user();
        if($user->hasPermission('Vista Estatus Orden F.')){
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
                        $PartidaOFAreas = $PartidaOF->Areas()->where('Areas_id','!=',$this->AreaEspecialAsignacion)->orderBy('Areas_id','desc')->get();
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
                        $PartidaOFAreas = $PartidaOF->Areas()->where('Areas_id','!=',$this->AreaEspecialAsignacion)->orderBy('Areas_id','desc')->get();
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
                        $PartidaOFAreas = $PartidaOF->Areas()->where('Areas_id','!=',$this->AreaEspecialAsignacion)->orderBy('Areas_id','desc')->get();
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
                        $PartidaOFAreas = $PartidaOF->Areas()->where('Areas_id','!=',$this->AreaEspecialAsignacion)->orderBy('Areas_id','desc')->get();
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
        return redirect()->route('error');
    }
    //Sacar nombre del Area
    public function AreaNombre($IdArea){
        $Area = Areas::find($IdArea); 
        return $Area->nombre;
    }
    //Funcion para traer la cantidad por Area
    public function Area_Actual($Codigo,$Area){
        $OF=OrdenFabricacion::where('OrdenFabricacion',$Codigo)->first();
        $PartidasOF=$OF->PartidasOF->first();
        $POFAreas=$PartidasOF->Areas()->where('Areas_id',$Area)->get();
        return$totalCantidad = $POFAreas->SUM('pivot.Cantidad');
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

    //Metodos anteriores
    // Controlador para las órdenes de Venta
    public function obtenerOrdenesVenta(Request $request){
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
    public function detallesventa(Request $request){

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
    public function GraficarOROF(Request $request){
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
    public function Graficador(Request $request){
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
    public function obtenerOrdenesFabricacion(Request $request){
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
    public function GraficadorFabricacion(Request $request) {
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
    }

}