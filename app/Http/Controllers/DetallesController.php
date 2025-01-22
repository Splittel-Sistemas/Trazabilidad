<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DetallesController extends Controller

{
    public function show( Request $request)
        {
                return view('layouts.ordenes.detalles');
            
        }
}