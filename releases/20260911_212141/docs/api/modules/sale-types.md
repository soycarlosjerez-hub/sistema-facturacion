# Sale Types

Tipos de venta (contado, crédito, etc.) con configuración básica.

---

## Endpoint Index

### Listar Tipos de Venta

**`GET /api/sale-types`**

Retorna tipos de venta ordenados por nombre.

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
      "nombre": "Contado",
      "descripcion": "Pago inmediato en efectivo o tarjeta",
      "created_at": "2024-01-01T08:00:00.000000Z",
      "updated_at": "2024-01-01T08:00:00.000000Z"
    },
    {
      "id": 2,
      "nombre": "Crédito",
      "descripcion": "Pago diferido con plazo acordado",
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

### Crear Tipo de Venta

**`POST /api/sale-types`**

Crea un nuevo tipo de venta.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Fiado",
  "descripcion": "Venta a crédito sin documento formal"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | **Sí** | Nombre del tipo de venta |
| `descripcion` | `string` | No | Descripción |

**Validations:**

```
nombre: required|string|max:255
descripcion: nullable|string
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 3,
    "nombre": "Fiado",
    "descripcion": "Venta a crédito sin documento formal",
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Tipo de Venta

**`GET /api/sale-types/{saleType}`**

Retorna un tipo de venta individual.

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
    "nombre": "Contado",
    "descripcion": "Pago inmediato en efectivo o tarjeta",
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-01T08:00:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Tipo de Venta

**`PUT /api/sale-types/{saleType}`**
**`PATCH /api/sale-types/{saleType}`**

Actualiza parcialmente un tipo de venta.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Contado - Efectivo/Tarjeta",
  "descripcion": "Pago inmediato en efectivo, tarjeta o transferencia"
}
```

**Campos aceptados:** Mismos que Store (todos opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "nombre": "Contado - Efectivo/Tarjeta",
    "descripcion": "Pago inmediato en efectivo, tarjeta o transferencia",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Tipo de Venta

**`DELETE /api/sale-types/{saleType}`**

Elimina un tipo de venta.

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

- Los tipos de venta se ordenan por nombre
- Son usados en transacciones de ventas para clasificar el método de pago
- Se recomienda no eliminar tipos de venta con ventas asociadas
