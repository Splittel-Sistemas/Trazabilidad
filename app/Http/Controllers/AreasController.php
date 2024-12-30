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
    public function Suministro(){
        return view('Areas.Suministro');//, compact('datos', 'FechaInicio', 'FechaFin','status'));
        /*$FechaInicio=date('Ymd', strtotime('-1 day'));
        $FechaFin=date('Ymd');
        $NumOV="";
        $message="";
        $datos=$this->OrdenesVenta($FechaFin,$FechaInicio,$NumOV);
        if($datos!=0){
            if(empty($datos)){
                $status="empty";
            }else{
                $status="success";
            }
        }else{
            $status="error";
        }
        $FechaInicio=date('Y-m-d', strtotime('-1 day'));
        $FechaFin=date('Y-m-d');
        return view('Planeacion.Planeacion', compact('datos', 'FechaInicio', 'FechaFin','status'));*/
    }
    public function SuministroBuscar(Request $request){
        $cadena1=0;
        $cadena2=0;
         $menu='<div class="list-group">';
        if ($request->has('CodigoPrimero')) {
            $cadena1=$request->CodigoPrimero;
            $datos=OrdenFabricacion::where('OrdenFabricacion', 'like', '%'.$cadena1. '%')->get();
            $menu="";
            $BanderaPartidas=0;
            foreach( $datos as $ordenFabricacion){
                if($request->has('CodigoSegundo') && $request->CodigoSegundo!=""){
                    foreach( $ordenFabricacion->partidasOF()->get() as $PartidasordenFabricacion){
                        if ($request->CodigoSegundo==$PartidasordenFabricacion->id) {
                            $BanderaPartidas=1;
                            $menu.='<a href="#" class="list-group-item list-group-item-action border small p-1 active" onclick="TraerDatos('.$PartidasordenFabricacion->id.',\''.$ordenFabricacion->OrdenFabricacion.'\')">'.$ordenFabricacion->OrdenFabricacion."-".$PartidasordenFabricacion->id.'</a>';
                        }             
                    }
                }else{
                    foreach( $ordenFabricacion->partidasOF()->get() as $key =>$PartidasordenFabricacion){
                            $BanderaPartidas=1;
                            $menu.='<a href="#" class="list-group-item list-group-item-action border small p-1 ';
                            if($key==0){
                                $menu.='active';
                            }
                            $menu.='" onclick="TraerDatos('.$PartidasordenFabricacion->id.',\''.$ordenFabricacion->OrdenFabricacion.'\')">'.$ordenFabricacion->OrdenFabricacion."-".$PartidasordenFabricacion->id.'</a>';
                    }
                }
            }
            if($BanderaPartidas==0){
                $menu.='<i class="list-group-item list-group-item-action border small p-1 disabled">No existen partidas con el codigo Agregado</i>';
            }
            $menu.='</div>';
            return $menu;
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
}
