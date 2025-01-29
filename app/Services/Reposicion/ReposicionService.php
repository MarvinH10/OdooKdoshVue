<?php

namespace App\Services\Reposicion;

use Exception;
use Illuminate\Support\Facades\Log;

class ReposicionService
{
    protected $modelos;
    protected $base_datos;
    protected $uid;
    protected $contraseña;

    public function __construct($modelos, $base_datos, $uid, $contraseña)
    {
        $this->modelos = $modelos;
        $this->base_datos = $base_datos;
        $this->uid = $uid;
        $this->contraseña = $contraseña;
    }

    public function traerDatosReposicion()
    {
        try {
            Log::info('🔍 Iniciando consulta optimizada de productos desde Odoo...');
            $offset = 0;
            $limit = 100;
            $todosProductos = [];

            $fields = $this->modelos->execute_kw(
                $this->base_datos,
                $this->uid,
                $this->contraseña,
                'product.product',
                'fields_get',
                [],
                ['attributes' => ['string', 'type']]
            );

            if (!array_key_exists('detailed_type', $fields)) {
                Log::warning('⚠️ detailed_type no existe en product.product. Usando type en su lugar.');
                $filterField = 'type';
            } else {
                $filterField = 'detailed_type';
            }

            do {
                Log::info("📌 Consultando productos desde offset: {$offset}");

                $products = $this->modelos->execute_kw(
                    $this->base_datos,
                    $this->uid,
                    $this->contraseña,
                    'product.product',
                    'search_read',
                    [[[$filterField, '=', 'product']]],
                    [
                        'fields' => ['id', 'name', 'default_code', 'product_tmpl_id', 'product_template_attribute_value_ids'],
                        'limit' => $limit,
                        'offset' => $offset
                    ]
                );

                if (!is_array($products)) {
                    Log::error('⛔ La respuesta de Odoo no es un array.', ['response' => $products]);
                    return [];
                }

                Log::info('✅ Productos obtenidos en este lote:', ['count' => count($products)]);

                foreach ($products as &$product) {
                    if (!empty($product['product_template_attribute_value_ids']) && is_array($product['product_template_attribute_value_ids'])) {
                        $product['attribute_values'] = array_map(function ($attributeId) {
                            return $attributeId;
                        }, $product['product_template_attribute_value_ids']);
                    }
                }

                $todosProductos = array_merge($todosProductos, $products);
                $offset += $limit;

            } while (count($products) === $limit);

            Log::info('✅ Consulta finalizada. Total de productos obtenidos:', ['total' => count($todosProductos)]);
            return $todosProductos;

        } catch (Exception $e) {
            Log::error('❌ Error en traerDatosReposicion:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
