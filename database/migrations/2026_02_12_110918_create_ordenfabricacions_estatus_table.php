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
        Schema::create('ordenfabricacion_estatus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('OrdenFabricacion_id')->nullable();
            $table->integer('id_user')->nullable();
            $table->text('comentario')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenfabricacion_estatus');
    }
};
