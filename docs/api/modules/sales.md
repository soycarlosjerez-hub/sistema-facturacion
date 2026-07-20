# Sales

Ventas con facturación fiscal dominicana (NCF), comprobantes, pagos y resolución automática de clientes.

---

## Endpoint Resumen

| Endpoint | Método | Ruta | Descripción |
|----------|--------|------|-------------|
| Index | `GET` | `/api/sales` | Listar ventas con filtros avanzados |
| Store | `POST` | `/api/sales` | Registrar venta con NCF y detalles |
| Show | `GET` | `/api/sales/{sale}` | Detalle de venta con pagos y detalles |
| Update | `PUT/PATCH` | `/api/sales/{sale}` | Actualizar venta |
| Destroy | `DELETE` | `/api/sales/{sale}` | Anular venta |
| Resumen | `GET` | `/api/sales/resumen` | Dashboard de métricas de ventas |

---

## Endpoint Index

### Listar Ventas

**`GET /api/sales`**

Retorna ventas con cliente, sucursal, tipo comprobante y detalles.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `cliente_id` | `integer` | Filtrar por cliente |
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `estado` | `string` | Estado de la venta |
| `fecha_desde` | `date` | Fecha inicial (YYYY-MM-DD) |
| `fecha_hasta` | `date` | Fecha final (YYYY-MM-DD) |
| `search_ncf` | `string` | Buscar por NCF |
| `min_total` | `decimal` | Monto mínimo |
| `max_total` | `decimal` | Monto máximo |

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
      "id": 1042,
      "ncf": "B0100000042",
      "ncf_tipo": "Factura de Crédito Fiscal",
      "tipo_comprobante": "Factura",
      "encf": "B0100000042",
      "cliente": {
        "id": 15,
        "nombre": "Distribuciones Ortiz",
        "rnc_cedula": "13012345678",
        "tipo_cliente": "credito_fiscal"
      },
      "sucursal": {
        "id": 1,
        "nombre": "Matriz"
      },
      "usuario": {
        "id": 5,
        "name": "Carlos Martínez"
      },
      "caja": {
        "id": 1,
        "nombre": "Caja Principal"
      },
      "tipo_venta": {
        "id": 1,
        "nombre": "Contado"
      },
      "subtotal": 25000.00,
      "impuestos": 4500.00,
      "descuento": 0.00,
      "propina": 0.00,
      "cargo_servicio": 0.00,
      "total": 29500.00,
      "estado": "completada",
      "tipo_orden": "mostrador",
      "notas": "",
      "fecha": "2024-01-28T14:22:00.000000Z",
      "detalles_count": 5,
      "pagos_count": 1
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 1,
    "per_page": 15,
    "total": 1
  },
  "links": {}
}
```

---

## Endpoint Store

### Registrar Venta

**`POST /api/sales`**

Registra una nueva venta con NCF, detalles y resolución automática de cliente.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "ncf": "B0100000042",
  "ncf_tipo": "Factura de Crédito Fiscal",
  "tipo_comprobante": "Factura",
  "encf": "B0100000042",
  "cliente_id": null,
  "cliente_nombre": "Consumidor Final",
  "cliente_telefono": "",
  "cliente_email": "",
  "cliente_rnc_cedula": "00000000000",
  "tipo_cliente": "consumo_final",
  "tipo_venta_id": 1,
  "sucursal_id": 1,
  "caja_id": 1,
  "fecha": "2024-01-28T14:22:00.000000Z",
  "subtotal": 25000.00,
  "impuestos": 4500.00,
  "descuento": 0.00,
  "propina": 0.00,
  "cargo_servicio": 0.00,
  "total": 29500.00,
  "estado": "completada",
  "tipo_orden": "mostrador",
  "notas": "",
  "detalles": [
    {
      "producto_id": 15,
      "cantidad": 2,
      "precio_unitario": 120.00,
      "itbis_porcentaje": 18,
      "subtotal": 240.00,
      "impuesto": 43.20
    },
    {
      "producto_id": 22,
      "cantidad": 1,
      "precio_unitario": 24760.00,
      "itbis_porcentaje": 18,
      "subtotal": 24760.00,
      "impuesto": 4456.80
    }
  ]
}
```

