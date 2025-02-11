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
        Schema::create('PorcentajePlaneacion', function (Blueprint $table) {
            $table->id();
            $table->integer('NumeroPersonas')->nullable();
            $table->date('FechaPlaneacion');
            $table->integer('CantidadPlaneada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PorcentajePlaneacion');
    }
};
