<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Bienvenido a Kdosh Store Huánuco" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
        >
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header
                    class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3"
                >
                    <div class="flex lg:justify-center lg:col-start-2">
                        <img
                            src="../../../public/images/blancokdosh.png"
                            alt="logo"
                            style="width: 150px; height: 100px"
                        />
                    </div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Ingresar
                            </Link>
                        </template>
                    </nav>
                </header>

                <main class="mt-6">
                    <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                        <!-- Card 1: Kdosh Store Overview -->
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg"
                        >
                            <h3 class="text-xl font-bold mb-2 dark:text-white">
                                Sobre Kdosh Store Huánuco
                            </h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                Kdosh Store Huánuco es mucho más que una tienda
                                de productos: es un emblema de calidad, atención
                                al cliente y compromiso con la satisfacción de
                                cada uno de nuestros compradores. Situada en el
                                corazón de Huánuco, nuestra tienda se
                                especializa en ofrecer una gama de productos
                                cuidadosamente seleccionados para cumplir con
                                las necesidades del público más exigente.
                            </p>
                        </div>

                        <!-- Card 2: Productos Diversos -->
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg"
                        >
                            <h3 class="text-xl font-bold mb-2 dark:text-white">
                                Amplia Variedad de Productos
                            </h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                En Kdosh Store, contamos con un extenso catálogo
                                de productos que abarca desde tecnología hasta
                                ropa y accesorios. Nos aseguramos de mantener
                                nuestros productos actualizados, brindando a
                                nuestros clientes las últimas tendencias y la
                                tecnología más avanzada. Cada artículo en
                                nuestras vitrinas ha sido elegido para
                                garantizar su calidad y durabilidad.
                            </p>
                        </div>

                        <!-- Card 3: Servicio al Cliente -->
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg"
                        >
                            <h3 class="text-xl font-bold mb-2 dark:text-white">
                                Excelencia en Servicio al Cliente
                            </h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                Uno de nuestros mayores orgullos es el servicio
                                al cliente que brindamos. En Kdosh Store,
                                creemos que cada cliente merece una atención
                                personalizada y una experiencia de compra
                                placentera. Nuestro equipo está comprometido con
                                resolver dudas, proporcionar asesoría y
                                garantizar que cada compra sea exactamente lo
                                que necesitas.
                            </p>
                        </div>

                        <!-- Card 4: Innovación y Futuro -->
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg"
                        >
                            <h3 class="text-xl font-bold mb-2 dark:text-white">
                                Innovación y Futuro
                            </h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                Kdosh Store no solo se adapta al presente, sino
                                que también mira hacia el futuro. Nuestro
                                enfoque en la innovación nos permite traer
                                productos que revolucionan el mercado y mejoran
                                la vida de nuestros clientes. Ya sea con nuevas
                                tecnologías o mejores prácticas de atención,
                                siempre estamos en la vanguardia del cambio
                                positivo.
                            </p>
                        </div>
                    </div>
                </main>

                <footer
                    class="py-16 text-center text-sm text-black dark:text-white/70"
                >
                    Kdosh Store Huánuco — Comprometidos con tu satisfacción
                </footer>
            </div>
        </div>
    </div>
</template>
