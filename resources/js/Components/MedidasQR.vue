<script setup>
import LogoKdosh from "@/Components/LogoKdosh.vue";
import { defineProps, computed } from "vue";

const props = defineProps({
    selectedItem: Object,
    qrCodeDataUrl: String,
    selectedButtonIndex: Number,
});

const styles = [
    { width: 'w-60', height: 'h-60', priceTextSize: 'text-3xl', descriptionTextSize: 'text-xs', marginLeft: 'ml-6', priceFontSize: '24px', qrCodeSize: 100 },
    { width: 'w-48', height: 'h-24', priceTextSize: 'text-xl', descriptionTextSize: 'text-xs', marginLeft: 'ml-4', priceFontSize: '10px', qrCodeSize: 55 },
    { width: 'w-48', height: 'h-24', priceTextSize: 'text-xs', marginLeft: 'ml-4', qrCodeSize: 50 },
    { width: 'w-[400px]', height: 'h-[400px]', priceTextSize: 'text-4xl', descriptionTextSize: 'text-sm', marginLeft: 'ml-5', priceFontSize: '22px', qrCodeSize: 120 },
    { width: 'w-52', height: 'h-60', priceTextSize: 'text-3xl', descriptionTextSize: 'text-xs', marginLeft: 'ml-6', priceFontSize: '24px', qrCodeSize: 100 },
    { width: 'w-64', height: 'h-20', priceTextSize: 'text-xl', descriptionTextSize: 'text-xs', marginLeft: 'ml-4', priceFontSize: '10px', qrCodeSize: 55 },
    { width: 'w-60', height: 'h-60', priceTextSize: 'text-3xl', descriptionTextSize: 'text-[7px]', marginLeft: 'ml-6', priceFontSize: '24px', qrCodeSize: 100 },
];

const qrStyles = computed(() => {
    return styles[props.selectedButtonIndex] || styles[0];
});
</script>

