<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrdenFabricacionController extends Controller
{
    //
    public function index()
    {
    return view('layouts.ordenes.ordenesventa'); 
    }
}
