<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use App\Models\Digitador;
use App\Services\ServicioOdoo;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ReporteController extends Controller
{
    private $productsFile = 'productos.json';
    protected $servicioOdoo;

    public function __construct(ServicioOdoo $servicioOdoo)
    {
        $this->servicioOdoo = $servicioOdoo;
    }

    public function index()
    {
        $destinos = Destino::all();
        $digitadores = Digitador::all();

        return Inertia::render('Reporte/Reporte', [
            'destinos' => $destinos,
            'digitadores' => $digitadores
        ]);
    }

    /********************LO QUE RESPECTA A TRAER DATOS DE ODOO 17********************/
    public function traerDatosOrdenCompra($parametro)
    {
        try {
            $this->servicioOdoo->authenticate();

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado en Laravel'], 401);
            }

            $dataReporte = $this->servicioOdoo->traerDatosOrdenCompra($parametro);

            return response()->json($dataReporte);
        } catch (Exception $e) {
            Log::error('Error al obtener los datos del repositorio:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener los datos del repositorio'], 500);
        }
    }
}
