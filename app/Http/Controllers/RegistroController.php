<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role; 
use App\Models\Permission; 
use Illuminate\Support\Facades\Hash;

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

    // Método para mostrar un usuario específico
    public function show(User $registro) 
    {
        return view('registro.show', compact('registro'));
    }

    // Método para mostrar el formulario de edición de un usuario
    public function edit(User $registro)
    {
        $roles = Role::all(); // Obtiene todos los roles
        $permissions = Permission::all(); // Obtiene todos los permisos
        return view('registro.edit', compact('registro', 'roles', 'permissions'));
    }

    // Método para mostrar el formulario de creación de un nuevo usuario
    public function create()
    {
        $roles = Role::all(); // Obtiene todos los roles
        $permissions = Permission::all(); // Obtiene todos los permisos
        return view('registro.create', compact('roles', 'permissions')); // Pasa los datos a la vista
    }

    public function store(Request $request)
    {
        // Validación de los datos del formulario
        $validatedData = $request->validate([
            'apellido' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
           // 'roles' => 'required|array', // Los roles son obligatorios
            //'permissions' => 'required|array', // Los permisos son obligatorios
        ]);

        // Crear el nuevo usuario
        $user = User::create([
            'name' => $validatedData['name'],
            'apellido' => $validatedData['apellido'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        /*// Asignar roles y permisos al usuario
        $user->roles()->sync($validatedData['roles']);
        $user->permissions()->sync($validatedData['permissions']);*/

        // Redirigir al usuario con un mensaje de éxito
        return redirect()->route('registro.index')->with('status', 'Usuario creado exitosamente.');
    }

    // Método para actualizar un usuario
    public function update(Request $request, User $registro)
    {
        // Valida solo los campos que podrían cambiar.
        $validatedData = $request->validate([
            'apellido' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $registro->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'array',
            'permissions' => 'array',
        ]);

        // Solo actualiza los campos proporcionados en la solicitud.
        if ($request->has('apellido')) {
            $registro->apellido = $request->input('apellido');
        }

        if ($request->has('name')) {
            $registro->name = $request->input('name');
        }

        if ($request->has('email')) {
            $registro->email = $request->input('email');
        }

        // Si se proporciona una nueva contraseña, actualízala.
        if ($request->filled('password')) {
            $registro->password = Hash::make($request->input('password'));
        }

        // Guardar los cambios en el usuario.
        $registro->save();

        // Sincronizar roles, si se envían roles en la solicitud.
        if ($request->has('roles')) {
            $registro->roles()->sync($request->input('roles'));
        }

        // Sincronizar permisos, si se envían permisos en la solicitud.
        if ($request->has('permissions')) {
            $registro->permissions()->sync($request->input('permissions'));
        }

        // Si la solicitud es AJAX, devolver respuesta JSON.
        if ($request->ajax()) {
            return response()->json(['status' => 'Usuario actualizado exitosamente.']);
        }

        // Redirigir con mensaje de éxito.
        return redirect()->route('registro.index')->with('status', 'Usuario actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $registro = User::findOrFail($id);
        $registro->delete();

        return response()->json(['mensaje' => 'Usuario eliminado exitosamente.']);
    }
}

