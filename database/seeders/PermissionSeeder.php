<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'Vista Dashboard',
            'Vista Progreso',
            'Vista Reportes',
            'Vista Planeacion',
            'Vista Corte',
            'Vista Suministro',
            'Vista Preparado',
            'Vista Ensamble',
            'Vista Pulido',
            'Vista Medicion',
            'Vista Visualizacion',
            'Vista Empaquetado',
            'Vista Usuarios',
            'Vista Roles y permisos',
            'Vista Lineas',
            'Crear Usuario',
            'Editar Usuario',
            'Activar/Desactivar Usuario',
            'Crear Linea',
            'Editar Linea',
            'Activar/Desactivar Linea',
            'Crear Rol',
            'Editar Rol',
            'Enviar Avisos',
        ];        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
