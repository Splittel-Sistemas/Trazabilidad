<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Layouts.Menu');
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