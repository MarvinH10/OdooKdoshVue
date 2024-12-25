<?php

namespace App\Services;

use App\Services\Producto\ProductoService;
use Illuminate\Support\Facades\Auth;
use Ripcord\Ripcord;
use Exception;

class ServicioOdoo
{
    protected $url;
    protected $base_datos;
    protected $usuario;
    protected $contraseña;
    protected $uid;
    protected $modelos;
    public $productoService;

    public function __construct()
    {
        $this->url = env('VITE_APP_ODOO_URL');
        $this->base_datos = env('VITE_APP_ODOO_BASE_DATOS');
        $this->modelos = Ripcord::client("$this->url/xmlrpc/2/object");
    }

    public function authenticate()
    {
        $autenticado = Auth::user();

        if (!$autenticado) {
            throw new Exception('Usuario no autenticado');
        }

        $common_cliente = Ripcord::client("$this->url/xmlrpc/2/common");
        $this->usuario = $autenticado->email;
        $this->contraseña = $autenticado->token;
        $this->uid = $common_cliente->authenticate($this->base_datos, $this->usuario, $this->contraseña, array());

        if (!$this->uid) {
            throw new Exception('Error de autenticación con Odoo');
        }

        $this->productoService = new ProductoService($this->modelos, $this->base_datos, $this->uid, $this->contraseña);

        return $this->uid;
    }

    /*INICIO PRODUCTO SERVICIO*/
    public function traerProductosFavoritos()
    {
        return $this->productoService->traerProductosFavoritos();
    }

    public function traerCategorias()
    {
        return $this->productoService->traerCategorias();
    }

    public function traerSubcategorias($id)
    {
        return $this->productoService->traerSubcategorias($id);
    }

    public function traerAtributos()
    {
        return $this->productoService->traerAtributos();
    }

    public function traerValoresAtributos($id)
    {
        return $this->productoService->traerValoresAtributos($id);
    }

    public function createProductsBatch($bulkProductData)
    {
        return $this->productoService->createProductsBatch($bulkProductData);
    }

    public function createVariant($productId, $attributes)
    {
        return $this->productoService->createVariant($productId, $attributes);
    }
    /*FIN PRODUCTO SERVICIO*/
}
