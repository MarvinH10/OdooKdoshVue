<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';

// Variables reactivas
const proveedores = ref([]);
const productos = ref([]);
const selectedProveedor = ref(null);
const selectedProducto = ref(null);
const codigoFactura = ref(''); // Nuevo campo para el código de factura
const error = ref('');
const loading = ref(true);

// Atributos y valores
const rowAttributes = ref([]);
const columnAttributes = ref([]);
const quantities = ref([]);
const prices = ref([]); // Precios por combinación
const singleQuantity = ref(0); // Para manejar productos sin o con un atributo
const singlePrice = ref(0); // Precio único para productos sin o con un atributo
const productList = ref([]); // Lista para guardar los datos del proveedor y productos

// Función para obtener productos con atributos desde el backend
const fetchProductsWithAttributes = async () => {
  try {
    const response = await axios.get('/api/products');
    productos.value = response.data;
  } catch (err) {
    error.value = 'Error obteniendo productos: ' + err.message;
    console.error(err);
  }
};

// Configura los atributos y valores para el producto seleccionado
const setupAttributesAndValues = () => {
  if (selectedProducto.value) {
    const attributeValues = selectedProducto.value.attribute_values;

    if (attributeValues.length === 0) {
      // No hay atributos, usa un input único para la cantidad total y precio
      rowAttributes.value = [];
      columnAttributes.value = [];
      singleQuantity.value = 0; // Reinicia la cantidad
      singlePrice.value = 0; // Reinicia el precio
      quantities.value = []; // Vacía la matriz
      prices.value = []; // Vacía la matriz de precios
    } else if (attributeValues.length === 1) {
      // Un solo atributo, usar input único para la cantidad total y precio
      rowAttributes.value = attributeValues;
      columnAttributes.value = [];
      singleQuantity.value = 0; // Reinicia la cantidad
      singlePrice.value = 0; // Reinicia el precio
      quantities.value = [[0]]; // Inicializa con un solo valor
      prices.value = [[0]]; // Inicializa con un solo precio
    } else {
      // Separar los valores de atributos en dos conjuntos si hay más de un atributo
      rowAttributes.value = attributeValues.filter(attr => attr.name === 'S' || attr.name === 'M' || attr.name === 'X' || attr.name === 'L');
      columnAttributes.value = attributeValues.filter(attr => attr.name === 'ROJO' || attr.name === 'AZUL');

      // Generar matriz de cantidades y precios
      generateQuantitiesMatrix();
      generatePricesMatrix();
    }
  }
};

// Generar matriz de cantidades
const generateQuantitiesMatrix = () => {
  quantities.value = Array(rowAttributes.value.length)
    .fill(0)
    .map(() => Array(columnAttributes.value.length).fill(0));
};

// Generar matriz de precios
const generatePricesMatrix = () => {
  prices.value = Array(rowAttributes.value.length)
    .fill(0)
    .map(() => Array(columnAttributes.value.length).fill(0));
};

// Función para obtener la lista de proveedores
const fetchSuppliers = async () => {
  try {
    const response = await axios.get('/api/compras/odoo/suppliers');
    proveedores.value = response.data;
  } catch (err) {
    error.value = 'Error obteniendo proveedores: ' + err.message;
    console.error(err);
  } finally {
    loading.value = false;
  }
};

// Función para calcular el subtotal basado en precios
const calculateSubtotal = (product) => {
  let total = 0;

  if (product.quantities.length > 0) {
    product.quantities.forEach((row, rowIndex) => {
      row.forEach((qty, colIndex) => {
        total += qty * product.prices[rowIndex][colIndex];
      });
    });
  } else {
    total = product.singleQuantity * product.singlePrice;
  }

  return total;
};

// Función para calcular el total de los precios para la tabla de productos registrados
const calculateTotalPrice = (product) => {
  let totalPrice = 0;

  if (product.prices.length > 0) {
    product.prices.forEach((row) => {
      row.forEach((price) => {
        totalPrice += price;
      });
    });
  } else {
    totalPrice = product.singlePrice;
  }

  return totalPrice;
};

