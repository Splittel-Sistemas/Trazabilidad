<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegistroController extends Controller
{
    // Método para listar todos los usuarios
    public function index()
    {
        $personal = User::all(); // Obtiene todos los usuarios
        return view('registro.index', compact('personal')); 
    }

    // Método para mostrar un usuario específico
    public function show(User $registro) 
    {
        return view('registro.show', compact('registro'));
    }

    // Método para mostrar el formulario de edición de un usuario
    public function edit(User $registro)
    {
        return view('registro.edit', compact('registro'));
    }

    // Método para mostrar el formulario de creación de un nuevo usuario
    public function create()
    {
        return view('registro.create'); 
    }

    // Método para almacenar un nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'apellido' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $registro = new User; 
        $registro->apellido = $request->input('apellido');
        $registro->nombre = $request->input('nombre');
        $registro->email = $request->input('email');
        $registro->password = Hash::make($request->input('password'));
        $registro->save();

        return redirect()->route('registro.index')->with('status', 'Usuario registrado exitosamente.');
    }    

    // Método para actualizar un usuario
    public function update(Request $request, User $registro)
    {
        $request->validate([
            'apellido' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $registro->id,
        ]);

        $registro->apellido = $request->input('apellido');
        $registro->nombre = $request->input('nombre');
        $registro->email = $request->input('email');

        // Si se proporciona una nueva contraseña, actualizarla
        if ($request->filled('password')) {
            $registro->password = Hash::make($request->input('password'));
        }

        $registro->save();

        return redirect()->route('registro.index')->with('status', 'Usuario actualizado exitosamente.');
    }

    // Método para eliminar un usuario
    public function destroy(User $registro)
    {
        $registro->delete();
        return redirect()->route('registro.index')->with('status', 'Usuario eliminado exitosamente.');
    }
}
