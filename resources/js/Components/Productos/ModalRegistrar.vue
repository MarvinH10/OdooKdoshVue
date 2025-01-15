<script lang="ts" setup>
import { defineProps, defineEmits, reactive, ref } from "vue";
import TagSelect from "./TagSelect.vue";

const atributoOptions = ref(["value1", "value2", "value3"]);
const selectedValues = ref([]);

type Categoria = {
    id: number;
    name: string;
};

type Atributo = {
    id: number;
    name: string;
};

defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    isLoading: {
        type: Object,
        required: true,
    },
    categorias: {
        type: Array as () => Categoria[],
        required: true,
    },
    subcategorias: {
        type: Object,
        required: true,
    },
    atributos: {
        type: Array as () => Atributo[],
        required: true,
    },
});

const emit = defineEmits(["close", "submit", "cambiarCategoriaPrincipal", "cambiarSubcategoria"]);

const producto = reactive({
    name: "",
    code: "",
    price: 0,
    categoryPrincipal: null as number | null,
    subcategory1: null as number | null,
    subcategory2: null as number | null,
    subcategory3: null as number | null,
    subcategory4: null as number | null,
    atributos: [] as Array<{
        attributeId: string;
        attributeValues: string[];
        referenceInternal: string;
        extraPrice: number;
    }>,
});

const closeModal = () => {
    producto.name = "";
    producto.code = "";
    producto.price = 0;
    producto.categoryPrincipal = null;
    producto.subcategory1 = null;
    producto.subcategory2 = null;
    producto.subcategory3 = null;
    producto.subcategory4 = null;
    emit("close");
};

const onCategoryPrincipalChange = (event: Event) => {
    const selectElement = event.target as HTMLSelectElement;
    const idCategoria = Number(selectElement.value);
    producto.subcategory1 = null;
    producto.subcategory2 = null;
    producto.subcategory3 = null;
    producto.subcategory4 = null;
    emit("cambiarCategoriaPrincipal", idCategoria);
};

const onSubcategoryChange = (event: Event, nivel: number) => {
    const selectElement = event.target as HTMLSelectElement;
    const idPadre = Number(selectElement.value);

    if (nivel === 1) {
        producto.subcategory2 = null;
        producto.subcategory3 = null;
        producto.subcategory4 = null;
    } else if (nivel === 2) {
        producto.subcategory3 = null;
        producto.subcategory4 = null;
    } else if (nivel === 3) {
        producto.subcategory4 = null;
    }

    emit("cambiarSubcategoria", idPadre, nivel);
};

const anadirAtributo = () => {
    producto.atributos.push({
        attributeId: "",
        attributeValues: [],
        referenceInternal: "",
        extraPrice: 0,
    });
};

const removerAtributo = (index: number) => {
    producto.atributos.splice(index, 1);
};

const submitProduct = () => {
    emit("submit", { ...producto });
    closeModal();
};
</script>

