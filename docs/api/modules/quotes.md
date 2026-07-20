# Quotes

Cotizaciones convertibles en ventas con folio, vigencia y detalles de productos.

---

## Endpoint Index

### Listar Cotizaciones

**`GET /api/quotes`**

Retorna cotizaciones con cliente, sucursal y detalles.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `cliente_id` | `integer` | Filtrar por cliente |
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `estado` | `string` | Estado de la cotización |
| `search` | `string` | Buscar por folio o referencia |

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
      "folio": "COT-2024-001",
      "cliente_id": 15,
      "sucursal_id": 1,
      "user_id": 5,
      "fecha": "2024-01-20T10:00:00.000000Z",
      "vigencia": "2024-02-20",
      "subtotal": 50000.00,
      "impuestos": 9000.00,
      "descuento": 2500.00,
      "total": 56500.00,
      "estado": "pendiente",
      "notas": "",
      "cliente": { "id": 15, "nombre": "Distribuciones Ortiz" },
      "sucursal": { "id": 1, "nombre": "Matriz" },
      "detalles_count": 3,
      "created_at": "2024-01-20T10:00:00.000000Z",
      "updated_at": "2024-01-20T10:00:00.000000Z"
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

### Crear Cotización

**`POST /api/quotes`**

Crea una nueva cotización con detalles.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "folio": "COT-2024-002",
  "cliente_id": 15,
  "sucursal_id": 1,
  "user_id": 5,
  "fecha": "2024-01-21T10:00:00.000000Z",
  "vigencia": "2024-02-21",
  "subtotal": 50000.00,
  "impuestos": 9000.00,
  "descuento": 2500.00,
  "total": 56500.00,
  "estado": "pendiente",
  "notas": "Precio válido por 30 días",
  "detalles": [
    {
      "producto_id": 15,
      "cantidad": 100,
      "precio_unitario": 120.00,
      "subtotal": 12000.00
    },
    {
      "producto_id": 22,
      "cantidad": 50,
      "precio_unitario": 760.00,
      "subtotal": 38000.00
    }
  ]
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `folio` | `string` | **Sí** | Folio de cotización |
| `cliente_id` | `integer` | **Sí** | ID de cliente (existe) |
| `sucursal_id` | `integer` | **Sí** | ID de sucursal (existe) |
| `user_id` | `integer` | **Sí** | ID del usuario (existe) |
| `fecha` | `datetime` | **Sí** | Fecha de emisión |
| `vigencia` | `date` | **Sí** | Fecha de vencimiento |
| `subtotal` | `decimal` | **Sí** | Subtotal (≥ 0) |
| `impuestos` | `decimal` | **Sí** | Impuestos (≥ 0) |
| `descuento` | `decimal` | No | Descuento (≥ 0) |
| `total` | `decimal` | **Sí** | Total (≥ 0) |
| `estado` | `string` | **Sí** | Estado: `pendiente`, `aceptada`, `rechazada` |
| `notas` | `string` | No | Notas |
| `detalles` | `array` | **Sí** | Líneas de productos |

**Validations:**

```
folio: required|string|max:255
cliente_id: required|exists:clientes,id
sucursal_id: required|exists:sucursales,id
user_id: required|exists:users,id
fecha: required|date
vigencia: required|date|after:fecha
subtotal: required|numeric|min:0
impuestos: required|numeric|min:0
total: required|numeric|min:0
estado: required|in:pendiente,aceptada,rechazada
detalles: required|array|min:1
detalles.*.producto_id: required|exists:productos,id
detalles.*.cantidad: required|numeric|min:0.01
detalles.*.precio_unitario: required|numeric|min:0
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 9,
    "folio": "COT-2024-002",
    "cliente_id": 15,
    "sucursal_id": 1,
    "fecha": "2024-01-21T10:00:00.000000Z",
    "vigencia": "2024-02-21",
    "subtotal": 50000.00,
    "total": 56500.00,
    "estado": "pendiente",
    "detalles": [
      { "id": 1001, "producto": { "id": 15, "nombre": "Cerveza Corona 355ml" }, "cantidad": 100, "precio_unitario": 120.00 }
    ],
    "created_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Cotización

**`GET /api/quotes/{quote}`**

Retorna una cotización con detalles completos.

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
    "folio": "COT-2024-001",
    "cliente_id": 15,
    "sucursal_id": 1,
    "user_id": 5,
    "fecha": "2024-01-20T10:00:00.000000Z",
    "vigencia": "2024-02-20",
    "subtotal": 50000.00,
    "impuestos": 9000.00,
    "descuento": 2500.00,
    "total": 56500.00,
    "estado": "pendiente",
    "notas": "",
    "cliente": { "id": 15, "nombre": "Distribuciones Ortiz" },
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "usuario": { "id": 5, "name": "Carlos Martínez" },
    "detalles": [
      { "id": 1, "producto": { "id": 15, "nombre": "Cerveza Corona 355ml" }, "cantidad": 100, "precio_unitario": 120.00 }
    ],
    "created_at": "2024-01-20T10:00:00.000000Z",
    "updated_at": "2024-01-20T10:00:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Cotización

**`PUT /api/quotes/{quote}`**
**`PATCH /api/quotes/{quote}`**

Actualiza parcialmente una cotización.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "estado": "aceptada",
  "notas": "Cotización aprobada por el cliente"
}
```

**Campos aceptados:** Mismos que Store (todos opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "estado": "aceptada",
    "notas": "Cotización aprobada por el cliente",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Cotización

**`DELETE /api/quotes/{quote}`**

Elimina una cotización.

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

- Las cotizaciones pueden convertirse en ventas cuando son aceptadas
- `vigencia` establece cuándo expira el precio cotizado
- Estados posibles: `pendiente`, `aceptada`, `rechazada`
- Los detalles incluyen productos y precios cotizados
