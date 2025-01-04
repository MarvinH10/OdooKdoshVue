<script setup>
import { ref, reactive, onMounted, watch, nextTick } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";
import ProductoTabla from "@/Components/ProductoTabla.vue";

const subcategoryCache = new Map();
const fetchedIds = new Set();
const baseUrl = import.meta.env.VITE_APP_ODOO_URL;

const productos = ref([]);
const isUploading = ref(false);
const producto = reactive({
    id: null,
    name: "",
    list_price: 0,
    default_code: "",
    categ_id: null,
    subcateg1_id: null,
    subcateg2_id: null,
    subcateg3_id: null,
    subcateg4_id: null,
    attributes: [
        {
            attribute_id: null,
            value_ids: [],
            extra_references: [],
            extra_prices: [],
        },
    ],
});
const originalProducto = reactive({});
const showModal = ref(false);
const showLinksModal = ref(false);
const isEdit = ref(false);
const categories = ref([]);
const categoriesMap = ref({});
const subcategoriesMap = ref({});
const subcategories = reactive({
    1: [],
    2: [],
    3: [],
    4: [],
});
const loading = ref(true);
const registeredProductIds = ref([]);

const allAttributes = ref([]);
const attributeInputs = ref([]);
const productosRegistrados = ref([]);

const getAttributeValuesString = (attributes) => {
    return attributes
        .map((attr) =>
            attr.value_ids.map((id) => getValueAttributeName(id)).join(", ")
        )
        .join(" ");
};

axios.defaults.withCredentials = true;

axios.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        console.error("Error en la llamada API:", error);
        alert(
            "Error en la llamada API: " +
            (error.response?.data?.error || error.message)
        );
        return Promise.reject(error);
    }
);

const generarIdUnico = (existiendoProductos) => {
    const maxId = existiendoProductos.reduce(
        (max, product) => Math.max(max, product.id),
        0
    );
    return maxId + 1;
};

const registrarProducto = async () => {
    try {
        const productPayload = { ...producto };

        for (let i = 1; i <= 4; i++) {
            if (!productPayload[`subcateg${i}_id`]) {
                delete productPayload[`subcateg${i}_id`];
            }
        }

        if (attributeInputs.value.length) {
            productPayload.attributes = attributeInputs.value.map((input) => {
                const extraReferences = input.selectedAttributeValues.map((_, index) => {
                    return (
                        input.extraReferences[index]?.trim() ||
                        ""
                    );
                });

                const valueIds = input.selectedAttributeValues.map((value) => parseInt(value));
                const valueNames = valueIds.map((valueId) => {
                    return getValueAttributeName(valueId);
                });

                return {
                    attribute_id: input.selectedAttribute,
                    value_ids: valueIds,
                    value_names: valueNames,
                    extra_references: extraReferences,
                    extra_prices: input.extraPrices.map((price) =>
                        parseFloat(price) || 0
                    ),
                };
            });

            const hasEmptyReferences = productPayload.attributes.some((attr) =>
                attr.extra_references.some((ref) => !ref || ref.trim() === "")
            );
        } else {
            productPayload.attributes = [];
        }

        console.log("Producto a registrar:", JSON.stringify(productPayload, null, 2));

        if (isEdit.value) {
            const updatePayload = {};
            Object.keys(productPayload).forEach((key) => {
                if (productPayload[key] !== originalProducto[key]) {
                    updatePayload[key] = productPayload[key];
                }
            });

            await axios.put(`/productos/actualizar/${productPayload.id}`, updatePayload);

            const index = productos.value.findIndex((p) => p.id === productPayload.id);
            productos.value[index] = { ...productPayload };
        } else {
            const response = await axios.post("/productos/almacenar", productPayload);

            productos.value.push(response.data);
        }

        resetProducto();
        showModal.value = false;

        toast.success("Producto registrado correctamente.", {
            autoClose: 3000,
            position: "bottom-right",
        });
    } catch (error) {
        console.error("Error registrando producto:", error);

        toast.error(
            "Error registrando producto: " +
            (error.response?.data?.error || error.message),
            { autoClose: 4000, position: "bottom-right" }
        );
    }
};

