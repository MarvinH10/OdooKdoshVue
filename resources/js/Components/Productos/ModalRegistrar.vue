<script lang="ts" setup>
import { defineProps, defineEmits, reactive, watch, computed, PropType } from "vue";
import TagSelect from "./TagSelect.vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

type Categoria = {
    id: number;
    name: string;
};

type Atributo = {
    id: number;
    name: string;
};

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    productoInicial: {
        type: Object as PropType<{
            name: string;
            code: string;
            price: number;
            category: number | null;
            subcategory1: number | null;
            subcategory2: number | null;
            subcategory3: number | null;
            subcategory4: number | null;
            attributes: Array<{
                attributeId: string;
                attributeValues: Array<{ id: string; name: string }>;
                referenceGlobal: string;
                referencesInternal: Record<string, string>;
                extraPrice: number;
            }>;
        }> | null,
        default: null,
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
    valores_atributos: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["close", "submit", "update", "cambiarCategoriaPrincipal", "cambiarSubcategoria", "cambiarAtributo",]);

const producto = reactive({
    name: "",
    code: "",
    price: 0,
    category: null as number | null,
    subcategory1: null as number | null,
    subcategory2: null as number | null,
    subcategory3: null as number | null,
    subcategory4: null as number | null,
    attributes: [] as Array<{
        attributeId: string;
        attributeValues: Array<{ id: string; name: string }>;
        referenceGlobal: string;
        referencesInternal: Record<string, string>;
        extraPrice: number;
    }>,
});

const isSubcategory1Disabled = computed(() => !producto.category);
const isSubcategory2Disabled = computed(() => !producto.subcategory1);
const isSubcategory3Disabled = computed(() => !producto.subcategory2);
const isSubcategory4Disabled = computed(() => !producto.subcategory3);

const camposBloqueados = reactive({
    codeInput: false,
    referenceInputs: false,
});

const enReferenciaCambioGlobal = (index: number, value: string) => {
    const attribute = producto.attributes[index];
    attribute.referenceGlobal = value;

    if (value.trim() === "") {
        attribute.attributeValues.forEach((attrValue) => {
            attribute.referencesInternal[attrValue.id] = "";
        });
    } else {
        attribute.attributeValues.forEach((attrValue) => {
            attribute.referencesInternal[attrValue.id] = `${value} | ${attrValue.name}`;
        });
    }
};

const actualizarReferencias = (index: number, values: Array<{ id: string; name: string }>) => {
    const currentReferences = producto.attributes[index].referencesInternal || {};
    const updatedReferences: Record<string, string> = {};
    values.forEach((value) => {
        updatedReferences[value.id] = currentReferences[value.id] || "";
        updatedReferences[value.id + "_extraPrice"] = currentReferences[value.id + "_extraPrice"] || "";
    });
    producto.attributes[index].referencesInternal = updatedReferences;
};

const resetFormulario = () => {
    producto.name = "";
    producto.code = "";
    producto.price = 0;
    producto.category = null;
    producto.subcategory1 = null;
    producto.subcategory2 = null;
    producto.subcategory3 = null;
    producto.subcategory4 = null;
    producto.attributes = [];
};


