<?php
namespace App\Http\Controllers;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\FuncionesGeneralesController;
use Illuminate\Support\Facades\Log;
use App\Models\OrdenVenta;
use App\Models\PartidasOF;
use App\Models\OrdenFabricacion;
use App\Models\PorcentajePlaneacion;
use App\Models\FechasBuffer;
use App\Models\RegistrosBuffer;
use App\Models\Partidasof_Areas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Linea;
use App\Models\Permission;
use App\Models\User;
use App\Models\Comentarios;
class PlaneacionController extends Controller
{
    protected $funcionesGenerales;
    protected $CantidadParaEscanner;//Variable para ver apartir de cuantas piezas es Escaneado o masivo
    public function __construct(FuncionesGeneralesController $funcionesGenerales){
        $this->funcionesGenerales = $funcionesGenerales;
        $this->CantidadParaEscanner = 10;
    }
    public function index(){
        // Obtener el usuario autenticado
        $user = Auth::user();
        // Verificar el permiso 'Vista Planeacion'
        if ($user->hasPermission('Vista Planeacion')) {
    
            // Inicializar las fechas
            $FechaInicio = date('Ymd');
            $FechaFin = date('Ymd');
            $NumOV = "";
            $message = "";
    
            // Obtener los datos de las órdenes de venta
            $datos = $this->OrdenesVenta($FechaFin, $FechaInicio, $NumOV);
            // Determinar el estado según los datos
            $VerificarSAP=1;
            if ($datos != 0) {
                $status = empty($datos) ? "empty" : "success";
            } else {
                $datos=[];
                $VerificarSAP=0;
                $status = "empty";
            }
            $linea = Linea::where('active', 1)->get();
            // Ajustar formato de las fechas para la vista
            $FechaInicio = date('Y-m-d');
            $FechaFin = date('Y-m-d');
            //Traer a los usuarios para asignar cortes
            $UsuariosCortes =  User::whereHas('roles.permissions', function ($query) {
                    $query->where('name', 'Vista Corte');
            })
            ->where('role','O')
            ->get();
            // Retornar la vista con los datos
            return view('Planeacion.Planeacion', compact('datos', 'FechaInicio', 'FechaFin', 'status','VerificarSAP', 'linea', 'UsuariosCortes'));
    
        } else {
    
            // Redirigir a una página de error si no tiene permiso
            return redirect()->route('error.');
        }
    }
    public function PorcentajesPlaneacion(Request $request){
        $fecha = $request->fecha;
        $Linea_id = $request->Linea_id;//chris
        $PorcentajePlaneacion = PorcentajePlaneacion::where('FechaPlaneacion', $fecha)
            ->where('Linea_id', $Linea_id)//chris
            ->first();
        if (!$PorcentajePlaneacion) {
            $NumeroPersonas = 20;  
            $PiezasPorPersona = 50; 
        } else {
            $NumeroPersonas = $PorcentajePlaneacion->NumeroPersonas;
            $PiezasPorPersona = $PorcentajePlaneacion->CantidadPlaneada / max($NumeroPersonas, 1); 
        }
        $PlaneadoPorDia = Partidasof_Areas::where('FechaComienzo','>=',$fecha.' 00:00:00')
                            ->where('FechaComienzo','<=',$fecha.' 23:59:59')
                            ->where('Areas_id','18')
                            ->where('Linea_id',$Linea_id)->get()->SUM('Cantidad');
        $Linea=Linea::find($Linea_id);
        $CantidadEstimadaDia = $NumeroPersonas * $PiezasPorPersona;
        $PorcentajePlaneada = $CantidadEstimadaDia > 0 
            ? number_format($PlaneadoPorDia / $CantidadEstimadaDia * 100, 2) 
            : 0;

        $PorcentajeFaltante = number_format(100 - $PorcentajePlaneada, 2);

        return response()->json([
            'Linea_id' => $Linea_id,
            'Linea' => $Linea->Nombre,
            'PorcentajePlaneada' => $PorcentajePlaneada,
            'PorcentajeFaltante' => $PorcentajeFaltante,
            'NumeroPersonas' => $NumeroPersonas,
            'PlaneadoPorDia' => $PlaneadoPorDia,
            'CantidadEstimadaDia' => $CantidadEstimadaDia,
            'Piezasfaltantes' => max($CantidadEstimadaDia - $PlaneadoPorDia, 0),
            'Fecha_Grafica' => Carbon::parse($fecha)->translatedFormat('d \d\e F \d\e Y'),
        ]);
    }
    public function GuardarParametrosPorcentajes(Request $request){
        $CantidadPersona=$request->CantidadPersona;
        $Piezaspersona=$request->Piezaspersona;
        $Fecha=$request->Fecha;
        $Linea_id = $request->Linea; //chris
        $linea = Linea::where('id', $Linea_id)->where('active',1)->first();//chris
        if($linea == "" OR $linea == null){
            return 0;
        }
        $registro=PorcentajePlaneacion::where('FechaPlaneacion',$Fecha)
                                       ->where('Linea_id',$Linea_id)->first();
        if($registro=="" OR $registro==null){
            $Linea_id = $Linea_id;
            $NumeroPersonas=$CantidadPersona;
            $PiezasPorPersona=$Piezaspersona;
            $CantidadEstimadaDia=$NumeroPersonas*$PiezasPorPersona;
            $PorcentajePlaneacion = new PorcentajePlaneacion();
            $PorcentajePlaneacion->FechaPlaneacion = $Fecha;
            $PorcentajePlaneacion->CantidadPlaneada = $CantidadEstimadaDia; 
            $PorcentajePlaneacion->NumeroPersonas = $NumeroPersonas;
            $PorcentajePlaneacion->Linea_id = $Linea_id;//christian
            $PorcentajePlaneacion->save();
        }else{
            $registro->FechaPlaneacion=$Fecha;
            $registro->NumeroPersonas=$CantidadPersona;
            $registro->CantidadPlaneada=$Piezaspersona*$CantidadPersona;
            $registro->Linea_id = $Linea_id;//christian
            $registro->save();
        }
        return Carbon::parse($Fecha)->translatedFormat('d \d\e F \d\e Y');
    }
    public function PartidasOF(Request $request){
        //datos para la consulta
        $schema = 'HN_OPTRONICS';
        $ordenventa = $request->input('docNum');
        $cliente = $request->input('cliente');
        if (empty($ordenventa)) {
            return response()->json([
                'status' => 'error',
                'message' => 'El número de orden no fue proporcionado.'
            ]);
        }
        //Consulta a SAP para traer las partidas de una OV
        $sql = "SELECT T1.\"ItemCode\" AS \"Articulo\", 
                    T1.\"Dscription\" AS \"Descripcion\", 
                    ROUND(T2.\"PlannedQty\", 0) AS \"Cantidad OF\", 
                    T2.\"DueDate\" AS \"Fecha entrega OF\", 
                    T1.\"PoTrgNum\" AS \"Orden de F.\" ,
                    T1.\"LineNum\" AS \"LineNum\",
                    T2.\"CardCode\" AS \"Cliente\",
                    CASE T2.\"Status\"
                    	WHEN 'P' THEN 'Planeado'
                    	WHEN 'R' THEN 'Liberado'
                    	WHEN 'L' THEN 'Cerrado'
                    	WHEN 'C' THEN 'Cancelado'
                    END \"Estatus\"
                FROM {$schema}.\"ORDR\" T0
                INNER JOIN {$schema}.\"RDR1\" T1 ON T0.\"DocEntry\" = T1.\"DocEntry\"
                LEFT JOIN {$schema}.\"OWOR\" T2 ON T1.\"DocEntry\" = T2.\"OriginAbs\" AND T2.\"Status\" NOT IN ('C') AND T2.\"ItemCode\" = T1.\"ItemCode\"
                WHERE T0.\"DocNum\" = '{$ordenventa}'
                AND  (T2.\"Status\" = 'R' OR T2.\"Status\" = 'P')
                ORDER BY T1.\"PoTrgNum\""; 
                //ORDER BY T1.\"VisOrder\"";
        //Ejecucion de la consulta
        $partidas = $this->funcionesGenerales->ejecutarConsulta($sql);
        
        if ($partidas === false) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al ejecutar la consulta. Verifique los parámetros.'
            ]);
        }
        if (empty($partidas)) {
            //Log::warning("No se encontraron partidas para la orden: $ordenventa");
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontraron partidas para esta orden.'
            ]);
        }
        $html = '<div class="table-responsive table-partidas" style="width:100%;">';
        $html .= '<table class="table table-sm fs--1 mb-0" id="table_OF'.$ordenventa.'" style="width:100%;">';
        $html .= '<thead>
                    <tr>
                        <th class="text-center">Todo <input type="checkbox" id="selectAll'.$ordenventa.'" onclick="SeleccionaFilas(this)"></th>
                        <th>Orden Fabricación</th>
                        <th>Artículo</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Fecha entrega</th>
                        <th style="display:none;"></th>
                        <th style="display:none;"></th>
                        <th style="display:none;">Escáner</th>
                        <th>Requiere corte</th>
                        <th style="display:none;"></th>
                        <th style="display:none;"></th>
                    </tr>
                </thead>
                <tbody class="list">';
        $bandera_tabla_mostrar=0;
        foreach ($partidas as $index => $partida) {
            //Valida que la Orden de Fabricacion no se encuentre registrada
            $respuesta=$this->comprobar_existe_partida($ordenventa,$partida['Orden de F.']);
            //echo "Partida ".$partida['Orden de F.']." ".$respuesta." "."<br>";
            if($respuesta==0){
                $bandera_tabla_mostrar=1;
                $ordenFab = trim($partida['Orden de F.']); 
                $cantidadOF = is_numeric($partida['Cantidad OF']) 
                    ? number_format($partida['Cantidad OF'], 0, '.', '') 
                    : 'No disponible'; 
        
                $fechaEntrega = !empty($partida['Fecha entrega OF']) 
                    ? \Carbon\Carbon::parse($partida['Fecha entrega OF'])->format('d-m-Y') 
                    : 'No disponible'; 
                //$html .= '<tr id="row-' . $index . '" draggable="true" ondragstart="drag(event)" data-orden-fab="' . trim($partida['Orden de F.']) . '" data-articulo="' . $partida['Articulo'] . '" data-descripcion="' . $partida['Descripcion'] . '" data-cantidad="' . $cantidadOF . '" data-fecha-entrega="' . $fechaEntrega . '">
                $html .='<tr id="row' .$ordenventa.$index . '" draggable="true" ondragstart="drag(event)" class="draggable" >
                        <td class="text-center">';
                if($partida['Orden de F.'] != ""){
                    $html .='<input type="checkbox" class="selectAll'.$ordenventa.'rowCheckbox" onclick="SeleccionarFila(event, this)">';
                }
                $html .='</td>
                            <td>' . ($partida['Orden de F.'] ?? 'No disponible') . '</td>
                            <td>' . ($partida['Articulo'] ?? 'No disponible') . '</td>
                            <td>' . ($partida['Descripcion'] ?? 'No disponible') . '</td>
                            <td class="text-center">' . ($cantidadOF ?: 'No disponible') . '</td>
                            <td>' . ($fechaEntrega ?: 'No disponible') . '</td>
                            <td style="display:none;">' . $ordenventa. '</td>
                            <td style="display:none;">' . $cliente. '</td>
                            <td class="text-center" style="display:none;"><input type="checkbox" class="Escaner'.$ordenventa.'"></td>
                            <td style="display:none;">' . $partida['LineNum']. '</td>
                            <td class="text-center"><input type="checkbox" class="Corte'.$ordenventa.'"></td>
                            <td class="text-center"><input type="hidden" value="'.$this->funcionesGenerales->encrypt($partida['Cliente'] ?? '000').'"></td>
                        </tr>';
            }
        }
        if($bandera_tabla_mostrar==0){
            return response()->json([
                'status' => 'success',
                'message' => '<p class="text-center" style="font-size:12px;">Todas las &Oacute;rdenes de fabricaci&oacute;n ya se encuentran asignadas</p>'
            ]);    
        }
            $html .= '</tbody></table></div>';
            return response()->json([
                'status' => 'success',
                'message' => $html
            ]);
    }
    public function PlaneacionFF(Request $request){
        $FechaInicio=$request->input('startDate');
        $FechaInicio_consulta=str_replace("-","",$FechaInicio);
        $FechaFin=$request->input('endDate');
        $FechaFin_consulta=str_replace("-","",$FechaFin);
        $NumOV="";
        $tablaOrdenes="";
            $datos=$this->OrdenesVenta($FechaFin_consulta,$FechaInicio_consulta,$NumOV);
            if($datos!=0){
                if(empty($datos)){
                    $status="empty";
                }else{
                    $status="success";
                    $banderaOV=0;
                    foreach ($datos as $index => $orden) {
                        if($orden['Estatus']>0){
                            $banderaOV=1;
                            $tablaOrdenes .= '<tr class="table-light" id="details' . $index . 'cerrar" style="cursor: pointer;" draggable="true" ondragstart="drag(event)">
                                            <td role="button" data-bs-toggle="collapse" data-bs-target="#details' . $index . '" aria-expanded="false" aria-controls="details' . $index . '" onclick="loadContent(\'details' . $index . '\', ' . $orden['OV'] .', `' . $orden['Cliente'] . '`)">
                                                ' . $orden['OV'] . " - " . $orden['Cliente'] . '
                                            </td>
                                        </tr>
                                        <tr id="details' . $index . '" class="collapse">
                                            <td class="table-border" id="details' . $index . 'llenar">
                                            sassasassasa
                                                <!-- Aquí se llenarán los detalles de la orden cuando el usuario haga clic -->
                                            </td>
                                            <td style="display:none"> ' . $orden['Cliente']. '</td>
                                            <td style="display:none"> ' . $orden['OV']. '</td>
                                        </tr>';
                        }
                    }
                    if($banderaOV==0){
                        $status="empty";
                    }
                }
            }else{
                $status="error";
            }
            return response()->json([
                'status' => $status,
                'data' => $tablaOrdenes,
                'fechaHoy' => $FechaInicio,
                'fechaAyer' => $FechaFin
            ]);
    }
    public function PlaneacionFOV(Request $request){
        $NumOV=$request->input('OV');
        $tablaOrdenes="";
            $datos=$this->OrdenesVenta("","",$NumOV);
            if($datos!=0){
                if(empty($datos)){
                    $status="empty";
                }else{
                    $status="success";
                    foreach ($datos as $index => $orden) {
                        if($orden['Estatus']>0){
                            $tablaOrdenes .= '<tr class="table-light" id="details' . $index . 'cerrar" style="cursor: pointer;" draggable="true" ondragstart="drag(event)" data-bs-toggle="collapse" data-bs-target="#details' . $index . '" aria-expanded="false" aria-controls="details' . $index . '">
                                            <td onclick="loadContent(\'details' . $index . '\', ' . $orden['OV'] .', `' . $orden['Cliente'] . '`)">
                                                ' . $orden['OV'] . " - " . $orden['Cliente'] . '
                                            </td>
                                        </tr>
                                        <tr id="details' . $index . '" class="collapse">
                                            <td class="table-border" id="details' . $index . 'llenar">
                                                <!-- Aquí se llenarán los detalles de la orden cuando el usuario haga clic -->
                                            </td>
                                            <td style="display:none"> ' . $orden['Cliente']. '</td>
                                            <td style="display:none"> ' . $orden['OV']. '</td>
                                        </tr>';
                        }
                    }
                }
            }else{
                $status="error";
            }
            return response()->json([
                'status' => $status,
                'data' => $tablaOrdenes,
                'NumOV' => $NumOV
            ]);
    }
    public function PartidasOFGuardar(Request $request){
        $DatosPlaneacion=json_decode($request->input('DatosPlaneacion'));
        $usuariosIMEX = ['C0875','C0912'];//Array con los codigos de Clientes IMEX
        $FechaHoy=date('Y-m-d');
        $Fechaplaneada=$DatosPlaneacion[0]->Fecha_planeada;
        if($Fechaplaneada<$FechaHoy){
            return response()->json([
                'status' => "errordate",
            ]);
        }
        $bandera="";
        $NumOV="";
        $NumOF=[];
        for($i=0;$i<count($DatosPlaneacion);$i++){
            $LLC=$this->funcionesGenerales->decrypt($DatosPlaneacion[$i]->LLC);
            if (in_array($LLC, $usuariosIMEX)) {
                $LLC=1;
            }else{$LLC=0;}
            $respuesta=0;
            $bandera_existe="";
            $respuesta=$this->comprobar_OV($DatosPlaneacion[$i]->OV);
            $NumOV=$DatosPlaneacion[$i]->OV;
            $respuestaOF= new Ordenfabricacion();
            $datosRegistrosBuffer=RegistrosBuffer:: where('OrdenVentaB','=',$DatosPlaneacion[$i]->OV)
                                    ->where(function($query) use ($DatosPlaneacion, $i) {
                                        $query->where('OrdenFabricacionB', '=', $DatosPlaneacion[$i]->OF)
                                        ->orWhere('NumeroLineaB', '=', $DatosPlaneacion[$i]->Linea);
                                    })
                                    ->first();
            if($datosRegistrosBuffer){
                $datosRegistrosBuffer->delete();
            }
            if($respuesta==0){
                $respuestaOV= new OrdenVenta();
                $respuestaOV->OrdenVenta=$DatosPlaneacion[$i]->OV;
                $respuestaOV->NombreCliente=$DatosPlaneacion[$i]->Cliente;
                $bandera=$respuestaOV->save();
                if($bandera==1){
                    $NumOF[]=$DatosPlaneacion[$i]->OF;
                    $Fecha_entrega=isset($DatosPlaneacion[$i]->Fecha_entrega)?Carbon::createFromFormat('d-m-Y', $DatosPlaneacion[$i]->Fecha_entrega)->format('Y-m-d'):null;
                    $respuestaOF->OrdenVenta_id=$respuestaOV->id;
                    $respuestaOF->OrdenFabricacion=$DatosPlaneacion[$i]->OF;
                    $respuestaOF->Articulo=$DatosPlaneacion[$i]->Articulo;
                    $respuestaOF->Descripcion=$DatosPlaneacion[$i]->Descripcion;
                    $respuestaOF->CantidadTotal=$DatosPlaneacion[$i]->Cantidad;
                    $respuestaOF->FechaEntregaSAP=$Fecha_entrega;
                    if(!($DatosPlaneacion[$i]->Corte==1)){
                        $respuestaOF->EstatusEntrega=1;
                    }else{$respuestaOF->EstatusEntrega=0;$respuestaOF->ResponsableUser_id = $DatosPlaneacion[$i]->responsablecorte;}
                    $respuestaOF->FechaEntrega=$DatosPlaneacion[$i]->Fecha_planeada;
                    if($DatosPlaneacion[$i]->Cantidad>$this->CantidadParaEscanner){
                        $respuestaOF->Escaner=0;
                    }else{
                        $respuestaOF->Escaner=1;
                    }
                    $respuestaOF->linea_id = $DatosPlaneacion[$i]->Linea;
                    $respuestaOF->Corte = $DatosPlaneacion[$i]->Corte;
                    $respuestaOF->LLC = $LLC;// 1 si 0 no
                    $respuestaOF->save();
                }
                else{
                    return response()->json([
                        'status' => "error",
                        'NumOV' => $NumOF,
                        'NumOV' => $NumOV
                    ]);
                }
            }else{
                $datos=OrdenVenta:: where('id','=',$respuesta)->first();
                $comprobar_existe_partida=$this->comprobar_existe_partida($datos->id, $DatosPlaneacion[$i]->OF);
                if($comprobar_existe_partida==0){
                    $NumOF[]=$DatosPlaneacion[$i]->OF;
                    $Fecha_entrega=isset($DatosPlaneacion[$i]->Fecha_entrega)?Carbon::createFromFormat('d-m-Y', $DatosPlaneacion[$i]->Fecha_entrega)->format('Y-m-d'):null;
                    $respuestaOF->OrdenVenta_id=$datos->id;
                    $respuestaOF->OrdenFabricacion=$DatosPlaneacion[$i]->OF;
                    $respuestaOF->Articulo=$DatosPlaneacion[$i]->Articulo;
                    $respuestaOF->Descripcion=$DatosPlaneacion[$i]->Descripcion;
                    $respuestaOF->CantidadTotal=$DatosPlaneacion[$i]->Cantidad;
                    $respuestaOF->FechaEntregaSAP=$Fecha_entrega;
                    if(!($DatosPlaneacion[$i]->Corte==1)){
                        $respuestaOF->EstatusEntrega=1;
                    }else{$respuestaOF->EstatusEntrega=0;$respuestaOF->ResponsableUser_id = $DatosPlaneacion[$i]->responsablecorte;}
                    $respuestaOF->FechaEntrega=$DatosPlaneacion[$i]->Fecha_planeada;
                    if($DatosPlaneacion[$i]->Cantidad>$this->CantidadParaEscanner){
                        $respuestaOF->Escaner=0;
                    }else{
                        $respuestaOF->Escaner=1;
                    }
                    $respuestaOF->linea_id = $DatosPlaneacion[$i]->Linea;
                    $respuestaOF->Corte = $DatosPlaneacion[$i]->Corte;
                    $respuestaOF->LLC = $LLC; // 1 si 0 no
                    $respuestaOF->save();
                }
            }
            if(!($DatosPlaneacion[$i]->Corte==1)){
                $PartidasOF = new PartidasOF();
                $PartidasOF->OrdenFabricacion_id=$respuestaOF->id;
                $PartidasOF->cantidad_partida=$respuestaOF->CantidadTotal;
                $PartidasOF->NumeroPartida=1;
                $PartidasOF->TipoPartida='N';
                $PartidasOF->FechaFabricacion=Now();
                $PartidasOF->EstatusPartidaOF=0;
                $PartidasOF->FechaComienzo=Now();
                $PartidasOF->FechaFinalizacion=Now();
                $PartidasOF->save();
                $data = [
                    'Cantidad' => $respuestaOF->CantidadTotal,
                    'TipoPartida' => 'N', // N = Normal
                    'FechaComienzo' => now(),
                    'FechaTermina' => now(),
                    'NumeroEtiqueta' => 1,
                    'Linea_id' => 1,
                    'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                ];
                //Area 2 Corte
                $PartidasOF->Areas()->attach(2, $data);
            }else{
                $PartidasOF = new PartidasOF();
                $PartidasOF->OrdenFabricacion_id=$respuestaOF->id;
                $PartidasOF->cantidad_partida=$respuestaOF->CantidadTotal;
                $PartidasOF->NumeroPartida=1;
                $PartidasOF->TipoPartida='N';
                $PartidasOF->FechaFabricacion=Now();
                $PartidasOF->EstatusPartidaOF=0;
                $PartidasOF->save();
            }
        }
        if (!empty($NumOF)) {
            return response()->json([
                'status' => "success",
                'NumOF' => $NumOF,
                'NumOV' => $NumOV
            ]);
        }else{
            return response()->json([
                'status' => "empty",
                'NumOF' => $NumOF,
                'NumOV' => $NumOV
            ]);
        }
    }
    public function PartidasOFFiltroFechas_Tabla(Request $request){
        //return $request;
        $Fecha = $request->input('fecha');
        $datos = $this->PartidasOFFiltroFechas($Fecha);
        $ResponsableUser_id = $request->ResponsableUser_id;
        if($ResponsableUser_id=="OSC"){
            $ResponsableUser_id=null;
        }
        $tabla = "";
        if (count($datos) > 0) {
            $banderaOFPlaneadas=0;
            foreach ($datos as $dato) {
                $countdatosOrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion', '=', $dato['OrdenFabricacion'])
                    ->where('ResponsableUser_id', $ResponsableUser_id)
                    ->first();
                if ($countdatosOrdenFabricacion!="") {
                    $banderaOFPlaneadas=1;
                    //$countPartidas = $countdatosOrdenFabricacion->partidasOF()->count();
                    $PartidasSinCorte=$countdatosOrdenFabricacion->partidasOF;
                    $countPartidasSinCortes=0;
                    $countPartidas=0;
                    foreach($PartidasSinCorte as $PartidasSinC){
                        $countPartidasSinCortes=$PartidasSinC->Areas()->where('Areas_id', 3)->get()->count();
                        $countPartidas=$PartidasSinC->Areas()->where('Areas_id', 2)->get()->count();
                    }
                    if ($countPartidas == 0 AND $countdatosOrdenFabricacion->Corte == 1) { 
                        $tabla .= '<tr>
                            <td class="text-center">'.$dato['OrdenVenta'].'</td>
                            <td class="text-center">'.$dato['OrdenFabricacion'].'</td>
                            <td class="text-center"><input type="checkbox" onclick="CambiarUrgencia(this,\''.$this->funcionesGenerales->encrypt($dato['ordenfabricacion_id']).'\')"';
                        if($dato['Urgencia']=='U'){
                            $tabla .=' checked ';
                        }  
                        $tabla .= '></td>
                            <td class="text-center">
                                <button type="button" onclick="RegresarOrdenFabricacion(\''.$this->funcionesGenerales->encrypt($dato['ordenfabricacion_id']).'\')" class="btn btn-sm btn-danger">
                                    <i class="fa fa-arrow-left"></i> Cancelar
                                </button>
                                <button type="button" onclick="DetallesOrdenFabricacion(\''.$this->funcionesGenerales->encrypt($dato['ordenfabricacion_id']).'\')" class="btn btn-sm btn-primary">
                                    <i class="fa fa-eye"></i> Detalles
                                </button>
                            </td>
                        </tr>';
                    }else if($countPartidasSinCortes == 0 AND $countdatosOrdenFabricacion->Corte == 0){
                        $tabla .= '<tr>
                        <td class="text-center">'.$dato['OrdenVenta'].'</td>
                        <td class="text-center">'.$dato['OrdenFabricacion'].'</td>
                        <td class="text-center"><input type="checkbox" onclick="CambiarUrgencia(this,\''.$this->funcionesGenerales->encrypt($dato['ordenfabricacion_id']).'\')"';
                        if($dato['Urgencia']=='U'){
                            $tabla .=' checked ';
                        }  
                        $tabla .= '></td>
                        <td class="text-center">
                            <button type="button" onclick="RegresarOrdenFabricacion(\''.$this->funcionesGenerales->encrypt($dato['ordenfabricacion_id']).'\')" class="btn btn-sm btn-danger">
                                <i class="fa fa-arrow-left"></i> Cancelar
                            </button>
                            <button type="button" onclick="DetallesOrdenFabricacion(\''.$this->funcionesGenerales->encrypt($dato['ordenfabricacion_id']).'\')" class="btn btn-sm btn-primary">
                                <i class="fa fa-eye"></i> Detalles
                            </button>
                        </td>
                    </tr>';
                    } else {
                        $tabla .= '<tr>
                            <td class="text-center">'.$dato['OrdenVenta'].'</td>
                            <td class="text-center">'.$dato['OrdenFabricacion'].'</td>
                            <td class="text-center"><input type="checkbox" onclick="CambiarUrgencia(this,\''.$this->funcionesGenerales->encrypt($dato['ordenfabricacion_id']).'\')"';
                            if($dato['Urgencia']=='U'){
                                $tabla .=' checked ';
                            }  
                            $tabla .= '></td>
                            <td class="text-center"></td>
                        </tr>';
                    }
                }
            }
            if($banderaOFPlaneadas==0){
                return response()->json([
                    'status' => "empty",
                    'tabla' => '<tr><td colspan="100%" align="center">No existen registros</td></tr>'
                ]);
            }
            return response()->json([
                'status' => "success",
                'tabla' => $tabla
            ]);
        } else {
            return response()->json([
                'status' => "empty",
                'tabla' => '<tr><td colspan="100%" align="center">No existen registros</td></tr>'
            ]);
        }
    }
    public function LlenarTablaVencidasOV(){
        $datos=RegistrosBuffer::select('OrdenVentaB')
                                    ->groupBy('OrdenVentaB')  // Agrupa por 'OrdenVentaB'
                                    ->get();
        $tablaOrdenes="";
        if($datos->count()==0){
            $status="empty";
            $tablaOrdenes='<tr><td colspan="100%"" align="center">No existen Partidas por planear</td></tr>';
        }else{
            $status="success";
            foreach ($datos as $index => $orden) {
                $datosSAP=$this->OrdenesVenta("","", $orden['OrdenVentaB']);
                $tablaOrdenes .= '<tr class="table-light" id="detailsVencidos' . $index . 'cerrar" style="cursor: pointer;" draggable="true" ondragstart="drag(event)" >
                                    <td  role="button" data-bs-toggle="collapse" data-bs-target="#detailsVencidos' . $index . '" onclick="loadContentVencidas(\'detailsVencidos' . $index . '\', ' . $datosSAP[0]['OV'] .', `' . $datosSAP[0]['Cliente'] . '`)">
                                        ' . $datosSAP[0]['OV'] . " - " . $datosSAP[0]['Cliente'] . '
                                    </td>
                                </tr>
                                <tr id="detailsVencidos' . $index . '" class="collapse">
                                    <td class="table-border" id="detailsVencidos' . $index . 'llenar">
                                        <!-- Aquí se llenarán los detalles de la orden cuando el usuario haga clic -->
                                    </td>
                                    <td style="display:none"> ' . $datosSAP[0]['Cliente']. '</td>
                                    <td style="display:none"> ' . $datosSAP[0]['OV']. '</td>
                                </tr>';
            }
        }
        return response()->json([
            'status' => $status,
            'data' => $tablaOrdenes
        ]);;
    }
    //Funcion para eliminar Orden de Fabricacion planeadas
    public function PartidasOFRegresar(Request $request){
        try{
            $NumOF_id=$this->funcionesGenerales->decrypt($request->input('NumOF'));
            $OF = OrdenFabricacion::where('id','=',$NumOF_id)->first();
            if($OF->Corte==1){
            //$numero_partidas=$OF->PartidasOF->count();
            foreach($OF->PartidasOF as $PartidasSinC){
                $numero_partidas=$PartidasSinC->Areas()->where('Areas_id', 3)->get()->count();
            }
                if($numero_partidas>0){
                    return response()->json([
                        'status' => "iniciado",
                        'OF' => ""
                    ]);
                }
            }else{
                $countPartidasSinCortes=0;
                foreach($OF->PartidasOF as $PartidasSinC){
                    $countPartidasSinCortes=$PartidasSinC->Areas()->where('Areas_id', 4)->get()->count();
                }
                if($countPartidasSinCortes>0){
                    return response()->json([
                        'status' => "iniciado",
                        'OF' => ""
                    ]);
                }
            }
            //Comprobar si ya esta iniciada
            if (!empty($OF)) {
                $OV=$OF->ordenVenta()->get();
                $OV=$OV[0]->OrdenVenta;
                $datos_partida=$this->FiltroWhereOrdenFabicacion($OV,$OF->OrdenFabricacion);
                $datos_partida=isset($datos_partida[0]['LineNum'])?$datos_partida[0]['LineNum']:1999;
                $RegistrosBuffer = new RegistrosBuffer();
                $RegistrosBuffer->FechasBuffer_id=1;
                $RegistrosBuffer->OrdenVentaB=$OV;
                $RegistrosBuffer->OrdenFabricacionB=$OF->OrdenFabricacion;
                $RegistrosBuffer->NumeroLineaB=$datos_partida;
                $OF_OrdenFabricacion=$OF->OrdenFabricacion;
                $RegistrosBuffer->save();
                $bandera_borrar=$OF->delete();
                if ($bandera_borrar) {
                    return response()->json([
                        'status' => "success",
                        'OF' => $OF_OrdenFabricacion
                    ]);
                } else {
                    return response()->json([
                        'status' => "error",
                        'OF' => ""
                    ]);
                }
            }else{ 
                return response()->json([
                    'status' => "empty",
                    'OF' => ""
                ]);
            }
        }catch(Exception $e){
            return response()->json([
                'status' => "error",
                'OF' => ""
            ]);
        }
    }
     //Funcion para cambiar estutus de si se escanea o no
    public function CambiarEstatusEscaner(Request $request){
        try{
            $NumOF_id=$this->funcionesGenerales->decrypt($request['Id']);
            $OF = OrdenFabricacion::where('id','=',$NumOF_id)->first();
            $escaner=$request['Escanear'];
            if($escaner=="true"){
                $OF->Escaner=1;
                $OF->save();
                return response()->json([
                    'status' => "success",
                    'valor' => "true"
                ]);
            }else{
                $OF->Escaner=0;
                $OF->save();
                return response()->json([
                    'status' => "success",
                    'valor' => "false"
                ]);
            }
        }catch(Exception $e){
            return response()->json([
                'status' => "error",
            ]);
        }
    }
    //Funcion para ver las Ordenes de venta de  fecha inicio a fecha fin y por numero de OV
    public function OrdenesVenta($FechaInicio,$FechaFin,$NumOV){
        $schema = 'HN_OPTRONICS';
        $where="";
        $datos="";
        if($NumOV==""){
            $where='T0."DocDate" BETWEEN \'' . $FechaFin . '\' AND \'' . $FechaInicio . '\'';
        }else{
            $where = 'T0."DocNum" LIKE \'%' . $NumOV . '%\'';
        }
        $sql = 'SELECT T0."DocNum" AS "OV", T0."CardName" AS "Cliente", T0."DocDate" AS "Fecha", 
                T0."DocStatus" AS "Estado", T0."DocTotal" AS "Total" FROM ' . $schema . '.ORDR T0 
                WHERE '.$where.'ORDER BY T0."DocNum"';
        try {
            $datos = $this->funcionesGenerales->ejecutarConsulta($sql);
        } catch (\Exception $e) {
            return $datos=0;
        }
        for($i=0;$i<count($datos);$i++){
            $num_partidas=$this->OrdenFabricacion($datos[$i]['OV']);
            $datos[$i]['Estatus']=$num_partidas;
        }
        return $datos;
    }
    public function OrdenFabricacion($ordenventa){
        $schema = 'HN_OPTRONICS';
        //Consulta a SAP para traer las partidas de una OV
        $sql = "SELECT T1.\"ItemCode\" AS \"Articulo\", 
                    T1.\"Dscription\" AS \"Descripcion\", 
                    ROUND(T2.\"PlannedQty\", 0) AS \"Cantidad OF\", 
                    T2.\"DueDate\" AS \"Fecha entrega OF\", 
                    T1.\"PoTrgNum\" AS \"Orden de F.\" ,
                    T1.\"LineNum\" AS \"LineNum\",
                    CASE T2.\"Status\"
                    	WHEN 'P' THEN 'Planeado'
                    	WHEN 'R' THEN 'Liberado'
                    	WHEN 'L' THEN 'Cerrado'
                    	WHEN 'C' THEN 'Cancelado'
                    END \"Estatus\"
                    FROM {$schema}.\"ORDR\" T0
                    INNER JOIN {$schema}.\"RDR1\" T1 ON T0.\"DocEntry\" = T1.\"DocEntry\"
                    LEFT JOIN {$schema}.\"OWOR\" T2 ON T1.\"DocEntry\" = T2.\"OriginAbs\" AND T2.\"Status\" NOT IN ('C') AND T2.\"ItemCode\" = T1.\"ItemCode\"
                    WHERE T0.\"DocNum\" = '{$ordenventa}'
                    AND  (T2.\"Status\" = 'R' OR T2.\"Status\" = 'P')
                    ORDER BY T1.\"PoTrgNum\""; 
                //ORDER BY T1.\"VisOrder\"";
        //Ejecucion de la consulta
        $partidas = $this->funcionesGenerales->ejecutarConsulta($sql);
        $partidasOF=OrdenVenta::where('OrdenVenta','=',$ordenventa)->first();
        if($partidasOF==null || $partidasOF==""){
            $countpartidas=0;
        }else{
            $countpartidas=$partidasOF->ordenesFabricacions()->get()->count();
        }
        return count($partidas)-$countpartidas;
    }
    //Funcion para comprobar si existe una Orden de Fabricacion de una OV
    public function comprobar_existe_partida($OrdenVenta, $Ordenfabricacion){
        $datos=OrdenVenta:: where('OrdenVenta','=',$OrdenVenta)->first();
        if($datos){
            $datos=OrdenFabricacion::where('OrdenVenta_id','=',$datos->id)
            ->where('OrdenFabricacion','=',$Ordenfabricacion)                        
            ->count();
        }else{
            $datos=0;
        }
        return $datos;
    }
    //Funcion para comprobar si existe una OV
    public function comprobar_OV($DocNumOv){
        $datos=OrdenVenta::where('OrdenVenta','=',$DocNumOv)->first();
        if(!$datos){
            $datos=0;
        }else{
            $datos=$datos->id;
        }
        return $datos;
    }
    //Funcion para retornar las partidas con la OF
    public function PartidasOF_array($OV){
        //datos para la consulta
        $schema = 'HN_OPTRONICS';
        $ordenventa = $OV;
        //Consulta a SAP para traer las partidas de una OV
        $sql = "SELECT T1.\"ItemCode\" AS \"Articulo\", 
        T1.\"Dscription\" AS \"Descripcion\", 
        ROUND(T2.\"PlannedQty\", 0) AS \"Cantidad OF\", 
        T2.\"DueDate\" AS \"Fecha entrega OF\", 
        T1.\"PoTrgNum\" AS \"Orden de F.\" ,
        T1.\"LineNum\" AS \"LineNum\",
        CASE T2.\"Status\"
            WHEN 'P' THEN 'Planeado'
            WHEN 'R' THEN 'Liberado'
            WHEN 'L' THEN 'Cerrado'
            WHEN 'C' THEN 'Cancelado'
        END \"Estatus\"
        FROM {$schema}.\"ORDR\" T0
        INNER JOIN {$schema}.\"RDR1\" T1 ON T0.\"DocEntry\" = T1.\"DocEntry\"
        LEFT JOIN {$schema}.\"OWOR\" T2 ON T1.\"DocEntry\" = T2.\"OriginAbs\" AND T2.\"Status\" NOT IN ('C') AND T2.\"ItemCode\" = T1.\"ItemCode\"
        WHERE T0.\"DocNum\" = '{$ordenventa}'
        AND  T2.\"Status\" = 'R'
        ORDER BY T1.\"PoTrgNum\"";  
                //ORDER BY T1.\"VisOrder\"";
        //Ejecucion de la consulta
        $partidas = $this->funcionesGenerales->ejecutarConsulta($sql);
        return $partidas;
    }
    //Funcion para filtrar partidas de Orden de fabricacion por fecha
    public function PartidasOFFiltroFechas($Fecha){
        $datos=OrdenFabricacion::join('ordenventa', 'ordenfabricacion.OrdenVenta_id', '=', 'ordenventa.id')
                                ->where('FechaEntrega','=',$Fecha)
                                ->select('ordenfabricacion.id as ordenfabricacion_id','ordenfabricacion.Urgencia as Urgencia', 'ordenventa.id as ordenventa_id','OrdenVenta','OrdenFabricacion') 
                                ->orderBy('OrdenVenta', 'asc') // Orden descendente
                                ->get();
        return $datos->toArray();
    }
    //Funcion para ver detalles
    public function PartidasOF_Detalles(Request $request){
        $NumOF_id=$this->funcionesGenerales->decrypt($request->input('NumOF'));
        $datos=OrdenFabricacion:: join('ordenventa', 'ordenfabricacion.OrdenVenta_id', '=', 'ordenventa.id')
                                ->select(
                                    'ordenfabricacion.id as ordenfabricacionid', 
                                    'ordenfabricacion.OrdenVenta_id', 
                                    'ordenventa.OrdenVenta', 
                                    'ordenventa.NombreCliente', 
                                    'ordenfabricacion.OrdenFabricacion', 
                                    'ordenfabricacion.Articulo', 
                                    'ordenfabricacion.Descripcion', 
                                    'ordenfabricacion.CantidadTotal', 
                                    'ordenfabricacion.FechaEntrega', 
                                    'ordenfabricacion.FechaEntregaSAP', 
                                    'ordenfabricacion.Escaner'  // Aquí se selecciona la columna Escaner para el checkbox
                                )
                                ->where('ordenfabricacion.id','=',$NumOF_id)->first();
        if($datos){
            $cadena='<table class="table table-sm fs--1 mb-0" style="width:100%">
                        <thead>
                        </thead>
                        <tbody>
                            <tr>
                                <th class="table-active">Orden de Venta</th>
                                <td class="text-center">'.$datos->OrdenVenta.'</td>
                            </tr>
                            <tr>
                                <th class="table-active">Cliente</th>
                                <td class="text-center">'.$datos->NombreCliente.'</td>
                            </tr>
                            <tr>
                                <th class="table-active">Orden de Fabricación</th>
                                <td class="text-center">'.$datos->OrdenFabricacion.'</td>
                            </tr>
                            <tr>
                                <th class="table-active">Articulo</th>
                                <td class="text-center">'.$datos->Articulo.'</td>
                            </tr>
                            <tr>
                                <th class="table-active">Descripción</th>
                                <td class="text-center">'.$datos->Descripcion.'</td>
                            </tr>

                            <tr>
                                <th class="table-active">Cantidad Total</th>
                                <td class="text-center">'.$datos->CantidadTotal.'</td>
                            </tr>
                            <tr>
                                <th class="table-active">Fecha Planeación</th>
                                <td class="text-center">
                                    <div class="row d-flex justify-content-center">
                                        <div class=" col-5 d-flex justify-content-center"">
                                            <div class="input-group text-center">
                                                    <input type="date" class="form-control form-control-sm " disabled id="fechaSeleccionada_'.$datos->OrdenFabricacion.'" value="'.$datos->FechaEntrega.'">
                                                    <button id="btnEditar_'.$datos->OrdenFabricacion.'" class="btn btn-outline-info btn-xs p-1" onclick="mostrarCalendario(\''.$datos->OrdenFabricacion.'\')"><i class="fa-solid fa-pen-to-square"></i></button>
                                                    <button id="btnGuardar_'.$datos->OrdenFabricacion.'" class="btn btn-success btn-xs p-1 d-none" onclick="guardarFecha(\''.$datos->OrdenFabricacion.'\')">Guardar</button>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-center text-danger" id="Error_'.$datos->OrdenFabricacion.'" style="display:none;">Error</p>
                                </td>
                            </tr>
                            <tr>
                                <th class="table-active">Fecha Entrega</th>
                                <td class="text-center">'.Carbon::parse($datos->FechaEntregaSAP)->format('d/m/Y').'</td>
                            </tr>
                        </tbody></table>';
                return response()->json([
                    'status' => "success",
                    'tabla' => $cadena,
                    'OF'=>$datos->OrdenFabricacion
                ]);
        }else{
            return response()->json([
                'status' => "empty",
                'tabla' => '<p class="text-center">No existen información para esta Orden de Fabricación</p>'
            ]);
        }
    }
    // Funcion filtro por Orden de venta o Orden fabricacion
    public function PlaneacionFOFOV(Request $request){
        $FiltroOF_table2=$request->input('FiltroOF_table2');
        //return$Linea_id = $request->Linea_id;
        $datos=OrdenFabricacion::join('ordenventa', 'ordenfabricacion.OrdenVenta_id', '=', 'ordenventa.id')
        ->where('ordenfabricacion.OrdenFabricacion', 'like', '%'.$FiltroOF_table2. '%')
        ->orWhere('ordenventa.OrdenVenta', 'like', '%'.$FiltroOF_table2. '%')
        ->select('ordenfabricacion.id as ordenfabricacion_id', 'ordenventa.id as ordenventa_id','OrdenVenta','OrdenFabricacion') 
        ->orderBy('OrdenVenta', 'asc') // Orden descendente
        ->get();
        $tabla="";
        if(count($datos)>0){
            for ($i=0; $i < count($datos); $i++) { 
                $countdatosOrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion', '=', $datos[$i]['OrdenFabricacion'])->first();
                if ($countdatosOrdenFabricacion) {
                    //$countPartidas = $countdatosOrdenFabricacion->partidasOF()->count();
                    $PartidasSinCorte=$countdatosOrdenFabricacion->partidasOF;
                    $countPartidasSinCortes=0;
                    $countPartidas=0;
                    foreach($PartidasSinCorte as $PartidasSinC){
                        $countPartidasSinCortes=$PartidasSinC->Areas()->where('Areas_id', 3)->get()->count();
                        $countPartidas=$PartidasSinC->Areas()->where('Areas_id', 2)->get()->count();
                    }
                    if ($countPartidas == 0 AND $countdatosOrdenFabricacion->Corte == 1) { 
                        $tabla .= '<tr>
                            <td class="text-center">'.$datos[$i]['OrdenVenta'].'</td>
                            <td class="text-center">'.$datos[$i]['OrdenFabricacion'].'</td> 
                            <td class="text-center"><input type="checkbox" onclick="CambiarUrgencia(this,\''.$this->funcionesGenerales->encrypt($datos[$i]['ordenfabricacion_id']).'\')"';
                        if($datos[$i]['Urgencia']=='U'){
                            $tabla .=' checked ';
                        }  
                        $tabla .='></td>
                            <td class="text-center">
                                <button type="button" onclick="RegresarOrdenFabricacion(\''.$this->funcionesGenerales->encrypt($datos[$i]['ordenfabricacion_id']).'\')" class="btn btn-sm btn-danger">
                                    <i class="fa fa-arrow-left"></i> Cancelar
                                </button>
                                <button type="button" onclick="DetallesOrdenFabricacion(\''.$this->funcionesGenerales->encrypt($datos[$i]['ordenfabricacion_id']).'\')" class="btn btn-sm btn-primary">
                                    <i class="fa fa-eye"></i> Detalles
                                </button>
                            </td>
                        </tr>';
                    }else if($countPartidasSinCortes == 0 AND $countdatosOrdenFabricacion->Corte == 0){
                        $tabla .= '<tr>
                        <td class="text-center">'.$datos[$i]['OrdenVenta'].'</td>
                        <td class="text-center">'.$datos[$i]['OrdenFabricacion'].'</td>
                        <td class="text-center"><input type="checkbox" onclick="CambiarUrgencia(this,\''.$this->funcionesGenerales->encrypt($datos[$i]['ordenfabricacion_id']).'\')"';
                        if($datos[$i]['Urgencia']=='U'){
                            $tabla .=' checked ';
                        }  
                        $tabla .= '></td>
                        <td class="text-center">
                            <button type="button" onclick="RegresarOrdenFabricacion(\''.$this->funcionesGenerales->encrypt($datos[$i]['ordenfabricacion_id']).'\')" class="btn btn-sm btn-danger">
                                <i class="fa fa-arrow-left"></i> Cancelar
                            </button>
                            <button type="button" onclick="DetallesOrdenFabricacion(\''.$this->funcionesGenerales->encrypt($datos[$i]['ordenfabricacion_id']).'\')" class="btn btn-sm btn-primary">
                                <i class="fa fa-eye"></i> Detalles
                            </button>
                        </td>
                    </tr>';
                    } else {
                        $tabla .= '<tr>
                            <td class="text-center">'.$datos[$i]['OrdenVenta'].'</td>
                            <td class="text-center">'.$datos[$i]['OrdenFabricacion'].'</td>
                            <td class="text-center"><input type="checkbox" onclick="CambiarUrgencia(this,\''.$this->funcionesGenerales->encrypt($datos[$i]['ordenfabricacion_id']).'\')"';
                            if($datos[$i]['Urgencia']=='U'){
                                $tabla .=' checked ';
                            }  
                            $tabla .= '></td>
                            <td class="text-center"></td>
                        </tr>';
                    }
                }
                /*$tabla.='<tr>
                            <td class="text-center">'.$datos[$i]['OrdenVenta'].'</td>
                            <td class="text-center">'.$datos[$i]['OrdenFabricacion'].'</td>
                            <td class="text-center">'.'<button type="button" onclick="RegresarOrdenFabricacion(\''.$this->funcionesGenerales->encrypt($datos[$i]['ordenfabricacion_id']).'\')" <a class="btn btn-sm btn-danger"><i class="fa fa-arrow-left"></i>Cancelar</button>
                                <button type="button" onclick="DetallesOrdenFabricacion(\''.$this->funcionesGenerales->encrypt($datos[$i]['ordenfabricacion_id']).'\')" class="btn btn-sm btn-primary "><i class="fa fa-eye"></i> Detalles</button>'.'
                             </td>
                        </tr>';*/
            }
            return response()->json([
                'status' => "success",
                'tabla' => $tabla
            ]);
        }else{
            return response()->json([
                'status' => "empty",
                'tabla' => '<tr><td colspan="100%"" align="center">No existen registros para '.$FiltroOF_table2.'</td></tr>'
            ]);
        }
    }
    //Funcion para guardar las partidas que estan pendientes
    public function LlenarTablaBuffer(){
        try{
            $FiltroFecha = date('Y-m-d', strtotime('-2 day'));
            $FechasBuffer = new FechasBuffer();
            $FechasBuffer->FechaRegistrosPendientes=$FiltroFecha;
            $id_FechasBuffer=$FechasBuffer->save();
            $Ordenesventa_DiaAnterior=$this->OrdenesVenta($FiltroFecha,$FiltroFecha,"");
            foreach ($Ordenesventa_DiaAnterior as $index => $orden) {
                $datos_OV=$this->PartidasOF_array($orden['OV']);
                foreach ($datos_OV as $index => $ordenf) {
                    $comprobar_existe=$this->comprobar_existe_partida($orden['OV'], $ordenf['Orden de F.']);
                    if($comprobar_existe==0){
                        $RegistrosBuffer = new RegistrosBuffer();
                        $RegistrosBuffer->FechasBuffer_id=$FechasBuffer->id;
                        $RegistrosBuffer->OrdenVentaB=$orden['OV'];
                        $RegistrosBuffer->OrdenFabricacionB=isset($ordenf['Orden de F.'])?$ordenf['Orden de F.']:"";
                        $RegistrosBuffer->NumeroLineaB=$ordenf['LineNum'];
                        $RegistrosBuffer->save();
                    }
                }
            }
        }catch(\Exception $e){
            return "Ocurrio un error ".$e." , No se pudieron guardar los datos::".$FiltroFecha;
        }
        return "Datos guardados correctamente::".$FiltroFecha;
    }
    //Funcion Where para traer una Orden de fabricacion mandandole OV y OF
    public function FiltroWhereOrdenFabicacion($ordenventa,$ordenfabricacion){
        //datos para la consulta
        $schema = 'HN_OPTRONICS';
        if (empty($ordenventa)) {
            return response()->json([
                'status' => 'error',
                'message' => 'El número de orden no fue proporcionado.'
            ]);
        }
        //Consulta a SAP para traer las partidas de una OV
        $sql = "SELECT T1.\"ItemCode\" AS \"Articulo\", 
                    T1.\"Dscription\" AS \"Descripcion\", 
                    ROUND(T2.\"PlannedQty\", 0) AS \"Cantidad OF\", 
                    T2.\"DueDate\" AS \"Fecha entrega OF\", 
                    T1.\"PoTrgNum\" AS \"Orden de F.\" ,
                    T1.\"LineNum\" AS \"LineNum\",
                    CASE T2.\"Status\"
                    	WHEN 'P' THEN 'Planeado'
                    	WHEN 'R' THEN 'Liberado'
                    	WHEN 'L' THEN 'Cerrado'
                    	WHEN 'C' THEN 'Cancelado'
                    END \"Estatus\"
                    FROM {$schema}.\"ORDR\" T0
                    INNER JOIN {$schema}.\"RDR1\" T1 ON T0.\"DocEntry\" = T1.\"DocEntry\"
                    LEFT JOIN {$schema}.\"OWOR\" T2 ON T1.\"DocEntry\" = T2.\"OriginAbs\" AND T2.\"Status\" NOT IN ('C') AND T2.\"ItemCode\" = T1.\"ItemCode\"
                    WHERE T0.\"DocNum\" = '{$ordenventa}'
                    AND  T2.\"Status\" = 'R'
                    ORDER BY T1.\"PoTrgNum\""; 
                //ORDER BY T1.\"VisOrder\"";
        //Ejecucion de la consulta
        $partidas = $this->funcionesGenerales->ejecutarConsulta($sql);
        return $partidas;
    }
    //Llenar la fecha Planeacion
    public function PlaneacionDiaPorcentajePlaneacion(){
        $fecha=date('Y-m-d');
        $registro=PorcentajePlaneacion::where('FechaPlaneacion',$fecha)->first();
        if($registro=="" OR $registro==null){
            $NumeroPersonas=20;
            $PiezasPorPersona=50;
            $CantidadEstimadaDia=$NumeroPersonas*$PiezasPorPersona;
            $PorcentajePlaneacion = new PorcentajePlaneacion();
            $PorcentajePlaneacion->FechaPlaneacion = $fecha;
            $PorcentajePlaneacion->CantidadPlaneada = $CantidadEstimadaDia; 
            $PorcentajePlaneacion->NumeroPersonas = $NumeroPersonas;
            $PorcentajePlaneacion->save();
        }
        return "Datos guardados correctamente::".$fecha;
    }
    public function ActualizarFechaPlaneacion(Request $request){
        try {
            $Comentario = $request->input('comentario');
            $ordenFabricacion = $request->input('OrdenFabricacion');
            $fechaEntrega = $request->input('fecha_entrega');
            // Validación básica
            if (!$ordenFabricacion || !$fechaEntrega) {
                return response()->json(['message' => 'Datos incompletos'], 400);
            }
            // Convertir la fecha de entrega a un objeto Carbon
            $fechaEntregaCarbon = Carbon::parse($fechaEntrega);
            $fechaActual = Carbon::today();
    
            // Validar que la fecha de entrega no sea menor a la fecha actual
            if ($fechaEntregaCarbon->lt($fechaActual)) {
                return response()->json(['message' => 'La fecha de entrega no puede ser menor a la fecha actual'], 400);
            }
            // Buscar la orden en la base de datos
            $orden = ordenfabricacion::where('OrdenFabricacion', $ordenFabricacion)->first();
            if (!$orden) {
                return response()->json(['message' => 'Orden no encontrada'], 404);
            }
            // Actualizar la fecha de entrega
            $orden->FechaEntrega = $fechaEntrega;
            $orden->save();
            $Comentarios = new Comentarios();
            $Comentarios->OrdenFabricacion_id = $ordenFabricacion;
            $Comentarios->Partida_id = 1;
            $Comentarios->Areas_id = 1;
            $Comentarios->Usuario_id =  $this->funcionesGenerales->InfoUsuario();
            $Comentarios->Fecha =  now();
            $Comentarios->Comentario =  "Cambio de fecha de ".$orden->FechaEntrega." a ".$fechaEntrega." ".$Comentario ;
            $Comentarios->save();
            return response()->json(['message' => 'Fecha actualizada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    public function CambiarEstatusUrgencia(Request $request){
        try{
            $NumOF_id=$this->funcionesGenerales->decrypt($request['Id']);
            $OF = OrdenFabricacion::where('id','=',$NumOF_id)->first();
            $escaner=$request->Urgente;
            if($escaner=="true"){
                $OF->Urgencia='U';
                $OF->save();
                return response()->json([
                    'status' => "success",
                    'valor' => "true"
                ]);
            }else{
                $OF->Urgencia='N';
                $OF->save();
                return response()->json([
                    'status' => "success",
                    'valor' => "false"
                ]);
            }
        }catch(Exception $e){
            return response()->json([
                'status' => "error",
            ]);
        }
    }
    
    
}    