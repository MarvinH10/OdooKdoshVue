<?php

use App\Http\Controllers\FormulariosController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ReposicionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::controller(ProductosController::class)->group(function () {
        Route::get('/productos', 'index')->name('productos');
        Route::get('/productos/traer', 'traer');
        Route::post('/productos/almacenar', 'almacenar');
        Route::put('/productos/actualizar/{id}', 'actualizar');
        Route::delete('/productos/eliminar/{id}', 'quitar');
        Route::get('/productos_favoritos/traer', 'traerProductosFavoritos');
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
        Route::get('/reposicion/data_repo/traer', 'traerDatosRepo');
    });
});
