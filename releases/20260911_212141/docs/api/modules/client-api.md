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

## Endpoint Logout

### Cerrar Sesión

**`POST /api/cliente/logout`**

Invalida la sesión actual eliminando el access token.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
{
  "message": "Sesión cerrada correctamente"
}
```

---

## Endpoint Me

### Perfil del Cliente

**`GET /api/cliente/me`**

Retorna la información del perfil del cliente autenticado.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

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

## Endpoint Update Profile

### Actualizar Perfil

**`PUT /api/cliente/profile`**

Actualiza la información del perfil del cliente. Validación de unicidad excluye al usuario actual.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Ana María García",
  "telefono": "+1-809-555-0200",
  "direccion": "Calle Principal #45",
  "ciudad": "Santo Domingo",
  "provincia": "Distrito Nacional"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | No | Nombre completo actualizado |
| `email` | `string` | No | Debe ser único (excluyendo usuario actual) |
| `telefono` | `string` | No | Debe ser único (excluyendo usuario actual) |
| `direccion` | `string` | No | Dirección de envío |
| `ciudad` | `string` | No | Ciudad |
| `provincia` | `string` | No | Provincia |

**Response `200 OK`:**

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

## Endpoint Change Password

### Cambiar Contraseña

**`POST /api/cliente/change-password`**

Cambia la contraseña del cliente. Invalida todas las demás sesiones activas.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "current_password": "miPasswordSeguro123",
  "new_password": "nuevoPasswordSeguro456",
  "new_password_confirmation": "nuevoPasswordSeguro456"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `current_password` | `string` | **Sí** | Contraseña actual |
| `new_password` | `string` | **Sí** | Mínimo 12 caracteres |
| `new_password_confirmation` | `string` | **Sí** | Debe coincidir con `new_password` |

**Response `200 OK`:**

Invalida otras sesiones y retorna nuevo access token.

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

## Endpoint Orders List

### Mis Pedidos

**`GET /api/cliente/pedidos`**

Lista los pedidos realizados por el cliente autenticado. Carga `detalles.producto`, `sucursal`, y `entregaEmpresa`. Usa paginación personalizada.

**Query Parameters:**

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `page` | `integer` | No | Número de página (default: 1) |

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK` — Wrapper personalizado con array `pedidos`:**

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

## Endpoint Order Show

### Detalle de Pedido

**`GET /api/cliente/pedidos/{id}`**

Visualiza un solo pedido perteneciente al cliente autenticado. Carga `pagos` y `entregaEmpresa`.

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `id` | `integer` | ID del pedido (debe pertenecer al cliente autenticado) |

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

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

### Campos de Perfil de Cliente

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | `integer` | ID del cliente |
| `nombre` | `string` | Nombre completo |
| `email` | `string` | Email |
| `telefono` | `string` | Teléfono |
| `tipo_cliente` | `string` | Tipo de cliente |
| `activo` | `boolean` | Estado de la cuenta |
| `email_verified_at` | `datetime\|null` | Timestamp verificación email |
| `acceso_api` | `boolean` | Flag acceso API |
| `direccion` | `string\|null` | Dirección de envío |
| `ciudad` | `string\|null` | Ciudad |
| `provincia` | `string\|null` | Provincia |
| `created_at` | `datetime` | Fecha registro |
| `updated_at` | `datetime` | Última actualización |

### Campos de Resumen de Pedido

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | `integer` | ID del pedido |
| `tipo_orden` | `string` | Tipo: `mostrador`, `delivery`, `pickup` |
| `estado` | `string` | Estado del pedido |
| `subtotal` | `float` | Subtotal sin impuestos |
| `total` | `float` | Total final |
| `notas` | `string` | Notas del pedido |
| `sucursal` | `object` | Información de sucursal |
| `entregaEmpresa` | `object\|null` | Empresa de entrega |
| `detalles` | `array` | Items con detalles del producto |
| `created_at` | `datetime` | Fecha creación |

### Campos de Pago

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | `integer` | ID del pago |
| `metodo_pago` | `string` | Método: `efectivo`, `tarjeta`, `transferencia`, `mixto`, `fiado` |
| `monto` | `float` | Monto pagado |
| `cambio` | `float\|null` | Cambio dado (para pagos en efectivo) |
| `created_at` | `datetime` | Timestamp del pago |
