# Business Types

Tipos de negocio (restaurante, tienda, cafetería, etc.) con configuración de módulos, campos extra y comportamiento de soft delete.

---

## Endpoint Index

### Listar Tipos de Negocio

**`GET /api/business-types`**

Retorna todos los tipos de negocio activos ordenados por `orden`, con sus módulos cargados.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `per_page` | `integer` | Ítems por página (default: 15) |
| `with_categories` | `boolean` | Incluir conteo de categorías asociadas |

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
      "key": "restaurant",
      "slug": "restaurante",
      "nombre": "Restaurante",
      "descripcion": "Negocio de comida con menú y mesas",
      "color": "#E74C3C",
      "color_default": "#E74C3C",
      "icon": "🍽️",
      "icono_default": "🍽️",
      "activo": true,
      "orden": 1,
      "campos_extra": ["mesa_id", "tipo_orden"],
      "soft_delete_default": true,
      "modules": [
        { "id": 1, "key": "restaurant", "nombre": "Restaurante", "pivot_visible": true, "pivot_orden": 1 },
        { "id": 2, "key": "inventory", "nombre": "Inventario", "pivot_visible": true, "pivot_orden": 2 }
      ],
      "categories_count": 12,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-15T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 1,
    "per_page": 15,
    "total": 5
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Tipo de Negocio

**`POST /api/business-types`**

Crea un nuevo tipo de negocio con módulos y configuración personalizada.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "key": "cloud-kitchen",
  "slug": "cocina-virtual",
  "nombre": "Cocina Virtual",
  "descripcion": "Negocio de comida para delivery sin mesas físicas",
  "color": "#9B59B6",
  "icon": "📦",
  "activo": true,
  "orden": 4,
  "campos_extra": ["direccion_entrega", "tiempo_estimado"],
  "soft_delete_default": true
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `key` | `string` | **Sí** | Identificador único (snake_case, letras/números/guiones) |
| `slug` | `string` | **Sí** | Slug amigable (único) |
| `nombre` | `string` | **Sí** | Nombre legible |
| `descripcion` | `string` | No | Descripción extensa |
| `color` | `string` | No | Color HEX principal |
| `color_default` | `string` | No | Color HEX por defecto para items |
| `icon` | `string` | No | Icono emoji |
| `icono_default` | `string` | No | Icono por defecto |
| `activo` | `boolean` | No | Estado activo (default: `true`) |
| `orden` | `integer` | No | Posición en lista (default: 0) |
| `campos_extra` | `array` | No | Campos adicionales para este tipo |
| `soft_delete_default` | `boolean` | No | Usar soft delete por defecto (default: `false`) |

**Validations:**

```
key: required|string|unique:business_types,key
slug: required|string|unique:business_types,slug
nombre: required|string|max:255
color: nullable|regex:/^#[0-9A-Fa-f]{6}$/
orden: integer|min:0
campos_extra: array
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 6,
    "key": "cloud-kitchen",
    "slug": "cocina-virtual",
    "nombre": "Cocina Virtual",
    "descripcion": "Negocio de comida para delivery sin mesas físicas",
    "color": "#9B59B6",
    "activo": true,
    "orden": 4,
    "campos_extra": ["direccion_entrega", "tiempo_estimado"],
    "soft_delete_default": true,
    "modules": [],
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

**Nota:** Al crear, actualizar o eliminar, se limpia automáticamente el caché de tipos de negocio.

---

## Endpoint Show

### Obtener Tipo de Negocio

**`GET /api/business-types/{businessType}`**

Retorna un tipo de negocio individual con sus módulos cargados.

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
    "key": "restaurant",
    "slug": "restaurante",
    "nombre": "Restaurante",
    "descripcion": "Negocio de comida con menú y mesas",
    "color": "#E74C3C",
    "color_default": "#E74C3C",
    "icon": "🍽️",
    "icono_default": "🍽️",
    "activo": true,
    "orden": 1,
    "campos_extra": ["mesa_id", "tipo_orden"],
    "soft_delete_default": true,
    "modules": [
      { "id": 1, "key": "restaurant", "nombre": "Restaurante", "pivot_visible": true, "pivot_orden": 1 },
      { "id": 2, "key": "inventory", "nombre": "Inventario", "pivot_visible": true, "pivot_orden": 2 },
      { "id": 3, "key": "reservations", "nombre": "Reservaciones", "pivot_visible": true, "pivot_orden": 3 }
    ],
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-15T10:00:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Tipo de Negocio

**`PUT /api/business-types/{businessType}`**
**`PATCH /api/business-types/{businessType}`**

Actualiza parcialmente un tipo de negocio. Todos los campos son opcionales.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Restaurante Premium",
  "color": "#C0392B",
  "descripcion": "Restaurante de alta gama"
}
```

**Campos aceptados:** Mismos que Store (todos `sometimes`/opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "key": "restaurant",
    "nombre": "Restaurante Premium",
    "color": "#C0392B",
    "descripcion": "Restaurante de alta gama",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Tipo de Negocio

**`DELETE /api/business-types/{businessType}`**

Elimina un tipo de negocio. Verifica que no tenga categorías asociadas.

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

**Response `422 Unprocessable Entity` (si tiene categorías):**

```json
{
  "message": "Cannot delete business type. It has associated categories.",
  "errors": {
    "_global": ["Este tipo de negocio tiene categorías asociadas."]
  }
}
```

---

## Notas

- Los tipos de negocio definen qué **módulos** están disponibles para una instancia
- Cada instancia de negocio (`business_instances`) selecciona qué módulos mostrar por tipo
- El campo `key` es el identificador técnico usado internamente (no editable tras creación)
- `campos_extra` define campos personalizados que aparecen en formularios para ese tipo
- `soft_delete_default` determina si los items creados bajo este tipo usan soft delete
- El caché se invalida automáticamente en `store`, `update` y `destroy`
