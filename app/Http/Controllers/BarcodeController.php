<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OdooService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BarcodeController extends Controller
{
    protected $odooService;

    public function __construct(OdooService $odooService)
    {
        $this->odooService = $odooService;
    }
    
    public function index()
    {
        return Inertia::render('Barcode/Barcode');
    }

    public function traerProductosxId($productId)
    {
        $products_Id = $this->odooService->getProductById($productId);
        return response()->json($products_Id);
    }
}
