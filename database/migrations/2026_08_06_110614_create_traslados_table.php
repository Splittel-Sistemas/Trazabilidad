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
        Schema::create('traslados', function (Blueprint $table) {
            $table->id();
            $table->integer('referencia_sap');
            $table->enum('estado', ['generado', 'recibido', 'cancelado'])->default('generado');
            $table->integer('id_usuario_traslado');
            //$table->foreign('id_usuario_traslado')->references('users')->on('id');
            $table->integer('id_usuario_recive')->nullable();
            //$table->foreign('id_usuario_recive')->references('users')->on('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traslados');
    }
};
