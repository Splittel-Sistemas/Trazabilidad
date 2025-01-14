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
        Schema::create('Partidas_Areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Partidas_id');
            $table->unsignedBigInteger('Areas_id');
            $table->unsignedBigInteger('Users_id');
            $table->unsignedBigInteger('Linea_id');
            $table->foreign('Partidas_id')->references('id')->on('Partidas')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('Areas_id')->references('id')->on('Areas')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('Users_id')->references('id')->on('Users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('Linea_id')->references('id')->on('Linea')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('Partidas_Areas');
    }
};
