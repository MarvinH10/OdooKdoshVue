<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

const productosRepo = ref([]);
const productosConcatenados = ref([]);
const productosSeleccionados = ref([]);
const searchQuery = ref("");
const searchQueryName = ref("");
const isLoading = ref(false);
const pageSize = ref(20);
const currentPage = ref(1);
const default_code = ref("");

const fetchProductosRepo = async (query, type) => {
    if (!query) {
        return;
    }

    const storedProductos = localStorage.getItem(`productosRepo_${name}`);
    if (storedProductos) {
        productosRepo.value = JSON.parse(storedProductos);
        productosConcatenados.value = productosRepo.value.map((producto) => {
            const referencia = producto.default_code ? `[${producto.default_code}]` : "";
            const nombreProducto = producto.name;

            let valoresAtributos = "";
            if (producto.attribute_values && producto.attribute_values.length > 0) {
                valoresAtributos = producto.attribute_values.join(", ");
            }

            const nombreConcatenado = valoresAtributos
                ? `${referencia} ${nombreProducto} (${valoresAtributos})`
                : `${referencia} ${nombreProducto}`;

            return {
                nombreConcatenado: nombreConcatenado,
                xml_id: producto.xml_id || "Sin XML ID"
            };
        });
        return;
    }

    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 120000);

    try {
        isLoading.value = true;
        const url = type === "default_code"
            ? `/reposicion/data_repo/traer/referencia/${query}`
            : `/reposicion/data_repo/traer/nombre/${query}`;

        const response = await axios.get(url, { signal: controller.signal });
        productosRepo.value = response.data;

        if (productosRepo.value.length === 0) {
            toast.error("No se encontraron productos con esa referencia.", {
                autoClose: 3000,
                position: "bottom-right",
                style: { width: "400px" },
                className: "border-l-4 border-red-500 p-4",
            });
            return;
        }

        productosConcatenados.value = productosRepo.value.map((producto) => {
            const referencia = producto.default_code ? `[${producto.default_code}]` : "";
            const nombreProducto = producto.name;

            let valoresAtributos = "";
            if (producto.attribute_values && producto.attribute_values.length > 0) {
                valoresAtributos = producto.attribute_values.join(", ");
            }

            const nombreConcatenado = valoresAtributos
                ? `${referencia} ${nombreProducto} (${valoresAtributos})`
                : `${referencia} ${nombreProducto}`;

            return {
                nombreConcatenado: nombreConcatenado,
                xml_id: producto.xml_id || "Sin XML ID"
            };
        });

        localStorage.setItem(`productosRepo_${default_code}`, JSON.stringify(productosRepo.value));
    } catch (error) {
        if (axios.isCancel(error)) {
            toast.warning("La búsqueda tardó demasiado, escribe una referencia más específica.", {
                autoClose: 4000,
                position: "bottom-right",
                style: { width: "400px" },
                className: "border-l-4 border-yellow-500 p-4",
            });
        } else {
            toast.warning("No hay ese producto en reposición", {
                autoClose: 3000,
                position: "bottom-right",
                style: { width: "400px" },
                className: "border-l-4 border-yellow-500 p-4",
            });
        }
    } finally {
        isLoading.value = false;
        clearTimeout(timeout);
    }
};

const filteredProductos = computed(() => {
    if (!searchQuery.value) return productosConcatenados.value;
    return productosConcatenados.value.filter((producto) =>
        producto && producto.nombreConcatenado
            ? producto.nombreConcatenado.toLowerCase().includes(searchQuery.value.toLowerCase())
            : false
    );
});

const paginatedProductos = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value;
    const end = start + pageSize.value;

    return filteredProductos.value.slice(start, end);
});

const seleccionarProducto = (producto) => {
    if (!productosSeleccionados.value.includes(producto)) {
        productosSeleccionados.value.push(producto);
        // console.log("Producto seleccionado:", producto);
    }
};

const eliminarProductoSeleccionado = (producto) => {
    productosSeleccionados.value = productosSeleccionados.value.filter(
        (p) => p !== producto
    );
};

const copyToClipboard = () => {
    const formattedText = filteredProductos.value.map(producto => {
        return `${producto.nombreConcatenado}\t${producto.xml_id || "Sin XML ID"}`;
    }).join("\n");
    const textArea = document.createElement("textarea");
    textArea.value = formattedText;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand("copy");
    document.body.removeChild(textArea);

    toast.success("Datos copiados al portapapeles", {
        autoClose: 3000,
        position: "bottom-right",
        style: {
            width: "400px",
        },
        className: "border-l-4 border-green-500 p-4",
    });
};

const copyToClipboardTable = () => {
    const formattedData = productosSeleccionados.value.map(producto => {
        return `${producto.nombreConcatenado}\t${producto.xml_id || "Sin XML ID"}`;
    }).join("\n");

    const textArea = document.createElement("textarea");
    textArea.value = formattedData;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand("copy");
    document.body.removeChild(textArea);

    toast.success("Datos copiados al portapapeles", {
        autoClose: 3000,
        position: "bottom-right",
        style: {
            width: "400px",
        },
        className: "border-l-4 border-green-500 p-4",
    });
};

