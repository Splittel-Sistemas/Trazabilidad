<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        Schema::create('FechasBuffer', function (Blueprint $table) {
            $table->id();
            $table->date('FechaRegistrosPendientes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('FechasBuffer');
    }
};
