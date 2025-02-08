import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: () => import('@/Pages/Auth/Login.vue'),
    },
    {
        path: '/',
        name: 'Welcome',
        component: () => import('@/Pages/Welcome.vue'),
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: () => import('@/Pages/Dashboard.vue'),
    },
    {
        path: '/productos',
        name: 'Productos',
        component: () => import('@/Pages/Productos/Productos.vue'),
    },
    {
        path: '/formulas',
        name: 'Formulas',
        component: () => import('@/Pages/Formula/Formula.vue'),
    },
    {
        path: '/reposicion',
        name: 'Reposicion',
        component: () => import('@/Pages/Reposicion/Reposicion.vue'),
    },
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

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(router)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
