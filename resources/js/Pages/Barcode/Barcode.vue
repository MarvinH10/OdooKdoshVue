<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed } from "vue";
import { styles } from "@/stylesConfig";
import { generateContent1 } from "@/generateContent1.js";
import { generateContent2 } from "@/generateContent2.js";
import ModalCantidadBarcodes from "@/Components/ModalCantidadBarcodes.vue";
import MedidasQR from "@/Components/MedidasQR.vue";
import QRCode from "qrcode";

const buttonImages = [
    "/images/BarcodeMedidas/type1.jpg",
    "/images/BarcodeMedidas/type2.jpg",
    "/images/BarcodeMedidas/type3.jpg",
    "/images/BarcodeMedidas/type4.jpg",
    "/images/BarcodeMedidas/type1.jpg",
    "/images/BarcodeMedidas/type2.jpg",
    "/images/BarcodeMedidas/type7.png",
];

const contentGenerators = [
    generateContent1,
    generateContent2,
];

const selectedButtonIndex = ref(null);
const showModalCantidad = ref(false);
const imageItems = ref([]);

const traerDatoProducto = async (id) => {
    try {
        const storedData = localStorage.getItem(`producto_${id}`);
        if (storedData) {
            imageItems.value = JSON.parse(storedData);
            // console.log("Datos cargados desde localStorage:", imageItems.value);
            return;
        }

        const response = await axios.get(`/barcode/traer/${id}`);
        if (response.data && response.data.length > 0) {
            const producto = response.data[0];

            const promises = producto.variantes.map(async (item) => {
                const qrCode = await QRCode.toDataURL(item.barcode || "", { width: 100, margin: 1 });
                return {
                    categ_id: producto.categ_id ? producto.categ_id[1] : "",
                    code: item.barcode || "",
                    description: `${item.default_code || ""}`,
                    price: item.lst_price ? item.lst_price.toFixed(2) : "",
                    attribute: item.atributos ? item.atributos.join(", ") : "",
                    qrCode,
                };
            });

            imageItems.value = await Promise.all(promises);

            localStorage.setItem(`producto_${id}`, JSON.stringify(imageItems.value));
            // console.log("Datos con QR Codes generados y almacenados:", imageItems.value);
        } else {
            console.error("La respuesta no contiene 'variantes'.", response.data);
        }
    } catch (error) {
        console.error("Error al obtener datos del producto:", error);
    }
};

const filteredItems = computed(() => {
    return selectedButtonIndex.value !== null ? imageItems.value : [];
});

const toggleSelection = (index) => {
    selectedButtonIndex.value = selectedButtonIndex.value === index ? null : index;
};

const printSelectedContent = () => {
    if (selectedButtonIndex.value === null) {
        alert("Por favor, selecciona un diseño antes de imprimir.");
        return;
    }

    const style = styles[selectedButtonIndex.value];
    const selectedData = filteredItems.value;

    if (!selectedData.length) {
        alert("No hay elementos para imprimir.");
        return;
    }

    const generateContent = contentGenerators[selectedButtonIndex.value];

    if (!generateContent) {
        alert("No se encontró un generador de contenido para este diseño.");
        return;
    }

    const printWindow = window.open("", "_blank");
    const printContent = selectedData.map((item) => generateContent(item, style)).join("");

    printWindow.document.open();
    printWindow.document.write(`
        <html>
            <head>
                <title>Impresión</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        padding: 20px;
                        background-color: #f9f9f9;
                    }
                </style>
            </head>
            <body>${printContent}</body>
        </html>
    `);
    printWindow.document.close();

    printWindow.onload = () => {
        printWindow.print();
        printWindow.close();
    };
};

const openModalCantidad = () => {
    showModalCantidad.value = true;
}

const closeModalCantidad = () => {
    showModalCantidad.value = false;
}

onMounted(() => {
    traerDatoProducto(72);
});
</script>

<template>
    <AppLayout title="Barcode">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Barcode
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-12xl mx-auto sm:px-6 lg:px-1">
                <div class="bg-white overflow-hidden p-6">
                    <div class="flex flex-wrap">
                        <div class="sm:w-1/6 lg:w-1/5 xl:w-1/4">
                            <button
                                class="flex flex-col bg-blue-700 text-white text-sm px-[15.5px] py-[0.5px] mb-1 rounded border text-center">
                                Seleccionar
                            </button>
                            <button @click="printSelectedContent"
                                class="flex flex-col bg-blue-700 text-white text-sm px-[25.5px] py-[0.5px] mb-5 rounded border text-center">
                                Imprimir
                            </button>
                            <button @click="openModalCantidad"
                                class="flex flex-col bg-blue-700 text-white text-sm px-[15.5px] py-[0.5px] rounded border text-center">
                                Cantidades
                            </button>
                        </div>

                        <div class="w-1/2">
                            <div class="flex relative">
                                <button v-for="(src, index) in buttonImages" :key="index"
                                    @click="toggleSelection(index)" :class="[
                                        'border border-dashed w-32 h-30 p-2 rounded bg-gray-100',
                                        selectedButtonIndex === index ? 'bg-[#8d99ae] border-gray-700' : 'hover:bg-[#8d99ae] hover:border-gray-700'
                                    ]">
                                    <div class="w-auto p-2 rounded">
                                        <img :src="src" class="w-32 h-30 object-cover" alt="Barcode" />
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <MedidasQR :filteredItems="filteredItems" :selectedButtonIndex="selectedButtonIndex" />
            </div>
        </div>

        <ModalCantidadBarcodes v-if="showModalCantidad" @close="closeModalCantidad" />
    </AppLayout>
</template>
