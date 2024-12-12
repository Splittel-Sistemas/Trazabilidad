<?php

use App\Http\Controllers\HomeControler;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlaneacionController;

Route::get('/', [HomeControler::class,'Home'])->name('Home');
Route::get('/Planeacion', [PlaneacionController::class,'index'])->name('Planeacion');
Route::post('/Planeacion/Filtro/Fechas', [PlaneacionController::class,'PlaneacionFF'])->name('PlaneacionFF');
Route::post('/Planeacion/Filtro/OrdenVenta',[PlaneacionController::class,'PlaneacionFOV'])->name('PlaneacionFOV');
/*Route::post('/Planeacion/Filtro/Back',[PlaneacionController::class,'PlaneacionFOV'])->name('PlaneacionFOVBack');
Route::post('/Planeacion/Filtro/Next',[PlaneacionController::class,'PlaneacionFOV'])->name('PlaneacionFOVNext');*/
Route::get('/Planeacion/partidas', [PlaneacionController::class,'PartidasOF'])->name('PartidasOF');
Route::post('/Planeacion/partidas', [PlaneacionController::class,'PartidasOFGuardar'])->name('PartidasOFGuardar');
Route::delete('/Planeacion/partidas', [PlaneacionController::class,'PartidasOFRegresar'])->name('PartidasOFRegresar');
Route::post('/Planeacion/partidas/FiltroFechas', [PlaneacionController::class,'PartidasOFFiltroFechas_Tabla'])->name('PartidasOFFiltroFechas_Tabla');
Route::get('/Planeacion/detalles', [PlaneacionController::class,'PartidasOF_Detalles'])->name('PartidasOF_Detalles');
//Route::post('/Planeacion/Filtro/OrdenVenta',[PlaneacionController::class,'PlaneacionFOV'])->name('PlaneacionFOV');

//Route::get('/', [HomeController::class,'Home'])->name('home');
/*use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\SapController;
use App\Http\Controllers\SuministrosController;
use App\Http\Controllers\OrdenesController;
use App\Http\Controllers\PrincipalController;
use App\Http\Controllers\PlaneacionController;
use App\Http\Controllers\OrdenFabricacionController;
use App\Http\Controllers\BarcodeController;

use App\Http\Controllers\CorteController;
use App\Http\Controllers\OrdenVentaController;

// Rutas de autenticación
Route::get('/login', [LoginController::class,'login_view'])->name('login');
Route::post('/login/inicio', [LoginController::class,'login'])->name('login_post');
//Route::view('/registro', "usuarios.registro")->name('register');
Route::view('/menu', "layouts.menu")->middleware('auth')->name('menu');
//Route::post('/registro', [LoginController::class,'register'])->name('validar-registro');
Route::post('/login', [LoginController::class,'login'])->name('inicia-sesion');
Route::get('/logout', [LoginController::class,'logout'])->name('logout');

Route::get('/panel-principal', [PrincipalController::class, 'index'])->name('panel.principal');


Route::resource('registro', RegistroController::class);


Route::get('/conexion-sap', [SapController::class, 'conexionSap']);
Route::get('/datos-sap', [SapController::class, 'obtenerDatosSap']);


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

Route::get('/cortes', [CorteController::class, 'index'])->name('cortes');
Route::get('/cortes/data', [CorteController::class, 'getData'])->name('corte.getData');

Route::post('/FiltroFecha', [CorteController::class, 'FiltroFecha'])->name('FiltroFecha');
Route::post('/FiltroOrden', [CorteController::class, 'FiltroOrden'])->name('FiltroOrden');


Route::get('/buscar-orden', [BarcodeController::class, 'searchOrder']);

Route::get('/orden-fabricacion', [CorteController::class, 'create']);
Route::post('/orden-fabricacion', [CorteController::class, 'store']);





use App\Http\Controllers\DetallesController;
*/
//Route::get('/orden/{id}', [DetallesController::class, 'show'])->name('orden.show');



 