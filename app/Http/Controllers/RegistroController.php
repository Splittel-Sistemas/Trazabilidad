<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role; 
use App\Models\Permission; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegistroController extends Controller
{
    // Método para listar todos los usuarios
    public function index()
    {
        $personal = User::all(); 
        $roles = Role::all(); 
        $permissions = Permission::all();
        
        return view('registro.index', compact('personal', 'roles', 'permissions'));
    }
    // RegistroController.php
    public function edit(User $registro)
    {
        $roles = Role::all(); 
        $userRoles = $registro->roles->pluck('id'); 
        return view('registro.edit', compact('registro', 'roles', 'userRoles'));
    }
    // Obtener los roles asignados al usuario
    public function show(User $registro)
    {
        
        $roles = $registro->roles->pluck('id')->toArray(); 
        return response()->json([
            'apellido' => $registro->apellido,
            'name' => $registro->name,
            'email' => $registro->email,
            'roles' => $roles 
        ]);
    }
    // Método para mostrar el formulario de creación de un nuevo usuario
    public function create()
    {
        $roles = Role::all(); 
        $permissions = Permission::all(); 
        return view('registro.create', compact('roles', 'permissions')); 
    }
    // Validaciones y crear usuario
    public function store(Request $request)
    {
       
        $validatedData = $request->validate([
            'apellido' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array', 
          
        ]);

        // Crear el nuevo usuario
        $user = User::create([
            'name' => $validatedData['name'],
            'apellido' => $validatedData['apellido'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        
        $user->roles()->sync($validatedData['roles']);
      

        
        return redirect()->route('registro.index')->with('status', 'Usuario creado exitosamente.');
    }
    // Método para actualizar un usuario
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->only(['apellido', 'name', 'email']));

        
        $user->roles()->sync($request->roles); 

        return redirect()->route('registro.index')->with('status', 'Usuario actualizado exitosamente');
    }
    public function destroy($id)
    {
        $registro = User::findOrFail($id);
        $registro->delete();

        return response()->json(['mensaje' => 'Usuario eliminado exitosamente.']);
    }

    // Método para activar un usuario
    public function activar(Request $request)
    {
        $user = User::find($request->user_id);
        if ($user) {
            $user->active = true;
            $user->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Usuario no encontrado.'], 404);
    }

    // Método para desactivar un usuario
    public function desactivar(Request $request)
    {
        $user = User::find($request->user_id);
        if ($user) {
            $user->active = false;
            $user->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Usuario no encontrado.'], 404);
    }
}
