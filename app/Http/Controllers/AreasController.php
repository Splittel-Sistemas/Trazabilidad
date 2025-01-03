<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\FuncionesGeneralesController;
use App\Models\OrdenFabricacion;
use App\Models\PartidasOF;

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
        if($CodigoTam==2){
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
                foreach( $datos as $ordenFabricacion){
                    foreach( $ordenFabricacion->partidasOF()->get() as $PartidasordenFabricacion){
                        foreach( $PartidasordenFabricacion->Partidas()->get() as $Partidas){
                                $menu.='<tr>
                                    <td>'.$PartidasordenFabricacion->NumParte.'</td>
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
        /*$cadena1=0;
        $cadena2=0;
        $Escaner="";
        $Existe="No";
        if($cadena1==""){
            return '<div class="list-group"></div>';
        }
         $menu='<div class="list-group">';
        if ($request->has('CodigoPrimero')) {
            $cadena1=$request->CodigoPrimero;
            $datos=OrdenFabricacion::where('OrdenFabricacion', 'like', '%'.$cadena1. '%')->get();
            $menu="";
            $BanderaPartidas=0;
            $cont=0;
            foreach( $datos as $ordenFabricacion){
                if($request->has('CodigoSegundo') && $request->CodigoSegundo!=""){
                    foreach( $ordenFabricacion->partidasOF()->get() as $PartidasordenFabricacion){
                        if ($request->CodigoSegundo==$PartidasordenFabricacion->id) {
                            $BanderaPartidas=1;
                            $menu.='<a href="#" class="list-group-item list-group-item-action border small p-1 active" onclick="TraerDatos('.$PartidasordenFabricacion->id.',\''.$ordenFabricacion->OrdenFabricacion.'\')">'.$ordenFabricacion->OrdenFabricacion."-".$PartidasordenFabricacion->id.'</a>';
                            $Escaner=$ordenFabricacion->Escaner;
                            $Existe="Si";
                        }             
                    }
                }else{
                    foreach( $ordenFabricacion->partidasOF()->get() as $key =>$PartidasordenFabricacion){
                            $BanderaPartidas=1;
                            $menu.='<a href="#" class="list-group-item list-group-item-action border small p-1 ';
                            if($cont==0){
                                $menu.='active';
                            }
                            $menu.='" onclick="TraerDatos('.$PartidasordenFabricacion->id.',\''.$ordenFabricacion->OrdenFabricacion.'\')">'.$ordenFabricacion->OrdenFabricacion."-".$PartidasordenFabricacion->id.'</a>';
                            $cont++;    
                    }
                }
            }
            if($BanderaPartidas==0){
                $menu.='<i class="list-group-item list-group-item-action border small p-1 disabled">No existen partidas con el codigo ingresado</i>';
            }
            $menu.='</div>';
            return response()->json([
                'menu' => $menu,
                'Escaner' => $Escaner,
                'Existe' => $Existe

            ]);
        }
        return response()->json([
            'menu' => "",
            'Escaner' => "",
            'Existe' => ""

        ]);*/
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
