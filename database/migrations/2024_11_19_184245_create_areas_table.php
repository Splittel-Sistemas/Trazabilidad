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
            ['nombre' => 'Planeacion'],
            ['nombre' => 'Corte'],
            ['nombre' => 'Suministro'],
            ['nombre' => 'Transición'],
            ['nombre' => 'Preparado'],
            ['nombre' => 'Ribonizado'],
            ['nombre' => 'Ensamble'],
            ['nombre' => 'Corte de fibra'],
            ['nombre' => 'Pulido'],
            ['nombre' => 'Armado'],
            ['nombre' => 'Inspeccion'],
            ['nombre' => 'Polaridad'],
            ['nombre' => 'Crimpado'],
            ['nombre' => 'Medición'],
            ['nombre' => 'Visualización'],
            ['nombre' => 'Montaje'],
            ['nombre' => 'Empaque'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('Areas');
    }
}
