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
use App\Http\Controllers\OrdenFabricacionController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\FuncionesGeneralesControlleControllerController;

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

Route::get('/leer-codigo-barra', [BarcodeController::class, 'index'])->name('leer.codigo.barra');

Route::get('/ordenes-fabricacion', [OrdenFabricacionController::class,'index'])->name('ordenes.indexx');
Route::get('/orders', [PlaneacionController::class, 'OrdenesVActual'])->name('orders');
Route::post('/partidas', [PlaneacionController::class, 'DatosDePartida'])->name('datospartida');
Route::post('/filtros', [PlaneacionController::class, 'filtros'])->name('filtros');
Route::post('/filtro', [PlaneacionController::class, 'filtro'])->name('filtro');
Route::post('/guardarDatos', [PlaneacionController::class, 'guardarDatos'])->name('guardarDatos');
Route::post('/eliminar-registro', [PlaneacionController::class, 'eliminarRegistro'])->name('eliminarRegistro');



use App\Http\Controllers\DetallesController;

//Route::get('/orden/{id}', [DetallesController::class, 'show'])->name('orden.show');



 