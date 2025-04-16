<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RolesPermisoController extends Controller
{
 
  
    
    public function index()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();
        if ($user->hasPermission('Vista Roles y Permisos')) {
            //$roles = Role::with('permissions')->get();
            $roles = Role::with(['permissions' => function($query) {
                $query->orderBy('name');  // Ordena los permisos por nombre alfabéticamente
            }])->get();
            $permissions1 = Permission::orderBy('name')->get();
            return view('RolesPermisos.index', compact('roles', 'permissions1'));
        } else {
            
            return redirect()->route('error.');
        }
    }

    /**
     * Mostrar el formulario para crear un nuevo rol o permiso.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name','desc')->get()->groupBy('groups');
        //$permissions = Permission::orderBy('name','desc')->get();

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
        $role = Role::with('permissions')->find($id); 
        $allPermissions = Permission::OrderBy('name')->get(); 
        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('id'), 
            'available_permissions' => $allPermissions, 
        ]);
    }

    /**
     * Actualizar un rol o permiso.
     */
    public function update(Request $request, $id)
    {
        // Buscar el rol a actualizar
        $role = Role::findOrFail($id);
        if($id==1){
            return redirect()->route('RolesPermisos.index')->with('error', 'Los roles del Administrador No pueden ser Modificados.');
        }
        // Validar y actualizar el nombre del rol
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id, // Asegurarse de no violar la unicidad
            'permissions' => 'array|exists:permissions,id', 
        ]);
        
        $role->name = $request->input('name');
        
        // Sincronizar los permisos con los valores de la solicitud
        $role->permissions()->sync($request->input('permissions', [])); // Sincroniza los permisos, si los hay

        // Guardar el rol actualizado
        $role->save();

        return redirect()->route('RolesPermisos.index')->with('status', 'Rol actualizado con éxito.');
    }

    /**
     * Eliminar un rol o permiso.
     */
    public function destroy(string $id)
    {
        // Lógica para eliminar un rol o permiso
    }
}
