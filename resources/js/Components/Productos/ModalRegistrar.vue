<script lang="ts" setup>
import { defineProps, defineEmits, reactive } from "vue";

defineProps({
    show: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(["close", "submit"]);

const producto = reactive({
    name: "",
    code: "",
    price: 0,
    categoryPrincipal: "",
    subcategory1: "",
    subcategory2: "",
    subcategory3: "",
    subcategory4: "",
    attributes: [],
});

const closeModal = () => {
    producto.name = "";
    producto.code = "";
    producto.price = 0;
    producto.categoryPrincipal = "";
    producto.subcategory1 = "";
    producto.subcategory2 = "";
    producto.subcategory3 = "";
    producto.subcategory4 = "";
    producto.attributes = [];
    emit("close");
};

const submitProduct = () => {
    emit("submit", { ...producto });
    closeModal();
};
</script>

<template>
    <div v-if="show" class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Registrar Producto</h3>
                            <div class="mt-2">
                                <form @submit.prevent="submitProduct">
                                    <div class="mb-4 flex flex-wrap gap-4">
                                        <div class="flex-1">
                                            <label for="name"
                                                class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                                            <input type="text" id="name" v-model="producto.name"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                                        </div>

                                        <div class="flex-1">
                                            <label for="code" class="block text-gray-700 text-sm font-bold mb-2">Código
                                                del producto:</label>
                                            <input type="text" id="code" v-model="producto.code"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                                        </div>
                                    </div>

                                    <div class="mb-4 flex flex-wrap gap-4">
                                        <div class="flex-1">
                                            <label for="categoryPrincipal"
                                                class="block text-gray-700 text-sm font-bold mb-2">Categoría
                                                Principal:</label>
                                            <select id="categoryPrincipal" v-model="producto.categoryPrincipal"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                <option value="">Seleccione una opción</option>
                                                <option value="cat1">Categoría 1</option>
                                                <option value="cat2">Categoría 2</option>
                                            </select>
                                        </div>
                                        <div class="flex-1">
                                            <label for="subcategory1"
                                                class="block text-gray-700 text-sm font-bold mb-2">Categoría 1:</label>
                                            <select id="subcategory1" v-model="producto.subcategory1"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                <option value="">Seleccione una opción</option>
                                                <option value="sub1">Subcategoría 1</option>
                                                <option value="sub2">Subcategoría 2</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Precio
                                            Venta:</label>
                                        <input type="number" id="price" v-model="producto.price"
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                                    </div>

                                    <div class="flex items-center justify-end mt-4">
                                        <button type="submit"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2">
                                            Registrar
                                        </button>
                                        <button type="button" @click="closeModal"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
