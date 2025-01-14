<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\FuncionesGeneralesController;
use App\Models\OrdenFabricacion;
use App\Models\PartidasOF;
use App\Models\Partidas;

class AreasController extends Controller
{
    protected $funcionesGenerales;
    public function __construct(FuncionesGeneralesController $funcionesGenerales)
    {
        $this->funcionesGenerales = $funcionesGenerales;
    }
    //Area 3 Suministro
    public function Suministro(){
        $Area=$this->funcionesGenerales->encrypt(3);
        return view('Areas.Suministro',compact('Area'));
    }
    public function SuministroBuscar(Request $request){
        if ($request->has('Confirmacion')) {
            $confirmacion=1;
        }else{
            $confirmacion=0;
        }
        $Codigo = $request->Codigo;
        $Inicio = $request->Inicio;
        $Finalizar = $request->Finalizar;
        $CodigoPartes = explode("-", $Codigo);
        $CodigoTam = count($CodigoPartes);
        $TipoEscanerrespuesta=0;
        $menu="";
        $Escaner="";
        $CantidadCompletada=0;
        $EscanerExiste=0;
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
                $CantidadTotal=$datos->CantidadTotal;
                $Escaner=$datos->Escaner;
                if($CodigoTam==3){
                    if($Escaner==1){
                        if($Inicio==1){
                            $TipoEscanerrespuesta=$this->TipoEscaner($CodigoPartes,$CodigoTam,3,$confirmacion);
                        }
                        if($Finalizar==1){
                            $TipoEscanerrespuesta=$this->TipoEscanerFinalizar($CodigoPartes,$CodigoTam,3,$confirmacion);
                        }
                    }else if($Escaner==0){
                        $TipoManualrespuesta=$datos[0]->partidasOF()->where('id','=',$CodigoPartes[1])->first();
                        if(!($TipoManualrespuesta=="" || $TipoManualrespuesta==null)){
                            $EscanerExiste = 1;
                        }else{
                            $EscanerExiste = 0;
                        }
                    }
                }
                //foreach( $datos as $ordenFabricacion){
                    foreach( $datos->partidasOF()->orderBy('id', 'desc')->get() as $PartidasordenFabricacion){
                        foreach( $PartidasordenFabricacion->Partidas()->orderBy('id', 'desc')->get() as $Partidas){
                            if(!($Partidas->Areas()->where('Areas_id','=',3)->first() =="" || $Partidas->Areas()->where('Areas_id','=',3)->first() == null )){
                                $menu.='<tr>
                                <td class="align-middle ps-3 NumParte">'.$Partidas->NumParte.'</td>
                                <td class="align-middle Cantidad">'.$Partidas->CantidadaPartidas.'</td>
                                <td class="align-middle Inicio">'.$Partidas->Areas()->first()->pivot->FechaComienzo.'</td>
                                <td class="align-middle Fin">'.$Partidas->Areas()->first()->pivot->FechaTermina.'</td>';
                                if($Partidas->Areas()->first()->pivot->FechaTermina==""){
                                    $menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="fw-bold">En proceso</span><span class="ms-1 fas fa-cogs"></span></div></td>';
                                    if(!($Partidas->Areas()->first()->pivot->FechaTermina=="" || $Partidas->Areas()->first()->pivot->FechaTermina==null) && $Partidas->TipoAccion==0){
                                        $CantidadCompletada+=$Partidas->CantidadaPartidas;
                                    }
                                }else{$menu.='<td class="align-middle Estatus"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Completado</span><span class="ms-1 fas fa-check"></span></div></td>';
                                    if(!($Partidas->Areas()->first()->pivot->FechaTermina=="" || $Partidas->Areas()->first()->pivot->FechaTermina==null) && $Partidas->TipoAccion==0){
                                        $CantidadCompletada+=$Partidas->CantidadaPartidas;
                                    }
                                }
                                $menu.='<td class="align-middle"><button class="btn btn-sm btn-danger">Detener</button></td>
                                    </tr>';
                            }
                        }
                    } 
                //}
                $menu='<div class="card-body">
                    <div id="ContainerTableSuministros" class="table-list">
                        <div class="row justify-content-start g-0">
                            <div class="col-auto px-3">
                            <div class="badge badge-phoenix fs--4 badge-phoenix-secondary"><span class="fw-bold">Piezas Completadas </span>'.$CantidadCompletada.'/'.$CantidadTotal.'<span class="ms-1 fas fa-stream"></span></div>
                            </div>
                        </div>
                        <div class="row justify-content-end g-0">
                            <div class="col-auto px-3"><select class="form-select form-select-sm mb-3" data-list-filter="data-list-filter">
                                <option selected="" value="">Todos</option>
                                <option value="En proceso">En proceso</option>
                                <option value="Completado">Completado</option>
                            </select>
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
                                    <th class="sort border-top" >Acci&oacute;nes</th>
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
            $datosPartidas=$datos->Partidas()->where('NumParte','=',$CodigoPartes[2])->orderBy('id', 'desc')->first();
                if($datosPartidas==null){
                    $Partidas = new Partidas();
                    $Partidas->PartidasOF_id=$datos->id;
                    $Partidas->CantidadaPartidas=1;
                    $Partidas->TipoAccion=0;
                    $Partidas->NumParte=$CodigoPartes[2];
                    $pivotData = [
                        'FechaComienzo' => $FechaHoy,
                        'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                        'Linea_id' => $this->funcionesGenerales->Linea(),
                    ];
                    if ($Partidas->save()) {
                        $Partidas->Areas()->attach($Area,$pivotData);
                        return 1;
                    } else {
                        return 0;
                    }
                }else{
                    if(!($datosPartidas->Areas()->where('Areas_id','=',3)->first() =="" || $datosPartidas->Areas()->where('Areas_id','=',3)->first() == null)){
                        if($datosPartidas->Areas()->first()->pivot->FechaTermina==null || $datosPartidas->Areas()->first()->pivot->FechaTermina==""){
                            return 2;
                        }else{
                            if ($confirmacion==1) {
                                $Partidas = new Partidas();
                                $Partidas->PartidasOF_id=$datos->id;
                                $Partidas->CantidadaPartidas=1;
                                $Partidas->TipoAccion=1;
                                $Partidas->NumParte=$CodigoPartes[2];
                                $pivotData = [
                                    'FechaComienzo' => $FechaHoy,
                                    'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                                    'Linea_id' => $this->funcionesGenerales->Linea(),
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
                            $Partidas->TipoAccion=0;
                            $pivotData = [
                                'FechaComienzo' => $FechaHoy,
                                'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                                'Linea_id' => $this->funcionesGenerales->Linea(),
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
    public function TipoNoEscaner(Request $request){ 
        $request->validate([
            'Codigo' => 'required|string|max:255',
            'Cantidad' => 'required|Integer|max:1000',
        ]);
        $Area =$this->funcionesGenerales->decrypt($request->Area);
        $Codigo = $request->Codigo;
        $Cantidad = $request->Cantidad;
        $Inicio = $request->Inicio;
        $Fin = $request->Fin;
        $ContarPartidas=0;
        $CodigoPartes = explode("-", $Codigo);
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
        if($datos->count()==0){
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
        //1 = iniciado, 0 = Finalizado, 2 = Retrabajo
        $Partidas=$partidasOF->Partidas()->where('Estatus','=','1')->get();
        foreach ($Partidas as $key => $item) {
            return $item->Areas();
            /*if($item){
                $ContarPartidas+=$item->CantidadaPartidas;
            }*/
        }
        $FechaHoy=date('Y-m-d H:i:s');
        $ContarPartidas+=$Cantidad;
        if($ContarPartidas<=$partidasOF->cantidad_partida){
                $Partidasg = new Partidas();
                $Partidasg->PartidasOF_id=$partidasOF->id;
                $Partidasg->CantidadaPartidas=$Cantidad;
                $Partidasg->TipoAccion=0;
                $Partidasg->Estatus=1;
                $Partidasg->NumParte=0;
                $Partidasg->FechaComienzo=$FechaHoy;
                if ($Partidasg->save()) {
                    $Partidasg->Areas()->attach($Area);
                    return 1;
                } else {
                    return 0;
                }
            return response()->json([
                'Inicio'=>$Inicio,
                'Fin'=>$Fin,
                'status' => "dontexist",
                'CantidadTotal' => "",
                'CantidadCompletada' => "",
                'OF' => $CodigoPartes[0],       
            ]);
        }else{
            return response()->json([
                'Inicio'=>$Inicio,
                'Fin'=>$Fin,
                'status' => "retrabajo",
                'CantidadTotal' => "",
                'CantidadCompletada' => "",
                'OF' => $CodigoPartes[0],       
            ]);
        }
        //131837-5-1
        // Validación de los datos del formulario
       
        /*if ($request->has('hola')) {
            Codigo: CodigoEscaner,
                Cantidad:Cantidad
        }
        return response()->json([
        'status' => "success",
        ]);*/
       /*return response()->json([
            'tabla' => $menu,
            'Escaner' => "",
            'status' => "NoExiste",
            'CantidadTotal' => "",
            'CantidadCompletada' => 4,
            'OF' => $CodigoPartes[0]

        ]);*/
    }
    public function TablaTipoNoEscaner($OrdenFabricacion){  
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
    public function ComprobarNumEtiqueta($CodigoPartes,$Area){
        //Return 0:No es el codigo; 1:Si es el codigo; 2:Aun no ha pasado por el area Anterior
        //Comprueba si los datos que vienen en el codigo Existen
        $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->first();
        $partidasOF=$datos->partidasOF()->where('id','=',$CodigoPartes[1])->first();
        $NumEtiqueta=0;
        if($partidasOF=="" || $partidasOF==null){
            return 0;
        }else{
            //Comprobamos que el
            if($Area==3){
                if($partidasOF->FechaFinalizacion=="" || $partidasOF->FechaFinalizacion=null){
                    return 2;
                }
            }
            $datos=$datos->partidasOF()->get();
            for($i=0;$i<$datos->count();$i++){
                $NumEtiqueta+=$datos[$i]->cantidad_partida;
                if($datos[$i]->id==$partidasOF->id){
                    break;
                }
            }
            $inicio=$NumEtiqueta-$partidasOF->cantidad_partida+1;
            $fin=$NumEtiqueta;
            if($CodigoPartes[2]>=$inicio && $CodigoPartes[2]<=$fin){
                return 1;
            }else{
                return 0;
            }
        }
    }
}
