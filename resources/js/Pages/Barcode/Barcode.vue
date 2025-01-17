<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, computed, watch } from "vue";
import { styles } from "@/stylesConfig";
import { useRoute } from "vue-router";
import { generateContent1 } from "@/generateContent1.js";
import { generateContent2 } from "@/generateContent2.js";
import { generateContent3 } from "@/generateContent3.js";
import { generateContent4 } from "@/generateContent4.js";
import { generateContent5 } from "@/generateContent5.js";
import { generateContent6 } from "@/generateContent6.js";
import { generateContent7 } from "@/generateContent7.js";
import ModalCantidadBarcodes from "@/Components/ModalCantidadBarcodes.vue";
import MedidasQR from "@/Components/MedidasQR.vue";
import QRCode from "qrcode";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const route = useRoute();
const productId = ref(route.query.product_id ? parseInt(route.query.product_id, 10) : undefined);
const isUploading = ref(false);

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
    generateContent3,
    generateContent4,
    generateContent5,
    generateContent6,
    generateContent7,
];

const selectedButtonIndex = ref(null);
const showModalCantidad = ref(false);
const imageItems = ref([]);

const traerDatoProductoSinCache = async (id) => {
    isUploading.value = true;
    try {
        const response = await axios.get(`/barcode/traer/${id}`);
        if (response.data && response.data.length > 0) {
            const producto = response.data[0];

            if (!producto.variantes || !Array.isArray(producto.variantes)) {
                console.error("El campo 'variantes' no está presente o no es válido:", producto);
                toast.error("El producto no contiene variantes o no es válido.", {
                    autoClose: 3000,
                    position: "bottom-right",
                });
                return;
            }

            const promises = producto.variantes.map(async (item) => {
                const qrCode = await QRCode.toDataURL(item.barcode || "", { width: 100, margin: 1 });
                return {
                    categ_id: producto.categ_id ? producto.categ_id[1] : "",
                    code: item.barcode || "",
                    description: `${producto.name || ""}`,
                    price: item.lst_price ? item.lst_price.toFixed(2) : "",
                    attribute: item.atributos
                        ? item.atributos.map(attr => attr.split(":")[1]?.trim() || attr).join(", ")
                        : "",
                    default_code: item.default_code || "",
                    qrCode,
                    status: "activo",
                    quantity: 1,
                };
            });

            imageItems.value = await Promise.all(promises);

            localStorage.setItem(`producto_${id}`, JSON.stringify(imageItems.value));
            toast.success("Datos cargados correctamente.", {
                autoClose: 3000,
                position: "bottom-right",
            });
        } else {
            console.error("La respuesta no contiene datos válidos:", response.data);
            toast.error("No se pudo obtener información del producto.", {
                autoClose: 3000,
                position: "bottom-right",
            });
        }
    } catch (error) {
        console.error("Error al obtener datos del producto:", error.response || error);
        toast.error("Ocurrió un error al consultar los datos del producto.", {
            autoClose: 3000,
            position: "bottom-right",
        });
    } finally {
        isUploading.value = false;
    }
};