**Campos Principales:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `ncf` | `string` | **Sí** | Número de NCF fiscal |
| `ncf_tipo` | `string` | **Sí** | Tipo de comprobante fiscal |
| `tipo_comprobante` | `string` | **Sí** | Clase de comprobante |
| `encf` | `string` | No | NCF extendido |
| `cliente_id` | `integer` | No | ID de cliente existente |
| `cliente_nombre` | `string` | **Sí** | Nombre del cliente (auto-create si no existe) |
| `cliente_telefono` | `string` | No | Teléfono del cliente |
| `cliente_email` | `string` | No | Email del cliente |
| `cliente_rnc_cedula` | `string` | No | RNC/Cédula del cliente |
| `tipo_cliente` | `string` | **Sí** | `consumo_final`, `credito_fiscal`, `especial` |
| `tipo_venta_id` | `integer` | No | ID de tipo de venta |
| `sucursal_id` | `integer` | **Sí** | Sucursal donde se realiza la venta |
| `caja_id` | `integer` | No | Caja registradora |
| `fecha` | `datetime` | **Sí** | Fecha/hora de la venta |
| `subtotal` | `decimal` | **Sí** | Subtotal antes de impuestos |
| `impuestos` | `decimal` | **Sí** | Total de impuestos |
| `descuento` | `decimal` | No | Descuento aplicado |
| `propina` | `decimal` | No | Propina |
| `cargo_servicio` | `decimal` | No | Cargo por servicio |
| `total` | `decimal` | **Sí** | Total final |
| `estado` | `string` | **Sí** | Estado de la venta |
| `tipo_orden` | `string` | No | Tipo de orden: `mostrador`, `delivery`, `pickup` |
| `notas` | `string` | No | Notas internas |
| `detalles` | `array` | **Sí** | Array de detalles de venta |

**Campos de Detalles:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `producto_id` | `integer` | **Sí** | ID del producto (existe) |
| `cantidad` | `decimal` | **Sí** | Cantidad vendida (> 0) |
| `precio_unitario` | `decimal` | **Sí** | Precio unitario (≥ 0) |
| `itbis_porcentaje` | `decimal` | No | % ITbis (default: 18) |
| `subtotal` | `decimal` | No | Calculado automáticamente |
| `impuesto` | `decimal` | No | Calculado automáticamente |

**Validations Clave:**

```
ncf: required|string|max:255
ncf_tipo: required|string|max:255
tipo_comprobante: required|string|max:255
cliente_nombre: required|string|max:255
tipo_cliente: required|in:consumo_final,credito_fiscal,especial
sucursal_id: required|exists:sucursales,id
subtotal: required|numeric|min:0
impuestos: required|numeric|min:0
total: required|numeric|min:0
estado: required|string|max:255
tipo_orden: nullable|in:mostrador,delivery,pickup
detalles: required|array|min:1
detalles.*.producto_id: required|exists:productos,id
detalles.*.cantidad: required|numeric|min:0.01
detalles.*.precio_unitario: required|numeric|min:0
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 1043,
    "ncf": "B0100000043",
    "ncf_tipo": "Factura de Crédito Fiscal",
    "tipo_comprobante": "Factura",
    "cliente": {
      "id": 16,
      "nombre": "Consumidor Final",
      "rnc_cedula": "00000000000",
      "tipo_cliente": "consumo_final"
    },
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "usuario": { "id": 5, "name": "Carlos Martínez" },
    "subtotal": 25000.00,
    "impuestos": 4500.00,
    "total": 29500.00,
    "estado": "completada",
    "tipo_orden": "mostrador",
    "fecha": "2024-01-28T14:22:00.000000Z",
    "detalles": [
      {
        "id": 5001,
        "producto": { "id": 15, "nombre": "Cerveza Corona 355ml" },
        "cantidad": 2,
        "precio_unitario": 120.00,
        "subtotal": 240.00,
        "impuesto": 43.20
      },
      {
        "id": 5002,
        "producto": { "id": 22, "nombre": "Arroz Premium 5kg" },
        "cantidad": 1,
        "precio_unitario": 24760.00,
        "subtotal": 24760.00,
        "impuesto": 4456.80
      }
    ],
    "created_at": "2024-01-28T14:22:00.000000Z"
  },
  "message": "Venta registrada exitosamente"
}
```

**Nota sobre Resolución Automática de Cliente:**
Si se envía `cliente_rnc_cedula` o `cliente_telefono` o `cliente_email`, el sistema busca un cliente existente. Si no encuentra coincidencia, crea uno automáticamente con el `cliente_nombre` proporcionado.

