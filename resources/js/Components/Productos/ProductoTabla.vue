<script lang="ts">
import { defineComponent, PropType, computed } from "vue";

export default defineComponent({
    name: "ProductoTabla",
    props: {
        productos: {
            type: Array as PropType<Array<{
                id: number; name: string; code: string; price: number; category: string;
                attributes: Array<{
                    attributeId: string;
                    attributeValues: Array<{ id: string; name: string }>;
                    referenceGlobal: string;
                    referencesInternal: Record<string, string>;
                    extraPrice: number;
                }>;
            }>>,
            required: true,
        },
        categorias: {
            type: Array as PropType<Array<{ id: string; name: string }>>,
            required: true,
        },
        subcategoriasMap: {
            type: Object as PropType<{ [key: number]: string }>,
            required: true,
        },
        atributos: {
            type: Array as PropType<Array<{ id: number; name: string }>>,
            required: true,
        },
        isUploading: {
            type: Boolean,
            required: true,
        },
        idProductoRegistrado: {
            type: Array as PropType<Array<{ id: number; }>>,
            required: true,
        },
        baseUrl: {
            type: String,
            required: true,
        },
        showLinksModal: {
            type: Boolean,
            required: true,
        }
    },
    emits: ["agregar", "registrar", "duplicar", "eliminar", "editar", "cerrarLinkModal"],
    setup(props, { emit }) {
        // console.log("Productos recibidos:", props.productos);
        const tieneProductos = computed(() => {
            return props.productos.length > 0;
        });

        const getCategoryNameWithSubcategories = (producto: any): string => {
            // console.log("Producto recibido:", producto);

            const categoriaPrincipal = props.categorias.find(
                (cat) => String(cat.id) === String(producto.category)
            );
            // console.log("Categoría principal encontrada:", categoriaPrincipal);

            const subcategoria1 = props.subcategoriasMap[producto.subcategory1] || "";
            const subcategoria2 = props.subcategoriasMap[producto.subcategory2] || "";
            const subcategoria3 = props.subcategoriasMap[producto.subcategory3] || "";
            const subcategoria4 = props.subcategoriasMap[producto.subcategory4] || "";

            // console.log("Subcategorías encontradas:");
            // console.log("Subcategoría 1:", subcategoria1);
            // console.log("Subcategoría 2:", subcategoria2);
            // console.log("Subcategoría 3:", subcategoria3);
            // console.log("Subcategoría 4:", subcategoria4);

            const categoriasConcatenadas = [
                categoriaPrincipal ? categoriaPrincipal.name : "Sin categoría",
                subcategoria1,
                subcategoria2,
                subcategoria3,
                subcategoria4,
            ]
                .filter((nombre) => nombre)
                .join(" / ");

            // console.log("Categorías concatenadas:", categoriasConcatenadas);

            return categoriasConcatenadas;
        };

        const handleAgregar = () => {
            emit("agregar", null);
        };

        const handleRegistrar = () => {
            emit("registrar");
        };

        const handleDuplicar = (producto: any) => {
            emit("duplicar", producto);
        };

        const handleEditar = (producto: any) => {
            emit("editar", producto);
        };

        const handleEliminar = (producto: any) => {
            emit("eliminar", producto.id);
        };

        const handleCerrarLinkModal = () => {
            emit("cerrarLinkModal");
        };

        return {
            tieneProductos,
            getCategoryNameWithSubcategories,
            handleAgregar,
            handleRegistrar,
            handleDuplicar,
            handleEliminar,
            handleEditar,
            handleCerrarLinkModal,
        };
    },
});
</script>

<template>
    <div>
        <div class="flex justify-between items-center mb-4">
            <button @click="handleAgregar"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-circle-plus"></i> Agregar
            </button>

            <div class="flex justify-end mt-4">
                <button v-if="!isUploading" @click="handleRegistrar"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-save"></i> Crear Productos en Odoo
                </button>

                <div v-else class="text-center text-blue-500 font-bold">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Subiendo productos...
                </div>
            </div>
        </div>

        <div class="overflow-y-auto max-h-[36rem] rounded">
            <table v-if="tieneProductos" class="min-w-full table-auto border-collapse">
                <thead class="bg-black sticky top-[-1px] text-white z-10">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Nombre</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Código</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Categorías</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Precio</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Atributos</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Valores de Atributos</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Referencia Interna</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Precio Extra</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="producto in productos" :key="producto.id" class="hover:bg-gray-100">
                        <td class="border border-gray-300 px-4 py-2">{{ producto.id }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ producto.name }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ producto.code }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ getCategoryNameWithSubcategories(producto) }}
                        </td>
                        <td class="border border-gray-300 px-4 py-2">{{ producto.price }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <div v-for="attribute in producto.attributes" :key="attribute.attributeId">
                                <span
                                    class="bg-gray-200 text-gray-700 px-2 py-1 rounded-full text-sm inline-block mb-2">
                                    {{
                                        atributos.find((atributo) => atributo.id === parseInt(attribute.attributeId))?.name
                                        || ""
                                    }}
                                </span>
                            </div>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">
                            <div v-for="attribute in producto.attributes" :key="attribute.attributeId">
                                <div>
                                    <span v-for="value in attribute.attributeValues" :key="value.id"
                                        class="bg-green-200 text-green-700 px-2 py-1 rounded-full text-sm inline-block mr-1 mb-2">
                                        {{ value.name || "Sin Nombre" }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">
                            <div v-for="attribute in producto.attributes" :key="attribute.attributeId">
                                <div>
                                    <span v-for="[key, value] in Object.entries(attribute.referencesInternal)
                                        .filter(([key]) => !key.endsWith('_extraPrice'))"
                                        :key="`${attribute.attributeId}-${key}`"
                                        class="bg-blue-200 text-blue-700 px-2 py-1 rounded-full text-sm inline-block mr-1 mb-2">
                                        {{ value || "Sin Ref. Int." }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">
                            <div v-for="attribute in producto.attributes" :key="attribute.attributeId">
                                <div>
                                    <span v-for="value in attribute.attributeValues" :key="value.id"
                                        class="bg-red-200 text-red-700 px-2 py-1 rounded-full text-sm inline-block mr-1 mb-2">
                                        {{ attribute.referencesInternal[value.id + '_extraPrice'] || "0" }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">
                            <button @click="handleDuplicar(producto)"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 ml-1 rounded">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button @click="handleEditar(producto)"
                                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 ml-1 rounded">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button @click="handleEliminar(producto)"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 ml-1 rounded">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="text-center text-gray-500 py-8">
                No hay productos por mostrar
            </div>
        </div>
    </div>
    <div v-if="showLinksModal" class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full max-w-6xl">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Productos Registrados
                            </h3>
                            <div class="mt-2">
                                <p>
                                    A continuación se muestran los enlaces a los productos
                                    registrados en Odoo:
                                </p>
                                <ul class="mt-4">
                                    <li v-for="producto in idProductoRegistrado" :key="producto.id">
                                        <a :href="`${baseUrl}web?debug=1#id=${producto}&cids=1&menu_id=206&action=354&model=product.template&view_type=form`"
                                            target="_blank">
                                            Ver Producto {{ producto }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button @click="handleCerrarLinkModal"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-2">
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
