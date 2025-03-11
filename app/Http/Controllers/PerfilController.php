<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class PerfilController extends Controller
{
    public function index()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();
        $roles = $user->roles;  // Aquí obtienes los roles asociados al usuario
    
        // Pasar el usuario y los roles a la vista
        return view('Usuarios.UsuariosIndex', compact('user', 'roles'));
    }
    public function update(Request $request)
{
    Log::info($request->all());  // Esto debería mostrar los datos que recibes

    // Validación de los datos
    $request->validate([
        'name' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
    ]);

    $user = Auth::user();

    // Actualiza los datos del usuario
    $user->update([
        'name' => $request->name,
        'apellido' => $request->apellido,
        'email' => $request->email,
    ]);

    return response()->json(['message' => 'Perfil actualizado correctamente.']);
}


    
}
