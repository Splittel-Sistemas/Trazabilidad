<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenVentaTable extends Migration
{
     public function up()
    {
        Schema::create('orden_venta', function (Blueprint $table) {
            $table->string('barcode')->unique()->nullable();
            $table->id();
            $table->string('orden_fab');
            $table->string('articulo');
            $table->string('descripcion');
            $table->decimal('cantidad_of', 10, 2);
            $table->date('fecha_entrega');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orden_venta');
    }
}

