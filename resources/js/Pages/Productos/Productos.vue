<script lang="ts" setup>
import { ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import ProductoTabla from "@/Components/Productos/ProductoTabla.vue";
import ModalRegistrar from "@/Components/Productos/ModalRegistrar.vue";

const showModal = ref(false);

type Producto = {
    id: number;
    name: string;
    code: string;
    price: number;
};

const productos = ref<Producto[]>([
    { id: 1, name: "Producto 1", code: "P001", price: 100 },
    { id: 2, name: "Producto 2", code: "P002", price: 200 },
    { id: 3, name: "Producto 3", code: "P003", price: 300 },
]);

const openModal = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const handleSubmit = (producto: any) => {
    console.log("Producto registrado:", producto);
    closeModal();
};

const registrarProductos = () => {
    // console.log("Registrar productos en Odoo");
};
</script>

<template>
    <AppLayout title="Productos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Productos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-12xl mx-auto sm:px-6 lg:px-1">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <ProductoTabla :productos="productos" @agregar="openModal" @registrar="registrarProductos" />
                </div>
            </div>
        </div>

        <ModalRegistrar :show="showModal" @close="closeModal" @submit="handleSubmit" />
    </AppLayout>
</template>
