<?php

namespace App\Services\Barcode;

use Exception;
use Illuminate\Support\Facades\Log;

class BarcodeService
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

    public function traerProductosById($producto_Id)
    {
        try {
            $producto = $this->modelos->execute_kw(
                $this->base_datos,
                $this->uid,
                $this->contraseña,
                'product.template',
                'search_read',
                [
                    [['id', '=', $producto_Id]]
                ],
                [
                    'fields' => [
                        'id',
                        'name',
                        'list_price',
                        'categ_id',
                        'barcode',
                        'default_code',
                        'product_variant_ids'
                    ],
                ]
            );

            if (!empty($producto) && !empty($producto[0]['product_variant_ids'])) {
                $variantes = $this->modelos->execute_kw(
                    $this->base_datos,
                    $this->uid,
                    $this->contraseña,
                    'product.product',
                    'search_read',
                    [
                        [['id', 'in', $producto[0]['product_variant_ids']]]
                    ],
                    [
                        'fields' => [
                            'id',
                            'default_code',
                            'barcode',
                            'lst_price',
                            'product_template_attribute_value_ids'
                        ],
                    ]
                );

                foreach ($variantes as &$variante) {
                    if (!empty($variante['product_template_attribute_value_ids'])) {
                        $atributos = $this->modelos->execute_kw(
                            $this->base_datos,
                            $this->uid,
                            $this->contraseña,
                            'product.template.attribute.value',
                            'search_read',
                            [
                                [['id', 'in', $variante['product_template_attribute_value_ids']]]
                            ],
                            [
                                'fields' => ['id', 'name', 'attribute_id']
                            ]
                        );

                        $variante['atributos'] = array_map(function ($atributo) {
                            return $atributo['attribute_id'][1] . ': ' . $atributo['name'];
                        }, $atributos);
                    } else {
                        $variante['atributos'] = [];
                    }
                }

                $producto[0]['variantes'] = $variantes;
            } else {
                unset($producto[0]['product_variant_ids']);
            }

            return $producto;
        } catch (Exception $e) {
            Log::error('Error al traer el producto:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function traerProductosByIdOrdenCompra($order_id)
    {
        try {
            $order_id = (int) $order_id;

            $productos = $this->modelos->execute_kw(
                $this->base_datos,
                $this->uid,
                $this->contraseña,
                'purchase.order.line',
                'search_read',
                [
                    [['order_id', '=', $order_id]]
                ],
                ['fields' => ['product_id']]
            );

            if (empty($productos)) {
                return [];
            }

            $product_ids = array_map(fn($p) => $p['product_id'][0], $productos);

            $categorias = $this->modelos->execute_kw(
                $this->base_datos,
                $this->uid,
                $this->contraseña,
                'product.product',
                'search_read',
                [
                    [['id', 'in', $product_ids]]
                ],
                ['fields' => ['id', 'categ_id', 'barcode', 'lst_price']]
            );

            $resultados = array_map(function ($producto) use ($productos) {
                $product = current(array_filter($productos, fn($p) => $p['product_id'][0] === $producto['id']));
                $producto['product_id'] = $product['product_id'];
                return $producto;
            }, $categorias);

            return $resultados;
        } catch (Exception $e) {
            Log::error('Error al traer las categorías:', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
