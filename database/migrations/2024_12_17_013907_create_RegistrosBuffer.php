<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        Schema::create('RegistrosBuffer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('FechasBuffer_id')
                    ->nullable();
            $table->string('OrdenVentaB');
            $table->string('OrdenFabricacionB');
            $table->string('NumeroLineaB');
            $table->foreign('FechasBuffer_id')->references('id')
                ->on('FechasBuffer')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('RegistrosBuffer');
    }
};
