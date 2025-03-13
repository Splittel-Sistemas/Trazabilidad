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
    Schema::table('OrdenFabricacion', function (Blueprint $table) {
        $table->foreign('Linea_id')->references('id')->on('Linea')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('OrdenFabricacion', function (Blueprint $table) {
        $table->dropForeign(['Linea_id']);
    });
}

};
