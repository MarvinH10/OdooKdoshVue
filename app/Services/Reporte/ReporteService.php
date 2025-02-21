<?php

namespace App\Services\Reporte;

use Exception;
use Illuminate\Support\Facades\Log;

class ReporteService
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

    public function traerDatosOrdenCompra($parametro)
    {
        try {
            $parametro = (int) $parametro;

            $purchase_order_info = $this->modelos->execute_kw(
                $this->base_datos,
                $this->uid,
                $this->contraseña,
                'purchase.order',
                'search_read',
                [
                    [['id', '=', $parametro]]
                ],
                [
                    'fields' => ['name', 'date_approve', 'partner_id', 'partner_ref', 'order_line'],
                    'limit' => 1
                ]
            );

            if (empty($purchase_order_info)) {
                return null;
            }

            $order = $purchase_order_info[0];
            $order_name = $order['name'] ?? '';
            $order_date = $order['date_approve'] ?? '';
            $partner_name = $order['partner_id'][1] ?? '';
            $partner_ref = $order['partner_ref'] ?? '';
            $order_line_ids = $order['order_line'] ?? [];

            if (empty($order_line_ids)) {
                return [
                    'order_id' => $order_name,
                    'date_approve' => $order_date,
                    'supplier' => $partner_name,
                    'partner_ref' => $partner_ref,
                    'products' => []
                ];
            }

            $purchase_order_lines = $this->modelos->execute_kw(
                $this->base_datos,
                $this->uid,
                $this->contraseña,
                'purchase.order.line',
                'search_read',
                [
                    [['id', 'in', $order_line_ids]]
                ],
                ['fields' => ['id', 'name', 'product_qty']]
            );

            $products = array_map(function ($item) {
                return [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'product_qty' => $item['product_qty'],
                    'brand' => $this->extractBrand($item['name'])
                ];
            }, $purchase_order_lines);

            return [
                'order_id' => $parametro,
                'order_name' => $order_name,
                'date_approve' => $order_date,
                'supplier' => $partner_name,
                'partner_ref' => $partner_ref,
                'products' => $products
            ];
        } catch (Exception $e) {
            Log::error('Error al traer los datos del reporte:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private function extractBrand($name)
    {
        if (preg_match('/-\s+([^()]+)\s*\(/i', $name, $matches)) {
            return trim($matches[1]);
        }

        return "";
    }
}
