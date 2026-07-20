# Orders API

Complex restaurant orders module with permission-gated endpoints for managing customer orders, payments, and line items.

## Base URL

```
/api/orders
```

## Authentication

Requires authentication with `auth` session cookie. Each endpoint enforces specific permissions.

---

## GET /api/orders

List orders scoped to the authenticated user's branch (`deSucursal()`). Loads `detalles.producto`, `cliente`, `usuario`, and `terminal` relationships.

### Permissions

`permission:ordenes.view`

### Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `tipo` | string | Optional | Filter by order type (`mostrador`, `delivery`, `pickup`) — maps to `tipo_orden` |
| `estado` | string | Optional | Filter by order status |
| `cliente_id` | integer | Optional | Filter by customer ID |
| `fecha` | date | Optional | Filter by order date (YYYY-MM-DD) |
| `page` | integer | Optional | Page number (default: 1) |

### Response

`200 OK`

```json
{
  "data": [
    {
      "id": 1,
      "tipo_orden": "mostrador",
      "estado": "pendiente",
      "cliente_id": 5,
      "cliente": {
        "id": 5,
        "nombre": "María López",
        "telefono": "+1-809-555-0100"
      },
      "usuario_id": 3,
      "usuario": {
        "id": 3,
        "nombre": "Carlos Ruiz"
      },
      "terminal_id": 2,
      "terminal": {
        "id": 2,
        "nombre": "Terminal Caja 2"
      },
      "subtotal": 1250.00,
      "total": 1375.00,
      "notas": "Sin cebolla",
      "created_at": "2026-07-20T14:00:00.000000Z",
      "updated_at": "2026-07-20T14:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "last_page": 3,
    "from": 1,
    "to": 15,
    "total": 42
  }
}
```

---

## POST /api/orders

Create a new order with automatic client resolution. Uses a database transaction + `OrdenService`.

### Permissions

`permission:ordenes.create`

### Request Body

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `tipo_orden` | string | Yes | In: `mostrador`, `delivery`, `pickup` |
| `cliente_id` | integer | No | Used for auto-client resolution |
| `cliente_nombre` | string | No | Customer name |
| `cliente_telefono` | string | No | Customer phone |
| `cliente_email` | string | No | Customer email |
| `cliente_rnc_cedula` | string | No | Customer RNC/Cédula |
| `tipo_cliente` | string | No | Customer type |
| `entrega_empresa_id` | integer | No | Delivery company — must exist |
| `direccion_entrega` | string | No | Delivery address |
| `telefono_contacto` | string | No | Contact phone for delivery |
| `hora_retiro` | string | No | Pickup time |
| `notas` | string | No | Order notes |
| `nombre_cliente` | string | No | Alias for customer name |
| `correo_electronico` | string | No | Alias for customer email |
| `items` | array | No | Array of order line items |

#### Items Array Structure

Each item in `items`:

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `producto_id` | integer | Yes | Product ID |
| `cantidad` | integer | Yes | Quantity |
| `notas` | string | No | Item-specific notes |
| `curso` | string | No | Course type — In: `entrada`, `fuerte`, `postre`, `bebida` |

### Example Request

```json
{
  "tipo_orden": "mostrador",
  "cliente_nombre": "Juan Pérez",
  "cliente_telefono": "+1-809-555-0100",
  "notas": "Sin hielo",
  "items": [
    {
      "producto_id": 12,
      "cantidad": 2,
      "curso": "fuerte"
    },
    {
      "producto_id": 8,
      "cantidad": 1,
      "notas": "Extra salsa",
      "curso": "entrada"
    }
  ]
}
```

### Response

`201 Created`

```json
{
  "data": {
    "id": 100,
    "tipo_orden": "mostrador",
    "estado": "pendiente",
    "cliente_id": 50,
    "cliente": {
      "id": 50,
      "nombre": "Juan Pérez",
      "telefono": "+1-809-555-0100"
    },
    "usuario_id": 3,
    "terminal_id": 2,
    "subtotal": 2500.00,
    "total": 2750.00,
    "notas": "Sin hielo",
    "created_at": "2026-07-20T14:00:00.000000Z",
    "updated_at": "2026-07-20T14:00:00.000000Z"
  }
}
```

