# Customers

Clientes con datos fiscales completos para facturación dominicana (RNC/Cédula, régimen mensual, etc.).

---

## Endpoint Index

### Listar Clientes

**`GET /api/customers`**

Retorna clientes con datos fiscales y saldo de crédito.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `search` | `string` | Buscar por nombre, RNC/Cédula o email |
| `tipo_cliente` | `string` | Filtrar por tipo de cliente |
| `has_credit_balance` | `boolean` | Clientes con saldo pendiente |

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
      "id": 15,
      "nombre": "Distribuciones Ortiz",
      "email": "info@distribucionesortiz.com",
      "telefono": "+1-809-555-9999",
      "whatsapp": "+1-809-555-9999",
      "direccion": "Av. Duarte #123, Santiago",
      "ciudad": "Santiago",
      "provincia": "Santiago",
      "codigo_postal": "51000",
      "rnc_cedula": "13012345678",
      "tipo_documento": "RNC",
      "tipo_cliente": "credito_fiscal",
      "limite_credito": 500000.00,
      "saldo_pendiente": 125000.00,
      "plazo_pago_dias": 30,
      "tasa_descuento_pct": 5.00,
      "moneda": "RD$",
      "activo": true,
      "segundo_nombre": "",
      "segundo_apellido": "",
      "nit": "13012345678",
      "persona_contacto": "Roberto Ortiz",
      "cargo_contacto": "Gerente de Compras",
      "segmento": "grande",
      "origen_cliente": "referencia",
      "sector_actividad": "Distribución",
      "created_at": "2024-01-01T08:00:00.000000Z",
      "updated_at": "2024-01-20T14:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 1,
    "per_page": 15,
    "total": 89
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Cliente

**`POST /api/customers`**

Crea un nuevo cliente con datos fiscales completos.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Distribuciones Ortiz",
  "email": "info@distribucionesortiz.com",
  "telefono": "+1-809-555-9999",
  "whatsapp": "+1-809-555-9999",
  "direccion": "Av. Duarte #123, Santiago",
  "ciudad": "Santiago",
  "provincia": "Santiago",
  "codigo_postal": "51000",
  "rnc_cedula": "13012345678",
  "tipo_documento": "RNC",
  "tipo_cliente": "credito_fiscal",
  "limite_credito": 500000.00,
  "plazo_pago_dias": 30,
  "tasa_descuento_pct": 5.00,
  "moneda": "RD$",
  "activo": true,
  "segundo_nombre": "",
  "segundo_apellido": "",
  "nit": "13012345678",
  "persona_contacto": "Roberto Ortiz",
  "cargo_contacto": "Gerente de Compras",
  "segmento": "grande",
  "origen_cliente": "referencia",
  "sector_actividad": "Distribución"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | **Sí** | Nombre legal o razón social |
| `email` | `string` | No | Email único |
| `telefono` | `string` | No | Teléfono único |
| `whatsapp` | `string` | No | WhatsApp (puede diferir de teléfono) |
| `direccion` | `string` | No | Dirección completa |
| `ciudad` | `string` | No | Ciudad |
| `provincia` | `string` | No | Provincia |
| `codigo_postal` | `string` | No | Código postal |
| `rnc_cedula` | `string` | No | RNC o Cédula (único) |
| `tipo_documento` | `string` | No | `RNC` o `CEDULA` |
| `tipo_cliente` | `string` | **Sí** | `consumo_final`, `credito_fiscal`, `especial` |
| `limite_credito` | `decimal` | No | Límite de crédito (≥ 0) |
| `plazo_pago_dias` | `integer` | No | Días de crédito (0-365) |
| `tasa_descuento_pct` | `decimal` | No | % descuento (0-100) |
| `moneda` | `string` | No | `RD$`, `USD`, `EUR` |
| `activo` | `boolean` | No | Estado activo (default: `true`) |
| `segundo_nombre` | `string` | No | Segundo nombre |
| `segundo_apellido` | `string` | No | Segundo apellido |
| `nit` | `string` | No | NIT alternativo |
| `persona_contacto` | `string` | No | Persona de contacto |
| `cargo_contacto` | `string` | No | Cargo de la persona de contacto |
| `segmento` | `string` | No | `micro`, `pequeno`, `mediano`, `grande`, `gobierno` |
| `origen_cliente` | `string` | No | `referencia`, `web`, `walkin`, `publicidad`, `otro` |
| `sector_actividad` | `string` | No | Sector económico |

