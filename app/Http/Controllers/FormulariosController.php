<?php

namespace App\Http\Controllers;

use App\Services\OdooService;
use Illuminate\Support\Facades\Auth;
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

    public function index()
    {
        $user = Auth::user();

        return Inertia::render('Formula/Formula', [
            'productos' => $this->readProductsFromFile(),
            'userOdooId' => $user->odoo_uid,
        ]);
    }

    /********************LO QUE RESPECTA A TRAER DATOS DE ODOO 17********************/
    public function traerDatosFavoritos()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado en Laravel'], 401);
            }

            $userId = $this->odooService->authenticate();

            if (!$userId) {
                return response()->json(['error' => 'Usuario no autenticado en Odoo'], 401);
            }

            $dataFavorite = $this->odooService->getFavoriteData($userId);
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
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado en Laravel'], 401);
            }

            $odooUserId = $user->odoo_uid;

            if (!$odooUserId) {
                return response()->json(['error' => 'Usuario no autenticado en Odoo'], 401);
            }

            $result = $this->odooService->convertirFavoritosANoFavoritos($odooUserId);
            return response()->json(['message' => 'Productos actualizados a no favoritos', 'result' => $result]);
        } catch (\Exception $e) {
            Log::error('Error al convertir productos a no favoritos:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al convertir productos a no favoritos'], 500);
        }
    }
}
