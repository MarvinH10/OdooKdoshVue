<script lang="ts">
import { defineAsyncComponent, defineComponent, ref, computed, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const AppLayout = defineAsyncComponent(() => import("@/Layouts/AppLayout.vue"));

export default defineComponent({
    components: { AppLayout },
    props: {
        destinos: {
            type: Array as () => { id: number; name: string }[],
            required: true
        },
        digitadores: {
            type: Array as () => { id: number; name: string }[],
            required: true
        }
    },
    setup(props) {
        const route = useRoute();
        const orderId = ref<number | undefined>(undefined);
        const selectedDestino = ref(props.destinos.length > 0 ? props.destinos[0].id : "");
        const selectedDigitador = ref(props.digitadores.length > 0 ? props.digitadores[0].id : "");
        const selectedDigitadorData = computed(() =>
            props.digitadores.find(d => d.id === selectedDigitador.value) || { id: 0, name: "" }
        );
        const selectedDestinoData = computed(() =>
            props.destinos.find(d => d.id === selectedDestino.value) || { id: 0, name: "" }
        );
        const report = ref<any>({
            id: null,
            order_name: "",
            date_order: "",
            supplier: "",
            partner_ref: "",
        });
        const items = ref<any[]>([]);
        const isLoading = ref(false);
        const itemsPerPage = 28;

        const paginatedItems = computed(() =>
            Array.from({ length: Math.ceil(items.value.length / itemsPerPage) }, (_, i) =>
                items.value.slice(i * itemsPerPage, (i + 1) * itemsPerPage)
            )
        );

        const totalQuantity = computed(() =>
            items.value.reduce((sum, item) => sum + Number(item.count || 0), 0)
        );

        const fetchData = async () => {
            if (!orderId.value) return;
            isLoading.value = true;
            try {
                const response = await fetch(`/reporte/traer/${orderId.value}`);
                if (!response.ok) throw new Error("Error al obtener datos");

                const data = await response.json();
                if (!data || !data.products?.length) {
                    throw new Error("No hay reportes con ese ID.");
                }

                report.value = {
                    id: data.order_id,
                    order_name: data.order_name,
                    date_order: data.date_order,
                    supplier: data.supplier,
                    partner_ref: data.partner_ref,
                };

                items.value = data.products.map((p: any) => ({
                    id: p.id,
                    description: p.name,
                    brand: p.brand,
                    count: p.product_qty,
                }));
            } catch (error) {
                console.error("Error al cargar los datos:", error);
                toast.error(error.message || "No se pudo obtener información del reporte.", {
                    autoClose: 3000,
                    position: "bottom-right",
                });
                report.value = {
                    id: null,
                    order_name: "",
                    date_order: "",
                    supplier: "",
                    partner_ref: "",
                };
                items.value = [];
            } finally {
                isLoading.value = false;
            }
        };

        onMounted(() => {
            const id = route.query.order_id ? Number(route.query.order_id) : null;

            if (id === null && !sessionStorage.getItem("reloaded")) {
                sessionStorage.setItem("reloaded", "true");
                location.reload();
                return;
            }

            sessionStorage.removeItem("reloaded");

            if (!isNaN(id)) {
                orderId.value = id;
                fetchData();
            }
        });

        watch(() => route.query.order_id, (newOrderId) => {
            const id = Number(newOrderId);
            if (!isNaN(id)) {
                orderId.value = id;
                fetchData();
            }
        }, { immediate: true });

        return {
            report,
            items,
            isLoading,
            paginatedItems,
            totalQuantity,
            selectedDestino,
            selectedDigitador,
            selectedDigitadorData,
            selectedDestinoData
        };
    },
});
</script>

<template>
    <AppLayout title="Reporte">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Reporte
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 mb-4">
                <div class="p-6 overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="mb-4 flex items-center gap-2">
                        <label class="font-semibold">DESTINO:</label>
                        <select v-model="selectedDestino"
                            class="w-auto px-6 py-1 border-none bg-transparent focus:outline-none appearance-none">
                            <option v-for="destino in destinos" :key="destino.id" :value="destino.id">
                                {{ destino.name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="font-semibold">DIGITADOR:</label>
                        <select v-model="selectedDigitador"
                            class="w-auto px-6 py-1 border-none bg-transparent focus:outline-none appearance-none">
                            <option v-for="digitador in digitadores" :key="digitador.id" :value="digitador.id">
                                {{ digitador.name }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div v-if="isLoading" class="text-center my-4 mt-6">
                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando reporte...
            </div>
            <div v-else-if="items.length === 0" class="text-center my-4 mt-6 text-red-500">
                No se encontraron reportes con el ID proporcionado.
            </div>
            <div v-else class="print-area mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-for="(page, pageIndex) in paginatedItems" :key="pageIndex" class="p-6 bg-white page-break">
                    <div class="border-b border-black border-dashed mb-2">
                        <h2 class="text-lg font-bold mb-4">
                            <span>{{ report.order_name }}</span> | <span>{{ report.date_order }}</span>
                        </h2>
                        <div v-if="pageIndex === 0">
                            <div class="uppercase mb-4 text-sm">
                                <div class="w-1/2 inline-block pr-2">
                                    <table class="w-full">
                                        <tbody>
                                            <tr>
                                                <td class="text-right font-semibold pr-1">Digitador: </td>
                                                <td>{{ selectedDigitadorData.name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-right font-semibold pr-1">Almacenero(s):</td>
                                                <td class="border-b border-black"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right font-semibold pr-1">Inicio: </td>
                                                <td>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right font-semibold pr-1">Fin: </td>
                                                <td>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="w-1/2 inline-block pl-2">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td class="w-[150px] text-right font-semibold pr-1">Proveedor:</td>
                                                <td>{{ report.supplier }}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-[150px] text-right font-semibold pr-1">Factura:</td>
                                                <td>{{ report.partner_ref }}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-[150px] text-right font-semibold pr-1">Cantidad Total:</td>
                                                <td>{{ totalQuantity }}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-[150px] text-right font-semibold pr-1">Destino:</td>
                                                <td>{{ selectedDestinoData.name }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="w-full border border-collapse border-gray-400 text-left text-[12px]">
                        <thead>
                            <tr class="bg-white">
                                <th class="border border-gray-600 px-2 py-1">DESCRIPCIÓN</th>
                                <th class="border border-gray-600 px-2 py-1">MARCA</th>
                                <th class="border border-gray-600 px-2 py-1">CNT</th>
                                <th class="border border-gray-600 px-2 py-1">ALM</th>
                                <th class="border border-gray-600 px-2 py-1">VAL</th>
                                <th class="border border-gray-600 px-2 py-1">OBS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in page" :key="index" class="odd:bg-gray-200">
                                <td class="border border-gray-600 px-2 py-1">{{ item.description }}</td>
                                <td class="border border-gray-600 px-2 py-1">{{ item.brand }}</td>
                                <td class="border border-gray-600 px-2 py-1">{{ item.count }}</td>
                                <td class="border border-gray-600 px-2 py-1"></td>
                                <td class="border border-gray-600 px-2 py-1"></td>
                                <td class="border border-gray-600 px-2 py-1"></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="text-center mt-4 font-semibold text-[10px]">
                        Página {{ pageIndex + 1 }} de {{ paginatedItems.length }}
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
select {
    background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="black"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>');
    background-repeat: no-repeat;
    background-position: right 6px center;
    background-size: 16px;
    padding-right: 24px;
}

select:focus {
    outline: none !important;
    box-shadow: none !important;
}

@media print {
    body * {
        visibility: hidden;
    }

    .print-area,
    .print-area * {
        visibility: visible;
    }

    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    html,
    body {
        background: white !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .page-break {
        page-break-before: always;
    }

    .bg-white {
        background-color: white !important;
    }

    .bg-gray-200 {
        background-color: #E5E7EB !important;
    }
}
</style>
