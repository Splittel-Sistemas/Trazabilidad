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
use Illuminate\Support\Facades\DB;


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

         

    public function tablaPrincipal()
    {
        $tabla = DB::table('users')
            ->select('users.apellido', 'users.name', 'users.email', 'users.password', 'users.role', 'users.active')
            ->get();
    
        // Aquí puedes realizar la comparación si necesitas verificar alguna contraseña
        // Pero recuerda, la contraseña no se puede desencriptar, solo comparar.
        foreach ($tabla as $user) {
            // Si quisieras verificar una contraseña en particular, podrías hacerlo así:
            $passwordCorrecta = Hash::check('contraseñaIntentada', $user->password);
            
            // Esto devolverá un valor booleano dependiendo de si la contraseña coincide
            if ($passwordCorrecta) {
                // Hacer algo si las contraseñas coinciden
            }
        }
    
        return response()->json($tabla);
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

    public function storeoperador(Request $request)
    {// Validación de los datos del formulario
    $validatedData = $request->validate([
        'apellido' => 'required|string|max:255',
        'name' => 'required|string|max:255',
    ]);

    // Generación de la clave y el correo
    $clave = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT); 
    $email = $clave . '@splitel.com'; 

    // Verificación de que el correo no exista
    while (User::where('email', $email)->exists()) {
        $clave = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
        $email = $clave . '@splitel.com';
    }

    // Creación del usuario
    $user = new User();
    $user->name = $validatedData['name'];
    $user->apellido = $validatedData['apellido'];
    $user->email = $email;
    $user->password = $clave;  // Aquí no se encripta
    $user->role = 'O';
    $user->save();

    return redirect()->route('registro.index')->with('status', 'Usuario creado exitosamente.');
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
             $user = User::create([
                 'name' => $validatedData['name'],
                 'apellido' => $validatedData['apellido'],
                 'email' => $validatedData['email'],
                 'password' => Hash::make($validatedData['password']),
                 'role' => 'A', 
             ]);
         
      
             $user->roles()->sync($validatedData['roles']); 
         

             return redirect()->route('registro.index')->with('status', 'Usuario creado exitosamente.');
         }
    
    

}
