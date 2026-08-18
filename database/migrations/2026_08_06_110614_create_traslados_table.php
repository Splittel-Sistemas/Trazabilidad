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
            $table->enum('estado', ['Generado', 'Parcial', 'Recibido', 'Cancelado'])->default('generado');
            $table->unsignedBigInteger('usuario_traslado_id');
            $table->foreign('usuario_traslado_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_recive_id')->nullable();
            $table->foreign('usuario_recive_id')->references('id')->on('users');
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
