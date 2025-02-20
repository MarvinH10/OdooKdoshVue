<?php

namespace App\Services\Formulario;

use Exception;
use Illuminate\Support\Facades\Log;

class FormularioService
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
                [['priority', '=', '1']],
            ],
            [
                'fields' => ['id', 'name', 'list_price', 'default_code'],
            ]
        );
    }

    // public function traerDatosFavoritos($usuarioId)
    // {
    //     try {
    //         $productos = $this->modelos->execute_kw(
    //             $this->base_datos,
    //             $this->uid,
    //             $this->contraseña,
    //             'product.product',
    //             'search_read',
    //             [
    //                 [['priority', '=', '1'], ['create_uid', '=', $usuarioId]],
    //             ],
    //             [
    //                 'fields' => ['id', 'default_code', 'name', 'product_tmpl_id', 'product_template_attribute_value_ids', 'create_uid'],
    //             ]
    //         );

    //         foreach ($productos as &$producto) {
    //             if (!empty($producto['product_template_attribute_value_ids'])) {
    //                 $attributeValues = $this->modelos->execute_kw(
    //                     $this->base_datos,
    //                     $this->uid,
    //                     $this->contraseña,
    //                     'product.template.attribute.value',
    //                     'search_read',
    //                     [
    //                         [['id', 'in', $producto['product_template_attribute_value_ids']]],
    //                     ],
    //                     ['fields' => ['id', 'name']]
    //                 );

    //                 $producto['attribute_values'] = array_map(function ($attributeValue) {
    //                     return $attributeValue['name'];
    //                 }, $attributeValues);
    //             } else {
    //                 $producto['attribute_values'] = "";
    //             }

    //             $xmlDato = $this->modelos->execute_kw(
    //                 $this->base_datos,
    //                 $this->uid,
    //                 $this->contraseña,
    //                 'ir.model.data',
    //                 'search_read',
    //                 [
    //                     [['model', '=', 'product.product'], ['res_id', '=', $producto['id']]],
    //                 ],
    //                 ['fields' => ['module', 'name']]
    //             );

    //             if (!empty($xmlDato)) {
    //                 $producto['xml_id'] = $xmlDato[0]['module'] . '.' . $xmlDato[0]['name'];
    //             } else {
    //                 $producto['xml_id'] = null;
    //             }
    //         }

    //         return $productos;
    //     } catch (Exception $e) {
    //         Log::error('Error traer referencias de los productos:', ['message' => $e->getMessage()]);
    //         return null;
    //     }
    // }

    public function traerDatosFavoritos($usuarioId)
    {
        try {
            $productos = $this->modelos->execute_kw(
                $this->base_datos,
                $this->uid,
                $this->contraseña,
                'product.product',
                'search_read',
                [
                    [['priority', '=', '1'], ['create_uid', '=', $usuarioId]],
                ],
                [
                    'fields' => ['id', 'default_code', 'name', 'product_tmpl_id', 'product_template_attribute_value_ids', 'create_uid'],
                ]
            );

            $attributeValueIds = array_merge(...array_column($productos, 'product_template_attribute_value_ids'));
            $attributeValuesMap = [];

            if (!empty($attributeValueIds)) {
                $attributeValues = $this->modelos->execute_kw(
                    $this->base_datos,
                    $this->uid,
                    $this->contraseña,
                    'product.template.attribute.value',
                    'search_read',
                    [
                        [['id', 'in', $attributeValueIds]],
                    ],
                    ['fields' => ['id', 'name']]
                );

                foreach ($attributeValues as $attribute) {
                    $attributeValuesMap[$attribute['id']] = $attribute['name'];
                }
            }

            $productIds = array_column($productos, 'id');
            $xmlDataMap = [];

            if (!empty($productIds)) {
                $xmlData = $this->modelos->execute_kw(
                    $this->base_datos,
                    $this->uid,
                    $this->contraseña,
                    'ir.model.data',
                    'search_read',
                    [
                        [['model', '=', 'product.product'], ['res_id', 'in', $productIds]],
                    ],
                    ['fields' => ['res_id', 'module', 'name']]
                );

                foreach ($xmlData as $xml) {
                    $xmlDataMap[$xml['res_id']] = $xml['module'] . '.' . $xml['name'];
                }
            }

            foreach ($productos as &$producto) {
                $producto['attribute_values'] = !empty($producto['product_template_attribute_value_ids'])
                    ? array_map(fn($id) => $attributeValuesMap[$id] ?? '', $producto['product_template_attribute_value_ids'])
                    : "";

                $producto['xml_id'] = $xmlDataMap[$producto['id']] ?? null;
            }

            return $productos;
        } catch (Exception $e) {
            Log::error('Error traer referencias de los productos:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function convertirFavoritosANoFavoritos($usuarioId)
    {
        try {
            $favoritos = $this->traerDatosFavoritos($usuarioId);

            if ($favoritos) {
                $productoIds = array_column($favoritos, 'id');

                $this->modelos->execute_kw(
                    $this->base_datos,
                    $this->uid,
                    $this->contraseña,
                    'product.product',
                    'write',
                    [
                        $productoIds,
                        ['priority' => '0'],
                    ]
                );

                Log::info('Productos favoritos actualizados a no favoritos');
                return $productoIds;
            }

            return [];
        } catch (Exception $e) {
            Log::error('Error al actualizar productos favoritos a no favoritos:', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
