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
    public function login_view()
{
    return view('layouts.login');  
}
public function login(Request $request)
{
    // Determinar si es administrativo o operador
    if ($request->has('email') && $request->has('password')) {
        // Validación para administrativos
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('Home')); // Redirigir si las credenciales son correctas
        }

        return redirect('login')->withErrors(['email' => 'Correo electrónico o contraseña incorrectos.']);
    } 
    elseif ($request->has('clave')) {
        // Validación para operadores
        $request->validate([
            'clave' => 'required|string',
        ]);

        // Buscar operador por clave en la base de datos
        $user = User::where('clave', $request->clave)->first();

        if ($user) {
            Auth::login($user);
            return redirect()->intended(route('Home'));
        }

        return redirect('login')->withErrors(['clave' => 'Clave incorrecta.']);
    }

    return redirect('login')->withErrors(['error' => 'Debe ingresar credenciales válidas.']);
}


public function register(Request $request)
{
    // Validación de los datos de registro
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ]);

    // Crear el nuevo usuario
    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);
    $user->save();

    // Autenticar al usuario recién registrado
    Auth::login($user);

    // Redirigir al usuario a la página principal
    return redirect(route('Home'));
}
public function logout(Request $request)
{
    Auth::logout(); // Cierra la sesión del usuario
    $request->session()->invalidate(); // Invalida la sesión para evitar problemas de sesión
    $request->session()->regenerateToken(); // Regenera el token CSRF
    return redirect()->route('login_view'); // Redirige al login o página de inicio
}





}
