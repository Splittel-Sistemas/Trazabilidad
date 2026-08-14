<?php

namespace App\Http\Controllers;

use App\Models\Traslados;
use Illuminate\Http\Request;

class TrasladosController extends Controller
{

    public function index()
    {
        $traslados = Traslados::with('trasladoDetalles')->get();
        return view('layouts.traslados.index', compact('traslados'));
    }

    
    public function show(int $id)
    {
        $traslado = Traslados::with('trasladoDetalles')->where('id', $id)->first();
        $serviceLayer = Traslados::with('serviceLayer')->where('id', $id)->first();
        return view('layouts.traslados.show', compact('traslado', 'serviceLayer'));
    }
}