const registrarTodosLosProductos = async () => {
    try {
        isUploading.value = true;
        console.log("Registrando todos los productos:", productos.value);
        const response = await axios.post(
            "/productos/registrar_todo",
            productos.value
        );
        registeredProductIds.value = response.data.product_ids;
        showLinksModal.value = true;

        for (const producto of productos.value) {
            await eliminarProducto(producto.id);
        }

        productos.value = [];
        toast.success("Productos registrados con éxito", {
            autoClose: 3000,
            position: "bottom-right",
            style: {
                width: "400px",
            },
            className: "border-l-4 border-green-500 p-4",
        });
    } catch (error) {
        console.error("Error registrando todos los productos:", error);
        alert(
            "Error registrando todos los productos: " +
            (error.response?.data?.error || error.message)
        );
    } finally {
        isUploading.value = false;
    }
};

const loadInitialData = async () => {
    try {
        const [categoriesResponse, attributesResponse] = await Promise.all([
            axios.get("/categorias/traer"),
            axios.get("/atributos/traer"),
        ]);

        categories.value = categoriesResponse.data;
        categoriesMap.value = categoriesResponse.data.reduce((map, category) => {
            map[category.id] = category.name;
            return map;
        }, {});

        allAttributes.value = attributesResponse.data.map(attribute => ({
            ...attribute,
            values: [],
            isLoaded: false,
        }));

        // console.log("Categorías y atributos cargados exitosamente.");
    } catch (error) {
        console.error("Error cargando datos iniciales:", error);
    }
};

const fetchSubcategories = async (id, level) => {
    if (subcategoryCache.has(id)) {
        subcategories[level] = subcategoryCache.get(id);
        subcategories[level].forEach((subcat) => {
            subcategoriesMap.value[subcat.id] = subcat.name;
        });
        return;
    }

    try {
        const response = await axios.get(`/subcategorias/traer/${id}`);
        subcategories[level] = response.data;
        subcategoryCache.set(id, response.data);
        response.data.forEach((subcat) => {
            subcategoriesMap.value[subcat.id] = subcat.name;
        });
        //console.log(
        //    `Subcategorías nivel ${level} cargadas para categoría ${id}.`
        //);
    } catch (error) {
        console.error(`Error cargando subcategorías para id ${id}:`, error);
    }
};

const fetchAttributeValues = async (id, index) => {
    try {
        const response = await axios.get(`/valores_atributos/traer/${id}`);
        attributeInputs.value[index].values = response.data;

        localStorage.setItem(`attribute_values_${id}`, JSON.stringify(response.data));

        nextTick(() => {
            const selectElement = $(`#attribute_values${index}`);
            selectElement
                .select2({
                    width: "100%",
                    tags: true,
                })
                .on("change", function () {
                    const selectedValues = Array.from(
                        this.selectedOptions,
                        (option) => option.value
                    );
                    attributeInputs.value[index].selectedAttributeValues = selectedValues;

                    attributeInputs.value[index].extraReferences = selectedValues.map(
                        (valueId) => {
                            const attributeName = getValueAttributeName(valueId);
                            return `${attributeInputs.value[index].newField} | ${attributeName}`;
                        }
                    );
                });
        });

        // console.log(`Valores de atributo cargados para atributo ${id}`);
    } catch (error) {
        console.error(`Error cargando valores de atributo ${id}:`, error);
    }
};

