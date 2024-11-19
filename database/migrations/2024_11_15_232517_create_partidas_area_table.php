<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidasAreaTable extends Migration
{
    public function up()
    {
        Schema::create('partidas_area', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partida_of_id')->constrained('partidas_of')->onDelete('cascade'); // Relación con partidas_of
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade'); // Relación con areas
            $table->string('actividad');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('partidas_area');
    }
}
