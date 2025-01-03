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

    public function almacenar(Request $request)
    {
        try {
            $productos = $this->leerProductosDeArchivo();

            $nuevoProducto = $request->validate([
                'name' => 'required|string|max:255',
                'default_code' => 'nullable|string|max:255',
                'categ_id' => 'required|integer',
                'subcateg1_id' => 'nullable|integer',
                'subcateg2_id' => 'nullable|integer',
                'subcateg3_id' => 'nullable|integer',
                'subcateg4_id' => 'nullable|integer',
                'list_price' => 'nullable|numeric',
                'attributes' => 'nullable|array',
                'attributes.*.attribute_id' => 'required|integer',
                'attributes.*.value_ids' => 'required|array',
                'attributes.*.value_ids.*' => 'required|integer',
                'attributes.*.extra_references' => 'nullable|array',
                'attributes.*.extra_references.*' => 'nullable|string',
                'attributes.*.extra_prices' => 'nullable|array',
                'attributes.*.extra_prices.*' => 'nullable|numeric',
            ]);

            $nuevoProducto['id'] = !empty($productos) ? end($productos)['id'] + 1 : 1;

            if (!empty($nuevoProducto['attributes'])) {
                $AtributosProcesados = [];
                $AtributosReferenciados = [];

                foreach ($nuevoProducto['attributes'] as $atributo) {
                    $tieneReferencias = false;
                    if (!empty($atributo['extra_references'])) {
                        foreach ($atributo['extra_references'] as $ref) {
                            if (!empty($ref)) {
                                $tieneReferencias = true;
                                break;
                            }
                        }
                    }

                    $AtributoProcesado = [
                        'attribute_id' => $atributo['attribute_id'],
                        'value_ids' => $atributo['value_ids'] ?? [],
                        'extra_references' => $atributo['extra_references'] ?? [],
                        'extra_prices' => $atributo['extra_prices'] ?? [],
                    ];

                    if ($tieneReferencias) {
                        $AtributosReferenciados[] = $AtributoProcesado;
                    } else {
                        $AtributosProcesados[] = $AtributoProcesado;
                    }
                }

                $nuevoProducto['attributes'] = array_merge($AtributosProcesados, $AtributosReferenciados);
            }

            $productos[] = $nuevoProducto;
            $this->escribirProductosAlArchivo($productos);

            return response()->json($nuevoProducto);
        } catch (Exception $e) {
            Log::error('Error almacenando producto:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error almacenando producto: ' . $e->getMessage()], 500);
        }
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
                    'is_favorite' => true,
                    'available_in_pos' => true,
                    'name' => $producto['name'],
                    'categ_id' => $producto['categ_id'],
                    'default_code' => $producto['default_code'],
                    'list_price' => $producto['list_price'],
                    'type' => 'product',
                    'is_storable' => true,
                    'create_uid' => $odooUid,
                    'taxes_id' => [(int) 5],
                ];

                foreach (['subcateg1_id', 'subcateg2_id', 'subcateg3_id', 'subcateg4_id'] as $subcateg) {
                    if (!empty($producto[$subcateg])) {
                        $datosProducto['categ_id'] = $producto[$subcateg];
                    }
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
            $tieneReferencias = !empty(array_filter($attribute['extra_references'] ?? [], function ($ref) {
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
