<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Log;

class RolesPermisoController extends Controller
{
    /**
     * Mostrar una lista de roles y permisos.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('RolesPermisos.index', compact('roles', 'permissions'));
    }
    /**
     * Mostrar el formulario para crear un nuevo rol o permiso.
     */
    public function create()
    {
        $permissions = Permission::all();

        return view('RolesPermisos.create', compact('permissions'));
    }
    /**
     * Almacenar un nuevo rol o permiso.
     */
    public function store(Request $request)
    {
       
        $request->validate([
            'nombre' => 'required|string|unique:roles,name', 
            'permissions' => 'array|exists:permissions,id', 
        ]);

        $role = Role::create(['name' => $request->nombre]); 

    
        if ($request->has('permissions') && is_array($request->permissions)) {
        
            $role->permissions()->sync($request->permissions); 
        }

        return redirect()->route('RolesPermisos.index')->with('success', 'Rol creado con éxito.');
    }
    /**
 * Mostrar el formulario para editar un rol o permiso.
 */
public function edit($id)
{
    $role = Role::with('permission')->findOrFail($id); // Nota el singular en 'permission'
    $permissions = Permission::all(); // Obtienes todos los permisos disponibles
    

    return response()->json([
        'name' => $role->name,
        'permission' => $permissions, // Se pasa la lista completa de permisos disponibles
        'assigned_permissions' => $role->permission ? $role->permission->pluck('name') : [], // Pluck para obtener solo los IDs de los permisos asignados
    ]);
}

/**
 * Actualizar un rol o permiso existente.
 */
public function update(Request $request, string $id)
{
    try {
        // Validar los datos
        $validatedData = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'permissions' => 'nullable|array|exists:permissions,id',
        ]);

        $role = Role::findOrFail($id); 

        $role->update(['name' => $validatedData['name']]);

        if ($request->has('permission')) {
            $role->permission()->sync($validatedData['permission']);
        } else {
            $role->permissions()->sync([]); 
        }

        return redirect()->route('RolesPermisos.index')->with('success', 'Rol actualizado con éxito.');
    } catch (\Exception $e) {
       
        Log::error('Error al actualizar rol o permiso: ' . $e->getMessage());
        
  
        return redirect()->route('RolesPermisos.index')->with('error', 'Hubo un error al actualizar el rol o permiso.');
    }
}

    /**
     * Eliminar un rol o permiso.
     */
    public function destroy(string $id)
    {
    }

}
