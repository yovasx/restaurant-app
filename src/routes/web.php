<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminBackupController;
use App\Http\Controllers\RestauranteController;
use App\Http\Controllers\ComensalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $guards = [];

    if (auth()->guard('comensal')->check()) {
        $guards[] = 'comensal';
    }

    if (auth()->guard('admin')->check()) {
        $guards[] = 'admin';
    }

    if (auth()->guard('restaurante')->check()) {
        $guards[] = 'restaurante';
    }

    if (count($guards) === 1) {
        return match ($guards[0]) {
            'comensal' => redirect()->route('comensal.inicio'),
            'admin' => redirect()->route('admin.dashboard'),
            'restaurante' => redirect()->route('restaurante.dashboard'),
        };
    }

    $query = \App\Models\Restaurante::where('restaurantes.estado', 'activo');

    if (request()->filled('categoria') && request()->categoria !== 'todos') {
        $query->whereHas('categorias', function ($q) {
            $q->where('categorias.id', request()->categoria);
        });
    }

    $restaurants = $query
        ->leftJoin('restaurantes_stats as stats', 'stats.restaurante_id', '=', 'restaurantes.id')
        ->select('restaurantes.*', 'stats.avg_rating', 'stats.avg_price')
        ->latest('restaurantes.created_at')
        ->take(12)
        ->get();

    $categorias = \App\Models\Categoria::where('estado', 'activo')->get();

    return view('welcome', compact('restaurants', 'categorias'));
})->name('home');

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('login/admin', function () {
    return redirect()->route('login', ['role' => 'admin']);
})->name('login.admin');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout/comensal', [AuthController::class, 'logoutComensal'])->name('logout.comensal');
Route::post('logout/admin', [AuthController::class, 'logoutAdmin'])->name('logout.admin');
Route::post('logout/restaurante', [AuthController::class, 'logoutRestaurante'])->name('logout.restaurante');

Route::get('register', [AuthController::class, 'showRegisterComensal'])->name('register.comensal');
Route::post('register', [AuthController::class, 'registerComensal'])->name('register.comensal.post');

Route::get('register/restaurante', [AuthController::class, 'showRegisterRestaurante'])->name('register.restaurante');
Route::post('register/restaurante', [AuthController::class, 'registerRestaurante'])->name('register.restaurante.post');

// Lectura publica para invitados y comensales
Route::get('/restaurantes/nearby', [ComensalController::class, 'nearby'])->name('restaurantes.nearby');
Route::get('/restaurante/{id}', [ComensalController::class, 'show'])->where('id', '[0-9]+')->name('restaurante.show');

// Inicio publico para invitados y dashboard para comensal autenticado
Route::get('/inicio', function (\Illuminate\Http\Request $request) {
    if (auth()->guard('comensal')->check()) {
        return app(\App\Http\Controllers\ComensalController::class)->index($request);
    }
    return redirect()->route('home');
})->name('comensal.inicio');

// Rutas protegidas para comensal
Route::middleware('auth:comensal')->group(function () {
    Route::get('/explorar', [ComensalController::class, 'explorar'])->name('comensal.explorar');

    Route::get('/perfil', function () {
        return view('comensal.perfil');
    })->name('comensal.perfil');
    
    Route::post('/perfil/update', [AuthController::class, 'updatePerfilComensal'])->name('comensal.perfil.update');

});

// Rutas de Restaurante
Route::middleware('auth:restaurante')->group(function () {
    Route::get('/restaurante/panel', [RestauranteController::class, 'dashboard'])->name('restaurante.dashboard');

    Route::get('/restaurante/reportes', [\App\Http\Controllers\RestaurantReportController::class, 'index'])->name('restaurante.reportes.index');
    Route::get('/restaurante/reportes/export/excel', [\App\Http\Controllers\RestaurantReportController::class, 'exportExcel'])->name('restaurante.reportes.export.excel');
    Route::get('/restaurante/reportes/export/pdf', [\App\Http\Controllers\RestaurantReportController::class, 'exportPdf'])->name('restaurante.reportes.export.pdf');
    Route::resource('productos', ProductoController::class)->except(['show']);
    Route::post('/productos/{producto}/toggle', [ProductoController::class, 'toggle'])->name('productos.toggle');
    Route::get('/restaurante/configuracion', [RestauranteController::class, 'configuracion'])->name('restaurante.configuracion');
    Route::post('/restaurante/configuracion', [RestauranteController::class, 'updateConfiguracion'])->name('restaurante.configuracion.update');
    // Promociones
    Route::resource('restaurante/promociones', \App\Http\Controllers\PromocionController::class)
        ->names('restaurante.promociones')
        ->parameters(['promociones' => 'promocion'])
        ->except(['show']);
    // Reseñas
    Route::get('/restaurante/resenas', [\App\Http\Controllers\PromocionController::class, 'resenas'])->name('restaurante.resenas');
    // Pronósticos
    Route::get('/restaurante/pronosticos', [\App\Http\Controllers\RestaurantForecastController::class, 'index'])->name('restaurante.pronosticos.index');
    // Sucursales
    Route::get('/restaurante/sucursales', [RestauranteController::class, 'sucursalesIndex'])->name('restaurante.sucursales.index');
    Route::post('/restaurante/sucursales', [RestauranteController::class, 'storeSucursal'])->name('restaurante.sucursales.store');
    Route::post('/restaurante/sucursales/{restaurante}/update', [RestauranteController::class, 'updateSucursal'])->name('restaurante.sucursales.update');
    Route::post('/restaurante/sucursales/{restaurante}/archivar', [RestauranteController::class, 'archiveSucursal'])->name('restaurante.sucursales.archive');
    Route::post('/restaurante/sucursales/{restaurante}/principal', [RestauranteController::class, 'setSucursalPrincipal'])->name('restaurante.sucursales.set-primary');
    Route::post('/restaurante/sucursales/{restaurante}/seleccionar', [RestauranteController::class, 'selectSucursal'])->name('restaurante.sucursales.select');
});

// Rutas de Administrador
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
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

    Route::get('/reportes', [AdminReportController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/export/excel', [AdminReportController::class, 'exportExcel'])->name('reportes.export.excel');
    Route::get('/reportes/export/pdf', [AdminReportController::class, 'exportPdf'])->name('reportes.export.pdf');

    Route::get('/backups', [AdminBackupController::class, 'index'])->name('backups.index');
    Route::post('/backups/generate', [AdminBackupController::class, 'generate'])->name('backups.generate');
    Route::get('/backups/{file}', [AdminBackupController::class, 'download'])->name('backups.download');

    Route::get('/auditoria', [\App\Http\Controllers\AdminAuditController::class, 'index'])->name('auditoria.index');
});
