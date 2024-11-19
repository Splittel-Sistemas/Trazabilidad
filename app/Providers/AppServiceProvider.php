<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Services\TCPDFService;
use App\Services\TCPDFService as ServicesTCPDFService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Registramos el servicio TCPDF como un singleton
        $this->app->singleton(ServicesTCPDFService::class, function ($app) {
            return new ServicesTCPDFService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Establecemos la longitud predeterminada de los índices de string en 191 caracteres
        Schema::defaultStringLength(191);
    }
    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->text('descripcion');
            $table->timestamps();
        });
    }

    /**
     * Deshacer las migraciones.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
}