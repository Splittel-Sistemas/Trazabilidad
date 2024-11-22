<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function index()
    {
        return view('layouts.ordenes.buscaryregistrar'); // Asegúrate de que el nombre de la vista coincida con la ruta de la vista que creaste
    }
}
