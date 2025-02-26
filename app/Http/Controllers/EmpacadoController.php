<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class EmpacadoController extends Controller
{
    //
    public function index()
    {
        return view('Areas.Empacado');
    }
    public function tabla()
{
    $areas = DB::table('partidasof_areas')
        ->join('partidasof', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
        ->join('ordenfabricacion', 'partidasof.OrdenFabricacion_id', '=', 'ordenfabricacion.id')
        ->join('ordenventa', 'ordenfabricacion.OrdenVenta_id', '=', 'ordenventa.id')
        ->whereIn('partidasof_areas.Areas_id', [8])
        ->select(
            'ordenventa.OrdenVenta',
            'ordenfabricacion.OrdenFabricacion',
            'ordenfabricacion.CantidadTotal',
            'ordenfabricacion.FechaEntrega',
            'partidasof_areas.Cantidad',   
        )
        ->get();

    return response()->json($areas);
}
public function guardarDB()
{
   

}

}
