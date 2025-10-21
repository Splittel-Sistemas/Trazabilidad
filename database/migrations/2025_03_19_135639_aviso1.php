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
        Schema::create('avisos', function (Blueprint $table) {
            $table->id(); // ID autoincrementable
            $table->string('titulo')->nullable(); // Ahora es opcional
            $table->text('contenido'); // Contenido del aviso
            $table->datetime('FechaInicio'); 
            $table->datetime('FechaFin');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avisos');
    }
};
