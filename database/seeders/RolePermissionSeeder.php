<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos si no existen
        $verDashboard = Permission::firstOrCreate(['name' => 'Dashboard']);
        $verCortes = Permission::firstOrCreate(['name' => 'Vista Cortes']);
        $verPlaneacion = Permission::firstOrCreate(['name' => 'Vista Planeacion']);
        $verSuministro = Permission::firstOrCreate(['name' => 'Vista Suministro']);
        $verPreparado = Permission::firstOrCreate(['name' => 'Vista Preparado']);
        $verEnsambre = Permission::firstOrCreate(['name' => 'Vista Ensambre']);
        $verPulido = Permission::firstOrCreate(['name' => 'Vista Pulido']);
        $verMedicion = Permission::firstOrCreate(['name' => 'Vista Medicion']);
        $verVisualizacion = Permission::firstOrCreate(['name' => 'Vista Visualizacion']);

        // Crear roles si no existen
        $personalAdministrativo = Role::firstOrCreate(['name' => 'Administrador']);
        $personalCortes = Role::firstOrCreate(['name' => 'Cortes']);
        $personalPlaneacion = Role::firstOrCreate(['name' => 'Planeacion']);
        $personalSuministros = Role::firstOrCreate(['name' => 'Suministros']);
        $personalPreparado = Role::firstOrCreate(['name' => 'Preparado']);
        $personalEnsamble = Role::firstOrCreate(['name' => 'Ensamble']);
        $personalPulido = Role::firstOrCreate(['name' => 'Pulido']);
        $personalMedicion = Role::firstOrCreate(['name' => 'Medicion']);
        $personalVisualizacion = Role::firstOrCreate(['name' => 'Visualizacion']);

        // Asignar permisos a roles utilizando givePermissionTo
        $personalAdministrativo->givePermissionTo([
            $verDashboard,
            $verVisualizacion,
            $verMedicion,
            $verPulido,
            $verEnsambre,
            $verPreparado,
            $verCortes,
            $verPlaneacion,
        ]);

        $personalVisualizacion->givePermissionTo([
            $verDashboard,
            $verVisualizacion,
        ]);

        $personalMedicion->givePermissionTo([
            $verDashboard,
            $verMedicion,
        ]);

        $personalPulido->givePermissionTo([
            $verDashboard,
            $verPulido,
        ]);

        $personalEnsamble->givePermissionTo([
            $verDashboard,
            $verEnsambre,
        ]);

        $personalPreparado->givePermissionTo([
            $verDashboard,
            $verPreparado,
        ]);

        $personalCortes->givePermissionTo([
            $verDashboard,
            $verCortes,
        ]);

        $personalPlaneacion->givePermissionTo([
            $verDashboard,
            $verPlaneacion,
        ]);

        $personalSuministros->givePermissionTo([
            $verDashboard,
            $verSuministro,
        ]);
    }
}
