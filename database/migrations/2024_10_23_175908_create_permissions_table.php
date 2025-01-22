<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    
    {
        // Agregar columnas a la tabla permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
        });
        DB::table('permissions')->insert([
            ['name' => 'CompletadosEdit'],
            ['name' => 'Vistas Editar'],
            ['name' => 'RolesEdit'],
            ['name' => 'Dashboard'],
            ['name' => 'Vista Cortes'],
            ['name' => 'Vista Planeacion'],
            ['name' => 'Vista Suministro'],
            ['name' => 'Vista Preparado'],
            ['name' => 'Vista Ensambre'],
            ['name' => 'Vista Pulido'],
            ['name' => 'Vista Medicion'],
            ['name' => 'Vista Visualizacion'],
            ['name' => 'Vista Ver'],
            ['name' => 'PlaneacionEdit'],
            ['name' => 'UsuriosEdit'],
            ['name' => 'CorteEdit'],
        ]);
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};