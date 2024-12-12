<?php

 namespace App\Http\Controllers;
 
 use Illuminate\Http\Request;
 use App\Models\PartidasOf;
 
 class PartidaController extends Controller
 {
     public function guardar(Request $request)
     {
        //$respuesta = new PartidasOF();
        //$respuesta-> orden_fabricacion_id = $request->input('ordenFabricacionId') ;
        return( $request);


         /*
         $validated = $request->validate([
             'cantidad_cortes' => 'required|integer', 
             'cantidad_del_dia' => 'required|integer', 
             'orden_fabricacion_id' => 'required|exists:orden_fabricaciones,id', 
         ]);
         $partida = PartidasOf::create([
             'cantidad_cortes' => $validated['cantidad_cortes'],
             'cantidad_del_dia' => $validated['cantidad_del_dia'],
             'orden_fabricacion_id' => $validated['orden_fabricacion_id'],
         ]);
         return response()->json(['message' => 'Partida guardada con éxito', 'partida' => $partida], 200);
     }*/
 }
}
 