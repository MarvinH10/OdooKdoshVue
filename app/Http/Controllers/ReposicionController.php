<?php

namespace App\Http\Controllers;

use App\Services\OdooService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ReposicionController extends Controller
{
    private $productsFile = 'productos.json';
    protected $odooService;

    public function __construct(OdooService $odooService)
    {
        $this->odooService = $odooService;
    }

    private function readProductsFromFile()
    {
        if (file_exists($this->productsFile)) {
            return json_decode(file_get_contents($this->productsFile), true);
        }
        return [];
    }

    public function index()
    {
        return Inertia::render('Reposicion/Reposicion', [
            'productos' => $this->readProductsFromFile(),
        ]);
    }

    /********************LO QUE RESPECTA A TRAER DATOS DE ODOO 17********************/
    public function traerDatosRepo()
    {
        try {
            $dataRepo = $this->odooService->getDataRepo();
            return response()->json($dataRepo);
        } catch (\Exception $e) {
            Log::error('Error fetching repo data:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error fetching repo data'], 500);
        }
    }
}
