<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('register', [AuthController::class, 'showRegisterComensal'])->name('register.comensal');
Route::post('register', [AuthController::class, 'registerComensal'])->name('register.comensal.post');

// Rutas de Comensal
Route::middleware(['auth:comensal'])->group(function () {
    Route::get('/inicio', function () {
        return view('comensal.inicio');
    })->name('inicio');
});

// Rutas de Restaurante / Usuario
Route::middleware(['auth:usuario'])->group(function () {
    Route::resource('productos', ProductoController::class)->except(['show']);
});
