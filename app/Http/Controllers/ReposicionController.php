<?php

namespace App\Http\Controllers;

use App\Services\ServicioOdoo;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Exception;
use Illuminate\Support\Facades\Auth;

class ReposicionController extends Controller
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
        return Inertia::render('Reposicion/Reposicion', [
            'productos' => $this->readProductsFromFile(),
        ]);
    }

    /********************LO QUE RESPECTA A TRAER DATOS DE ODOO 17********************/
    public function traerDatosReposicion($default_code)
    {
        try {
            $this->servicioOdoo->authenticate();

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado en Laravel'], 401);
            }

            $dataRepo = $this->servicioOdoo->traerDatosReposicion($default_code);

            return response()->json($dataRepo);
        } catch (Exception $e) {
            Log::error('Error al obtener los datos del repositorio:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener los datos del repositorio'], 500);
        }
    }
}
