<script setup>
import { ref, onMounted, computed } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

const productosRepo = ref([]);
const productosConcatenados = ref([]);
const searchQuery = ref("");
const isLoading = ref(false);

const fetchProductosRepo = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get("/reposicion/data_repo/traer");
        productosRepo.value = response.data;

        productosConcatenados.value = productosRepo.value.map((producto) => {
            const referencia = `[${producto.default_code}]`;
            const nombreProducto = producto.name;

            if (
                producto.attribute_values &&
                producto.attribute_values.length > 0
            ) {
                const valoresAtributos = producto.attribute_values.join(", ");
                return `${referencia} ${nombreProducto} (${valoresAtributos})`;
            } else {
                return `${referencia} ${nombreProducto}`;
            }
        });
    } catch (error) {
        console.error("Error al traer productos repo:", error);
    } finally {
        isLoading.value = false;
    }
};

const filteredProductos = computed(() => {
    if (!searchQuery.value) {
        return [];
    }
    return productosConcatenados.value.filter((producto) =>
        producto.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const copyToClipboard = () => {
    const formattedText = filteredProductos.value.join("\n");
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

const refreshPage = () => {
    fetchProductosRepo();
};

onMounted(() => {
    fetchProductosRepo();
});
</script>

<template>
    <AppLayout title="Reposición">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reposición
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-12xl mx-auto sm:px-6 lg:px-1">
                <div
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6"
                >
                    <div class="mb-4 relative">
                        <i
                            class="fas fa-search absolute left-3 top-3 text-gray-500"
                        ></i>
                        <input
                            v-model="searchQuery"
                            type="text"
                            class="border p-2 pl-10 rounded-lg w-full"
                            placeholder="Buscar producto por nombre o referencia interna..."
                        />
                    </div>
                    <button
                        @click="copyToClipboard"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4"
                    >
                        <i class="fas fa-copy mr-2"></i>
                        Copiar al portapapeles
                    </button>
                    <button
                        @click="refreshPage"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mb-4 float-right"
                    >
                        <i class="fas fa-sync-alt mr-2"></i>
                        Actualizar
                    </button>

                    <div v-if="isLoading" class="text-center my-4">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Cargando productos...
                    </div>

                    <div v-else>
                        <ul v-if="filteredProductos.length > 0">
                            <li
                                v-for="(producto, index) in filteredProductos"
                                :key="index"
                                class="py-2"
                            >
                                {{ producto }}
                            </li>
                        </ul>
                        <p v-else>Escribe algo para buscar esos productos en reposición...</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
