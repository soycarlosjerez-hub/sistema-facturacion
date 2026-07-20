# Rentals API

Service module for managing vehicle/alquiler rentals.

## Base URL

```
/api/modules/rentals
```

## Authentication

Requires authentication with `auth` session cookie.

---

## Endpoint Index

### Listar Alquileres

**`GET /api/modules/rentals`**

Retorna lista paginada de alquileres con filtros.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `cliente_id` | `integer` | Filtrar por cliente |
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `estado` | `string` | Filtrar por estado |

**Headers:**

```
Accept: application/json
Cookie: _session={cookie}
```

**Response `200 OK` — Colección paginada de objetos de alquiler:**

```json
{
  "data": [
    {
      "id": 1,
      "folio": "ALQ-2024-001",
      "cliente_id": 5,
      "sucursal_id": 1,
      "user_id": 5,
      "vehiculo_id": 3,
      "fecha_inicio": "2024-01-28",
      "fecha_fin": "2024-02-02",
      "estado": "activo",
      "total": 5000.00,
      "deposito": 2000.00,
      "notas": "Cliente frecuente",
      "cliente": {
        "id": 5,
        "nombre": "María López"
      },
      "sucursal": {
        "id": 1,
        "nombre": "Matriz"
      },
      "created_at": "2024-01-27T10:00:00.000000Z",
      "updated_at": "2024-01-28T08:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "last_page": 2,
    "from": 1,
    "to": 15,
    "total": 18
  }
}
```

---

## Endpoint Store

### Crear Alquiler

**`POST /api/modules/rentals`**

Crea un nuevo registro de alquiler de vehículo.

**Headers:**

```
Accept: application/json
Content-Type: application/json
Cookie: _session={cookie}
```

**Request Body:**

```json
{
  "folio": "ALQ-2024-001",
  "cliente_id": 5,
  "sucursal_id": 1,
  "user_id": 5,
  "vehiculo_id": 3,
  "fecha_inicio": "2024-01-28",
  "fecha_fin": "2024-02-02",
  "estado": "activo",
  "total": 5000.00,
  "deposito": 2000.00,
  "notas": "Cliente frecuente"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `folio` | `string` | **Sí** | Identificador de alquiler |
| `cliente_id` | `integer` | **Sí** | ID de cliente (existe) |
| `sucursal_id` | `integer` | **Sí** | ID de sucursal (existe) |
| `user_id` | `integer` | **Sí** | ID de usuario (existe) |
| `vehiculo_id` | `integer` | No | ID de vehículo |
| `fecha_inicio` | `date` | **Sí** | Fecha de inicio |
| `fecha_fin` | `date` | **Sí** | Fecha de fin (después de `fecha_inicio`) |
| `estado` | `string` | **Sí** | Estado del alquiler |
| `total` | `numeric` | **Sí** | Monto total (≥ 0) |
| `deposito` | `numeric` | No | Monto del depósito |
| `notas` | `string` | No | Notas |

**Validations:**

```
folio: required|string|max:255
cliente_id: required|exists:clientes,id
sucursal_id: required|exists:sucursales,id
user_id: required|exists:users,id
fecha_inicio: required|date
fecha_fin: required|date|after:fecha_inicio
estado: required|string
total: required|numeric|min:0
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 10,
    "folio": "ALQ-2024-001",
    "cliente_id": 5,
    "sucursal_id": 1,
    "fecha_inicio": "2024-01-28",
    "fecha_fin": "2024-02-02",
    "estado": "activo",
    "total": 5000.00,
    "created_at": "2024-01-27T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Alquiler

**`GET /api/modules/rentals/{id}`**

Retorna un solo alquiler por ID.

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
    "folio": "ALQ-2024-001",
    "cliente_id": 5,
    "sucursal_id": 1,
    "user_id": 5,
    "vehiculo_id": 3,
    "fecha_inicio": "2024-01-28",
    "fecha_fin": "2024-02-02",
    "estado": "activo",
    "total": 5000.00,
    "deposito": 2000.00,
    "notas": "Cliente frecuente",
    "cliente": { "id": 5, "nombre": "María López" },
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "created_at": "2024-01-27T10:00:00.000000Z",
    "updated_at": "2024-01-28T08:00:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Alquiler

**`PUT /api/modules/rentals/{id}`**
**`PATCH /api/modules/rentals/{id}`**

Actualiza un alquiler existente.

**Headers:**

```
Accept: application/json
Content-Type: application/json
Cookie: _session={cookie}
```

**Request Body:** Mismos campos que Store (todos opcionales para actualizaciones parciales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "estado": "finalizado",
    "updated_at": "2024-02-02T18:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Alquiler

**`DELETE /api/modules/rentals/{id}`**

Elimina un alquiler por ID.

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

- `folio` es el identificador único del alquiler
- `fecha_inicio` y `fecha_fin` definen el período de alquiler
- `deposito` es el monto garantizado al iniciar el alquiler
- `estado` refleja el progreso: `pendiente`, `activo`, `finalizado`, `cancelado`
- `vehiculo_id` vincula el alquiler a un vehículo específico
