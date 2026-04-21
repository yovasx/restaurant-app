<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RestauranteController;
use App\Http\Controllers\ComensalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/setup', function() {
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link', ['--force' => true]);
    } catch (\Exception $e) { /* symlink may already exist */ }

    return "¡Listo! La caché fue limpiada y la Base de Datos fue re-migrada e insertada (admin@admin.com creado). Ya puedes ir a la vista de Login presionando la flecha atrás del navegador, ingresar con Restaurante usando el correo 'admin@admin.com' y clave 'admin123'.";
});

Route::get('register', [AuthController::class, 'showRegisterComensal'])->name('register.comensal');
Route::post('register', [AuthController::class, 'registerComensal'])->name('register.comensal.post');

Route::get('register/restaurante', [AuthController::class, 'showRegisterRestaurante'])->name('register.restaurante');
Route::post('register/restaurante', [AuthController::class, 'registerRestaurante'])->name('register.restaurante.post');

// Rutas protegidas para comensal
Route::middleware('auth:comensal')->group(function () {
    Route::get('/inicio', [ComensalController::class, 'index'])->name('comensal.inicio');
    Route::get('/explorar', [ComensalController::class, 'explorar'])->name('comensal.explorar');

    Route::get('/perfil', function () {
        return view('comensal.perfil');
    })->name('comensal.perfil');
    
    Route::post('/perfil/update', [AuthController::class, 'updatePerfilComensal'])->name('comensal.perfil.update');

    // API endpoint to get nearby restaurants (Haversine) - expects ?lat=&lng=
    Route::get('/restaurantes/nearby', [ComensalController::class, 'nearby'])->name('restaurantes.nearby');

    // Restaurant detail view for comensal (only numeric id to avoid route collisions)
    Route::get('/restaurante/{id}', [ComensalController::class, 'show'])->whereNumber('id')->name('restaurante.show');
});

// Rutas de Restaurante / Usuario
Route::middleware(['auth:usuario'])->group(function () {
    Route::resource('productos', ProductoController::class)->except(['show']);
    Route::post('/productos/{producto}/toggle', [ProductoController::class, 'toggle'])->name('productos.toggle');
    Route::get('/restaurante/panel', [RestauranteController::class, 'dashboard'])->name('restaurante.dashboard');
    Route::get('/restaurante/configuracion', [RestauranteController::class, 'configuracion'])->name('restaurante.configuracion');
    Route::post('/restaurante/configuracion', [RestauranteController::class, 'updateConfiguracion'])->name('restaurante.configuracion.update');
    // Promociones
    Route::resource('restaurante/promociones', \App\Http\Controllers\PromocionController::class)
        ->names('restaurante.promociones')
        ->parameters(['promociones' => 'promocion'])
        ->except(['show']);
    // Reseñas
    Route::get('/restaurante/resenas', [\App\Http\Controllers\PromocionController::class, 'resenas'])->name('restaurante.resenas');
});

// Rutas de Administrador
Route::middleware(['auth:usuario', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/usuarios/create', [AdminController::class, 'createUsuario'])->name('usuarios.create');
    Route::post('/usuarios', [AdminController::class, 'storeUsuario'])->name('usuarios.store');
    
    Route::get('/restaurantes', [AdminController::class, 'restaurantes'])->name('restaurantes.index');
    Route::get('/restaurantes/{id}/edit', [AdminController::class, 'editRestaurante'])->name('restaurantes.edit');
    Route::post('/restaurantes/{id}/delete', [AdminController::class, 'destroyRestaurante'])->name('restaurantes.destroy');
    Route::post('/restaurantes/{id}/update', [AdminController::class, 'updateRestaurante'])->name('restaurantes.update');
    
    Route::get('/comensales', [AdminController::class, 'comensales'])->name('comensales.index');
    Route::get('/comensales/{id}/edit', [AdminController::class, 'editComensal'])->name('comensales.edit');
    Route::post('/comensales/{id}/delete', [AdminController::class, 'destroyComensal'])->name('comensales.destroy');
    Route::post('/comensales/{id}/update', [AdminController::class, 'updateComensal'])->name('comensales.update');
    
    Route::get('/categorias', [AdminController::class, 'categorias'])->name('categorias.index');
    Route::post('/categorias', [AdminController::class, 'storeCategoria'])->name('categorias.store');
    Route::get('/categorias/{id}/edit', [AdminController::class, 'editCategoria'])->name('categorias.edit');
    Route::post('/categorias/{id}/update', [AdminController::class, 'updateCategoria'])->name('categorias.update');
    Route::post('/categorias/{id}/delete', [AdminController::class, 'destroyCategoria'])->name('categorias.destroy');
    
    Route::post('/roles/change', [AdminController::class, 'changeRole'])->name('roles.change');
});
