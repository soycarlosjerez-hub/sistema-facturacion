# Products

Catálogo de productos con gestión de inventario, ingredientes, código de barras e ITbis.

---

## Endpoint Index

### Listar Productos

**`GET /api/products`**

Retorna productos con categoría e ingredientes. Soporta filtrado por stock.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `categoria_id` | `integer` | Filtrar por categoría |
| `search` | `string` | Buscar por nombre o código de barras |
| `low_stock` | `boolean` | Productos con stock ≤ stock_minimo |
| `out_of_stock` | `boolean` | Productos sin stock (stock = 0) |

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
{
  "data": [
    {
      "id": 15,
      "categoria_id": 3,
      "nombre": "Cerveza Corona 355ml",
      "codigo_barras": "061186108314",
      "descripcion": "Cerveza mexicana importada",
      "precio": 120.00,
      "precio_compra": 75.00,
      "unidad_medida": "unidad",
      "itbis_porcentaje": 18,
      "stock": 500,
      "stock_minimo": 50,
      "imagen": "corona-355.jpg",
      "categoria": {
        "id": 3,
        "nombre": "Bebidas",
        "color": "#FF6B6B"
      },
      "ingredientes": [
        { "id": 1, "nombre": "Malta", "cantidad": 2.5, "unidad": "gramos" },
        { "id": 2, "nombre": "Lúpulo", "cantidad": 0.5, "unidad": "gramos" }
      ],
      "created_at": "2024-01-10T08:00:00.000000Z",
      "updated_at": "2024-01-20T12:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 1,
    "per_page": 15,
    "total": 47
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Producto

**`POST /api/products`**

Crea un nuevo producto con su categoría e ingredientes opcionales.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "categoria_id": 3,
  "nombre": "Cerveza Corona 355ml",
  "codigo_barras": "061186108314",
  "descripcion": "Cerveza mexicana importada",
  "precio": 120.00,
  "precio_compra": 75.00,
  "unidad_medida": "unidad",
  "itbis_porcentaje": 18,
  "stock": 500,
  "stock_minimo": 50,
  "imagen": "corona-355.jpg"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `categoria_id` | `integer` | **Sí** | ID de categoría (existe) |
| `nombre` | `string` | **Sí** | Nombre del producto |
| `codigo_barras` | `string` | No | Código de barras (único) |
| `descripcion` | `string` | No | Descripción larga |
| `precio` | `decimal` | **Sí** | Precio de venta (≥ 0) |
| `precio_compra` | `decimal` | No | Precio de costo (≥ 0) |
| `unidad_medida` | `string` | No | Unidad: `unidad`, `kg`, `lb`, `lt`, `pack` |
| `itbis_porcentaje` | `decimal` | No | % ITbis (default: 18) |
| `stock` | `integer` | No | Stock actual (≥ 0) |
| `stock_minimo` | `integer` | No | Alerta de stock mínimo (≥ 0) |
| `imagen` | `string` | No | Nombre de archivo de imagen |

**Validations:**

```
categoria_id: required|exists:categories,id
nombre: required|string|max:255
codigo_barras: nullable|string|unique:productos,codigo_barras
precio: required|numeric|min:0
precio_compra: nullable|numeric|min:0
itbis_porcentaje: nullable|numeric|min:0|max:100
stock: nullable|integer|min:0
stock_minimo: nullable|integer|min:0
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 156,
    "categoria_id": 3,
    "nombre": "Cerveza Corona 355ml",
    "codigo_barras": "061186108314",
    "descripcion": "Cerveza mexicana importada",
    "precio": 120.00,
    "precio_compra": 75.00,
    "unidad_medida": "unidad",
    "itbis_porcentaje": 18,
    "stock": 500,
    "stock_minimo": 50,
    "imagen": "corona-355.jpg",
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Producto

**`GET /api/products/{producto}`**

Retorna un producto con categoría e ingredientes cargados.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
{
  "data": {
    "id": 15,
    "categoria_id": 3,
    "nombre": "Cerveza Corona 355ml",
    "codigo_barras": "061186108314",
    "descripcion": "Cerveza mexicana importada",
    "precio": 120.00,
    "precio_compra": 75.00,
    "unidad_medida": "unidad",
    "itbis_porcentaje": 18,
    "stock": 500,
    "stock_minimo": 50,
    "imagen": "corona-355.jpg",
    "categoria": {
      "id": 3,
      "nombre": "Bebidas",
      "color": "#FF6B6B",
      "icono": "🥤"
    },
    "ingredientes": [
      { "id": 1, "nombre": "Malta", "cantidad": 2.5, "unidad": "gramos" },
      { "id": 2, "nombre": "Lúpulo", "cantidad": 0.5, "unidad": "gramos" }
    ],
    "created_at": "2024-01-10T08:00:00.000000Z",
    "updated_at": "2024-01-20T12:30:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Producto

**`PUT /api/products/{producto}`**
**`PATCH /api/products/{producto}`**

Actualiza parcialmente un producto. Todos los campos son opcionales.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "precio": 130.00,
  "stock": 450,
  "descripcion": "Cerveza mexicana 355ml - Actualizado"
}
```

**Campos aceptados:** Mismos que Store (todos `sometimes`/opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 15,
    "nombre": "Cerveza Corona 355ml",
    "precio": 130.00,
    "stock": 450,
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Producto

**`DELETE /api/products/{producto}`**

Elimina un producto del catálogo.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
{
  "message": "Deleted successfully"
}
```

---

## Notas

- `codigo_barras` debe ser único dentro del tenant
- `precio` y `precio_compra` se usan para calcular margen de ganancia
- `stock_minimo` dispara alertas cuando `stock <= stock_minimo`
- El filtro `low_stock` muestra productos donde `stock <= stock_minimo`
- El filtro `out_of_stock` muestra productos donde `stock = 0`
- Los ingredientes se almacenan en una tabla separada (`producto_ingredientes`)
- `unidad_medida` valores comunes: `unidad`, `kg`, `lb`, `lt`, `pack`, `docena`
- `itbis_porcentaje` en República Dominicana: 18% (estándar), 0% (exento), 16% (reducido)
