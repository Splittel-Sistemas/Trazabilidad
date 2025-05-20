<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaneacionController;
use App\Http\Controllers\CorteController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\AreasController;
use App\Http\Controllers\RolesPermisoController;
use App\Http\Controllers\PreparadoController;
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\EmpacadoController;
use App\Http\Controllers\LineasController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\DashboardControlle;
use GuzzleHttp\Promise\Coroutine;
use Illuminate\Routing\Route as RoutingRoute;

// Ruta para mostrar el formulario de login
Route::post('/operador_login', [loginController::class, 'operador'])->name('operador.login');
Route::get('/login', [loginController::class, 'login_view'])->name('login');
Route::post('/login', [loginController::class, 'login'])->name('login_post');
Route::get('/logout', [loginController::class, 'logout'])->name('logout')->middleware('auth');
Route::post('/register', [loginController::class, 'register'])->name('register')->middleware('auth');
Route::get('/lineas/progreso', [HomeController::class, 'lineas'])->name('lineas.indicador');
Route::get('/check-session', [HomeController::class, 'CheckSession'])->name('CheckSession');
//Ruta Home
Route::get('/', [HomeController::class, 'index'])->name('Home')->middleware('auth');

//Rutas Planeación
Route::get('/Planeacion', [PlaneacionController::class,'index'])->name('Planeacion')->middleware('auth');
Route::post('/Planeacion/Filtro/Fechas', [PlaneacionController::class,'PlaneacionFF'])->name('PlaneacionFF')->middleware('auth');
Route::post('/Planeacion/Filtro/OrdenVenta',[PlaneacionController::class,'PlaneacionFOV'])->name('PlaneacionFOV')->middleware('auth');
Route::get('/Planeacion/Filtro/OrdenFabricacion_OrdenVenta',[PlaneacionController::class,'PlaneacionFOFOV'])->name('PlaneacionFOFOV')->middleware('auth');
Route::get('/Planeacion/partidas', [PlaneacionController::class,'PartidasOF'])->name('PartidasOF')->middleware('auth');
Route::get('/Planeacion/partidas/vencidas', [PlaneacionController::class,'LlenarTablaVencidasOV'])->name('LlenarTablaVencidasOV')->middleware('auth');
Route::post('/Planeacion/partidas', [PlaneacionController::class,'PartidasOFGuardar'])->name('PartidasOFGuardar')->middleware('auth');
Route::delete('/Planeacion/partidas', [PlaneacionController::class,'PartidasOFRegresar'])->name('PartidasOFRegresar')->middleware('auth');
Route::post('/Planeacion/partidas/FiltroFechas', [PlaneacionController::class,'PartidasOFFiltroFechas_Tabla'])->name('PartidasOFFiltroFechas_Tabla')->middleware('auth');
Route::post('/Planeacion/partidas/EscanerEstatus', [PlaneacionController::class,'CambiarEstatusEscaner'])->name('CambiarEstatusEscaner')->middleware('auth');
Route::post('/Planeacion/partidas/UrgenciaEstatus', [PlaneacionController::class,'CambiarEstatusUrgencia'])->name('CambiarEstatusUrgencia')->middleware('auth');
Route::get('/Planeacion/detalles', [PlaneacionController::class,'PartidasOF_Detalles'])->name('PartidasOF_Detalles')->middleware('auth');
Route::get('/Planeacion/PorcentajesPlaneacion', [PlaneacionController::class,'PorcentajesPlaneacion'])->name('PorcentajesPlaneacion')->middleware('auth');
Route::post('/Planeacion/Porcentaje/Guardar', [PlaneacionController::class,'GuardarParametrosPorcentajes'])->name('GuardarParametrosPorcentajes')->middleware('auth');
//Rutas Ares
//Corte Nuevas
Route::get('/Area/Corte', [CorteController::class, 'index'])->name('corte.index')->middleware('auth');
Route::get('/Area/Corte/Tabla', [CorteController::class,'CorteRecargarTabla'])->name('CorteRecargarTabla')->middleware('auth');
Route::get('/Area/Corte/Tabla/Cerrada', [CorteController::class,'CorteRecargarTablaCerrada'])->name('CorteRecargarTablaCerrada')->middleware('auth');
Route::post('/Area/Corte/InfoModal', [CorteController::class,'CortesDatosModal'])->name('CortesDatosModal')->middleware('auth');
Route::post('/Area/Corte/Emisiones', [CorteController::class,'TraerEmisiones'])->name('TraerEmisiones')->middleware('auth');
Route::post('/Area/Corte/Guardar', [CorteController::class,'GuardarCorte'])->name('GuardarCorte')->middleware('auth');
Route::post('/Area/Corte/Cancelar', [CorteController::class,'CancelarCorte'])->name('CancelarCorte')->middleware('auth');
Route::post('/Area/Corte/Finalizar', [CorteController::class,'FinalizarCorte'])->name('FinalizarCorte')->middleware('auth');
Route::post('/Area/Corte/Buscar', [CorteController::class,'BuscarCorte'])->name('BuscarCorte')->middleware('auth');
Route::get('/Area/Corte/GenerarPDF', [CorteController::class, 'generarPDF'])->name('generarPDF')->middleware('auth');//Generar PDF
Route::get('/Area/Corte/GenerarPDF/45X25', [CorteController::class, 'generarPDF45X25'])->name('generarPDF45X25')->middleware('auth');//Generar PDF
Route::get('/Area/Corte/GenerarPDF/110X20', [CorteController::class, 'generarPDF110X20'])->name('generarPDF110X20')->middleware('auth');//Generar PDF
//Suministro
Route::get('/Area/Suministro', [AreasController::class,'Suministro'])->name('Suministro')->middleware('auth');
Route::get('/Area/Suministro/Tabla', [AreasController::class,'SuministroRecargarTabla'])->name('SuministroRecargarTabla')->middleware('auth');
Route::get('/Area/Suministro/Tabla/Cerrada', [AreasController::class,'SuministroRecargarTablaCerrada'])->name('SuministroRecargarTablaCerrada')->middleware('auth');
Route::post('/Area/Suministro/InfoModal', [AreasController::class,'SuministroDatosModal'])->name('SuministroDatosModal')->middleware('auth');
Route::post('/Area/Suministro/Emision', [AreasController::class,'SuministroEmision'])->name('SuministroEmision')->middleware('auth');
Route::post('/Area/Suministro/Guardar', [AreasController::class,'SuministroGuardar'])->name('SuministroGuardar')->middleware('auth');
Route::post('/Area/Suministro/Cancelar', [AreasController::class,'SuministroCancelar'])->name('SuministroCancelar')->middleware('auth');
Route::post('/Area/Suministro/Finalizar', [AreasController::class,'SuministroFinalizar'])->name('SuministroFinalizar')->middleware('auth');
Route::post('/Area/Suministro/Buscar', [AreasController::class,'BuscarSuministro'])->name('BuscarSuministro')->middleware('auth');
Route::get('/Area/Corte/GenerarPDF/Suministro/110X10X3', [CorteController::class, 'generarPDFSuministro'])->name('generarPDFSuministro')->middleware('auth');//Generar PDF
Route::get('/Area/Corte/GenerarPDF/Suministro/45X25', [CorteController::class, 'generarPDFSuministro45X25'])->name('generarPDFSuministro45X25')->middleware('auth');//Generar PDF
Route::get('/Area/Corte/GenerarPDF/Suministro/110X20', [CorteController::class, 'generarPDFSuministro110X20'])->name('generarPDFSuministro110X20')->middleware('auth');//Generar PDF
//Preparado
Route::get('/Area/Transición', [AreasController::class,'Transicion'])->name('Transicion')->middleware('auth');
Route::get('/Area/Preparado', [AreasController::class,'Preparado'])->name('Preparado')->middleware('auth');
Route::post('/Area/Preparado/buscar', [AreasController::class,'PreparadoBuscar'])->name('PreparadoBuscar')->middleware('auth');
Route::get('/Area/Suministro/buscar', [AreasController::class,'SuministroBuscar'])->name('SuministroBuscar')->middleware('auth');
Route::post('/Area/Suministro/NoEscaner', [AreasController::class,'TipoNoEscaner'])->name('TipoNoEscaner')->middleware('auth');
Route::get('/Area/Ribonizado', [AreasController::class,'Ribonizado'])->name('Ribonizado')->middleware('auth');
Route::get('/Area/Ensamble', [AreasController::class,'Ensamble'])->name('Ensamble')->middleware('auth');
Route::get('/Area/Corte/fibra', [AreasController::class,'Cortedefibra'])->name('Cortedefibra')->middleware('auth');
Route::get('/Area/Pulido', [AreasController::class,'Pulido'])->name('Pulido')->middleware('auth');
Route::post('/Area/Pulido/CerrarPlato', [AreasController::class,'PulidoCerrarPlato'])->name('PulidoCerrarPlato')->middleware('auth');
Route::post('/Area/Pulido/Linea', [AreasController::class,'PulidoLinea'])->name('PulidoLinea')->middleware('auth');
Route::get('/Area/Armado', [AreasController::class,'Armado'])->name('Armado')->middleware('auth');
Route::get('/Area/Inspección', [AreasController::class,'Inspeccion'])->name('Inspeccion')->middleware('auth');
Route::get('/Area/Polaridad', [AreasController::class,'Polaridad'])->name('Polaridad')->middleware('auth');
Route::get('/Area/Crimpado', [AreasController::class,'Crimpado'])->name('Crimpado')->middleware('auth');
Route::get('/Area/Medición', [AreasController::class,'Medicion'])->name('Medicion')->middleware('auth');
Route::get('/Area/Visualización', [AreasController::class,'Visualizacion'])->name('Visualizacion')->middleware('auth');
Route::get('/Area/Partidas', [AreasController::class,'AreaPartidas'])->name('AreaPartidas')->middleware('auth');
Route::get('/Area/Montaje', [AreasController::class,'Montaje'])->name('Montaje')->middleware('auth');
Route::post('/Area/Tabla/Pendientes', [AreasController::class,'AreaTablaPendientes'])->name('AreaTablaPendientes')->middleware('auth');
//Clasificacion
Route::get('/Area/Clasificación', [AreasController::class,'Clasificacion'])->name('Clasificacion')->middleware('auth');
Route::get('/Area/Clasificación/RecargarTabla', [AreasController::class,'ClasificacionRecargarTabla'])->name('ClasificacionRecargarTabla')->middleware('auth');
Route::post('/Area/Clasificación/InfoModal', [AreasController::class,'ClasificacionInfoModal'])->name('ClasificacionInfoModal')->middleware('auth');
Route::post('/Area/Clasificación/Asignar', [AreasController::class,'ClasificacionAsignar'])->name('ClasificacionAsignar')->middleware('auth');

