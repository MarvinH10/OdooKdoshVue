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
        groupedItems() {
            const groups = [];
            for (let i = 0; i < this.filteredItems.length; i += 3) {
                groups.push(this.filteredItems.slice(i, i + 3));
            }
            return groups;
        },
    },
};
</script>

<template>
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-6 ml-[30px]"
        :class="qrStyles.marginLeft">
        <!-- Button 1 -->
        <div v-for="(item) in filteredItems" v-if="selectedButtonIndex === 0 && filteredItems"
            class="bg-white p-1 w-60 h-60 text-center relative">
            <div class="text-4xl font-bold mb-2">S/ {{ item.price }}</div>
            <div class="text-xs text-black mb-4">{{ item.categ_id }}</div>
            <div class="flex items-center justify-center relative">
                <div
                    class="absolute left-[96px] top-1/2 transform -translate-y-1/2 -translate-x-full text-xs font-semibold -rotate-90 w-[90px]">
                    {{ item.attribute }}</div>
                <img :src="item.qrCode" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
                <div
                    class="absolute left-[230px] top-1/2 transform -translate-y-1/2 -translate-x-full text-xs font-semibold -rotate-90 w-[100px]">
                    {{ item.default_code }}</div>
            </div>
            <div class="text-xs font-mono mt-2">{{ item.code }}</div>
            <div class="text-xs text-black">{{ item.description }}</div>
        </div>

        <!-- Button 2 -->
        <div v-for="(item) in filteredItems" v-else-if="selectedButtonIndex === 1 && filteredItems"
            class="bg-white w-[12.5rem] h-[5.5rem] text-center relative">
            <div class="text-xl font-bold mt-[1px] mr-20">S/ {{ item.price }}</div>
            <div class="text-black text-[0.5rem] ml-1 mr-[80px] leading-tight">
                {{ item.categ_id }}
            </div>
            <div class="text-black text-[0.45rem] ml-1 mr-[80px] leading-tight">
                {{ item.default_code || "\u00A0" }}
            </div>
            <div class="text-black text-[0.45rem] ml-1 mr-[80px] leading-tight">{{
                item.description }}</div>
            <div class="text-black text-[0.45rem] ml-1 mr-[80px] leading-tight">
                {{ item.attribute || "\u00A0" }}
            </div>
            <div class="absolute right-[10px] top-1/2 transform -translate-y-1/2 flex flex-col items-center">
                <img :src="item.qrCode" alt="QR Code" class="w-[50px] h-[50px] mb-1" />

                <div class="text-black text-[8.5px] text-center">
                    {{ item.code }}
                </div>
            </div>
        </div>

        <!-- Button 3 -->
        <div v-for="(group, groupIndex) in groupedItems" v-else-if="selectedButtonIndex === 2" :key="groupIndex"
            class="bg-white w-[12.5rem] h-[5.5rem] text-center relative">
            <div class="flex justify-around items-center h-full">
                <div v-for="(item, index) in group" :key="index" class="relative">
                    <div class="text-xs mb-1">S/ {{ item.price }}</div>
                    <img :src="item.qrCode" alt="QR Code" class="mx-2"
                        :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
                    <div class="text-black text-[7.5px]">{{ item.code }}</div>
                </div>
            </div>
        </div>

        <!-- Button 4 -->
        <div v-for="(item) in filteredItems" v-else-if="selectedButtonIndex === 3 && filteredItems"
            class="bg-white p-1 w-[400px] h-[400px] text-center relative">
            <LogoKdosh class="justify-center flex" />
            <div class="text-4xl mb-2">S/ {{ item.price }}</div>
            <div class="text-sm text-black">{{ item.description }}</div>
            <div class="flex items-center justify-center relative">
                <div
                    class="absolute left-[167px] top-1/2 transform -translate-y-1/2 -translate-x-full text-xs font-semibold -rotate-90 w-[300px]">
                    {{ item.categ_id }}</div>
                <img :src="item.qrCode" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
                <div
                    class="absolute left-[430px] top-1/2 transform -translate-y-1/2 -translate-x-full text-xs font-semibold -rotate-90 w-[100px]">
                    {{ item.default_code }}</div>
            </div>
            <div class="text-xs font-mono mt-2">{{ item.code }}</div>
            <div class="text-[17px] font-semibold mt-[65px]">
                MCMLXXXIX&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MCMLXXXIX&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MCMLXXXIX
            </div>
        </div>

        <!-- Button 5 -->
        <div v-for="(item) in filteredItems" v-else-if="selectedButtonIndex === 4 && filteredItems"
            class="bg-white p-1 w-[13rem] h-[14rem] text-center relative">
            <div class="text-3xl font-bold mb-1">S/ {{ item.price }}</div>
            <div class="text-xs text-black mb-1">{{ item.categ_id }}</div>
            <div class="flex items-center justify-center relative">
                <div
                    class="absolute left-[80px] top-1/2 transform -translate-y-1/2 -translate-x-full text-xs font-semibold -rotate-90 w-[90px]">
                    {{ item.attribute }}</div>
                <img :src="item.qrCode" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
                <div
                    class="absolute left-[215px] top-1/2 transform -translate-y-1/2 -translate-x-full text-xs font-semibold -rotate-90 w-[100px]">
                    {{ item.default_code }}</div>
            </div>
            <div class="text-xs font-mono mt-1">{{ item.code }}</div>
            <div class="text-xs text-black">{{ item.description }}</div>
        </div>

        <!-- Button 6 -->
        <div v-for="(item) in filteredItems" v-else-if="selectedButtonIndex === 5 && filteredItems"
            class="bg-white w-[230px] h-20 text-center relative">
            <div class="text-xl font-bold mr-20 mt-[1px]">S/ {{ item.price }}</div>
            <div class="text-[0.5rem] text-black ml-1 mr-[62px] leading-tight">
                {{ item.categ_id }}
            </div>
            <div class="text-[0.45rem] text-black mr-[72px] leading-tight">
                {{ item.default_code || "\u00A0" }}
            </div>
            <div class="text-[0.45rem] text-black mr-[72px] leading-tight">{{
                item.description }}</div>
            <div class="text-black text-[0.45rem] ml-1 mr-[72px] leading-tight">
                {{ item.attribute || "\u00A0" }}
            </div>
            <div class="absolute right-[5px] top-1/2 transform -translate-y-1/2 flex flex-col items-center">
                <img :src="item.qrCode" alt="QR Code" class="w-[50px] h-[50px] mb-1" />

                <div class="text-black text-[8.5px] text-center">
                    {{ item.code }}
                </div>
            </div>
        </div>

        <!-- Button 7 -->
        <div v-for="(item) in filteredItems" v-else-if="selectedButtonIndex === 6 && filteredItems"
            class="bg-white p-1 w-60 h-60 text-center relative" :style="{ borderRadius: '50%' }">
            <div class="text-3xl font-bold mt-[0.95rem]">S/ {{ item.price }}</div>
            <div class="text-[0.60rem] text-black mb-2">{{ item.categ_id }}</div>
            <div class="flex items-center justify-center relative">
                <div
                    class="absolute left-[96px] top-1/2 transform -translate-y-1/2 -translate-x-full text-xs font-semibold -rotate-90 w-[90px]">
                    {{ item.attribute }}</div>
                <img :src="item.qrCode" alt="QR Code" class="mx-4"
                    :style="{ width: qrStyles.qrCodeSize + 'px', height: qrStyles.qrCodeSize + 'px' }" />
                <div
                    class="absolute left-[230px] top-1/2 transform -translate-y-1/2 -translate-x-full text-xs font-semibold -rotate-90 w-[100px]">
                    {{ item.default_code }}</div>
            </div>
            <div class="text-xs font-mono mt-1">{{ item.code }}</div>
            <div class="flex justify-center">
                <div class="text-[8px] text-black text-center w-[90px]">
                    {{ item.description }}
                </div>
            </div>
        </div>
    </div>
</template>
