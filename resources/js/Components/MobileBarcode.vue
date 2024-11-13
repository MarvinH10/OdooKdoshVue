<script setup>
import { defineProps, defineEmits } from "vue";

const props = defineProps({
    selectedButtonIndex: Number,
    selectedItem: Object,
    toggleSelection: Function,
    imageItems: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['print-selected', 'open-cantidades-modal']);
</script>

<template>
    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-1">
            <div class="bg-white overflow-hidden p-6">
                <div class="flex flex-wrap">
                    <div class="w-full md:w-1/4 mb-4 md:mb-0">
                        <button
                            class="flex flex-col bg-blue-700 text-white text-sm px-[15.5px] py-[0.5px] mb-1 rounded border text-center">
                            Seleccionar
                        </button>
                        <button @click="emit('print-selected')"
                            class="flex flex-col bg-blue-700 text-white text-sm px-[25.5px] py-[0.5px] mb-5 rounded border text-center">
                            Imprimir
                        </button>
                        <button @click="emit('open-cantidades-modal')"
                            class="flex flex-col bg-blue-700 text-white text-sm px-[15.5px] py-[0.5px] rounded border text-center">
                            Cantidades
                        </button>
                    </div>

                    <div class="m-2 w-full md:w-1/2">
                        <div class="flex flex-wrap gap-2 justify-center">
                            <button v-for="(item, index) in imageItems" :key="index" @click="toggleSelection(index)"
                                :class="['border border-dashed w-32 h-30 p-2 rounded',
                                    selectedButtonIndex === index ? 'bg-[#8d99ae] border-gray-700' : 'hover:bg-[#8d99ae] hover:border-gray-700'
                                ]">
                                <div class="w-auto p-2 rounded">
                                    <img :src="item.src" class="w-40 h-30 object-cover" alt="Barcode" />
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-2xl font-bold">Etiquetas: 0</div>
                </div>
            </div>
            <div v-if="selectedItem" class="bg-[#F3F4F6] mt-4 p-4">
                <div class="text-lg font-medium text-gray-800">{{ selectedItem.content }}</div>
            </div>
        </div>
    </div>
</template>
