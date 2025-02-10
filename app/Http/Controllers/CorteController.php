<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PartidasOF;
use App\Models\Emision;
use App\Models\OrdenFabricacion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use TCPDF;

class CorteController extends Controller
{
    protected $funcionesGenerales;
    public function __construct(FuncionesGeneralesController $funcionesGenerales){
        $this->funcionesGenerales = $funcionesGenerales;
    }
    public function index(){
        // Fecha actual
        $fecha = date('Y-m-d');
        $fechaAtras=date('Y-m-d', strtotime('-1 week', strtotime($fecha)));
        $OrdenesFabricacionAbiertas=$this->OrdenesFabricacionAbiertas();
        $OrdenesFabricacionCerradas=$this->OrdenesFabricacionCerradas($fechaAtras, $fecha);
        return view('Areas.Cortes', compact('OrdenesFabricacionAbiertas','OrdenesFabricacionCerradas','fecha','fechaAtras'));
    }
    public function CorteRecargarTabla(){
        //EstatusEntrega==0 aun no iniciado; 1 igual a terminado
        try {
            //$OrdenFabricacion=OrdenFabricacion::where('EstatusEntrega','=','0')->get();
            $OrdenFabricacion=$this->OrdenesFabricacionAbiertas();
            $tabla="";
            foreach($OrdenFabricacion as $orden) {
                $tabla.='<tr>
                        <td>'. $orden->OrdenFabricacion .'</td>
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
        $Ordenfabricacionpartidas='<table id="TablePartidasModal" class="table table-sm fs--1 mb-0" style="width:100%">
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
                                <th class="text-center">Etiquetas</th>
                                <th class="text-center" colspan="2">Acciones</th>
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
            $id=$this->funcionesGenerales->encrypt($OrdenFabricacion->id);//gris 30 y 20 blanco style="width: 20%;"
            $Ordenfabricacioninfo.='
                              <table class="table table-bordered table-sm text-xs"> 
                                <tbody>
                                    <tr>
                                        <th class="table-active p-1"  style="width: 30%;" >Artículo</th>
                                        <td class="text-center p-1"  style="width: 20%;">'.$OrdenFabricacion->Articulo.'</td>
                                        <th class="table-active p-1"  style="width: 30%;" >Fecha Planeación</th>
                                        <td class="text-center p-1"  style="width: 20%;" >'.$OrdenFabricacion->FechaEntrega.'</td>
                                    </tr>
                                    <tr>
                                        <th class="table-active p-1"  style="width: 30%;" >Cantidad Total</th>
                                        <td class="text-center p-1"  style="width: 20%;" >'.$OrdenFabricacion->CantidadTotal.'</td>
                                        <th class="table-active p-1"  style="width: 30%;" >Piezas cortadas</th>
                                        <td class="text-center p-1"  style="width: 20%;" >'.$OrdenFabricacion->partidasOF()->get()->sum('cantidad_partida').'</td>
                                    </tr>
                                    <tr>
                                        <th class="table-active p-1"  style="width: 30%;" >Descripción</th>
                                        <td class="text-center p-1"  style="width: 20%;"  colspan="3">'.$OrdenFabricacion->Descripcion.'</td>
                                    </tr>
                                </tbody>
                            </table>';
            $PartidasOF=$OrdenFabricacion->partidasOF()->get();
            if($PartidasOF->count()==0){
                $Ordenfabricacionpartidas.='<tr>
                                            <td class="text-center" colspan="9">Aún no existen partidas creadas</td>
                                        </tr>';
            }else{
                $RangoEtiquetas=1;
                foreach($PartidasOF as $partida){
                    $Ordenfabricacionpartidas.='<tr>
                        <th class="text-center">'.$partida->NumeroPartida.'</th>';
                    $Ordenfabricacionpartidas.='<td class="text-center">'.$partida->cantidad_partida.'</td>';
                    if($partida->TipoPartida=='R'){
                        $Ordenfabricacionpartidas.='<td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">Retrabajo</span></div></td>';
                    }else{
                        $Ordenfabricacionpartidas.='<td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Normal</span></div></td>';
                    }
                    if($partida->cantidad_partida==1){
                        $Ordenfabricacionpartidas.='<td class="text-center">'.$RangoEtiquetas.'</td>';
                    }else{
                        $Ordenfabricacionpartidas.='<td class="text-center">'.$RangoEtiquetas."-".($RangoEtiquetas+$partida->cantidad_partida-1).'</td>';
                    }
                    if(!($partida->FechaFinalizacion=='' || $partida->FechaFinalizacion==null)){
                        $Ordenfabricacionpartidas.='<td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-danger"><span class="fw-bold">Cerrada</span></div></td>';
                    }else{
                        $Ordenfabricacionpartidas.='<td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Abierta</span></div></td>';
                    }
                    $Ordenfabricacionpartidas.='<td class="text-center"><button class="btn btn-link me-1 mb-1" onclick="etiquetaColor(\''.$this->funcionesGenerales->encrypt($partida->id).'\')" type="button"><i class="fas fa-download"></i></button></td>';
                    if ($request->has('detalles')) {
                        $Ordenfabricacionpartidas.='<td class="text-center"></td><td></td>
                        </tr>';
                    }else{
                        if($partida->FechaFinalizacion == '' || $partida->FechaFinalizacion == null){
                            $Ordenfabricacionpartidas .= '<td>
                                <button class="btn btn-sm btn-outline-danger rounded-pill me-1 mb-1 px-3 py-1" 
                                    type="button" 
                                    onclick="Finalizar(\'' . $this->funcionesGenerales->encrypt($partida->id) . '\')">
                                    Finalizar
                                </button>
                            </td>';
                            $Ordenfabricacionpartidas .= '<td class="text-center">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill me-1 mb-1 px-3 py-1" 
                                type="button" 
                                onclick="Cancelar(\'' . $this->funcionesGenerales->encrypt($partida->id) . '\',\'' . $partida->NumeroPartida . '\')">
                                Cancelar
                            </button>
                        </td>';  
                        }
                        $Ordenfabricacionpartidas .= '</tr>';
                    }
                    
                    
                        $RangoEtiquetas+=$partida->cantidad_partida;
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
            'id' => $id
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
            $Emisiones=$this->Emisiones($OrdenFabricacion->OrdenFabricacion);
                foreach ($Emisiones as $Emision){
                    $Emisiondatos=$OrdenFabricacion->Emisions()->where('NumEmision',$Emision['NoEmision'])->first();
                        if($Emisiondatos==""){
                            $opciones.='<option value="'.$Emision['NoEmision'].'">'.$Emision['NoEmision'].'</option>';
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
            $OrdenFabricacion=OrdenFabricacion::where('id','=',$id)->first();
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
                }elseif($retrabajo=='Normal')
                {
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

    }
    public function CancelarCorte(Request $request){
        $id=$this->funcionesGenerales->decrypt($request->id);
        $PartidaOF = PartidasOF::find($id);
        if($PartidaOF=="" || $PartidaOF==null){
            return response()->json([
                'status' => 'errornoexiste',
                'message' =>'La Orden de Fabricación no existe!',
            ]);
        }else{
            $Partidas=$PartidaOF->Areas()->where('Areas_id',3)->get();
            if($Partidas->count()==0){
                if(!($PartidaOF->FechaFinalizacion == "" || $PartidaOF->FechaFinalizacion == null)){
                    return response()->json([
                        'status' => 'errorfinalizada',
                        'message' =>'Partida '.$PartidaOF->NumeroPartida.' Ocurrio un error!, No se puede cancelar la Partida, porque ya se encuentra finalizada',
                        'OF' =>$this->funcionesGenerales->encrypt($PartidaOF->ordenFabricacion()->first()->id),
                    ]);
                }
                $OrdenFabricacion=$PartidaOF->ordenFabricacion()->first();
                $OrdenEmision=$OrdenFabricacion->Emisions()->where("EtapaEmision",'C')->where("Etapaid",$PartidaOF->id)->first();
                $PartidaOF->delete();
                if($OrdenEmision!=""){
                    $OrdenEmision->delete();
                }
                $SumaPartidas=$OrdenFabricacion->partidasOF()->where('TipoPartida','=','N')->where('FechaFinalizacion','!=',null)->get()->sum('cantidad_partida');
                $PartidasIniciadas=$OrdenFabricacion->partidasOF()->where('FechaFinalizacion','=',null)->get();
                if($OrdenFabricacion->CantidadTotal==$SumaPartidas && $PartidasIniciadas->count()==0){
                    $OrdenFabricacion->EstatusEntrega=1;
                    $OrdenFabricacion->save();
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
        $PartidasOF = PartidasOF::find($id);
        if($PartidasOF=="" || $PartidasOF==null){
            return response()->json([
                'status' => 'errornoexiste',
                'message' =>'La Orden de Fabricación no existe!',
            ]);
        }
        if(!($PartidasOF->FechaFinalizacion == "" || $PartidasOF->FechaFinalizacion == null)){
            return response()->json([
                'status' => 'errorfinalizada',
                'message' =>'Partida '.$PartidasOF->NumeroPartida.' Ocurrio un error!, La partida ya se encuentra finalizada',
                'OF' =>$this->funcionesGenerales->encrypt($PartidasOF->ordenFabricacion()->first()->id),
            ]);
        }
        $fechaHoy=date('Y-m-d H:i:s');
        $PartidasOF->FechaFinalizacion = $fechaHoy;
        $PartidasOF->save();
        //Sacamos las partidas que ya estan finalizadas 
        $OrdenFabricacion=$PartidasOF->ordenFabricacion()->first();
        $SumaPartidas=$OrdenFabricacion->partidasOF()->where('TipoPartida','=','N')->where('FechaFinalizacion','!=',null)->get()->sum('cantidad_partida');
        $PartidasIniciadas=$OrdenFabricacion->partidasOF()->where('FechaFinalizacion','=',null)->get();
        if($OrdenFabricacion->CantidadTotal==$SumaPartidas && $PartidasIniciadas->count()==0){
            $OrdenFabricacion->EstatusEntrega=1;
            $OrdenFabricacion->save();
        }
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
    public function Emisiones($OrdenFabricacion){
        $OrdenFabricacion;
        $schema = 'HN_OPTRONICS';
        /*$query_emisiones="SELECT T00.\"DocNum\" \"NoEmision\", T00.\"DocDate\" \"FechaEmision\", T111.\"ItemCode\" \"Componente\", T111.\"Dscription\" \"Descripcion\",
                            T111.\"Quantity\" \"Cantidad\", T111.\"WhsCode\" \"Almacen\"*/
        $query_emisiones="SELECT DISTINCT T00.\"DocNum\" \"NoEmision\", TO_DATE(T00.\"DocDate\") \"FechaEmision\", T00.\"Ref2\" \"Cantidad\"                       
                        FROM {$schema}.\"OIGE\" T00
                        LEFT JOIN {$schema}.\"IGE1\" T111 ON T00.\"DocEntry\" = T111.\"DocEntry\"
                        LEFT JOIN {$schema}.\"OWOR\" T222 ON T111.\"BaseEntry\" = T222.\"DocEntry\" AND T111.\"BaseType\" = T222.\"ObjType\"
                        LEFT JOIN {$schema}.\"WOR1\" T333 ON T222.\"DocEntry\" = T333.\"DocEntry\" AND T111.\"BaseLine\" = T333.\"LineNum\"
                        WHERE T222.\"DocNum\" = ".$OrdenFabricacion."
                        ORDER BY 1";
        return$emisiones=$this->funcionesGenerales->ejecutarConsulta($query_emisiones);
    }
    //Ordenes de Fbaricacion Filtro Fecha, Abierta=0 Cerrada=1
    public function OrdenesFabricacionAbiertas(){
        $OrdenFabricacion=OrdenFabricacion::where('EstatusEntrega','=','0')->orderBy('FechaEntrega', 'asc')->get();
        foreach($OrdenFabricacion as $orden) {
            $orden['idEncript'] = $this->funcionesGenerales->encrypt($orden->id);
            $orden['Piezascortadas'] = $orden->partidasOF()->where('TipoPartida','N')->get()->sum('cantidad_partida');
            $orden['PiezascortadasR'] = $orden->partidasOF()->where('TipoPartida','R')->get()->sum('cantidad_partida');
            $orden->id="";
        }
        return $OrdenFabricacion;
    }
    public function OrdenesFabricacionCerradas($Fechainicio, $Fechafin){
        //$Fechainicio = Carbon::parse($Fechainicio)->addDay();
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
                $FechaFinalizacion=$OrdenFabricacion1->partidasOF()->orderBy('FechaFinalizacion','desc')->first()->FechaFinalizacion;
                if($FechaFinalizacion!=$orden->FechaFinalizacion){
                    unset($OrdenFabricacion[$key]);
                }else{
                    $arrayOF[]=$orden->OrdenFabricacion;
                    $orden['FechaComienzo']=$OrdenFabricacion1->partidasOF()->orderBy('FechaComienzo')->first()->FechaComienzo;
                    $orden['idEncript'] = $this->funcionesGenerales->encrypt($orden->OrdenFabricacion_id);
                    $orden['Piezascortadas'] = $OrdenFabricacion1->partidasOF()->where('TipoPartida','N')->get()->sum('cantidad_partida');
                    $orden['PiezascortadasR'] = $OrdenFabricacion1->partidasOF()->where('TipoPartida','R')->get()->sum('cantidad_partida');
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
            $colores = [
                1 => ['nombre' => 'Azul Claro', 'rgb' => [14, 14, 231]], // Azul más claro y más fuerte
                2 => ['nombre' => 'Rojo Claro', 'rgb' => [241, 48, 48]], // Rojo más claro y más fuerte
                3 => ['nombre' => 'Rosa Mexicano Claro', 'rgb' => [236, 42, 104]], // Rosa Mexicano más claro y más fuerte
                4 => ['nombre' => 'Café Claro', 'rgb' => [150, 115, 90]], // Café más claro y más fuerte
                5 => ['nombre' => 'Verde Bandera Claro', 'rgb' => [5, 112, 62]], // Verde Bandera más claro y más fuerte
                6 => ['nombre' => 'Amarillo Claro', 'rgb' => [252, 252, 44]], // Amarillo más claro y más fuerte
                7 => ['nombre' => 'Verde Limón Claro', 'rgb' => [230, 252, 122]], // Verde Limón más claro y más fuerte
                8 => ['nombre' => 'Rosa Claro', 'rgb' => [255, 210, 220]], // Rosa más claro y más fuerte
                9 => ['nombre' => 'Naranja Claro', 'rgb' => [255, 168, 53]] // Naranja más claro y más fuerte
            ];
            $R= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][0]:255;
            $G= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][1]:255;
            $B= isset($colores[$Coloretiqueta])?$colores[$Coloretiqueta]['rgb'][2]:255;
            // Buscar la partida por ID
            $PartidaOF = PartidasOF::find($partidaId);
            if (is_null($PartidaOF) || is_null($PartidaOF->ordenFabricacion()->first())) {
                throw new \Exception('No se encontraron datos para este ID.');
            }
            $OrdenFabricacion = $PartidaOF->ordenFabricacion;
            $PartidasOFEtiq=$OrdenFabricacion->partidasOF()->get();
            $inicio=0;
            $inicio=0;
            $fin=0;
            //Asignamos el numero de inicio y fin de la etiqueta para la partida seleccionada
            foreach($PartidasOFEtiq as $PartidaOFEtiq){
                $fin+=$PartidaOFEtiq->cantidad_partida;
                if($PartidaOFEtiq->id==$partidaId){
                    break;
                }
                $inicio+=$PartidaOFEtiq->cantidad_partida;
            }
            // Preparar las partidas relacionadas solo para la partida seleccionada
            $partidasData = [];
            $contador = $inicio+1; 
            $partidaId=$PartidaOF->NumeroPartida;
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
            $pdf->SetMargins(3, 3, 3);
            $pdf->SetFont('helvetica', 'B', 10);

            foreach ($partidasData as $partida) {
                //Color de la pagina
                $pdf->AddPage('L', array(70, 100)); // Añadir una nueva página de 50mm x 100mm
                $pdf->SetFillColor($R, $G, $B); 
                $pdf->Rect(0, 0, $pdf->GetPageWidth(),3, 'F');
                $pdf->SetTextColor(0, 0, 0);  // Color de texto negro
                $content = 
                    'Número Cable: ' . strip_tags($partida['cantidad']) . "\n" .
                    'Orden de Fabricación: ' . strip_tags($partida['OrdenFabricacion']) . "\n" .
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";

                // Añadir el contenido a la página
                $pdf->MultiCell(0, 5, $content, 0, 'L', 0, 1);
                $CodigoBarras = strip_tags($partida['Codigo']);
                $pdf->SetXY(30, $pdf->GetY() + 5);
                $pdf->write1DBarcode($CodigoBarras, 'C128', '', '', 40, 10, 0.4, array(), 'N');
                $pdf->Cell(0,5, $CodigoBarras, 0, 1, 'C');
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return $pdf->Output('orden_fabricacion_' . $partidaId . '.pdf', 'I');//D descarga, I devolver
        } catch (\Exception $e) {
            //Log::error('Error al generar PDF: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }





/*
    public function SinCortesProceso(Request $request){
        //$today = date('Y-m-d');
        //$semna = date('Y-m-d', strtotime('-1 week'));
    
        // Consulta de órdenes de fabricación filtradas por la fecha actual
        $ordenesFabricacion = $this->filtroOvFechaTodas();
    
        // Agregar estatus calculado
        $ordenesFabricacion->transform(function ($item) {
            $cantidadTotal = $item->CantidadTotal;
            $sumaCantidadPartida = $item->suma_cantidad_partida;
    
            if ($sumaCantidadPartida == 0) {
                $item->estatus = 'Sin cortes';
            } elseif ($sumaCantidadPartida < $cantidadTotal) {
                $item->estatus = 'En proceso';
            } else {
                $item->estatus = 'Completado';
            }
    
            return $item;
        });
    
        // Filtrar los registros según los estatus
        $ordenesSinCorteYEnProceso = $ordenesFabricacion->filter(function ($orden) {
            return in_array($orden->estatus, ['Sin cortes', 'En proceso']);
        });
    
        // Convertir la colección a un arreglo
        return response()->json($ordenesSinCorteYEnProceso->values()->toArray());
    }
    public function Completado(Request $request){
       ;
        $today = date('Y-m-d');
        $semna = date('Y-m-d', strtotime('-1 week'));
    
        // Consulta de órdenes de fabricación filtradas por la fecha actual
        $ordenesFabricacion = $this->filtroOvFecha($today, $semna);
        
    
        // Agregar estatus calculado
        $ordenesFabricacion->transform(function ($item) {
            $cantidadTotal = $item->CantidadTotal;
            $sumaCantidadPartida = $item->suma_cantidad_partida;
    
            if ($sumaCantidadPartida == 0) {
                $item->estatus = 'Sin cortes';
            } elseif ($sumaCantidadPartida < $cantidadTotal) {
                $item->estatus = 'En proceso';
            } else {
                $item->estatus = 'Completado';
            }
    
            return $item;
        });
    
        // Filtrar los registros según los estatus
        $ordenesSinCorteYEnProceso = $ordenesFabricacion->filter(function ($orden) {
            return in_array($orden->estatus, ['Completado']);
        });
        
    
        // Convertir la colección a un arreglo
        return response()->json($ordenesSinCorteYEnProceso->values()->toArray());
    }
    public function filtroOvFechaTodas(){
        $ordenesFabricacion = OrdenFabricacion::join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')
            ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega',
                DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida')
            )
            ->groupBy(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega'
            )
            ->get();

        return $ordenesFabricacion;
    }
    public function filtroOvFecha($today, $semna){
        $ordenesFabricacion = OrdenFabricacion::join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')
            ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega',
                DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida')
            )

            ->where('OrdenFabricacion.FechaEntrega','<=', $today) 
            ->where('OrdenFabricacion.FechaEntrega','>=', $semna)
            ->groupBy(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega'
            )
            ->get();
            return $ordenesFabricacion;
    }
    public function filtrarPorFecha(Request $request){
        
        $fecha = $request->input('fecha');
        
        if (!$fecha) {
            return response()->json(['error' => 'La fecha es requerida'], 400);
        }

        $ordenesFabricacion = OrdenFabricacion::join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')
            ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega',
                DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida')
            )
            ->whereDate('OrdenFabricacion.FechaEntrega', $fecha)
            ->groupBy(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega'
            )
            ->get();
            $ordenesFabricacion->transform(function ($item) {
                $cantidadTotal = $item->CantidadTotal;
                $sumaCantidadPartida = $item->suma_cantidad_partida;
        
                if ($sumaCantidadPartida == 0) {
                    $item->estatus = 'Sin cortes';
                } elseif ($sumaCantidadPartida < $cantidadTotal) {
                    $item->estatus = 'En proceso';
                } else {
                    $item->estatus = 'Completado';
                }
        
                return $item;
            });

        return response()->json($ordenesFabricacion);
    }
    public function fechaCompletado(Request $request){
         
        $fecha = $request->input('fecha');
        
        if (!$fecha) {
            return response()->json(['error' => 'La fecha es requerida'], 400);
        }

        $ordenesFabricacion = OrdenFabricacion::join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')
            ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega',
                DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida')
            )
            ->whereDate('OrdenFabricacion.FechaEntrega', $fecha)
            ->groupBy(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega'
            )
            ->get();
            $ordenesFabricacion->transform(function ($item) {
                $cantidadTotal = $item->CantidadTotal;
                $sumaCantidadPartida = $item->suma_cantidad_partida;
        
                if ($sumaCantidadPartida == 0) {
                    $item->estatus = 'Sin cortes';
                } elseif ($sumaCantidadPartida < $cantidadTotal) {
                    $item->estatus = 'En proceso';
                } else {
                    $item->estatus = 'Completado';
                }
        
                return $item;
            });

        return response()->json($ordenesFabricacion);
    }
    public function getData(Request $request){
        $limit = $request->input('length', 10); // Número de registros por página
        $start = $request->input('start', 0);  // Índice del primer registro
        $searchValue = $request->input('search.value', ''); // Valor del filtro de búsqueda

        // Consulta para obtener los datos de OrdenFabricacion
        $query = OrdenFabricacion::join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')
            ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenVenta_id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega',
                'OrdenFabricacion.created_at',
                'OrdenFabricacion.updated_at',
                DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida') // Suma de las partidas
            )
            ->groupBy(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenVenta_id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega',
                'OrdenFabricacion.created_at',
                'OrdenFabricacion.updated_at'
            );

        // Total de registros sin filtrar
        $totalRecords = $query->count();

        // Aplicar filtro de búsqueda
        if (!empty($searchValue)) {
            $query->where('OrdenFabricacion.Articulo', 'like', '%' . $searchValue . '%');
        }

        // Total de registros filtrados
        $totalFiltered = $query->count();

        // Obtener los registros paginados
        $data = $query->skip($start)->take($limit)->get();

        // Transformar los datos para agregar el estatus
        $data->transform(function ($item) {
            $cantidadTotal = $item->CantidadTotal;
            $sumaCantidadPartida = $item->suma_cantidad_partida;

            // Obtener las partidas relacionadas
            $partidas = DB::table('partidasof')
                ->where('OrdenFabricacion_id', $item->id)
                ->get();

            // Verificar si alguna partida tiene 'FechaFinalizar' en null
            $pendientesFecha = $partidas->firstWhere('FechaFinalizar', null);
            if ($pendientesFecha) {
                $estatus = 'En proceso';
            } elseif ($sumaCantidadPartida >= $cantidadTotal) {
                $estatus = 'Completado';
            } else {
                $estatus = 'En proceso';
            }

            $item->estatus = $estatus; // Asignar el estatus al elemento
            return $item;
        });

        // Formato de respuesta para DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
        
    }
    public function cambiarEstatus(Request $request){
        $id = $request->input('id');
        $nuevoEstatus = $request->input('estatus');
        
        // Depuración: log del estatus recibido
        Log::debug('Estatus recibido:', ['estatus' => $nuevoEstatus]);

        $estatusValidos = ['Completado', 'En proceso', 'Sin cortes'];
        if (!in_array($nuevoEstatus, $estatusValidos)) {
            return response()->json(['error' => 'Estatus inválido'], 400);
        }

        // Buscar la orden de fabricación
        $orden = OrdenFabricacion::find($id);
        if (!$orden) {
            return response()->json(['error' => 'Orden no encontrada'], 404);
        }

        // Transformación del estatus según la lógica
        $cantidadTotal = $orden->CantidadTotal;
        $sumaCantidadPartida = $orden->suma_cantidad_partida;

        if ($sumaCantidadPartida == 0) {
            $orden->estatus = 'Sin cortes';
        } elseif ($sumaCantidadPartida < $cantidadTotal) {
            $orden->estatus = 'En proceso';
        } else {
            $orden->estatus = 'Completado';
        }

        // Regresar el nuevo estatus actualizado a la vista
        return response()->json([
            'message' => 'Estatus actualizado correctamente',
            'estatus' => $orden->estatus, // Devuelve el estatus calculado
        ]);
    }
    public function actualizarTablasecundaria(){
        $ordenesFabricacion = $this->index()->ordenesFabricacion; // Reutilizar lógica de index
        return response()->json($ordenesFabricacion);
    }
    public function actualizarTabla(){
        $ordenesFabricacion = $this->index()->ordenesFabricacion; // Reutilizar lógica de index
        return response()->json($ordenesFabricacion);
    }
    public function verDetalles($id){
        $ordenFabricacion = DB::table('OrdenFabricacion')
                            ->select('OrdenFabricacion.OrdenFabricacion', 
                                        'OrdenFabricacion.FechaEntrega', 
                                        'OrdenFabricacion.Articulo', 
                                        'OrdenFabricacion.Estado')
                            ->where('OrdenFabricacion.id', $id) 
                            ->first();
                            

        if ($ordenFabricacion) {
            return response()->json(['orden_fabricacion' => $ordenFabricacion]);
        } else {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }
    }
    public function buscarOrdenVenta(Request $request){
        $query = $request->input('query');
        $fechaHaceUnaSemana = $request->input('fechaHaceUnaSemana'); // Obtiene la fecha de hace una semana si está presente
    
        $queryBuilder = DB::table('OrdenFabricacion')
            ->select('OrdenFabricacion.id', 
                    'OrdenFabricacion.OrdenVenta_id', 
                    'OrdenFabricacion.OrdenFabricacion', 
                    'OrdenFabricacion.Articulo', 
                    'OrdenFabricacion.Descripcion', 
                    'OrdenFabricacion.CantidadTotal', 
                    'OrdenFabricacion.FechaEntregaSAP', 
                    'OrdenFabricacion.FechaEntrega', 
                    'OrdenFabricacion.created_at', 
                    'OrdenFabricacion.updated_at',
                    DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida'))
            ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->groupBy('OrdenFabricacion.id', 
                    'OrdenFabricacion.OrdenVenta_id', 
                    'OrdenFabricacion.OrdenFabricacion', 
                    'OrdenFabricacion.Articulo', 
                    'OrdenFabricacion.Descripcion', 
                    'OrdenFabricacion.CantidadTotal', 
                    'OrdenFabricacion.FechaEntregaSAP', 
                    'OrdenFabricacion.FechaEntrega', 
                    'OrdenFabricacion.created_at', 
                    'OrdenFabricacion.updated_at');
    
        // Si hay una consulta de búsqueda, aplica el filtro
        if ($query) {
            $queryBuilder->where('OrdenFabricacion.OrdenFabricacion', 'like', "%$query%");
        }
    
        // Si fechaHaceUnaSemana está presente, filtra las órdenes desde hace una semana
        if ($fechaHaceUnaSemana) {
            $queryBuilder->whereDate('OrdenFabricacion.created_at', '>=', $fechaHaceUnaSemana);
        }
    
        $resultados = $queryBuilder->get();
       
        $resultados->transform(function ($item) {
            $cantidadTotal = $item->CantidadTotal;
            $sumaCantidadPartida = $item->suma_cantidad_partida;
    
            // Lógica para determinar el estatus
            if ($sumaCantidadPartida == 0) {
                $item->estatus = 'Sin cortes';
            } elseif ($sumaCantidadPartida < $cantidadTotal) {
                $item->estatus = 'En proceso';
            } else {
                $item->estatus = 'Completado';
            }
    
            return $item;
        });
    
        // Devolver los resultados con estatus calculado
        return response()->json($resultados);
    }
    public function guardarPartidasOF(Request $request){
        // Validar que los datos recibidos sean correctos
        $request->validate([
            'datos_partidas' => 'required|array', // 'datos_partidas' debe ser un array
            'datos_partidas.*.orden_fabricacion_id' => 'required|exists:OrdenFabricacion,id', 
            'datos_partidas.*.cantidad_partida' => 'required|integer|min:1', 
            'datos_partidas.*.fecha_fabricacion' => 'required|date', 
        ]);
    
        // Validar y guardar las partidas
        foreach ($request->datos_partidas as $partida) {
            // Buscar la orden de fabricación correspondiente
            $ordenFabricacion = OrdenFabricacion::find($partida['orden_fabricacion_id']);
    
            // Obtener la suma actual de las partidas registradas para esta OrdenFabricacion_id
            $sumaActual = PartidasOF::where('OrdenFabricacion_id', $partida['orden_fabricacion_id'])
                ->sum('cantidad_partida');
    
            // Calcular la nueva suma incluyendo la cantidad que se desea ingresar
            $nuevaSuma = $sumaActual + $partida['cantidad_partida'];
    
            // Verificar si la nueva suma excede la cantidad total permitida
            if ($nuevaSuma > $ordenFabricacion->CantidadTotal) {
                return response("La cantidad total acumulada ({$nuevaSuma}) excede la cantidad total permitida ({$ordenFabricacion->CantidadTotal}) para la Orden.", 422);
            }
    
            // Crear la partida si pasa la validación
            PartidasOF::create([
                'OrdenFabricacion_id' => $partida['orden_fabricacion_id'],
                'cantidad_partida' => $partida['cantidad_partida'],
                'FechaFabricacion' => $partida['fecha_fabricacion'],
            ]);
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'Las partidas se guardaron correctamente',
        ]);
    }
    public function getDetalleOrden(Request $request){
        $ordenId = $request->id;

        if (!$ordenId) {
            return response()->json([
                'success' => false,
                'message' => 'ID no proporcionado.',
            ]);
        }

        try {
            // Busca los datos exactos con el modelo configurado
            $detalle = DB::table('OrdenFabricacion')->where('id', $ordenId)->first();

            if (!$detalle) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró la orden de fabricación.',
                ]);
            }

            // Devuelve los datos correctamente formateados
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $detalle->id,
                    'OrdenFabricacion' => $detalle->OrdenFabricacion,
                    'Articulo' => $detalle->Articulo,
                    'Descripcion' => $detalle->Descripcion,
                    'CantidadTotal' => $detalle->CantidadTotal,
                    'FechaEntregaSAP' => $detalle->FechaEntregaSAP,
                    'FechaEntrega' => $detalle->FechaEntrega,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los detalles: ' . $e->getMessage(),
            ]);
        }
    }
    public function getCortes(Request $request){
        $ordenFabricacionId = $request->id;

        // Obtiene las partidas relacionadas con la orden de fabricación
        $partidas = PartidasOF::where('OrdenFabricacion_id', $ordenFabricacionId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Formatea los datos antes de enviarlos
        $data = $partidas->map(function ($partida) {
            return [
                'id' => $partida->id,
                'cantidad_partida' => $partida->cantidad_partida,
                'fecha_fabricacion' => Carbon::parse($partida->fecha_fabricacion)->format('d-m-Y'), 
                'FechaFinalizacion' => $partida->FechaFinalizacion 
                    ? Carbon::parse($partida->FechaFinalizacion)->format('d-m-Y') 
                    : null, 
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    } 
    /*public function finalizarCorte(Request $request){
        // Validar que el ID exista y que la fecha sea válida
        $request->validate([
            'id' => 'required|exists:partidasof,id', 
            'fecha_finalizacion' => 'required|date', 
        ]);

        // Buscar el registro basado en el ID
        $corte = PartidasOF::find($request->id);

        // Actualizar el campo FechaFinalizacion
        $corte->FechaFinalizacion = $request->fecha_finalizacion;
        $corte->save();

        // Retornar una respuesta de éxito
        return response()->json([
            'success' => true,
            
        ]);
        
    }*/
    /*public function getEstatus(Request $request){
        $ordenFabricacion = OrdenFabricacion::findOrFail($request->id);
        return response()->json([
            'success' => true,
            'estatus' => $ordenFabricacion->estatus,
            'badgeClass' => match ($ordenFabricacion->estatus) {
                'Completado' => 'badge-success',
                'En proceso' => 'badge-warning',
                default => 'badge-danger',
            },
        ]);
    }
    public function getCantidadTotal($id){
        $ordenFabricacion = OrdenFabricacion::find($id);

        if ($ordenFabricacion) {
            return response()->json(['success' => true, 'CantidadTotal' => $ordenFabricacion->CantidadTotal]);
        }

        return response()->json(['success' => false, 'message' => 'Orden de fabricación no encontrada.']);
    }
    public function getCortesInfo($id){
        try {
            // Sumar los cortes registrados de la tabla `partidas_of`
            $sumaCortes = DB::table('partidasof')
                ->where('OrdenFabricacion_id', $id) 
                ->sum('cantidad_partida');

            // Obtener información de la orden de fabricación
            $ordenFabricacion = DB::table('ordenfabricacion')
                ->where('id', $id)
                ->first();

            if (!$ordenFabricacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orden de fabricación no encontrada.',
                ]);
            }

            return response()->json([
                'success' => true,
                'CantidadTotal' => $ordenFabricacion->CantidadTotal, 
                'cortes_registrados' => $sumaCortes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la información de los cortes.',
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function MostarInformacion(Request $request){
        $partidaId = $request->input('id');
    
        // Buscar la partida por ID
        $partida = PartidasOF::with('ordenFabricacion')->find($partidaId);
    
        if (is_null($partida) || is_null($partida->ordenFabricacion)) {
            return response()->json(['error' => 'No se encontraron datos para este ID.']);
        }
    
        // Obtener la orden de fabricación relacionada
        $ordenFabricacion = $partida->ordenFabricacion;
    
        // Calcular el inicio del contador para esta OrdenFabricacion_id
        $numeroInicial = PartidasOF::where('OrdenFabricacion_id', $partida->OrdenFabricacion_id)
            ->where('id', '<', $partidaId)
            ->sum('cantidad_partida') + 1;
    
        // Preparar las partidas relacionadas solo para la partida seleccionada
        $partidasData = [];
        $contador = $numeroInicial; 
    
        for ($i = 1; $i <= $partida->cantidad_partida; $i++) {
            $partidasData[] = [
                'cantidad' => $contador, 
                'descripcion' => $ordenFabricacion->Descripcion ?? 'Sin Descripción',
                'orden_fabricacion' => $ordenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
            ];
            $contador++; // Incrementar el contador
        }
    
        // Preparar la respuesta con la información de la partida seleccionada
        $response = [
            'orden_fabricacion' => $ordenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
            'descripcion' => $ordenFabricacion->Descripcion ?? 'Sin Descripción',
            'cantidad_partida' => $partida->cantidad_partida ?? 0,
            'partidas' => $partidasData, 
        ];
    
        return response()->json($response);
    }
    public function PDFCondicion(Request $request){
        try {
            //dd($request->all());
            
            $request->validate([
                'desde_no' => 'required|integer|min:1',
                'hasta_no' => 'required|integer|min:1|gte:desde_no',
            ]);

            
            $desde = $request->input('desde_no');
            $hasta = $request->input('hasta_no');

            // Obtener la orden de fabricación (solo si necesitas los datos de la orden de fabricación)
            $ordenFabricacion = OrdenFabricacion::find($request->input('id'));

            if (!$ordenFabricacion) {
                throw new \Exception('No se encontró la orden de fabricación');
            }

            // Crear las etiquetas con el rango especificado
            $partidasData = [];
            $contadorPartida = $desde; 

            // Generar tantas etiquetas como el rango lo indique
            for ($i = $desde; $i <= $hasta; $i++) {
                $partidasData[] = [
                    'no' => $contadorPartida,
                    'descripcion' => $ordenFabricacion->Descripcion ?? 'Sin Descripción',
                    'orden_fabricacion' => $ordenFabricacion->OrdenFabricacion,
                ];
                $contadorPartida++;
            }

            // Crear el PDF usando TCPDF o domPDF
            $pdf = new TCPDF();
            $pdf->SetMargins(5, 5, 5); 
            $pdf->AddPage();

            // Título de la orden de fabricación
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 10, 'Orden de Fabricación: ' . strip_tags($ordenFabricacion->OrdenFabricacion), 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(0, 5, 'Descripción: ' . strip_tags($ordenFabricacion->Descripcion), 0, 1, 'C');
            $pdf->Ln(5);  // Salto de línea

            // Datos de las partidas generadas
            foreach ($partidasData as $partida) {
                $content = 
                    'No: ' . strip_tags($partida['no']) . "\n" .
                    'Orden de Fabricación: ' . strip_tags($partida['orden_fabricacion']) . "\n" .
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";

                // Obtener la posición inicial y calcular la altura de la celda
                $startY = $pdf->GetY();

                // Ajustar el tamaño del rectángulo
                $rectWidth = 80;  // Ancho del rectángulo
                $rectHeight = 15;  // Altura del rectángulo ajustada
                $pdf->Rect(10, $startY, $rectWidth, $rectHeight, 'D');  // Dibujar el rectángulo

                // Colocar el contenido dentro del cuadro con fuente más pequeña
                $pdf->SetXY(12, $startY + 1);  
                $pdf->SetFont('helvetica', '', 6);  
                $pdf->MultiCell($rectWidth - 4, 5, strip_tags($content), 0, 'L', 0, 1);  
            }

            // Limpiar cualquier salida previa
            ob_end_clean();

            // Generar el PDF y forzar la descarga
            return $pdf->Output('etiquetas_' . $ordenFabricacion->OrdenFabricacion . '.pdf', 'D');
        } catch (\Exception $e) {
            // Registrar el error y devolver mensaje de error
            Log::error('Error al generar PDF: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }*/
}






