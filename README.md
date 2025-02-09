# 📌 Extracción de API con Laravel y Vue para Gestión de Productos y Reposiciones

## 🚀 Introducción
Este proyecto implementa la extracción de datos desde una API utilizando **Laravel** en el backend y **Vue.js** en el frontend. Se centra en la gestión de productos y reposiciones, permitiendo extraer información de productos mediante referencias y administrar su almacenamiento.

## 🛠️ Tecnologías Utilizadas

- **Laravel** (PHP 8+) - Backend
- **Vue.js 3** - Frontend
- **Axios** - Consumo de API
- **Inertia.js** - Integración entre Laravel y Vue
- **TailwindCSS** - Estilos
- **MySQL** - Base de Datos
- **Filament** (Opcional) - Panel de administración

---

## ⚙️ Configuración y Instalación

### 🔹 1. Clonar el Repositorio
```sh
    git clone https://github.com/tu-usuario/tu-repositorio.git
    cd tu-repositorio
```

### 🔹 2. Instalar Dependencias
#### Backend (Laravel)
```sh
    composer install
    cp .env.example .env
    php artisan key:generate
```

Configurar las credenciales de la base de datos en **.env**:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=usuario
DB_PASSWORD=contraseña
```

```sh
    php artisan migrate --seed
```

#### Frontend (Vue)
```sh
    npm install
    npm run dev
```

---

## 🔥 Funcionalidades Principales

### 📦 Gestión de Productos
- Registrar productos en la base de datos.
- Extraer información de productos desde la API de Odoo.
- Obtener detalles completos del producto por código de referencia.
- Generar códigos de barras y códigos QR para cada producto.

### 📌 Gestión de Reposiciones
- Listar productos que necesitan reposición.
- Extraer información de reposiciones desde Odoo.
- Actualizar inventario automáticamente con la API.

---

## 🔄 API Endpoints

### 🔹 Productos
| Método | Endpoint                  | Descripción |
|--------|---------------------------|-------------|
| `GET`  | `/api/productos`          | Listar todos los productos |
| `GET`  | `/api/productos/{id}`     | Obtener un producto por ID |
| `POST` | `/api/productos`          | Crear un nuevo producto |
| `PUT`  | `/api/productos/{id}`     | Actualizar un producto |
| `DELETE` | `/api/productos/{id}`   | Eliminar un producto |

### 🔹 Reposiciones
| Método | Endpoint                        | Descripción |
|--------|---------------------------------|-------------|
| `GET`  | `/api/reposiciones`             | Listar reposiciones |
| `GET`  | `/api/reposiciones/{ref}`       | Obtener reposición por referencia |
| `POST` | `/api/reposiciones`             | Crear una nueva reposición |

---

## ⚡ Consumo de API con Axios en Vue

### Ejemplo de Extracción de Producto por Código de Referencia
```javascript
import axios from 'axios';

const obtenerProducto = async (codigo) => {
    try {
        const response = await axios.get(`/api/productos/${codigo}`);
        console.log(response.data);
    } catch (error) {
        console.error('Error al obtener el producto:', error);
    }
};
```

### Ejemplo de Creación de Producto desde Vue
```javascript
const nuevoProducto = async (productoData) => {
    try {
        const response = await axios.post('/api/productos', productoData);
        console.log('Producto creado:', response.data);
    } catch (error) {
        console.error('Error al crear producto:', error);
    }
};
```

---

## 📚 Documentación Adicional
Si necesitas más información, revisa la documentación oficial:
- [Laravel Docs](https://laravel.com/docs)
- [Vue.js Docs](https://vuejs.org/)
- [Axios Docs](https://axios-http.com/)
- [Inertia.js Docs](https://inertiajs.com/)

---

## 🎯 Contribución
Si deseas contribuir, sigue estos pasos:
1. **Fork** el repositorio 🍴
2. Crea una nueva **branch**: `git checkout -b feature/nueva-feature`
3. Realiza **commit** de tus cambios: `git commit -m "Agregando nueva feature"`
4. Sube tus cambios: `git push origin feature/nueva-feature`
5. Abre un **Pull Request** 🛠️

---
