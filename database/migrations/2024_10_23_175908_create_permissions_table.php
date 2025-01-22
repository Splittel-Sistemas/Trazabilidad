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
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('CompletadosEdit')->nullable(); 
            $table->string('Vistas Editar')->nullable();
            $table->string('RolesEdit')->nullable(); 
            $table->string('Dashboard')->nullable();
            $table->string('Vista Cortes')->nullable(); 
            $table->string('Vista Planeacion')->nullable();
            $table->string('Vista Suministro')->nullable(); 
            $table->string('Vista Preparado')->nullable();
            $table->string('Vista Ensambre')->nullable(); 
            $table->string('Vista Pulido')->nullable();
            $table->string('Vista Medicion')->nullable(); 
            $table->string('Vista Visualizacion')->nullable();
            $table->string('Vista Ver')->nullable(); 
            $table->string('PlaneacionEdit')->nullable();
            $table->string('UsuriosEdit')->nullable(); 
            $table->string('CorteEdit')->nullable();
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
        
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn([
                'CompletadosEdit',
                'Vistas Editar',
                'RolesEdit',
                'Dashboard',
                'Vista Cortes',
                'Vista Planeacion',
                'Vista Suministro',
                'Vista Preparado',
                'Vista Ensambre',
                'Vista Pulido',
                'Vista Medicion',
                'Vista Visualizacion',
                'Vista Ver',
                'PlaneacionEdit',
                'UsuriosEdit',
                'CorteEdit',
            ]);
        });

        // Eliminar registros insertados
        DB::table('permissions')->whereIn('name', [
            'CompletadosEdit',
            'Vistas Editar',
            'RolesEdit',
            'Dashboard',
            'Vista Cortes',
            'Vista Planeacion',
            'Vista Suministro',
            'Vista Preparado',
            'Vista Ensambre',
            'Vista Pulido',
            'Vista Medicion',
            'Vista Visualizacion',
            'Vista Ver',
            'PlaneacionEdit',
            'UsuriosEdit',
            'CorteEdit',
        ])->delete();
    }
};
