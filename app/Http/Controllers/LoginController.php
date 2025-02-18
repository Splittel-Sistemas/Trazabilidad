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
    // Validación de las credenciales
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $remember = $request->has('remember');

    // Obtener el usuario por su email
    $user = User::where('email', $request->email)->first();

    // Verificar si el usuario existe y si está activo
    if (!$user || $user->active == 0) {
        return redirect('login')
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Cuenta inactiva o no encontrada.']);
    }

    // Intentar autenticar al usuario
    if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
        // Regenerar la sesión
        $request->session()->regenerate();

        // Redirigir al usuario a la página principal
        return redirect()->intended(route('Home'));
    } else {
        // Si las credenciales son incorrectas
        return redirect('login')
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Correo electrónico o contraseña incorrectos.']);
    }
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


public function operador(Request $request)
{
    // Validar que la clave sea ingresada
    $request->validate([
        'clave' => 'required',
    ]);

    // Buscar al usuario por la clave
    $operador = User::where('password', $request->clave)->first(); 

    if ($operador) {
        // Verificar si el usuario está activo
        if ($operador->active == 1) {
            Auth::login($operador);
            $request->session()->regenerate();
            return redirect()->intended(route('Home'));
        } else {
            return redirect()->route('login_view')
                ->withErrors(['clave' => 'El acceso ha sido restringido. Contacte al administrador.']);
        }
    }

    return redirect()->route('login_view')
        ->withErrors(['clave' => 'Clave incorrecta.']);
}









}
