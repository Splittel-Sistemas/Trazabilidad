<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use GuzzleHttp\Client;
use App\Http\Middleware\FuncionesSapService;
class AppServiceProvider extends ServiceProvider

{
    /**
     * Register any application services.ñ
     */
    public function register(): void
    {
        $this->app->singleton(FuncionesSapService::class, function ($app) {
            return new FuncionesSapService();
        });
        
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
    Schema::defaultStringLength(191);
    }
    public function up()
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->text('descripcion');
            $table->timestamps();
        });
    }
}
