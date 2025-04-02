<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrador',
            'Apellido' => 'Splittel',
            'email' => 'AdministradorSplittel@gmail.com',
            'password' => Hash::make('12345678'), // Encriptar la contraseña
            'active' => '1',
            'role' => 'A', // Puedes asignar un rol, como 'A' o 'O'
            'created_at' => now(), 
            'updated_at' => now()
        ]);
          // Nuevo usuario
          User::create([
            'name' => 'Operador',
            'Apellido' => 'Splittel',
            'email' => 'OperadorSplittel@gmail.com',
            'password' => Hash::make('12345678'), // Otra contraseña encriptada
            'active' => '1',
            'role' => 'O', // Otro rol
            'created_at' => now(), 
            'updated_at' => now()
        ]);
    }

}
