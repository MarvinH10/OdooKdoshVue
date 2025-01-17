import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/barcode',
        name: 'Barcode',
        component: () => import('@/Pages/Barcode/Barcode.vue'),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
