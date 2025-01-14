<script lang="ts">
import { defineComponent, PropType, computed } from "vue";

export default defineComponent({
    name: "ProductoTabla",
    props: {
        productos: {
            type: Array as PropType<Array<{ id: number; name: string; code: string; price: number }>>,
            required: true,
        },
    },
    emits: ["agregar", "registrar"],
    setup(props, { emit }) {
        const tieneProductos = computed(() => {
            // console.log("Productos recibidos:", props.productos);
            return props.productos.length > 0;
        });

        const handleAgregar = () => {
            emit("agregar");
        };

        const handleRegistrar = () => {
            emit("registrar");
        };

        return {
            tieneProductos,
            handleAgregar,
            handleRegistrar,
        };
    },
});
</script>

<template>
    <div>
        <div class="flex justify-between items-center mb-4">
            <button @click="handleAgregar"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-circle-plus"></i> Agregar
            </button>

            <button @click="handleRegistrar"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-save"></i> Crear Productos en Odoo
            </button>
        </div>

        <div class="overflow-y-auto max-h-[36rem] border border-black rounded">
            <table v-if="tieneProductos" class="min-w-full table-auto border-collapse">
                <thead class="bg-black sticky top-[-1px] text-white z-10">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">Nombre</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Código</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Precio</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="producto in productos" :key="producto.id" class="hover:bg-gray-100">
                        <td class="border border-gray-300 px-4 py-2">{{ producto.name }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ producto.code }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ producto.price }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <button class="bg-blue-500 text-white px-2 py-1 rounded">Editar</button>
                            <button class="bg-red-500 text-white px-2 py-1 rounded ml-2">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="text-center text-gray-500 py-8">
                No hay productos por mostrar
            </div>
        </div>
    </div>
</template>
