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
        Schema::create('Linea', function (Blueprint $table) {
            $table->id();
            $table->integer('NumeroLinea')->nullable();
            $table->string('Nombre'); 
            $table->string('ColorLinea',15);
            $table->boolean('active')->default(true);
            $table->text('Descripcion')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Linea');
    }
};
