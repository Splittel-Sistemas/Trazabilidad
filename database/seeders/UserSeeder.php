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

        $users = [
            ['name' => 'VANESSA GUADALUPE', 'apellido' => 'ENRIQUEZ GONZALEZ', 'role' => 'O'],
            ['name' => 'VALERIA', 'apellido' => 'RANGEL NIEVES ', 'role' => 'O'],
            ['name' => 'JANETH PATRICIA', 'apellido' => 'MOYA RAMIREZ', 'role' => 'O'],
            ['name' => 'DIEGO ALONSO', 'apellido' => 'ARAGON VARGAS', 'role' => 'O'],
            ['name' => 'ROSA MARIA', 'apellido' => 'VEGA TREJO', 'role' => 'O'],
            ['name' => 'ISABEL PORFIRIA', 'apellido' => 'GARCIA HIDALGO', 'role' => 'O'],
            ['name' => 'JESSICA', 'apellido' => 'LUNA OLVERA', 'role' => 'O'],
            ['name' => 'FRIDA SOFIA', 'apellido' => 'MARQUEZ RAMIREZ', 'role' => 'O'],
            ['name' => 'MAYRA KARINA', 'apellido' => 'PEREZ PADILLA', 'role' => 'O'],

            ['name' => 'ANA LILIA', 'apellido' => 'RICO AGUILAR', 'role' => 'O'],
            ['name' => 'VERONICA', 'apellido' => 'RAMIREZ SALAZAR', 'role' => 'O'],
            ['name' => 'JULIETA', 'apellido' => 'MEJIA AGUAS ', 'role' => 'O'],
            ['name' => 'ANA LUISA', 'apellido' => 'ROMERO AGUILAR', 'role' => 'O'],
            ['name' => 'SYLVIA PAULINA', 'apellido' => 'BAZAN ROJO', 'role' => 'O'],
            ['name' => 'CLAUDIA', 'apellido' => 'NOVOA BALDERAS', 'role' => 'O'],
            ['name' => 'MARIA DEL CARMEN', 'apellido' => 'GARCIA SANCHEZ ', 'role' => 'O'],
            ['name' => 'DULCE VIANEY', 'apellido' => 'VEGA EVANGELIO', 'role' => 'O'],
            ['name' => 'ANTONIO', 'apellido' => 'RODRIGUEZ HERNANDEZ', 'role' => 'O'],
            ['name' => 'PALMIRA KENNETH', 'apellido' => 'CARPIO CASTRO', 'role' => 'O'],
            ['name' => 'JUANA', 'apellido' => 'ESCAMILLA VAZQUEZ', 'role' => 'O'],
            ['name' => 'VERONICA', 'apellido' => 'BERENICE HARO RIVERA', 'role' => 'O'],
            ['name' => 'ANA PAOLA', 'apellido' => 'GUERRERO LICEA', 'role' => 'O'],
            ['name' => 'ANDREA', 'apellido' => 'PEREZ GRANADOS', 'role' => 'O'],
            ['name' => 'ERIKA', 'apellido' => 'GARCIA LEDESMA', 'role' => 'O'],
            ['name' => 'MARIA DEL ROSARIO', 'apellido' => 'LICEA TREJO', 'role' => 'O'],
            ['name' => 'VIANEY ARELY', 'apellido' => 'EVANGELIO GASCA', 'role' => 'O'],
            ['name' => 'LETICIA', 'apellido' => 'BAUTISTA RESENDIZ', 'role' => 'O'],
            ['name' => 'ROSAURA', 'apellido' => 'ARREGUIN GARCIA', 'role' => 'O'],
            ['name' => 'SARA JESSICA', 'apellido' => 'LOPEZ RANGEL', 'role' => 'O'],
            ['name' => 'MONICA', 'apellido' => 'HERNANDEZ OJEDA', 'role' => 'O'],
            ['name' => 'ALEJANDRO', 'apellido' => 'MEDINA SIMON', 'role' => 'O'],
            ['name' => 'MARIA DEL CARMEN', 'apellido' => 'HURTADO CURADO', 'role' => 'O'],
            ['name' => 'DIEGO', 'apellido' => 'NASUL ORDUÑA ARBALLO', 'role' => 'O'],
            ['name' => 'LAURA', 'apellido' => 'SANCHEZ LOPEZ', 'role' => 'O'],
            ['name' => 'MARIA DEL PUEBLITO', 'apellido' => 'VELAZQUEZ MENDOZA', 'role' => 'O'],
            ['name' => 'ELSA EDITH', 'apellido' => 'RESENDIZ MARTINEZ', 'role' => 'O'],
            ['name' => 'KARINA LUCIA', 'apellido' => 'MENDEZ TREJO', 'role' => 'O'],
            ['name' => 'BRENDA GABRIELA', 'apellido' => 'PANTOJA PANTOJA', 'role' => 'O'],
            ['name' => 'MARIA FERNANDA', 'apellido' => 'NOGUEZ PALOMINO', 'role' => 'O'],
            ['name' => 'ESMERALDA', 'apellido' => 'RAMIREZ HERNANDEZ', 'role' => 'O'],
            ['name' => 'VAANESSA', 'apellido' => 'CASTILLO OLVERA', 'role' => 'O'],
            ['name' => 'DALIA GALILEA', 'apellido' => 'CRUZ PEÑA', 'role' => 'O'],
            ['name' => 'Saira Alicia', 'apellido' => 'Martinez Garcia', 'role' => 'O'],
            ['name' => 'Ivonne', 'apellido' => 'Segura Martinez', 'role' => 'O'],
            ['name' => 'DIANA FABIOLA', 'apellido' => 'CASTAÑEDA NAVA', 'role' => 'O'],
            ['name' => 'VERONICA', 'apellido' => 'NIETO MARTINEZ', 'role' => 'O'],
            ['name' => 'JOSE JUAN', 'apellido' => 'MARTINEZ OLVERA', 'role' => 'O'],
            ['name' => 'VANESSA', 'apellido' => 'GOMEZ PEREZ', 'role' => 'O'],
            ['name' => 'YESICA SANDRA', 'apellido' => 'ORDAZ DOMINGUEZ', 'role' => 'O'],
            ['name' => 'BRENDA ISAURA', 'apellido' => 'PADILLA TREJO', 'role' => 'O'],
            ['name' => 'MIRIAM PAULINA', 'apellido' => 'MEDINA CALLEJAS', 'role' => 'O'],
            ['name' => 'MARIA PALOMA', 'apellido' => 'AGUILLON HERNANDEZ', 'role' => 'O'],
            ['name' => 'ALEJANDRA', 'apellido' => 'TORRES MATA', 'role' => 'O'],
            ['name' => 'MARIA JUANA', 'apellido' => 'ANGELES GONZALEZ', 'role' => 'O'],
            ['name' => 'MARIA GUADALUPE', 'apellido' => 'SALAZAR CARDENAS', 'role' => 'O'],
            ['name' => 'NANCY ALONDRA', 'apellido' => 'CRUZ PEÑA', 'role' => 'O'],
            ['name' => 'ADRIANA', 'apellido' => 'DURAN GUDIÑO', 'role' => 'O'],
            ['name' => 'NELLY MONSERRAT', 'apellido' => 'RAMIREZ PEÑA', 'role' => 'O'],
            ['name' => 'CINTYA GUADALUPE', 'apellido' => 'HERNANDEZ CASTELANO', 'role' => 'O'],
            ['name' => 'MARIA YAZMIN', 'apellido' => 'MORALES DE LEON', 'role' => 'O'],
            ['name' => 'MARIBEL', 'apellido' => 'RAMIREZ ORTIZ', 'role' => 'O'],
            ['name' => 'ESTELA', 'apellido' => 'RUBIO AGUILA', 'role' => 'O'],
            ['name' => 'ADRIANA MONSERRAT', 'apellido' => 'GONZALEZ GARCIA', 'role' => 'O'],
            ['name' => 'FATIMA LIZBETH', 'apellido' => 'ORTIZ HURTADO', 'role' => 'O'],
            ['name' => 'CORTES MARTINEZ', 'apellido' => 'ITZEL', 'role' => 'O'],
            ['name' => 'KARLA DOLORES', 'apellido' => 'MACIAS BARCENAS', 'role' => 'O'],
            ['name' => 'MARIA MONICA', 'apellido' => 'HERNANDEZ NIETO', 'role' => 'O'],
            ['name' => 'AMAYRANI', 'apellido' => 'RODRIGEZ LOPEZ', 'role' => 'O'],
            ['name' => 'LUZ ADELA', 'apellido' => 'BELLO OLIVAR', 'role' => 'O'],
            ['name' => 'KARLA ERIKA', 'apellido' => 'CRUZ MARTINEZ', 'role' => 'O'],
            ['name' => 'Ma FERNANDA', 'apellido' => 'LOPEZ ALCANTARA', 'role' => 'O'],
            ['name' => 'ANGEL JAVIER', 'apellido' => 'CASTILLO MARTINEZ', 'role' => 'O'],
            ['name' => 'KARLA CECILIA', 'apellido' => 'COLIN ESPINOZA', 'role' => 'O'],
            ['name' => 'BEATRIZ', 'apellido' => 'OLVERA MARTINEZ', 'role' => 'O'],
            ['name' => 'CECILIA', 'apellido' => 'GARCIA PAULINA', 'role' => 'O'],
            ['name' => 'CRUZ AMARANTA', 'apellido' => 'BENITEZ JIMENEZ', 'role' => 'O'],
            ['name' => 'VERONICA JOSE', 'apellido' => 'PADRON', 'role' => 'O'],
            ['name' => 'SUSANA', 'apellido' => 'SANCHEZ VILLANUEVA', 'role' => 'O'],
            ['name' => 'MARIA IVON', 'apellido' => 'AGUILLON AGUAS', 'role' => 'O'],
            ['name' => 'ANA LILIA', 'apellido' => 'MA. ESTELA MARTINEZ SALINAS', 'role' => 'O'],
            ['name' => 'CECILIA FRANCO', 'apellido' => 'PILAR', 'role' => 'O'],
            ['name' => 'JUANA', 'apellido' => 'GOMEZ PADILLA', 'role' => 'O'],
            ['name' => 'NORMA ELIA', 'apellido' => 'MEZA RAMIREZ', 'role' => 'O'],
            ['name' => 'IRMA', 'apellido' => 'CARRILLO RAMIREZ', 'role' => 'O'],
            ['name' => 'MARTHA PATRICIA', 'apellido' => 'RAMIREZ SALAZAR', 'role' => 'O'],
            ['name' => 'ANDREA', 'apellido' => 'MENDOZA HERNANDEZ', 'role' => 'O'],
            ['name' => 'FELICIA', 'apellido' => 'MEJIA AGUAS', 'role' => 'O'],
            ['name' => 'REMEDIOS ADILENE', 'apellido' => 'ACOSTA GUERRERO', 'role' => 'O'],
            ['name' => 'SONIA', 'apellido' => 'CAMACHO LOPEZ', 'role' => 'O'],
            ['name' => 'CRISTOPHER EDUARDO', 'apellido' => 'GOMEZ JIMENEZ', 'role' => 'O'],
            ['name' => 'CARLOS GUSTAVO', 'apellido' => 'MARQUEZ RAMIREZ', 'role' => 'O'],
            ['name' => 'IVONNE ALEJANDRA', 'apellido' => 'SANDOVAL GOMEZ', 'role' => 'O'],
            ['name' => 'MARIA ERNESTINA', 'apellido' => 'AGUILLON ROBLES', 'role' => 'O'],
            ['name' => 'JUANA DE SANTIAGO', 'apellido' => 'BALTAZAR', 'role' => 'O'],
            ['name' => 'LUCINA', 'apellido' => 'HERNANDEZ GARCIA', 'role' => 'O'],
            ['name' => 'MARIA DEL CARMEN', 'apellido' => 'POMPA MARTINEZ', 'role' => 'O'],
            ['name' => 'LETICIA', 'apellido' => 'SANCHEZ CHAVERO', 'role' => 'O'],
            ['name' => 'MA DE LOURDES', 'apellido' => 'AGUILLON HERNANDEZ', 'role' => 'O'],
            ['name' => 'SUSANA', 'apellido' => 'LUNA OLVERA', 'role' => 'O'],
            ['name' => 'MARIA PAOLA', 'apellido' => 'RAMIREZ HERNANDEZ', 'role' => 'O'],
            ['name' => 'MARITSA', 'apellido' => 'RAMIREZ SALAZAR', 'role' => 'O'],
            ['name' => 'DANIEL IVAN', 'apellido' => 'TREJO ALVARADO', 'role' => 'O'],
            ['name' => 'EVELYN', 'apellido' => 'MORALES ESCUDERO', 'role' => 'O'],
            ['name' => 'CARMINA', 'apellido' => 'GARCIA PEÑALOZA', 'role' => 'O'],
            ['name' => 'REYNA GUADALUPE', 'apellido' => 'GARCIA AGUILAR', 'role' => 'O'],
            ['name' => 'Dulce Fabiola', 'apellido' => 'MARTINEZ GARCIA', 'role' => 'O'],
            ['name' => 'MARIA NOHEMI', 'apellido' => 'FUENTES LINARES', 'role' => 'O'],
            ['name' => 'MARTINA', 'apellido' => 'BARCENAS HERRERA', 'role' => 'O'],
            ['name' => 'DAYSY', 'apellido' => 'ARIAS ORDUÑA', 'role' => 'O'],
            ['name' => 'MARIA DE LOURDES', 'apellido' => 'SEGOBIA BARCENAS', 'role' => 'O'],
            ['name' => 'GRACIELA', 'apellido' => 'VALDES TRISTHAN', 'role' => 'O'],
            ['name' => 'MARIA DOLORES', 'apellido' => 'OVIEDO NAVARRO', 'role' => 'O'],
            ['name' => 'MA. HERLINDA', 'apellido' => 'PALACIOS HERNANDEZ', 'role' => 'O'],
            ['name' => 'LUZ ALONDRA', 'apellido' => 'BRACAMONTES', 'role' => 'O'],
            ['name' => 'AMERICA ALEJANDRA', 'apellido' => 'MORALES', 'role' => 'O'],
            ['name' => 'MA. ELENA', 'apellido' => 'MARTINEZ RAMIREZ', 'role' => 'O'],
            ['name' => 'ANA CECILIA', 'apellido' => 'OLIVARES CANCHOLA', 'role' => 'O'],
            ['name' => 'DIANA PAOLA', 'apellido' => 'SANTOS AGUILLON', 'role' => 'O'],
            ['name' => 'CARLA DANIELA', 'apellido' => 'ROSAS ESPARZA', 'role' => 'O'],
            ['name' => 'SARAID', 'apellido' => 'VELAZQUEZ FABIAN', 'role' => 'O'],
            ['name' => 'MIRIAM', 'apellido' => 'VEGA MARTINEZ', 'role' => 'O'],
    ];
    foreach ($users as $index => $userData) {
        // Generar la contraseña secuencial y asegurarse de que tenga 5 dígitos con ceros a la izquierda
        $password = $this->generateSequentialPassword($index + 1); // Contraseña secuencial
    
        User::create([
            'name' => $userData['name'],
            'Apellido' => $userData['apellido'],
            'email' => $userData['name'].$password.'@splitel.com',
            'password' => $password, // Encriptar la contraseña
            'active' => '1',
            'role' => $userData['role'], // Asignamos el rol
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    }
    private function generateSequentialPassword($number)
    {
        // Formateamos el número a 5 dígitos con ceros a la izquierda
        return str_pad($number, 5, '0', STR_PAD_LEFT);
    }

}
