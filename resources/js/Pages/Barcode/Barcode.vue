<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';

const barcodes = ref([]);
const error = ref('');
const loading = ref(true);

const fetchBarcodes = async () => {
    try {
        const response = await axios.get('/api/get-barcode');
        barcodes.value = response.data;
    } catch (err) {
        error.value = 'Error fetching Barcodes: ' + err.message;
        console.error(err);
    } finally {
        loading.value = false;
    }
};

onMounted(async () => {
    await fetchBarcodes();
});
</script>

<template>
    <AppLayout title="Proveedores">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Barcode
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h1>Barcode</h1>
                    <div v-if="error" class="text-red-500">{{ error }}</div>
                    <div v-if="loading">Loading...</div>
                    <div v-else>
                        <div v-for="barcode in barcodes" :key="barcode.id" class="text-center">
                            {{ barcode.name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
    <Modalbarcode :show="true">
    <template #title>crear

    </template>
    <template #content>content
    </template>

    <template #footer>footer</template>
</Modalbarcode>
</template>





