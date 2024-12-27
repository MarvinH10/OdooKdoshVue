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
}
