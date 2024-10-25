<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        Permission::create(['name' => 'edit_posts', 'description' => 'Permite editar publicaciones']);
        Permission::create(['name' => 'delete_posts', 'description' => 'Permite eliminar publicaciones']);
        // Agrega más permisos según sea necesario
    }
}