const addAttribute = () => {
    attributeInputs.value.push({
        selectedAttribute: null,
        values: [],
        selectedAttributeValues: [],
        extraReferences: [],
        extraPrices: [],
    });
    nextTick(() => {
        const index = attributeInputs.value.length - 1;
        const selectElement = $(`#attribute_values${index}`);
        selectElement
            .select2({
                width: "100%",
                tags: true,
            })
            .on("change", function () {
                const selectedValues = Array.from(
                    this.selectedOptions,
                    (option) => option.value
                );
                attributeInputs.value[index].selectedAttributeValues = selectedValues;

                attributeInputs.value[index].extraReferences = new Array(
                    selectedValues.length
                ).fill("");
                attributeInputs.value[index].extraPrices = new Array(
                    selectedValues.length
                ).fill("");
            });
    });
};

const removeAttribute = (index) => {
    attributeInputs.value.splice(index, 1);
};

const resetProducto = () => {
    Object.assign(producto, {
        id: null,
        name: "",
        list_price: 0,
        default_code: "",
        product_id: null,
        categ_id: null,
        subcateg1_id: null,
        subcateg2_id: null,
        subcateg3_id: null,
        subcateg4_id: null,
        attributes: [],
    });
    attributeInputs.value = [];
    Object.assign(originalProducto, producto);
    isEdit.value = false;
};

const eliminarProducto = async (id) => {
    productos.value = productos.value.filter((producto) => producto.id !== id);

    try {
        await axios.delete(`/productos/eliminar/${id}`);
    } catch (error) {
        console.error("Error eliminando producto:", error);
        alert("Error al eliminar el producto, por favor intente de nuevo.");
        recuperarProductos();
    }
};

async function recuperarProductos() {
    try {
        const response = await axios.get("/productos/traer");
        productos.value = response.data;
    } catch (error) {
        console.error("Error al recuperar productos:", error);
    }
}

const duplicarProducto = async (producto) => {
    const newId = generarIdUnico(productos.value);
    const duplicatedProducto = {
        ...producto,
        id: newId,
        name: `${producto.name} (Copia)`,
    };

    if (duplicatedProducto.attributes) {
        duplicatedProducto.attributes = duplicatedProducto.attributes.map(
            (attr) => ({
                ...attr,
                extra_references: [...attr.extra_references],
                extra_prices: [...attr.extra_prices],
                value_ids: [...attr.value_ids],
            })
        );
    }

    try {
        const response = await axios.post(
            "/productos/almacenar",
            duplicatedProducto
        );
        productos.value.push(response.data);

        console.log(
            `Producto duplicado y almacenado con ID ${newId}:`,
            duplicatedProducto
        );
    } catch (error) {
        console.error("Error al almacenar el producto duplicado:", error);
    }
};

