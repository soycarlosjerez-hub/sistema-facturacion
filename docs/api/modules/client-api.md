# Client Portal API

Authenticated customer portal endpoints. Requires `auth.cliente` middleware (JWT bearer token obtained via login).

## Base URL

```
/api/cliente
```

## Authentication

Requires a valid JWT bearer token obtained through the client authentication flow (`POST /api/auth/cliente/login`). Include the token in the `Authorization` header:

```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGci...
```

---

## POST /api/cliente/logout

Invalidate the current API session by deleting the access token.

### Request Body

None.

### Response

`200 OK`

```json
{
  "message": "Sesión cerrada correctamente"
}
```

---

## GET /api/cliente/me

Return the authenticated customer's profile information.

### Request Body

None.

### Response

`200 OK`

```json
{
  "data": {
    "id": 50,
    "nombre": "Ana García",
    "email": "ana@example.com",
    "telefono": "+1-809-555-0100",
    "tipo_cliente": "consumo",
    "activo": true,
    "email_verified_at": "2026-07-20T14:05:00.000000Z",
    "acceso_api": true,
    "direccion": null,
    "ciudad": null,
    "provincia": null,
    "created_at": "2026-07-20T14:00:00.000000Z",
    "updated_at": "2026-07-20T14:00:00.000000Z"
  }
}
```

---

## PUT /api/cliente/profile

Update the customer's profile information. Uniqueness validation excludes the current user.

### Request Body

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `nombre` | string | No | Updated full name |
| `email` | string | No | Must be unique (excluding current user) |
| `telefono` | string | No | Must be unique (excluding current user) |
| `direccion` | string | No | Mailing address |
| `ciudad` | string | No | City |
| `provincia` | string | No | Province |

### Example Request

```json
{
  "nombre": "Ana María García",
  "telefono": "+1-809-555-0200",
  "direccion": "Calle Principal #45",
  "ciudad": "Santo Domingo",
  "provincia": "Distrito Nacional"
}
```

### Response

`200 OK`

```json
{
  "data": {
    "id": 50,
    "nombre": "Ana María García",
    "email": "ana@example.com",
    "telefono": "+1-809-555-0200",
    "direccion": "Calle Principal #45",
    "ciudad": "Santo Domingo",
    "provincia": "Distrito Nacional",
    "updated_at": "2026-07-20T15:00:00.000000Z"
  }
}
```

---

## POST /api/cliente/change-password

Change the customer's password. Invalidates all other active sessions.

### Request Body

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `current_password` | string | Yes | Current password for verification |
| `new_password` | string | Yes | Minimum 12 characters, with confirmation |
| `new_password_confirmation` | string | Yes | Must match `new_password` |

### Example Request

```json
{
  "current_password": "miPasswordSeguro123",
  "new_password": "nuevoPasswordSeguro456",
  "new_password_confirmation": "nuevoPasswordSeguro456"
}
```

### Response

`200 OK`

Invalidates all other sessions and returns a new access token.

```json
{
  "data": {
    "message": "Contraseña cambiada correctamente",
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGci...",
    "token_type": "Bearer"
  }
}
```

---

## GET /api/cliente/pedidos

List orders placed by the authenticated customer. Loads `detalles.producto`, `sucursal`, and `entregaEmpresa` relationships. Uses custom pagination wrapper.

### Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `page` | integer | No | Page number (default: 1) |

### Response

`200 OK` — Custom pagination wrapper with `pedidos` array.

```json
{
  "data": {
    "pedidos": [
      {
        "id": 100,
        "tipo_orden": "mostrador",
        "estado": "completada",
        "subtotal": 2500.00,
        "total": 2750.00,
        "notas": "Sin hielo",
        "sucursal": {
          "id": 1,
          "nombre": "Tienda Central"
        },
        "entregaEmpresa": null,
        "detalles": [
          {
            "id": 200,
            "producto_id": 12,
            "producto": {
              "id": 12,
              "nombre": "Pollo Guisado"
            },
            "cantidad": 2,
            "curso": "fuerte",
            "subtotal": 2500.00
          }
        ],
        "created_at": "2026-07-20T14:00:00.000000Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "last_page": 2,
      "from": 1,
      "to": 15,
      "total": 23
    }
  }
}
```

---

## GET /api/cliente/pedidos/{id}

View a single order belonging to the authenticated customer. Loads `pagos` and `entregaEmpresa` relationships.

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | integer | Order ID (must belong to authenticated customer) |

### Response

`200 OK`

```json
{
  "data": {
    "id": 100,
    "tipo_orden": "mostrador",
    "estado": "completada",
    "cliente_id": 50,
    "subtotal": 2500.00,
    "total": 2750.00,
    "notas": "Sin hielo",
    "sucursal_id": 1,
    "sucursal": {
      "id": 1,
      "nombre": "Tienda Central"
    },
    "entregaEmpresa": null,
    "pagos": [
      {
        "id": 50,
        "metodo_pago": "efectivo",
        "monto": 3000.00,
        "cambio": 250.00,
        "created_at": "2026-07-20T14:05:00.000000Z"
      }
    ],
    "detalles": [
      {
        "id": 200,
        "producto_id": 12,
        "producto": {
          "id": 12,
          "nombre": "Pollo Guisado"
        },
        "cantidad": 2,
        "curso": "fuerte",
        "subtotal": 2500.00
      }
    ],
    "created_at": "2026-07-20T14:00:00.000000Z",
    "updated_at": "2026-07-20T14:05:00.000000Z"
  }
}
```

---

## Field Reference

### Cliente Profile Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Customer ID |
| `nombre` | string | Full name |
| `email` | string | Email address |
| `telefono` | string | Phone number |
| `tipo_cliente` | string | Customer type |
| `activo` | boolean | Account active status |
| `email_verified_at` | datetime|null | Email verification timestamp |
| `acceso_api` | boolean | API access flag |
| `direccion` | string|null | Mailing address |
| `ciudad` | string|null | City |
| `provincia` | string|null | Province |
| `created_at` | datetime | Registration timestamp |
| `updated_at` | datetime | Last update timestamp |

### Order Summary Fields (in list view)

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Order ID |
| `tipo_orden` | string | Order type: `mostrador`, `delivery`, `pickup` |
| `estado` | string | Order status |
| `subtotal` | float | Subtotal before taxes |
| `total` | float | Grand total |
| `notas` | string | Order notes |
| `sucursal` | object | Branch/location information |
| `entregaEmpresa` | object|null | Delivery company (if applicable) |
| `detalles` | array | Line items with product details |
| `created_at` | datetime | Order creation timestamp |

### Payment Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Payment ID |
| `metodo_pago` | string | Payment method: `efectivo`, `tarjeta`, `transferencia`, `mixto`, `fiado` |
| `monto` | float | Amount paid |
| `cambio` | float|null | Change given (for cash payments) |
| `created_at` | datetime | Payment timestamp |
