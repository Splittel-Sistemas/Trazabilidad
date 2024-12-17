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
        Schema::create('RegistroBuffer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('RegistroBuffer_id')
                    ->nullable();
            $table->string('OrdenVentaB');
            $table->string('OrdenFabricacionB');
            $table->foreign('RegistroBuffer_id')->references('id')
                ->on('FechasBuffer')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('RegistroBuffer');
    }
};
