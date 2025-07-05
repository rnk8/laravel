<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuditDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Rutas que requieren autenticación
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // APIs del Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'productStats'])->name('dashboard.stats');
    Route::get('/dashboard/charts/{type}', [DashboardController::class, 'chartData'])->name('dashboard.charts');
    Route::get('/dashboard/quick-stats', [DashboardController::class, 'quickStats'])->name('dashboard.quick-stats');

    // Gestión de Productos
    Route::resource('products', ProductController::class);
    
    // APIs de Productos
    Route::get('/api/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::patch('/products/{product}/stock', [ProductController::class, 'updateStock'])->name('products.update-stock');
    Route::patch('/products/{product}/featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
    Route::get('/products/export/csv', [ProductController::class, 'export'])->name('products.export');

    // Dashboard de Auditoría
    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('/dashboard', [AuditDashboardController::class, 'index'])->name('dashboard');
        Route::get('/logs', [AuditDashboardController::class, 'logs'])->name('logs');
        Route::get('/suspicious', [AuditDashboardController::class, 'suspicious'])->name('suspicious');
        Route::get('/reports', [AuditDashboardController::class, 'reports'])->name('reports');
        Route::get('/penetration-tests', [AuditDashboardController::class, 'penetrationTests'])->name('penetration-tests');
    });

    // Perfil de Usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