Route::get('/corte/getDetalleOrden', [CorteController::class, 'getDetalleOrden'])->name('corte.getDetalleOrden')->middleware('auth');
Route::post('/guardarpartida', [CorteController::class, 'guardarPartidasOF'])->name('guardar.partida')->middleware('auth');
Route::get('corte/getCortes', [CorteController::class, 'getCortes'])->name('corte.getCortes')->middleware('auth');
Route::get('/orden-fabricacion/{ordenFabricacionId}/cortes-info', [CorteController::class, 'getCortesInfo'])->name('orden-fabricacion.cortes-info')->middleware('auth');
Route::post('corte/finalizar/corte', [CorteController::class, 'finalizarCorte'])->name('corte.finalizarCorte')->middleware('auth');
Route::post('/orden-fabricacion/update-status', [CorteController::class, 'updateStatus'])->name('orden-fabricacion.update-status')->middleware('auth');
Route::post('/filtrar-por-fecha', [CorteController::class, 'filtrarPorFecha'])->name('Fitrar.Fecha')->middleware('auth');
Route::get('/ordenes/completadas',[CorteController:: class, 'Completado'])->name('ordenes.completadas')->middleware('auth');
Route::get('/ruta-para-actualizar-tabla', [CorteController::class, 'actualizarTabla'])->name('actualizar.tabla')->middleware('auth');
Route::delete('/corte/eliminar', [CorteController::class, 'eliminarCorte'])->name('corte.eliminarCorte')->middleware('auth');
Route::delete('/corte/eliminar1', [CorteController::class, 'eliminarCorte1'])->name('corte.eliminarCorte1')->middleware('auth');
Route::post('/buscar',[CorteController::class, 'buscar'])->name('buscar.todo')->middleware('auth');
Route::post('/filtrar-fecha', [CorteController::class, 'filtrarPorFechac'])->name('Fitrar.Fechacerrado')->middleware('auth');
Route::get('/corte/detalles', [CorteController::class, 'getDetalleOrden'])->name('corte.getDetalles')->middleware('auth');
Route::get('/ordenes/cerradas',[CorteController:: class, 'index'])->name('ordenes.cerradas')->middleware('auth');
Route::post('/filtrar-por-fechaS', [CorteController::class, 'fechaCompletado'])->name('Fitrar.FechaS')->middleware('auth');
Route::get('/ordenes-filtradas', [CorteController::class, 'SinCortesProceso'])->name('ordenes.filtradas')->middleware('auth');
//Rutas cortes
/*
    Route::get('/cortes/getData', [CorteController::class, 'getData'])->name('corte.getData')->middleware('auth');
    Route::get('/detalles', [CorteController::class, 'verDetalles'])->name('detalles')->middleware('auth');
    Route::get('/buscar-ordenes', [CorteController::class, 'buscarOrdenVenta'])->name('buscar.ordenes')->middleware('auth');
    Route::get('/corte/getDetalleOrden', [CorteController::class, 'getDetalleOrden'])->name('corte.getDetalleOrden')->middleware('auth');
    Route::get('corte/getCortes', [CorteController::class, 'getCortes'])->name('corte.getCortes')->middleware('auth');
    Route::post('corte/finalizar/corte', [CorteController::class, 'finalizarCorte'])->name('corte.finalizarCorte')->middleware('auth');
    Route::get('/orden-fabricacion/cantidad-total/{id}', [CorteController::class, 'getCantidadTotal'])->name('ordenFabricacion.getCantidadTotal')->middleware('auth');
    Route::post('/guardarpartida', [CorteController::class, 'guardarPartidasOF'])->name('guardar.partida')->middleware('auth');
    Route::get('/orden-fabricacion/{id}/cortes-info', [CorteController::class, 'getCortesInfo'])->name('ordenFabricacion.getCortesInfo')->middleware('auth');
    Route::get('/orden-fabricacion/{ordenFabricacionId}/cortes-info', [CorteController::class, 'getCortesInfo'])->name('orden-fabricacion.cortes-info')->middleware('auth');
    Route::post('/orden-fabricacion/update-status', [CorteController::class, 'updateStatus'])->name('orden-fabricacion.update-status')->middleware('auth');
Route::get('/ruta-para-actualizar-tabla', [CorteController::class, 'actualizarTabla'])->name('actualizar.tabla')->middleware('auth');
*/

