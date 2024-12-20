<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeControler extends Controller
{
    public function  Home(){
        return view('Home');
    }
    public function index()
    {
        // Verificar si el usuario está autenticado y activo
        $user = Auth::user();

        if (!$user || !$user->active) {
            // Si el usuario no está autenticado o está desactivado, lo redirigimos al login
            Auth::logout();  // Cerrar la sesión si el usuario está desactivado
            return redirect()->route('login')->withErrors(['email' => 'Tu cuenta ha sido desactivada.']);
        }

        // Si el usuario está activo, continua con la carga de la página
        return view('home');  // Ajusta esto al nombre de tu vista Home
    }
}
