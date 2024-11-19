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
            $table->foreignId('orden_venta_id')->constrained('orden_venta')->onDelete('cascade'); // Relación con orden_venta
            $table->string('numero_fabricacion');
            $table->date('fecha_fabricacion');
            $table->string('estado');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orden_fabricacion');
    }
}
