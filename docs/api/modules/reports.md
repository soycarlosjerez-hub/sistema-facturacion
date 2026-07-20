# Reports API

Dashboard statistics, top-selling products, top customers, and low-stock inventory alerts.

## Base URL

```
/api/reports
```

## Authentication

Requires authentication with `auth` session cookie.

---

## Endpoint Dashboard

### Estadísticas del Dashboard

**`GET /api/reports/dashboard`**

Retorna estadísticas integrales del negocio para el dashboard.

**Headers:**

```
Accept: application/json
Cookie: _session={cookie}
```

**Response `200 OK`:**

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

**Descripción de Campos:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `ventas_hoy` | `float` | Total de ventas hoy |
| `ventas_mes` | `float` | Total de ventas este mes |
| `compras_hoy` | `float` | Total de compras hoy |
| `compras_mes` | `float` | Total de compras este mes |
| `clientes_totales` | `integer` | Clientes registrados totales |
| `productos_activos` | `integer` | Productos activos |
| `inventario_bajo_stock` | `integer` | Productos bajo stock mínimo |
| `ingresos_mes` | `float` | Ingresos totales del mes |
| `gastos_mes` | `float` | Gastos totales del mes |
| `ganancia_neta` | `float` | Ganancia neta (ingresos - gastos) |

---

## Endpoint Top Products

### Productos Más Vendidos

**`GET /api/reports/top-products`**

Retorna productos más vendidos ordenados por cantidad vendida.

**Query Parameters:**

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `limit` | `integer` | No | Cantidad de productos (default: 10) |

**Headers:**

```
Accept: application/json
Cookie: _session={cookie}
```

**Example Request:**

```
GET /api/reports/top-products?limit=5
```

**Response `200 OK`:**

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

**Descripción de Campos:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `producto_id` | `integer` | ID del producto |
| `producto_nombre` | `string` | Nombre del producto |
| `cantidad_vendida` | `integer` | Unidades vendidas totales |
| `ingreso_total` | `float` | Ingresos generados |

---

## Endpoint Top Customers

### Mejores Clientes

**`GET /api/reports/top-customers`**

Retorna mejores clientes ordenados por gasto total.

**Query Parameters:**

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `limit` | `integer` | No | Cantidad de clientes (default: 10) |

**Headers:**

```
Accept: application/json
Cookie: _session={cookie}
```

**Example Request:**

```
GET /api/reports/top-customers?limit=5
```

**Response `200 OK`:**

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

**Descripción de Campos:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `cliente_id` | `integer` | ID del cliente |
| `cliente_nombre` | `string` | Nombre del cliente |
| `total_comprado` | `float` | Monto total gastado |
| `pedidos_realizados` | `integer` | Número de pedidos |

---

## Endpoint Low Stock

### Inventario Bajo Stock

**`GET /api/reports/inventory-low-stock`**

Lista productos por debajo de su umbral mínimo de stock. Une `almacen_productos` con `productos`.

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

**Descripción de Campos:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `producto_id` | `integer` | ID del producto |
| `producto_nombre` | `string` | Nombre del producto |
| `stock_actual` | `integer` | Nivel de stock actual |
| `stock_minimo` | `integer` | Umbral mínimo de stock |
| `diferencia` | `integer` | Défict (negativo = por debajo del mínimo) |
| `unidad_medida` | `string` | Unidad de medida |

---

## Notas

- Todas las rutas requieren autenticación con cookie de sesión
- Los valores monetarios están en la moneda configurada del tenant
- `inventario_bajo_stock` se calcula comparando `stock_actual` vs `stock_minimo`
- `ganancia_neta` = `ingresos_mes` - `gastos_mes`
