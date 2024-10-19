<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;


Route::get('/login', [LoginController::class,'login_view'])->name('login');
Route::post('/login/inicio',[LoginController::class,'login'])->name('login_post');
Route::view('/registro', "usuarios.registro")->name('register');
Route::view('/menu', "layouts.menu")->middleware('auth')->name('menu');

Route::post('/regist',[LoginController::class,'register'])->name('validar-registro');
Route::post('/login',[LoginController::class,'login'])->name('inicia-sesion');
Route::get('/logout',[LoginController::class,'logout'])->name('logout');