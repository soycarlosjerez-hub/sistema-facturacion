# Warehouses

Almacenes para gestión de inventario por ubicación física.

---

## Endpoint Index

### Listar Almacenes

**`GET /api/warehouses`**

Retorna almacenes con su sucursal asociada.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `search` | `string` | Buscar por nombre |

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
      "nombre": "Almacén Central",
      "ubicacion": "Sector trasero",
      "sucursal_id": 1,
      "tenant_id": 1,
      "sucursal": { "id": 1, "nombre": "Matriz" },
      "created_at": "2024-01-01T08:00:00.000000Z",
      "updated_at": "2024-01-20T14:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 1,
    "per_page": 15,
    "total": 2
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Almacén

**`POST /api/warehouses`**

Crea un nuevo almacén.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Almacén Secundario",
  "ubicacion": "Edificio B, Piso 1",
  "sucursal_id": 2
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | **Sí** | Nombre del almacén |
| `ubicacion` | `string` | No | Ubicación física |
| `sucursal_id` | `integer` | **Sí** | ID de sucursal (existe) |

**Validations:**

```
nombre: required|string|max:255
ubicacion: nullable|string|max:500
sucursal_id: required|exists:sucursales,id
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 3,
    "nombre": "Almacén Secundario",
    "ubicacion": "Edificio B, Piso 1",
    "sucursal_id": 2,
    "tenant_id": 1,
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Almacén

**`GET /api/warehouses/{warehouse}`**

Retorna un almacén con su sucursal.

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
    "nombre": "Almacén Central",
    "ubicacion": "Sector trasero",
    "sucursal_id": 1,
    "tenant_id": 1,
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-20T14:30:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Almacén

**`PUT /api/warehouses/{warehouse}`**
**`PATCH /api/warehouses/{warehouse}`**

Actualiza parcialmente un almacén. Todos los campos son opcionales.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Almacén Central - Ampliado",
  "ubicacion": "Sector trasero, Ala norte"
}
```

**Campos aceptados:** Mismos que Store (todos `sometimes`/opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "nombre": "Almacén Central - Ampliado",
    "ubicacion": "Sector trasero, Ala norte",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Almacén

**`DELETE /api/warehouses/{warehouse}`**

Elimina un almacén.

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

- Cada almacén pertenece a una sucursal específica
- `ubicacion` describe la ubicación física dentro de la sucursal
- Los almacenes se usan para gestionar inventario por ubicación
- Las compras pueden dirigirse a un almacén específico
