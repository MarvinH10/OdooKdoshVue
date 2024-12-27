<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-500 text-white py-10">
            <div class="container mx-auto text-center">
                <ApplicationLogo class="mx-auto h-16 w-auto" />
                <h1 class="mt-4 text-4xl font-bold">Bienvenido a Odoo Kdosh Store</h1>
                <p class="mt-2 text-lg">
                    Gestiona tus productos, inventarios de forma eficiente.
                </p>
            </div>
        </div>

        <div class="container mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white shadow-lg rounded-lg p-6 text-center">
                <div class="flex justify-center items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        class="w-12 h-12 text-purple-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Documentación</h2>
                <p class="text-gray-600 mt-2">
                    Aprende a usar Odoo Kdosh Store con nuestra completa guía de usuario.
                </p>
                <a href="https://laravel.com/docs" class="mt-4 inline-block text-indigo-600 font-semibold">Ver
                    documentación</a>
            </div>

            <div class="bg-white shadow-lg rounded-lg p-6 text-center">
                <div class="flex justify-center items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        class="w-12 h-12 text-purple-500">
                        <path stroke-linecap="round"
                            d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Tutoriales</h2>
                <p class="text-gray-600 mt-2">
                    Explora videos y guías paso a paso para dominar el sistema.
                </p>
                <a href="https://laracasts.com" class="mt-4 inline-block text-indigo-600 font-semibold">Ver
                    tutoriales</a>
            </div>

            <div class="bg-white shadow-lg rounded-lg p-6 text-center">
                <div class="flex justify-center items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        class="w-12 h-12 text-purple-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Diseño moderno</h2>
                <p class="text-gray-600 mt-2">
                    Personaliza tus diseños y administra fácilmente tus productos.
                </p>
            </div>

            <div class="bg-white shadow-lg rounded-lg p-6 text-center">
                <div class="flex justify-center items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        class="w-12 h-12 text-purple-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Autenticación segura</h2>
                <p class="text-gray-600 mt-2">
                    Accede a funcionalidades avanzadas con nuestro sistema seguro.
                </p>
            </div>
        </div>
    </div>
</template>
