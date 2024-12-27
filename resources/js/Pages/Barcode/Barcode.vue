<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed, watch } from "vue";
import MobileBarcode from "@/Components/MobileBarcode.vue";
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

const isMobileTablet = ref(false);
const selectedButtonIndex = ref(null);
const showModalCantidad = ref(false);
const imageItems = ref([]);

const revisarIsMobileTablet = () => {
    isMobileTablet.value = window.innerWidth <= 980;
};

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

const cargarDatosLocalmente = (id) => {
    const storedData = localStorage.getItem(`producto_${id}`);
    if (storedData) {
        imageItems.value = JSON.parse(storedData);
        // console.log("Datos cargados desde localStorage:", imageItems.value);
    } else {
        console.log("No se encontraron datos en localStorage para el ID:", id);
    }
};

const filteredItems = computed(() => {
    return selectedButtonIndex.value !== null ? imageItems.value : [];
});

const toggleSelection = (index) => {
    selectedButtonIndex.value = selectedButtonIndex.value === index ? null : index;
};

const selectedItem = computed(() => {
    return selectedButtonIndex.value !== null
        ? imageItems.value[selectedButtonIndex.value]
        : null;
});

const printSelectedContent = () => {
    if (selectedItem.value) {
        const printWindow = window.open("", "_blank");

        const printContent = `
            <html>
                <head>
                    <title>Barcode</title>
                    <style>
                        body { font-family: Arial, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
                        .print-content { width: 240px; text-align: center; padding: 10px; background-color: #fff; }
                        .price { font-size: 24px; font-weight: bold; margin-bottom: 8px; }
                        .category { font-size: 12px; color: gray; margin-bottom: 8px; }
                        .qr-container { display: flex; align-items: center; justify-content: center; position: relative; }
                        .attribute { position: absolute; left: 50px; top: 50%; transform: translateY(-50%) rotate(-90deg); font-size: 12px; font-weight: bold; }
                        .qr-code { margin: 0 auto; }
                        .code { font-size: 10px; font-family: monospace; margin-top: 8px; }
                        .description { font-size: 10px; color: gray; margin-top: 4px; }
                    </style>
                </head>
                <body>
                    <div class="print-content">
                        <div class="price">S/ 50</div>
                        <div class="category">CABALLERO / ZAPATILLA / ADIDAS</div>
                        <div class="qr-container">
                            <div class="attribute">ROJO</div>
                            <img src="${qrCodeDataUrl.value}" alt="QR Code" class="qr-code" width="100" height="100" />
                        </div>
                        <div class="code">201922836761</div>
                        <div class="description">POLO</div>
                    </div>
                </body>
            </html>
        `;

        printWindow.document.open();
        printWindow.document.write(printContent);
        printWindow.document.close();

        printWindow.onload = () => {
            printWindow.print();
            printWindow.close();
        };
    } else {
        alert("No hay contenido seleccionado para imprimir.");
    }
};

const openModalCantidad = () => {
    showModalCantidad.value = true;
}

const closeModalCantidad = () => {
    showModalCantidad.value = false;
}

onMounted(() => {
    revisarIsMobileTablet();
    window.addEventListener("resize", revisarIsMobileTablet);

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

        <div v-if="isMobileTablet">
            <MobileBarcode :selectedButtonIndex="selectedButtonIndex" :toggleSelection="toggleSelection"
                :selectedItem="selectedItem" :imageItems="imageItems" @print-selected="printSelectedContent"
                @open-cantidades-modal="openModalCantidad" />
        </div>

        <div v-else class="py-12">
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
