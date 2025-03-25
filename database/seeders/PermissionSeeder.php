<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'CompletadosEdit',
            'Vistas Editar',
            'RolesEdit',
            'Dashboard',
            'Vista Cortes',
            'Vista Planeacion',
            'Vista Suministro',
            'Vista Preparado',
            'Vista Ensambre',
            'Vista Pulido',
            'Vista Medicion',
            'Vista Visualizacion',
            'Vista Ver',
            'PlaneacionEdit',
            'UsuriosEdit',
            'CorteEdit',
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
            'Vista Roles y Permisos',
            'Editar Dashboard',
            'Editar Progreso',
            'Editar Reportes',
            'Editar Planeacion',
            'Editar Corte',
            'Editar Suministro',
            'Editar Preparado',
            'Editar Ensamble',
            'Editar Pulido',
            'Editar Medicion',
            'Editar Visualizacion',
            'Editar Empaquetado',
            'Editar Usuarios',
            'Editar Roles y Permisos',
            'Crear Progreso',
            'Crear Reportes',
            'Crear Planeacion',
            'Crear Corte',
            'Crear Suministro',
            'Crear Preparado',
            'Crear Ensamble',
            'Crear Pulido',
            'Crear Medicion',
            'Crear Visualizacion',
            'Crear Empaquetado',
            'Crear Usuarios',
            'Crear Roles y Permisos',
            'Eliminar Progreso',
            'Eliminar Reportes',
            'Eliminar Planeacion',
            'Eliminar Corte',
            'Eliminar Suministro',
            'Eliminar Preparado',
            'Eliminar Ensamble',
            'Eliminar Pulido',
            'Eliminar Medicion',
            'Eliminar Visualizacion',
            'Eliminar Empaquetado',
            'Eliminar Usuarios',
            'Eliminar Roles y Permisos',
            'Enviar Avisos',
            'Vista Busqueda',
        ];        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
