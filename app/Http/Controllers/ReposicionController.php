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
    public function traerDatosReposicion()
    {
        try {
            $this->servicioOdoo->authenticate();

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado en Laravel'], 401);
            }

            $dataRepo = $this->servicioOdoo->traerDatosReposicion();

            return response()->json($dataRepo);
        } catch (Exception $e) {
            Log::error('Error al obtener los datos del repositorio:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener los datos del repositorio'], 500);
        }
    }

    /********************ACA ALMACENAREMOS LOS DATOS TRAIDOS POR LA FUNCION ARRIBA (AUN PENSANDO)********************/
    public function almacenarDatosReposicion()
    {
        try {
            $this->servicioOdoo->authenticate();
            $dataRepo = $this->servicioOdoo->traerDatosReposicion();
            file_put_contents($this->productsFile, json_encode($dataRepo));
            return response()->json(['message' => 'Datos almacenados correctamente'], 200);
        } catch (Exception $e) {
            Log::error('Error al almacenar los datos del repositorio:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al almacenar los datos del repositorio'], 500);
        }
    }
}
