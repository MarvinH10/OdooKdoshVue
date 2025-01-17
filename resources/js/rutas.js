import { createRouter, createWebHistory } from 'vue-router';
import Barcode from '@/Pages/Barcode/Barcode.vue';

const routes = [
    {
        path: '/barcode',
        name: 'Barcode',
        component: Barcode,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
