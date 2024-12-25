<?php

namespace App\Services\Producto;

use Exception;
use Illuminate\Support\Facades\Log;

class ProductoService
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

    public function traerProductosFavoritos()
    {
        return $this->modelos->execute_kw(
            $this->base_datos,
            $this->uid,
            $this->contraseña,
            'product.template',
            'search_read',
            [
                [['is_favorite', '=', true]],
            ],
            [
                'fields' => ['id', 'name', 'list_price', 'default_code'],
            ]
        );
    }

    public function traerCategorias()
    {
        return $this->modelos->execute_kw(
            $this->base_datos,
            $this->uid,
            $this->contraseña,
            'product.category',
            'search_read',
            [
                [['parent_id', '=', false]],
            ],
            [
                'fields' => ['id', 'name'],
            ]
        );
    }

    public function traerSubcategorias($id)
    {
        return $this->modelos->execute_kw(
            $this->base_datos,
            $this->uid,
            $this->contraseña,
            'product.category',
            'search_read',
            [
                [['parent_id', '=', (int) $id]],
            ],
            [
                'fields' => ['id', 'name'],
            ]
        );
    }

    public function traerAtributos()
    {
        return $this->modelos->execute_kw(
            $this->base_datos,
            $this->uid,
            $this->contraseña,
            'product.attribute',
            'search_read',
            [],
            [
                'fields' => ['id', 'name'],
            ]
        );
    }

    public function traerValoresAtributos($id)
    {
        return $this->modelos->execute_kw(
            $this->base_datos,
            $this->uid,
            $this->contraseña,
            'product.attribute.value',
            'search_read',
            [
                [['attribute_id', '=', (int) $id]],
            ],
            [
                'fields' => ['id', 'name'],
            ]
        );
    }

    public function createProductsBatch($bulkProductData)
    {
        try {
            Log::info('Enviando datos en batch para crear productos en Odoo', ['data' => $bulkProductData]);
            $createdProductIds = $this->modelos->execute_kw(
                $this->base_datos,
                $this->uid,
                $this->contraseña,
                'product.template',
                'create',
                [$bulkProductData]
            );

            return $createdProductIds;
        } catch (Exception $e) {
            Log::error('Error creando productos en batch en Odoo:', ['message' => $e->getMessage(), 'data' => $bulkProductData]);
            return [];
        }
    }

    public function createVariant($productId, $attributes)
    {
        try {
            foreach ($attributes as $attribute) {
                $attribute_id = (int) $attribute['attribute_id'];
                $attribute_value_ids = array_map('intval', $attribute['value_ids']);
                $attribute_references = [];
                $attribute_prices_extra = [];

                if (isset($attribute['extra_references'])) {
                    $attribute_references = array_combine($attribute_value_ids, $attribute['extra_references']);
                }

                if (isset($attribute['extra_prices'])) {
                    $attribute_prices_extra = array_combine($attribute_value_ids, $attribute['extra_prices']);
                }

                $variant_data = [
                    'product_tmpl_id' => $productId,
                    'attribute_id' => $attribute_id,
                    'value_ids' => [[6, 0, $attribute_value_ids]],
                ];
                $this->modelos->execute_kw($this->base_datos, $this->uid, $this->contraseña, 'product.template.attribute.line', 'create', [$variant_data]);

                foreach ($attribute_value_ids as $value_id) {
                    if (isset($attribute_references[$value_id])) {
                        $reference_extra = $attribute_references[$value_id];
                        $variant_ids = $this->modelos->execute_kw($this->base_datos, $this->uid, $this->contraseña, 'product.product', 'search', [
                            [['product_tmpl_id', '=', $productId],
                                ['product_template_attribute_value_ids.product_attribute_value_id', '=', $value_id]],
                        ]);

                        foreach ($variant_ids as $variant_id) {
                            $this->modelos->execute_kw($this->base_datos, $this->uid, $this->contraseña, 'product.product', 'write', [[$variant_id], ['default_code' => $reference_extra]]);
                        }
                    }

                    if (isset($attribute_prices_extra[$value_id])) {
                        $price_extra = $attribute_prices_extra[$value_id];
                        $template_attribute_value_id = $this->modelos->execute_kw($this->base_datos, $this->uid, $this->contraseña, 'product.template.attribute.value', 'search', [
                            [['product_tmpl_id', '=', $productId],
                                ['product_attribute_value_id', '=', $value_id]],
                        ]);

                        if (!empty($template_attribute_value_id)) {
                            $this->modelos->execute_kw($this->base_datos, $this->uid, $this->contraseña, 'product.template.attribute.value', 'write', [[$template_attribute_value_id[0]], ['price_extra' => $price_extra]]);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Log::error('Error creando variantes en Odoo:', ['message' => $e->getMessage(), 'productId' => $productId]);
            return null;
        }
    }
}
