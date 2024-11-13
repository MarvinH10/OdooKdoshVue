<script setup>
import { defineEmits, ref } from "vue";

const emit = defineEmits(['close']);

const items = ref([
    { code: "7633188062077", description: "ADIDAS ZAPATILLAS SOLARGLIDE ST 3 - FV7251", attr: "6.5", quantity: 100 },
    { code: "3393264635932", description: "ADIDAS ZAPATILLAS SOLARGLIDE ST 3 - FV7251", attr: "7", quantity: 1 },
    { code: "6601971591111", description: "ADIDAS ZAPATILLAS SOLARGLIDE ST 3 - FV7251", attr: "8.5", quantity: 1 },
    { code: "1650985588261", description: "ADIDAS ZAPATILLAS SOLARGLIDE ST 3 - FV7251", attr: "9", quantity: 1 },
    { code: "8641100500186", description: "ADIDAS ZAPATILLAS SOLARGLIDE ST 3 - FV7251", attr: "9.5", quantity: 1 },
]);

const resetQuantities = () => {
    items.value.forEach(item => {
        item.quantity = 0;
    });
};

const closeModal = () => {
    emit('close');
};

const acceptChanges = () => {
    closeModal();
};
</script>

<template>
    <div class="modal-background">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="text-lg font-semibold">Cantidad de Productos</h2>
                <button @click="closeModal" class="text-red-500 font-bold text-lg">&times;</button>
            </div>
            <table class="w-full mt-4 text-sm">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="p-2">Código</th>
                        <th class="p-2">Descripción</th>
                        <th class="p-2">Attr</th>
                        <th class="p-2">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in items" :key="index" :class="index % 2 === 0 ? 'bg-gray-100' : ''">
                        <td class="p-2">{{ item.code }}</td>
                        <td class="p-2">{{ item.description }}</td>
                        <td class="p-2">{{ item.attr }}</td>
                        <td class="p-2">
                            <input type="number" v-model="item.quantity" class="w-full border p-1 rounded" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="modal-footer mt-4 flex justify-between items-center">
                <button @click="resetQuantities" class="btn-reset">Poner todo a cero</button>
                <div>
                    <button @click="closeModal" class="btn-cancel mr-2">Cancelar</button>
                    <button @click="acceptChanges" class="btn-accept">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.modal-background {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 16px;
}

.btn-reset,
.btn-cancel,
.btn-accept {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    border-radius: 6px;
}

.btn-reset {
    background-color: #f3f4f6;
    color: #4b5563;
}

.btn-cancel {
    background-color: #ef4444;
    color: white;
}

.btn-accept {
    background-color: #6b7280;
    color: white;
}

@media (max-width: 640px) {

    .modal-content {
        width: 95%;
        padding: 15px;
    }

    .btn-reset,
    .btn-cancel,
    .btn-accept {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>
