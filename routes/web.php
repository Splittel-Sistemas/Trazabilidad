<?php

use App\Http\Controllers\HomeControler;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlaneacionController;
use App\Http\Controllers\CorteController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\AreasController;
use App\Http\Controllers\PreparadoController;

//Rutas Planeación
Route::get('/', [HomeControler::class,'Home'])->name('Home');
Route::get('/Planeacion', [PlaneacionController::class,'index'])->name('Planeacion');
Route::post('/Planeacion/Filtro/Fechas', [PlaneacionController::class,'PlaneacionFF'])->name('PlaneacionFF');
Route::post('/Planeacion/Filtro/OrdenVenta',[PlaneacionController::class,'PlaneacionFOV'])->name('PlaneacionFOV');
Route::get('/Planeacion/Filtro/OrdenFabricacion_OrdenVenta',[PlaneacionController::class,'PlaneacionFOFOV'])->name('PlaneacionFOFOV');
Route::get('/Planeacion/partidas', [PlaneacionController::class,'PartidasOF'])->name('PartidasOF');
Route::get('/Planeacion/partidas/vencidas', [PlaneacionController::class,'LlenarTablaVencidasOV'])->name('LlenarTablaVencidasOV');
Route::post('/Planeacion/partidas', [PlaneacionController::class,'PartidasOFGuardar'])->name('PartidasOFGuardar');
Route::delete('/Planeacion/partidas', [PlaneacionController::class,'PartidasOFRegresar'])->name('PartidasOFRegresar');
Route::post('/Planeacion/partidas/FiltroFechas', [PlaneacionController::class,'PartidasOFFiltroFechas_Tabla'])->name('PartidasOFFiltroFechas_Tabla');
Route::post('/Planeacion/partidas/EscanerEstatus', [PlaneacionController::class,'CambiarEstatusEscaner'])->name('CambiarEstatusEscaner');
Route::get('/Planeacion/detalles', [PlaneacionController::class,'PartidasOF_Detalles'])->name('PartidasOF_Detalles');

//Rutas Ares
Route::get('/Area/Corte', [AreasController::class,'Corte'])->name('Corte');
Route::get('/Area/Suministro', [AreasController::class,'Suministro'])->name('Suministro');
Route::get('/Area/Preparado', [AreasController::class,'Preparado'])->name('Preparado');
Route::get('/Area/Ensamble', [AreasController::class,'Ensamble'])->name('Ensamble');
Route::get('/Area/Pulido', [AreasController::class,'Pulido'])->name('Pulido');
Route::get('/Area/Medicion', [AreasController::class,'Medicion'])->name('Medicion');
Route::get('/Area/Visualizacion', [AreasController::class,'Visualizacion'])->name('Visualizacion');
Route::get('/Area/Partidas', [AreasController::class,'AreaPartidas'])->name('AreaPartidas');

//cortes
Route::get('/cortes', [CorteController::class, 'index'])->name('corte.index');
Route::get('/cortes/getData', [CorteController::class, 'getData'])->name('corte.getData');
Route::get('/detalles', [CorteController::class, 'verDetalles'])->name('detalles');
Route::get('/buscar-ordenes', [CorteController::class, 'buscarOrdenVenta'])->name('buscar.ordenes');
Route::get('/corte/getDetalleOrden', [CorteController::class, 'getDetalleOrden'])->name('corte.getDetalleOrden');
Route::get('corte/getCortes', [CorteController::class, 'getCortes'])->name('corte.getCortes');
Route::post('corte/finalizar/corte', [CorteController::class, 'finalizarCorte'])->name('corte.finalizarCorte');
Route::get('/orden-fabricacion/cantidad-total/{id}', [CorteController::class, 'getCantidadTotal'])->name('ordenFabricacion.getCantidadTotal');
Route::post('/guardarpartida', [CorteController::class, 'guardarPartidasOF'])->name('guardar.partida');
Route::get('/orden-fabricacion/{id}/cortes-info', [CorteController::class, 'getCortesInfo'])->name('ordenFabricacion.getCortesInfo');

// Ruta para mostrar el formulario de login
Route::get('/login', [loginController::class, 'login_view'])->name('login_view');
Route::post('/login', [loginController::class, 'login'])->name('login_post');
Route::post('/logout', [loginController::class, 'logout'])->name('logout');
Route::post('/register', [loginController::class, 'register'])->name('register');

//ruta para el formulario de registro
Route::resource('registro', RegistroController::class);
Route::get('/registro/{id}', [RegistroController::class, 'show'])->name('registro.show');

// Rutas para activar y desactivar usuarios
Route::post('/users/activar', [RegistroController::class, 'activar'])->name('users.activar');
Route::post('/users/desactivar', [RegistroController::class, 'desactivar'])->name('users.desactivar');

//rutas para generar etiquetas

Route::get('/preparado',[CorteController::class,'index'])->name('preparado.index');
Route::get('/generar-etiquetas/{corteId}', [CorteController::class, 'getDatosGenerarEtiquetas']);
Route::post('/generar-etiquetas', [CorteController::class, 'generarEtiquetas'])->name('generar.etiquetas');
// Ruta para obtener los datos de la orden y la partida
Route::get('/mostrar-info/{corteId}', [CorteController::class, 'getDatosParaModal']);



//Route::get('/detalle-orden', [CorteController::class, 'MostarInformacion'])->name('corte.getInformacion');


Route::get('/ruta/a/tu/controlador', [CorteController::class, 'MostarInformacion']);



























//registro 
//Route::view('/registro', "usuarios.registro")->name('register');



// Grupo de rutas para la gestión de usuarios
/*Route::prefix('registro')->name('registro.')->group(function () {
    // Listar todos los usuarios
    Route::get('/index', [RegistroController::class, 'index'])->name('index');

    // Crear un nuevo usuario
    Route::get('/create', [RegistroController::class, 'create'])->name('create');
    Route::post('/store', [RegistroController::class, 'store'])->name('store');

    // Mostrar detalles de un usuario específico
    Route::get('/{registro}', [RegistroController::class, 'show'])->name('show');

    // Editar un usuario existente
    Route::get('/{registro}/edit', [RegistroController::class, 'edit'])->name('edit');
    Route::put('/{registro}', [RegistroController::class, 'update'])->name('update');

    // Eliminar un usuario
    Route::delete('/{registro}', [RegistroController::class, 'destroy'])->name('destroy');
});*/

/*Route::get('/registro', [RegistroController::class, 'index'])->name('registro.index');
Route::get('/registro/create', [RegistroController::class, 'create'])->name('registro.create');
Route::post('/registro', [RegistroController::class, 'store'])->name('create');
Route::get('/registro/{registro}', [RegistroController::class, 'show'])->name('registro.show');
Route::get('/registro/{registro}/edit', [RegistroController::class, 'edit'])->name('registro.edit');
Route::put('/registro/{registro}', [RegistroController::class, 'update'])->name('registro.update');
Route::delete('/registro/{registro}', [RegistroController::class, 'destroy'])->name('registro.destroy');*/



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



 