const editarProducto = async (productoData) => {
    Object.assign(producto, productoData);
    Object.assign(originalProducto, productoData);

    const promises = [];

    const loadSubcategoriesFromCache = (id, level) => {
        if (subcategoryCache.has(id)) {
            subcategories[level] = subcategoryCache.get(id);
            subcategories[level].forEach((subcat) => {
                subcategoriesMap.value[subcat.id] = subcat.name;
            });
            return true;
        }
        return false;
    };

    if (producto.categ_id) {
        if (!loadSubcategoriesFromCache(producto.categ_id, 1)) {
            promises.push(fetchSubcategories(producto.categ_id, 1));
        }
    } else {
        producto.subcateg1_id = null;
        producto.subcateg2_id = null;
        producto.subcateg3_id = null;
        producto.subcateg4_id = null;
        subcategories[2] = [];
        subcategories[3] = [];
        subcategories[4] = [];
    }

    if (producto.subcateg1_id) {
        if (!loadSubcategoriesFromCache(producto.subcateg1_id, 2)) {
            promises.push(fetchSubcategories(producto.subcateg1_id, 2));
        }
    } else {
        producto.subcateg2_id = null;
        producto.subcateg3_id = null;
        producto.subcateg4_id = null;
        subcategories[3] = [];
        subcategories[4] = [];
    }

    if (producto.subcateg2_id) {
        if (!loadSubcategoriesFromCache(producto.subcateg2_id, 3)) {
            promises.push(fetchSubcategories(producto.subcateg2_id, 3));
        }
    } else {
        producto.subcateg3_id = null;
        producto.subcateg4_id = null;
        subcategories[4] = [];
    }

    if (producto.subcateg3_id) {
        if (!loadSubcategoriesFromCache(producto.subcateg3_id, 4)) {
            promises.push(fetchSubcategories(producto.subcateg3_id, 4));
        }
    } else {
        producto.subcateg4_id = null;
    }

    attributeInputs.value = producto.attributes.map((attr) => {
        const attribute = allAttributes.value.find(
            (a) => a.id === attr.attribute_id
        );
        const selectedAttributeValues = attr.value_ids.map(String);
        return {
            selectedAttribute: attr.attribute_id,
            values: attribute ? attribute.values : [],
            selectedAttributeValues: selectedAttributeValues,
            extraReferences:
                attr.extra_references.map(
                    (ref, index) =>
                        ref || (producto.default_code && !ref ? producto.default_code : "")
                ) || new Array(selectedAttributeValues.length).fill(""),
            extraPrices:
                attr.extra_prices || new Array(selectedAttributeValues.length).fill(""),
        };
    });

    await Promise.all(promises);

    nextTick(() => {
        showModal.value = true;
        nextTick(() => {
            attributeInputs.value.forEach((input, index) => {
                const selectElement = $(`#attribute_values${index}`);
                selectElement
                    .select2({
                        width: "100%",
                        tags: true,
                    })
                    .val(input.selectedAttributeValues)
                    .trigger("change");

                selectElement.on("change", function () {
                    const selectedValues = Array.from(
                        this.selectedOptions,
                        (option) => option.value
                    );
                    attributeInputs.value[index].selectedAttributeValues = selectedValues;

                    attributeInputs.value[index].extraReferences = selectedValues.map(
                        (val, i) => input.extraReferences[i] || producto.default_code
                    );
                    attributeInputs.value[index].extraPrices = new Array(
                        selectedValues.length
                    ).fill("");
                });
            });

            const defaultCodeInput = document.getElementById("default_code");
            if (defaultCodeInput) {
                if (hasExtraReferences()) {
                    defaultCodeInput.disabled = true;
                } else {
                    defaultCodeInput.disabled = false;
                }
            }
        });
    });

    isEdit.value = true;
};

const hasExtraReferences = () => {
    return attributeInputs.value.some((input) =>
        input.extraReferences.some((ref) => ref && ref.trim() !== "")
    );
};

const openModalForNewProduct = () => {
    resetProducto();
    showModal.value = true;
    nextTick(() => {
        attributeInputs.value.forEach((input, index) => {
            const selectElement = $(`#attribute_values${index}`);
            selectElement
                .select2({
                    width: "100%",
                    tags: true,
                })
                .on("change", function () {
                    const selectedValues = Array.from(
                        this.selectedOptions,
                        (option) => option.value
                    );
                    attributeInputs.value[index].selectedAttributeValues = selectedValues;

                    attributeInputs.value[index].extraReferences = new Array(
                        selectedValues.length
                    ).fill("");
                    attributeInputs.value[index].extraPrices = new Array(
                        selectedValues.length
                    ).fill("");
                });
        });
    });
};

onMounted(async () => {
    try {
        await recuperarProductos();

        const uniqueIds = new Set();
        productos.value.forEach((product) => {
            if (product.categ_id) uniqueIds.add(product.categ_id);
            if (product.subcateg1_id) uniqueIds.add(product.subcateg1_id);
            if (product.subcateg2_id) uniqueIds.add(product.subcateg2_id);
            if (product.subcateg3_id) uniqueIds.add(product.subcateg3_id);
        });

        const subcategoryPromises = Array.from(uniqueIds).map((id) => {
            if (!fetchedIds.has(id)) {
                fetchedIds.add(id);
                return fetchSubcategories(id, 1);
            }
            return Promise.resolve();
        });

        await Promise.all(subcategoryPromises);
    } catch (error) {
        console.error("Error fetching productos:", error);
    }
    await loadInitialData();
    loading.value = false;
});

