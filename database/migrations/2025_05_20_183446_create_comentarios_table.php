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
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            $table->integer('OrdenFabricacion_id');
            $table->unsignedBigInteger('Partida_id');
            $table->unsignedBigInteger('Areas_id');
            $table->unsignedBigInteger('Usuario_id');
            $table->date('Fecha');
            $table->text('Comentario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