const traerDatoProducto = async (id) => {
    try {
        const storedData = localStorage.getItem(`producto_${id}`);
        if (storedData) {
            imageItems.value = JSON.parse(storedData).map(item => ({
                ...item,
                status: "activo",
                quantity: item.quantity || 1,
            }));
            return;
        }

        const response = await axios.get(`/barcode/traer/${id}`);
        if (response.data && response.data.length >= 0) {
            const producto = response.data[0];

            const promises = producto.variantes.map(async (item) => {
                const qrCode = await QRCode.toDataURL(item.barcode || "", { width: 100, margin: 1 });
                return {
                    categ_id: producto.categ_id ? producto.categ_id[1] : "",
                    code: item.barcode || "",
                    description: `${producto.name || ""}`,
                    price: item.lst_price ? item.lst_price.toFixed(2) : "",
                    attribute: item.atributos
                        ? item.atributos.map(attr => attr.split(":")[1]?.trim() || attr).join(", ")
                        : "",
                    default_code: item.default_code || "",
                    qrCode,
                    status: "activo",
                    quantity: 1,
                };
            });

            imageItems.value = await Promise.all(promises);

            localStorage.setItem(`producto_${id}`, JSON.stringify(imageItems.value));
            toast.success("Cargo correctamente y ya puedes continuar, selecciona un tipo.", {
                autoClose: 3000,
                position: "bottom-right",
            });
        } else {
            console.error("Error al obtener datos del producto.", response.data);
            toast.error("Error al obtener datos del producto.", {
                autoClose: 3000,
                position: "bottom-right",
            });
        }
    } catch (error) {
        console.error("Error al obtener datos del producto:", error);
        toast.error("El ID del producto que elegiste no es un producto almacenable.", {
            autoClose: 3000,
            position: "bottom-right",
        });
    }
};

const filteredItems = computed(() => {
    return selectedButtonIndex.value !== null
        ? imageItems.value.filter(item => item.status === "activo")
        : [];
});

const filteredItemsFor3 = computed(() => {
    if (selectedButtonIndex.value === null) return [];

    const itemsPerGroup = 3;
    const activeItems = imageItems.value.filter(item => item.status === "activo");
    const groupedItems = [];
    for (let i = 0; i < activeItems.length; i += itemsPerGroup) {
        groupedItems.push(activeItems.slice(i, i + itemsPerGroup));
    }
    return groupedItems;
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

    if (selectedButtonIndex.value === 2) {
        const selectedData = filteredItemsFor3.value;

        if (!selectedData.length) {
            alert("No hay elementos para imprimir.");
            return;
        }

        const generateContent = contentGenerators[2];

        const printWindow = window.open("", "_blank");
        const printContent = selectedData.map((group) => generateContent(group, style)).join("");

        printWindow.document.open();
        printWindow.document.write(`
            <html>
                <head>
                    <title>Barcode QR</title>
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

        return;
    }

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
                <title>Barcode QR</title>
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

const updateItemQuantities = (updatedItems) => {
    if (!Array.isArray(updatedItems)) {
        console.error("updatedItems no es un array válido.");
        return;
    }

    updatedItems.forEach((updatedItem) => {
        const existingItem = imageItems.value.find((item) => item.code === updatedItem.code);

        if (existingItem) {
            existingItem.quantity = updatedItem.quantity;
            existingItem.status = updatedItem.status;
        } else {
            imageItems.push(updatedItem);
        }
    });

    imageItems.value = updatedItems.flatMap((updatedItem) => {
        if (updatedItem.quantity === 0) {
            return { ...updatedItem, status: "inactivo" };
        }

        return Array.from({ length: updatedItem.quantity }, () => ({
            ...updatedItem,
            status: "activo",
        }));
    });

    console.log("Quantities updated in imageItems:", imageItems.value);
};

watch(
    () => route.query.product_id,
    (newProductId) => {
        if (newProductId) {
            productId.value = newProductId;
            traerDatoProducto(newProductId);
        } else {
            console.error("El product_id cambió, pero no es válido.");
        }
    }
);
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
                            <button v-if="!isUploading" @click="traerDatoProductoSinCache(productId)"
                                class="flex flex-col-2 bg-green-700 text-white text-sm px-[9.5px] py-[0.5px] mb-1 rounded border text-center items-center justify-center">
                                <i class="fas fa-sync-alt mr-2"></i> Actualizar
                            </button>

                            <div v-else class="text-left text-blue-500 font-bold text-sm">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Actualizando...
                            </div>
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

        <ModalCantidadBarcodes v-if="showModalCantidad" :imageItems="imageItems" @close="closeModalCantidad"
            @updateQuantities="updateItemQuantities" />
    </AppLayout>
</template>