<template>
    <div v-if="show" class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Registrar Producto</h3>
                            <div class="mt-2">
                                <form @submit.prevent="submitProduct">
                                    <div class="mb-4 flex flex-wrap gap-4">
                                        <div class="flex-1">
                                            <label for="name"
                                                class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                                            <input type="text" id="name" v-model="producto.name"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                                        </div>

                                        <div class="flex-1">
                                            <label for="code" class="block text-gray-700 text-sm font-bold mb-2">Código
                                                del producto:</label>
                                            <input type="text" id="code" v-model="producto.code"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                                        </div>
                                    </div>

                                    <div class="mb-4 flex flex-wrap gap-4">
                                        <div class="flex-1">
                                            <label for="categoryPrincipal"
                                                class="block text-gray-700 text-sm font-bold mb-2">
                                                Categoría Principal:
                                            </label>
                                            <select id="categoryPrincipal" v-model="producto.categoryPrincipal"
                                                @change="onCategoryPrincipalChange($event)" :disabled="isLoading[1]"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                <option value="">Seleccione una opción</option>
                                                <option v-for="categoria in categorias" :key="categoria.id"
                                                    :value="categoria.id">
                                                    {{ categoria.name }}
                                                </option>
                                            </select>
                                        </div>

                                        <div class="flex-1">
                                            <label for="subcategory1"
                                                class="block text-gray-700 text-sm font-bold mb-2">
                                                Categoría 1:
                                            </label>
                                            <select id="subcategory1" v-model="producto.subcategory1"
                                                @change="onSubcategoryChange($event, 1)"
                                                :disabled="isLoading[1] || isLoading[2] || isLoading[3] || isLoading[4]"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                <option value="">Seleccione una opción</option>
                                                <option v-for="subcat in subcategorias[1] || []" :key="subcat.id"
                                                    :value="subcat.id">
                                                    {{ subcat.name }}
                                                </option>
                                            </select>
                                        </div>

                                        <div class="flex-1">
                                            <label for="subcategory2"
                                                class="block text-gray-700 text-sm font-bold mb-2">
                                                Categoría 2:
                                            </label>
                                            <select id="subcategory2" v-model="producto.subcategory2"
                                                @change="onSubcategoryChange($event, 2)"
                                                :disabled="isLoading[1] || isLoading[2] || isLoading[3] || isLoading[4]"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                <option value="">Seleccione una opción</option>
                                                <option v-for="subcat in subcategorias[2] || []" :key="subcat.id"
                                                    :value="subcat.id">
                                                    {{ subcat.name }}
                                                </option>
                                            </select>
                                        </div>

                                        <div class="flex-1">
                                            <label for="subcategory3"
                                                class="block text-gray-700 text-sm font-bold mb-2">
                                                Categoría 3:
                                            </label>
                                            <select id="subcategory3" v-model="producto.subcategory3"
                                                @change="onSubcategoryChange($event, 3)"
                                                :disabled="isLoading[1] || isLoading[2] || isLoading[3] || isLoading[4]"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                <option value="">Seleccione una opción</option>
                                                <option v-for="subcat in subcategorias[3] || []" :key="subcat.id"
                                                    :value="subcat.id">
                                                    {{ subcat.name }}
                                                </option>
                                            </select>
                                        </div>

                                        <div class="flex-1">
                                            <label for="subcategory4"
                                                class="block text-gray-700 text-sm font-bold mb-2">
                                                Categoría 4:
                                            </label>
                                            <select id="subcategory4" v-model="producto.subcategory4"
                                                @change="onSubcategoryChange($event, 4)"
                                                :disabled="isLoading[1] || isLoading[2] || isLoading[3] || isLoading[4]"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                <option value="">Seleccione una opción</option>
                                                <option v-for="subcat in subcategorias[4] || []" :key="subcat.id"
                                                    :value="subcat.id">
                                                    {{ subcat.name }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Precio
                                            Venta:</label>
                                        <input type="number" id="price" v-model="producto.price"
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                                    </div>

                                    <div class="mb-4">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">Atributos del Producto
                                        </h3>
                                        <div v-for="(attribute, index) in producto.atributos" :key="index"
                                            class="flex gap-2 mb-2">
                                            <div class="flex-1 mt-7">
                                                <label :for="'attribute' + index"
                                                    class="block text-gray-700 text-sm font-bold mb-2">
                                                    Atributo:
                                                </label>
                                                <select :id="'attribute' + index" v-model="attribute.attributeId"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                    <option value="">Seleccione un atributo</option>
                                                    <option v-for="atributo in atributos" :key="atributo.id"
                                                        :value="atributo.id">
                                                        {{ atributo.name }}
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="flex-1 mt-[27.5px] mr-5">
                                                <label :for="'attribute_values' + index"
                                                    class="block text-gray-700 text-sm font-bold mb-2">
                                                    Valores de Atributo:
                                                </label>
                                                <TagSelect id="attribute_values" label="Valores de Atributo"
                                                    :options="atributoOptions" v-model="attribute.attributeValues" />
                                            </div>

                                            <div class="flex-1">
                                                <input type="text"
                                                    class="shadow appearance-none border rounded w-full py-1 px-2 text-sm text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                    placeholder="Escribe una referencia global aquí">
                                                <label class="block text-gray-700 text-sm font-bold mb-2">Referencia
                                                    Interna:</label>
                                                <input type="text" v-model="attribute.referenceInternal"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                    placeholder="">
                                            </div>

                                            <div class="flex-1 mt-7">
                                                <label class="block text-gray-700 text-sm font-bold mb-2">Precio
                                                    Extra:</label>
                                                <input type="number" v-model="attribute.extraPrice"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                    placeholder="">
                                            </div>

                                            <div class="mt-[60px]">
                                                <button type="button" @click="removerAtributo(index)"
                                                    class="bg-red-500 text-white px-3 py-1 rounded self-end">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <button type="button" @click="anadirAtributo"
                                            class="bg-gray-500 text-white px-3 py-1 rounded mt-2">
                                            <i class="fas fa-circle-plus"></i> Nuevo Atributo
                                        </button>
                                    </div>

                                    <div class="flex items-center justify-end mt-4">
                                        <button type="submit"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2">
                                            Registrar
                                        </button>
                                        <button type="button" @click="closeModal"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
