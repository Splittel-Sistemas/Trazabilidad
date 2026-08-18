<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_layer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('traslado_id');
            $table->foreign('traslado_id')->references('id')->on('traslados');
            $table->json('response');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_layer');
    }
};
