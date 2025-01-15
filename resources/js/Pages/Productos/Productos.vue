<script lang="ts" setup>
import { ref, reactive, onMounted } from "vue";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";
import ProductoTabla from "@/Components/Productos/ProductoTabla.vue";
import ModalRegistrar from "@/Components/Productos/ModalRegistrar.vue";

const showModal = ref(false);

type Producto = {
    id: number;
    name: string;
    code: string;
    price: number;
    categoryPrincipal: number | null;
    subcategory1: number | null;
    subcategory2: number | null;
    subcategory3: number | null;
    subcategory4: number | null;
};

const productos = ref<Producto[]>([
    // { id: 1, name: "Producto 1", code: "P001", price: 100 },
    // { id: 2, name: "Producto 2", code: "P002", price: 200 },
    // { id: 3, name: "Producto 3", code: "P003", price: 300 },
]);

const categorias = ref([]);
const subcategorias = reactive<{ [key: number]: any[] }>({});
const subcategoriasMap = ref<{ [key: number]: string }>({});
const producto = reactive({
    subcategoria1: null,
    subcategoria2: null,
    subcategoria3: null,
});
const isLoading = reactive({
    1: false,
    2: false,
    3: false,
    4: false,
});
const atributos = ref([]);

const traerCategorias = async () => {
    try {
        const categoriasCacheadas = localStorage.getItem("categorias");
        if (categoriasCacheadas) {
            categorias.value = JSON.parse(categoriasCacheadas);
            return;
        }

        const respuesta = await axios.get(`/categorias/traer`);
        categorias.value = respuesta.data;

        localStorage.setItem("categorias", JSON.stringify(respuesta.data));
    } catch (error) {
        console.error("Error al traer las categorías:", error);
    }
};

const traerSubcategorias = async (id: number, level: number) => {
    const cacheKey = `subcategories_level_${level}_${id}`;
    isLoading[level] = true;

    const cachedData = localStorage.getItem(cacheKey);
    if (cachedData) {
        subcategorias[level] = JSON.parse(cachedData);
        subcategorias[level].forEach((subcat) => {
            subcategoriasMap.value[subcat.id] = subcat.name;
        });
        isLoading[level] = false;
        return;
    }

    try {
        const response = await axios.get(`/subcategorias/traer/${id}`);
        subcategorias[level] = response.data;

        localStorage.setItem(cacheKey, JSON.stringify(response.data));

        response.data.forEach((subcat) => {
            subcategoriasMap.value[subcat.id] = subcat.name;
        });
    } catch (error) {
        console.error(`Error cargando subcategorías para id ${id}:`, error);
    } finally {
        isLoading[level] = false;
    }
};

const alCambiarCategoriaPrincipal = async (idCategoria: number) => {
    if (idCategoria) {
        await traerSubcategorias(idCategoria, 1);

        producto.subcategoria1 = null;
        producto.subcategoria2 = null;
        producto.subcategoria3 = null;
    }
};

const alCambiarSubcategoria = async (idPadre: number, nivel: number) => {
    if (idPadre) {
        await traerSubcategorias(idPadre, nivel + 1);

        if (nivel === 1) {
            producto.subcategoria2 = null;
            producto.subcategoria3 = null;
        } else if (nivel === 2) {
            producto.subcategoria3 = null;
        }
    }
};

const traerAtributos = async () => {
    try {
        const atributosCacheadas = localStorage.getItem("atributos");
        if (atributosCacheadas) {
            atributos.value = JSON.parse(atributosCacheadas);
            return;
        }

        const respuesta = await axios.get(`/atributos/traer`);
        atributos.value = respuesta.data;

        localStorage.setItem("atributos", JSON.stringify(respuesta.data));
    } catch (error) {
        console.error("Error al traer los atributos:", error);
    }
};

const openModal = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const handleRegistrar = (producto: any) => {
    console.log("Producto registrado:", producto);
    closeModal();
};

const registrarProductos = () => {
    // console.log("Registrar productos en Odoo");
};

onMounted(() => {
    traerCategorias();
    traerAtributos();
});
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
                    <ProductoTabla :productos="productos" @agregar="openModal" @registrar="registrarProductos" />
                </div>
            </div>
        </div>

        <ModalRegistrar :show="showModal" @close="closeModal" @submit="handleRegistrar" :isLoading="isLoading"
            :categorias="categorias" :subcategorias="subcategorias" :atributos="atributos"
            @cambiarCategoriaPrincipal="alCambiarCategoriaPrincipal" @cambiarSubcategoria="alCambiarSubcategoria" />
    </AppLayout>
</template>
