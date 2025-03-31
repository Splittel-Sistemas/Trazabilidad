<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RolSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrador',
            'created_at' => now(), 
            'updated_at' => now()
        ]);
        User::create([
            'name' => 'Operador',
            'created_at' => now(), 
            'updated_at' => now()
        ]);
    }
}
