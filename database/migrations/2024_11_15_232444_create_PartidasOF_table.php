<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidasOfTable extends Migration
{
    public function up()
    {
        Schema::create('PartidasOF', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('OrdenFabricacion_id');
            $table->integer('CantidadPartida');
            $table->date('FechaFabricacion');
            $table->foreign('OrdenFabricacion_id')->references('id')
                    ->on('OrdenFabricacion')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('PartidasOF');
    }
}