Route::get('/corte/DetallesCompletado', [CorteController::class, 'DetallesCompletado'])->name('corte.DetallesCompletado')->middleware('auth');

//rutas para generar etiquetas
Route::get('/generar-etiquetas/{corteId}', [CorteController::class, 'getDatosGenerarEtiquetas'])->middleware('auth');
Route::post('/generar-etiquetas', [CorteController::class, 'generarEtiquetas'])->name('generar.etiquetas')->middleware('auth');
Route::get('/mostrar/etiqueta', [CorteController::class, 'MostarInformacion'])->name('mostrar.etiqueta')->middleware('auth');
Route::get('/generar-pdf', [CorteController::class, 'generarPDF'])->name('generar.pdf')->middleware('auth');
Route::post('/generar-pdf-rangos', [CorteController::class, 'PDFCondicion'])->name('pdfcondicion')->middleware('auth');

//ruta para el formulario de registro
Route::get('/registro', [RegistroController::class, 'index'])->name('registro.index')->middleware('auth');
Route::get('/tabla/registro',[RegistroController::class, 'tablaPrincipal'])->name('principal.tabla')->middleware('auth');
Route::post('/users/activar', [RegistroController::class, 'activar'])->name('users.activar')->middleware('auth');
Route::post('/users/desactivar', [RegistroController::class, 'desactivar'])->name('users.desactivar')->middleware('auth');
Route::get('/registro/create', [RegistroController::class, 'create'])->name('registro.create')->middleware('auth');
Route::post('/operador/store', [RegistroController::class, 'storeoperador'])->name('operador.store')->middleware('auth');
Route::post('/registro/store', [RegistroController::class, 'store'])->name('registro.store')->middleware('auth');
Route::get('/registro/edit/{id}', [RegistroController::class, 'edit'])->name('registro.edit')->middleware('auth');
Route::get('/registro/show/{id}',[RegistroController::class, 'show'])->name('registro.show')->middleware('auth');
Route::put('/registro/update/{id}', [RegistroController::class, 'update'])->name('registro.update')->middleware('auth');
Route::delete('registro/{id}', [RegistroController::class, 'destroy'])->name('registro.destroy')->middleware('auth');

