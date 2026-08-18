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
        Schema::create('traslado_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('traslado_id');
            $table->foreign('traslado_id')->references('id')->on('traslados');
            $table->integer('docnum');
            $table->integer('linenum');
            $table->string('itemcode');
            $table->integer('quantity_transfer');
            $table->integer('quantity_receive');
            $table->string('batchnum')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traslado_detalles');
    }
};
