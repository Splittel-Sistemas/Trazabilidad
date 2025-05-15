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
        Schema::create('PartidasOF_Areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('PartidasOF_id');
            $table->unsignedBigInteger('Areas_id');
            $table->unsignedBigInteger('Users_id');
            $table->unsignedBigInteger('Linea_id');
            $table->integer('Cantidad');
            $table->integer('NumeroBloque')->nullable();//Numero de Bloque
            $table->integer('CerrarBloque',1)->nullable();//Cerrar Bloque null = no ; 1 = si
            $table->integer('NumeroEtiqueta')->nullable();
            $table->string('TipoPartida',1)->nullable();//R retrabajo=R Normal=N
            $table->foreign('PartidasOF_id')->references('id')->on('PartidasOF')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('Areas_id')->references('id')->on('Areas')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('Users_id')->references('id')->on('Users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('Linea_id')->references('id')->on('Linea')->onDelete('cascade')->onUpdate('cascade');
            $table->datetime('FechaComienzo')->nullable();
            $table->datetime('FechaTermina')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PartidasOF_Areas');
    }
};
