# Reservations API

Service module for managing restaurant table reservations.

## Base URL

```
/api/modules/reservations
```

## Authentication

Requires authentication with `auth` session cookie.

---

## Endpoint Index

### Listar Reservaciones

**`GET /api/modules/reservations`**

Retorna lista paginada de reservaciones con filtros.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `cliente_id` | `integer` | Filtrar por cliente |
| `mesa_id` | `integer` | Filtrar por mesa |
| `estado` | `string` | Filtrar por estado |
| `fecha` | `date` | Filtrar por fecha |

**Headers:**

```
Accept: application/json
Cookie: _session={cookie}
```

**Response `200 OK` — Colección paginada de objetos de reservación:**

```json
{
  "data": [
    {
      "id": 1,
      "cliente_nombre": "María López",
      "cliente_id": 5,
      "cliente_telefono": "+1-809-555-0100",
      "cliente_email": "maria@example.com",
      "mesa_id": 3,
      "mesa": {
        "id": 3,
        "numero": 3,
        "nombre": "Mesa 3 - Ventana"
      },
      "fecha_hora": "2024-01-28T19:00:00.000000Z",
      "personas": 4,
      "estado": "confirmada",
      "notas": "Cumpleaños",
      "user_id": 5,
      "tenant_id": 1,
      "created_at": "2024-01-27T10:00:00.000000Z",
      "updated_at": "2024-01-27T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "last_page": 2,
    "from": 1,
    "to": 15,
    "total": 23
  }
}
```

---

## Endpoint Store

### Crear Reservación

**`POST /api/modules/reservations`**

Crea una nueva reservación de mesa.

**Headers:**

```
Accept: application/json
Content-Type: application/json
Cookie: _session={cookie}
```

**Request Body:**

```json
{
  "cliente_nombre": "María López",
  "cliente_id": 5,
  "cliente_telefono": "+1-809-555-0100",
  "cliente_email": "maria@example.com",
  "mesa_id": 3,
  "fecha_hora": "2024-01-28T19:00:00",
  "personas": 4,
  "estado": "confirmada",
  "notas": "Cumpleaños"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `cliente_nombre` | `string` | **Sí** | Nombre del cliente |
| `cliente_id` | `integer` | No | ID del cliente (si existe, actualiza info automáticamente) |
| `cliente_telefono` | `string` | No | Teléfono del cliente |
| `cliente_email` | `string` | No | Email del cliente (valida formato) |
| `mesa_id` | `integer` | **Sí** | ID de mesa (existe) |
| `fecha_hora` | `datetime` | **Sí** | Fecha/hora de la reservación |
| `personas` | `integer` | **Sí** | Número de personas (≥ 1) |
| `estado` | `string` | **Sí** | Estado de la reservación |
| `notas` | `string` | No | Notas |

**Notas de Validación:**

- `user_id` y `tenant_id` se establecen automáticamente desde la autenticación
- Si `cliente_id` es proporcionado, la información del cliente existente se actualiza automáticamente
- Si `cliente_email` está presente, envía notificación `ReservacionRecibidaMail`
- Confirma y cancela emails se envían automáticamente en cambios de estado
- Bloquea apertura de mesa si existe reservación pendiente para esa mesa (ventana > 1 hora)

**Response `201 Created`:**

```json
{
  "data": {
    "id": 10,
    "cliente_nombre": "María López",
    "cliente_id": 5,
    "mesa_id": 3,
    "fecha_hora": "2024-01-28T19:00:00.000000Z",
    "personas": 4,
    "estado": "confirmada",
    "notas": "Cumpleaños",
    "created_at": "2024-01-27T10:00:00.000000Z"
  },
  "message": "Reservación creada exitosamente"
}
```

---

## Endpoint Show

### Obtener Reservación

**`GET /api/modules/reservations/{id}`**

Retorna una sola reservación por ID.

**Headers:**

```
Accept: application/json
Cookie: _session={cookie}
```

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "cliente_nombre": "María López",
    "cliente_id": 5,
    "cliente_telefono": "+1-809-555-0100",
    "cliente_email": "maria@example.com",
    "mesa_id": 3,
    "mesa": {
      "id": 3,
      "numero": 3,
      "nombre": "Mesa 3 - Ventana"
    },
    "fecha_hora": "2024-01-28T19:00:00.000000Z",
    "personas": 4,
    "estado": "confirmada",
    "notas": "Cumpleaños",
    "user_id": 5,
    "tenant_id": 1,
    "created_at": "2024-01-27T10:00:00.000000Z",
    "updated_at": "2024-01-27T10:00:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Reservación

**`PUT /api/modules/reservations/{id}`**
**`PATCH /api/modules/reservations/{id}`**

Actualiza una reservación existente.

**Headers:**

```
Accept: application/json
Content-Type: application/json
Cookie: _session={cookie}
```

**Request Body:** Mismos campos que Store (todos opcionales para actualizaciones parciales).

**Notas:** Los cambios de estado disparan notificaciones automáticas por email (confirmación al confirmar, cancelación al cancelar).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "estado": "cancelada",
    "notas": "Cliente canceló",
    "updated_at": "2024-01-28T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Reservación

**`DELETE /api/modules/reservations/{id}`**

Elimina una reservación por ID.

**Headers:**

```
Accept: application/json
Cookie: _session={cookie}
```

**Response `200 OK`:**

```json
{
  "message": "Deleted successfully"
}
```

---

## Notas

- `fecha_hora` define cuándo el cliente llegará
- `personas` se valida contra la capacidad de la mesa asignada
- `estado` puede ser: `pendiente`, `confirmada`, `cancelada`, `completada`
- Las reservaciones bloquean la mesa durante la ventana horaria especificada
- Los emails automáticos mejoran la comunicación con el cliente
