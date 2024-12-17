<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidasOFTable extends Migration
{
    public function up()
    {
        Schema::create('PartidasOF', function (Blueprint $table) { 
            $table->id();
            $table->foreignId('OrdenFabricacion_id') 
                ->constrained('OrdenFabricacion') 
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->integer('cantidad_partida');
            $table->dateTime('fecha_fabricacion');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('PartidasOF');
    }
}
