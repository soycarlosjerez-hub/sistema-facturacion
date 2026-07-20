# Categories

Gestión de categorías de productos con soporte para tipos de negocio, ordenamiento personalizado y asociación dinámica.

---

## Endpoint Index

### Listar Categorías

**`GET /api/categories`**

Retorna una lista paginada de categorías con sus tipos de negocio asociados, conteo de productos y mesas.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `type` | `string` | Filtrar por tipo de negocio (`restaurant`, `retail`, etc.) |
| `activa` | `boolean` | Filtrar por estado activo/inactivo |
| `search` | `string` | Búsqueda por nombre |
| `sort_by` | `string` | Campo de ordenamiento (default: `orden`) |
| `sort_dir` | `string` | Dirección: `asc` o `desc` (default: `asc`) |

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
      "id": 1,
      "nombre": "Bebidas",
      "descripcion": "Todas las bebidas disponibles",
      "activa": true,
      "color": "#FF6B6B",
      "icono": "🥤",
      "orden": 1,
      "configuracion": {},
      "type_keys": ["restaurant", "cafe"],
      "business_types": [
        { "id": 1, "key": "restaurant", "nombre": "Restaurante" },
        { "id": 3, "key": "cafe", "nombre": "Cafetería" }
      ],
      "product_count": 24,
      "table_count": 0,
      "created_at": "2024-01-10T08:00:00.000000Z",
      "updated_at": "2024-01-20T12:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 1,
    "per_page": 15,
    "total": 8
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Categoría

**`POST /api/categories`**

