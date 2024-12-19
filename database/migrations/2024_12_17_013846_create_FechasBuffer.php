<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->timestamps();
        });
        // Insertar un registro en la tabla
        $fecha=date('Y-m-d');
        DB::table('FechasBuffer')->insert([
            'FechaRegistrosPendientes' => $fecha,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('FechasBuffer');
    }
};