//rutas roles y permiso
Route::get('/RolesPermisos', [RolesPermisoController::class, 'index'])->name('RolesPermisos.index')->middleware('auth');
Route::get('/RolesPermisos/create', [RolesPermisoController::class, 'create'])->name('RolesPermisos.create')->middleware('auth');
Route::post('/RolesPermisos/store', [RolesPermisoController::class, 'store'])->name('RolesPermisos.store')->middleware('auth');
Route::get('/RolesPermisos/edit/{id}', [RolesPermisoController::class, 'edit'])->name('RolesPermisos.edit')->middleware('auth');
Route::put('/RolesPermisos/update/{id}', [RolesPermisoController::class, 'update'])->name('RolesPermisos.update')->middleware('auth');
Route::delete('destroy/{id}', [RolesPermisoController::class, 'destroy'])->name('destroy')->middleware('auth');

//rutas busquedas
Route::get('/busquedas',[BusquedaController::class, 'index'])->name('Busquedas.OV')->middleware('auth');
Route::get('/tablaventa',[BusquedaController::class, 'obtenerOrdenesVenta'])->name('Buscar.Venta')->middleware('auth');
Route::get('/tablafabricacion',[BusquedaController::class, 'obtenerOrdenesFabricacion'])->name('Buscar.Fabricacion')->middleware('auth');
Route::get('/detallesventa',[BusquedaController::class, 'detallesventa'])->name('Buscar.Venta.Detalle')->middleware('auth');
Route::get('/graficador', [BusquedaController::class, 'Graficador'])-> name('graficador')->middleware('auth');
Route::get('/detallesOF',[BusquedaController::class, 'DetallesOF'])->name('Detalles.Fabricacion')->middleware('auth');
Route::get('/graficadorOF',[BusquedaController::class,'GraficadorFabricacion'])->name('graficadoOF')->middleware('auth');
Route::get('/graficasOR/OF',[BusquedaController::class,'GraficarOROF'])->name('graficarOR.OF')->middleware('auth');
Route::get('/tiempos/fabricacion',[BusquedaController::class, 'tiemposOrden'])->name('tiempo.orden')->middleware('auth');
Route::post('/Busqueda/Ordenes',[BusquedaController::class, 'TipoOrden'])->name('TipoOrden')->middleware('auth');

