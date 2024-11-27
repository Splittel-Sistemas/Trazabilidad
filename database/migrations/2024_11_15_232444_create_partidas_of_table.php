<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidasOfTable extends Migration
{
    public function up()
    {
        Schema::create('partidas_of', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_fabricacion_id')->constrained('orden_fabricacion')->onDelete('cascade'); 
            $table->string('codigo_partida');
            $table->integer('cantidad');
            $table->decimal('costo_unitario', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('partidas_of');
    }
}
