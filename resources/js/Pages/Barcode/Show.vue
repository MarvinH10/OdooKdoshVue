<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modalbarcode from '@/Components/Modalbarcode.vue';

const product = ref({ variants: [] });

const error = ref('');
const loading = ref(true);
const showModal = ref(false);
const variantQuantities = ref([]);


// Watch to initialize variantQuantities when product.variants is set
watch(() => product.value.variants, (newVariants) => {
    if (newVariants) {
        variantQuantities.value = newVariants.map(() => 0);
    }
});

const fetchProductById = async (id) => {
    try {
        const response = await axios.get(`/api/get-barcode/${id}`);
        product.value = response.data;
        if (product.value.variants) {
            variantQuantities.value = product.value.variants.map(() => 0);
        }
        console.log('Product Data:', product.value); // Verifica los datos recibidos
    } catch (err) {
        error.value = 'Error fetching product: ' + err.message;
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const openModal = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const confirmQuantities = () => {
    if (variantQuantities.value.some(qty => qty <= 0)) {
        alert('Please enter a valid quantity for each variant.');
        return;
    }
    console.log('Quantities confirmed:', variantQuantities.value);
    closeModal();
    duplicateVariants();
};

const duplicateVariants = () => {
    const duplicatedVariants = [];
    product.value.variants.forEach((variant, index) => {
        for (let i = 0; i < variantQuantities.value[index]; i++) {
            duplicatedVariants.push({ ...variant });
        }
    });
    product.value.variants = duplicatedVariants;
};

const getLastThreeCategoryParts = (category) => {
    if (!category) return 'No disponible';
    const parts = category.split(' / ');
    return parts.slice(-3).join(' / ');
};

const printSVG = () => {
    const printWindow = window.open('', '_blank');
    const htmlContent = `
        <html>
            <head>
                <title>Imprimir etiquetas</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .label-container { page-break-after: always; display: flex; justify-content: center; align-items: center; }
                    svg { margin: 20px; }
                </style>
            </head>
            <body>
                ${product.value.variants.map(variant => `
                    <div class="label-container">
                        <svg width="200" height="350" viewBox="0 0 200 350">
                            <rect width="200" height="350" fill="white" />
                            <text x="10" y="40" font-size="24" font-weight="bold">S/ ${variant.list_price}</text>
                            <text x="10" y="70" font-size="12">${getLastThreeCategoryParts(product.value.categ_id ? product.value.categ_id[1] : '')}</text>
                            <foreignObject x="50" y="90" width="100" height="100">
                                <div xmlns="http://www.w3.org/1999/xhtml">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?data=${variant.barcode}&amp;size=100x100" alt="qr code" class="barcode-image"/>
                                </div>
                            </foreignObject>
                            <text x="10" y="220" font-size="10">${variant.barcode}</text>
                            <text x="10" y="250" font-size="12">${variant.name}</text>
                            <text x="10" y="270" font-size="10">Ref: ${variant.default_code}</text>
                        </svg>
                    </div>
                `).join('')}
            </body>
        </html>
    `;
    printWindow.document.write(htmlContent);
    printWindow.document.close();

    // Esperar a que las imágenes se carguen antes de imprimir
    const images = printWindow.document.querySelectorAll('.barcode-image');
    let loadedImagesCount = 0;

    images.forEach((img) => {
        img.onload = () => {
            loadedImagesCount++;
            if (loadedImagesCount === images.length) {
                printWindow.print();
            }
        };
        img.onerror = () => {
            loadedImagesCount++;
            if (loadedImagesCount === images.length) {
                printWindow.print();
            }
        };
    });

    // Si no hay imágenes, imprimir directamente
    if (images.length === 0) {
        printWindow.print();
    }
};

onMounted(async () => {
    const url = window.location.href;
    const id = url.substring(url.lastIndexOf('/') + 1);
    console.log('Captured ID:', id); // Asegúrate de que el ID se está capturando correctamente
    await fetchProductById(id);
});
</script>

<template>
    <AppLayout title="Producto">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Producto
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h1>Detalles del Producto</h1>
                    <div v-if="error" class="text-red-500">{{ error }}</div>
                    <div v-if="loading">Loading...</div>
                    <div v-else>
                        <div v-if="product.id">
                            <div class="text-center" v-if="!product.variants.length">
                                <p>ID: {{ product.id }}</p>
                                <p>Nombre: {{ product.name }}</p>
                                <p>Precio: {{ product.list_price }}</p>
                                <p>Categoría: {{ getLastThreeCategoryParts(product.categ_id ? product.categ_id[1] : '') }}</p>
                                <p>Código de Referencia: {{ product.default_code }}</p>
                                <p>Barcode: {{ product.barcode }}</p>
                            </div>

                            <div v-if="product.variants.length">
                                <h2>Variantes</h2>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                    <div v-for="(variant, index) in product.variants" :key="variant.id" class="border rounded p-4">
                                        <svg width="200" height="350" viewBox="0 0 200 350">
                                            <rect width="200" height="350" fill="white" />
                                            <text x="10" y="40" font-size="24" font-weight="bold">S/ {{ variant.list_price }}</text>
                                            <text x="10" y="70" font-size="12">{{ getLastThreeCategoryParts(product.categ_id ? product.categ_id[1] : '') }}</text>
                                            <foreignObject x="50" y="90" width="100" height="100">
                                                <div xmlns="http://www.w3.org/1999/xhtml">
                                                    <img :src="'https://api.qrserver.com/v1/create-qr-code/?data=' + variant.barcode + '&amp;size=100x100'" alt="qr code" class="barcode-image"/>
                                                </div>
                                            </foreignObject>
                                            <text x="10" y="220" font-size="10">{{ variant.barcode }}</text>
                                            <text x="10" y="250" font-size="12">{{ variant.name }}</text>
                                            <text x="10" y="270" font-size="10">Ref: {{ variant.default_code }}</text>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Botón para abrir el modal -->
                                <button @click="openModal" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded">Confirmar Cantidades</button>
                                <!-- Botón para imprimir -->
                                <button @click="printSVG" class="mt-6 ml-4 px-4 py-2 bg-green-600 text-white rounded">Imprimir</button>
                            </div>
                        </div>
                        <div v-else>
                            <p>No se encontró el producto.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Componente Modal -->
        <Modalbarcode :show="showModal" @close="closeModal">
            <template #title>Confirmar Cantidades</template>
            <template #content>
                <p>Por favor, confirma las cantidades para cada variante antes de proceder.</p>
                <ul>
                    <li v-for="(quantity, index) in variantQuantities.value" :key="index">
                        Variante {{ index + 1 }}: <input type="number" min="0" v-model="variantQuantities.value[index]" />
                    </li>
                </ul>
            </template>
            <template #footer>
                <button @click="closeModal" class="mr-4 px-4 py-2 bg-gray-300 text-black rounded">Cancelar</button>
                <button @click="confirmQuantities" class="px-4 py-2 bg-blue-600 text-white rounded">Confirmar</button>
            </template>
        </Modalbarcode>
    </AppLayout>
</template>

<style scoped>
.grid {
    display: grid;
    gap: 1rem;
}
</style>
