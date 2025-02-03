<script setup>
import { ref, onMounted } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

const productosFavoritos = ref([]);
const productosConcatenados = ref([]);
const isLoading = ref(false);
const isConverting = ref(false);

const guardarALocalStorage = (data) => {
    localStorage.setItem("productosFavoritos", JSON.stringify(data));
};

const cargarDeLocalStorage = () => {
    const data = localStorage.getItem("productosFavoritos");
    return data ? JSON.parse(data) : [];
};

const convertirFavoritosANoFavoritos = async () => {
    try {
        isConverting.value = true;
        await axios.post("/formulas/data_favorite/convertir_no_favoritos");
        toast.success("Productos favoritos convertidos a no favoritos", {
            autoClose: 3000,
            position: "bottom-right",
            style: {
                width: "400px",
            },
            className: "border-l-4 border-green-500 p-4",
        });
        traerProductosFavoritos();
    } catch (error) {
        console.error("Error al convertir productos a no favoritos:", error);
    } finally {
        isConverting.value = false;
    }
};

const traerProductosFavoritos = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get("/formulas/data_favorite/traer");
        productosFavoritos.value = response.data;

        productosConcatenados.value = [];

        productosFavoritos.value.forEach((producto) => {
            const referencia = producto.default_code ? `[${producto.default_code}] ` : "";
            const nombreProducto = producto.name;

            if (producto.attribute_values.length > 0) {
                const valoresAtributos = producto.attribute_values.join(", ");
                productosConcatenados.value.push(
                    referencia
                        ? `${referencia}${nombreProducto} (${valoresAtributos})`
                        : `${nombreProducto} (${valoresAtributos})`
                );
            } else {
                productosConcatenados.value.push(
                    referencia
                        ? `${referencia}${nombreProducto}`
                        : `${nombreProducto}`
                );
            }
        });

        guardarALocalStorage(productosFavoritos.value);
    } catch (error) {
        console.error("Error al traer productos favoritos:", error);
    } finally {
        isLoading.value = false;
    }
};

const cargarProductosFavoritos = () => {
    productosFavoritos.value = cargarDeLocalStorage();
    productosConcatenados.value = productosFavoritos.value.map((producto) => {
        const referencia = producto.default_code ? `[${producto.default_code}] ` : "";
        const nombreProducto = producto.name;

        if (producto.attribute_values.length > 0) {
            const valoresAtributos = producto.attribute_values.join(", ");
            return referencia
                ? `${referencia}${nombreProducto} (${valoresAtributos})`
                : `${nombreProducto} (${valoresAtributos})`;
        } else {
            return referencia
                ? `${referencia}${nombreProducto}`
                : `${nombreProducto}`;
        }
    });
};

const copyToClipboard = () => {
    const xmlIds = productosFavoritos.value
        .map(producto => producto.xml_id)
        .filter(xml_id => xml_id);

    const formattedTextbyxmlIds = xmlIds.join("\n");
    const formattedText = productosConcatenados.value.join("\n");

    const combinedText = formattedText
        .split("\n")
        .map((line, index) => {
            const xmlIdLine = formattedTextbyxmlIds.split("\n")[index] || "";
            return `${line}\t${xmlIdLine}`;
        })
        .join("\n");

    const textArea = document.createElement("textarea");
    textArea.value = combinedText;
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
    traerProductosFavoritos();
};

onMounted(() => {
    cargarProductosFavoritos();
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
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <button @click="convertirFavoritosANoFavoritos"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-4 mr-3"
                        :disabled="isConverting">
                        <i class="fas fa-heart-broken mr-2"></i>
                        Convertir a No Favoritos
                    </button>
                    <button @click="copyToClipboard"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
                        <i class="fas fa-copy mr-2"></i>
                        Copiar al portapapeles
                    </button>
                    <button @click="refreshPage"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mb-4 float-right">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Actualizar
                    </button>
                    <div v-if="isConverting" class="text-center my-4 text-green-600 font-bold">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Convirtiendo
                        productos a no favoritos...
                    </div>

                    <div v-if="isLoading" class="text-center my-4">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando
                        productos favoritos...
                    </div>

                    <p v-if="!isLoading && !productosFavoritos.length" class="mt-4 text-center text-gray-500">
                        No hay productos favoritos por mostrar
                    </p>

                    <ul v-else>
                        <li v-for="(producto, index) in productosConcatenados" :key="index">
                            {{ producto }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
