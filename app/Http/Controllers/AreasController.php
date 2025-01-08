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
        return view('Areas.Suministro');
    }
    public function SuministroBuscar(Request $request){
        $Codigo = $request->Codigo;
        $Inicio = $request->Inicio;
        $Finalizar = $request->Finalizar;
        $CodigoPartes = explode("-", $Codigo);
        return $this->ComprobarNumEtiqueta($CodigoPartes);
        $CodigoTam = count($CodigoPartes);
        $TipoEscanerrespuesta=0;
        $menu="";
        $Escaner="";
        $CantidadCompletada=0;
        if($CodigoTam==2 ||$CodigoTam==3){
            $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->get();
            if($datos->count()==0){
                return response()->json([
                    'tabla' => $menu,
                    'Escaner' => $Escaner,
                    'status' => "empty",
                    'CantidadTotal' => "",
                    'CantidadCompletada' => $CantidadCompletada,
                    'OF' => $CodigoPartes[0]
        
                ]);
            }else{
                $CantidadTotal=$datos[0]->CantidadTotal;
                $Escaner=$datos[0]->Escaner;
                if($CodigoTam==3){
                    if($Escaner==1){
                        if($Inicio==1){
                            $TipoEscanerrespuesta=$this->TipoEscaner($CodigoPartes,$CodigoTam,3);
                        }
                        if($Finalizar==1){
                            $TipoEscanerrespuesta=$this->TipoEscanerFinalizar($CodigoPartes,$CodigoTam,3);
                        }
                    }
                }
                foreach( $datos as $ordenFabricacion){
                    foreach( $ordenFabricacion->partidasOF()->orderBy('id', 'desc')->get() as $PartidasordenFabricacion){
                        foreach( $PartidasordenFabricacion->Partidas()->orderBy('id', 'desc')->get() as $Partidas){
                            if(!($Partidas->Areas()->where('Areas_id','=',3)->first() =="" || $Partidas->Areas()->where('Areas_id','=',3)->first() == null)){
                                $menu.='<tr>
                                <td>'.$Partidas->NumParte.'</td>
                                <td>'.$Partidas->CantidadaPartidas.'</td>
                                <td>'.$Partidas->FechaComienzo.'</td>
                                <td>'.$Partidas->FechaTermina.'</td>';
                                if($Partidas->FechaTermina==""){
                                    $menu.='<td><span class="badge bg-warning text-dark">En proceso</span></td>';
                                    $CantidadCompletada+=$Partidas->CantidadaPartidas;
                                }else{$menu.='<td><span class="badge bg-success">Completado</span></td>';$CantidadCompletada+=$Partidas->CantidadaPartidas;}
                                $menu.='<td><button class="btn btn-sm btn-danger">Detener</button></td>
                                    </tr>';
                            }
                        }
                    } 
                }
                return response()->json([
                    'tabla' => $menu,
                    'Escaner' => $Escaner,
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
                'CantidadCompletada' => $CantidadCompletada,
                'OF' => $CodigoPartes[0]
    
            ]);
        }
    }
    public function TipoEscaner($CodigoPartes,$CodigoTam,$Area){
        // Respuestas 0=Error, 1=Guardado, 2=Ya existe, 3=Retrabajo
        $FechaHoy=date('Y-m-d H:i:s');
        if($CodigoTam!=3 || $CodigoPartes[0]=="" || $CodigoPartes[1]=="" || $CodigoPartes[2]=="" ){
            return 0;
        }else{
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
                    $Partidas->NumParte=$CodigoPartes[2];
                    $Partidas->FechaComienzo=$FechaHoy;
                    if ($Partidas->save()) {
                        $Partidas->Areas()->attach($Area);
                        return 1;
                    } else {
                        return 0;
                    }
                }else{
                    if(!($datosPartidas->Areas()->where('Areas_id','=',3)->first() =="" || $datosPartidas->Areas()->where('Areas_id','=',3)->first() == null)){
                        if($datosPartidas->FechaTermina==null || $datosPartidas->FechaTermina==""){
                            return 2;
                        }else{
                            $Partidas = new Partidas();
                            $Partidas->PartidasOF_id=$datos->id;
                            $Partidas->CantidadaPartidas=1;
                            $Partidas->NumParte=$CodigoPartes[2];
                            $Partidas->FechaComienzo=$FechaHoy;
                            if ($Partidas->save()) {
                                $Partidas->Areas()->attach($Area);
                                return 3;
                            } else {
                                return 0;
                            }
                        }
                    }else{
                        $Partidas = new Partidas();
                            $Partidas->PartidasOF_id=$datos->id;
                            $Partidas->CantidadaPartidas=1;
                            $Partidas->NumParte=$CodigoPartes[2];
                            $Partidas->FechaComienzo=$FechaHoy;
                            if ($Partidas->save()) {
                                $Partidas->Areas()->attach($Area);
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
                if(!($datosPartidas->FechaComienzo==null || $datosPartidas->FechaComienzo=="") && ($datosPartidas->FechaTermina==null || $datosPartidas->FechaTermina=="")){
                    $datosPartidas->FechaTermina=$FechaHoy;
                    if ($datosPartidas->save()) {
                        return 1;
                    } else {
                        return 0;
                    }
                }else{return 2;}
            }else{
                return 3;
            }
        }
    }
    public function TipoManual(){
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
    public function ComprobarNumEtiqueta($Codigo){
        $CodigoPartes = explode("-", $Codigo);
        return$datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->get();
    }
}
