<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenVentaTable extends Migration
{
    public function up()
    {
        Schema::create('orden_venta', function (Blueprint $table) {
            $table->id();
            $table->string('numero_orden');
            $table->date('fecha_venta');
            $table->string('cliente');
            $table->decimal('total_venta', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orden_venta');
    }
}
