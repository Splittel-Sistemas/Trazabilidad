<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;


Route::get('/login', [LoginController::class,'login_view'])->name('login');
Route::post('/login/inicio',[LoginController::class,'login'])->name('login_post');
Route::view('/registo', "register")->name('register');
Route::view('/menu', "layouts.menu")->name('menu');

Route::post('/validar-registro',[LoginController::class,'register'])->name('validar-registro');
Route::post('/login',[LoginController::class,'login'])->name('inicia-sesion');
Route::get('/logout',[LoginController::class,'logout'])->name('logout');

/*Route::get('/', function () {
    return view('layouts.Menu');
    //return "Bienvenido a la pagina";
});
Route::get('/login', function () {
    return view('layouts.login');
    //return "Bienvenido a la pagina";
});*/
 /*Route:: get('cursos', function () {
    return "Bienvenido a la pagina de cursos";
});
Route:: get('create', function () {
    return "podras crear un curso:";
});
Route:: get('cursos/{curso}', function ($curso) {
    return "bienvenido al curso: $curso";
});*/ 
