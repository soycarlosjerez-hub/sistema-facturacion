# Reports API

Dashboard statistics, top-selling products, top customers, and low-stock inventory alerts.

## Base URL

```
/api/reports
```

## Authentication

Requires authentication with `auth` session cookie.

---

## GET /api/reports/dashboard

Return comprehensive business dashboard statistics.

### Query Parameters

None.

### Response

`200 OK`

```json
{
  "data": {
    "ventas_hoy": 15750.00,
    "ventas_mes": 245000.00,
    "compras_hoy": 8200.00,
    "compras_mes": 120000.00,
    "clientes_totales": 342,
    "productos_activos": 156,
    "inventario_bajo_stock": 12,
    "ingresos_mes": 245000.00,
    "gastos_mes": 120000.00,
    "ganancia_neta": 125000.00
  }
}
```

### Field Descriptions

| Field | Type | Description |
|-------|------|-------------|
| `ventas_hoy` | float | Total sales today |
| `ventas_mes` | float | Total sales this month |
| `compras_hoy` | float | Total purchases today |
| `compras_mes` | float | Total purchases this month |
| `clientes_totales` | integer | Total registered clients |
| `productos_activos` | integer | Number of active products |
| `inventario_bajo_stock` | integer | Count of products below minimum stock |
| `ingresos_mes` | float | Total income this month |
| `gastos_mes` | float | Total expenses this month |
| `ganancia_neta` | float | Net profit (income minus expenses) |

---

## GET /api/reports/top-products

Retrieve top-selling products ranked by quantity sold.

### Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `limit` | integer | No | Number of products to return (default: 10) |

### Example Request

```
GET /api/reports/top-products?limit=5
```

### Response

`200 OK`

```json
{
  "data": [
    {
      "producto_id": 12,
      "producto_nombre": "Pollo Guisado",
      "cantidad_vendida": 145,
      "ingreso_total": 181250.00
    },
    {
      "producto_id": 8,
      "producto_nombre": "Sopa Tropical",
      "cantidad_vendida": 98,
      "ingreso_total": 49000.00
    }
  ]
}
```

### Field Descriptions

| Field | Type | Description |
|-------|------|-------------|
| `producto_id` | integer | Product ID |
| `producto_nombre` | string | Product name |
| `cantidad_vendida` | integer | Total units sold |
| `ingreso_total` | float | Total revenue generated |

---

## GET /api/reports/top-customers

Retrieve top customers ranked by total spending.

### Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `limit` | integer | No | Number of customers to return (default: 10) |

### Example Request

```
GET /api/reports/top-customers?limit=5
```

### Response

`200 OK`

```json
{
  "data": [
    {
      "cliente_id": 5,
      "cliente_nombre": "María López",
      "total_comprado": 45200.00,
      "pedidos_realizados": 23
    },
    {
      "cliente_id": 12,
      "cliente_nombre": "Carlos Ruiz",
      "total_comprado": 32100.00,
      "pedidos_realizados": 15
    }
  ]
}
```

### Field Descriptions

| Field | Type | Description |
|-------|------|-------------|
| `cliente_id` | integer | Customer ID |
| `cliente_nombre` | string | Customer name |
| `total_comprado` | float | Total amount spent |
| `pedidos_realizados` | integer | Number of orders placed |

---

## GET /api/reports/inventory-low-stock

List products below their minimum stock threshold. Joins `almacen_productos` with `productos`.

### Query Parameters

None.

### Response

`200 OK`

```json
{
  "data": [
    {
      "producto_id": 12,
      "producto_nombre": "Pollo Guisado",
      "stock_actual": 3,
      "stock_minimo": 10,
      "diferencia": -7,
      "unidad_medida": "kg"
    },
    {
      "producto_id": 25,
      "producto_nombre": "Arroz",
      "stock_actual": 5,
      "stock_minimo": 20,
      "diferencia": -15,
      "unidad_medida": "kg"
    }
  ]
}
```

### Field Descriptions

| Field | Type | Description |
|-------|------|-------------|
| `producto_id` | integer | Product ID |
| `producto_nombre` | string | Product name |
| `stock_actual` | integer | Current stock level |
| `stock_minimo` | integer | Minimum stock threshold |
| `diferencia` | integer | Deficit (negative = below minimum) |
| `unidad_medida` | string | Unit of measurement |