// Función para guardar la lista de productos registrados
const saveProductList = () => {
  if (!selectedProveedor || !selectedProducto) {
    alert('Por favor, selecciona un proveedor y un producto antes de guardar.');
    return;
  }

  const newProduct = {
    proveedor: selectedProveedor.value,
    producto: selectedProducto.value,
    attributes: {
      rows: rowAttributes.value.map(attr => attr.name),
      columns: columnAttributes.value.map(attr => attr.name)
    },
    quantities: quantities.value.map(row => [...row]),
    prices: prices.value.map(row => [...row]),
    singleQuantity: singleQuantity.value,
    singlePrice: singlePrice.value,
    subtotal: 0
  };

  newProduct.subtotal = calculateSubtotal(newProduct);

  productList.value.push(newProduct);
};

// Función para editar un producto
const editProduct = (index) => {
  const product = productList.value[index];
  selectedProveedor.value = product.proveedor;
  selectedProducto.value = product.producto;
  rowAttributes.value = product.attributes.rows.map(name => ({ name }));
  columnAttributes.value = product.attributes.columns.map(name => ({ name }));
  quantities.value = JSON.parse(JSON.stringify(product.quantities)); // Clonar los arrays
  prices.value = JSON.parse(JSON.stringify(product.prices)); // Clonar los arrays
  singleQuantity.value = product.singleQuantity;
  singlePrice.value = product.singlePrice;
};

// Función para eliminar un producto
const deleteProduct = (index) => {
  productList.value.splice(index, 1);
  alert('Producto eliminado!');
};

// Función para mostrar la lista de productos y el proveedor en consola
const showProductListInConsole = () => {
  const completeData = {
    proveedor: selectedProveedor.value,
    codigoFactura: codigoFactura.value,
    productos: productList.value
  };

  console.log('Datos completos:', completeData);
};

// Función para registrar la orden de compra
const registerPurchaseOrder = async () => {
  if (!selectedProveedor || !productList.value.length || !codigoFactura.value) {
    alert('Por favor, completa todos los campos necesarios antes de registrar.');
    return;
  }

  // Preparar los datos para el envío
  const orderData = {
    proveedor_id: selectedProveedor.value.id,
    codigo_factura: codigoFactura.value,
    productos: productList.value.map(product => ({
      producto_id: product.producto.id,
      cantidad: product.singleQuantity > 0 ? product.singleQuantity : product.quantities.reduce((sum, row) => sum + row.reduce((a, b) => a + b, 0), 0),
      precio: product.singlePrice > 0 ? product.singlePrice : product.prices.reduce((sum, row) => sum + row.reduce((a, b) => a + b, 0), 0)
    }))
  };

  try {
    const response = await axios.post('/api/compras/registrar-orden', orderData);
    alert('Orden de compra registrada exitosamente: ' + response.data.order_id);
    console.log('Orden registrada:', response.data);
  } catch (error) {
    console.error('Error registrando orden de compra:', error);
    alert('Error registrando la orden de compra.');
  }
};

// Monitorear cambios en el producto seleccionado
watch(selectedProducto, setupAttributesAndValues);

// Inicializar al montar el componente
onMounted(() => {
  fetchSuppliers();
  fetchProductsWithAttributes();
});
</script>

