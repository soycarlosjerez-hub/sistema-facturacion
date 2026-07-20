# Orders API

Complex restaurant orders module with permission-gated endpoints for managing customer orders, payments, and line items.

## Base URL

```
/api/orders
```

## Authentication

Requires authentication with `auth` session cookie. Each endpoint enforces specific permissions.

---

## Endpoint Index

### Listar Órdenes

**`GET /api/orders`**

Lista órdenes scopeadas a la sucursal del usuario autenticado (`deSucursal()`). Carga `detalles.producto`, `cliente`, `usuario`, y `terminal`.

**Permissions:**

`permission:ordenes.view`

**Query Parameters:**

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `tipo` | `string` | No | Filtrar por tipo (`mostrador`, `delivery`, `pickup`) — mapea a `tipo_orden` |
| `estado` | `string` | No | Filtrar por estado |
| `cliente_id` | `integer` | No | Filtrar por ID de cliente |
| `fecha` | `date` | No | Filtrar por fecha (YYYY-MM-DD) |
| `page` | `integer` | No | Número de página (default: 1) |

**Headers:**

```
Accept: application/json
Cookie: _session={cookie}
```

**Response `200 OK`:**

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

## Endpoint Store

### Crear Orden

**`POST /api/orders`**

Crea una nueva orden con resolución automática de cliente. Usa transacción de base de datos + `OrdenService`.

**Permissions:**

`permission:ordenes.create`

**Headers:**

```
Accept: application/json
Content-Type: application/json
Cookie: _session={cookie}
```

**Request Body:**

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

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `tipo_orden` | `string` | **Sí** | `mostrador`, `delivery`, `pickup` |
| `cliente_id` | `integer` | No | Para auto-resolución de cliente |
| `cliente_nombre` | `string` | No | Nombre del cliente |
| `cliente_telefono` | `string` | No | Teléfono del cliente |
| `cliente_email` | `string` | No | Email del cliente |
| `cliente_rnc_cedula` | `string` | No | RNC/Cédula del cliente |
| `tipo_cliente` | `string` | No | Tipo de cliente |
| `entrega_empresa_id` | `integer` | No | Empresa de delivery (existe) |
| `direccion_entrega` | `string` | No | Dirección de entrega |
| `telefono_contacto` | `string` | No | Teléfono de contacto |
| `hora_retiro` | `string` | No | Hora de retiro |
| `notas` | `string` | No | Notas de la orden |
| `nombre_cliente` | `string` | No | Alias para nombre del cliente |
| `correo_electronico` | `string` | No | Alias para email del cliente |
| `items` | `array` | No | Items de la orden |

**Items Array Structure:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `producto_id` | `integer` | **Sí** | ID del producto |
| `cantidad` | `integer` | **Sí** | Cantidad |
| `notas` | `string` | No | Notas del item |
| `curso` | `string` | No | Curso: `entrada`, `fuerte`, `postre`, `bebida` |

**Response `201 Created`:**

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

## Endpoint Show

### Obtener Orden

**`GET /api/orders/{orden}`**

Muestra una orden con `detalles.producto`, `cliente`, `usuario`, `terminal`, `pagos`, y `entregaEmpresa`.

**Permissions:**

`permission:ordenes.view`

**Headers:**

```
Accept: application/json
Cookie: _session={cookie}
```

**Response `200 OK`:**

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

## Endpoint Update

### Actualizar Orden

**`PUT /api/orders/{orden}`**
**`PATCH /api/orders/{orden}`**

Actualiza información del cliente y campos de la orden. Recalcula subtotal y total.

**Permissions:**

`permission:ordenes.update`

**Headers:**

```
Accept: application/json
Content-Type: application/json
Cookie: _session={cookie}
```

**Request Body:**

```json
{
  "cliente_nombre": "Juan Carlos Pérez",
  "entrega_empresa_id": 5,
  "direccion_entrega": "Calle Principal #45",
  "telefono_contacto": "+1-809-555-0200",
  "notas": "Sin hielo"
}
```

**Campos aceptados:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `cliente_nombre` | `string` | Actualizar nombre del cliente |
| `cliente_telefono` | `string` | Actualizar teléfono |
| `cliente_email` | `string` | Actualizar email |
| `cliente_rnc_cedula` | `string` | Actualizar RNC/Cédula |
| `tipo_cliente` | `string` | Actualizar tipo |
| `entrega_empresa_id` | `integer` | Actualizar empresa de entrega |
| `direccion_entrega` | `string` | Actualizar dirección |
| `telefono_contacto` | `string` | Actualizar teléfono de contacto |
| `hora_retiro` | `string` | Actualizar hora de retiro |
| `notas` | `string` | Actualizar notas |
| `tipo_orden` | `string` | Actualizar tipo de orden |

**Response `200 OK`:**

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

## Endpoint Destroy

### Anular Orden

**`DELETE /api/orders/{orden}`**

Anula una orden vía `OrdenService->anular()`.

**Permissions:**

`permission:ordenes.cancel`

**Query Parameters:**

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `motivo` | `string` | No | Razón de cancelación |

**Headers:**

