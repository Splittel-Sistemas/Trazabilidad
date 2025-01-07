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
        Schema::create('Partidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('PartidasOF_id') 
                ->constrained('PartidasOF') 
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('NumParte')->nullable();
            $table->integer('Estatus')->nullable();
            $table->integer('TipoAccion')->nullable();
            $table->integer('CantidadaPartidas')->nullable();
            $table->datetime('FechaComienzo')->nullable();
            $table->datetime('FechaTermina')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Partidas');
    }
};
