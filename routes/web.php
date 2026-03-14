<?php

use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::middleware(['auth', 'verified'])->group(function () {


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.pdf');

    Route::resource('products', ProductController::class);

    Route::resource('categories', CategoryController::class);

    Route::post('/stock-movements', [StockMovementController::class, 'store'])->name('stock-movements.store');
    Route::get('/history', [StockMovementController::class, 'index'])->name('history.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::post('/stock-movements/adjust', [StockMovementController::class, 'adjust'])->name('stock.adjust');
});

require __DIR__ . '/auth.php';