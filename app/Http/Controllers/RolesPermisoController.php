<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Log;

class RolesPermisoController extends Controller
{
 
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
        $role = Role::with('permissions')->findOrFail($id); 
        $allPermissions = Permission::all(); 

        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('id'), 
            'available_permissions' => $allPermissions, 
        ]);
    }
    public function update(Request $request, $id)
{
    // Buscar el rol a actualizar
    $role = Role::findOrFail($id);

    // Actualizar el nombre del rol
    $role->name = $request->input('name');

    // Sincronizar los permisos con los valores de la solicitud
    $role->permissions()->sync($request->input('permissions', []));

    // Guardar el rol actualizado
    $role->save();

    return response()->json([
        'success' => true,
        'message' => 'Rol actualizado con éxito.',
    ]);
}

    /**
     * Eliminar un rol o permiso.
     */
    public function destroy(string $id)
    {
    }

}
