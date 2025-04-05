<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        Permission::create([
            'name' =>'Vista Dashboard',
            'groups' => 1, 
        ]);
        Permission::create([
            'name' =>'Vista Progreso',
            'groups' => 2, 
        ]);
        Permission::create([
            'name' =>'Vista Reportes',
            'groups' => 3,
        ]);
        Permission::create([
            'name' =>'Vista Planeacion',
            'groups' => 4,
        ]);
        Permission::create([
            'name' =>'Vista Corte',
            'groups' => 5, 
        ]);
        Permission::create([
            'name' =>'Vista Suministro',
            'groups' => 6, 
        ]);
        Permission::create([
            'name' =>'Vista Preparado',
            'groups' => 7, 
        ]);
        Permission::create([
            'name' =>'Vista Ensamble',
            'groups' => 8, 
        ]);
        Permission::create([
            'name' =>'Vista Pulido',
            'groups' => 9, 
        ]);
        Permission::create([
            'name' =>'Vista Medicion',
            'groups' => 16, 
        ]);
        Permission::create([
            'name' =>'Vista Visualizacion',
            'groups' => 10, 
        ]);
        Permission::create([
            'name' =>'Vista Empaquetado',
            'groups' => 11, 
        ]);
        Permission::create([
            'name' =>'Vista Usuarios',
            'groups' => 12, 
        ]);
        Permission::create([
            'name' =>'Vista Roles y permisos',
            'groups' => 13, 
        ]);
        Permission::create([
            'name' =>'Vista Lineas',
            'groups' => 14, 
        ]);

        Permission::create([
            'name' =>'Crear Usuario',
            'groups' => 12, 
        ]);

        Permission::create([
            'name' =>'Editar Usuario',
            'groups' => 12, 
        ]);

        Permission::create([
            'name' =>'Activar/Desactivar Usuario',
            'groups' => 12, 
        ]);

        Permission::create([
            'name' =>'Crear Linea',
            'groups' => 14, 
        ]);
        Permission::create([
            'name' =>'Editar Linea',
            'groups' => 14, 
        ]);
        Permission::create([
            'name' =>'Activar/Desactivar Linea',
            'groups' => 14, 
        ]);
        Permission::create([
            'name' =>'Crear Rol',
            'groups' => 13, 
        ]);
        Permission::create([
            'name' =>'Editar Rol',
            'groups' => 13, 
        ]);
        Permission::create([
            'name' => 'Enviar Avisos',
            'groups' => 15, 
        ]);
        Permission::create([
            'name' =>'Finalizar Trazabilidad',
            'groups' => 11, 
        ]);
        //Nuevas Areas
        Permission::create([
            'name' =>'Vista Transición',
            'groups' => 16, 
        ]);
        Permission::create([
            'name' =>'Vista Ribonizado',
            'groups' => 17, 
        ]);
        Permission::create([
            'name' =>'Vista Corte de fibra',
            'groups' => 18, 
        ]);
        Permission::create([
            'name' =>'Vista Montaje',
            'groups' => 19, 
        ]);
        Permission::create([
            'name' =>'Vista Inspección',
            'groups' => 20, 
        ]);
        Permission::create([
            'name' =>'Vista Polaridad',
            'groups' => 21, 
        ]);
        Permission::create([
            'name' =>'Vista Crimpado',
            'groups' => 22, 
        ]);
        Permission::create([
            'name' =>'Vista Montaje',
            'groups' => 22, 
        ]);
        Permission::create([
            'name' =>'Vista Armado',
            'groups' => 13, 
        ]);


/*
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
            'Finalizar Trazabilidad',
        ];   */  
        /*   
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }*/
    }
}
