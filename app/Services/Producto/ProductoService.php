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
                [['priority', '=', '0']],
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

            if (!empty($createdProductIds)) {
                $irModelDataRecords = [];

                foreach ($createdProductIds as $productId) {
                    $uniqueXmlIdTemplate = 'product_template_' . $productId . '_' . time();

                    $irModelDataRecords[] = [
                        'name' => $uniqueXmlIdTemplate,
                        'module' => 'kdosh_module',
                        'model' => 'product.template',
                        'res_id' => $productId,
                        'noupdate' => false
                    ];

                    $variantIds = $this->modelos->execute_kw(
                        $this->base_datos,
                        $this->uid,
                        $this->contraseña,
                        'product.product',
                        'search',
                        [[['product_tmpl_id', '=', $productId]]]
                    );

                    if (!empty($variantIds)) {
                        foreach ($variantIds as $variantId) {
                            $uniqueXmlIdProduct = 'product_product_' . $variantId . '_' . time();

                            $irModelDataRecords[] = [
                                'name' => $uniqueXmlIdProduct,
                                'module' => 'kdosh_module',
                                'model' => 'product.product',
                                'res_id' => $variantId,
                                'noupdate' => false
                            ];
                        }
                    }
                }

                $this->modelos->execute_kw(
                    $this->base_datos,
                    $this->uid,
                    $this->contraseña,
                    'ir.model.data',
                    'create',
                    [$irModelDataRecords]
                );

                Log::info('XML IDs creados en ir.model.data', ['data' => $irModelDataRecords]);
            }

            return $createdProductIds;
        } catch (Exception $e) {
            Log::error('Error creando productos en batch en Odoo:', ['message' => $e->getMessage(), 'data' => $bulkProductData]);
            return [];
        }
    }

    public function createVariant($productId, $attributes)
    {
        try {
            $allVariantIds = [];

            foreach ($attributes as $attribute) {
                $attributeId = (int) $attribute['attributeId'];
                $attributeValueIds = array_map(function ($value) {
                    return (int) $value['id'];
                }, $attribute['attributeValues']);

                $attributeReferences = [];
                $attributePricesExtra = [];

                foreach ($attribute['attributeValues'] as $value) {
                    $valueId = $value['id'];
                    if (isset($attribute['referencesInternal']["{$valueId}_extraPrice"])) {
                        $attributePricesExtra[$valueId] = (float) $attribute['referencesInternal']["{$valueId}_extraPrice"];
                    }
                    if (isset($attribute['referencesInternal']["{$valueId}"])) {
                        $attributeReferences[$valueId] = $attribute['referencesInternal']["{$valueId}"];
                    }
                }

                $variantData = [
                    'product_tmpl_id' => $productId,
                    'attribute_id' => $attributeId,
                    'value_ids' => [[6, 0, $attributeValueIds]],
                ];
                $this->modelos->execute_kw(
                    $this->base_datos,
                    $this->uid,
                    $this->contraseña,
                    'product.template.attribute.line',
                    'create',
                    [$variantData]
                );

                foreach ($attributeValueIds as $valueId) {
                    if (isset($attributeReferences[$valueId])) {
                        $referenceExtra = $attributeReferences[$valueId];

                        $variantIds = $this->modelos->execute_kw(
                            $this->base_datos,
                            $this->uid,
                            $this->contraseña,
                            'product.product',
                            'search',
                            [
                                [
                                    ['product_tmpl_id', '=', $productId],
                                    ['product_template_attribute_value_ids.product_attribute_value_id', '=', $valueId]
                                ]
                            ]
                        );

                        foreach ($variantIds as $variantId) {
                            $this->modelos->execute_kw(
                                $this->base_datos,
                                $this->uid,
                                $this->contraseña,
                                'product.product',
                                'write',
                                [[$variantId], ['default_code' => $referenceExtra]]
                            );

                            $allVariantIds[] = $variantId;
                        }
                    }

                    if (isset($attributePricesExtra[$valueId])) {
                        $priceExtra = $attributePricesExtra[$valueId];
                        $templateAttributeValueId = $this->modelos->execute_kw(
                            $this->base_datos,
                            $this->uid,
                            $this->contraseña,
                            'product.template.attribute.value',
                            'search',
                            [
                                [
                                    ['product_tmpl_id', '=', $productId],
                                    ['product_attribute_value_id', '=', $valueId]
                                ]
                            ]
                        );

                        if (!empty($templateAttributeValueId)) {
                            $this->modelos->execute_kw(
                                $this->base_datos,
                                $this->uid,
                                $this->contraseña,
                                'product.template.attribute.value',
                                'write',
                                [[$templateAttributeValueId[0]], ['price_extra' => $priceExtra]]
                            );
                        }
                    }
                }
            }

            if (!empty($allVariantIds)) {
                $irModelDataRecords = [];
                foreach ($allVariantIds as $variantId) {
                    $uniqueXmlIdProduct = 'product_product_' . $variantId . '_' . time();

                    $irModelDataRecords[] = [
                        'name' => $uniqueXmlIdProduct,
                        'module' => 'kdosh_module',
                        'model' => 'product.product',
                        'res_id' => $variantId,
                        'noupdate' => false
                    ];
                }

                if (!empty($irModelDataRecords)) {
                    $this->modelos->execute_kw(
                        $this->base_datos,
                        $this->uid,
                        $this->contraseña,
                        'ir.model.data',
                        'create',
                        [$irModelDataRecords]
                    );
                    Log::info('XML IDs creados en ir.model.data para todas las variantes de product.product', ['data' => $irModelDataRecords]);
                }
            }
        } catch (Exception $e) {
            Log::error('Error creando variantes en Odoo:', [
                'message' => $e->getMessage(),
                'productId' => $productId,
                'attributes' => $attributes,
            ]);
            return null;
        }
    }
}
