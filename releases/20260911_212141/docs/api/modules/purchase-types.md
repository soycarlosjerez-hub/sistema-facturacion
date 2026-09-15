# Purchase Types

Tipos de compra (normal, urgente, consigna, etc.) con configuración básica.

---

## Endpoint Index

### Listar Tipos de Compra

**`GET /api/purchase-types`**

Retorna tipos de compra ordenados por nombre.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `per_page` | `integer` | Ítems por página (default: 15) |

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
      "nombre": "Normal",
      "created_at": "2024-01-01T08:00:00.000000Z",
      "updated_at": "2024-01-01T08:00:00.000000Z"
    },
    {
      "id": 2,
      "nombre": "Urgente",
      "created_at": "2024-01-01T08:00:00.000000Z",
      "updated_at": "2024-01-01T08:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 2,
    "per_page": 15,
    "total": 2
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Tipo de Compra

**`POST /api/purchase-types`**

Crea un nuevo tipo de compra.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Consigna"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | **Sí** | Nombre del tipo de compra |

**Validations:**

```
nombre: required|string|max:255
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 3,
    "nombre": "Consigna",
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Tipo de Compra

**`GET /api/purchase-types/{purchaseType}`**

Retorna un tipo de compra individual.

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
    "nombre": "Normal",
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-01T08:00:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Tipo de Compra

**`PUT /api/purchase-types/{purchaseType}`**
**`PATCH /api/purchase-types/{purchaseType}`**

Actualiza parcialmente un tipo de compra.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Normal - Estándar"
}
```

**Campos aceptados:** Mismo que Store (todos opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "nombre": "Normal - Estándar",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Tipo de Compra

**`DELETE /api/purchase-types/{purchaseType}`**

Elimina un tipo de compra.

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

- Los tipos de compra se ordenan por nombre
- Son usados en órdenes de compra para clasificar la prioridad