const watchCategoryChange = (getter, level) => {
    watch(getter, (newVal, oldVal) => {
        if (newVal && newVal !== oldVal) {
            fetchSubcategories(newVal, level).then(() => {
                for (let i = level + 1; i <= 4; i++) {
                    subcategories[i] = [];
                    producto[`subcateg${i}_id`] = null;
                }

                localStorage.setItem("subcategories", JSON.stringify(subcategories));
            });
        } else if (!newVal) {
            for (let i = level; i <= 4; i++) {
                subcategories[i] = [];
                producto[`subcateg${i}_id`] = null;
            }

            localStorage.setItem("subcategories", JSON.stringify(subcategories));
        }
    });
};

watchCategoryChange(() => producto.categ_id, 1);
watchCategoryChange(() => producto.subcateg1_id, 2);
watchCategoryChange(() => producto.subcateg2_id, 3);
watchCategoryChange(() => producto.subcateg3_id, 4);

const getValueAttributeName = (valueId) => {
    for (const input of attributeInputs.value) {
        const value = input.values.find((val) => val.id === parseInt(valueId));
        if (value) {
            return value.name;
        }
    }

    for (const attribute of allAttributes.value) {
        if (attribute.values) {
            const value = attribute.values.find(
                (val) => val.id === parseInt(valueId)
            );
            if (value) {
                return value.name;
            }
        }
    }

    return "Valor Desconocido";
};

watch(
    () => attributeInputs.value.map((input) => input.newField),
    (newValues) => {
        newValues.forEach((newFieldValue, index) => {
            const selectedValues =
                attributeInputs.value[index].selectedAttributeValues;

            if (newFieldValue && newFieldValue.trim() !== "" && selectedValues) {
                attributeInputs.value[index].extraReferences = selectedValues.map(
                    (valueId) => {
                        const attributeName = getValueAttributeName(valueId);
                        return `${newFieldValue.trim()} | ${attributeName}`;
                    }
                );
            } else {
                attributeInputs.value[index].extraReferences = [];
            }
        });
    },
    { deep: true }
);

watch(
    () => attributeInputs.value.map((input) => input.extraReferences).flat(),
    (newVal) => {
        const defaultCodeInput = document.getElementById("default_code");
        if (defaultCodeInput) {
            if (newVal.some((ref) => ref && ref.trim() !== "")) {
                defaultCodeInput.disabled = false;
                defaultCodeInput.value = "";
                producto.default_code = "";
            } else {
                defaultCodeInput.disabled = false;
            }
        }
    },
    { deep: true }
);

watch(
    () => producto.default_code,
    (newVal) => {
        if (newVal) {
            attributeInputs.value.forEach((input) => {
                input.extraReferences = new Array(
                    input.selectedAttributeValues.length
                ).fill("");
            });
        }
    }
);

const handleKeyDown = (event) => {
    if (
        event.ctrlKey &&
        event.shiftKey &&
        (event.key === "ArrowUp" || event.key === "ArrowDown")
    ) {
        const activeElement = document.activeElement;
        if (
            activeElement &&
            activeElement.classList.contains("extra-reference-input")
        ) {
            attributeInputs.value.forEach((input) => {
                input.extraReferences = new Array(
                    input.selectedAttributeValues.length
                ).fill("");
            });
        } else if (
            activeElement &&
            activeElement.classList.contains("extra-price-input")
        ) {
            attributeInputs.value.forEach((input) => {
                input.extraPrices = new Array(
                    input.selectedAttributeValues.length
                ).fill("");
            });
        }
    }
};

window.addEventListener("keydown", handleKeyDown);
</script>

