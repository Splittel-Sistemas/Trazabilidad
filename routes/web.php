<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\SapController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\SuministrosController;
use App\Http\Controllers\OrdenesController;
use App\Http\Controllers\PrincipalController;
use App\Http\Controllers\PlaneacionController;

use App\Http\Controllers\OrdenVentaController;

// Rutas de autenticación
Route::get('/login', [LoginController::class,'login_view'])->name('login');
Route::post('/login/inicio', [LoginController::class,'login'])->name('login_post');
Route::view('/registro', "usuarios.registro")->name('register');
Route::view('/menu', "layouts.menu")->middleware('auth')->name('menu');
Route::post('/registro', [LoginController::class,'register'])->name('validar-registro');
Route::post('/login', [LoginController::class,'login'])->name('inicia-sesion');
Route::get('/logout', [LoginController::class,'logout'])->name('logout');

Route::get('/panel-principal', [PrincipalController::class, 'index'])->name('panel.principal');


Route::resource('registro', RegistroController::class);


Route::get('/conexion-sap', [SapController::class, 'conexionSap']);
Route::get('/datos-sap', [SapController::class, 'obtenerDatosSap']);

Route::get('/generate-pdf', [PDFController::class, 'generatePdf']);

Route::get('/suministros', [SuministrosController::class, 'index'])->name('suministros.index');
Route::post('/suministros/enviar', [SuministrosController::class, 'enviar'])->name('suministros.enviar');

Route::get('/ordenes', [OrdenesController::class, 'index'])->name('ordenes.index');
Route::post('/enviar', [OrdenesController::class, 'ordenes.enviar'])->name('ordenes.enviar');

Route::get('/orden-venta', [OrdenVentaController::class, 'index'])->name('ordenventa');
Route::post('/orden-venta/{id}/update-state', [OrdenVentaController::class, 'updateState'])->name('ordenventa.updateState');


use App\Http\Controllers\BarcodeController;


Route::get('/leer-codigo-barra', [BarcodeController::class, 'index'])->name('leer.codigo.barra');
use App\Http\Controllers\OrdenFabricacionController;

Route::get('/ordenes-fabricacion', [OrdenFabricacionController::class,'index'])->name('ordenes.indexx');
//use App\Http\Controllers\OrdenVController;

//Route::get('/ordenesventa',[OrdenesVController::class, 'index'])->name('buscar.orden.venta')

Route::get('/orders', [PlaneacionController::class, 'OrdenesVActual'])->name('orders');
Route::post('/partidas', [PlaneacionController::class, 'DatosDePartida'])->name('datospartida');
Route::post('/upload-file', [PlaneacionController::class, 'uploadFile'])->name('uploadFile');


Route::get('/', function () {
    return view('layouts.Menu');
    //return "Bienvenido a la pagina";
});
Route::get('/login', function () {
    return "hola";
    //return view('layouts.login');
    //return "Bienvenido a la pagina";
});
 /*Route:: get('cursos', function () {
    return "Bienvenido a la pagina de cursos";
});
Route:: get('create', function () {
    return "podras crear un curso:";
});
Route:: get('cursos/{curso}', function ($curso) {
    return "bienvenido al curso: $curso";
});*/ 
