<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SomeController;


Route::get('/login', [LoginController::class,'login_view'])->name('login');
Route::post('/login/inicio',[LoginController::class,'login'])->name('login_post');
Route::view('/registo', "usuarios.registro")->name('register');
Route::view('/menu', "layouts.menu")->middleware('auth')->name('menu');

Route::post('/registro',[LoginController::class,'register'])->name('validar-registro');
Route::post('/login',[LoginController::class,'login'])->name('inicia-sesion');
Route::get('/logout',[LoginController::class,'logout'])->name('logout');

Route::resource('registro', RegistroController::class);


/*// Ruta para mostrar todos los registros
Route::get('/registros', [RegistroController::class, 'index'])->name('registros.index');
// Ruta para mostrar el formulario de creación de un nuevo registro
Route::get('/registros/create', [RegistroController::class, 'create'])->name('registros.create');
// Ruta para almacenar un nuevo registro
Route::post('/registros/store', [RegistroController::class, 'store'])->name('registros.store');
// Ruta para mostrar un registro específico
Route::get('/registros/{registro}', [RegistroController::class, 'show'])->name('registros.show');*/
