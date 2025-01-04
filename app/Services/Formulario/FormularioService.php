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

    public function traerDatosFasvoritos($usuarioId)
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

            foreach ($productos as &$producto) {
                if (!empty($producto['product_template_attribute_value_ids'])) {
                    $attributeValues = $this->modelos->execute_kw(
                        $this->base_datos,
                        $this->uid,
                        $this->contraseña,
                        'product.template.attribute.value',
                        'search_read',
                        [
                            [['id', 'in', $producto['product_template_attribute_value_ids']]],
                        ],
                        ['fields' => ['id', 'name']]
                    );

                    $producto['attribute_values'] = array_map(function ($attributeValue) {
                        return $attributeValue['name'];
                    }, $attributeValues);
                } else {
                    $producto['attribute_values'] = "";
                }
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
            $favoritos = $this->traerDatosFasvoritos($usuarioId);

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
