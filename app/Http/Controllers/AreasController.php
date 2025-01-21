<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\FuncionesGeneralesController;
use App\Models\OrdenFabricacion;
use App\Models\PartidasOF;
use App\Models\Partidas;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\returnValue;

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
        $user = Auth::user();
       
    
        
        if ($user->hasPermission('Vista Suministro')) {
           
            return view('Areas.Suministro',compact('Area'));
        } else {
           
            return redirect()->away('https://assets-blog.hostgator.mx/wp-content/uploads/2018/10/paginas-de-error-hostgator.webp');
        }
        
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
                $CantidadTotal=$datos->CantidadTotal;
                $Escaner=$datos->Escaner;
                if($CodigoTam==3){
                    if($Area==3){
                        if($Escaner==1){
                            if($Inicio==1){
                                $TipoEscanerrespuesta=$this->CompruebaAreasAnteriortodas($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada);
                                if($TipoEscanerrespuesta!=5){
                                    $TipoEscanerrespuesta=$this->CompruebaAreasPosteriortodas($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada);
                                    if($TipoEscanerrespuesta!=6){
                                        $TipoEscanerrespuesta=$this->TipoEscaner($CodigoPartes,$CodigoTam,$Area,$confirmacion);
                                    }
                                }
                            }
                            if($Finalizar==1){
                                $TipoEscanerrespuesta=$this->TipoEscanerFinalizar($CodigoPartes,$CodigoTam,$Area,$confirmacion);
                            }
                        }else if($Escaner==0){
                            $TipoManualrespuesta=$datos->partidasOF()->where('id','=',$CodigoPartes[1])->first();
                            if(!($TipoManualrespuesta=="" || $TipoManualrespuesta==null)){
                                $EscanerExiste = 1;
                            }else{
                                $EscanerExiste = 0;
                            }
                        }
                    }else{
                        if($Escaner==1){
                            if($Inicio==1){
                                //Comprobar si ya paso el paso anterior
                                $TipoEscanerrespuesta=$this->ComprobarAreaAnterior($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada);
                                if($TipoEscanerrespuesta!=5){
                                    //Comprobar si se encuentra iniciada en el paso posterior
                                    $TipoEscanerrespuestaPosterior=$this->ComprobarAreaPosterior($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada);
                                    if($TipoEscanerrespuestaPosterior!=6){
                                        $TipoEscanerrespuesta=$this->TipoEscanerAreas($CodigoPartes,$CodigoTam,$Area,$confirmacion);
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
                            <select class="form-select form-select-sm mb-3" data-list-filter="data-list-filter">
                            '.$Opciones.'
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
    //Area 4 Preparado
    public function Preparado(){
        $Area=$this->funcionesGenerales->encrypt(4);
        return view('Areas.Preparado',compact('Area'));
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
                        'Linea_id' => $this->funcionesGenerales->Linea(),
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
                            $Partidas->Estatus=1;
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
                    'Linea_id' => $this->funcionesGenerales->Linea(),
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
                            'Linea_id' => $this->funcionesGenerales->Linea(),
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
    public function TipoNoEscanerAreas($CodigoPartes,$CodigoTam,$Area,$confirmacion){
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
                    'Linea_id' => $this->funcionesGenerales->Linea(),
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
                            'Linea_id' => $this->funcionesGenerales->Linea(),
                        ];
                        $datosPartidas->Areas()->attach($Area,$pivotData);
                        return 1;
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
        if($Estatus==2){
            $TipoAccion=1;
        }
        //Validacion al finalizar las partidas a finalizar no pueden ser mayores a las entradas
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
        //Validacion al mandar a Retrabajo las partidas a retrabajo no pueden ser mayor a las salidas
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
                //if(($PartidasInicio+$PartidasRetrabajo-$PartidasFin)<$Cantidad){
                    return response()->json([
                        'Inicio'=>$Inicio,
                        'Fin'=>$Fin,
                        'status' => "SurplusRetrabajo",
                        'OF' => $CodigoPartes[0],       
                    ]);  
                //}
            }
        }
            if($Estatus==2 || $Fin==1){
                $ContarPartidas=0;
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
                            'Linea_id' => $this->funcionesGenerales->Linea(),
                        ];
                    }else{
                        $pivotData = [
                            'FechaTermina' => $FechaHoy,
                            'Users_id' => $this->funcionesGenerales->InfoUsuario(),
                            'Linea_id' => $this->funcionesGenerales->Linea(),
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
            //131860-1-1
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
    public function ComprobarAreaAnterior($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada){
        $TipoEscanerrespuesta=0;
        $EMPartidasOF=$datos->partidasOF->where('id','=',$CodigoPartes[1])->first();
         if($EMPartidasOF=="" || $EMPartidasOF==null){
            return 'error';
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
    public function CompruebaAreasAnteriortodas($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada){
        $partidas = $datos->partidasOF()
                        ->join('partidas', 'partidasOF.id', '=', 'partidas.PartidasOF_id')  // JOIN entre PartidasOF y Partidas
                        ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')  // JOIN con la tabla pivote (ajustar nombre de la tabla)
                        ->join('areas', 'partidas_areas.Areas_id', '=', 'areas.id')  // JOIN con Areas a través de la tabla pivote
                        ->where('partidas_areas.FechaTermina', '=', null)  // Filtro por un área específica
                        ->where('areas.id', '==', $Area-1)  // Filtro por un área específica
                        ->select('partidasOF.*', 'partidas.*', 'areas.*','partidas_areas.*')  // Seleccionar todas las columnas de las tres tablas
                        ->get()->count();
        if($partidas>0){
            return 5;
        }
    }
    public function CompruebaAreasPosteriortodas($datos,$Area,$CodigoPartes,$menu,$Escaner,$CantidadCompletada){
        $partidas = $datos->partidasOF()
                        ->join('partidas', 'partidasOF.id', '=', 'partidas.PartidasOF_id')  // JOIN entre PartidasOF y Partidas
                        ->join('partidas_areas', 'partidas.id', '=', 'partidas_areas.Partidas_id')  // JOIN con la tabla pivote (ajustar nombre de la tabla)
                        ->join('areas', 'partidas_areas.Areas_id', '=', 'areas.id')  // JOIN con Areas a través de la tabla pivote
                        ->where('partidas_areas.FechaTermina', '=', null)  // Filtro por un área específica
                        ->where('areas.id', '!=', $Area)  // Filtro por un área específica
                        ->select('partidasOF.*', 'partidas.*', 'areas.*','partidas_areas.*')  // Seleccionar todas las columnas de las tres tablas
                        ->get()->count();
        if($partidas>0){
            return 6;
        }
    }
}
