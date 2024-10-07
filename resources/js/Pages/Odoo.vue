<script setup>
import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3';
import axios from 'axios'

import AppLayout from '@/Layouts/AppLayout.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    partners: {
        type: Array,
        default: [],
    }
});

const form = useForm({
    name: '',
});

const proveedores = ref(props.partners)
const cargando = ref(false)

const recargar = async () => {
    cargando.value = true
    try {
        const response = await axios.get(route('odoo.patners'))
        proveedores.value = response.data
        cargando.value = false
    } catch (error) {
        console.error('Error fetching partners:', error)
    }
};

const recargar2 = () => {
    cargando.value = true
    const posicion = window.scrollY
    let nuevo = {
        'name': form.name,
        'country_id': 'Huanuco'
    }
    form.post(route('odoo.patners'), {
        onSuccess: () => {
            form.reset('name')
            proveedores.value.push(nuevo)
        },
        onFinish: () => {
            cargando.value = false
            window.scrollTo(0, posicion)
        }
    });
};

onMounted(() => {

})
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-10">
                    <h1>Odoo Partners</h1>
                    <div v-if="cargando">
                        <span>Cargando...</span>
                    </div>
                    <div v-else>
                        <ul>
                            <li v-for="partner in proveedores">
                                {{ partner.name }} - {{ partner.country_id ? partner.country_id[1] : 'No Country' }}
                            </li>
                        </ul>
                    </div>

                    <div class="mt-2">
                        <form @submit.prevent="recargar2" class="py-2">

                            <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required
                                autocomplete="off" />
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded my-3"
                                :disabled="cargando">Recargar</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
