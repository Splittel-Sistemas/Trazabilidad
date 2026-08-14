<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('traslado_detalles', function (Blueprint $table) {
            $table->id();
            $table->integer('id_traslado');
            //$table->foreign('id_traslado')->references('id')->on('traslados');
            $table->string('codigo_producto');
            $table->integer('cantidad_traslado');
            $table->integer('cantidad_recepcion');
            $table->string('lote', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traslado_detalles');
    }
};
