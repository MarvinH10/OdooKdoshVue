<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";

const productosRegistrados = ref([]); 
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Productos Registrados
                    </h3>
                    <ul v-if="productosRegistrados.length > 0">
                        <li v-for="(producto, index) in productosRegistrados" :key="index">
                            {{ producto }}
                        </li>
                    </ul>
                    <p v-else>No hay productos registrados aún.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
