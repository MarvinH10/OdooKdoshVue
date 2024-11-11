<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Ripcord\Ripcord;

class OdooService
{
    protected $url;
    protected $db;
    protected $username;
    protected $password;
    protected $uid;
    protected $models;

    public function __construct()
    {
        $this->url = 'https://testing-for-api.odoo.com/';
        $this->db = 'testing-for-api';
        $this->models = Ripcord::client("$this->url/xmlrpc/2/object");
    }

    public function authenticate()
    {
        $user = Auth::user();

        if (!$user) {
            throw new \Exception('Usuario no autenticado');
        }

        $common = Ripcord::client("$this->url/xmlrpc/2/common");
        $this->username = $user->email;
        $this->password = $user->token;
        $this->uid = $common->authenticate($this->db, $this->username, $this->password, array());

        if (!$this->uid) {
            throw new \Exception('Error de autenticación con Odoo');
        }
        return $this->uid;
    }

    /*********************TRAER DATOS DE ODOO 17 PARA PRODUCTOS*********************/
    public function getProductFavorites()
    {
        $this->authenticate();

        return $this->models->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
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

    public function getCategorias()
    {
        $this->authenticate();

        return $this->models->execute_kw($this->db, $this->uid, $this->password,
            'product.category', 'search_read',
            array(array(array('parent_id', '=', false))),
            array('fields' => array('id', 'name'))
        );
    }

    public function getSubcategories($id)
    {
        $this->authenticate();

        $parentId = (int) $id;
        return $this->models->execute_kw($this->db, $this->uid, $this->password,
            'product.category', 'search_read',
            array(array(array('parent_id', '=', $parentId))),
            array('fields' => array('id', 'name'))
        );
    }

    public function getAttributos()
    {
        $this->authenticate();

        return $this->models->execute_kw($this->db, $this->uid, $this->password,
            'product.attribute', 'search_read',
            array(),
            array('fields' => array('id', 'name'))
        );
    }

    public function getValueAttributos($id)
    {
        $this->authenticate();

        return $this->models->execute_kw($this->db, $this->uid, $this->password,
            'product.attribute.value', 'search_read',
            array(array(array('attribute_id', '=', (int) $id))),
            array('fields' => array('id', 'name'))
        );
    }

    public function getFavoriteData($userId)
    {
        $this->authenticate();

        try {
            $products = $this->models->execute_kw(
                $this->db,
                $this->uid,
                $this->password,
                'product.product',
                'search_read',
                [
                    [['is_favorite', '=', true], ['create_uid', '=', $userId]],
                ],
                [
                    'fields' => ['id', 'default_code', 'name', 'product_tmpl_id', 'product_template_attribute_value_ids', 'create_uid'],
                ]
            );

            foreach ($products as &$product) {
                if (!empty($product['product_template_attribute_value_ids'])) {
                    $attributeValues = $this->models->execute_kw(
                        $this->db,
                        $this->uid,
                        $this->password,
                        'product.template.attribute.value',
                        'search_read',
                        [
                            [['id', 'in', $product['product_template_attribute_value_ids']]],
                        ],
                        ['fields' => ['id', 'name']]
                    );

                    $product['attribute_values'] = array_map(function ($attributeValue) {
                        return $attributeValue['name'];
                    }, $attributeValues);
                } else {
                    $product['attribute_values'] = "";
                }
            }

            return $products;

        } catch (\Exception $e) {
            Log::error('Error fetching product references:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function getDataRepo()
    {
        $this->authenticate();

        try {
            $products = $this->models->execute_kw(
                $this->db,
                $this->uid,
                $this->password,
                'product.product',
                'search_read',
                [
                    [['type', '=', 'consu']],
                ],
                [
                    'fields' => ['id', 'name', 'default_code', 'product_tmpl_id', 'product_template_attribute_value_ids'],
                ]
            );

            foreach ($products as &$product) {
                if (!empty($product['product_template_attribute_value_ids']) && is_array($product['product_template_attribute_value_ids'])) {
                    $attributeValues = $this->models->execute_kw(
                        $this->db,
                        $this->uid,
                        $this->password,
                        'product.template.attribute.value',
                        'search_read',
                        [
                            [['id', 'in', $product['product_template_attribute_value_ids']]],
                        ],
                        ['fields' => ['id', 'name']]
                    );

                    if (!empty($attributeValues)) {
                        $product['attribute_values'] = array_map(function ($attributeValue) {
                            return $attributeValue['name'];
                        }, $attributeValues);
                    }
                }
            }

            return $products;

        } catch (\Exception $e) {
            Log::error('Error fetching product references:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /*********************GUARDAR DATOS DE ODOO 17 PARA PRODUCTOS*********************/
    public function createProduct($data)
    {
        $this->authenticate();

        try {
            Log::info('Enviando datos para crear producto en Odoo', ['data' => $data]);
            $productId = $this->models->execute_kw($this->db, $this->uid, $this->password,
                'product.template', 'create', array($data));
            Log::info('Producto creado en Odoo', ['productId' => $productId]);
            return $productId;
        } catch (\Exception $e) {
            Log::error('Error creating product in Odoo:', ['message' => $e->getMessage(), 'data' => $data]);
            return null;
        }
    }

    public function createVariant($productId, $attributes)
    {
        $this->authenticate();

        try {
            Log::info('Creando variantes para el producto en Odoo', ['productId' => $productId]);

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
                Log::info('Creando línea de atributo', ['variant_data' => $variant_data]);
                $attribute_line_id = $this->models->execute_kw($this->db, $this->uid, $this->password, 'product.template.attribute.line', 'create', [$variant_data]);

                foreach ($attribute_value_ids as $value_id) {
                    if (isset($attribute_references[$value_id])) {
                        $reference_extra = $attribute_references[$value_id];
                        $variant_ids = $this->models->execute_kw($this->db, $this->uid, $this->password, 'product.product', 'search', [
                            [['product_tmpl_id', '=', $productId],
                                ['product_template_attribute_value_ids.product_attribute_value_id', '=', $value_id]],
                        ]);

                        foreach ($variant_ids as $variant_id) {
                            Log::info('Actualizando referencia interna de la variante', ['variant_id' => $variant_id, 'reference_extra' => $reference_extra]);
                            $this->models->execute_kw($this->db, $this->uid, $this->password, 'product.product', 'write', [[$variant_id], ['default_code' => $reference_extra]]);
                        }
                    }

                    if (isset($attribute_prices_extra[$value_id])) {
                        $price_extra = $attribute_prices_extra[$value_id];
                        $template_attribute_value_id = $this->models->execute_kw($this->db, $this->uid, $this->password, 'product.template.attribute.value', 'search', [
                            [['product_tmpl_id', '=', $productId],
                                ['product_attribute_value_id', '=', $value_id]],
                        ]);

                        if (!empty($template_attribute_value_id)) {
                            Log::info('Actualizando price_extra del atributo', ['template_attribute_value_id' => $template_attribute_value_id[0], 'price_extra' => $price_extra]);
                            $this->models->execute_kw($this->db, $this->uid, $this->password, 'product.template.attribute.value', 'write', [[$template_attribute_value_id[0]], ['price_extra' => $price_extra]]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error creando variantes en Odoo:', ['message' => $e->getMessage(), 'productId' => $productId]);
            return null;
        }
    }

    public function createProductsBatch($bulkProductData)
    {
        $this->authenticate();

        try {
            Log::info('Enviando datos en batch para crear productos en Odoo', ['data' => $bulkProductData]);
            $createdProductIds = $this->models->execute_kw(
                $this->db,
                $this->uid,
                $this->password,
                'product.template',
                'create',
                [$bulkProductData]
            );

            Log::info('Productos creados en Odoo', ['productIds' => $createdProductIds]);
            return $createdProductIds;
        } catch (\Exception $e) {
            Log::error('Error creando productos en batch en Odoo:', ['message' => $e->getMessage(), 'data' => $bulkProductData]);
            return [];
        }
    }

    public function convertirFavoritosANoFavoritos($userId)
    {
        $this->authenticate();

        try {
            $favoritos = $this->getFavoriteData($userId);

            if ($favoritos) {
                $productoIds = array_column($favoritos, 'id');

                $this->models->execute_kw(
                    $this->db,
                    $this->uid,
                    $this->password,
                    'product.product',
                    'write',
                    [
                        $productoIds,
                        ['is_favorite' => false],
                    ]
                );

                Log::info('Productos favoritos actualizados a no favoritos');
                return $productoIds;
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Error updating favorite products to non-favorites:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    //EN BASE DE DATOS ALMACENANDO
    public function traerProductosConsumibles()
    {
        $this->authenticate();

        return $this->models->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
            'product.template',
            'search_read',
            [
                [['type', '=', 'consu']],
            ],
            [
                'fields' => ['id', 'name', 'type'],
            ]
        );
    }

    public function traerCategorias()
    {
        $this->authenticate();

        return $this->models->execute_kw($this->db, $this->uid, $this->password,
            'product.category', 'search_read',
            array(),
            array('fields' => array('id', 'name', 'parent_id'))
        );
    }

    public function traerSubcategorias($id)
    {
        $this->authenticate();

        return $this->models->execute_kw($this->db, $this->uid, $this->password,
            'product.category', 'search_read',
            array(array(array('parent_id', '=', $id))),
            array('fields' => array('id', 'name', 'parent_id'))
        );
    }

    public function traerAtributos()
    {
        $this->authenticate();

        return $this->models->execute_kw($this->db, $this->uid, $this->password,
            'product.attribute', 'search_read',
            array(),
            array('fields' => array('id', 'name'))
        );
    }

    public function traerValoresAtributos($id)
    {
        $this->authenticate();

        return $this->models->execute_kw($this->db, $this->uid, $this->password,
            'product.attribute.value', 'search_read',
            array(array(array('attribute_id', '=', (int) $id))),
            array('fields' => array('id', 'name'))
        );
    }
}
