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
Schedule::call(function () {
    $planeacionController = App::make(PlaneacionController::class);
    // Llamar al método del controlador
    $fecha=$planeacionController->LlenarTablaBuffer();
    Log::info($fecha);
})->everyThirtySeconds();//->everyMinute();///->dailyAt('23:59');
/*Artisan::command('inspire', function () {
    $quote = Inspiring::quote();
    $this->comment($quote);
    LlenarTablaBuffer
    Log::info("Comando 'inspire' ejecutado: {$quote}");
})->purpose('Display an inspiring quote')->everyMinute();*/
