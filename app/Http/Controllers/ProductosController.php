<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\ServicioOdoo;
use Illuminate\Http\Request;
use App\Models\Producto;
use Inertia\Inertia;
use Exception;

class ProductosController extends Controller
{
    protected $servicioOdoo;

    public function __construct(ServicioOdoo $servicioOdoo)
    {
        $this->servicioOdoo = $servicioOdoo;
    }

    private function leerProductosDeArchivo()
    {
        if (Storage::exists('productos.json')) {
            return json_decode(Storage::get('productos.json'), true);
        }
        return [];
    }

    private function escribirProductosAlArchivo(array $productos)
    {
        Storage::put('productos.json', json_encode($productos));
    }

    /*INICIO FUNCIONALIDADES EN VISTA*/
    public function index()
    {
        return Inertia::render('Productos/Productos');
    }

    public function traer()
    {
        return response()->json(Producto::all());
    }

    public function quitar($id)
    {
        try {
            $productos = $this->leerProductosDeArchivo();
            $productos = array_filter($productos, function ($producto) use ($id) {
                return $producto['id'] != $id;
            });
            $this->escribirProductosAlArchivo(array_values($productos));
            return response()->json(['message' => 'Producto eliminado']);
        } catch (Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting product'], 500);
        }
    }

    public function actualizar(Request $request, $id)
    {
        try {
            $productos = $this->leerProductosDeArchivo();

            $productoIndex = array_search($id, array_column($productos, 'id'));

            if ($productoIndex === false) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            $productos[$productoIndex] = array_merge($productos[$productoIndex], $request->all());

            $this->escribirProductosAlArchivo($productos);

            return response()->json($productos[$productoIndex]);
        } catch (Exception $e) {
            Log::error('Error actualizando producto: ' . $e->getMessage());

            return response()->json(['error' => 'Error actualizando producto'], 500);
        }
    }
    /*FINAL FUNCIONALIDADES EN VISTA*/

    /*INICIO FUNCIONALIDADES EN ODOO API*/
    public function traerProductosFavoritos()
    {
        $this->servicioOdoo->authenticate();

        $productosFavoritos = $this->servicioOdoo->traerProductosFavoritos();
        return response()->json($productosFavoritos);
    }

    public function traerCategoriasPDV()
    {
        $this->servicioOdoo->authenticate();

        $categorias = $this->servicioOdoo->traerCategoriasPDV();
        return response()->json($categorias);
    }

    public function traerCategorias()
    {
        $this->servicioOdoo->authenticate();

        $categorias = $this->servicioOdoo->traerCategorias();
        return response()->json($categorias);
    }

    public function traerSubcategorias($id)
    {
        $this->servicioOdoo->authenticate();

        $subCategorias = $this->servicioOdoo->traerSubcategorias($id);
        return response()->json($subCategorias);
    }

    public function traerAtributos()
    {
        $this->servicioOdoo->authenticate();

        $atributos = $this->servicioOdoo->traerAtributos();
        return response()->json($atributos);
    }

    public function traerValoresAtributos($id)
    {
        $this->servicioOdoo->authenticate();

        $valoresAtributos = $this->servicioOdoo->traerValoresAtributos($id);
        return response()->json($valoresAtributos);
    }

    public function registrarProductos(Request $request)
    {
        try {
            $this->servicioOdoo->authenticate();

            $usuario = Auth::user();

            $odooUid = $usuario->odoo_uid;

            if (!$odooUid) {
                return response()->json(['error' => 'El usuario no tiene un UID de Odoo asociado'], 403);
            }

            $productos = $request->all();
            $granearDatosProducto = [];

            foreach ($productos as $producto) {
                if (!empty($producto['attributes'])) {
                    $producto['attributes'] = $this->ordenarAtributos($producto['attributes']);
                }

                $datosProducto = [
                    'priority' =>  '1',
                    'sale_ok' => true,
                    'available_in_pos' => true,
                    'name' => $producto['name'],
                    'pos_categ_ids' => [(int) $producto['categoryPDV']],
                    'categ_id' => $producto['category'],
                    'default_code' => $producto['code'],
                    'list_price' => $producto['price'],
                    'type' => 'product',
                    'create_uid' => $odooUid,
                    'taxes_id' => [(int) 6],
                ];

                if (!empty($producto['subcategory4'])) {
                    $datosProducto['categ_id'] = (int)$producto['subcategory4'];
                } elseif (!empty($producto['subcategory3'])) {
                    $datosProducto['categ_id'] = (int)$producto['subcategory3'];
                } elseif (!empty($producto['subcategory2'])) {
                    $datosProducto['categ_id'] = (int)$producto['subcategory2'];
                } elseif (!empty($producto['subcategory1'])) {
                    $datosProducto['categ_id'] = (int)$producto['subcategory1'];
                } elseif (!empty($producto['category'])) {
                    $datosProducto['categ_id'] = (int)$producto['category'];
                } else {
                    throw new Exception('No se proporcionó ninguna categoría válida.');
                }

                $granearDatosProducto[] = $datosProducto;
            }

            $crearIdsProducto = $this->servicioOdoo->createProductsBatch($granearDatosProducto);

            foreach ($productos as $index => $producto) {
                if (!empty($producto['attributes'])) {
                    $this->servicioOdoo->createVariant($crearIdsProducto[$index], $producto['attributes']);
                }
            }

            return response()->json(['message' => 'Productos registrados con éxito', 'product_ids' => $crearIdsProducto]);
        } catch (Exception $e) {
            Log::error('Error registrando productos:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error registrando productos: ' . $e->getMessage()], 500);
        }
    }

    private function ordenarAtributos(array $attributes)
    {
        $conReferencias = [];
        $sinReferencias = [];

        foreach ($attributes as $attribute) {
            $tieneReferencias = !empty(array_filter($attribute['referencesInternal'] ?? [], function ($ref) {
                return trim($ref) !== '';
            }));

            if ($tieneReferencias) {
                $conReferencias[] = $attribute;
            } else {
                $sinReferencias[] = $attribute;
            }
        }

        return array_merge($sinReferencias, $conReferencias);
    }
    /*FINAL FUNCIONALIDADES EN ODOO API*/
}
