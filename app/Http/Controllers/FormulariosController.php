<?php

namespace App\Http\Controllers;

use App\Services\ServicioOdoo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Exception;

class FormulariosController extends Controller
{
    private $productsFile = 'productos.json';
    protected $servicioOdoo;

    public function __construct(ServicioOdoo $servicioOdoo)
    {
        $this->servicioOdoo = $servicioOdoo;
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
            $this->servicioOdoo->authenticate();

            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado en Laravel'], 401);
            }

            $usuarioId = $this->servicioOdoo->authenticate();

            if (!$usuarioId) {
                return response()->json(['error' => 'Usuario no autenticado en Odoo'], 401);
            }

            $dataFavorite = $this->servicioOdoo->traerDatosFasvoritos($usuarioId);
            return response()->json($dataFavorite);
        } catch (Exception $e) {
            Log::error('Error fetching favorite data:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error fetching favorite data'], 500);
        }
    }

    /********************CONVERTIR PRODUCTOS FAVORITOS A NO FAVORITOS********************/
    public function convertirFavoritosANoFavoritos()
    {
        try {
            $this->servicioOdoo->authenticate();

            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado en Laravel'], 401);
            }

            $odooUsuarioId = $user->odoo_uid;

            if (!$odooUsuarioId) {
                return response()->json(['error' => 'Usuario no autenticado en Odoo'], 401);
            }

            $result = $this->servicioOdoo->convertirFavoritosANoFavoritos($odooUsuarioId);
            return response()->json(['message' => 'Productos actualizados a no favoritos', 'result' => $result]);
        } catch (Exception $e) {
            Log::error('Error al convertir productos a no favoritos:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al convertir productos a no favoritos'], 500);
        }
    }
}