<template>
  <AppLayout title="Proveedores y Productos">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Compras
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h1>Compras</h1>
          <div v-if="error" class="text-red-500">{{ error }}</div>
          <div v-if="loading">Cargando...</div>

          <div v-else>
            <!-- Selector de Proveedores y campo de Código de Factura -->
            <div class="flex justify-between items-center mb-6">
              <div class="flex-1 mr-4">
                <label for="proveedor-select" class="block mb-2 font-semibold">Selecciona un Proveedor:</label>
                <select v-model="selectedProveedor" id="proveedor-select" class="w-full p-2 border rounded">
                  <option value="">Selecciona un proveedor</option>
                  <option v-for="proveedor in proveedores" :key="proveedor.id" :value="proveedor">
                    {{ proveedor.name }}
                  </option>
                </select>
              </div>
              <div class="flex-1">
                <label for="codigo-factura" class="block mb-2 font-semibold">Código de Factura:</label>
                <input v-model="codigoFactura" type="text" id="codigo-factura" class="w-full p-2 border rounded" placeholder="Ingrese el código de factura" />
              </div>
            </div>

            <div class="mb-6">
              <strong>Proveedor Seleccionado: </strong> {{ selectedProveedor ? selectedProveedor.name : 'Ninguno' }}
            </div>

            <!-- Selector de Productos -->
            <label for="producto-select" class="block mb-2 font-semibold">Selecciona un Producto:</label>
            <select v-model="selectedProducto" @change="setupAttributesAndValues" id="producto-select" class="w-full mb-4 p-2 border rounded">
              <option value="">Selecciona un producto</option>
              <option v-for="producto in productos" :key="producto.id" :value="producto">
                {{ producto.name }}
              </option>
            </select>

            <div class="mb-6">
              <strong>Producto Seleccionado: </strong> {{ selectedProducto ? selectedProducto.name : 'Ninguno' }}
            </div>

            <!-- Mostrar cantidad única y precio para productos sin o con un solo atributo -->
            <div v-if="selectedProducto && (rowAttributes.length === 0 || rowAttributes.length === 1)">
              <label class="block mb-2 font-semibold">
                {{ rowAttributes.length === 0 ? 'Cantidad Total (Sin Atributos)' : 'Cantidad Total (Un Atributo)' }}
              </label>
              <input v-model.number="singleQuantity" type="number" class="table-input w-full mb-4" placeholder="Cantidad Total" />
              
              <label class="block mb-2 font-semibold">
                {{ rowAttributes.length === 0 ? 'Precio Unitario' : 'Precio Unitario (Un Atributo)' }}
              </label>
              <input v-model.number="singlePrice" type="number" class="table-input w-full mb-4" placeholder="Precio Unitario" />
            </div>

            <!-- Tablas de atributos, cantidades y precios -->
            <div v-if="selectedProducto && rowAttributes.length > 1 && columnAttributes.length > 0" class="table-container">
              <!-- Tabla de Cantidades -->
              <table class="custom-table">
                <thead>
                  <tr>
                    <th class="blue-bg">Cantidad</th>
                    <th v-for="(colAttr, index) in columnAttributes" :key="'col-' + index" class="yellow-bg">{{ colAttr.name }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(rowAttr, rowIndex) in rowAttributes" :key="'row-' + rowIndex">
                    <td class="yellow-bg">{{ rowAttr.name }}</td>
                    <td v-for="(colAttr, colIndex) in columnAttributes" :key="'cell-' + rowIndex + '-' + colIndex">
                      <input v-model.number="quantities[rowIndex][colIndex]" type="number" class="table-input" placeholder="Cantidad" />
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- Tabla de Precios -->
              <table class="custom-table mt-4">
                <thead>
                  <tr>
                    <th class="blue-bg">Precio</th>
                    <th v-for="(colAttr, index) in columnAttributes" :key="'price-col-' + index" class="yellow-bg">{{ colAttr.name }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(rowAttr, rowIndex) in rowAttributes" :key="'price-row-' + rowIndex">
                    <td class="yellow-bg">{{ rowAttr.name }}</td>
                    <td v-for="(colAttr, colIndex) in columnAttributes" :key="'price-cell-' + rowIndex + '-' + colIndex">
                      <input v-model.number="prices[rowIndex][colIndex]" type="number" class="table-input" placeholder="Precio" />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Botón para guardar la lista de productos -->
            <button @click="saveProductList" class="mt-4 px-4 py-2 bg-blue-500 text-white font-semibold rounded hover:bg-blue-700">
              Guardar Productos
            </button>

            <!-- Tabla de productos registrados -->
            <div v-if="productList.length > 0" class="mt-8">
              <h3 class="font-semibold mb-4">Productos Registrados:</h3>
              <table class="custom-table w-full">
                <thead>
                  <tr>
                    <th>Producto</th>
                    <th>Atributos</th>
                    <th>Cantidad</th>
                    <th>Total Precio</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(product, index) in productList" :key="'registered-' + index">
                    <td>{{ product.producto.name }}</td>
                    <td>
                      <span v-if="product.attributes.rows.length > 0">
                        {{ product.attributes.rows.join(', ') }} -
                      </span>
                      {{ product.attributes.columns.join(', ') }}
                    </td>
                    <td>
                      <div v-if="product.quantities.length > 0">
                        <span v-for="(row, rowIndex) in product.quantities" :key="'row-' + rowIndex">
                          <span v-for="(qty, colIndex) in row" :key="'col-' + colIndex">
                            {{ qty }}
                          </span>
                        </span>
                      </div>
                      <div v-else>
                        {{ product.singleQuantity }}
                      </div>
                    </td>
                    <td>
                      {{ calculateTotalPrice(product) }}
                    </td>
                    <td>{{ product.subtotal }}</td>
                    <td>
                      <button @click="editProduct(index)" class="px-2 py-1 bg-yellow-500 text-white font-semibold rounded hover:bg-yellow-700 mr-2">
                        Editar
                      </button>
                      <button @click="deleteProduct(index)" class="px-2 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-700">
                        Eliminar
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Botón para mostrar datos en consola -->
            <button @click="showProductListInConsole" class="mt-4 px-4 py-2 bg-green-500 text-white font-semibold rounded hover:bg-green-700">
              Mostrar Datos
            </button>

            <!-- Botón para registrar orden de compra -->
            <button @click="registerPurchaseOrder" class="mt-4 ml-2 px-4 py-2 bg-purple-500 text-white font-semibold rounded hover:bg-purple-700">
              Registrar Orden de Compra
            </button>

          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style>
.table-container {
  display: flex;
  justify-content: space-around;
  align-items: flex-start;
  gap: 20px;
  margin-top: 20px;
}

.custom-table {
  border-collapse: collapse;
  width: auto;
  margin: 20px 0;
}

.custom-table th,
.custom-table td {
  border: 1px solid #000;
  padding: 8px;
  text-align: center;
  font-weight: bold;
}

.blue-bg {
  background-color: #ADD8E6;
}

.yellow-bg {
  background-color: #FFFF00;
}

.custom-table tbody tr td {
  height: 50px;
}

.table-input {
  width: 80%;
  padding: 5px;
  text-align: center;
  border: 1px solid #ddd;
  font-size: 14px;
  box-sizing: border-box;
}

.w-full {
  width: 100%;
}

.mb-4 {
  margin-bottom: 1rem;
}

.mb-6 {
  margin-bottom: 1.5rem;
}

.mt-8 {
  margin-top: 2rem;
}

.block {
  display: block;
}

.p-2 {
  padding: 0.5rem;
}

.border {
  border: 1px solid #ddd;
}

.rounded {
  border-radius: 0.25rem;
}

.font-semibold {
  font-weight: 600;
}

.mt-4 {
  margin-top: 1rem;
}

.bg-blue-500 {
  background-color: #4299e1;
}

.hover\:bg-blue-700:hover {
  background-color: #2b6cb0;
}

.bg-green-500 {
  background-color: #48bb78;
}

.hover\:bg-green-700:hover {
  background-color: #2f855a;
}

.bg-purple-500 {
  background-color: #805AD5;
}

.hover\:bg-purple-700:hover {
  background-color: #6B46C1;
}

.text-white {
  color: white;
}

.bg-yellow-500 {
  background-color: #ecc94b;
}

.hover\:bg-yellow-700:hover {
  background-color: #d69e2e;
}

.bg-red-500 {
  background-color: #f56565;
}

.hover\:bg-red-700:hover {
  background-color: #c53030;
}

.mr-2 {
  margin-right: 0.5rem;
}

.ml-2 {
  margin-left: 0.5rem;
}

.flex {
  display: flex;
}

.justify-between {
  justify-content: space-between;
}

.items-center {
  align-items: center;
}

.flex-1 {
  flex: 1;
}

.mr-4 {
  margin-right: 1rem;
}
</style>
