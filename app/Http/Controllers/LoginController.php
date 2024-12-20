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
    return view('layouts.login');  // Cambia 'layouts.login' por la ruta correcta de tu vista
}
    public function login(Request $request)
{
    // Validación de las credenciales
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Obtener las credenciales
    $credentials = [
        'email' => $request->email,
        'password' => $request->password,
    ];

    $remember = $request->has('remember');

    // Intentar autenticar al usuario
    if (Auth::attempt($credentials, $remember)) {
        // Regenerar la sesión
        $request->session()->regenerate();

        // Redirigir al usuario a la página principal
        return redirect()->intended(route('Home'));
    } else {
        // Si las credenciales son incorrectas
        return redirect('login')->withErrors(['email' => 'Credenciales incorrectas.']);
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
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect(route('login'));
}




}
