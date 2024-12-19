<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrdenFabricacion;

class AreasController extends Controller
{
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