const closeModal = () => {
    resetFormulario();
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

const añadirAtributo = () => {
    producto.attributes.push({
        attributeId: "",
        attributeValues: [],
        referenceGlobal: "",
        referencesInternal: {},
        extraPrice: 0,
    });
};

const removerAtributo = (index: number) => {
    producto.attributes.splice(index, 1);
};

const submitProduct = () => {
    if (!producto.name.trim()) {
        toast.error("El campo Nombre es obligatorio.", {
            autoClose: 3000,
            position: "bottom-right",
        });
        return;
    }

    if (!producto.category) {
        toast.error("Debe seleccionar al menos una Categoría Principal.", {
            autoClose: 3000,
            position: "bottom-right",
        });
        return;
    }
    const reorderedAttributes = producto.attributes.slice().sort((a, b) => {
        const hasDataA =
            a.referenceGlobal.trim() !== "" ||
            Object.values(a.referencesInternal).some(value => value.toString().trim() !== "");
        const hasDataB =
            b.referenceGlobal.trim() !== "" ||
            Object.values(b.referencesInternal).some(value => value.toString().trim() !== "");

        if (hasDataA && !hasDataB) {
            return 1;
        } else if (!hasDataA && hasDataB) {
            return -1;
        }
        return 0;
    });

    producto.attributes = reorderedAttributes;

    if (props.productoInicial) {
        emit("update", { ...producto });
        toast.success("Producto actualizado correctamente.", {
            autoClose: 3000,
            position: "bottom-right",
        });
    } else {
        emit("submit", { ...producto });
        toast.success("Producto añadido a la lista correctamente.", {
            autoClose: 3000,
            position: "bottom-right",
        });
    }
    closeModal();
};

watch(
  () => props.productoInicial,
  (nuevoProducto) => {
    if (nuevoProducto) {
      Object.assign(producto, nuevoProducto);
    } else {
      resetFormulario();
    }
  },
  { immediate: true }
);

watch(
    () => producto.code,
    (newVal) => {
        if (newVal) {
            camposBloqueados.referenceInputs = true;
        } else {
            camposBloqueados.referenceInputs = false;
        }
    }
);

watch(
    () => producto.attributes.map(attr => attr.referenceGlobal).join(""),
    (newVal) => {
        if (newVal.trim()) {
            camposBloqueados.codeInput = true;
        } else {
            camposBloqueados.codeInput = false;
        }
    }
);
</script>

<template>
    <div v-if="show" class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ productoInicial ? "Editar Producto" : "Registrar Producto" }}</h3>
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
                                                :disabled="camposBloqueados.codeInput" :class="[
                                                    'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
                                                    { 'text-gray-500 bg-gray-100 cursor-not-allowed': camposBloqueados.codeInput }
                                                ]" />
                                        </div>
                                    </div>

                                    <div class="mb-4 flex flex-wrap gap-4">
                                        <div class="flex-1">
                                            <label for="category" class="block text-gray-700 text-sm font-bold mb-2">
                                                Categoría Principal:
                                            </label>
                                            <select id="category" v-model="producto.category"
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
                                                :disabled="isLoading[1] || isLoading[2] || isLoading[3] || isLoading[4] || isSubcategory1Disabled"
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
                                                :disabled="isLoading[1] || isLoading[2] || isLoading[3] || isLoading[4] || isSubcategory2Disabled"
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
                                                :disabled="isLoading[1] || isLoading[2] || isLoading[3] || isLoading[4] || isSubcategory3Disabled"
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
                                                :disabled="isLoading[1] || isLoading[2] || isLoading[3] || isLoading[4] || isSubcategory4Disabled"
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
                                        <div v-for="(attribute, index) in producto.attributes" :key="index"
                                            class="flex gap-2 mb-2">
                                            <div class="flex-1 mt-7">
                                                <label :for="'attribute_' + index"
                                                    class="block text-gray-700 text-sm font-bold mb-2">
                                                    Atributo:
                                                </label>
                                                <select :id="'attribute_' + index" v-model="attribute.attributeId"
                                                    @change="emit('cambiarAtributo', attribute.attributeId)"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                    <option value="">Seleccione un atributo</option>
                                                    <option v-for="atributo in atributos" :key="atributo.id"
                                                        :value="atributo.id">
                                                        {{ atributo.name }}
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="flex-1 mt-[27.5px] mr-5">
                                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                                    Valores de Atributo:
                                                </label>
                                                <template v-if="valores_atributos[attribute.attributeId]">
                                                    <TagSelect :id="'attribute_values_' + index"
                                                        :options="valores_atributos[attribute.attributeId]"
                                                        :attributeId="attribute.attributeId"
                                                        v-model="attribute.attributeValues"
                                                        @update:modelValue="actualizarReferencias(index, $event)" />
                                                </template>
                                                <template v-else>
                                                    <input :id="'attribute_values_' + index" type="text"
                                                        value="Seleccione un atributo primero" disabled
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-500 bg-gray-100 leading-tight cursor-not-allowed" />
                                                </template>
                                            </div>

                                            <div class="flex-1">
                                                <input type="text" :id="'referenceGlobal_' + index"
                                                    v-model="attribute.referenceGlobal"
                                                    :disabled="camposBloqueados.referenceInputs"
                                                    @input="enReferenciaCambioGlobal(index, attribute.referenceGlobal)"
                                                    :class="[
                                                        'shadow appearance-none border rounded w-full py-1 px-2 text-sm leading-tight focus:outline-none focus:shadow-outline',
                                                        { 'text-gray-500 bg-gray-100 cursor-not-allowed': camposBloqueados.referenceInputs }
                                                    ]" placeholder="Escribe una referencia global aquí" />
                                                <label class="block text-gray-700 text-sm font-bold mb-2">Referencia
                                                    Interna:</label>
                                                <div v-for="attrValue in attribute.attributeValues" :key="attrValue.id"
                                                    class="mb-2">
                                                    <input :id="'referenceInternal_' + index + '_' + attrValue.id"
                                                        v-model="attribute.referencesInternal[attrValue.id]"
                                                        :disabled="camposBloqueados.referenceInputs" :class="[
                                                            'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
                                                            { 'text-gray-500 bg-gray-100 cursor-not-allowed': camposBloqueados.referenceInputs }
                                                        ]"
                                                        :placeholder="'Referencia interna para ' + attrValue.name" />
                                                </div>
                                            </div>

                                            <div class="flex-1 mt-7">
                                                <label class="block text-gray-700 text-sm font-bold mb-2">Precio
                                                    Extra:</label>
                                                <div v-for="attrValue in attribute.attributeValues" :key="attrValue.id"
                                                    class="mb-2">
                                                    <input type="number"
                                                        v-model="attribute.referencesInternal[attrValue.id + '_extraPrice']"
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                        :placeholder="'Precio Extra para ' + attrValue.name" />
                                                </div>
                                            </div>

                                            <div class="mt-[60px]">
                                                <button type="button" @click="removerAtributo(index)"
                                                    class="bg-red-500 text-white px-3 py-1 rounded self-end">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <button type="button" @click="añadirAtributo"
                                            class="bg-gray-500 text-white px-3 py-1 rounded mt-2">
                                            <i class="fas fa-circle-plus"></i> Nuevo Atributo
                                        </button>
                                    </div>

                                    <div class="flex items-center justify-end mt-4">
                                        <button type="submit"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2">
                                            {{ productoInicial ? "Actualizar" : "Registrar" }}
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
