# Laundry API

Service module for managing laundry/lavadero services.

## Base URL

```
/api/modules/laundry
```

## Authentication

Requires authentication with `auth` session cookie.

---

## Endpoint Index

### Listar Lavandería

**`GET /api/modules/laundry`**

Retorna lista paginada de registros de lavandería con filtros.

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

**Response `200 OK` — Colección paginada de objetos de lavandería:**

```json
{
  "data": [
    {
      "id": 1,
      "folio": "LAV-2024-001",
      "cliente_id": 5,
      "sucursal_id": 1,
      "user_id": 5,
      "vehiculo_id": null,
      "fecha_ingreso": "2024-01-28T08:00:00.000000Z",
      "fecha_entrega": "2024-02-01T18:00:00.000000Z",
      "estado": "en_proceso",
      "servicio": "lavado_y_planchado",
      "total": 1500.00,
      "notas": "Ropa delicada",
      "cliente": {
        "id": 5,
        "nombre": "María López"
      },
      "sucursal": {
        "id": 1,
        "nombre": "Matriz"
      },
      "created_at": "2024-01-28T08:00:00.000000Z",
      "updated_at": "2024-01-28T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "last_page": 3,
    "from": 1,
    "to": 15,
    "total": 42
  }
}
```

---

## Endpoint Store

### Crear Registro de Lavandería

**`POST /api/modules/laundry`**

Crea un nuevo registro de servicio de lavandería.

**Headers:**

```
Accept: application/json
Content-Type: application/json
Cookie: _session={cookie}
```

**Request Body:**

```json
{
  "folio": "LAV-2024-001",
  "cliente_id": 5,
  "sucursal_id": 1,
  "user_id": 5,
  "fecha_ingreso": "2024-01-28T08:00:00",
  "fecha_entrega": "2024-02-01T18:00:00",
  "estado": "en_proceso",
  "servicio": "lavado_y_planchado",
  "total": 1500.00,
  "notas": "Ropa delicada"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `folio` | `string` | **Sí** | Identificador de lavandería |
| `cliente_id` | `integer` | **Sí** | ID de cliente (existe) |
| `sucursal_id` | `integer` | **Sí** | ID de sucursal (existe) |
| `user_id` | `integer` | **Sí** | ID de usuario (existe) |
| `vehiculo_id` | `integer` | No | ID de vehículo |
| `fecha_ingreso` | `datetime` | No | Fecha/hora de ingreso |
| `fecha_entrega` | `datetime` | No | Fecha/hora de entrega |
| `estado` | `string` | **Sí** | Estado del servicio |
| `servicio` | `string` | **Sí** | Tipo de servicio |
| `total` | `numeric` | **Sí** | Monto total (≥ 0) |
| `notas` | `string` | No | Notas |

**Validations:**

```
folio: required|string|max:255
cliente_id: required|exists:clientes,id
sucursal_id: required|exists:sucursales,id
user_id: required|exists:users,id
estado: required|string
servicio: required|string
total: required|numeric|min:0
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 10,
    "folio": "LAV-2024-001",
    "cliente_id": 5,
    "sucursal_id": 1,
    "estado": "en_proceso",
    "servicio": "lavado_y_planchado",
    "total": 1500.00,
    "created_at": "2024-01-28T08:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Registro de Lavandería

**`GET /api/modules/laundry/{id}`**

Retorna un solo registro de lavandería por ID.

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
    "folio": "LAV-2024-001",
    "cliente_id": 5,
    "sucursal_id": 1,
    "user_id": 5,
    "vehiculo_id": null,
    "fecha_ingreso": "2024-01-28T08:00:00.000000Z",
    "fecha_entrega": "2024-02-01T18:00:00.000000Z",
    "estado": "en_proceso",
    "servicio": "lavado_y_planchado",
    "total": 1500.00,
    "notas": "Ropa delicada",
    "cliente": { "id": 5, "nombre": "María López" },
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "created_at": "2024-01-28T08:00:00.000000Z",
    "updated_at": "2024-01-28T10:00:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Registro de Lavandería

**`PUT /api/modules/laundry/{id}`**
**`PATCH /api/modules/laundry/{id}`**

Actualiza un registro de lavandería existente.

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
    "updated_at": "2024-02-01T18:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Registro de Lavandería

**`DELETE /api/modules/laundry/{id}`**

Elimina un registro de lavandería por ID.

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

- `folio` es el identificador único del servicio
- `fecha_ingreso` y `fecha_entrega` definen el período del servicio
- `servicio` clasifica el tipo: `lavado`, `planchado`, `lavado_y_planchado`, `manchas`, etc.
- `estado` refleja el progreso del servicio: `recibido`, `en_proceso`, `listo`, `entregado`
- `vehiculo_id` es opcional y vincula el servicio a un vehículo específico
