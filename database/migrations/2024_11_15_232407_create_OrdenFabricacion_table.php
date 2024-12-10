<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenFabricacionTable extends Migration
{
    public function up()
    {
        Schema::create('orden_fabricacion', function (Blueprint $table) { 
            $table->id();
            $table->unsignedBigInteger('orden_venta_id')->nullable(); 
            $table->string('orden_fabricacion');
            $table->string('articulo');
            $table->string('descripcion');
            $table->integer('cantidad_total');
            $table->date('fecha_entrega');

            
            $table->foreign('orden_venta_id')
                ->references('id')
                ->on('orden_venta') 
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orden_fabricacion');
    }
}