---

## Endpoint Show

### Detalle de Venta

**`GET /api/sales/{sale}`**

Retorna una venta completa con detalles, pagos y relación del cliente.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1042,
    "ncf": "B0100000042",
    "ncf_tipo": "Factura de Crédito Fiscal",
    "tipo_comprobante": "Factura",
    "encf": "B0100000042",
    "cliente": {
      "id": 15,
      "nombre": "Distribuciones Ortiz",
      "rnc_cedula": "13012345678",
      "email": "info@distribucionesortiz.com",
      "telefono": "+1-809-555-9999",
      "tipo_cliente": "credito_fiscal",
      "direccion": "Av. Duarte #123"
    },
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "usuario": { "id": 5, "name": "Carlos Martínez" },
    "caja": { "id": 1, "nombre": "Caja Principal" },
    "tipo_venta": { "id": 1, "nombre": "Contado" },
    "subtotal": 25000.00,
    "impuestos": 4500.00,
    "descuento": 0.00,
    "propina": 0.00,
    "cargo_servicio": 0.00,
    "total": 29500.00,
    "estado": "completada",
    "tipo_orden": "mostrador",
    "notas": "",
    "fecha": "2024-01-28T14:22:00.000000Z",
    "detalles": [
      {
        "id": 4001,
        "producto": { "id": 15, "nombre": "Cerveza Corona 355ml", "precio": 120.00 },
        "cantidad": 2,
        "precio_unitario": 120.00,
        "itbis_porcentaje": 18,
        "subtotal": 240.00,
        "impuesto": 43.20
      }
    ],
    "pagos": [
      {
        "id": 1001,
        "metodo_pago": "efectivo",
        "monto": 29500.00,
        "fecha": "2024-01-28T14:22:00.000000Z"
      }
    ],
    "created_at": "2024-01-28T14:22:00.000000Z",
    "updated_at": "2024-01-28T14:22:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Venta

**`PUT /api/sales/{sale}`**
**`PATCH /api/sales/{sale}`**

Actualiza parcialmente una venta. Todos los campos son opcionales.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "notas": "Pedido especial - sin cebolla",
  "descuento": 500.00
}
```

**Campos aceptados:** Mismos que Store (todos `sometimes`/opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1042,
    "notas": "Pedido especial - sin cebolla",
    "descuento": 500.00,
    "total": 29000.00,
    "updated_at": "2024-01-28T15:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Anular Venta

**`DELETE /api/sales/{sale}`**

Anula una venta y revierte el stock.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
{
  "message": "Venta anulada exitosamente"
}
```

---

## Special Endpoints

### Resumen de Ventas (Dashboard)

**`GET /api/sales/resumen`**

Retorna métricas agregadas de ventas para dashboard.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `periodo` | `string` | `dia`, `semana`, `mes`, `anio` (default: `dia`) |
| `fecha_desde` | `date` | Fecha inicial |
| `fecha_hasta` | `date` | Fecha final |

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
{
  "data": {
    "total_ventas": 156,
    "total_ingresos": 487500.00,
    "total_subtotal": 411344.54,
    "total_impuestos": 76155.46,
    "total_descuentos": 2500.00,
    "promedio_ticket": 3125.00,
    "ventas_por_estado": {
      "completada": 148,
      "cancelada": 5,
      "pendiente": 3
    },
    "ventas_por_metodo_pago": {
      "efectivo": 285000.00,
      "tarjeta": 175000.00,
      "mixto": 27500.00
    },
    "top_productos": [
      { "producto_id": 15, "nombre": "Cerveza Corona 355ml", "cantidad_vendida": 320, "ingresos": 38400.00 },
      { "producto_id": 22, "nombre": "Arroz Premium 5kg", "cantidad_vendida": 150, "ingresos": 371400.00 }
    ]
  }
}
```

---

## Notas

- Los NCF son obligatorios para facturación fiscal en República Dominicana
- El sistema resuelve automáticamente el cliente por RNC/Cédula, teléfono o email
- Si no se encuentra cliente, se crea uno nuevo como "Consumidor Final"
- Los detalles de venta afectan el stock del producto automáticamente
- `tipo_orden` distingue entre `mostrador`, `delivery` y `pickup`
- El resumen incluye desglose por estado, método de pago y top productos