//rutas del dashboard
Route::get('/retrabajo', [HomeController:: class, 'Ordenes'])->name('ordenes.retrabajo')->middleware('auth');
Route::get('/cerradas', [HomeController::class, 'cerradas'])->name('orden.cerredas')->middleware('auth');
Route::get('/abiertas', [HomeController:: class, 'abiertas'])->name('ordenes.abiertas')->middleware('auth');
Route::get('/graficasdores', [HomeController:: class, 'graficas'])->name('graficas.dashboard')->middleware('auth');
Route::get('/dashboard/capacidadproductiva', [HomeController:: class, 'CapacidadProductiva'])->name('CapacidadProductiva')->middleware('auth');

// routes/web.php
Route::get('/detalles-oc', [HomeController::class, 'detallesOC'])->name('ordenes.detallesOC')->middleware('auth');
Route::get('/tiempo', [HomeController::class, 'tiempoOC'])->name('ordenes.tiempo')->middleware('auth');
Route::get('/progreso-das',[HomeController::class, 'progreso'])->name('progreso.dash')->middleware('auth');
Route::get('/progreso-of',[HomeController::class,'progresoof'])->name('of.progreso')->middleware('auth');
Route::get('/indicadores-ce',[HomeController::class,'graficasmes'])->name('indicadores.CE')->middleware('auth');
Route::get('/ordenes-abiertas',[HomeController::class, 'tablasAbiertas'])->name('tabla.abiertas')->middleware('auth');
Route::get('/ordenes-completada', [HomeController::class, 'tablasCompletadas'])->name('tabla.completas')->middleware('auth');
Route::get('/tabla-semana', [HomeController::class,'tablasemana'])->name('tablas.semana')->middleware('auth');
Route::get('/tabla-mes',[HomeController::class, 'tablasMes'])->name('tablas.mes')->middleware('auth');
Route::get('/tabla-horas',[HomeController::class, 'tablasHoras'])->name('tablas.hora')->middleware('auth');
Route::get('/wizarp', [HomeController::class, 'wizarp'])->name('wizarp.dashboard');
Route::get('/wizarpdia', [HomeController::class, 'wizarpdia'])->name('wizarpdia.dashboard');
Route::get('/wizarpmes', [HomeController::class, 'wizarpmes'])->name('wizarpmes.dashboard');
Route::get('/indicadores-cedia',[HomeController::class,'graficasdia'])->name('indicadores-cedia')->middleware('auth');
Route::get('/indicadores-cesemana',[HomeController::class,'graficasemana'])->name('indicadores.CEsemana')->middleware('auth');
Route::get('/dashboard/indicadordia',[HomeController::class, 'Dasboardindicadordia'])->name('dashboard.indicador')->middleware('auth');
Route::post('/guardar-dashboard', [HomeController::class, 'guardarDasboard'])->name('guardar.Dasboard')->middleware('auth');