---

## GET /api/orders/{orden}

Show a single order with `detalles.producto`, `cliente`, `usuario`, `terminal`, `pagos`, and `entregaEmpresa` relationships loaded.

### Permissions

`permission:ordenes.view`

### Response

`200 OK`

```json
{
  "data": {
    "id": 100,
    "tipo_orden": "mostrador",
    "estado": "pendiente",
    "cliente_id": 50,
    "cliente": {
      "id": 50,
      "nombre": "Juan Pérez",
      "telefono": "+1-809-555-0100"
    },
    "usuario_id": 3,
    "usuario": {
      "id": 3,
      "nombre": "Carlos Ruiz"
    },
    "terminal_id": 2,
    "terminal": {
      "id": 2,
      "nombre": "Terminal Caja 2"
    },
    "entrega_empresa_id": null,
    "entregaEmpresa": null,
    "subtotal": 2500.00,
    "total": 2750.00,
    "notas": "Sin hielo",
    "detalles": [
      {
        "id": 200,
        "producto_id": 12,
        "producto": {
          "id": 12,
          "nombre": "Pollo Guisado",
          "precio_venta": 1250.00
        },
        "cantidad": 2,
        "notas": "",
        "curso": "fuerte",
        "subtotal": 2500.00
      }
    ],
    "pagos": [],
    "created_at": "2026-07-20T14:00:00.000000Z",
    "updated_at": "2026-07-20T14:00:00.000000Z"
  }
}
```

---

## PUT /api/orders/{orden}

Update client information and order fields. Recalculates subtotal and total.

### Permissions

`permission:ordenes.update`

### Request Body

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `cliente_nombre` | string | No | Update customer name |
| `cliente_telefono` | string | No | Update customer phone |
| `cliente_email` | string | No | Update customer email |
| `cliente_rnc_cedula` | string | No | Update customer RNC/Cédula |
| `tipo_cliente` | string | No | Update customer type |
| `entrega_empresa_id` | integer | No | Update delivery company |
| `direccion_entrega` | string | No | Update delivery address |
| `telefono_contacto` | string | No | Update contact phone |
| `hora_retiro` | string | No | Update pickup time |
| `notas` | string | No | Update order notes |
| `tipo_orden` | string | No | Update order type |

### Response

`200 OK`

```json
{
  "data": {
    "id": 100,
    "tipo_orden": "delivery",
    "estado": "pendiente",
    "cliente_id": 50,
    "cliente": {
      "id": 50,
      "nombre": "Juan Pérez",
      "telefono": "+1-809-555-0100"
    },
    "entrega_empresa_id": 5,
    "direccion_entrega": "Calle Principal #45",
    "telefono_contacto": "+1-809-555-0200",
    "subtotal": 2500.00,
    "total": 2750.00,
    "notas": "Sin hielo",
    "updated_at": "2026-07-20T15:00:00.000000Z"
  }
}
```

---

## PATCH /api/orders/{orden}

Partially update an order. Same body fields as PUT.

### Permissions

`permission:ordenes.update`

### Response

`200 OK`

---

## DELETE /api/orders/{orden}

Annul/cancel an order via `OrdenService->anular()`.

### Permissions

`permission:ordenes.cancel`

### Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `motivo` | string | Optional | Reason for cancellation |

### Example Request

```
DELETE /api/orders/100?motivo=Cliente_canceló_ordena
```

### Response

`200 OK`

```json
{
  "message": "Orden anulada correctamente"
}
```

---

## POST /api/orders/{orden}/pay

Process payment for an order.

### Permissions

`permission:ordenes.pay`

### Request Body

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `metodo_pago` | string | Yes | In: `efectivo`, `tarjeta`, `transferencia`, `mixto`, `fiado` |
| `monto_recibido` | float | Conditional | Amount received (for efectivo/mixto) |
| `monto_tarjeta` | float | Conditional | Card amount (for tarjeta/mixto) |
| `monto_transferencia` | float | Conditional | Transfer amount (for transferencia/mixto) |
| `propina` | float | No | Tip amount |
| `cargo_servicio` | boolean | No | Service charge flag |

### Example Request

