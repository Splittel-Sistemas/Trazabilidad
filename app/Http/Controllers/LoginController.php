<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class LoginController extends Controller
{
    //
    public function register(Request $request){
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        Auth::login($user);
        return redirect(route('menu'));
    }
    public function login(Request $request, ){ 
        // Validación
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Credenciales sin hashear la contraseña
    $credentials = [
        'email' => $request->email,
        'password' =>  $request->password, // Usa la contraseña original
    ];

    $remember = $request->has('remember');
    //return $remember;

    // Intenta autenticar al usuario
    if (Auth::attempt($credentials, $remember)) {
        // Regenera la sesión
        $request->session()->regenerate(); 
        
        return redirect()->intended(route('menu'));
    } else {
        // Redirige de nuevo al login con un mensaje de error
      
        return redirect('login')->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    }
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect(route('login'));
        
    }
    public function login_view(){
        return view('layouts.login');
    }
}

