<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\User;
use Illuminate\Http\Request;

class OrdenVentaController extends Controller
{
    public function index()
    {
        
        $users = User::all();
        return view('layouts.ordenes.ordenes', compact('users'));
    }

   
    public function updateState($id, Request $request)
    {
        $orden = User::find($id);
        
        if ($orden) {
            $orden->estado = $request->input('state');
            $orden->save();
            return response()->json(['message' => 'Estado actualizado correctamente']);
        }
        
        return response()->json(['message' => 'Orden no encontrada'], 404);
    }
}