**Validations:**

```
nombre: required|string|max:255
email: nullable|string|email|unique:clientes,email
telefono: nullable|string|unique:clientes,telefono
rnc_cedula: nullable|string|unique:clientes,rnc_cedula
tipo_cliente: required|in:consumo_final,credito_fiscal,especial
limite_credito: nullable|numeric|min:0
plazo_pago_dias: nullable|integer|min:0|max:365
tasa_descuento_pct: nullable|numeric|min:0|max:100
moneda: nullable|in:RD$,USD,EUR
segmento: nullable|in:micro,pequeno,mediano,grande,gobierno
origen_cliente: nullable|in:referencia,web,walkin,publicidad,otro
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 90,
    "nombre": "Distribuciones Ortiz",
    "email": "info@distribucionesortiz.com",
    "telefono": "+1-809-555-9999",
    "rnc_cedula": "13012345678",
    "tipo_cliente": "credito_fiscal",
    "limite_credito": 500000.00,
    "plazo_pago_dias": 30,
    "tasa_descuento_pct": 5.00,
    "moneda": "RD$",
    "activo": true,
    "segmento": "grande",
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Cliente

**`GET /api/customers/{customer}`**

Retorna un cliente con historial de ventas y facturas.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
{
  "data": {
    "id": 15,
    "nombre": "Distribuciones Ortiz",
    "email": "info@distribucionesortiz.com",
    "telefono": "+1-809-555-9999",
    "whatsapp": "+1-809-555-9999",
    "direccion": "Av. Duarte #123, Santiago",
    "ciudad": "Santiago",
    "provincia": "Santiago",
    "codigo_postal": "51000",
    "rnc_cedula": "13012345678",
    "tipo_documento": "RNC",
    "tipo_cliente": "credito_fiscal",
    "limite_credito": 500000.00,
    "saldo_pendiente": 125000.00,
    "plazo_pago_dias": 30,
    "tasa_descuento_pct": 5.00,
    "moneda": "RD$",
    "activo": true,
    "persona_contacto": "Roberto Ortiz",
    "cargo_contacto": "Gerente de Compras",
    "segmento": "grande",
    "origen_cliente": "referencia",
    "sector_actividad": "Distribución",
    "ventas_count": 45,
    "total_comprado": 2450000.00,
    "ultima_compra": "2024-01-28T14:22:00.000000Z",
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-20T14:30:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Cliente

**`PUT /api/customers/{customer}`**
**`PATCH /api/customers/{customer}`**

Actualiza parcialmente un cliente. Todos los campos son opcionales.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Distribuciones Ortiz SRL",
  "limite_credito": 750000.00,
  "plazo_pago_dias": 45,
  "tasa_descuento_pct": 7.50
}
```

**Campos aceptados:** Mismos que Store (todos `sometimes`/opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 15,
    "nombre": "Distribuciones Ortiz SRL",
    "limite_credito": 750000.00,
    "plazo_pago_dias": 45,
    "tasa_descuento_pct": 7.50,
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Cliente

**`DELETE /api/customers/{customer}`**

Elimina un cliente del sistema.

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

- `rnc_cedula` es único por tenant — no permite duplicados
- `email` y `telefono` también son únicos
- `tipo_cliente` afecta cómo se genera el NCF y los comprobantes fiscales
- `plazo_pago_dias` (0-365) controla el crédito otorgado
- `tasa_descuento_pct` (0-100) aplica descuento automático en ventas
- `segmento` clasifica al cliente para marketing y análisis
- `origen_cliente` rastrea de dónde vino el cliente
- `saldo_pendiente` se calcula dinámicamente de ventas pendientes de pago
