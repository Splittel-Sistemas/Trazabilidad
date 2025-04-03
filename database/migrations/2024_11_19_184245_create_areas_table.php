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
            ['nombre' => 'planeacion'],
            ['nombre' => 'corte'],
            ['nombre' => 'suministro'],
            ['nombre' => 'transición'],
            ['nombre' => 'preparado'],
            ['nombre' => 'ensamble'],
            ['nombre' => 'pulido'],
            ['nombre' => 'medición'],
            ['nombre' => 'visualización'],
            ['nombre' => 'montaje'],
            ['nombre' => 'empaque'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('Areas');
    }
}
