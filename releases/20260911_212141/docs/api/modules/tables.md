# Tables

Mesas para restaurantes con categorías, estados y asignación por sucursal.

---

## Endpoint Index

### Listar Mesas

**`GET /api/tables`**

Retorna mesas con su categoría, sucursal y estado actual.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `categoria_id` | `integer` | Filtrar por categoría |
| `activa` | `boolean` | Filtrar por estado activo |
| `search` | `string` | Buscar por número o nombre |

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
      "numero": 1,
      "nombre": "Mesa 1 - Ventana",
      "sucursal_id": 1,
      "categoria_id": 1,
      "capacidad": 4,
      "activa": true,
      "tenant_id": 1,
      "sucursal": { "id": 1, "nombre": "Matriz" },
      "categoria": { "id": 1, "nombre": "Interior", "color": "#4ECDC4" },
      "estado_actual": "libre",
      "created_at": "2024-01-01T08:00:00.000000Z",
      "updated_at": "2024-01-20T14:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 1,
    "per_page": 15,
    "total": 15
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Mesa

**`POST /api/tables`**

Crea una nueva mesa para restaurante.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "numero": 16,
  "nombre": "Mesa 16 - Terraza",
  "sucursal_id": 1,
  "categoria_id": 2,
  "capacidad": 6,
  "activa": true
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `numero` | `integer` | **Sí** | Número de la mesa |
| `nombre` | `string` | No | Nombre descriptivo |
| `sucursal_id` | `integer` | **Sí** | ID de sucursal (existe) |
| `categoria_id` | `integer` | No | ID de categoría de mesa |
| `capacidad` | `integer` | No | Capacidad de personas (≥ 1) |
| `activa` | `boolean` | No | Estado activo (default: `true`) |

**Validations:**

```
numero: required|integer|min:1
nombre: nullable|string|max:255
sucursal_id: required|exists:sucursales,id
categoria_id: nullable|exists:categories,id
capacidad: nullable|integer|min:1
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 16,
    "numero": 16,
    "nombre": "Mesa 16 - Terraza",
    "sucursal_id": 1,
    "categoria_id": 2,
    "capacidad": 6,
    "activa": true,
    "tenant_id": 1,
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Mesa

**`GET /api/tables/{table}`**

Retorna una mesa con estado actual y reservas.

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
    "numero": 1,
    "nombre": "Mesa 1 - Ventana",
    "sucursal_id": 1,
    "categoria_id": 1,
    "capacidad": 4,
    "activa": true,
    "tenant_id": 1,
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "categoria": { "id": 1, "nombre": "Interior", "color": "#4ECDC4" },
    "estado_actual": "libre",
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-20T14:30:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Mesa

**`PUT /api/tables/{table}`**
**`PATCH /api/tables/{table}`**

Actualiza parcialmente una mesa. Todos los campos son opcionales.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "capacidad": 6,
  "nombre": "Mesa 1 - Ventana VIP"
}
```

**Campos aceptados:** Mismos que Store (todos `sometimes`/opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "capacidad": 6,
    "nombre": "Mesa 1 - Ventana VIP",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Mesa

**`DELETE /api/tables/{table}`**

Elimina una mesa.

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

- `numero` identifica la mesa dentro de la sucursal
- `categoria_id` agrupa mesas por zona (interior, terraza, barra, etc.)
- `capacidad` se usa para sugerir mesas según el número de comensales
- `estado_actual` se actualiza dinámicamente según reservas y órdenes activas
- Las mesas inactivas (`activa = false`) no aparecen en selecciones
