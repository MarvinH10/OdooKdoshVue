<?php

use App\Http\Controllers\OdooController;
use App\Http\Controllers\ProductosController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::get('/productos', [ProductosController::class, 'index']);
Route::post('/productos', [ProductosController::class, 'store']);
Route::delete('/productos/{id}', [ProductosController::class, 'destroy']);
Route::get('/odoo/categories', [ProductosController::class, 'getCategories']);
Route::get('/odoo/categories/{parentId}', [ProductosController::class, 'getSubcategories']);

require __DIR__ . '/admin.php';
