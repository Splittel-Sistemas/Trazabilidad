<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenfabricacion_prioridad', function (Blueprint $table) {
            $table->id();
            $table->integer('Posicion');
            $table->boolean('posicion')->default(false);
            $table->integer('OrdenFabricacion_id');
            /*$table->integer('OrdenFabricacion_id_down');*/
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('ordenfabricacion_prioridad');
    }
};
