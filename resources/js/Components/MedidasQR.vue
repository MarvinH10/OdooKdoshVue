<script>
import LogoKdosh from "@/Components/LogoKdosh.vue";
import { styles } from "@/stylesConfig";

export default {
    name: "MedidasQR",
    components: { LogoKdosh },
    props: {
        filteredItems: {
            type: Array,
            required: true,
        },
        selectedButtonIndex: {
            type: [Number, null],
            required: false,
            default: null,
        },
    },
    computed: {
        qrStyles() {
            return styles[this.selectedButtonIndex] || styles[0];
        },
    },
};
</script>

<template>
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" :class="qrStyles.marginLeft">
        <!-- Button 1 -->
        <div v-for="(item) in filteredItems" v-if="selectedButtonIndex === 0 && filteredItems"
            :class="`bg-white p-1 ${qrStyles.width} ${qrStyles.height} text-center relative`">
            <div :class="`${qrStyles.priceTextSize} font-bold mb-2`">S/ {{ item.price }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black mb-4`">{{ item.categ_id }}</div>
            <div class="flex items-center justify-center relative">
                <div
                    class="absolute left-12 top-1/2 transform -translate-y-1/2 -translate-x-full text-sm font-semibold -rotate-90">
                    {{ item.attribute }}</div>
                <img :src="item.qrCode" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
            </div>
            <div class="text-xs font-mono mt-2">{{ item.code }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black`">{{ item.description }}</div>
        </div>

        <!-- Button 2 -->
        <div v-for="(item) in filteredItems" v-else-if="selectedButtonIndex === 1 && filteredItems"
            :class="`bg-white ${qrStyles.width} ${qrStyles.height} text-center relative`">
            <div :class="`${qrStyles.priceTextSize} font-bold mr-20 mt-3`">S/ {{ item.price }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black ml-1 mr-[62px] leading-tight mb-1`">
                {{ item.categ_id }}
            </div>
            <div :class="`${qrStyles.descriptionTextSize} text-black ml-1 mr-[72px] leading-tight`">{{
                item.description }}</div>
            <div class="text-black text-[7.8px] ml-1 mr-[72px] leading-tight">
                {{ item.attribute }}
            </div>
            <div class="flex items-center left-[60px] -top-[80px] justify-center relative">
                <img :src="item.qrCode" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
                <div
                    class="text-black text-[8.5px] absolute left-[127px] top-[65px] transform -translate-y-1/2 -translate-x-full">
                    {{ item.code }}</div>
            </div>
        </div>

        <!-- Button 3 -->
        <div v-for="(item) in filteredItems" v-else-if="selectedButtonIndex === 2 && filteredItems"
            :class="`bg-white ${qrStyles.width} ${qrStyles.height} text-center relative`">
            <div class="flex items-center left-[-60px] -top-[-19px] justify-center relative">
                <div :class="`${qrStyles.priceTextSize} absolute left-[68px] top-[-15px]`">S/ {{ item.price }}
                </div>
                <img :src="item.qrCode" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
                <div
                    class="text-black text-[7.5px] absolute left-[124px] top-[60px] transform -translate-y-1/2 -translate-x-full">
                    {{ item.code }}</div>
            </div>
        </div>

        <!-- Button 4 -->
        <div v-for="(item) in filteredItems" v-else-if="selectedButtonIndex === 3 && filteredItems"
            :class="`bg-white p-1 ${qrStyles.width} ${qrStyles.height} text-center relative`">
            <LogoKdosh class="justify-center flex" />
            <div :class="`${qrStyles.priceTextSize} mb-2`">S/ {{ item.price }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black`">{{ item.description }}</div>
            <div class="flex items-center justify-center relative">
                <div
                    class="absolute left-[129px] top-[40px] transform -translate-y-1/2 -translate-x-full text-sm font-semibold -rotate-90">
                    {{ item.categ_id }}</div>
                <img :src="item.qrCode" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
            </div>
            <div class="text-xs font-mono mt-2">{{ item.code }}</div>
            <div class="text-[15px] font-semibold mt-[65px]">
                MCMLXXXIX&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MCMLXXXIX&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MCMLXXXIX
            </div>
        </div>

        <!-- Button 5 -->
        <div v-for="(item) in filteredItems" v-else-if="selectedButtonIndex === 4 && filteredItems"
            :class="`bg-white p-1 ${qrStyles.width} ${qrStyles.height} text-center relative`">
            <div :class="`${qrStyles.priceTextSize} font-bold mb-1`">S/ {{ item.price }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black mb-1`">{{ item.categ_id }}</div>
            <div class="flex items-center justify-center relative">
                <div
                    class="absolute left-12 top-1/2 transform -translate-y-1/2 -translate-x-full text-sm font-semibold -rotate-90">
                    {{ item.attribute }}</div>
                <img :src="item.qrCode" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
            </div>
            <div class="text-xs font-mono mt-1">{{ item.code }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black`">{{ item.description }}</div>
        </div>

        <!-- Button 6 -->
        <div v-for="(item) in filteredItems" v-else-if="selectedButtonIndex === 5 && filteredItems"
            :class="`bg-white ${qrStyles.width} ${qrStyles.height} text-center relative`">
            <div :class="`${qrStyles.priceTextSize} font-bold mr-20`">S/ {{ item.price }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black ml-1 mr-[62px] leading-tight mb-2`">
                {{ item.categ_id }}
            </div>
            <div :class="`${qrStyles.descriptionTextSize} text-black mr-[72px] leading-tight`">{{
                item.description }}</div>
            <div class="text-black text-[7.8px] ml-1 mr-[72px] leading-tight">
                {{ item.attribute }}
            </div>
            <div class="flex items-center left-[80px] -top-[72px] justify-center relative">
                <img :src="item.qrCode" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
                <div
                    class="text-black text-[8.5px] absolute left-[146px] top-[65px] transform -translate-y-1/2 -translate-x-full">
                    {{ item.code }}</div>
            </div>
        </div>

        <!-- Button 7 -->
        <div v-for="(item) in filteredItems" v-else-if="selectedButtonIndex === 6 && filteredItems"
            :class="`bg-white p-1 ${qrStyles.width} ${qrStyles.height} text-center relative`"
            :style="{ borderRadius: '50%' }">
            <div :class="`${qrStyles.priceTextSize} font-bold mt-3 mb-1`">S/ {{ item.price }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black mb-2`">{{ item.categ_id }}</div>
            <div class="flex items-center justify-center relative">
                <div
                    class="absolute left-12 top-1/2 transform -translate-y-1/2 -translate-x-full text-sm font-semibold -rotate-90">
                    {{ item.attribute }}</div>
                <img :src="item.qrCode" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
            </div>
            <div class="text-xs font-mono mt-1">{{ item.code }}</div>
            <div :class="`${qrStyles.descriptionTextSize} text-black`">{{ item.description }}</div>
        </div>
    </div>
</template>