Crea una nueva categoría. Opcionalmente se asocian tipos de negocio mediante `type_keys`.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Postres",
  "descripcion": "Postres y dulces",
  "activa": true,
  "color": "#4ECDC4",
  "icono": "🍰",
  "orden": 5,
  "configuracion": {
    "mostrar_en_menu": true,
    "destacado": false
  },
  "type_keys": ["restaurant"],
  "type_configs": {
    "restaurant": {
      "mostrar_en_kds": true,
      "grupo_cocina": "postres"
    }
  }
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | **Sí** | Nombre de la categoría (único por tenant) |
| `descripcion` | `string` | No | Descripción larga |
| `activa` | `boolean` | No | Estado activo/inactivo (default: `true`) |
| `color` | `string` | No | Color HEX para UI (#RRGGBB) |
| `icono` | `string` | No | Emoji o icono representativo |
| `orden` | `integer` | No | Posición en lista (default: 0) |
| `configuracion` | `object` | No | Configuración genérica JSON |
| `type_keys` | `string[]` | No | Array de keys de business types a asociar |
| `type_configs` | `object` | No | Configuración específica por tipo de negocio |

**Validations:**

```
nombre: required|string|max:255|unique:categories,nombre
activa: boolean
color: regex:/^#[0-9A-Fa-f]{6}$/
orden: integer|min:0
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 9,
    "nombre": "Postres",
    "descripcion": "Postres y dulces",
    "activa": true,
    "color": "#4ECDC4",
    "icono": "🍰",
    "orden": 5,
    "configuracion": {
      "mostrar_en_menu": true,
      "destacado": false
    },
    "type_keys": ["restaurant"],
    "business_types": [
      { "id": 1, "key": "restaurant", "nombre": "Restaurante" }
    ],
    "product_count": 0,
    "table_count": 0,
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Categoría

**`GET /api/categories/{category}`**

Retorna una categoría individual con relaciones cargadas.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "nombre": "Bebidas",
    "descripcion": "Todas las bebidas disponibles",
    "activa": true,
    "color": "#FF6B6B",
    "icono": "🥤",
    "orden": 1,
    "configuracion": {},
    "type_keys": ["restaurant", "cafe"],
    "business_types": [
      { "id": 1, "key": "restaurant", "nombre": "Restaurante" },
      { "id": 3, "key": "cafe", "nombre": "Cafetería" }
    ],
    "products": [
      { "id": 15, "nombre": "Coca-Cola 500ml", "precio": 80.00 },
      { "id": 22, "nombre": "Agua Mineral 1L", "precio": 45.00 }
    ],
    "tables": [],
    "created_at": "2024-01-10T08:00:00.000000Z",
    "updated_at": "2024-01-20T12:30:00.000000Z"
  }
}
```

**Response `404 Not Found`:**

```json
{
  "message": "Not Found"
}
```

---

## Endpoint Update

### Actualizar Categoría

**`PUT /api/categories/{category}`**
**`PATCH /api/categories/{category}`**

Actualiza parcialmente una categoría. Todos los campos son opcionales.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body (ejemplo parcial):**

```json
{
  "nombre": "Bebidas Frías",
  "color": "#3498DB",
  "orden": 2
}
```

**Campos aceptados:** Mismos que Store (todos `sometimes`/opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "nombre": "Bebidas Frías",
    "descripcion": "Todas las bebidas disponibles",
    "activa": true,
    "color": "#3498DB",
    "icono": "🥤",
    "orden": 2,
    "configuracion": {},
    "type_keys": ["restaurant", "cafe"],
    "business_types": [
      { "id": 1, "key": "restaurant", "nombre": "Restaurante" },
      { "id": 3, "key": "cafe", "nombre": "Cafetería" }
    ],
    "product_count": 24,
    "table_count": 0,
    "created_at": "2024-01-10T08:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Categoría

**`DELETE /api/categories/{category}`**

Elimina una categoría. Verifica que no tenga productos ni mesas asociadas.

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

**Response `422 Unprocessable Entity` (si tiene productos o mesas):**

```json
{
  "message": "Cannot delete category. It has associated products or tables.",
  "errors": {
    "_global": ["Esta categoría tiene productos o mesas asociadas."]
  }
}
```

---

## Special Endpoints

### Toggle Activa

Cambiar el estado activo/inactivo de una categoría.

**`PATCH /api/categories/{category}/toggle-activa`**

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "nombre": "Bebidas",
    "activa": false,
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Toggle activated successfully"
}
```

---

### Reordenar Categorías

Reordenar múltiples categorías en una sola petición.

**`POST /api/categories/reorder`**

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "items": [
    { "id": 3, "orden": 0 },
    { "id": 1, "orden": 1 },
    { "id": 5, "orden": 2 },
    { "id": 2, "orden": 3 }
  ]
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `items[].id` | `integer` | **Sí** | ID de la categoría (debe existir) |
| `items[].orden` | `integer` | **Sí** | Nueva posición (≥ 0) |

**Response `200 OK`:**

```json
{
  "message": "Categories reordered successfully"
}
```

---

### Asociar/Desasociar Tipo de Negocio

Asociar o desasociar un tipo de negocio a/from una categoría.

**`POST /api/categories/{category}/type`**

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "type_key": "restaurant",
  "action": "attach",
  "configuracion": {
    "mostrar_en_kds": true,
    "grupo_cocina": "bebidas"
  },
  "soft_delete_enabled": true
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `type_key` | `string` | **Sí** | Key del business type (debe existir) |
| `action` | `string` | **Sí** | `"attach"` o `"detach"` |
| `configuracion` | `object` | No | Configuración específica para este tipo |
| `soft_delete_enabled` | `boolean` | No | Habilitar soft delete para este tipo |

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "nombre": "Bebidas",
    "type_keys": ["restaurant", "cafe"],
    "business_types": [
      { "id": 1, "key": "restaurant", "nombre": "Restaurante" },
      { "id": 3, "key": "cafe", "nombre": "Cafetería" }
    ]
  },
  "message": "Business type associated successfully"
}
```

---

## Notas

- Las categorías pueden estar asociadas a múltiples **tipos de negocio** (restaurant, retail, cafetería, etc.)
- Cada asociación puede tener su propia **configuración** (`type_configs`)
- El campo `orden` determina la posición en listas y menús
- El borrado verifica integridad referencial contra `productos` y `mesas`
- Los colores usan formato HEX (#RRGGBB)
- Los iconos típicamente son emojis para compatibilidad cross-platform
