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
        $CodigoPartes = explode("-", $Codigo);
        $CodigoTam = count($CodigoPartes);
        $menu="";
        $Escaner="";
        $CantidadCompletada=0;
        if($CodigoTam==2 ||$CodigoTam==3){
            $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->get();
            if(!$datos){
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
                if($Escaner==1){
                        $TipoEscanerrespuesta=$this->TipoEscaner($CodigoPartes,$CodigoTam,3);
                }
                foreach( $datos as $ordenFabricacion){
                    foreach( $ordenFabricacion->partidasOF()->get() as $PartidasordenFabricacion){
                        foreach( $PartidasordenFabricacion->Partidas()->get() as $Partidas){
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
                return response()->json([
                    'tabla' => $menu,
                    'Escaner' => $Escaner,
                    'status' => "success",
                    'CantidadTotal' => $CantidadTotal,
                    'CantidadCompletada' => $CantidadCompletada,
                    'OF' => $CodigoPartes[0]
        
                ]);
            }
        }
        return response()->json([
            'tabla' => $menu,
            'Escaner' => "",
            'status' => "NoExiste",
            'CantidadTotal' => "",
            'CantidadCompletada' => $CantidadCompletada,
            'OF' => $CodigoPartes[0]

        ]);
    }
    public function TipoEscaner($CodigoPartes,$CodigoTam,$Area){
        $FechaHoy=date('Y-m-d H:i:s');
        if($CodigoTam!=3){
            return 0;
        }else{
            //Comprueba si existe la Orden  de Fabricacion
            $datos=OrdenFabricacion::where('OrdenFabricacion', '=', $CodigoPartes[0])->first();
            if($datos->count()==0){
                return 0;
            }
            //Comprueba si existe el id de la partida
            $datos= $datos->PartidasOF()->where('id',"=",$CodigoPartes[1])->first();
            if($datos->count()==0){
                return 0;
            }
            //Comprobamos si existe la Orden de fabricacion con la partida y el numero de parte ya creado
            $datos->Partidas()->get();
            $datosPartidas=$datos->Partidas();
            return $datosPartidas;
            $Partidas = new Partidas();
            $Partidas->PartidasOF_id=$datos->id;
            $Partidas->CantidadaPartidas=1;
            $Partidas->NumParte=$CodigoPartes[2];
            $Partidas->FechaComienzo=$FechaHoy;

            if ($Partidas->save()) {
                return 1;
            } else {
                return 0;
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
}
