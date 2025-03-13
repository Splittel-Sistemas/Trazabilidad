<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('PorcentajePlaneacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Linea_id');
            $table->integer('NumeroPersonas')->nullable();
            $table->date('FechaPlaneacion');
            $table->integer('CantidadPlaneada');
            $table->timestamps();

            $table->foreign('Linea_id')->references('id')->on('Linea')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('PorcentajePlaneacion');
    }
};
