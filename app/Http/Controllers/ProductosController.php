<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Services\OdooService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductosController extends Controller
{
    protected $odooService;

    public function __construct(OdooService $odooService)
    {
        $this->odooService = $odooService;
    }

    private function readProductsFromFile()
    {
        if (Storage::exists('productos.json')) {
            return json_decode(Storage::get('productos.json'), true);
        }
        return [];
    }

    private function writeProductsToFile(array $productos)
    {
        Storage::put('productos.json', json_encode($productos));
    }

    /********************LO QUE RESPECTA A ARREGLOS********************/
    public function index()
    {
        return Inertia::render('Productos/Productos', [
            'productos' => Producto::all(),
        ]);
    }

    public function traer()
    {
        return response()->json(Producto::all());
    }

    public function almacenar(Request $request)
    {
        try {
            $productos = $this->readProductsFromFile();

            $newProduct = $request->validate([
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

            $newProduct['id'] = !empty($productos) ? end($productos)['id'] + 1 : 1;

            if (!empty($newProduct['attributes'])) {
                $processedAttributes = [];
                $attributesWithRefs = [];

                foreach ($newProduct['attributes'] as $attribute) {
                    $hasRefs = false;
                    if (!empty($attribute['extra_references'])) {
                        foreach ($attribute['extra_references'] as $ref) {
                            if (!empty($ref)) {
                                $hasRefs = true;
                                break;
                            }
                        }
                    }

                    $processedAttribute = [
                        'attribute_id' => $attribute['attribute_id'],
                        'value_ids' => $attribute['value_ids'] ?? [],
                        'extra_references' => $attribute['extra_references'] ?? [],
                        'extra_prices' => $attribute['extra_prices'] ?? [],
                    ];

                    if ($hasRefs) {
                        $attributesWithRefs[] = $processedAttribute;
                    } else {
                        $processedAttributes[] = $processedAttribute;
                    }
                }

                $newProduct['attributes'] = array_merge($processedAttributes, $attributesWithRefs);
            }

            $productos[] = $newProduct;
            $this->writeProductsToFile($productos);

            return response()->json($newProduct);
        } catch (\Exception $e) {
            Log::error('Error storing product:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error storing product: ' . $e->getMessage()], 500);
        }
    }

    public function quitar($id)
    {
        try {
            $productos = $this->readProductsFromFile();
            $productos = array_filter($productos, function ($producto) use ($id) {
                return $producto['id'] != $id;
            });
            $this->writeProductsToFile(array_values($productos));
            return response()->json(['message' => 'Producto eliminado']);
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting product'], 500);
        }
    }

    public function actualizar(Request $request, $id)
    {
        try {
            $productos = $this->readProductsFromFile();

            $productoIndex = array_search($id, array_column($productos, 'id'));

            if ($productoIndex === false) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            $productos[$productoIndex] = array_merge($productos[$productoIndex], $request->all());

            $this->writeProductsToFile($productos);

            return response()->json($productos[$productoIndex]);
        } catch (\Exception $e) {
            Log::error('Error actualizando producto: ' . $e->getMessage());

            return response()->json(['error' => 'Error actualizando producto'], 500);
        }
    }

    /********************LO QUE RESPECTA A TRAER DATOS DE ODOO 17********************/
    public function traerProductosFavoritos()
    {
        $products_favorites = $this->odooService->getProductFavorites();
        return response()->json($products_favorites);
    }

    public function traerCategorias()
    {
        $categories = $this->odooService->getCategorias();
        return response()->json($categories);
    }

    public function traerSubcategorias($id)
    {
        $subcategories = $this->odooService->getSubcategories($id);
        return response()->json($subcategories);
    }

    public function traerAtributos()
    {
        $attributes = $this->odooService->getAttributos();
        return response()->json($attributes);
    }

    public function traerValoresAtributos($id)
    {
        $valueattributes = $this->odooService->getValueAttributos($id);
        return response()->json($valueattributes);
    }

    public function registrarProductos(Request $request)
    {
        try {
            $user = Auth::user();

            $odooUid = $user->odoo_uid;

            if (!$odooUid) {
                return response()->json(['error' => 'El usuario no tiene un UID de Odoo asociado'], 403);
            }

            $productos = $request->all();
            $productIds = [];
            $bulkProductData = [];

            foreach ($productos as $producto) {
                if (!empty($producto['attributes'])) {
                    $producto['attributes'] = $this->ordenarAtributos($producto['attributes']);
                }

                $productData = [
                    'is_favorite' => true,
                    'available_in_pos' => true,
                    'name' => $producto['name'],
                    'categ_id' => $producto['categ_id'],
                    'default_code' => $producto['default_code'],
                    'list_price' => $producto['list_price'],
                    'type' => 'consu',
                    'is_storable' => true,
                    'create_uid' => $odooUid,
                    'taxes_id' => [(int) 5],
                ];

                foreach (['subcateg1_id', 'subcateg2_id', 'subcateg3_id', 'subcateg4_id'] as $subcateg) {
                    if (!empty($producto[$subcateg])) {
                        $productData['categ_id'] = $producto[$subcateg];
                    }
                }

                $bulkProductData[] = $productData;
            }

            $createdProductIds = $this->odooService->createProductsBatch($bulkProductData);

            foreach ($productos as $index => $producto) {
                if (!empty($producto['attributes'])) {
                    $this->odooService->createVariant($createdProductIds[$index], $producto['attributes']);
                }
            }

            return response()->json(['message' => 'Productos registrados con éxito', 'product_ids' => $createdProductIds]);
        } catch (\Exception $e) {
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
}