const nextPage = () => {
    if (currentPage.value * pageSize.value < productosConcatenados.value.length) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const clearProductosCache = () => {
    Object.keys(localStorage).forEach((key) => {
        if (key.startsWith("productosRepo_")) {
            localStorage.removeItem(key);
        }
    });

    productosRepo.value = [];
    productosConcatenados.value = [];
    productosSeleccionados.value = [];

    toast.success("Se eliminó correctamente el cache de las reposiciones.", {
        autoClose: 3000,
        position: "bottom-right",
        style: { width: "400px" },
        className: "border-l-4 border-green-500 p-4",
    });
};

const handleSearch = () => {
    if (searchQuery.value) {
        default_code.value = searchQuery.value;
        fetchProductosRepo(default_code.value, "default_code");
    }
    if (searchQueryName.value) {
        default_code.value = searchQueryName.value;
        fetchProductosRepo(default_code.value, "name");
    }
};

watch(searchQuery, (newValue) => {
    if (!newValue) {
        resetProductos();
        localStorage.removeItem("searchQuery");
    } else {
        localStorage.setItem("searchQuery", newValue);
    }
});

watch(searchQueryName, (newValue) => {
    if (!newValue) {
        resetProductos();
        localStorage.removeItem("searchQueryName");
    } else {
        localStorage.setItem("searchQueryName", newValue);
    }
});

const resetProductos = () => {
    productosRepo.value = [];
    productosConcatenados.value = [];
    productosSeleccionados.value = [];
    currentPage.value = 1;
};

onMounted(() => {
    const storedQuery = localStorage.getItem("searchQuery");
    if (storedQuery) {
        searchQuery.value = storedQuery;
        fetchProductosRepo(storedQuery, "default_code");
    }
    const storedQueryName = localStorage.getItem("searchQueryName");
    if (storedQueryName) {
        searchQueryName.value = storedQueryName;
        fetchProductosRepo(storedQueryName, "name");
    }
});
</script>

<template>
    <AppLayout title="Reposición">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Reposición
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-12xl sm:px-6 lg:px-1">
                <div class="p-6 overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="relative flex w-1/2 mx-auto mb-4">
                        <i class="absolute text-gray-500 fas fa-search left-3 top-3"></i>
                        <input v-model="searchQuery" @keyup.enter="handleSearch" type="text"
                            class="w-full p-2 pl-10 border rounded-lg"
                            placeholder="Buscar producto por referencia interna" />
                        <button @click="handleSearch"
                            class="flex items-center p-2 ml-2 text-white bg-blue-500 rounded-lg">
                            <i class="text-white fas fa-search mr-2"></i> Buscar
                        </button>
                    </div>

                    <div class="relative flex w-1/2 mx-auto mb-4">
                        <i class="absolute text-gray-500 fas fa-search left-3 top-3"></i>
                        <input v-model="searchQueryName" @keyup.enter="handleSearch" type="text"
                            class="w-full p-2 pl-10 border focus:border-green-500 focus:border-[0.125rem] focus:ring-0 focus:outline-none rounded-lg"
                            placeholder="Buscar producto por nombre" />
                        <button @click="handleSearch"
                            class="flex items-center p-2 ml-2 text-white bg-green-500 rounded-lg">
                            <i class="text-white fas fa-search mr-2"></i> Buscar
                        </button>
                    </div>

                    <button @click="copyToClipboard"
                        class="px-4 py-2 mb-4 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                        <i class="mr-2 fas fa-copy"></i>
                        Copiar al portapapeles
                    </button>

                    <button @click="clearProductosCache"
                        class="float-right px-4 py-2 mb-4 font-bold text-white bg-red-500 rounded hover:bg-red-700">
                        <i class="mr-2 fas fa-trash"></i>
                        Borrar Caché
                    </button>

                    <div v-if="isLoading" class="my-4 text-center">
                        <i class="mr-2 fas fa-spinner fa-spin"></i>
                        Buscando más productos en reposición...
                    </div>

                    <div v-else>
                        <ul v-if="paginatedProductos.length > 0">
                            <li v-for="(producto, index) in paginatedProductos" :key="index"
                                class="flex justify-between py-2">
                                <span>{{ producto.nombreConcatenado }}</span>
                                <button @click="seleccionarProducto(producto)"
                                    class="px-2 py-1 font-bold text-white bg-green-500 rounded hover:bg-green-700">
                                    Seleccionar
                                </button>
                            </li>
                        </ul>
                        <p v-else>Escribe algo para buscar esos productos en reposición...</p>
                    </div>

                    <div class="flex justify-between mt-4">
                        <button @click="prevPage" :disabled="currentPage === 1"
                            class="px-4 py-2 font-bold text-white bg-gray-400 rounded hover:bg-gray-600"
                            :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }">
                            Anterior
                        </button>
                        <span class="text-gray-700">Página {{ currentPage }}</span>
                        <button @click="nextPage" :disabled="currentPage * pageSize >= productosConcatenados.length"
                            class="px-4 py-2 font-bold text-white bg-gray-400 rounded hover:bg-gray-600"
                            :class="{ 'opacity-50 cursor-not-allowed': currentPage * pageSize >= productosConcatenados.length }">
                            Siguiente
                        </button>
                    </div>

                    <div v-if="productosSeleccionados.length > 0" class="mt-8">
                        <h3 class="mb-4 text-lg font-semibold">
                            Productos Seleccionados:
                        </h3>
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 border">Producto</th>
                                    <th class="px-4 py-2 border">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(
producto, index
                                    ) in productosSeleccionados" :key="index">
                                    <td class="px-4 py-2 border">
                                        {{ producto.nombreConcatenado }}
                                    </td>
                                    <td class="px-4 py-2 text-center border">
                                        <button @click="
                                            eliminarProductoSeleccionado(
                                                producto
                                            )
                                            "
                                            class="px-2 py-1 font-bold text-white bg-red-500 rounded hover:bg-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <button @click="copyToClipboardTable"
                            class="px-4 py-2 mt-4 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                            <i class="mr-2 fas fa-copy"></i>
                            Copiar seleccionados al portapapeles
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
