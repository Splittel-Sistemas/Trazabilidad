<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('CatalogoOpciones', function (Blueprint $table) {
            $table->id();
            $table->string('Nombre');
            $table->timestamps();
        });
        DB::table('areas')->insert([
            ['Nombre' => 'Abierto'],
            ['Nombre' => 'Cerrado'],
            ['Nombre' => 'Retrabajo'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('CatalogoOpciones');
    }
};