<template>
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" :class="qrStyles.marginLeft">
        <!-- Button 1 -->
        <div v-if="selectedButtonIndex === 0 && selectedItem"
            :class="`bg-white p-1 ${qrStyles.width} ${qrStyles.height} text-center relative`">
            <div :class="`${qrStyles.priceTextSize} font-bold mb-2`">S/ {{ selectedItem.price }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black mb-4`">CABALLERO / ZAPATILLA / ADIDAS</div>
            <div class="flex items-center justify-center relative">
                <div
                    class="absolute left-12 top-1/2 transform -translate-y-1/2 -translate-x-full text-sm font-semibold -rotate-90">
                    {{ selectedItem.attribute }}</div>
                <img :src="qrCodeDataUrl" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
            </div>
            <div class="text-xs font-mono mt-2">{{ selectedItem.code }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black`">{{ selectedItem.description }}</div>
        </div>

        <!-- Button 2 -->
        <div v-else-if="selectedButtonIndex === 1 && selectedItem"
            :class="`bg-white ${qrStyles.width} ${qrStyles.height} text-center relative`">
            <div :class="`${qrStyles.priceTextSize} font-bold mr-20`">S/ {{ selectedItem.price }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black text-[7.8px] ml-1 mr-[62px] leading-tight mb-1`">
                CABALLERO / ZAPATILLA / ADIDAS
            </div>
            <div :class="`${qrStyles.descriptionTextSize} text-black text-[7.8px] ml-1 mr-[72px] leading-tight`">{{
                selectedItem.description }}</div>
            <div class="text-black text-[7.8px] ml-1 mr-[72px] leading-tight">
                {{ selectedItem.attribute }}
            </div>
            <div class="flex items-center left-[60px] -top-[67px] justify-center relative">
                <img :src="qrCodeDataUrl" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
                <div
                    class="text-black text-[8.5px] absolute left-[127px] top-[65px] transform -translate-y-1/2 -translate-x-full">
                    {{ selectedItem.code }}</div>
            </div>
        </div>

        <!-- Button 3 -->
        <div v-else-if="selectedButtonIndex === 2 && selectedItem"
            :class="`bg-white ${qrStyles.width} ${qrStyles.height} text-center relative`">
            <div class="flex items-center left-[-60px] -top-[-19px] justify-center relative">
                <div :class="`${qrStyles.priceTextSize} absolute left-[68px] top-[-15px]`">S/ {{ selectedItem.price }}
                </div>
                <img :src="qrCodeDataUrl" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
                <div
                    class="text-black text-[7.5px] absolute left-[124px] top-[60px] transform -translate-y-1/2 -translate-x-full">
                    {{ selectedItem.code }}</div>
            </div>
        </div>

        <!-- Button 4 -->
        <div v-else-if="selectedButtonIndex === 3 && selectedItem"
            :class="`bg-white p-1 ${qrStyles.width} ${qrStyles.height} text-center relative`">
            <LogoKdosh class="justify-center flex" />
            <div :class="`${qrStyles.priceTextSize} mb-2`">S/ {{ selectedItem.price }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black`">{{ selectedItem.description }}</div>
            <div class="flex items-center justify-center relative">
                <div
                    class="absolute left-[129px] top-[40px] transform -translate-y-1/2 -translate-x-full text-sm font-semibold -rotate-90">
                    CABALLERO / ZAPATILLA / ADIDAS</div>
                <img :src="qrCodeDataUrl" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
            </div>
            <div class="text-xs font-mono mt-2">{{ selectedItem.code }}</div>
            <div class="text-[15px] font-semibold mt-[65px]">
                MCMLXXXIX&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MCMLXXXIX&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MCMLXXXIX
            </div>
        </div>

        <!-- Button 5 -->
        <div v-else-if="selectedButtonIndex === 4 && selectedItem"
            :class="`bg-white p-1 ${qrStyles.width} ${qrStyles.height} text-center relative`">
            <div :class="`${qrStyles.priceTextSize} font-bold mb-1`">S/ {{ selectedItem.price }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black mb-1`">CABALLERO / ZAPATILLA / ADIDAS</div>
            <div class="flex items-center justify-center relative">
                <div
                    class="absolute left-12 top-1/2 transform -translate-y-1/2 -translate-x-full text-sm font-semibold -rotate-90">
                    {{ selectedItem.attribute }}</div>
                <img :src="qrCodeDataUrl" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
            </div>
            <div class="text-xs font-mono mt-1">{{ selectedItem.code }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black`">{{ selectedItem.description }}</div>
        </div>

        <!-- Button 6 -->
        <div v-else-if="selectedButtonIndex === 5 && selectedItem"
            :class="`bg-white ${qrStyles.width} ${qrStyles.height} text-center relative`">
            <div :class="`${qrStyles.priceTextSize} font-bold mr-20`">S/ {{ selectedItem.price }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black text-[7.8px] ml-1 mr-[62px] leading-tight mb-2`">
                CABALLERO / ZAPATILLA / ADIDAS
            </div>
            <div :class="`${qrStyles.descriptionTextSize} text-black text-[7.8px] mr-[72px] leading-tight`">{{
                selectedItem.description }}</div>
            <div class="text-black text-[7.8px] ml-1 mr-[72px] leading-tight">
                {{ selectedItem.attribute }}
            </div>
            <div class="flex items-center left-[80px] -top-[59px] justify-center relative">
                <img :src="qrCodeDataUrl" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
                <div
                    class="text-black text-[8.5px] absolute left-[157px] top-[65px] transform -translate-y-1/2 -translate-x-full">
                    {{ selectedItem.code }}</div>
            </div>
        </div>

        <!-- Button 7 -->
        <div v-else-if="selectedButtonIndex === 6 && selectedItem"
            :class="`bg-white p-1 ${qrStyles.width} ${qrStyles.height} text-center relative`"
            :style="{ borderRadius: '50%' }">
            <div :class="`${qrStyles.priceTextSize} font-bold mt-3 mb-1`">S/ {{ selectedItem.price }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black mb-2`">CABALLERO / ZAPATILLA / ADIDAS</div>
            <div class="flex items-center justify-center relative">
                <div
                    class="absolute left-12 top-1/2 transform -translate-y-1/2 -translate-x-full text-sm font-semibold -rotate-90">
                    {{ selectedItem.attribute }}</div>
                <img :src="qrCodeDataUrl" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
            </div>
            <div class="text-xs font-mono mt-1">{{ selectedItem.code }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black`">{{ selectedItem.description }}</div>
        </div>
    </div>
</template>