# Response Format & Error Handling

Guía completa del formato de respuestas, paginación, filtrado y manejo de errores de la API.

---

## Estructura de Respuesta Exitosa

### Recurso Individual

```http
HTTP/1.1 200 OK
Content-Type: application/json
```

```json
{
  "data": {
    "id": 1,
    "nombre": "Producto Ejemplo",
    "codigo_barras": "7501234567890",
    "precio": 1500.00,
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-20T14:45:00.000000Z"
  }
}
```

### Colección Paginada

```http
HTTP/1.1 200 OK
Content-Type: application/json
```

```json
{
  "data": [
    {
      "id": 1,
      "nombre": "Producto Uno",
      "precio": 1500.00
    },
    {
      "id": 2,
      "nombre": "Producto Dos",
      "precio": 2500.00
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 2,
    "per_page": 15,
    "total": 47
  },
  "links": {
    "first": "/api/products?page=1",
    "last": "/api/products?page=4",
    "prev": null,
    "next": "/api/products?page=2"
  }
}
```

### Respuesta de Creación

```http
HTTP/1.1 201 Created
Content-Type: application/json
```

```json
{
  "data": {
    "id": 48,
    "nombre": "Nuevo Producto",
    "created_at": "2024-01-21T09:00:00.000000Z",
    "updated_at": "2024-01-21T09:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

### Respuesta de Eliminación

```http
HTTP/1.1 200 OK
Content-Type: application/json
```

```json
{
  "message": "Deleted successfully"
}
```

---

## Paginación

### Parámetros

| Parámetro | Tipo | Default | Máximo | Descripción |
|-----------|------|---------|--------|-------------|
| `page` | `integer` | 1 | — | Número de página |
| `per_page` | `integer` | 15 | 100 | Ítems por página |

### Ejemplo

```bash
# Página 2, 25 ítems por página
GET /api/products?page=2&per_page=25
```

### Campos de Meta

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `current_page` | `integer` | Página actual |
| `from` | `integer` | Primer ítem en la página |
| `to` | `integer` | Último ítem en la página |
| `per_page` | `integer` | Ítems por página |
| `total` | `integer` | Total de registros |
| `last_page` | `integer` | Última página |

### Campos de Links

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `first` | `string|null` | URL primera página |
| `last` | `string|null` | URL última página |
| `prev` | `string|null` | URL página anterior |
| `next` | `string|null` | URL página siguiente |

---

## Filtrado

### Filtros Universales

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `search` | `string` | Búsqueda en campos principales del recurso |
| `per_page` | `integer` | Cantidad de resultados por página |

### Filtros Específicos por Módulo

#### Productos (`/api/products`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `categoria_id` | `integer` | Filtrar por categoría |
| `search` | `string` | Buscar por nombre o código de barras |
| `low_stock` | `boolean` | Productos con stock ≤ stock_minimo |
| `out_of_stock` | `boolean` | Productos sin stock |

#### Ventas (`/api/sales`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `cliente_id` | `integer` | Filtrar por cliente |
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `estado` | `string` | Estado de la venta |
| `fecha_desde` | `date` | Fecha inicial (YYYY-MM-DD) |
| `fecha_hasta` | `date` | Fecha final (YYYY-MM-DD) |
| `search_ncf` | `string` | Buscar por NCF |
| `min_total` | `decimal` | Monto mínimo |
| `max_total` | `decimal` | Monto máximo |

#### Clientes (`/api/customers`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `search` | `string` | Buscar por nombre, RNC/Cédula o email |
| `tipo_cliente` | `string` | Tipo de cliente |
| `has_credit_balance` | `boolean` | Clientes con saldo pendiente |

#### Mesas (`/api/tables`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `categoria_id` | `integer` | Filtrar por categoría |
| `activa` | `boolean` | Solo mesas activas |
| `search` | `string` | Buscar por número o nombre |

#### Sucursales (`/api/branches`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `activa` | `boolean` | Solo sucursales activas |
| `search` | `string` | Buscar por nombre o código |

#### Cajas (`/api/cash-registers`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `activo` | `boolean` | Solo cajas activas |

#### Almacenes (`/api/warehouses`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `search` | `string` | Buscar por nombre |

#### Impresoras (`/api/printers`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `activa` | `boolean` | Solo impresoras activas |
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `search` | `string` | Buscar por nombre |

#### Procesadores de Pago (`/api/payment-processors`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `activa` | `boolean` | Solo procesadores activos |
| `search` | `string` | Buscar por nombre |

#### Cotizaciones (`/api/quotes`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `cliente_id` | `integer` | Filtrar por cliente |
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `estado` | `string` | Estado de la cotización |
| `search` | `string` | Buscar por folio o referencia |

#### Devoluciones (`/api/returns`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `venta_id` | `integer` | Filtrar por venta original |
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `estado` | `string` | Estado de la devolución |

#### Reservas (`/api/reservations`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `cliente_id` | `integer` | Filtrar por cliente |
| `mesa_id` | `integer` | Filtrar por mesa |
| `estado` | `string` | Estado de la reserva |
| `fecha` | `date` | Filtrar por fecha |

#### Listas de Precios (`/api/price-lists`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `activa` | `boolean` | Solo listas activas |

#### Secuencias NCF (`/api/ncf-sequences`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `tipo_ncf` | `string` | Tipo de NCF |
| `activa` | `boolean` | Solo secuencias activas |

#### Auditoría (`/api/audit-logs`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `user_id` | `integer` | Filtrar por usuario |
| `action` | `string` | Acción realizada |
| `model_type` | `string` | Tipo de modelo |
| `fecha_desde` | `date` | Fecha inicial |
| `fecha_hasta` | `date` | Fecha final |

#### Órdenes (`/api/orders`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `tipo` | `string` | Tipo de orden (mostrador/delivery/pickup) |
| `estado` | `string` | Estado de la orden |
| `cliente_id` | `integer` | Filtrar por cliente |
| `fecha` | `date` | Filtrar por fecha |

#### Reportes (`/api/reports/*`)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `limit` | `integer` | Cantidad de resultados (default: 10) |
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `fecha_desde` | `date` | Fecha inicial |
| `fecha_hasta` | `date` | Fecha final |

---

## Ordenamiento

### Parámetros

| Parámetro | Tipo | Default | Descripción |
|-----------|------|---------|-------------|
| `sort_by` | `string` | `id` | Campo por el cual ordenar |
| `sort_dir` | `string` | `asc` | Dirección: `asc` o `desc` |

### Ejemplo

```bash
GET /api/products?sort_by=nombre&sort_dir=desc
GET /api/categories?sort_by=orden&sort_dir=asc
```

---

## Formato de Errores

### Error de Validación (422)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "nombre": [
      "El campo nombre es obligatorio."
    ],
    "email": [
      "El campo email debe ser un correo electrónico válido.",
      "El email ya ha sido registrado."
    ],
    "password": [
      "El campo password debe tener al menos 8 caracteres."
    ]
  }
}
```

### Error de No Autorización (401)

```json
{
  "message": "Token no proporcionado."
}
```

```json
{
  "message": "API Key inválida o desactivada."
}
```

```json
{
  "message": "Token expirado."
}
```

### Error de Prohibido (403)

```json
{
  "message": "Este usuario no tiene permiso para realizar esta acción."
}
```

```json
{
  "message": "Instancia bloqueada: Pago vencido."
}
```

```json
{
  "message": "Acceso API deshabilitado para este cliente."
}
```

### Error de No Encontrado (404)

```json
{
  "message": "Not Found"
}
```

### Error Genérico (500)

```json
{
  "message": "The server encountered an internal error."
}
```

---

## Ejemplos Completos

### Crear un Producto

```bash
curl -X POST "https://api.tu-dominio.com/api/products" \
  -H "Authorization: Bearer iak_xxxxxxxx" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "categoria_id": 3,
    "nombre": "Cerveza Corona 355ml",
    "codigo_barras": "061186108314",
    "descripcion": "Cerveza mexicana",
    "precio": 120.00,
    "precio_compra": 75.00,
    "unidad_medida": "unidad",
    "itbis_porcentaje": 18,
    "stock": 500,
    "stock_minimo": 50,
    "imagen": "corona-355.jpg"
  }'
```

**Respuesta 201:**

```json
{
  "data": {
    "id": 156,
    "categoria_id": 3,
    "nombre": "Cerveza Corona 355ml",
    "codigo_barras": "061186108314",
    "descripcion": "Cerveza mexicana",
    "precio": 120.00,
    "precio_compra": 75.00,
    "unidad_medida": "unidad",
    "itbis_porcentaje": 18,
    "stock": 500,
    "stock_minimo": 50,
    "imagen": "corona-355.jpg",
    "created_at": "2024-01-21T10:30:00.000000Z",
    "updated_at": "2024-01-21T10:30:00.000000Z"
  },
  "message": "Created successfully"
}
```

### Listar Ventas con Filtros

```bash
curl -X GET "https://api.tu-dominio.com/api/sales?fecha_desde=2024-01-01&fecha_hasta=2024-01-31&estado=completada&page=1&per_page=20" \
  -H "Authorization: Bearer iak_xxxxxxxx" \
  -H "Accept: application/json"
```

**Respuesta 200:**

```json
{
  "data": [
    {
      "id": 1042,
      "ncf": "B0100000042",
      "tipo_comprobante": "Factura de Crédito Fiscal",
      "cliente": {
        "id": 15,
        "nombre": "Distribuciones Ortiz",
        "rnc_cedula": "13012345678"
      },
      "sucursal": {
        "id": 1,
        "nombre": "Matriz"
      },
      "subtotal": 25000.00,
      "impuestos": 4500.00,
      "descuento": 0.00,
      "total": 29500.00,
      "estado": "completada",
      "fecha": "2024-01-28T14:22:00.000000Z",
      "detalles_count": 5
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 1,
    "per_page": 20,
    "total": 1
  },
  "links": {}
}
```

### Error de Validación

```bash
curl -X POST "https://api.tu-dominio.com/api/products" \
  -H "Authorization: Bearer iak_xxxxxxxx" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "nombre": "",
    "precio": -50
  }'
```

**Respuesta 422:**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "nombre": [
      "El campo nombre es obligatorio."
    ],
    "precio": [
      "El campo precio debe ser mayor o igual a 0."
    ]
  }
}
```

---

## Tips

- Siempre incluye `Accept: application/json` en tus peticiones
- Usa `per_page=100` para reducir el número de páginas en consultas grandes
- Los filtros son opcionales — omítelos para obtener todos los registros
- Las fechas siguen el formato ISO 8601: `YYYY-MM-DDTHH:mm:ss.ssssssZ`
- Los montos monetarios siempre incluyen 2 decimales
- Los IDs son numéricos autoincrementales
