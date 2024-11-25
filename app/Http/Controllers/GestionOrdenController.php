<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class GestionOrdenController extends Controller
{
    public function index(Request $request)
    {
        {
            return view('layouts.ordenes.ordenesv'); 

        }
    }
}
