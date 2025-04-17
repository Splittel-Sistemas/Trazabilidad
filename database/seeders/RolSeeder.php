<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class RolSeeder extends Seeder
{
    public function run()
    {
        Role::create([
            'name' => 'ADMINISTRADOR',
            'created_at' => now(), 
            'updated_at' => now()
        ]);
        Role::create([
            'name' => 'OPERADOR',
            'created_at' => now(), 
            'updated_at' => now()
        ]);
    }
}
