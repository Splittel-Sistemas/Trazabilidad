<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenVentaTable extends Migration
{
    public function up()
    {
        Schema::create('OrdenVenta', function (Blueprint $table) {
            $table->id();
            $table->string('OrdenVenta');
            $table->string('NombreCliente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('OrdenVenta');
    }
}

