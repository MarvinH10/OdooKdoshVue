<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed, watch } from "vue";
import MobileBarcode from "@/Components/MobileBarcode.vue";
import ModalCantidadBarcodes from "@/Components/ModalCantidadBarcodes.vue";
import MedidasQR from "@/Components/MedidasQR.vue";
import QRCode from "qrcode";

const isMobileTablet = ref(false);
const selectedButtonIndex = ref(null);
const showModalCantidad = ref(false);
const qrCodeDataUrl = ref("");

const imageItems = ref([
    { src: "/images/BarcodeMedidas/type1.jpg", code: "7633188062077", description: "ADIDAS ZAPATILLAS SOLARGLIDE ST 3 - FV7251", price: "450.00", attribute: "6.5" },
    { src: "/images/BarcodeMedidas/type2.jpg", code: "7633188062077", description: "ADIDAS ZAPATILLAS SOLARGLIDE ST 3 - FV7251", price: "450.00", attribute: "6.5" },
    { src: "/images/BarcodeMedidas/type3.jpg", code: "7633188062077", description: "ADIDAS ZAPATILLAS SOLARGLIDE ST 3 - FV7251", price: "450.00", attribute: "6.5" },
    { src: "/images/BarcodeMedidas/type4.jpg", code: "7633188062077", description: "ADIDAS ZAPATILLAS SOLARGLIDE ST 3 - FV7251", price: "450.00", attribute: "6.5" },
    { src: "/images/BarcodeMedidas/type1.jpg", code: "7633188062077", description: "ADIDAS ZAPATILLAS SOLARGLIDE ST 3 - FV7251", price: "450.00", attribute: "6.5" },
    { src: "/images/BarcodeMedidas/type2.jpg", code: "7633188062077", description: "ADIDAS ZAPATILLAS SOLARGLIDE ST 3 - FV7251", price: "450.00", attribute: "6.5" },
    { src: "/images/BarcodeMedidas/type7.png", code: "7633188062077", description: "ADIDAS ZAPATILLAS SOLARGLIDE ST 3 - FV7251", price: "450.00", attribute: "6.5" },
]);

const revisarIsMobileTablet = () => {
    isMobileTablet.value = window.innerWidth <= 980;
};

onMounted(() => {
    revisarIsMobileTablet();
    window.addEventListener("resize", revisarIsMobileTablet);
});

const generateQRCode = async () => {
    if (selectedItem.value) {
        qrCodeDataUrl.value = await QRCode.toDataURL(selectedItem.value.code, { width: 100, margin: 1 });
    }
};

const toggleSelection = (index) => {
    selectedButtonIndex.value = selectedButtonIndex.value === index ? null : index;
};

const selectedItem = computed(() => {
    return selectedButtonIndex.value !== null
        ? imageItems.value[selectedButtonIndex.value]
        : null;
});

watch(selectedItem, generateQRCode, { immediate: true });

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
                        <div class="price">S/ ${selectedItem.value.price}</div>
                        <div class="category">CABALLERO / ZAPATILLA / ADIDAS</div>
                        <div class="qr-container">
                            <div class="attribute">${selectedItem.value.attribute}</div>
                            <img src="${qrCodeDataUrl.value}" alt="QR Code" class="qr-code" width="100" height="100" />
                        </div>
                        <div class="code">${selectedItem.value.code}</div>
                        <div class="description">${selectedItem.value.description}</div>
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
                                <button v-for="(item, index) in imageItems" :key="index" @click="toggleSelection(index)"
                                    :class="[
                                        'border border-dashed w-32 h-30 p-2 rounded bg-gray-100',
                                        selectedButtonIndex === index ? 'bg-[#8d99ae] border-gray-700' : 'hover:bg-[#8d99ae] hover:border-gray-700'
                                    ]">
                                    <div class="w-auto p-2 rounded">
                                        <img :src="item.src" class="w-32 h-30 object-cover" alt="Barcode" />
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <MedidasQR :selectedItem="selectedItem" :qrCodeDataUrl="qrCodeDataUrl"
                    :selectedButtonIndex="selectedButtonIndex" />
            </div>
        </div>

        <ModalCantidadBarcodes v-if="showModalCantidad" @close="closeModalCantidad" />
    </AppLayout>
</template>
