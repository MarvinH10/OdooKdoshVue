<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

const productosFavoritos = ref([]);
const productosConcatenados = ref([]);

const convertirFavoritosANoFavoritos = async () => {
    try {
        const response = await axios.post(
            "/formulas/data_favorite/convertir_no_favoritos"
        );
        alert("Productos favoritos convertidos a no favoritos");
    } catch (error) {
        console.error("Error al convertir productos a no favoritos:", error);
    }
};

const fetchProductosFavoritos = async () => {
    try {
        const response = await axios.get("/formulas/data_favorite/traer");
        productosFavoritos.value = response.data;

        productosConcatenados.value = [];

        productosFavoritos.value.forEach((producto) => {
            const referencia = `[${producto.default_code}]`;
            const nombreProducto = producto.name;

            if (producto.attribute_values.length > 0) {
                const valoresAtributos = producto.attribute_values.join(", ");
                productosConcatenados.value.push(
                    `${referencia} ${nombreProducto} (${valoresAtributos})`
                );
            } else {
                productosConcatenados.value.push(
                    `${referencia} ${nombreProducto}`
                );
            }
        });
    } catch (error) {
        console.error("Error al traer productos favoritos:", error);
    }
};

const copyToClipboard = () => {
    const formattedText = productosConcatenados.value.join("\n");
    const textArea = document.createElement("textarea");
    textArea.value = formattedText;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand("copy");
    document.body.removeChild(textArea);

    alert("Datos copiados al portapapeles");
};

onMounted(() => {
    fetchProductosFavoritos();
});
</script>

<template>
    <AppLayout title="Formulas">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Formulas
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-12xl mx-auto sm:px-6 lg:px-1">
                <div
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6"
                >
                    <button
                        @click="convertirFavoritosANoFavoritos"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-4 mr-3"
                    >
                        <i class="fas fa-heart-broken mr-2"></i>
                        Convertir a No Favoritos
                    </button>
                    <button
                        @click="copyToClipboard"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4"
                    >
                        <i class="fas fa-copy mr-2"></i>
                        Copiar al portapapeles
                    </button>
                    <ul>
                        <li
                            v-for="(producto, index) in productosConcatenados"
                            :key="index"
                        >
                            {{ producto }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
