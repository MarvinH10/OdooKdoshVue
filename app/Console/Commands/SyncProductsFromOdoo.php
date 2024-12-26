<?php

namespace App\Console\Commands;

use App\Models\Atributo;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\ValorAtributo;
use App\Services\ServicioOdoo;
use Illuminate\Console\Command;

class SyncProductsFromOdoo extends Command
{
    protected $signature = 'sync:productos-odoo';
    protected $description = 'Sync consumable products and concatenated categories from Odoo to the local database';
    protected $servicioOdoo;

    public function __construct(ServicioOdoo $servicioOdoo)
    {
        parent::__construct();
        $this->servicioOdoo = $servicioOdoo;
    }

    public function handle()
    {
        $this->syncProductosConsumibles();
        $this->syncCategorias();
        $this->syncAtributos();
        $this->info('Sincronización de productos y categorías completada.');
    }

    // private function syncProductosConsumibles()
    // {
    //     $productosOdoo = $this->servicioOdoo->traerProductosConsumibles();

    //     foreach ($productosOdoo as $productoOdoo) {
    //         $producto = Producto::updateOrCreate(
    //             ['id' => $productoOdoo['id']],
    //             [
    //                 'pro_nombre' => $productoOdoo['name'],
    //                 'tipo' => $productoOdoo['type'],
    //             ]
    //         );

    //         $this->info("Producto sincronizado: {$producto->pro_nombre}");
    //     }
    // }

    private function syncCategorias()
    {
        $categoriasOdoo = $this->servicioOdoo->traerCategorias();

        foreach ($categoriasOdoo as $categoriaOdoo) {
            $nombreCompleto = $this->concatenarCategorias($categoriaOdoo);

            $categoria = Categoria::updateOrCreate(
                ['id' => $categoriaOdoo['id']],
                ['cat_nombre' => $nombreCompleto]
            );

            $this->info("Categoría sincronizada: {$categoria->cat_nombre}");
        }
    }

    private function concatenarCategorias($categoriaOdoo)
    {
        $subcategoriasOdoo = $this->servicioOdoo->traerSubcategorias($categoriaOdoo['id']);

        $nombres = [$categoriaOdoo['name']];

        foreach ($subcategoriasOdoo as $subcategoriaOdoo) {
            $nombres[] = $subcategoriaOdoo['name'];
        }

        return implode(' / ', $nombres);
    }

    private function syncAtributos()
    {
        $atributosOdoo = $this->servicioOdoo->traerAtributos();

        foreach ($atributosOdoo as $atributoOdoo) {
            $atributo = Atributo::updateOrCreate(
                ['id' => $atributoOdoo['id']],
                ['atr_nombre' => $atributoOdoo['name']]
            );

            $this->info("Atributo sincronizado: {$atributo->atr_nombre}");

            $this->syncValoresAtributos($atributoOdoo['id'], $atributo->id);
        }
    }

    private function syncValoresAtributos($atributoOdooId, $atributoLocalId)
    {
        $valoresAtributoOdoo = $this->servicioOdoo->traerValoresAtributos($atributoOdooId);

        foreach ($valoresAtributoOdoo as $valorAtributoOdoo) {
            $valorAtributo = ValorAtributo::updateOrCreate(
                ['id' => $valorAtributoOdoo['id']],
                [
                    'atr_id' => $atributoLocalId,
                    'val_nombre' => $valorAtributoOdoo['name'],
                ]
            );

            $this->info("Valor de atributo sincronizado: {$valorAtributo->val_nombre}");
        }
    }
}
