<script>
export default {
    name: "ProductosTabla",
    props: {
        productos: {
            type: Array,
            required: true,
        },
        loading: {
            type: Boolean,
            required: true,
        },
        categoriesMap: {
            type: Object,
            required: true,
        },
        subcategoriesMap: {
            type: Object,
            required: true,
        },
        allAttributes: {
            type: Array,
            required: true,
        },
        attributeInputs: {
            type: Array,
            required: true,
        },
    },
    emits: ["duplicar", "editar", "eliminar"],
    mounted() {
        console.log("Productos recibidos:", this.productos);
    },
    methods: {
        duplicarProducto(producto) {
            this.$emit("duplicar", producto);
        },
        editarProducto(producto) {
            this.$emit("editar", producto);
        },
        eliminarProducto(id) {
            this.$emit("eliminar", id);
        },
        getCategoryNames(producto) {
            const names = [];
            if (producto.categ_id) {
                names.push(this.categoriesMap[producto.categ_id] || "Categoría desconocida");
            }
            if (producto.subcateg1_id) {
                names.push(this.subcategoriesMap[producto.subcateg1_id] || "Subcategoría 1 desconocida");
            }
            if (producto.subcateg2_id) {
                names.push(this.subcategoriesMap[producto.subcateg2_id] || "Subcategoría 2 desconocida");
            }
            if (producto.subcateg3_id) {
                names.push(this.subcategoriesMap[producto.subcateg3_id] || "Subcategoría 3 desconocida");
            }
            if (producto.subcateg4_id) {
                names.push(this.subcategoriesMap[producto.subcateg4_id] || "Subcategoría 4 desconocida");
            }
            return names.filter(Boolean).join(" / ");
        },
        getAttributeName(attributeId) {
            const attribute = this.allAttributes.find((attr) => attr.id === attributeId);
            return attribute ? attribute.name : "Atributo Desconocido";
        },
        getValueAttributeName(valueId) {
            for (const input of this.attributeInputs) {
                const value = input.values.find((val) => val.id === parseInt(valueId));
                if (value) {
                    return value.name;
                }
            }

            for (const attribute of this.allAttributes) {
                if (attribute.values) {
                    const value = attribute.values.find(
                        (val) => val.id === parseInt(valueId)
                    );
                    if (value) {
                        return value.name;
                    }
                }
            }

            return "Valor Desconocido";
        },
    },
};
</script>

<template>
    <div v-if="loading" class="text-center my-4">
        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando productos...
    </div>
    <div v-else class="overflow-x-auto">
        <table v-if="productos.length" class="min-w-full mt-4 table-auto">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-gray-800 tracking-wider">
                        ID
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-gray-800 tracking-wider">
                        Nombre
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-gray-800 tracking-wider">
                        Código
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-gray-800 tracking-wider">
                        Categorías
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-gray-800 tracking-wider">
                        Precio
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-gray-800 tracking-wider">
                        Atributos
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-gray-800 tracking-wider">
                        Valores de Atributos
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-gray-800 tracking-wider">
                        Referencia Interna
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-gray-800 tracking-wider">
                        Precio Extra
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-gray-800 tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(producto, index) in productos" :key="producto.id">
                    <td class="px-6 py-4 border-b border-gray-300">
                        {{ index + 1 }}
                    </td>
                    <td class="px-6 py-4 border-b border-gray-300">
                        {{ producto.name }}
                    </td>
                    <td class="px-6 py-4 border-b border-gray-300">
                        {{ producto.default_code }}
                    </td>
                    <td class="px-6 py-4 border-b border-gray-300">
                        {{ getCategoryNames(producto, categoriesMap, subcategoriesMap) }}
                    </td>
                    <td class="px-6 py-4 border-b border-gray-300">
                        S/. {{ producto.list_price }}
                    </td>
                    <td class="px-6 py-4 border-b border-gray-300">
                        <span v-for="attr in producto.attributes" :key="attr.attribute_id" class="tag">{{
                            getAttributeName(attr.attribute_id) }}</span>
                    </td>
                    <td class="px-6 py-4 border-b border-gray-300">
                        <span v-for="attr in producto.attributes" :key="attr.attribute_id">
                            <span v-for="(name, val) in attr.value_names" :key="val" class="tag">{{ name
                                }}</span>
                        </span>
                    </td>
                    <td class="px-6 py-4 border-b border-gray-300">
                        <span v-for="attr in producto.attributes" :key="attr.attribute_id">
                            <span v-for="(ref, index) in attr.extra_references" :key="index" class="tag">{{ ref
                                }}</span>
                        </span>
                    </td>
                    <td class="px-6 py-4 border-b border-gray-300">
                        <span v-for="attr in producto.attributes" :key="attr.attribute_id">
                            <span v-for="(price, index) in attr.extra_prices" :key="index" class="tag">{{ price !== null
                                ? "S/. " + price : "S/. 0" }}</span>
                        </span>
                    </td>
                    <td class="px-6 py-4 border-b border-gray-300">
                        <button @click="duplicarProducto(producto)"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button @click="editarProducto(producto)"
                            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 ml-1 rounded">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="eliminarProducto(producto.id)"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 ml-1 rounded">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <p v-if="!loading && !productos.length" class="mt-4 text-center text-gray-500">
        No hay productos por mostrar
    </p>
</template>
