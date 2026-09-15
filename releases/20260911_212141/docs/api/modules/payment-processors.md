# Payment Processors

Procesadores de pago configurados con credenciales encriptadas y estado activo.

---

## Endpoint Index

### Listar Procesadores de Pago

**`GET /api/payment-processors`**

Retorna procesadores de pago con búsqueda y filtro por estado.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `activa` | `boolean` | Filtrar por estado activo |
| `search` | `string` | Buscar por nombre |

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
      "id": 1,
      "nombre": "Tarjeta de Crédito",
      "codigo": "stripe",
      "activa": true,
      "configuracion": {
        "publishable_key": "pk_live_xxx",
        "secret_key": "***ENCRYPTED***"
      },
      "tenant_id": 1,
      "created_at": "2024-01-01T08:00:00.000000Z",
      "updated_at": "2024-01-20T14:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 1,
    "per_page": 15,
    "total": 3
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Procesador de Pago

**`POST /api/payment-processors`**

Crea un nuevo procesador de pago.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "PayPal Business",
  "codigo": "paypal",
  "activa": true,
  "configuracion": {
    "client_id": "AaBbCcDdEeFf",
    "client_secret": "***ENCRYPTED***",
    "mode": "live"
  }
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | **Sí** | Nombre del procesador |
| `codigo` | `string` | **Sí** | Código único (stripe, paypal, etc.) |
| `activa` | `boolean` | No | Estado activo (default: `true`) |
| `configuracion` | `object` | No | Credenciales encriptadas JSON |

**Validations:**

```
nombre: required|string|max:255
codigo: required|string|unique:payment_processors,codigo
configuracion: nullable|json
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 4,
    "nombre": "PayPal Business",
    "codigo": "paypal",
    "activa": true,
    "configuracion": {
      "client_id": "AaBbCcDdEeFf",
      "client_secret": "***ENCRYPTED***",
      "mode": "live"
    },
    "tenant_id": 1,
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Procesador de Pago

**`GET /api/payment-processors/{processor}`**

Retorna un procesador de pago.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "nombre": "Tarjeta de Crédito",
    "codigo": "stripe",
    "activa": true,
    "configuracion": {
      "publishable_key": "pk_live_xxx",
      "secret_key": "***ENCRYPTED***"
    },
    "tenant_id": 1,
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-20T14:30:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Procesador de Pago

**`PUT /api/payment-processors/{processor}`**
**`PATCH /api/payment-processors/{processor}`**

Actualiza parcialmente un procesador de pago.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Stripe Live",
  "configuracion": {
    "publishable_key": "pk_live_newKey",
    "secret_key": "***NEW_ENCRYPTED***"
  }
}
```

**Campos aceptados:** Mismos que Store (todos opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "nombre": "Stripe Live",
    "configuracion": {
      "publishable_key": "pk_live_newKey",
      "secret_key": "***NEW_ENCRYPTED***"
    },
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Procesador de Pago

**`DELETE /api/payment-processors/{processor}`**

Elimina un procesador de pago.

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

- Las credenciales (`configuracion`) se almacenan **encriptadas** en la base de datos
- `codigo` es el identificador técnico único (stripe, paypal, square, etc.)
- `activa` controla si el procesador aparece como opción en pagos
- Los secretos nunca se devuelven en texto plano en las respuestas
