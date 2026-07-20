# Returns

Devoluciones vinculadas a ventas originales con NCF y detalles de productos devueltos.

---

## Endpoint Index

### Listar Devoluciones

**`GET /api/returns`**

Retorna devoluciones con venta original, cliente y detalles.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `venta_id` | `integer` | Filtrar por venta original |
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `estado` | `string` | Estado de la devolución |

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
      "venta_id": 1042,
      "cliente_id": 15,
      "sucursal_id": 1,
      "user_id": 5,
      "ncf": "ND-B0100000001",
      "fecha": "2024-01-29T10:00:00.000000Z",
      "estado": "procesada",
      "motivo": "Producto dañado",
      "total_devuelto": 240.00,
      "venta": {
        "id": 1042,
        "ncf": "B0100000042",
        "total": 29500.00
      },
      "cliente": { "id": 15, "nombre": "Distribuciones Ortiz" },
      "sucursal": { "id": 1, "nombre": "Matriz" },
      "detalles_count": 2,
      "created_at": "2024-01-29T10:00:00.000000Z",
      "updated_at": "2024-01-29T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 1,
    "per_page": 15,
    "total": 3
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Devolución

**`POST /api/returns`**

Registra una devolución vinculada a una venta original.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "venta_id": 1042,
  "cliente_id": 15,
  "sucursal_id": 1,
  "user_id": 5,
  "ncf": "ND-B0100000001",
  "fecha": "2024-01-29T10:00:00.000000Z",
  "estado": "procesada",
  "motivo": "Producto dañado",
  "total_devuelto": 240.00,
  "detalles": [
    {
      "venta_detalle_id": 4001,
      "cantidades": 2
    }
  ]
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `venta_id` | `integer` | **Sí** | ID de venta original (existe) |
| `cliente_id` | `integer` | No | ID de cliente (existe) |
| `sucursal_id` | `integer` | **Sí** | ID de sucursal (existe) |
| `user_id` | `integer` | **Sí** | ID del usuario (existe) |
| `ncf` | `string` | **Sí** | NCF de nota de débito |
| `fecha` | `datetime` | **Sí** | Fecha de devolución |
| `estado` | `string` | **Sí** | Estado: `pendiente`, `procesada`, `rechazada` |
| `motivo` | `string` | No | Motivo de la devolución |
| `total_devuelto` | `decimal` | **Sí** | Monto devuelto (≥ 0) |
| `detalles` | `array` | **Sí** | Productos devueltos |

**Campos de Detalles:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `venta_detalle_id` | `integer` | **Sí** | ID del detalle de venta (existe) |
| `cantidades` | `integer` | **Sí** | Cantidad devuelta (> 0) |

**Validations:**

```
venta_id: required|exists:ventas,id
sucursal_id: required|exists:sucursales,id
user_id: required|exists:users,id
ncf: required|string|max:255
fecha: required|date
estado: required|in:pendiente,procesada,rechazada
total_devuelto: required|numeric|min:0
detalles: required|array|min:1
detalles.*.venta_detalle_id: required|exists:ventas_detalles,id
detalles.*.cantidades: required|integer|min:1
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 4,
    "venta_id": 1042,
    "ncf": "ND-B0100000001",
    "estado": "procesada",
    "motivo": "Producto dañado",
    "total_devuelto": 240.00,
    "detalles": [
      { "id": 1, "venta_detalle_id": 4001, "cantidades": 2 }
    ],
    "created_at": "2024-01-29T10:00:00.000000Z"
  },
  "message": "Devolución registrada exitosamente"
}
```

---

## Endpoint Show

### Obtener Devolución

**`GET /api/returns/{return}`**

Retorna una devolución con detalles completos.

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
    "venta_id": 1042,
    "cliente_id": 15,
    "sucursal_id": 1,
    "user_id": 5,
    "ncf": "ND-B0100000001",
    "fecha": "2024-01-29T10:00:00.000000Z",
    "estado": "procesada",
    "motivo": "Producto dañado",
    "total_devuelto": 240.00,
    "venta": { "id": 1042, "ncf": "B0100000042" },
    "cliente": { "id": 15, "nombre": "Distribuciones Ortiz" },
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "usuario": { "id": 5, "name": "Carlos Martínez" },
    "detalles": [
      {
        "id": 1,
        "venta_detalle_id": 4001,
        "cantidades": 2,
        "venta_detalle": {
          "producto": { "id": 15, "nombre": "Cerveza Corona 355ml" },
          "precio_unitario": 120.00
        }
      }
    ],
    "created_at": "2024-01-29T10:00:00.000000Z",
    "updated_at": "2024-01-29T10:00:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Devolución

**`PUT /api/returns/{return}`**
**`PATCH /api/returns/{return}`**

Actualiza parcialmente una devolución.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "estado": "rechazada",
  "motivo": "Producto no cumple condiciones de devolución"
}
```

**Campos aceptados:** Mismos que Store (todos opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "estado": "rechazada",
    "motivo": "Producto no cumple condiciones de devolución",
    "updated_at": "2024-01-29T11:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Devolución

**`DELETE /api/returns/{return}`**

Elimina una devolución.

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

- Las devoluciones generan automáticamente una Nota de Débito (NCF tipo ND)
- `venta_detalle_id` referencia el detalle específico de la venta original
- `cantidades` indica cuántas unidades de ese detalle se devuelven
- El stock se reponga automáticamente al marcar como "procesada"
- Estados posibles: `pendiente`, `procesada`, `rechazada`
