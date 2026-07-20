# Purchases

Órdenes de compra a proveedores con retenciones ISR e ITbis, detalles de líneas y seguimiento.

---

## Endpoint Index

### Listar Compras

**`GET /api/purchases`**

Retorna órdenes de compra con proveedor, sucursal, almacén y detalles.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `proveedor_id` | `integer` | Filtrar por proveedor |
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `estado` | `string` | Estado de la compra |
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
  "data": [
    {
      "id": 101,
      "proveedor_id": 5,
      "sucursal_id": 1,
      "almacen_id": 1,
      "user_id": 5,
      "tipo_compra_id": 1,
      "total": 125000.00,
      "subtotal": 105932.20,
      "itbis_total": 19067.80,
      "fecha": "2024-01-28T10:00:00.000000Z",
      "observaciones": "Compra semanal de abastecimiento",
      "aplica_retencion_isr": true,
      "aplica_retencion_itbis": true,
      "retencion_isr": 2000.00,
      "retencion_itbis": 1906.78,
      "folio": "OC-2024-0101",
      "estado": "recibida",
      "proveedor": {
        "id": 5,
        "nombre": "Importadora Nacional",
        "rnc_cedula": "13098765432"
      },
      "sucursal": { "id": 1, "nombre": "Matriz" },
      "almacen": { "id": 1, "nombre": "Almacén Central" },
      "detalles_count": 8,
      "created_at": "2024-01-28T10:00:00.000000Z",
      "updated_at": "2024-01-28T14:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 1,
    "per_page": 15,
    "total": 23
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Compra

**`POST /api/purchases`**

Registra una nueva orden de compra con detalles y retenciones.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "proveedor_id": 5,
  "sucursal_id": 1,
  "almacen_id": 1,
  "user_id": 5,
  "tipo_compra_id": 1,
  "total": 125000.00,
  "subtotal": 105932.20,
  "itbis_total": 19067.80,
  "fecha": "2024-01-28T10:00:00.000000Z",
  "observaciones": "Compra semanal de abastecimiento",
  "aplica_retencion_isr": true,
  "aplica_retencion_itbis": true,
  "retencion_isr": 2000.00,
  "retencion_itbis": 1906.78,
  "folio": "OC-2024-0101",
  "detalles": [
    {
      "producto_id": 15,
      "cantidad": 100,
      "precio_unitario": 75.00,
      "itbis_porcentaje": 18,
      "subtotal": 7500.00,
      "impuesto": 1350.00
    },
    {
      "producto_id": 22,
      "cantidad": 50,
      "precio_unitario": 1980.00,
      "itbis_porcentaje": 18,
      "subtotal": 99000.00,
      "impuesto": 17820.00
    }
  ]
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `proveedor_id` | `integer` | **Sí** | ID del proveedor (existe) |
| `sucursal_id` | `integer` | **Sí** | ID de sucursal (existe) |
| `almacen_id` | `integer` | No | ID de almacén (existe) |
| `user_id` | `integer` | No | ID del usuario responsable |
| `tipo_compra_id` | `integer` | No | ID de tipo de compra |
| `total` | `decimal` | **Sí** | Total final (≥ 0) |
| `subtotal` | `decimal` | **Sí** | Subtotal sin impuestos (≥ 0) |
| `itbis_total` | `decimal` | No | Total ITbis |
| `fecha` | `datetime` | **Sí** | Fecha de la compra |
| `observaciones` | `string` | No | Notas internas |
| `aplica_retencion_isr` | `boolean` | No | Aplica retención ISR |
| `aplica_retencion_itbis` | `boolean` | No | Aplica retención ITbis |
| `retencion_isr` | `decimal` | No | Monto retención ISR (≥ 0) |
| `retencion_itbis` | `decimal` | No | Monto retención ITbis (≥ 0) |
| `folio` | `string` | No | Número de orden de compra |
| `detalles` | `array` | **Sí** | Líneas de la compra |

**Campos de Detalles:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `producto_id` | `integer` | **Sí** | ID del producto (existe) |
| `cantidad` | `decimal` | **Sí** | Cantidad recibida (> 0) |
| `precio_unitario` | `decimal` | **Sí** | Precio unitario (≥ 0) |
| `itbis_porcentaje` | `decimal` | No | % ITbis (default: 18) |
| `subtotal` | `decimal` | No | Calculado automáticamente |
| `impuesto` | `decimal` | No | Calculado automáticamente |

**Validations:**

```
proveedor_id: required|exists:proveedores,id
sucursal_id: required|exists:sucursales,id
almacen_id: nullable|exists:almacenes,id
total: required|numeric|min:0
subtotal: required|numeric|min:0
fecha: required|date
aplica_retencion_isr: boolean
aplica_retencion_itbis: boolean
retencion_isr: nullable|numeric|min:0
retencion_itbis: nullable|numeric|min:0
detalles: required|array|min:1
detalles.*.producto_id: required|exists:productos,id
detalles.*.cantidad: required|numeric|min:0.01
detalles.*.precio_unitario: required|numeric|min:0
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 102,
    "proveedor_id": 5,
    "sucursal_id": 1,
    "almacen_id": 1,
    "folio": "OC-2024-0102",
    "total": 125000.00,
    "subtotal": 105932.20,
    "itbis_total": 19067.80,
    "aplica_retencion_isr": true,
    "aplica_retencion_itbis": true,
    "retencion_isr": 2000.00,
    "retencion_itbis": 1906.78,
    "estado": "pendiente",
    "fecha": "2024-01-28T10:00:00.000000Z",
    "detalles": [
      {
        "id": 6001,
        "producto": { "id": 15, "nombre": "Cerveza Corona 355ml" },
        "cantidad": 100,
        "precio_unitario": 75.00,
        "subtotal": 7500.00,
        "impuesto": 1350.00
      }
    ],
    "created_at": "2024-01-28T10:00:00.000000Z"
  },
  "message": "Orden de compra creada exitosamente"
}
```

---

## Endpoint Show

### Obtener Compra

**`GET /api/purchases/{purchase}`**

Retorna una orden de compra con detalles completos.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
{
  "data": {
    "id": 101,
    "proveedor_id": 5,
    "sucursal_id": 1,
    "almacen_id": 1,
    "user_id": 5,
    "tipo_compra_id": 1,
    "total": 125000.00,
    "subtotal": 105932.20,
    "itbis_total": 19067.80,
    "fecha": "2024-01-28T10:00:00.000000Z",
    "observaciones": "Compra semanal de abastecimiento",
    "aplica_retencion_isr": true,
    "aplica_retencion_itbis": true,
    "retencion_isr": 2000.00,
    "retencion_itbis": 1906.78,
    "folio": "OC-2024-0101",
    "estado": "recibida",
    "proveedor": {
      "id": 5,
      "nombre": "Importadora Nacional",
      "rnc_cedula": "13098765432",
      "email": "pedidos@importadoranacional.com",
      "telefono": "+1-809-555-7777"
    },
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "almacen": { "id": 1, "nombre": "Almacén Central" },
    "usuario": { "id": 5, "name": "Carlos Martínez" },
    "tipo_compra": { "id": 1, "nombre": "Normal" },
    "detalles": [
      {
        "id": 4001,
        "producto": { "id": 15, "nombre": "Cerveza Corona 355ml" },
        "cantidad": 100,
        "precio_unitario": 75.00,
        "itbis_porcentaje": 18,
        "subtotal": 7500.00,
        "impuesto": 1350.00
      }
    ],
    "created_at": "2024-01-28T10:00:00.000000Z",
    "updated_at": "2024-01-28T14:00:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Compra

**`PUT /api/purchases/{purchase}`**
**`PATCH /api/purchases/{purchase}`**

Actualiza parcialmente una compra. Todos los campos son opcionales.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "estado": "recibida",
  "observaciones": "Mercancía verificada y recibida"
}
```

**Campos aceptados:** Mismos que Store (todos `sometimes`/opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 101,
    "estado": "recibida",
    "observaciones": "Mercancía verificada y recibida",
    "updated_at": "2024-01-28T14:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Compra

**`DELETE /api/purchases/{purchase}`**

Elimina una orden de compra.

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

- Las compras incrementan el stock del producto al marcarse como "recibida"
- `retencion_isr` y `retencion_itbis` son retenciones fiscales dominicanas
- `aplica_retencion_isr` y `aplica_retencion_itbis` determinan si se calculan retenciones
- `folio` es el número interno de la orden de compra
- Los detalles de compra afectan el inventario del almacén especificado
- `tipo_compra_id` clasifica el tipo de adquisición (normal, urgente, consigna, etc.)