//area empaquetado
Route::get('/Area/Empaquetado',[AreasController::class,'Empaquetado'])->name('Empacado')->middleware('auth');
Route::get('/Tabla/principal',[AreasController::class,'tablaEmpacado'])->name('tabla.principal')->middleware('auth');
Route::post('/Area/Empaquetado/buscar', [AreasController::class,'EmpaquetadoBuscar'])->name('EmpaquetadoBuscar')->middleware('auth');
Route::get('/fin/Proceso',[AreasController::class,'finProcesoEmpaque'])->name('finProceso.empacado')->middleware('auth');
Route::post('/regresar-proceso', [AreasController::class, 'RegresarProceso'])->name('regresar.proceso')->middleware('auth');
Route::get('/tiempos-GraficaD', [HomeController::class, 'graficastiempo'])->name('graficastiempo')->middleware('auth');
Route::get('/tiempos muertos',[HomeController::class, 'graficastiempoMuerto'])->name('graficastiempoMuerto')->middleware('auth');
   
//lineas        
Route::get('/index/linea',[LineasController::class, 'index'])->name('index.linea')->middleware('auth');    
Route::get('/lineas/datos', [LineasController::class, 'tablalinea'])->name('lineas.datos')->middleware('auth');
Route::get('/linea/create', [LineasController::class, 'create'])->name('linea.create')->middleware('auth');
Route::post('/linea/store',[LineasController::class, 'store'])->name('linea.store')->middleware('auth');
Route::post('/lineas/activar', [LineasController::class, 'activar'])->name('lineas.activar')->middleware('auth');
Route::post('/lineas/desactivar', [LineasController::class, 'desactivar'])->name('lineas.desactivar')->middleware('auth');
Route::get('/linea/show/{id}', [LineasController::class, 'show'])->name('linea.show')->middleware('auth');
Route::put('/linea/update', [LineasController::class, 'update'])->name('linea.update')->middleware('auth');

