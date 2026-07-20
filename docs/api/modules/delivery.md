# Delivery API

Service module for managing delivery/conduce services.

## Base URL

```
/api/modules/delivery
```

## Authentication

Requires authentication with `auth` session cookie.

---

## Endpoint Index

### Listar Deliveries

**`GET /api/modules/delivery`**

Retorna lista paginada de entregas con filtros.

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

**Response `200 OK` — Colección paginada de objetos de delivery:**

```json
{
  "data": [
    {
      "id": 1,
      "folio": "DEL-2024-001",
      "cliente_id": 5,
      "sucursal_id": 1,
      "user_id": 5,
      "vehiculo_id": 3,
      "fecha_recepcion": "2024-01-28T08:00:00.000000Z",
      "fecha_entrega": "2024-01-28T14:00:00.000000Z",
      "estado": "entregado",
      "total": 850.00,
      "kilometraje": 45,
      "combustible": "75%",
      "danios": "Sin daños",
      "notas": "Entrega puntual",
      "cliente": {
        "id": 5,
        "nombre": "María López"
      },
      "sucursal": {
        "id": 1,
        "nombre": "Matriz"
      },
      "created_at": "2024-01-28T08:00:00.000000Z",
      "updated_at": "2024-01-28T14:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "last_page": 2,
    "from": 1,
    "to": 15,
    "total": 28
  }
}
```

---

## Endpoint Store

### Crear Registro de Delivery

**`POST /api/modules/delivery`**

Crea un nuevo registro de servicio de delivery.

**Headers:**

```
Accept: application/json
Content-Type: application/json
Cookie: _session={cookie}
```

**Request Body:**

```json
{
  "folio": "DEL-2024-001",
  "cliente_id": 5,
  "sucursal_id": 1,
  "user_id": 5,
  "vehiculo_id": 3,
  "fecha_recepcion": "2024-01-28T08:00:00",
  "fecha_entrega": "2024-01-28T14:00:00",
  "estado": "entregado",
  "total": 850.00,
  "kilometraje": 45,
  "combustible": "75%",
  "danios": "Sin daños",
  "notas": "Entrega puntual"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `folio` | `string` | **Sí** | Identificador de delivery |
| `cliente_id` | `integer` | **Sí** | ID de cliente (existe) |
| `sucursal_id` | `integer` | **Sí** | ID de sucursal (existe) |
| `user_id` | `integer` | **Sí** | ID de usuario (existe) |
| `vehiculo_id` | `integer` | No | ID de vehículo |
| `fecha_recepcion` | `datetime` | No | Fecha/hora de recepción |
| `fecha_entrega` | `datetime` | No | Fecha/hora de entrega |
| `estado` | `string` | **Sí** | Estado de la entrega |
| `total` | `numeric` | **Sí** | Monto total (≥ 0) |
| `kilometraje` | `integer` | No | Lectura de kilometraje |
| `combustible` | `string` | No | Nivel/estado de combustible |
| `danios` | `string` | No | Notas sobre daños |
| `notas` | `string` | No | Notas adicionales |

**Validations:**

```
folio: required|string|max:255
cliente_id: required|exists:clientes,id
sucursal_id: required|exists:sucursales,id
user_id: required|exists:users,id
estado: required|string
total: required|numeric|min:0
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 10,
    "folio": "DEL-2024-001",
    "cliente_id": 5,
    "sucursal_id": 1,
    "estado": "en_transito",
    "total": 850.00,
    "created_at": "2024-01-28T08:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Registro de Delivery

**`GET /api/modules/delivery/{id}`**

Retorna un solo registro de delivery por ID.

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
    "folio": "DEL-2024-001",
    "cliente_id": 5,
    "sucursal_id": 1,
    "user_id": 5,
    "vehiculo_id": 3,
    "fecha_recepcion": "2024-01-28T08:00:00.000000Z",
    "fecha_entrega": "2024-01-28T14:00:00.000000Z",
    "estado": "entregado",
    "total": 850.00,
    "kilometraje": 45,
    "combustible": "75%",
    "danios": "Sin daños",
    "notas": "Entrega puntual",
    "cliente": { "id": 5, "nombre": "María López" },
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "created_at": "2024-01-28T08:00:00.000000Z",
    "updated_at": "2024-01-28T14:00:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Registro de Delivery

**`PUT /api/modules/delivery/{id}`**
**`PATCH /api/modules/delivery/{id}`**

Actualiza un registro de delivery existente.

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
    "estado": "entregado",
    "kilometraje": 45,
    "updated_at": "2024-01-28T14:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Registro de Delivery

**`DELETE /api/modules/delivery/{id}`**

Elimina un registro de delivery por ID.

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

- `folio` es el identificador único del servicio de delivery
- `fecha_recepcion` marca cuándo se recibió el paquete/vehículo
- `fecha_entrega` marca cuándo se entregó
- `kilometraje` registra la lectura del odómetro al momento de la entrega
- `combustible` documenta el nivel de combustible
- `danios` registra cualquier daño observado