```
Accept: application/json
Cookie: _session={cookie}
```

**Example Request:**

```
DELETE /api/orders/100?motivo=Cliente_canceló_orden
```

**Response `200 OK`:**

```json
{
  "message": "Orden anulada correctamente"
}
```

---

## Endpoint Pay

### Procesar Pago

**`POST /api/orders/{orden}/pay`**

Procesa el pago de una orden.

**Permissions:**

`permission:ordenes.pay`

**Headers:**

```
Accept: application/json
Content-Type: application/json
Cookie: _session={cookie}
```

**Request Body:**

```json
{
  "metodo_pago": "efectivo",
  "monto_recibido": 3000.00,
  "propina": 200.00,
  "cargo_servicio": true
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `metodo_pago` | `string` | **Sí** | `efectivo`, `tarjeta`, `transferencia`, `mixto`, `fiado` |
| `monto_recibido` | `float` | Condicional | Monto recibido (para efectivo/mixto) |
| `monto_tarjeta` | `float` | Condicional | Monto tarjeta (para tarjeta/mixto) |
| `monto_transferencia` | `float` | Condicional | Monto transferencia (para transferencia/mixto) |
| `propina` | `float` | No | Monto propina |
| `cargo_servicio` | `boolean` | No | Flag cargo por servicio |

**Response `200 OK`:**

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

## Endpoint Add Detail

### Agregar Item

**`POST /api/orders/{orden}/details`**

Agrega un item a una orden existente.

**Permissions:**

`permission:ordenes.create`

**Headers:**

```
Accept: application/json
Content-Type: application/json
Cookie: _session={cookie}
```

**Request Body:**

```json
{
  "producto_id": 15,
  "cantidad": 1,
  "notas": "Sin picante",
  "curso": "postre"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `producto_id` | `integer` | **Sí** | Existe en BD |
| `cantidad` | `integer` | **Sí** | Mínimo 1 |
| `notas` | `string` | No | Notas del item |
| `curso` | `string` | No | `entrada`, `fuerte`, `postre`, `bebida` |

**Response `201 Created`:**

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

## Endpoint Update Detail

### Actualizar Item

**`PATCH /api/orders/{orden}/details/{detalle}`**

Actualiza la cantidad de un item.

**Permissions:**

`permission:ordenes.create`

**Headers:**

```
Accept: application/json
Content-Type: application/json
Cookie: _session={cookie}
```

**Request Body:**

```json
{
  "cantidad": 3
}
```

**Response `200 OK`:**

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

## Endpoint Delete Detail

### Eliminar Item

**`DELETE /api/orders/{orden}/details/{detalle}`**

Elimina un item de una orden.

**Permissions:**

`permission:ordenes.create`

**Headers:**

```
Accept: application/json
Cookie: _session={cookie}
```

**Response `200 OK`:**

```json
{
  "message": "Detalle eliminado correctamente"
}
```

---

## Field Reference

### Campos de Orden

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | `integer` | ID de la orden |
| `tipo_orden` | `string` | Tipo: `mostrador`, `delivery`, `pickup` |
| `estado` | `string` | Estado de la orden |
| `cliente_id` | `integer` | ID del cliente asociado |
| `cliente` | `object` | Objeto cliente anidado |
| `usuario_id` | `integer` | Empleado que creó la orden |
| `usuario` | `object` | Objeto empleado anidado |
| `terminal_id` | `integer` | Terminal POS usada |
| `terminal` | `object` | Objeto terminal anidado |
| `entrega_empresa_id` | `integer` | Empresa de entrega |
| `entregaEmpresa` | `object` | Objeto empresa de entrega |
| `subtotal` | `float` | Subtotal sin impuestos/propinas |
| `total` | `float` | Total final |
| `notas` | `string` | Notas generales |
| `created_at` | `datetime` | Fecha creación |
| `updated_at` | `datetime` | Última actualización |

### Campos de Detalle de Orden

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | `integer` | ID del item |
| `orden_id` | `integer` | ID de la orden padre |
| `producto_id` | `integer` | ID del producto |
| `producto` | `object` | Objeto producto anidado |
| `cantidad` | `integer` | Cantidad pedida |
| `notas` | `string` | Notas específicas del item |
| `curso` | `string` | Curso: `entrada`, `fuerte`, `postre`, `bebida` |
| `subtotal` | `float` | Subtotal del item |

### Matriz de Permisos

| Endpoint | Permiso |
|----------|---------|
| `GET /api/orders` | `permission:ordenes.view` |
| `POST /api/orders` | `permission:ordenes.create` |
| `GET /api/orders/{orden}` | `permission:ordenes.view` |
| `PUT/PATCH /api/orders/{orden}` | `permission:ordenes.update` |
| `DELETE /api/orders/{orden}` | `permission:ordenes.cancel` |
| `POST /api/orders/{orden}/pay` | `permission:ordenes.pay` |
| `POST /api/orders/{orden}/details` | `permission:ordenes.create` |
| `PATCH /api/orders/{orden}/details/{detalle}` | `permission:ordenes.create` |
| `DELETE /api/orders/{orden}/details/{detalle}` | `permission:ordenes.create` |
