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
        Schema::create('Emision', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('OrdenFabricacion_id')
                    ->nullable();
            $table->integer('NumEmision');
            $table->unsignedBigInteger('Etapaid');
            $table->string('EtapaEmision',1);
            $table->foreign('OrdenFabricacion_id')->references('id')
                    ->on('OrdenFabricacion')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Emision');
    }
};
