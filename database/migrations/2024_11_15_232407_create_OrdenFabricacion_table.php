<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenFabricacionTable extends Migration
{
    public function up()
    {
        Schema::create('OrdenFabricacion', function (Blueprint $table) {
            $table->id();
            $table->boolean('Escaner')->default(false);
            $table->unsignedBigInteger('OrdenVenta_id')
                    ->nullable();
            $table->string('OrdenFabricacion');
            $table->string('Articulo');
            $table->string('Descripcion');
            $table->integer('CantidadTotal');
            $table->date('FechaEntregaSAP');
            $table->date('FechaEntrega');
            $table->foreign('OrdenVenta_id')->references('id')
                    ->on('OrdenVenta')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('OrdenFabricacion');
    }
}