<template>
    <AppLayout title="Productos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Productos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-12xl mx-auto sm:px-6 lg:px-1">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <button @click="openModalForNewProduct"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-circle-plus"></i> Agregar
                    </button>
                    <ProductoTabla :productos="productos" :loading="loading" :categoriesMap="categoriesMap"
                        :subcategoriesMap="subcategoriesMap" :allAttributes="allAttributes"
                        :attributeInputs="attributeInputs" @duplicar="duplicarProducto" @editar="editarProducto"
                        @eliminar="eliminarProducto" />
                </div>
                <div class="flex justify-end mt-4">
                    <button v-if="!isUploading" @click="registrarTodosLosProductos"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-save"></i> Crear Productos en Odoo
                    </button>

                    <div v-else class="text-center text-blue-500 font-bold">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Subiendo productos...
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:w-full w-full max-w-md sm:max-w-xl md:max-w-2xl lg:max-w-4xl xl:max-w-6xl 2xl:max-w-7xl">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    {{ isEdit ? "Editar Producto" : "Registrar Producto" }}
                                </h3>
                                <div class="mt-2">
                                    <form @submit.prevent="registrarProducto">
                                        <div class="mb-4 flex flex-wrap gap-4">
                                            <div class="flex-1">
                                                <label for="name"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                                                <input type="text" id="name" v-model="producto.name"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                                            </div>
                                            <div id="default_code_container" class="flex-1">
                                                <label for="default_code"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Código del
                                                    producto:</label>
                                                <input type="text" id="default_code" v-model="producto.default_code"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                                            </div>
                                        </div>
                                        <div class="mb-4 flex flex-wrap gap-4">
                                            <div class="flex-1">
                                                <label for="categ_id"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Categoría
                                                    Principal:</label>
                                                <select id="categ_id" v-model="producto.categ_id"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                    <option v-for="category in categories" :key="category.id"
                                                        :value="category.id">
                                                        {{ category.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="flex-1">
                                                <label for="subcateg1_id"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Categoría
                                                    1:</label>
                                                <select id="subcateg1_id" v-model="producto.subcateg1_id" :disabled="!subcategories[1] || subcategories[1].length === 0
                                                    "
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                    <option v-for="subcategory in subcategories[1]"
                                                        :key="subcategory.id" :value="subcategory.id">
                                                        {{ subcategory.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="flex-1">
                                                <label for="subcateg2_id"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Categoría
                                                    2:</label>
                                                <select id="subcateg2_id" v-model="producto.subcateg2_id" :disabled="!subcategories[2] || subcategories[2].length === 0
                                                    "
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                    <option v-for="subcategory in subcategories[2]"
                                                        :key="subcategory.id" :value="subcategory.id">
                                                        {{ subcategory.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="flex-1">
                                                <label for="subcateg3_id"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Categoría
                                                    3:</label>
                                                <select id="subcateg3_id" v-model="producto.subcateg3_id" :disabled="!subcategories[3] || subcategories[3].length === 0
                                                    "
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                    <option v-for="subcategory in subcategories[3]"
                                                        :key="subcategory.id" :value="subcategory.id">
                                                        {{ subcategory.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="flex-1">
                                                <label for="subcateg4_id"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Categoría
                                                    4:</label>
                                                <select id="subcateg4_id" v-model="producto.subcateg4_id" :disabled="!subcategories[4] || subcategories[4].length === 0
                                                    "
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                    <option v-for="subcategory in subcategories[4]"
                                                        :key="subcategory.id" :value="subcategory.id">
                                                        {{ subcategory.name }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="list_price"
                                                class="block text-gray-700 text-sm font-bold mb-2">Precio
                                                Venta:</label>
                                            <input value="0" type="number" id="list_price" v-model="producto.list_price"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                                        </div>
                                        <div class="mb-4">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                                Atributos del Producto
                                            </h3>
                                            <div v-for="(input, index) in attributeInputs" :key="index"
                                                class="flex gap-2 mb-2">
                                                <div class="flex-1">
                                                    <label :for="'attribute' + index"
                                                        class="block text-gray-700 text-sm font-bold mb-2">
                                                        Atributo:
                                                    </label>
                                                    <select :id="'attribute' + index" v-model="input.selectedAttribute"
                                                        @change="
                                                            fetchAttributeValues(
                                                                input.selectedAttribute,
                                                                index
                                                            )
                                                            "
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                        <option v-for="attribute in allAttributes" :key="attribute.id"
                                                            :value="attribute.id">
                                                            {{ attribute.name }}
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="flex-1">
                                                    <label :for="'attribute_values' + index"
                                                        class="block text-gray-700 text-sm font-bold mb-2">
                                                        Valores de Atributo:
                                                    </label>
                                                    <select :id="'attribute_values' + index"
                                                        v-model="input.selectedAttributeValues" multiple
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                        <option v-for="value in input.values" :key="value.id"
                                                            :value="value.id">
                                                            {{ value.name }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="flex-1 mt-0.5">
                                                    <input type="text" v-model="input.newField"
                                                        placeholder="Escribe una referencia global aquí"
                                                        class="shadow appearance-none border rounded w-full py-1 px-2 text-sm text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                                        Referencia Interna:
                                                    </label>
                                                    <div v-if="
                                                        input.newField && input.newField.trim() !== ''
                                                    ">
                                                        <input type="text"
                                                            v-for="(ref, refIndex) in input.extraReferences"
                                                            :key="'ref' + index + refIndex"
                                                            v-model="input.extraReferences[refIndex]" :placeholder="`Referencia para ${getValueAttributeName(
                                                                input.selectedAttributeValues[refIndex]
                                                            ) || ''
                                                                }`"
                                                            @input="input.extraReferences[refIndex] = input.extraReferences[refIndex]?.trim() || '\u200B'"
                                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-2 extra-reference-input" />
                                                    </div>
                                                </div>

                                                <div class="flex-1 mt-8">
                                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                                        Precio Extra:
                                                    </label>
                                                    <input type="number"
                                                        v-for="(price, priceIndex) in input.extraPrices"
                                                        :key="'price' + index + priceIndex"
                                                        v-model="input.extraPrices[priceIndex]" :placeholder="`Precio para ${getValueAttributeName(
                                                            input.selectedAttributeValues[priceIndex]
                                                        ) || ''
                                                            }`"
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-2 extra-price-input" />
                                                </div>

                                                <button type="button" @click="removeAttribute(index)"
                                                    class="bg-red-500 text-white px-3 py-1 rounded mt-2 self-end">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                            <button type="button" @click="addAttribute"
                                                class="bg-gray-500 text-white px-3 py-1 rounded mt-2">
                                                <i class="fas fa-circle-plus"></i>
                                                Nuevo Atributo
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-end mt-4">
                                            <button type="submit"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2">
                                                {{ isEdit ? "Actualizar" : "Registrar" }}
                                            </button>
                                            <button type="button" @click="
                                                showModal = false;
                                            resetProducto();
                                            "
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

        <div v-if="showLinksModal" class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full max-w-6xl">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Productos Registrados
                                </h3>
                                <div class="mt-2">
                                    <p>
                                        A continuación se muestran los enlaces a los productos
                                        registrados en Odoo:
                                    </p>
                                    <ul class="mt-4">
                                        <li v-for="productId in registeredProductIds" :key="productId">
                                            <a :href="`${baseUrl}web?debug=1#id=${productId}&cids=1&menu_id=206&action=354&model=product.template&view_type=form`"
                                                target="_blank">
                                                Ver Producto {{ productId }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <button @click="showLinksModal = false"
                                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-2">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
.tag {
    display: inline-block;
    background-color: #e2e8f0;
    border-radius: 0.375rem;
    padding: 0.25rem 0.5rem;
    margin: 0.25rem;
    font-size: 0.875rem;
    color: #2d3748;
}

input::placeholder {
    color: #a0aec0;
}
</style>
