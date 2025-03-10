<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ServicioOdoo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BarcodeController extends Controller
{
    protected $servicioOdoo;

    public function __construct(ServicioOdoo $servicioOdoo)
    {
        $this->servicioOdoo = $servicioOdoo;
    }

    public function index()
    {
        return Inertia::render('Barcode/Barcode');
    }

    public function traerProductosById($productId)
    {
        $this->servicioOdoo->authenticate();

        $products_Id = $this->servicioOdoo->traerProductosById($productId);
        return response()->json($products_Id);
    }

    public function traerProductosByIdOrdenCompra($order_id)
    {
        $this->servicioOdoo->authenticate();

        $orders_Id = $this->servicioOdoo->traerProductosByIdOrdenCompra($order_id);
        return response()->json($orders_Id);
    }
}
