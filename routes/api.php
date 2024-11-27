<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ApiRegistroController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/users', [ApiController::class, 'users']); 
Route::post('/login', [ApiController::class, 'login']);
Route::resource('registro', ApiRegistroController::class);
