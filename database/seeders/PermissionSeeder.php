<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        //Permisos por modúlo
        //Dahboard
            Permission::create([
                'name' =>'Vista Dashboard',
                'groups' => 1, 
            ]);
        //Busqueda
            Permission::create([
                'name' =>'Vista Progreso',
                'groups' => 2, 
            ]);
            Permission::create([
                'name' =>'Vista Estatus Orden F.',
                'groups' => 2, 
            ]);

        //Reportes
            Permission::create([
                'name' =>'Vista Reportes',
                'groups' => 3,
            ]);
        //Estaciones
            //Planeación
            Permission::create([
                'name' =>'Vista Planeación',
                'groups' => 4,
            ]);
            //Corte
            Permission::create([
                'name' =>'Vista Corte',
                'groups' => 5, 
            ]);
            //Suministro
            Permission::create([
                'name' =>'Vista Suministro',
                'groups' => 6, 
            ]);
            //Asignacion
            Permission::create([
                'name' =>'Vista Asignación',
                'groups' => 7, 
            ]);
            //Gestor de Ordenes
            Permission::create([
                'name' =>'Vista Gestor de ordenes',
                'groups' => 8, 
            ]);
            //Ordnes en Linea
            Permission::create([
                'name' =>'Vista Ordenes en Línea',
                'groups' => 9, 
            ]);
            //Empaque
            Permission::create([
                'name' =>'Vista Empaque',
                'groups' => 10, 
            ]);
            Permission::create([
                'name' =>'Cancelar partida',
                'groups' => 10, 
            ]);
        //Usuarios
            Permission::create([
                'name' =>'Vista Usuarios',
                'groups' => 11, 
            ]);
            Permission::create([
                'name' =>'Crear Usuario',
                'groups' => 11, 
            ]);

            Permission::create([
                'name' =>'Editar Usuario',
                'groups' => 11, 
            ]);

            Permission::create([
                'name' =>'Activar/Desactivar Usuario',
                'groups' => 11, 
            ]);

        //Roles y permisos
            Permission::create([
                'name' =>'Vista Roles y permisos',
                'groups' => 12, 
            ]);
            Permission::create([
                'name' =>'Crear Rol',
                'groups' => 12, 
            ]);
            Permission::create([
                'name' =>'Editar Rol',
                'groups' => 12, 
            ]);
        //Lineas
            Permission::create([
                'name' =>'Vista Lineas',
                'groups' => 13, 
            ]);
            Permission::create([
                'name' =>'Crear Linea',
                'groups' => 13, 
            ]);
            Permission::create([
                'name' =>'Editar Linea',
                'groups' => 13, 
            ]);
            Permission::create([
                'name' =>'Activar/Desactivar Linea',
                'groups' => 13, 
            ]);
        //Permisos generales
        Permission::create([
            'name' => 'Enviar Avisos',
            'groups' => 14, 
        ]);
        Permission::create([
            'name' =>'Finalización Trazabilidad',
            'groups' => 15, 
        ]);
        //Etiquetas
            Permission::create([
                'name' =>'Vista Etiquetas',
                'groups' => 16, 
            ]);
        //Nuevas Areas
        /*Permission::create([
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
            'name' =>'Vista Clasificación',
            'groups' => 23, 
        ]);
          Permission::create([
            'name' =>'Vista Transición',
            'groups' => 24, 
        ]);
          Permission::create([
            'name' =>'Vista Estatus Orden Fabricación',
            'groups' => 25, 
        ]);
         Permission::create([
            'name' =>'Vista Armado',
            'groups' => 26, 
        ]);
        Permission::create([
            'name' =>'Vista Etiquetas',
            'groups' => 27, 
        ]);*/
                /*Permission::create([
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
        ]);*/
    }
}
