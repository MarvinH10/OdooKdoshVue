<?php

namespace App\Http\Controllers;

use App\Services\OdooService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class FormulariosController extends Controller
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

    private function writeProductsToFile(array $productos)
    {
        file_put_contents($this->productsFile, json_encode($productos));
    }

    public function index()
    {
        return Inertia::render('Formula/Formula', [
            'productos' => $this->readProductsFromFile(),
        ]);
    }

    /********************LO QUE RESPECTA A TRAER DATOS DE ODOO 17********************/
    public function traerDatosFavoritos()
    {
        try {
            $dataFavorite = $this->odooService->getFavoriteData();
            return response()->json($dataFavorite);
        } catch (\Exception $e) {
            Log::error('Error fetching favorite data:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error fetching favorite data'], 500);
        }
    }

    /********************CONVERTIR PRODUCTOS FAVORITOS A NO FAVORITOS********************/
    public function convertirFavoritosANoFavoritos()
    {
        try {
            $result = $this->odooService->convertirFavoritosANoFavoritos();
            return response()->json(['message' => 'Productos actualizados a no favoritos', 'result' => $result]);
        } catch (\Exception $e) {
            Log::error('Error al convertir productos a no favoritos:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al convertir productos a no favoritos'], 500);
        }
    }
}
