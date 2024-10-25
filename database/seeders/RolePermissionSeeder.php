<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {

        // Crear rol y permiso
        $adminRole = Role::create(['name' => 'admin']);
        $editArticlesPermission = Permission::create(['name' => 'edit articles']);

        // Asociar permiso con rol
        $adminRole->permissions()->attach($editArticlesPermission);
          // Asignar rol a un usuario (cambia el ID según el usuario deseado)
          $user = User::find(1); // Asegúrate de que exista
          $user->roles()->attach($adminRole);
    }
}
