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
        return view('registro.create', compact('roles', 'permissions')); 
        $permissions = [
            ['name' => 'crear_usuarios', 'description' => 'Permite crear nuevos usuarios.'],
            ['name' => 'editar_usuarios', 'description' => 'Permite editar usuarios existentes.'],
            ['name' => 'gestionar_roles_permisos', 'description' => 'Permite gestionar roles y permisos de usuarios.']
        ];
    
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], ['description' => $perm['description']]);
        }
    
        // Obtener todos los roles y permisos
        $roles = Role::all();
        $permissions = Permission::all();
    
        return view('registro.create', compact('roles', 'permissions'));
    }

    // Método para almacenar un nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'apellido' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'array', 
            'permissions' => 'array', 
        ]);
    
        // Crear un nuevo usuario
        $registro = new User; 
        $registro->apellido = $request->input('apellido');
        $registro->nombre = $request->input('nombre');
        $registro->email = $request->input('email');
        $registro->password = Hash::make($request->input('password'));
        $registro->save();
    
        // Asignar roles
        if ($request->has('roles')) {
            $registro->roles()->sync($request->input('roles'));
        }
    
        // Asignar permisos
        if ($request->has('permissions')) {
            $registro->permissions()->sync($request->input('permissions'));
        }
    
        return redirect()->route('registro.index')->with('status', 'Usuario registrado exitosamente.');
    }
    

    // Método para actualizar un usuario
    public function update(Request $request, User $registro)
{
    $request->validate([
        'apellido' => 'required|string|max:255',
        'nombre' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $registro->id,
        'roles' => 'array',
        'permissions' => 'array',
    ]);

    $registro->apellido = $request->input('apellido');
    $registro->nombre = $request->input('nombre');
    $registro->email = $request->input('email');

    // Actualizar la contraseña si se proporciona
    if ($request->filled('password')) {
        $registro->password = Hash::make($request->input('password'));
    }

    $registro->save();

    // Sincronizar roles
    if ($request->has('roles')) {
        $registro->roles()->sync($request->input('roles'));
    }

    // Sincronizar permisos
    if ($request->has('permissions')) {
        $registro->permissions()->sync($request->input('permissions'));
    }
    if ($request->ajax()) {
        return response()->json(['status' => 'Usuario actualizado exitosamente.']);
    }
    
    return redirect()->route('registro.index')->with('status', 'Usuario actualizado exitosamente.');
}
public function destroy($id)
{
    $registro = User::findOrFail($id);
    $registro->delete();

    return response()->json(['mensaje' => 'Usuario eliminado exitosamente.']);
}
}

