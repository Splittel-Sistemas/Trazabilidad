<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'Ver dashboard',
            'Gestionar usuarios',
            'Ver informes',
            'Editar contenido',
            'Vista Cortes',
            'Vista Planeacion',
            'Vista Suministro',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
