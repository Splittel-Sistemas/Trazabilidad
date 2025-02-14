<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role; 
use App\Models\Permission; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RegistroController extends Controller
{
    // Método para listar todos los usuarios
    public function index()
    {

        $user = Auth::user();
       
        
       
        //if ($user->hasPermission('Vistas Editar')) {
            
            $roles = Role::with('permissions')->get();
            $personal = User::all();
            $roles = Role::all(); 
            $permissions = Permission::all();
            
           
            return view('registro.index', compact('personal', 'roles', 'permissions'));
        //} else {
            
            return redirect()->away('https://assets-blog.hostgator.mx/wp-content/uploads/2018/10/paginas-de-error-hostgator.webp');
        //}
    }

         
        
    
  // RegistroController.php
    public function edit(User $registro)
    {
        //$role = Role::with('permissions')->findOrFail($id); 
        $roles = Role::all(); // Obtener todos los roles disponibles
        $userRoles = $registro->roles->pluck('id'); // Obtener los roles asignados al usuario
        return view('registro.edit', compact('registro', 'roles', 'userRoles'));
    }
    public function show(User $request, $registro)
    {
        // Obtener el registro de usuario
        $registro = User::findOrFail($registro);

        // Cargar la relación 'roles'
        $registro->load('roles');

        // Obtener los roles asignados al usuario
        $roles = $registro->roles->pluck('id')->toArray();

        return response()->json([
            'apellido' => $registro->apellido,
            'name' => $registro->name,
            'email' => $registro->email,
            'roles' => $roles // Enviar los roles asignados en la respuesta
        ]);
    }
    // Método para actualizar un usuario
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        // Validaciones
        $validatedData = $request->validate([
            'apellido' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);
    
        // Actualizar datos del usuario
        $user->update([
            'apellido' => $validatedData['apellido'],
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => $validatedData['password'] ? Hash::make($validatedData['password']) : $user->password,
        ]);
    
        // Actualizar roles
        $user->roles()->sync($request->roles);
    
        // Retornar respuesta en formato JSON para AJAX
        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente.'
        ]);
    }
     // Método para mostrar el formulario de creación de un nuevo usuario
     public function create()
     {
         $roles = Role::all(); // Obtiene todos los roles
         $permissions = Permission::all(); // Obtiene todos los permisos
         return view('registro.create', compact('roles', 'permissions')); // Pasa los datos a la vista
     }
     // Validaciones y crear usuario
     public function store(Request $request)
     {
         // Validación de los datos del formulario
         $validatedData = $request->validate([
             'apellido' => 'required|string|max:255',
             'name' => 'required|string|max:255',
             'email' => 'required|email|unique:users,email',
             'password' => 'required|string|min:8|confirmed',
             'roles' => 'required|array', // Los roles son obligatorios
            //'permissions' => 'required|array', // Los permisos son obligatorios
         ]);
 
         // Crear el nuevo usuario
         $user = User::create([
             'name' => $validatedData['name'],
             'apellido' => $validatedData['apellido'],
             'email' => $validatedData['email'],
             'password' => Hash::make($validatedData['password']),
         ]);
 
         // Asignar roles y permisos al usuario
         $user->roles()->sync($validatedData['roles']);
        // $user->permissions()->sync($validatedData['permissions']);
 
         // Redirigir al usuario con un mensaje de éxito
         return redirect()->route('registro.index')->with('status', 'Usuario creado exitosamente.');
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
