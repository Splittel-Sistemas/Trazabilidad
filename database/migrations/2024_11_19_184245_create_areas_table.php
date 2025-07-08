<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAreasTable extends Migration
{
     public function up()
    {
        Schema::create('Areas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
        });
        // Insertar los datos de las áreas
        DB::table('areas')->insert([
            ['nombre' => 'Planeacion'],//1
            ['nombre' => 'Corte'],//2
            ['nombre' => 'Suministro'],//3
            ['nombre' => 'Transición'],//4
            ['nombre' => 'Preparado'],//5
            ['nombre' => 'Ribonizado'],//6
            ['nombre' => 'Ensamble'],//7
            ['nombre' => 'Corte de fibra'],//8
            ['nombre' => 'Pulido'],//9
            ['nombre' => 'Armado'],//10
            ['nombre' => 'Inspeccion'],//11
            ['nombre' => 'Polaridad'],//12
            ['nombre' => 'Crimpado'],//13
            ['nombre' => 'Medición'],//14
            ['nombre' => 'Visual'],//15
            ['nombre' => 'Montaje'],//16 //AreaFinal
            ['nombre' => 'Empaque'],//17 //AreaFinal
            ['nombre' => 'Clasificación'],//18 //AreaFinal
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('Areas');
    }
}
