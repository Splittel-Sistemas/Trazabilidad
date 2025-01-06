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
        // Crear permisos si no existen
        $verDashboard = Permission::firstOrCreate(['name' => 'Ver dashboard']);
        $gestionarUsuarios = Permission::firstOrCreate(['name' => 'Gestionar usuarios']);
        $verInformes = Permission::firstOrCreate(['name' => 'Ver informes']);
        $editarContenido = Permission::firstOrCreate(['name' => 'Editar contenido']);

        // Crear roles
        $admin = Role::create(['name' => 'Administrador']);
        $supervisor = Role::create(['name' => 'Supervisor']);
        $operador = Role::create(['name' => 'Operador']);

        // Asignar permisos a roles
        $admin->permissions()->attach([
            $verDashboard->id, 
            $gestionarUsuarios->id, 
            $verInformes->id, 
            $editarContenido->id
        ]);
        
        $supervisor->permissions()->attach([
            $verDashboard->id, 
            $gestionarUsuarios->id, 
            $verInformes->id
        ]);
        
        $operador->permissions()->attach([
            $verDashboard->id, 
            $verInformes->id
        ]);
    }
}

