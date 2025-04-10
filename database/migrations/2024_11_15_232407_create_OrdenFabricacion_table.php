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
            $table->unsignedBigInteger('Linea_id');
            $table->unsignedBigInteger('OrdenVenta_id')->nullable();
            $table->unsignedBigInteger('ResponsableUser_id')->nullable();
            $table->string('OrdenFabricacion');
            $table->string('Articulo');
            $table->string('Descripcion');
            $table->integer('CantidadTotal');
            $table->tinyInteger('EstatusEntrega')->default(0)->nullable();
            $table->tinyInteger('EstatusOF')->default(0)->nullable();
            $table->boolean('Cerrada')->default(true);
            $table->date('FechaEntregaSAP');
            $table->date('FechaEntrega');
            $table->boolean('Escaner')->default(false);
            $table->boolean('Corte')->default(false);
            $table->foreign('OrdenVenta_id')->references('id')->on('OrdenVenta');
            $table->foreign('ResponsableUser_id')->references('id')->on('users');
            $table->timestamps();
        
            // Definir la clave foránea correctamente
            //$table->foreign('Linea_id')->references('id')->on(DB::raw('`Linea`'))->onDelete('cascade');

        });
        
        
    }        

    public function down()
    {
        Schema::dropIfExists('OrdenFabricacion');
    }
}
