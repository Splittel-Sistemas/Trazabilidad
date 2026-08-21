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
            $table->integer('of');
            $table->integer('ov');
            $table->integer('rp');
            $table->string('cardcode');
            $table->string('cardname');
            $table->integer('linenum');
            $table->string('itemcode');
            $table->string('dscription');
            $table->integer('quantity_transfer');
            $table->integer('quantity_receive');
            $table->string('batchnum')->nullable();
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
