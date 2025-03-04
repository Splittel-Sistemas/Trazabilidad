<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidasOFTable extends Migration
{
    public function up()
    {
        Schema::create('PartidasOF', function (Blueprint $table) { 
            //Estatus 0 igual a abierta; 1 igual a cerrada
            $table->id();
            $table->foreignId('OrdenFabricacion_id') 
                ->constrained('OrdenFabricacion') 
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('cantidad_partida');
            $table->integer('NumeroPartida')->nullable();
            $table->string('TipoPartida',1)->nullable();
            $table->dateTime('FechaFabricacion');
            $table->string('EstatusPartidaOF')->default(0)->nullable(); //cerrado= 0 abierto=1
            $table->datetime('FechaComienzo')->nullable();
            $table->datetime('FechaFinalizacion')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('PartidasOF');
    }
}