```json
{
  "metodo_pago": "efectivo",
  "monto_recibido": 3000.00,
  "propina": 200.00,
  "cargo_servicio": true
}
```

### Response

`200 OK`

```json
{
  "data": {
    "order_id": 100,
    "payment_id": 50,
    "metodo_pago": "efectivo",
    "monto_pagado": 2750.00,
    "cambio": 250.00,
    "propina": 200.00,
    "mensaje": "Pago procesado exitosamente"
  }
}
```

---

## POST /api/orders/{orden}/details

Add a line item to an existing order.

### Permissions

`permission:ordenes.create`

### Request Body

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `producto_id` | integer | Yes | Must exist in database |
| `cantidad` | integer | Yes | Minimum 1 |
| `notas` | string | No | Item-specific notes |
| `curso` | string | No | In: `entrada`, `fuerte`, `postre`, `bebida` |

### Example Request

```json
{
  "producto_id": 15,
  "cantidad": 1,
  "notas": "Sin picante",
  "curso": "postre"
}
```

### Response

`201 Created`

```json
{
  "data": {
    "id": 201,
    "orden_id": 100,
    "producto_id": 15,
    "producto": {
      "id": 15,
      "nombre": "Tres Leches"
    },
    "cantidad": 1,
    "notas": "Sin picante",
    "curso": "postre",
    "subtotal": 450.00,
    "created_at": "2026-07-20T15:30:00.000000Z"
  }
}
```

---

## PATCH /api/orders/{orden}/details/{detalle}

Update the quantity of a line item.

### Permissions

`permission:ordenes.create`

### Request Body

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `cantidad` | integer | Yes | Minimum 1 |

### Example Request

```json
{
  "cantidad": 3
}
```

### Response

`200 OK`

```json
{
  "data": {
    "id": 200,
    "orden_id": 100,
    "producto_id": 12,
    "cantidad": 3,
    "subtotal": 3750.00,
    "updated_at": "2026-07-20T15:30:00.000000Z"
  }
}
```

---

## DELETE /api/orders/{orden}/details/{detalle}

Remove a line item from an order.

### Permissions

`permission:ordenes.create`

### Response

`200 OK`

```json
{
  "message": "Detalle eliminado correctamente"
}
```

---

## Field Reference

### Order Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Order ID |
| `tipo_orden` | string | Order type: `mostrador`, `delivery`, `pickup` |
| `estado` | string | Order status |
| `cliente_id` | integer | Associated customer ID |
| `cliente` | object | Nested customer object |
| `usuario_id` | integer | Staff member who created the order |
| `usuario` | object | Nested staff object |
| `terminal_id` | integer | POS terminal used |
| `terminal` | object | Nested terminal object |
| `entrega_empresa_id` | integer | Delivery company (for delivery orders) |
| `entregaEmpresa` | object | Nested delivery company |
| `subtotal` | float | Subtotal before taxes/tips |
| `total` | float | Grand total |
| `notas` | string | General order notes |
| `created_at` | datetime | Order creation timestamp |
| `updated_at` | datetime | Last update timestamp |

### Order Detail Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Line item ID |
| `orden_id` | integer | Parent order ID |
| `producto_id` | integer | Product ID |
| `producto` | object | Nested product object |
| `cantidad` | integer | Quantity ordered |
| `notas` | string | Item-specific notes |
| `curso` | string | Course type: `entrada`, `fuerte`, `postre`, `bebida` |
| `subtotal` | float | Line item subtotal |

### Permission Matrix

| Endpoint | Permission |
|----------|-----------|
| `GET /api/orders` | `permission:ordenes.view` |
| `POST /api/orders` | `permission:ordenes.create` |
| `GET /api/orders/{orden}` | `permission:ordenes.view` |
| `PUT/PATCH /api/orders/{orden}` | `permission:ordenes.update` |
| `DELETE /api/orders/{orden}` | `permission:ordenes.cancel` |
| `POST /api/orders/{orden}/pay` | `permission:ordenes.pay` |
| `POST /api/orders/{orden}/details` | `permission:ordenes.create` |
| `PATCH /api/orders/{orden}/details/{detalle}` | `permission:ordenes.create` |
| `DELETE /api/orders/{orden}/details/{detalle}` | `permission:ordenes.create` |
