<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\PlaneacionController;

//*Comando para ejecutar las tareas
//php artisan schedule:work
//*Comando para ejecutar manualmente
//php artisan schedule:run
//Llenar 
Schedule::call(function () {
    $planeacionController = App::make(PlaneacionController::class);
    // Llamar al método del controlador
    $fecha=$planeacionController->LlenarTablaBuffer();
    Log::info($fecha);
})->dailyAt('23:59');

Schedule::call(function () {
    $planeacionController = App::make(PlaneacionController::class);
    // Llamar al método del controlador
    $fecha=$planeacionController->PlaneacionDiaPorcentajePlaneacion();
    Log::info($fecha);
})->dailyAt('00:01');
Schedule::call(function () {
    $planeacionController = App::make(PlaneacionController::class);
    // Llamar al método del controlador
    $fecha=$planeacionController->PlaneacionDiaPorcentajePlaneacion();
    Log::info($fecha);
})->hourly();
/*Artisan::command('inspire', function () {
    $quote = Inspiring::quote();
    $this->comment($quote);
    LlenarTablaBuffer
    Log::info("Comando 'inspire' ejecutado: {$quote}");
})->purpose('Display an inspiring quote')->everyMinute();*/
