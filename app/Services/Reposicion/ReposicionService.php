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

    public function traerDatosReposicion($default_code)
    {
        try {

            $products = $this->modelos->execute_kw(
                $this->base_datos,
                $this->uid,
                $this->contraseña,
                'product.product',
                'search_read',
                [
                    [['detailed_type', '=', 'product'], ['default_code', 'ilike', "{$default_code}%"]],
                ],
                [
                    'fields' => ['id', 'name', 'default_code', 'product_tmpl_id', 'product_template_attribute_value_ids'],
                ]
            );

            foreach ($products as &$product) {
                if (!empty($product['product_template_attribute_value_ids']) && is_array($product['product_template_attribute_value_ids'])) {
                    $attributeValues = $this->modelos->execute_kw(
                        $this->base_datos,
                        $this->uid,
                        $this->contraseña,
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

                $xmlDato = $this->modelos->execute_kw(
                    $this->base_datos,
                    $this->uid,
                    $this->contraseña,
                    'ir.model.data',
                    'search_read',
                    [
                        [['model', '=', 'product.product'], ['res_id', '=', $product['id']]],
                    ],
                    ['fields' => ['module', 'name']]
                );

                if (!empty($xmlDato)) {
                    $product['xml_id'] = $xmlDato[0]['module'] . '.' . $xmlDato[0]['name'];
                } else {
                    $product['xml_id'] = null;
                }
            }

            return $products;
        } catch (Exception $e) {
            Log::error('Error al traer los datos de la reposición:', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
