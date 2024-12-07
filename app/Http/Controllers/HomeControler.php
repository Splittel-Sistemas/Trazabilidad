<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeControler extends Controller
{
    public function  Home(){
        return view('Home');
    }
}
