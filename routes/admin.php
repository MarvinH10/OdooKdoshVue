<?php

use App\Http\Controllers\ProductosController;
use App\Http\Controllers\FormulariosController;
use App\Http\Controllers\ReposicionController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::controller(ProductosController::class)->group(function () {
        Route::get('/productos', 'index')->name('productos');
        Route::get('/productos/traer', 'traer');
        // Route::post('/productos/almacenar', 'almacenar');
        Route::put('/productos/actualizar/{id}', 'actualizar');
        Route::delete('/productos/eliminar/{id}', 'quitar');
        Route::get('/productos_favoritos/traer', 'traerProductosFavoritos');
        Route::get('/categoriaspdv/traer', 'traerCategoriasPDV');
        Route::get('/categorias/traer', 'traerCategorias');
        Route::get('/subcategorias/traer/{id}', 'traerSubcategorias');
        Route::get('/atributos/traer', 'traerAtributos');
        Route::get('/valores_atributos/traer/{id}', 'traerValoresAtributos');
        Route::post('/productos/registrar_todo', 'registrarProductos');
    });

    Route::controller(FormulariosController::class)->group(function () {
        Route::get('/formulas', 'index')->name('formulas');
        Route::get('/formulas/data_favorite/traer', 'traerDatosFavoritos');
        Route::post('/formulas/data_favorite/convertir_no_favoritos', 'convertirFavoritosANoFavoritos');
    });

    Route::controller(ReposicionController::class)->group(function () {
        Route::get('/reposicion', 'index')->name('reposicion');
        Route::get('/reposicion/data_repo/traer/referencia/{default_code}', 'traerDatosReposicionxReferencia');
        Route::get('/reposicion/data_repo/traer/nombre/{name}', 'traerDatosReposicionxName');
    });

    Route::controller(BarcodeController::class)->group(function () {
        Route::get('/barcode', 'index')->name('barcode');
        Route::get('/barcode/traer/{id}', 'traerProductosById');
        Route::get('/barcode/traer/purchase.order/{id}', 'traerProductosByIdOrdenCompra');
    });

    Route::controller(ReporteController::class)->group(function () {
        Route::get('/reporte', 'index')->name('reporte');
        Route::get('/reporte/traer/{id}', 'traerDatosOrdenCompra');
    });

    // Route::controller(DestinoController::class)->group(function () {
    //     Route::get('/destinos', 'index')->name('destinos.index');
    // });

    // Route::controller(DigitadorController::class)->group(function () {
    //     Route::get('/digitadores', 'index')->name('digitadores.index');
    // });
});