//perfil usuarios
Route::get('/index/perfil',[PerfilController::class, 'index'])->name('index.perfil')->middleware('auth'); 
Route::put('/update/perfil', [PerfilController::class, 'update'])->name('update.perfil')->middleware('auth');

//error
Route::get('/Error/permiso',[HomeController::class, 'error'])->name('error.')->middleware('auth'); 

//dasboard operador
Route::get('/Home',[HomeController::class, 'indexoperador'])->name('index.operador')->middleware('auth'); 


Route::post('/guardar-aviso', [HomeController::class, 'guardarAviso'])->name('guardarAviso');
Route::put('/Planeacion/actualizar/fecha',[PlaneacionController:: class,'ActualizarFechaPlaneacion'])->name('ActualizarFechaPlaneacion');
Route::get('/tiempo/promedio',[HomeController::class,'tiempopromedio'])->name('tiempopromedio');




//registro 
//Route::view('/registro', "usuarios.registro")->name('register')->middleware('auth');



// Grupo de rutas para la gestión de usuarios
/*Route::prefix('registro')->name('registro.')->group(function () {
    // Listar todos los usuarios
    Route::get('/index', [RegistroController::class, 'index'])->name('index')->middleware('auth');

    // Crear un nuevo usuario
    Route::get('/create', [RegistroController::class, 'create'])->name('create')->middleware('auth');
    Route::post('/store', [RegistroController::class, 'store'])->name('store')->middleware('auth');

    // Mostrar detalles de un usuario específico
    Route::get('/{registro}', [RegistroController::class, 'show'])->name('show')->middleware('auth');

    // Editar un usuario existente
    Route::get('/{registro}/edit', [RegistroController::class, 'edit'])->name('edit')->middleware('auth');
    Route::put('/{registro}', [RegistroController::class, 'update'])->name('update')->middleware('auth');

    // Eliminar un usuario
    Route::delete('/{registro}', [RegistroController::class, 'destroy'])->name('destroy')->middleware('auth');
})->middleware('auth');*/

/*Route::get('/registro', [RegistroController::class, 'index'])->name('registro.index')->middleware('auth');
Route::get('/registro/create', [RegistroController::class, 'create'])->name('registro.create')->middleware('auth');
Route::post('/registro', [RegistroController::class, 'store'])->name('create')->middleware('auth');
Route::get('/registro/{registro}', [RegistroController::class, 'show'])->name('registro.show')->middleware('auth');
Route::get('/registro/{registro}/edit', [RegistroController::class, 'edit'])->name('registro.edit')->middleware('auth');
Route::put('/registro/{registro}', [RegistroController::class, 'update'])->name('registro.update')->middleware('auth');
Route::delete('/registro/{registro}', [RegistroController::class, 'destroy'])->name('registro.destroy')->middleware('auth');*/




