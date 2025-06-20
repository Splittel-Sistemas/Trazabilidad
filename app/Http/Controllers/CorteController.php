<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PartidasOF;
use App\Models\Emision;
use App\Models\OrdenFabricacion;
use App\Models\Partidasof_Areas;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use TCPDF;
use Illuminate\Support\Facades\Auth;

class CorteController extends Controller
{
    protected $funcionesGenerales;
    public function __construct(FuncionesGeneralesController $funcionesGenerales){
        $this->funcionesGenerales = $funcionesGenerales;
    }
    public function index(){
        $user = Auth::user();
        if ($user->hasPermission('Vista Corte')) {
            $fecha = date('Y-m-d');
            $fechaAtras = date('Y-m-d', strtotime('-1 week', strtotime($fecha)));
            $OrdenesFabricacionAbiertas = $this->OrdenesFabricacionAbiertas();
            foreach($OrdenesFabricacionAbiertas as $key=>$OFA){
                $Usuario=isset($OFA->ResponsableUser_id)?User::find($OFA->ResponsableUser_id):"";
                if($Usuario==""){
                    $Nombre="";
                }else{
                    $Nombre = $Usuario->name."  ".$Usuario->apellido;
                }
                $OrdenesFabricacionAbiertas[$key]['responsable'] = $Nombre;
            }
            $OrdenesFabricacionCerradas = $this->OrdenesFabricacionCerradas($fechaAtras, $fecha);
            foreach($OrdenesFabricacionCerradas as $key=>$OFC){
                $Usuario=isset($OFC->ResponsableUser_id)?User::find($OFC->ResponsableUser_id):"";
                if($Usuario==""){
                    $Nombre="";
                }else{
                    $Nombre = $Usuario->name."  ".$Usuario->apellido;
                }
                $OrdenesFabricacionCerradas[$key]['responsable'] = $Nombre;
            }
            return view('Areas.Cortes', compact('OrdenesFabricacionAbiertas', 'OrdenesFabricacionCerradas', 'fecha', 'fechaAtras'));
        } else {
            return redirect()->route('error.');
        }
    }
    public function CorteRecargarTabla(){
        //EstatusEntrega==0 aun no iniciado; 1 igual a terminado
        try {
            //$OrdenFabricacion=OrdenFabricacion::where('EstatusEntrega','=','0')->get();
            $OrdenFabricacion=$this->OrdenesFabricacionAbiertas();
            $tabla="";
            foreach($OrdenFabricacion as $orden) {
                $Usuario=isset($orden->ResponsableUser_id)?User::find($orden->ResponsableUser_id):"";
                if($Usuario==""){
                    $Nombre="";
                }else{
                    $Nombre = $Usuario->name."  ".$Usuario->apellido;
                }
                $tabla.='<tr ';
                if($orden->Urgencia=='U'){
                    $tabla.='style="background:#8be0fc"';
                }
                $tabla.='>
                        <td>'. $orden->OrdenFabricacion .'</td>
                        <td class="text-center"><span class="badge badge-phoenix badge-phoenix-primary">'.$Nombre.'</span></td>
                        <td>'. $orden->Articulo .'</td>
                        <td>'. $orden->Descripcion .'</td>
                        <td>'. $orden->Piezascortadas.'</td>
                        <td>'. $orden->PiezascortadasR.'</td>
                        <td>'. $orden->CantidadTotal .'</td>
                        <td>'. $orden->FechaEntrega .'</td>
                        <td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Abierta</span></div></td>
                        <td><button class="btn btn-sm btn-outline-primary px-3 py-1" onclick="Planear(\''.$orden->idEncript.'\')">Cortes</button></td>
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
    public function CorteRecargarTablaCerrada(Request $request){
        $fechainicio=$request->fechainicio;
        $fechafin=$request->fechafin;
        //EstatusEntrega==0 aun no iniciado; 1 igual a terminado
        try {
            $OrdenFabricacion=$this->OrdenesFabricacionCerradas($fechainicio, $fechafin);
            $tabla="";
            foreach($OrdenFabricacion as $orden) {
                $tabla.='<tr>
                        <td>'. $orden->OrdenFabricacion .'</td>
                        <td>'. $orden->Articulo .'</td>
                        <td>'. $orden->Descripcion .'</td>
                        <td>'. $orden->Piezascortadas.'</td>
                        <td>'. $orden->PiezascortadasR.'</td>
                        <td>'. $orden->CantidadTotal .'</td>
                        <td>'. $orden->FechaComienzo .'</td>
                        <td>'. $orden->FechaFinalizacion .'</td>
                        <td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-danger"><span class="fw-bold">Cerrada</span></div></td>
                        <td><button class="btn btn-sm btn-outline-primary px-3 py-1" onclick="Detalles(\''.$orden->idEncript.'\')">Detalles</button></td>
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
    public function CortesDatosModal(Request $request){
        $id=$this->funcionesGenerales->decrypt($request->id);
        $OrdenFabricacion=OrdenFabricacion::where('id','=',$id)->first();
        $ErrorSap = 0;
        try {
            $DetallesCable=$this->funcionesGenerales->DetallesCable($OrdenFabricacion->OrdenFabricacion);
        } catch (\Throwable $e) {
            $DetallesCable=[];
            $ErrorSap = 1;
        }
        $DetallesCable=[];
        if(count($DetallesCable)==0 AND $ErrorSap == 0){
            return response()->json([
                'status' => 'successnotcable',
                'Ordenfabricacioninfo' => '<p class="text-center">La Orden de Fabricacion no contiene cable para cortar.<p>',
                'id' => $id
            ], 200);
        }
        $Hijo = isset($DetallesCable[0]['Hijo'])?$DetallesCable[0]['Hijo']:"";
        $CantidadBase = isset($DetallesCable[0]['Cantidad Base'])?$DetallesCable[0]['Cantidad Base']:"";
        $NombreHijo = isset($DetallesCable[0]['Nombre Hijo'])?$DetallesCable[0]['Nombre Hijo']:"";
        $Ordenfabricacionpartidas = '
                    <table id="TablePartidasModal" class="table table-sm fs--1 mb-0" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" colspan="8">Partidas</th>
                            </tr>
                            <tr>
                                <th class="text-center">Número Partida</th>
                                <th class="text-center">Piezas Cortadas</th>
                                <th class="text-center">Tipo Partida</th>
                                <th class="text-center">Rango de etiquetas</th>
                                <th class="text-center">Estatus</th>
                                <th class="text-center" colspan="2">Etiquetas</th>
                                <th class="text-center" colspan="2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>';
        $Ordenfabricacioninfo='<table class="table table-sm fs--1 mb-0" style="width:100%">
                        <thead >
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
            $PartidasOF=$OrdenFabricacion->partidasOF()->first();
            if($ErrorSap == 1){
                $Ordenfabricacioninfo.='
                                <div class="alert alert-danger d-flex align-items-center p-1 mx-2" role="alert">
                                    <span class="fas fa-times-circle text-danger fs-3 me-3"></span>
                                    <p class="mb-0 flex-1">Se produjo un error al conectar con SAP!, La operación continuará, pero no se mostrarán los detalles del cable.</p>
                                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
            }
            $Ordenfabricacioninfo.='<table class="table table-bordered table-sm text-xs"> 
                                <tbody>
                                    <tr>
                                        <th class="bg-light p-1"  style="width: 30%;">Artículo</th>
                                        <td class="text-center p-1"  style="width: 20%;">'.$OrdenFabricacion->Articulo.'</td>
                                        <th class="bg-light p-1"  style="width: 30%;">Fecha Planeación</th>
                                        <td class="text-center p-1"  style="width: 20%;" >'.$OrdenFabricacion->FechaEntrega.'</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light p-1"  style="width: 30%;">Cantidad Total</th>
                                        <td class="text-center p-1"  style="width: 20%;">'.$OrdenFabricacion->CantidadTotal.'</td>
                                        <th class="bg-light p-1"  style="width: 30%;">Piezas cortadas</th>  
                                        <td class="text-center p-1"  style="width: 20%;">'.$PartidasOF->Areas()->where('Areas_id',2)->get()->sum('pivot.Cantidad').'</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light p-1"  style="width: 30%;">Descripción</th>
                                        <td class="text-center p-1"  style="width: 20%;" colspan="3">'.$OrdenFabricacion->Descripcion.'</td>
                                    </tr>
                                    <tr><th class="text-center" colspan="4">Detalles del Cable</th></tr>
                                    <tr>
                                        <th class="bg-light p-1"  style="width: 30%;">Número Parte Cable</th>
                                        <td class="text-center p-1"  style="width: 20%;">'.$Hijo.'</td>
                                        <th class="bg-light p-1"  style="width: 30%;">Medida del Corte</th>
                                        <td class="text-center p-1"  style="width: 20%;">'.$CantidadBase.'</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light p-1"  style="width: 30%;">Descripción Cable</th>
                                        <td class="text-center p-1"  style="width: 20%;" colspan="3">'.$NombreHijo.'</td>
                                    </tr>
                                </tbody>
                            </table>';

            $PartidasOF=$OrdenFabricacion->partidasOF()->first();
            $PartidasOF = $PartidasOF->Areas()->where('Areas_id',2)->get();
            $RangoEtiquetas=1;
            $RangoEtiquetasR=$OrdenFabricacion->CantidadTotal+1;
            //$RangoEtiquetas=$OrdenFabricacion->CantidadTotal;
            if($PartidasOF->count()==0){
                $Ordenfabricacionpartidas.='<tr>
                                            <td class="text-center" colspan="9">Aún no existen partidas creadas</td>
                                        </tr>';
            }else{
                foreach($PartidasOF as $partida){
                    //NumeroEtiqueta especificamente en esta area se utiliza como numero de partida
                    $Ordenfabricacionpartidas.='<tr>
                        <th class="text-center">'.$partida['pivot']->NumeroEtiqueta.'</th>';
                    $Ordenfabricacionpartidas.='<td class="text-center">'.$partida['pivot']->Cantidad.'</td>';
                    if($partida['pivot']->TipoPartida=='R'){
                        $Ordenfabricacionpartidas.='<td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Retrabajo</span></div></td>';
                        if($partida['pivot']->Cantidad==1){
                            $Ordenfabricacionpartidas.='<td class="text-center">'.( $RangoEtiquetasR+$partida['pivot']->Cantidad-1).'</td>';
                        }else{
                            $Ordenfabricacionpartidas.='<td class="text-center">'. $RangoEtiquetasR."-".( $RangoEtiquetasR+$partida['pivot']->Cantidad-1).'</td>';
                        }
                        //$Ordenfabricacionpartidas.='<td class="text-center">'."1-".($partida['pivot']->Cantidad).'</td>';
                        $RangoEtiquetasR+=$partida['pivot']->Cantidad;
                    }else{
                        $Ordenfabricacionpartidas.='<td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Normal</span></div></td>';
                        if($partida['pivot']->Cantidad==1){
                            $Ordenfabricacionpartidas.='<td class="text-center">'.$RangoEtiquetas.'</td>';
                        }else{
                            $Ordenfabricacionpartidas.='<td class="text-center">'.$RangoEtiquetas."-".($RangoEtiquetas+$partida['pivot']->Cantidad-1).'</td>';
                        }
                        $RangoEtiquetas+=$partida['pivot']->Cantidad;
                    }
                    if(!($partida['pivot']->FechaTermina=='' || $partida['pivot']->FechaTermina==null)){
                        $Ordenfabricacionpartidas.='<td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-danger"><span class="fw-bold">Cerrada</span></div></td>';
                    }else{
                        $Ordenfabricacionpartidas.='<td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Abierta</span></div></td>';
                    }
                    $Ordenfabricacionpartidas.='<td ><button class="btn btn-link" onclick="etiquetaColor(\''.$this->funcionesGenerales->encrypt($partida['pivot']->id).'\',1)" type="button">40-4.5X2.5 <i class="fas fa-download"></i></button></td>
                                                <td ><button class="btn btn-link" onclick="etiquetaColor(\''.$this->funcionesGenerales->encrypt($partida['pivot']->id).'\'),3" type="button">1-11X2 <i class="fas fa-download"></i></button></td>';
                    if ($request->has('detalles')) {
                        $Ordenfabricacionpartidas.='<td class="text-center"></td><td></td>
                        </tr>';
                    }else{
                        if($partida['pivot']->FechaTermina == '' || $partida['pivot']->FechaTermina == null){
                            $Ordenfabricacionpartidas .= '<td class="text-center">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill me-1 mb-1 px-3 py-1" 
                                type="button" 
                                onclick="Cancelar(\'' . $this->funcionesGenerales->encrypt($partida['pivot']->id) . '\',\'' . $partida['pivot']->NumeroPartida . '\')">
                                Cancelar
                            </button>
                        </td>';  
                        $Ordenfabricacionpartidas .= '<td>
                        <button class="btn btn-sm btn-outline-danger rounded-pill me-1 mb-1 px-3 py-1" 
                            type="button" 
                            onclick="Finalizar(\'' . $this->funcionesGenerales->encrypt($partida['pivot']->id) . '\')">
                            Finalizar
                        </button>
                    </td>';
                        }else{
                            $Ordenfabricacionpartidas .= '<td></td><td></td>';
                        }
                        $Ordenfabricacionpartidas .= '</tr>';
                    }
                }
            }
        }else{
            $Ordenfabricacioninfo.='<tr>
                                        <td class="text-center" colspan="4">Datos no encontrados</td>
                                    </tr>';
            $Ordenfabricacionpartidas.='<tr>
                                        <td class="text-center" colspan="6">Aún no existen partidas creadas</td>
                                    </tr>';
        }
        $Ordenfabricacioninfo.='</tbody></table>';
        return response()->json([
            'status' => 'success',
            'Ordenfabricacioninfo' => $Ordenfabricacioninfo,
            'Ordenfabricacionpartidas' => $Ordenfabricacionpartidas,
            'id' => $id,
            'ErrorSAP' => $ErrorSap,
        ], 200);
    }
    public function TraerEmisiones(Request $request){
        $id=$this->funcionesGenerales->decrypt($request->id);
        $OrdenFabricacion=OrdenFabricacion::where('id','=',$id)->first();
        if($OrdenFabricacion==""){
            return response()->json([
                'status' => 'empty',
                'table' => '<option selected="" disabled>Orden Fabricacion no encontrada</option>',
            ], 500);
        }else{
            $opciones='<option selected disabled>Selecciona una Emisión de producción</option>';
            //Funcion para traer las emisiones esta en el archivo FuncionesGeneralesController
            $Emisiones=$this->funcionesGenerales->Emisiones($OrdenFabricacion->OrdenFabricacion);
                foreach ($Emisiones as $Emision){
                    $Emisiondatos=$OrdenFabricacion->Emisions()->where('NumEmision',$Emision['NoEmision'])->first();
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
    public function GuardarCorte(Request $request){
        try {
            $FechaHoy=date('Y-m-d H:i:s');
            $id=$this->funcionesGenerales->decrypt($request->id);
            $Cantitadpiezas=$request->Cantitadpiezas;
            $retrabajo=$request->retrabajo;
            $TipoPartida='N';
            $OrdenFabricacion=OrdenFabricacion::where('id','=',$id)->first();
            //El numero de la etiqueta se va usar como 
            if($OrdenFabricacion==null || $OrdenFabricacion==''){
                return response()->json([
                    'status' => 'error',
                    'message' =>'La Orden de Fabricación no existe!',
                ], 200);
            }else{
                $partidasOF=$OrdenFabricacion->partidasOF()->first();
                if($retrabajo=='Retrabajo'){
                    //$Verterminados=$OrdenFabricacion->partidasOF()->where('FechaFinalizacion','!=',"")->get()->sum('cantidad_partida');
                    $Verterminados=$partidasOF->Areas()->where('Areas_id',2)->get()->where('pivot.TipoPartida','N')->whereNotNull('pivot.FechaTermina')->sum('pivot.Cantidad');
                    $CantidadRetrabajo=$partidasOF->Areas()->where('Areas_id',2)->get()->where('pivot.TipoPartida','R')->whereNull('pivot.FechaTermina')->sum('cantidad_partida');
                    $Verterminados-=$CantidadRetrabajo;
                    if($Verterminados<$Cantitadpiezas){
                        return response()->json([
                            'status' => 'errorCantidada',
                            'message' =>'Partida no guardada, La cantidad solicitada de piezas para Retrabajo tiene que ser menor o igual al número de Partidas Finalizadas!',
                        ], 200);
                    }
                    //Detalles de emisiones
                    $ordenemision=$request->emision;
                    if($ordenemision==""){
                        return response()->json([
                            'status' => 'errorEmision',
                            'message' =>'Partida no guardada, La orden de Emision es requerida!',
                        ], 200);
                    }
                    $ComprobarOrdenEmicion=$this->funcionesGenerales->EmisioneFiltro($OrdenFabricacion->OrdenFabricacion,$ordenemision);
                    if(count($ComprobarOrdenEmicion)==0){
                        return response()->json([
                            'status' => 'errorEmision',
                            'message' =>'Partida no guardada, La orden de Emision no coincide con la Orden de Fabricacion!',
                        ], 200);
                    }
                    if($Cantitadpiezas!=$ComprobarOrdenEmicion[0]['Cantidad']){
                        return response()->json([
                            'status' => 'errorEmision',
                            'message' =>'Partida no guardada, La cantidad solicitado no coincide con la cantidad de la Emision de producción!',
                        ], 200);
                    }
                    $TipoPartida='R';
                }else{
                    $partidasOFsum=$partidasOF->Areas()->where('Areas_id',2)->get()->where('pivot.TipoPartida','N')->sum('pivot.Cantidad');
                    $partidasOFsum+=$Cantitadpiezas;
                    if($partidasOFsum>$OrdenFabricacion->CantidadTotal && $retrabajo=='Normal'){
                        return response()->json([
                            'status' => 'errorCantidada',
                            'message' =>'Partida no guardada, La cantidad total de piezas de las partidas no puede ser mayor al número Total de piezas de la Orden de Fabricación!',
                        ], 200);
                    }
                }
                $NumeroPartida=Partidasof_Areas::where('PartidasOF_id',$partidasOF->id)->where('Areas_id', 2)->orderByDesc('id','desc')->first();
                if($NumeroPartida=="" || $NumeroPartida==null){
                    $NumeroPartida = 1;
                    $partidasOF->FechaComienzo=now();
                    $partidasOF->save();
                }else{
                    $NumeroPartida = $NumeroPartida->NumeroEtiqueta+1;
                }
                $data = [
                    'Cantidad' => $Cantitadpiezas,
                    'Areas_id' => 2,
                    'TipoPartida' => $TipoPartida, // N = Normal
                    'FechaComienzo' => now(),
                    'NumeroEtiqueta' => $NumeroPartida,
                    'Linea_id' => 1,
                    'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                ];
                //Area 2 Corte
                $partidasOF->Areas()->attach(2, $data);
                if($retrabajo=='Retrabajo'){
                    //Solo se abre en Suministro cuando sea retrabajo
                    $OrdenFabricacion->EstatusEntrega=0;
                    $OrdenFabricacion->save();
                    $partidasOF->FechaFinalizacion=null;
                    $partidasOF->save();

                    $idEmision=Partidasof_Areas::where('PartidasOF_id',$partidasOF->id)->where('Areas_id', 2)->orderByDesc('id','desc')->first();
                    $emision = new Emision(); 
                    $emision->OrdenFabricacion_id = $OrdenFabricacion->id;
                    $emision->NumEmision = $ordenemision; 
                    $emision->NumEmision = $ordenemision;  
                    $emision->Etapaid = $idEmision->id;  
                    $emision->EtapaEmision = 'C'; 
                    // Asocias la emisión a la orden de fabricación
                    $emision->save();
                }
                $OrdenFabricacion->cerrada=1;
                $OrdenFabricacion->save();
                return response()->json([
                    'status' => 'success',
                    'message' =>'Partida Guardada Correctamente!',
                    'OF' =>$this->funcionesGenerales->encrypt($OrdenFabricacion->id),
                ], 200);
            }
        } catch (\Exception $e) {
            // Si ocurre algún error, retorna un mensaje de error
            return response()->json([
                'status' => 'errordatos',
                'message' => 'Ocurrió un error al guardar la partida: ' . $e->getMessage()
            ], 500); // Código de estado 500 para errores internos del servidor
        }
    }
    /*public function GuardarCorte(Request $request){
        try {
            $FechaHoy=date('Y-m-d H:i:s');
            $id=$this->funcionesGenerales->decrypt($request->id);
            $Cantitadpiezas=$request->Cantitadpiezas;
            $retrabajo=$request->retrabajo;
            return$OrdenFabricacion=OrdenFabricacion::where('id','=',$id)->first();
            //El numero de la etiqueta se va usar como 

            if($OrdenFabricacion==null || $OrdenFabricacion==''){
                return response()->json([
                    'status' => 'error',
                    'message' =>'La Orden de Fabricación no existe!',
                ], 200);
            }else{
                $partidasOFsum=$OrdenFabricacion->partidasOF()->where('TipoPartida','=','N')->get()->sum('cantidad_partida');
                $partidasOFsum+=$Cantitadpiezas;
                if($partidasOFsum>$OrdenFabricacion->CantidadTotal && $retrabajo=='Normal'){
                    return response()->json([
                        'status' => 'errorCantidada',
                        'message' =>'Partida no guardada, La cantidad total de piezas de las partidas no puede ser mayor al número Total de piezas de la Orden de Fabricación!',
                    ], 200);
                }
                $partidaOF = new PartidasOF();
                $partidaOF->OrdenFabricacion_id=$id;
                $partidaOF->cantidad_partida=$Cantitadpiezas;
                $NumeroPartida=$OrdenFabricacion->partidasOF()->orderBy('id', 'desc')->first();
                if($NumeroPartida=="" || $NumeroPartida==null){
                    $partidaOF->NumeroPartida=1;
                }else{$partidaOF->NumeroPartida=$NumeroPartida->NumeroPartida+1;}
                if($retrabajo=='Retrabajo'){
                    $partidaOF->TipoPartida='R';
                    $Verterminados=$OrdenFabricacion->partidasOF()->where('FechaFinalizacion','!=',"")->get()->sum('cantidad_partida');
                    $CantidadRetrabajo=$OrdenFabricacion->partidasOF()->where('TipoPartida','=','R')->get()->sum('cantidad_partida');
                    $Verterminados-=$CantidadRetrabajo;
                    if($Verterminados<$Cantitadpiezas){
                        return response()->json([
                            'status' => 'errorCantidada',
                            'message' =>'Partida no guardada, La cantidad solicitada de piezas para Retrabajo tiene que ser menor o igual al número de Partidas Finalizadas!',
                        ], 200);
                    }
                    $ordenemision=$request->emision;
                    if($ordenemision==""){
                        return response()->json([
                            'status' => 'errorEmision',
                            'message' =>'Partida no guardada, La orden de Emision es requerida!',
                        ], 200);
                    }
                    $ComprobarOrdenEmicion=$this->funcionesGenerales->EmisioneFiltro($OrdenFabricacion->OrdenFabricacion,$ordenemision);
                    if(count($ComprobarOrdenEmicion)==0){
                        return response()->json([
                            'status' => 'errorEmision',
                            'message' =>'Partida no guardada, La orden de Emision no coincide con la Orden de Fabricacion!',
                        ], 200);
                    }
                    if($Cantitadpiezas!=$ComprobarOrdenEmicion[0]['Cantidad']){
                        return response()->json([
                            'status' => 'errorEmision',
                            'message' =>'Partida no guardada, La cantidad solicitado no coincide con la cantidad de la Emision de producción!',
                        ], 200);
                    }
                }elseif($retrabajo=='Normal'){
                    $partidaOF->TipoPartida='N';
                }
                $partidaOF->FechaFabricacion=$FechaHoy;
                $partidaOF->FechaComienzo=$FechaHoy;
                $partidaOF->save();
                if($retrabajo=='Retrabajo'){
                    $emision = new Emision(); 
                    $emision->OrdenFabricacion_id = $OrdenFabricacion->id;
                    $emision->NumEmision = $ordenemision; 
                    $emision->NumEmision = $ordenemision;  
                    $emision->Etapaid = $partidaOF->id;  
                    $emision->EtapaEmision = 'C'; 
                    // Asocias la emisión a la orden de fabricación
                    $emision->save();
                }
                $OrdenFabricacion->EstatusEntrega=0;
                $OrdenFabricacion->save();
                return response()->json([
                    'status' => 'success',
                    'message' =>'Partida Guardada Correctamente!',
                    'OF' =>$this->funcionesGenerales->encrypt($OrdenFabricacion->id),
                ], 200);
            }
        } catch (\Exception $e) {
            // Si ocurre algún error, retorna un mensaje de error
            return response()->json([
                'status' => 'errordatos',
                'message' => 'Ocurrió un error al guardar la partida: ' . $e->getMessage()
            ], 500); // Código de estado 500 para errores internos del servidor
        }
    }*/
    public function CancelarCorte(Request $request){
        //Solo para esta Area NumeroEtiqueta se utiliza como NumeroPartida 
        $id=$this->funcionesGenerales->decrypt($request->id);
        $PartidasOFArea=Partidasof_Areas::find($id);
        if($PartidasOFArea=="" || $PartidasOFArea==null){
            return response()->json([
                'status' => 'errornoexiste',
                'message' =>'La Orden de Fabricación no existe!',
            ]);
        }else{
            $PartidaOF = PartidasOF::find($PartidasOFArea->PartidasOF_id);
            $Partidas=$PartidaOF->Areas()->where('Areas_id',3)->get();
            if($Partidas->count()==0){
                if(!($PartidasOFArea->FechaTermina == "" || $PartidasOFArea->FechaTermina == null)){
                    return response()->json([
                        'status' => 'errorfinalizada',
                        'message' =>'Partida '.$PartidasOFArea->NumeroEtiqueta.' Ocurrio un error!, No se puede cancelar la Partida, porque ya se encuentra finalizada',
                        'OF' =>$this->funcionesGenerales->encrypt($PartidaOF->ordenFabricacion()->first()->id),
                    ]);
                }
                $OrdenFabricacion=$PartidaOF->ordenFabricacion()->first();
                $OrdenEmision=$OrdenFabricacion->Emisions()->where("EtapaEmision",'C')->where("Etapaid",$PartidasOFArea->id)->first();
                $PartidasOFArea->delete();
                if($OrdenEmision!=""){
                    $OrdenEmision->delete();
                }
                $SumaPartidas = $PartidaOF->Areas()->where('Areas_id',2)->get()->where('pivot.TipoPartida','N')->whereNotNull('pivot.FechaTermina')->SUM('pivot.Cantidad');
                $PartidasIniciadas=$PartidaOF->Areas()->where('Areas_id',2)->get()->whereNull('pivot.FechaTermina');
                if($OrdenFabricacion->CantidadTotal==$SumaPartidas && $PartidasIniciadas->count()==0){
                    $OrdenFabricacion->EstatusEntrega=1;
                    $OrdenFabricacion->save();
                    $PartidaOF->FechaFinalizacion=now();
                    $PartidaOF->save();
                }
                return response()->json([
                    'status' => 'success',
                    'message' =>'Partida '.$PartidaOF->NumeroPartida.' Cancelada correctamente!',
                    'OF' =>$this->funcionesGenerales->encrypt($PartidaOF->ordenFabricacion()->first()->id),
                ]);
            }else{
                return response()->json([
                    'status' => 'erroriniciada',
                    'message' =>'Ocurrio un error!, No se puede cancelar la Partida, porque ya se encuentra iniciada en la siguiente Área',
                ]);

            }
        }
    }
    public function FinalizarCorte(Request $request){
        $id=$this->funcionesGenerales->decrypt($request->id);
        $PartidasOFArea=Partidasof_Areas::find($id);
        if($PartidasOFArea=="" || $PartidasOFArea==null){
            return response()->json([
                'status' => 'errornoexiste',
                'message' =>'La Orden de Fabricación no existe!',
            ]);
        }
        $PartidasOF = PartidasOF::find($PartidasOFArea->PartidasOF_id);
        if($PartidasOF=="" || $PartidasOF==null){
            return response()->json([
                'status' => 'errornoexiste',
                'message' =>'La Orden de Fabricación no existe!',
            ]);
        }
        if(!($PartidasOFArea->FechaTermina == "" || $PartidasOFArea->FechaTermina == null)){
            return response()->json([
                'status' => 'errorfinalizada',
                'message' =>'Partida '.$PartidasOFArea->NumeroPartida.' Ocurrio un error!, La partida ya se encuentra finalizada',
                'OF' =>$this->funcionesGenerales->encrypt($PartidasOF->ordenFabricacion()->first()->id),
            ]);
        }
        $fechaHoy=date('Y-m-d H:i:s');
        $PartidasOFArea->FechaTermina = $fechaHoy;
        $PartidasOFArea->save();
        //Sacamos las partidas que ya estan finalizadas 
        $OrdenFabricacion=$PartidasOF->ordenFabricacion()->first();
        //$SumaPartidas=$OrdenFabricacion->partidasOF()->where('TipoPartida','=','N')->where('FechaFinalizacion','!=',null)->get()->sum('cantidad_partida');
        //$PartidasIniciadas=$OrdenFabricacion->partidasOF()->where('FechaFinalizacion','=',null)->get();
        $SumaPartidas = $PartidasOF->Areas()->where('Areas_id',2)->get()->where('pivot.TipoPartida','N')->whereNotNull('pivot.FechaTermina')->SUM('pivot.Cantidad');
        $PartidasIniciadas=$PartidasOF->Areas()->where('Areas_id',2)->get()->whereNull('pivot.FechaTermina');
        if($OrdenFabricacion->CantidadTotal==$SumaPartidas && $PartidasIniciadas->count()==0){
            $OrdenFabricacion->EstatusEntrega=1;
            $OrdenFabricacion->save();
            $PartidasOF->FechaFinalizacion=now();
            $PartidasOF->save();
        }
        /*foreach($OrdenFabricacion->PartidasOF as $partida){
            $partida->EstatusPartidaOF=0;
            //$partida->EstatusPartidaOFSuministro=0;
            $partida->save();
        }*/
        return response()->json([
            'status' => 'success',
            'message' =>'Partida '.$PartidasOF->NumeroPartida.' Finalizada correctamente!',
            'OF' =>$this->funcionesGenerales->encrypt($PartidasOF->ordenFabricacion()->first()->id),
        ]);
    }
    public function BuscarCorte(Request $request){
        $OF=$request->OF;
        if(!$OF==""){
            $BuscarCorte = OrdenFabricacion::where('OrdenFabricacion', 'LIKE', '%'.$OF.'%')->get();
        }
        $opciones="";
        if($BuscarCorte->count()==0){
            $opciones.='<a class="list-group-item list-group-item-action">Orden de Fabricacion no encontrada</a>';
        }else{
            foreach($BuscarCorte as $key=>$ofcorte){
                if($key==0){
                    $opciones.='<a href="#" class="m-0 p-0 list-group-item list-group-item-action active" onclick="RetrabajoMostrarOFBuscarModal(\''.$this->funcionesGenerales->encrypt($ofcorte->id).'\')">'.$ofcorte->OrdenFabricacion.'</a>';
                }else{
                    $opciones.='<a href="#" class="m-0 p-0 list-group-item list-group-item-action" onclick="RetrabajoMostrarOFBuscarModal(\''.$this->funcionesGenerales->encrypt($ofcorte->id).'\')">'.$ofcorte->OrdenFabricacion.'</a>';
                }
            }
        }
        return $opciones;
    }
    //Consultas a SAP
    //Ordenes de Fabricacion Filtro Fecha, Abierta=0 Cerrada=1
    public function OrdenesFabricacionAbiertas(){
        $OrdenFabricacion=OrdenFabricacion::where('EstatusEntrega','=','0')->orderBy('FechaEntrega', 'asc')->get();
        foreach($OrdenFabricacion as $orden) {
            $PartidasOF=$orden->partidasOF()->first();
            $orden['idEncript'] = $this->funcionesGenerales->encrypt($orden->id);
            $orden['Piezascortadas'] = $PartidasOF->Areas()->where('Areas_id',2)->get()->where('pivot.TipoPartida','N')->sum('pivot.Cantidad');
            $orden['PiezascortadasR'] = $PartidasOF->Areas()->where('Areas_id',2)->get()->where('pivot.TipoPartida','R')->sum('pivot.Cantidad');
            $orden->id="";
        }
        return $OrdenFabricacion;
    }
    public function OrdenesFabricacionCerradas($Fechainicio, $Fechafin){
        $Fechafin = Carbon::parse($Fechafin)->addDay();
        $OrdenFabricacion=OrdenFabricacion::where('EstatusEntrega','=','1')
                                ->join('PartidasOF', 'OrdenFabricacion.id', '=', 'PartidasOF.OrdenFabricacion_id')
                                ->whereBetween('PartidasOF.FechaFinalizacion', [$Fechainicio.' 00:00:00', $Fechafin.' 00:00:00'])
                                ->select('OrdenFabricacion.*', 'PartidasOF.*')
                                ->orderBy('OrdenFabricacion.OrdenFabricacion') // Ordena por OrdenFabricacion descendente
                                ->orderBy('PartidasOF.FechaFinalizacion', 'desc') // Ordena por FechaFinalizacion descendente
                                ->get();
        $arrayOF= [];                        
        foreach($OrdenFabricacion as $key =>$orden) {
            if (!in_array($orden->OrdenFabricacion, $arrayOF)) {
                $OrdenFabricacion1=OrdenFabricacion::where('id',$orden->OrdenFabricacion_id)->first();
                $PartidasOF=$OrdenFabricacion1->partidasOF()->first();
                $FechaFinalizacion=$OrdenFabricacion1->partidasOF()->orderBy('FechaFinalizacion','desc')->first()->FechaFinalizacion;
                if($FechaFinalizacion!=$orden->FechaFinalizacion){
                    unset($OrdenFabricacion[$key]);
                }else{
                    $arrayOF[]=$orden->OrdenFabricacion;
                    $orden['FechaComienzo']=$OrdenFabricacion1->partidasOF()->orderBy('FechaComienzo')->first()->FechaComienzo;
                    $orden['idEncript'] = $this->funcionesGenerales->encrypt($orden->OrdenFabricacion_id);
                    $orden['Piezascortadas'] = $PartidasOF->Areas()->where('Areas_id',2)->get()->where('pivot.TipoPartida','N')->sum('pivot.Cantidad');
                    $orden['PiezascortadasR'] = $PartidasOF->Areas()->where('Areas_id',2)->get()->where('pivot.TipoPartida','R')->sum('pivot.Cantidad');
                    $orden->id="";
                }
            }else{
                unset($OrdenFabricacion[$key]);
            }
        }
        return $OrdenFabricacion;
    }
    public function generarPDF(Request $request){
        try {
            $partidaId = $this->funcionesGenerales->decrypt($request->input('id'));
            $Coloretiqueta = rand(1, 9);
            if (!$partidaId) {
                throw new \Exception('ID no recibido');
            }
            // Buscar la partida por ID
            $PartidasOFAreas = partidasof_Areas::find($partidaId);
            if (is_null($PartidasOFAreas)) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $PartidaOF = PartidasOF::find($PartidasOFAreas->PartidasOF_id);
            if (is_null($PartidaOF) || is_null($PartidaOF->ordenFabricacion()->first())) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $OrdenFabricacion = $PartidaOF->ordenFabricacion;
            $PartidasOFEtiq=$PartidaOF->Areas()->where('Areas_id',2)->get();
            $inicioR = $OrdenFabricacion->CantidadTotal;
            $inicio = 0;
            $finR = $OrdenFabricacion->CantidadTotal;
            $fin = 0;
            // Asignamos el número de inicio y fin de la etiqueta para la partida seleccionada
            $TipoPartida = "N";
            foreach($PartidasOFEtiq as $PartidaOFEtiq){
                $TipoPartida = $PartidaOFEtiq['pivot']->TipoPartida;
                if($TipoPartida == 'R'){
                    $finR += $PartidaOFEtiq['pivot']->Cantidad;
                }else{
                    $fin += $PartidaOFEtiq['pivot']->Cantidad;
                }
                $p=$PartidaOFEtiq['pivot']->id.'      '. $partidaId.'      \n';
                if($PartidaOFEtiq['pivot']->id == $partidaId){
                    if($TipoPartida == 'R'){
                        $inicio=$inicioR;
                        $fin = $finR;
                    }
                    break;
                }
                if($TipoPartida == 'R'){
                    $inicioR += $PartidaOFEtiq['pivot']->Cantidad;
                }else{
                    $inicio += $PartidaOFEtiq['pivot']->Cantidad;
                }
            }
            // Preparar las partidas relacionadas solo para la partida seleccionada
            $partidasData = [];
            $contador = $inicio + 1;
            $partidaId = $PartidaOF->NumeroPartida;
            for ($i = $inicio; $i < $fin; $i++) {
                $partidasData[] = [
                    'cantidad' => $contador, 
                    'descripcion' => $OrdenFabricacion->Descripcion ?? 'Sin Descripción',
                    'OrdenFabricacion' => $OrdenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
                    'Codigo'=> $OrdenFabricacion->OrdenFabricacion.'-'.$partidaId.'-'.$contador,
                ];
                $contador++; // Incrementar el contador
            }
            // Crear PDF
            $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(1, 1, 1);
            $pdf->SetFont('helvetica', 'B', 5.8);  
            $pdf->SetAutoPageBreak(TRUE, 0.5);  
            $x = 0;  //Contador para saber cuántas etiquetas se han colocado en la página x
            $y = 0; //Contador para saber cuántas etiquetas se han colocado en la página y
            foreach ($partidasData as $key=>$partida) {
                $NumeroCable=strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal;
                if ($key % 40 == 0 ) {
                    $pdf->AddPage('P', 'Letter');
                }
                if($key % 40 == 0 AND $key!=0){
                    $y=-27.2;//26.5;
                }
                // Calcular la posición X para cada etiqueta
                $posX = ($x % 4) * 53;//47;
                if ($key % 4 == 0 AND $key != 0) {
                    $y+=27.2;//26.5;
                }
                $pdf->SetFillColor(0, 0, 0); 
                $pdf->Rect($posX, $y+4, 80, 0.2, 'F');  // Color de fondo en la parte superior de la etiqueta
                $pdf->SetTextColor(0, 0, 0);
                // Definir el contenido del texto para cada etiqueta
                if ($TipoPartida == 'N') {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . "\n" .
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" .
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                } else {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . " R \n" . 
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" . 
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                }
                // Añadir el contenido de texto
                $pdf->SetXY($posX + 4, $y+5);  // Colocamos el texto un poco desplazado desde el borde
                $pdf->MultiCell(45, 26, $content, 0, 'L', 0, 1);  // Ajustamos la celda para que se ajuste al ancho de la etiqueta
                // Generar y colocar el código de barras
                $CodigoBarras = strip_tags($partida['Codigo']);
                $pdf->SetXY($posX + 10, $y+20);  // Ajuste de la posición del código de barras
                $pdf->write1DBarcode($CodigoBarras, 'C128', '', '', 20, 5, 0.4, array(), 'N');
                $pdf->SetXY($posX + 10, $y+26);  // Ajustar la posición para el texto debajo del código de barras
                $pdf->Cell(17.5, 1, $CodigoBarras, 0, 1, 'C'); // Código de barras centrado
                $pdf->SetXY($posX + 10, $y+28); 
                $pdf->Cell(17.5, 1, $NumeroCable, 0, 1, 'C'); 
                $x++;  // Incrementar el contador de etiquetas colocadas
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return $pdf->Output('orden_fabricacion_'.$OrdenFabricacion->OrdenFabricacion.'_' . $partidaId . '.pdf', 'I'); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    //Etiqueta de 3 
    /*public function generarPDF(Request $request){
        try {
            $partidaId = $this->funcionesGenerales->decrypt($request->input('id'));
            $Coloretiqueta = rand(1, 9);
            if (!$partidaId) {
                throw new \Exception('ID no recibido');
            }
            $colores = [
                1 => ['nombre' => 'Azul Claro', 'rgb' => [14, 14, 231]],
                2 => ['nombre' => 'Rojo Claro', 'rgb' => [241, 48, 48]],
                3 => ['nombre' => 'Rosa Mexicano Claro', 'rgb' => [236, 42, 104]],
                4 => ['nombre' => 'Café Claro', 'rgb' => [150, 115, 90]],
                5 => ['nombre' => 'Verde Bandera Claro', 'rgb' => [5, 112, 62]],
                6 => ['nombre' => 'Amarillo Claro', 'rgb' => [252, 252, 44]],
                7 => ['nombre' => 'Verde Limón Claro', 'rgb' => [230, 252, 122]],
                8 => ['nombre' => 'Rosa Claro', 'rgb' => [255, 210, 220]],
                9 => ['nombre' => 'Naranja Claro', 'rgb' => [255, 168, 53]]
            ];
            $R= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][0]:255;
            $G= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][1]:255;
            $B= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][2]:255;
    
            // Buscar la partida por ID
            $PartidasOFAreas = partidasof_Areas::find($partidaId);
            if (is_null($PartidasOFAreas)) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $PartidaOF = PartidasOF::find($PartidasOFAreas->PartidasOF_id);
            if (is_null($PartidaOF) || is_null($PartidaOF->ordenFabricacion()->first())) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $OrdenFabricacion = $PartidaOF->ordenFabricacion;
            //$PartidasOFEtiq = $OrdenFabricacion->partidasOF()->get();
            $PartidasOFEtiq=$PartidaOF->Areas()->where('Areas_id',2)->get();
            $inicioR = $OrdenFabricacion->CantidadTotal;
            $inicio = 0;
            $finR = $OrdenFabricacion->CantidadTotal;
            $fin = 0;
            // Asignamos el número de inicio y fin de la etiqueta para la partida seleccionada
            $TipoPartida = "N";
            foreach($PartidasOFEtiq as $PartidaOFEtiq){
                $TipoPartida = $PartidaOFEtiq['pivot']->TipoPartida;
                if($TipoPartida == 'R'){
                    $finR += $PartidaOFEtiq['pivot']->Cantidad;
                }else{
                    $fin += $PartidaOFEtiq['pivot']->Cantidad;
                }
                $p=$PartidaOFEtiq['pivot']->id.'      '. $partidaId.'      \n';
                if($PartidaOFEtiq['pivot']->id == $partidaId){
                    if($TipoPartida == 'R'){
                        $inicio=$inicioR;
                        $fin = $finR;
                    }
                    break;
                }
                if($TipoPartida == 'R'){
                    $inicioR += $PartidaOFEtiq['pivot']->Cantidad;
                }else{
                    $inicio += $PartidaOFEtiq['pivot']->Cantidad;
                }
            }
            // Preparar las partidas relacionadas solo para la partida seleccionada
            $partidasData = [];
            $contador = $inicio + 1;
            $partidaId = $PartidaOF->NumeroPartida;
            for ($i = $inicio; $i < $fin; $i++) {
                $partidasData[] = [
                    'cantidad' => $contador, 
                    'descripcion' => $OrdenFabricacion->Descripcion ?? 'Sin Descripción',
                    'OrdenFabricacion' => $OrdenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
                    'Codigo'=> $OrdenFabricacion->OrdenFabricacion.'-'.$partidaId.'-'.$contador,
                ];
                $contador++; // Incrementar el contador
            }
    
            // Crear PDF
            $pdf = new TCPDF();
            
            // Ajustar márgenes
            $pdf->SetMargins(1, 3, 1); 
            $pdf->SetFont('helvetica', 'B', 3.8);  
            $pdf->SetAutoPageBreak(TRUE, 0.5);  
    
            $counter = 0;  // Contador para saber cuántas etiquetas se han colocado en la página
            foreach ($partidasData as $key=>$partida) {
                $NumeroCable=strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal;
                // Si ya hemos colocado tres etiquetas en la fila, añadimos una nueva página
                if ($key % 3 == 0 || $key == 0) {
                    $pdf->AddPage('L', array(112, 25));  // Nueva página
                }
                
                // Calcular la posición X para cada etiqueta
                $posX = ($counter % 3) * 37.33; // Esto distribuye las etiquetas horizontalmente (3 por fila)
                
                // Añadir el color de fondo en la parte superior de la etiqueta
                $pdf->SetFillColor($R, $G, $B); 
                $pdf->Rect($posX, 0, 37.33, 3, 'F');  // Color de fondo en la parte superior de la etiqueta
                $pdf->SetTextColor(0, 0, 0);  // Color de texto en negro
    
                // Definir el contenido del texto para cada etiqueta
                if ($TipoPartida == 'N') {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . "\n" .
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" .
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                } else {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . " R \n" . 
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" . 
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                }
                // Añadir el contenido de texto
                $pdf->SetXY($posX + 4, 3);  // Colocamos el texto un poco desplazado desde el borde
                $pdf->MultiCell(28, 0, $content, 0, 'L', 0, 1);  // Ajustamos la celda para que se ajuste al ancho de la etiqueta
                // Generar y colocar el código de barras
                $CodigoBarras = strip_tags($partida['Codigo']);
                $pdf->SetXY($posX + 7, $pdf->GetY() + 2);  // Ajuste de la posición del código de barras
                $pdf->write1DBarcode($CodigoBarras, 'C128', '', '', 20, 5, 0.4, array(), 'N');
                $pdf->SetXY($posX + 7, $pdf->GetY() + 1);  // Ajustar la posición para el texto debajo del código de barras
                $pdf->Cell(17.5, 1, $CodigoBarras, 0, 1, 'C'); // Código de barras centrado
                $pdf->SetXY($posX + 7, $pdf->GetY()); 
                $pdf->Cell(17.5, 1, $NumeroCable, 0, 1, 'C'); 
                $counter++;  // Incrementar el contador de etiquetas colocadas
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return $pdf->Output('orden_fabricacion_'.$OrdenFabricacion->OrdenFabricacion.'_' . $partidaId . '.pdf', 'I'); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }*/
    public function generarPDF45X25(Request $request){
        try {
            $partidaId = $this->funcionesGenerales->decrypt($request->input('id'));
            $Coloretiqueta = rand(1, 9);
            if (!$partidaId) {
                throw new \Exception('ID no recibido');
            }
            $colores = [
                1 => ['nombre' => 'Azul Claro', 'rgb' => [14, 14, 231]],
                2 => ['nombre' => 'Rojo Claro', 'rgb' => [241, 48, 48]],
                3 => ['nombre' => 'Rosa Mexicano Claro', 'rgb' => [236, 42, 104]],
                4 => ['nombre' => 'Café Claro', 'rgb' => [150, 115, 90]],
                5 => ['nombre' => 'Verde Bandera Claro', 'rgb' => [5, 112, 62]],
                6 => ['nombre' => 'Amarillo Claro', 'rgb' => [252, 252, 44]],
                7 => ['nombre' => 'Verde Limón Claro', 'rgb' => [230, 252, 122]],
                8 => ['nombre' => 'Rosa Claro', 'rgb' => [255, 210, 220]],
                9 => ['nombre' => 'Naranja Claro', 'rgb' => [255, 168, 53]]
            ];
            $R= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][0]:255;
            $G= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][1]:255;
            $B= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][2]:255;
    
            // Buscar la partida por ID
            $PartidasOFAreas = partidasof_Areas::find($partidaId);
            if (is_null($PartidasOFAreas)) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $PartidaOF = PartidasOF::find($PartidasOFAreas->PartidasOF_id);
            if (is_null($PartidaOF) || is_null($PartidaOF->ordenFabricacion()->first())) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $OrdenFabricacion = $PartidaOF->ordenFabricacion;
            //$PartidasOFEtiq = $OrdenFabricacion->partidasOF()->get();
            $PartidasOFEtiq=$PartidaOF->Areas()->where('Areas_id',2)->get();
            $inicioR = $OrdenFabricacion->CantidadTotal;
            $inicio = 0;
            $finR = $OrdenFabricacion->CantidadTotal;
            $fin = 0;
            // Asignamos el número de inicio y fin de la etiqueta para la partida seleccionada
            $TipoPartida = "N";
            foreach($PartidasOFEtiq as $PartidaOFEtiq){
                $TipoPartida = $PartidaOFEtiq['pivot']->TipoPartida;
                if($TipoPartida == 'R'){
                    $finR += $PartidaOFEtiq['pivot']->Cantidad;
                }else{
                    $fin += $PartidaOFEtiq['pivot']->Cantidad;
                }
                $p=$PartidaOFEtiq['pivot']->id.'      '. $partidaId.'      \n';
                if($PartidaOFEtiq['pivot']->id == $partidaId){
                    if($TipoPartida == 'R'){
                        $inicio=$inicioR;
                        $fin = $finR;
                    }
                    break;
                }
                if($TipoPartida == 'R'){
                    $inicioR += $PartidaOFEtiq['pivot']->Cantidad;
                }else{
                    $inicio += $PartidaOFEtiq['pivot']->Cantidad;
                }
            }
            // Preparar las partidas relacionadas solo para la partida seleccionada
            $partidasData = [];
            $contador = $inicio + 1;
            $partidaId = $PartidaOF->NumeroPartida;
            for ($i = $inicio; $i < $fin; $i++) {
                $partidasData[] = [
                    'cantidad' => $contador, 
                    'descripcion' => $OrdenFabricacion->Descripcion ?? 'Sin Descripción',
                    'OrdenFabricacion' => $OrdenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
                    'Codigo'=> $OrdenFabricacion->OrdenFabricacion.'-'.$partidaId.'-'.$contador,
                ];
                $contador++; // Incrementar el contador
            }
    
            // Crear PDF
            $pdf = new TCPDF();
            
            // Ajustar márgenes
            $pdf->SetMargins(1, 3, 1); 
            $pdf->SetFont('helvetica', 'B', 5);  
            $pdf->SetAutoPageBreak(TRUE, 0.5);  
    
            $counter = 0;  // Contador para saber cuántas etiquetas se han colocado en la página
            foreach ($partidasData as $key=>$partida) {
                $NumeroCable=strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal;
                $pdf->AddPage('L', array(45, 25));  // Nueva página
                $posX = 0;
                
                // Añadir el color de fondo en la parte superior de la etiqueta
                $pdf->SetFillColor($R, $G, $B); 
                $pdf->Rect($posX, 0, 45, 3, 'F');  // Color de fondo en la parte superior de la etiqueta
                $pdf->SetTextColor(0, 0, 0);  // Color de texto en negro
    
                // Definir el contenido del texto para cada etiqueta
                if ($TipoPartida == 'N') {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . "\n" .
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" .
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                } else {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . " R \n" . 
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" . 
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                }
                // Añadir el contenido de texto
                $pdf->SetXY($posX + 4, 3);  // Colocamos el texto un poco desplazado desde el borde
                $pdf->MultiCell(40, 0, $content, 0, 'L', 0, 1);  // Ajustamos la celda para que se ajuste al ancho de la etiqueta
                // Generar y colocar el código de barras
                $CodigoBarras = strip_tags($partida['Codigo']);
                $pdf->SetXY($posX + 12, $pdf->GetY() + 2);  // Ajuste de la posición del código de barras
                $pdf->write1DBarcode($CodigoBarras, 'C128', '', '', 20, 5, 0.4, array(), 'N');
                $pdf->SetXY($posX + 12, $pdf->GetY() + 1);  // Ajustar la posición para el texto debajo del código de barras
                $pdf->Cell(17.5, 1, $CodigoBarras, 0, 1, 'C'); // Código de barras centrado
                $pdf->SetXY($posX + 12, $pdf->GetY()); 
                $pdf->Cell(17.5, 1, $NumeroCable, 0, 1, 'C'); 
                $counter++;  // Incrementar el contador de etiquetas colocadas
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return $pdf->Output('orden_fabricacion_'.$OrdenFabricacion->OrdenFabricacion.'_' . $partidaId . '.pdf', 'I'); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function generarPDF110X20(Request $request){
        try {
            $partidaId = $this->funcionesGenerales->decrypt($request->input('id'));
            $Coloretiqueta = rand(1, 9);
            if (!$partidaId) {
                throw new \Exception('ID no recibido');
            }
            $colores = [
                1 => ['nombre' => 'Azul Claro', 'rgb' => [14, 14, 231]],
                2 => ['nombre' => 'Rojo Claro', 'rgb' => [241, 48, 48]],
                3 => ['nombre' => 'Rosa Mexicano Claro', 'rgb' => [236, 42, 104]],
                4 => ['nombre' => 'Café Claro', 'rgb' => [150, 115, 90]],
                5 => ['nombre' => 'Verde Bandera Claro', 'rgb' => [5, 112, 62]],
                6 => ['nombre' => 'Amarillo Claro', 'rgb' => [252, 252, 44]],
                7 => ['nombre' => 'Verde Limón Claro', 'rgb' => [230, 252, 122]],
                8 => ['nombre' => 'Rosa Claro', 'rgb' => [255, 210, 220]],
                9 => ['nombre' => 'Naranja Claro', 'rgb' => [255, 168, 53]]
            ];
            $R= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][0]:255;
            $G= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][1]:255;
            $B= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][2]:255;
    
            // Buscar la partida por ID
            $PartidasOFAreas = partidasof_Areas::find($partidaId);
            if (is_null($PartidasOFAreas)) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $PartidaOF = PartidasOF::find($PartidasOFAreas->PartidasOF_id);
            if (is_null($PartidaOF) || is_null($PartidaOF->ordenFabricacion()->first())) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $OrdenFabricacion = $PartidaOF->ordenFabricacion;
            //$PartidasOFEtiq = $OrdenFabricacion->partidasOF()->get();
            $PartidasOFEtiq=$PartidaOF->Areas()->where('Areas_id',2)->get();
            $inicioR = $OrdenFabricacion->CantidadTotal;
            $inicio = 0;
            $finR = $OrdenFabricacion->CantidadTotal;
            $fin = 0;
            // Asignamos el número de inicio y fin de la etiqueta para la partida seleccionada
            $TipoPartida = "N";
            foreach($PartidasOFEtiq as $PartidaOFEtiq){
                $TipoPartida = $PartidaOFEtiq['pivot']->TipoPartida;
                if($TipoPartida == 'R'){
                    $finR += $PartidaOFEtiq['pivot']->Cantidad;
                }else{
                    $fin += $PartidaOFEtiq['pivot']->Cantidad;
                }
                $p=$PartidaOFEtiq['pivot']->id.'      '. $partidaId.'      \n';
                if($PartidaOFEtiq['pivot']->id == $partidaId){
                    if($TipoPartida == 'R'){
                        $inicio=$inicioR;
                        $fin = $finR;
                    }
                    break;
                }
                if($TipoPartida == 'R'){
                    $inicioR += $PartidaOFEtiq['pivot']->Cantidad;
                }else{
                    $inicio += $PartidaOFEtiq['pivot']->Cantidad;
                }
            }
            // Preparar las partidas relacionadas solo para la partida seleccionada
            $partidasData = [];
            $contador = $inicio + 1;
            $partidaId = $PartidaOF->NumeroPartida;
            for ($i = $inicio; $i < $fin; $i++) {
                $partidasData[] = [
                    'cantidad' => $contador, 
                    'descripcion' => $OrdenFabricacion->Descripcion ?? 'Sin Descripción',
                    'OrdenFabricacion' => $OrdenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
                    'Codigo'=> $OrdenFabricacion->OrdenFabricacion.'-'.$partidaId.'-'.$contador,
                ];
                $contador++; // Incrementar el contador
            }
    
            // Crear PDF
            $pdf = new TCPDF();
            
            // Ajustar márgenes
            $pdf->SetMargins(1, 1, 1); 
            $pdf->SetFont('helvetica', 'B', 4.2);  
            $pdf->SetAutoPageBreak(TRUE, 0.5);    
    
            $counter = 0;  // Contador para saber cuántas etiquetas se han colocado en la página
            foreach ($partidasData as $key=>$partida) {
                $NumeroCable=strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal;
                $pdf->AddPage('L', array(110, 20));  // Nueva página
                $posX = 0;
                
                // Añadir el color de fondo en la parte superior de la etiqueta
                /*$pdf->SetFillColor($R, $G, $B); 
                $pdf->Rect($posX, 0, 110, 3, 'F');  // Color de fondo en la parte superior de la etiqueta
                $pdf->SetTextColor(0, 0, 0);  // Color de texto en negro*/
    
                // Definir el contenido del texto para cada etiqueta
                if ($TipoPartida == 'N') {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . "\n" .
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" .
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                } else {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . " R \n" . 
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" . 
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                }
                // Añadir el contenido de texto
                $pdf->SetXY($posX, 1);  // Colocamos el texto un poco desplazado desde el borde
                $pdf->MultiCell(30, 0, $content, 0, 'L', 0, 1);  // Ajustamos la celda para que se ajuste al ancho de la etiqueta
                // Generar y colocar el código de barras
                $CodigoBarras = strip_tags($partida['Codigo']);
                $pdf->SetXY($posX + 31, 2);  // Ajuste de la posición del código de barras
                $pdf->write1DBarcode($CodigoBarras, 'C128', '', '', 28, 8, 0.4, array(), 'N');
                $pdf->SetXY($posX + 33, $pdf->GetY() + 1);  // Ajustar la posición para el texto debajo del código de barras
                $pdf->Cell(17.5, 1, $CodigoBarras, 0, 1, 'C'); // Código de barras centrado
                $pdf->SetXY($posX + 33, $pdf->GetY()); 
                $pdf->Cell(17.5, 1, $NumeroCable, 0, 1, 'C'); 
                $counter++;  // Incrementar el contador de etiquetas colocadas
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return $pdf->Output('orden_fabricacion_'.$OrdenFabricacion->OrdenFabricacion.'_' . $partidaId . '.pdf', 'I'); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function generarPDFSuministro(Request $request){
        try {
            $partidaId = $this->funcionesGenerales->decrypt($request->input('id'));
            if (!$partidaId) {
                throw new \Exception('ID no recibido');
            }
            // Buscar la partida por ID
            $PartidasOFAreas = partidasof_Areas::find($partidaId);
            if (is_null($PartidasOFAreas)) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $PartidaOF = PartidasOF::find($PartidasOFAreas->PartidasOF_id);
            if (is_null($PartidaOF) || is_null($PartidaOF->ordenFabricacion()->first())) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $OrdenFabricacion = $PartidaOF->ordenFabricacion;
            $PartidasOFEtiq=$PartidaOF->Areas()->where('Areas_id',3)->get();
            $inicioR = $OrdenFabricacion->CantidadTotal;
            $inicio = 0;
            $finR = $OrdenFabricacion->CantidadTotal;
            $fin = 0;
            // Asignamos el número de inicio y fin de la etiqueta para la partida seleccionada
            $TipoPartida = "N";
            foreach($PartidasOFEtiq as $key=>$PartidaOFEtiq){
                $TipoPartida = $PartidaOFEtiq['pivot']->TipoPartida;
                if($TipoPartida == 'R'){
                    $finR += $PartidaOFEtiq['pivot']->Cantidad;
                }else{
                    $fin += $PartidaOFEtiq['pivot']->Cantidad;
                }
                if($PartidaOFEtiq['pivot']->id == $partidaId){
                    if($TipoPartida == 'R'){
                        $inicio=$inicioR;
                        $fin = $finR;
                    }
                    break;
                }
                if($TipoPartida == 'R'){
                    $inicioR += $PartidaOFEtiq['pivot']->Cantidad;

                }else{
                    $inicio += $PartidaOFEtiq['pivot']->Cantidad;
                }
            }
            $partidasData = [];
            $contador = $inicio + 1;
            $partidaId = $PartidaOF->NumeroPartida;
            for ($i = $inicio; $i < $fin; $i++) {
                $partidasData[] = [
                    'cantidad' => $contador, 
                    'descripcion' => $OrdenFabricacion->Descripcion ?? 'Sin Descripción',
                    'OrdenFabricacion' => $OrdenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
                    'Codigo'=> $OrdenFabricacion->OrdenFabricacion.'-'.$partidaId.'-'.$contador,
                ];
                $contador++; // Incrementar el contador
            }
            // Crear PDF
            $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(1, 1, 1);
            $pdf->SetFont('helvetica', 'B', 6);  
            $pdf->SetAutoPageBreak(TRUE, 0.5); 
            $x = 0;  //Contador para saber cuántas etiquetas se han colocado en la página x
            $y = 0; //Contador para saber cuántas etiquetas se han colocado en la página y
            foreach ($partidasData as $key=>$partida) {
                $NumeroCable=strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal;
                if ($key % 40 == 0 ) {
                    $pdf->AddPage('P', 'Letter');
                }
                if($key % 40 == 0 AND $key!=0){
                    $y=-27.2;
                }
                // Calcular la posición X para cada etiqueta
                $posX = ($x % 4) * 53;
                if ($key % 4 == 0 AND $key != 0) {
                    $y+=27.2;
                }
                $pdf->SetFillColor(0, 0, 0); 
                $pdf->Rect($posX, $y+4, 80, 0.2, 'F');  // Color de fondo en la parte superior de la etiqueta
                $pdf->SetTextColor(0, 0, 0);
                // Definir el contenido del texto para cada etiqueta
                if ($TipoPartida == 'N') {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . "\n" .
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" .
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                } else {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . " R \n" . 
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" . 
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                }
                // Añadir el contenido de texto
                $pdf->SetXY($posX + 4, $y+5);  // Colocamos el texto un poco desplazado desde el borde
                $pdf->MultiCell(40, 25, $content, 0, 'L', 0, 1);  // Ajustamos la celda para que se ajuste al ancho de la etiqueta
                // Generar y colocar el código de barras
                $CodigoBarras = strip_tags($partida['Codigo']);
                $pdf->SetXY($posX + 10, $y+20);  // Ajuste de la posición del código de barras
                $pdf->write1DBarcode($CodigoBarras, 'C128', '', '', 20, 5, 0.4, array(), 'N');
                $pdf->SetXY($posX + 10, $y+26);  // Ajustar la posición para el texto debajo del código de barras
                $pdf->Cell(17.5, 1, $CodigoBarras, 0, 1, 'C'); // Código de barras centrado
                $pdf->SetXY($posX + 10, $y+28); 
                $pdf->Cell(17.5, 1, $NumeroCable, 0, 1, 'C'); 
                $x++;  // Incrementar el contador de etiquetas colocadas
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return $pdf->Output('orden_fabricacion_'.$OrdenFabricacion->OrdenFabricacion.'_' . $partidaId . '.pdf', 'I'); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    //Etiqueta de 3
    /*public function generarPDFSuministro(Request $request){
        try {
            $partidaId = $this->funcionesGenerales->decrypt($request->input('id'));
            $Coloretiqueta = rand(1, 9);
            if (!$partidaId) {
                throw new \Exception('ID no recibido');
            }
            $colores = [
                1 => ['nombre' => 'Azul Claro', 'rgb' => [14, 14, 231]],
                2 => ['nombre' => 'Rojo Claro', 'rgb' => [241, 48, 48]],
                3 => ['nombre' => 'Rosa Mexicano Claro', 'rgb' => [236, 42, 104]],
                4 => ['nombre' => 'Café Claro', 'rgb' => [150, 115, 90]],
                5 => ['nombre' => 'Verde Bandera Claro', 'rgb' => [5, 112, 62]],
                6 => ['nombre' => 'Amarillo Claro', 'rgb' => [252, 252, 44]],
                7 => ['nombre' => 'Verde Limón Claro', 'rgb' => [230, 252, 122]],
                8 => ['nombre' => 'Rosa Claro', 'rgb' => [255, 210, 220]],
                9 => ['nombre' => 'Naranja Claro', 'rgb' => [255, 168, 53]]
            ];
            $R= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][0]:255;
            $G= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][1]:255;
            $B= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][2]:255;
    
            // Buscar la partida por ID
            $PartidasOFAreas = partidasof_Areas::find($partidaId);
            if (is_null($PartidasOFAreas)) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $PartidaOF = PartidasOF::find($PartidasOFAreas->PartidasOF_id);
            if (is_null($PartidaOF) || is_null($PartidaOF->ordenFabricacion()->first())) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $OrdenFabricacion = $PartidaOF->ordenFabricacion;
            //$PartidasOFEtiq = $OrdenFabricacion->partidasOF()->get();
            $PartidasOFEtiq=$PartidaOF->Areas()->where('Areas_id',3)->get();
            $inicioR = $OrdenFabricacion->CantidadTotal;
            $inicio = 0;
            $finR = $OrdenFabricacion->CantidadTotal;
            $fin = 0;
            // Asignamos el número de inicio y fin de la etiqueta para la partida seleccionada
            $TipoPartida = "N";
            foreach($PartidasOFEtiq as $key=>$PartidaOFEtiq){
                $TipoPartida = $PartidaOFEtiq['pivot']->TipoPartida;
                if($TipoPartida == 'R'){
                    $finR += $PartidaOFEtiq['pivot']->Cantidad;
                }else{
                    $fin += $PartidaOFEtiq['pivot']->Cantidad;
                }
                if($PartidaOFEtiq['pivot']->id == $partidaId){
                    if($TipoPartida == 'R'){
                        $inicio=$inicioR;
                        $fin = $finR;
                    }
                    break;
                }
                if($TipoPartida == 'R'){
                    $inicioR += $PartidaOFEtiq['pivot']->Cantidad;

                }else{
                    $inicio += $PartidaOFEtiq['pivot']->Cantidad;
                }
            }
            // Preparar las partidas relacionadas solo para la partida seleccionada
            $partidasData = [];
            $contador = $inicio + 1;
            $partidaId = $PartidaOF->NumeroPartida;
            for ($i = $inicio; $i < $fin; $i++) {
                $partidasData[] = [
                    'cantidad' => $contador, 
                    'descripcion' => $OrdenFabricacion->Descripcion ?? 'Sin Descripción',
                    'OrdenFabricacion' => $OrdenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
                    'Codigo'=> $OrdenFabricacion->OrdenFabricacion.'-'.$partidaId.'-'.$contador,
                ];
                $contador++; // Incrementar el contador
            }
    
            // Crear PDF
            $pdf = new TCPDF();
            
            // Ajustar márgenes
            $pdf->SetMargins(1, 3, 1); 
            $pdf->SetFont('helvetica', 'B', 3.8);  
            $pdf->SetAutoPageBreak(TRUE, 0.5);  
    
            $counter = 0;  // Contador para saber cuántas etiquetas se han colocado en la página
            foreach ($partidasData as $key=>$partida) {
                $NumeroCable=strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal;
                // Si ya hemos colocado tres etiquetas en la fila, añadimos una nueva página
                if ($key % 3 == 0 || $key == 0) {
                    $pdf->AddPage('L', array(112, 25));  // Nueva página
                }
                
                // Calcular la posición X para cada etiqueta
                $posX = ($counter % 3) * 37.33; // Esto distribuye las etiquetas horizontalmente (3 por fila)
                
                // Añadir el color de fondo en la parte superior de la etiqueta
                $pdf->SetFillColor($R, $G, $B); 
                $pdf->Rect($posX, 0, 37.33, 3, 'F');  // Color de fondo en la parte superior de la etiqueta
                $pdf->SetTextColor(0, 0, 0);  // Color de texto en negro
    
                // Definir el contenido del texto para cada etiqueta
                if ($TipoPartida == 'N') {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . "\n" .
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" .
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                } else {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . " R \n" . 
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" . 
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                }
                // Añadir el contenido de texto
                $pdf->SetXY($posX + 4, 3);  // Colocamos el texto un poco desplazado desde el borde
                $pdf->MultiCell(28, 0, $content, 0, 'L', 0, 1);  // Ajustamos la celda para que se ajuste al ancho de la etiqueta
                // Generar y colocar el código de barras
                $CodigoBarras = strip_tags($partida['Codigo']);
                $pdf->SetXY($posX + 7, $pdf->GetY() + 2);  // Ajuste de la posición del código de barras
                $pdf->write1DBarcode($CodigoBarras, 'C128', '', '', 20, 5, 0.4, array(), 'N');
                $pdf->SetXY($posX + 7, $pdf->GetY() + 1);  // Ajustar la posición para el texto debajo del código de barras
                $pdf->Cell(17.5, 1, $CodigoBarras, 0, 1, 'C'); // Código de barras centrado
                $pdf->SetXY($posX + 7, $pdf->GetY()); 
                $pdf->Cell(17.5, 1, $NumeroCable, 0, 1, 'C'); 
                $counter++;  // Incrementar el contador de etiquetas colocadas
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return $pdf->Output('orden_fabricacion_'.$OrdenFabricacion->OrdenFabricacion.'_' . $partidaId . '.pdf', 'I'); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }*/
    public function generarPDFSuministro45X25(Request $request){
        try {
            $partidaId = $this->funcionesGenerales->decrypt($request->input('id'));
            $Coloretiqueta = rand(1, 9);
            if (!$partidaId) {
                throw new \Exception('ID no recibido');
            }
            $colores = [
                1 => ['nombre' => 'Azul Claro', 'rgb' => [14, 14, 231]],
                2 => ['nombre' => 'Rojo Claro', 'rgb' => [241, 48, 48]],
                3 => ['nombre' => 'Rosa Mexicano Claro', 'rgb' => [236, 42, 104]],
                4 => ['nombre' => 'Café Claro', 'rgb' => [150, 115, 90]],
                5 => ['nombre' => 'Verde Bandera Claro', 'rgb' => [5, 112, 62]],
                6 => ['nombre' => 'Amarillo Claro', 'rgb' => [252, 252, 44]],
                7 => ['nombre' => 'Verde Limón Claro', 'rgb' => [230, 252, 122]],
                8 => ['nombre' => 'Rosa Claro', 'rgb' => [255, 210, 220]],
                9 => ['nombre' => 'Naranja Claro', 'rgb' => [255, 168, 53]]
            ];
            $R= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][0]:255;
            $G= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][1]:255;
            $B= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][2]:255;
    
            // Buscar la partida por ID
            $PartidasOFAreas = partidasof_Areas::find($partidaId);
            if (is_null($PartidasOFAreas)) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $PartidaOF = PartidasOF::find($PartidasOFAreas->PartidasOF_id);
            if (is_null($PartidaOF) || is_null($PartidaOF->ordenFabricacion()->first())) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $OrdenFabricacion = $PartidaOF->ordenFabricacion;
            //$PartidasOFEtiq = $OrdenFabricacion->partidasOF()->get();
            $PartidasOFEtiq=$PartidaOF->Areas()->where('Areas_id',3)->get();
            $inicioR = $OrdenFabricacion->CantidadTotal;
            $inicio = 0;
            $finR = $OrdenFabricacion->CantidadTotal;
            $fin = 0;
            // Asignamos el número de inicio y fin de la etiqueta para la partida seleccionada
            $TipoPartida = "N";
            foreach($PartidasOFEtiq as $key=>$PartidaOFEtiq){
                $TipoPartida = $PartidaOFEtiq['pivot']->TipoPartida;
                if($TipoPartida == 'R'){
                    $finR += $PartidaOFEtiq['pivot']->Cantidad;
                }else{
                    $fin += $PartidaOFEtiq['pivot']->Cantidad;
                }
                if($PartidaOFEtiq['pivot']->id == $partidaId){
                    if($TipoPartida == 'R'){
                        $inicio=$inicioR;
                        $fin = $finR;
                    }
                    break;
                }
                if($TipoPartida == 'R'){
                    $inicioR += $PartidaOFEtiq['pivot']->Cantidad;

                }else{
                    $inicio += $PartidaOFEtiq['pivot']->Cantidad;
                }
            }
            // Preparar las partidas relacionadas solo para la partida seleccionada
            $partidasData = [];
            $contador = $inicio + 1;
            $partidaId = $PartidaOF->NumeroPartida;
            for ($i = $inicio; $i < $fin; $i++) {
                $partidasData[] = [
                    'cantidad' => $contador, 
                    'descripcion' => $OrdenFabricacion->Descripcion ?? 'Sin Descripción',
                    'OrdenFabricacion' => $OrdenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
                    'Codigo'=> $OrdenFabricacion->OrdenFabricacion.'-'.$partidaId.'-'.$contador,
                ];
                $contador++; // Incrementar el contador
            }
    
            // Crear PDF
            $pdf = new TCPDF();
            
            // Ajustar márgenes
            $pdf->SetMargins(1, 3, 1); 
            $pdf->SetFont('helvetica', 'B', 4);  
            $pdf->SetAutoPageBreak(TRUE, 0.5);  
    
            $counter = 0;  // Contador para saber cuántas etiquetas se han colocado en la página
            foreach ($partidasData as $key=>$partida) {
                $NumeroCable=strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal;
                // Si ya hemos colocado tres etiquetas en la fila, añadimos una nueva página
                $pdf->AddPage('L', array(45, 25));  // Nueva página
                
                // Calcular la posición X para cada etiqueta
                $posX = 0;
                
                // Añadir el color de fondo en la parte superior de la etiqueta
                $pdf->SetFillColor($R, $G, $B); 
                $pdf->Rect($posX, 0, 45, 3, 'F');  // Color de fondo en la parte superior de la etiqueta
                $pdf->SetTextColor(0, 0, 0);  // Color de texto en negro
    
                // Definir el contenido del texto para cada etiqueta
                if ($TipoPartida == 'N') {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . "\n" .
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" .
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                } else {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . " R \n" . 
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" . 
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                }
                // Añadir el contenido de texto
                $pdf->SetXY($posX + 4, 3);  // Colocamos el texto un poco desplazado desde el borde
                $pdf->MultiCell(40, 0, $content, 0, 'L', 0, 1);  // Ajustamos la celda para que se ajuste al ancho de la etiqueta
                // Generar y colocar el código de barras
                $CodigoBarras = strip_tags($partida['Codigo']);
                $pdf->SetXY($posX + 12, $pdf->GetY() + 2);  // Ajuste de la posición del código de barras
                $pdf->write1DBarcode($CodigoBarras, 'C128', '', '', 20, 5, 0.4, array(), 'N');
                $pdf->SetXY($posX + 12, $pdf->GetY() + 1);  // Ajustar la posición para el texto debajo del código de barras
                $pdf->Cell(17.5, 1, $CodigoBarras, 0, 1, 'C'); // Código de barras centrado
                $pdf->SetXY($posX + 12, $pdf->GetY()); 
                $pdf->Cell(17.5, 1, $NumeroCable, 0, 1, 'C'); 
                $counter++;  // Incrementar el contador de etiquetas colocadas
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return $pdf->Output('orden_fabricacion_'.$OrdenFabricacion->OrdenFabricacion.'_' . $partidaId . '.pdf', 'I'); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function generarPDFSuministro110X20(Request $request){
        try {
            $partidaId = $this->funcionesGenerales->decrypt($request->input('id'));
            $Coloretiqueta = rand(1, 9);
            if (!$partidaId) {
                throw new \Exception('ID no recibido');
            }
            $colores = [
                1 => ['nombre' => 'Azul Claro', 'rgb' => [14, 14, 231]],
                2 => ['nombre' => 'Rojo Claro', 'rgb' => [241, 48, 48]],
                3 => ['nombre' => 'Rosa Mexicano Claro', 'rgb' => [236, 42, 104]],
                4 => ['nombre' => 'Café Claro', 'rgb' => [150, 115, 90]],
                5 => ['nombre' => 'Verde Bandera Claro', 'rgb' => [5, 112, 62]],
                6 => ['nombre' => 'Amarillo Claro', 'rgb' => [252, 252, 44]],
                7 => ['nombre' => 'Verde Limón Claro', 'rgb' => [230, 252, 122]],
                8 => ['nombre' => 'Rosa Claro', 'rgb' => [255, 210, 220]],
                9 => ['nombre' => 'Naranja Claro', 'rgb' => [255, 168, 53]]
            ];
            $R= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][0]:255;
            $G= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][1]:255;
            $B= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][2]:255;
    
            // Buscar la partida por ID
            $PartidasOFAreas = partidasof_Areas::find($partidaId);
            if (is_null($PartidasOFAreas)) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $PartidaOF = PartidasOF::find($PartidasOFAreas->PartidasOF_id);
            if (is_null($PartidaOF) || is_null($PartidaOF->ordenFabricacion()->first())) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $OrdenFabricacion = $PartidaOF->ordenFabricacion;
            //$PartidasOFEtiq = $OrdenFabricacion->partidasOF()->get();
            $PartidasOFEtiq=$PartidaOF->Areas()->where('Areas_id',3)->get();
            $inicioR = $OrdenFabricacion->CantidadTotal;
            $inicio = 0;
            $finR = $OrdenFabricacion->CantidadTotal;
            $fin = 0;
            // Asignamos el número de inicio y fin de la etiqueta para la partida seleccionada
            $TipoPartida = "N";
            foreach($PartidasOFEtiq as $key=>$PartidaOFEtiq){
                $TipoPartida = $PartidaOFEtiq['pivot']->TipoPartida;
                if($TipoPartida == 'R'){
                    $finR += $PartidaOFEtiq['pivot']->Cantidad;
                }else{
                    $fin += $PartidaOFEtiq['pivot']->Cantidad;
                }
                if($PartidaOFEtiq['pivot']->id == $partidaId){
                    if($TipoPartida == 'R'){
                        $inicio=$inicioR;
                        $fin = $finR;
                    }
                    break;
                }
                if($TipoPartida == 'R'){
                    $inicioR += $PartidaOFEtiq['pivot']->Cantidad;

                }else{
                    $inicio += $PartidaOFEtiq['pivot']->Cantidad;
                }
            }
            // Preparar las partidas relacionadas solo para la partida seleccionada
            $partidasData = [];
            $contador = $inicio + 1;
            $partidaId = $PartidaOF->NumeroPartida;
            for ($i = $inicio; $i < $fin; $i++) {
                $partidasData[] = [
                    'cantidad' => $contador, 
                    'descripcion' => $OrdenFabricacion->Descripcion ?? 'Sin Descripción',
                    'OrdenFabricacion' => $OrdenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
                    'Codigo'=> $OrdenFabricacion->OrdenFabricacion.'-'.$partidaId.'-'.$contador,
                ];
                $contador++; // Incrementar el contador
            }
    
            // Crear PDF
            $pdf = new TCPDF();
            
            // Ajustar márgenes
            $pdf->SetMargins(1, 1, 1); 
            $pdf->SetFont('helvetica', 'B', 4.2);  
            $pdf->SetAutoPageBreak(TRUE, 0.5);  
    
            $counter = 0;  // Contador para saber cuántas etiquetas se han colocado en la página
            foreach ($partidasData as $key=>$partida) {
                $NumeroCable=strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal;
                // Si ya hemos colocado tres etiquetas en la fila, añadimos una nueva página
                $pdf->AddPage('L', array(110, 20));  // Nueva página
                // Calcular la posición X para cada etiqueta
                $posX = 0;
                
                // Añadir el color de fondo en la parte superior de la etiqueta
                /*$pdf->SetFillColor($R, $G, $B); 
                $pdf->Rect($posX, 0, 110, 2, 'F');  // Color de fondo en la parte superior de la etiqueta
                $pdf->SetTextColor(0, 0, 0);  // Color de texto en negro*/
    
                // Definir el contenido del texto para cada etiqueta
                if ($TipoPartida == 'N') {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . "\n" .
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" .
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                } else {
                    $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . " / " . $OrdenFabricacion->CantidadTotal . " R \n" . 
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" . 
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";
                }
                // Añadir el contenido de texto
                $pdf->SetXY($posX, 1);  // Colocamos el texto un poco desplazado desde el borde
                $pdf->MultiCell(30, 0, $content, 0, 'L', 0, 1);  // Ajustamos la celda para que se ajuste al ancho de la etiqueta
                // Generar y colocar el código de barras
                $CodigoBarras = strip_tags($partida['Codigo']);
                $pdf->SetXY($posX + 36, 4);  // Ajuste de la posición del código de barras
                $pdf->write1DBarcode($CodigoBarras, 'C128', '', '', 20, 5, 0.4, array(), 'N');
                $pdf->SetXY($posX + 36, $pdf->GetY() + 1);  // Ajustar la posición para el texto debajo del código de barras
                $pdf->Cell(17.5, 1, $CodigoBarras, 0, 1, 'C'); // Código de barras centrado
                $pdf->SetXY($posX + 36, $pdf->GetY()); 
                $pdf->Cell(17.5, 1, $NumeroCable, 0, 1, 'C'); 
                $counter++;  // Incrementar el contador de etiquetas colocadas
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return $pdf->Output('orden_fabricacion_'.$OrdenFabricacion->OrdenFabricacion.'_' . $partidaId . '.pdf', 'I'); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}






