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
        $personal = User::all(); // Obtiene todos los usuarios
        $roles = Role::all(); 
        $permissions = Permission::all();
        
        return view('registro.index', compact('personal', 'roles', 'permissions'));
    }

    // Método para mostrar un usuario específico
    public function show($id)
    {
        try {
            $registro = User::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('registro.index')->with('error', 'Usuario no encontrado.');
        }

        // Devolvemos la vista correcta con los datos del registro encontrado
        return view('registro.show', compact('registro')); // Asegúrate de que 'registro.show' es la vista correcta.
    }

    // Método para mostrar el formulario de edición de un usuario
    public function edit(User $registro)
    {
        $roles = Role::all();  // Obtener los roles
        $permissions = Permission::all();  // Obtener los permisos
    
        return view('registro.edit', compact('registro', 'roles', 'permissions'));
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
            //'roles' => 'required|array', // Los roles son obligatorios
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
        //$user->roles()->sync($validatedData['roles']);
       // $user->permissions()->sync($validatedData['permissions']);

        // Redirigir al usuario con un mensaje de éxito
        return redirect()->route('registro.index')->with('status', 'Usuario creado exitosamente.');
    }

    // Método para actualizar un usuario
    public function update(Request $solicitud, User $registro)
    {
        try {
            // Validar los datos
            $validatedData = $solicitud->validate([
                'name' => 'required|string|max:255',
                'apellido'=> 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $registro->id,
                'password' => 'nullable|string|min:8|confirmed', // Solo si se proporciona una nueva contraseña
                'roles' => 'nullable|array', // Roles opcionales
                'permisos' => 'nullable|array', // Permisos opcionales
            ]);
    
            // Si se proporciona una nueva contraseña, la encriptamos
            if ($solicitud->filled('password')) {
                $validatedData['password'] = Hash::make($solicitud->contraseña);
            }
    
            // Depuración: Ver los datos que se están actualizando
            Log::info('Datos validados para actualización: ', $validatedData);
    
            // Actualizar el registro con los datos validados
            $registro->update([
                'name' => $validatedData['name'],
                'apellido'=>$validatedData['apellido'],
                'email' => $validatedData['email'],
                'password' => $validatedData['password'] ?? $registro->password, // Solo actualiza si se ha proporcionado una nueva contraseña
            ]);
    
            // Sincronizar roles si se proporcionan
            if ($solicitud->has('roles')) {
                $registro->roles()->sync($solicitud->input('roles'));
            }
    
            // Sincronizar permisos si se proporcionan
            if ($solicitud->has('permisos')) {
                $registro->permissions()->sync($solicitud->input('permisos'));
            }
    
            // Redirigir con un mensaje de éxito
            return redirect()->route('registro.index')->with('status', 'Usuario actualizado exitosamente.');
    
        } catch (\Exception $e) {
            // En caso de error, registramos el mensaje de error
            Log::error('Error al actualizar el usuario: ' . $e->getMessage());
            
            // Retornamos un error general al usuario
            return redirect()->route('registro.index')->with('error', 'Hubo un error al actualizar el usuario.');
        }
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