/*use Illuminate\Support\Facades\Route->middleware('auth');
use App\Http\Controllers\LoginController->middleware('auth');
use App\Http\Controllers\RegistroController->middleware('auth');
use App\Http\Controllers\SapController->middleware('auth');
use App\Http\Controllers\SuministrosController->middleware('auth');
use App\Http\Controllers\OrdenesController->middleware('auth');
use App\Http\Controllers\PrincipalController->middleware('auth');
use App\Http\Controllers\PlaneacionController->middleware('auth');
use App\Http\Controllers\OrdenFabricacionController->middleware('auth');
use App\Http\Controllers\BarcodeController->middleware('auth');

use App\Http\Controllers\CorteController->middleware('auth');
use App\Http\Controllers\OrdenVentaController->middleware('auth');

// Rutas de autenticación
Route::get('/login', [LoginController::class,'login_view'])->name('login')->middleware('auth');
Route::post('/login/inicio', [LoginController::class,'login'])->name('login_post')->middleware('auth');
//Route::view('/registro', "usuarios.registro")->name('register')->middleware('auth');
Route::view('/menu', "layouts.menu")->middleware('auth')->name('menu')->middleware('auth');
//Route::post('/registro', [LoginController::class,'register'])->name('validar-registro')->middleware('auth');
Route::post('/login', [LoginController::class,'login'])->name('inicia-sesion')->middleware('auth');
Route::get('/logout', [LoginController::class,'logout'])->name('logout')->middleware('auth');

Route::get('/panel-principal', [PrincipalController::class, 'index'])->name('panel.principal')->middleware('auth');


Route::resource('registro', RegistroController::class)->middleware('auth');


Route::get('/conexion-sap', [SapController::class, 'conexionSap'])->middleware('auth');
Route::get('/datos-sap', [SapController::class, 'obtenerDatosSap'])->middleware('auth');


Route::get('/suministros', [SuministrosController::class, 'index'])->name('suministros.index')->middleware('auth');
Route::post('/suministros/enviar', [SuministrosController::class, 'enviar'])->name('suministros.enviar')->middleware('auth');

Route::get('/ordenes', [OrdenesController::class, 'index'])->name('ordenes.index')->middleware('auth');
Route::post('/enviar', [OrdenesController::class, 'ordenes.enviar'])->name('ordenes.enviar')->middleware('auth');

Route::get('/orden-venta', [OrdenVentaController::class, 'index'])->name('ordenventa')->middleware('auth');
Route::post('/orden-venta/{id}/update-state', [OrdenVentaController::class, 'updateState'])->name('ordenventa.updateState')->middleware('auth');

Route::get('/leer-codigo-barra', [BarcodeController::class, 'index'])->name('leer.codigo.barra')->middleware('auth');

Route::get('/ordenes-fabricacion', [OrdenFabricacionController::class,'index'])->name('ordenes.indexx')->middleware('auth');
Route::get('/orders', [PlaneacionController::class, 'OrdenesVActual'])->name('orders')->middleware('auth');
Route::post('/partidas', [PlaneacionController::class, 'DatosDePartida'])->name('datospartida')->middleware('auth');
Route::post('/filtros', [PlaneacionController::class, 'filtros'])->name('filtros')->middleware('auth');
Route::post('/filtro', [PlaneacionController::class, 'filtro'])->name('filtro')->middleware('auth');
Route::post('/guardarDatos', [PlaneacionController::class, 'guardarDatos'])->name('guardarDatos')->middleware('auth');
Route::post('/eliminar-registro', [PlaneacionController::class, 'eliminarRegistro'])->name('eliminarRegistro')->middleware('auth');

Route::get('/cortes', [CorteController::class, 'index'])->name('cortes')->middleware('auth');
Route::get('/cortes/data', [CorteController::class, 'getData'])->name('corte.getData')->middleware('auth');

Route::post('/FiltroFecha', [CorteController::class, 'FiltroFecha'])->name('FiltroFecha')->middleware('auth');
Route::post('/FiltroOrden', [CorteController::class, 'FiltroOrden'])->name('FiltroOrden')->middleware('auth');


Route::get('/buscar-orden', [BarcodeController::class, 'searchOrder'])->middleware('auth');

Route::get('/orden-fabricacion', [CorteController::class, 'create'])->middleware('auth');
Route::post('/orden-fabricacion', [CorteController::class, 'store'])->middleware('auth');





use App\Http\Controllers\DetallesController->middleware('auth');
*/
//Route::get('/orden/{id}', [DetallesController::class, 'show'])->name('orden.show')->middleware('auth');



 