# Price Lists API

Service module for managing price lists (listas de precios).

## Base URL

```
/api/modules/price-lists
```

## Authentication

Requires authentication with `auth` session cookie.

---

## Endpoint Index

### Listar Listas de Precios

**`GET /api/modules/price-lists`**

Retorna lista paginada de listas de precios con filtros.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `activa` | `boolean` | Filtrar por estado activo |

**Headers:**

```
Accept: application/json
Cookie: _session={cookie}
```

**Response `200 OK` — Colección paginada de objetos de lista de precios:**

```json
{
  "data": [
    {
      "id": 1,
      "nombre": "Lista Premium",
      "descripcion": "Precios para clientes premium",
      "porcentaje": 25.00,
      "sucursal_id": 1,
      "activa": true,
      "tenant_id": 1,
      "sucursal": {
        "id": 1,
        "nombre": "Matriz"
      },
      "created_at": "2024-01-01T08:00:00.000000Z",
      "updated_at": "2024-01-20T14:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "last_page": 1,
    "from": 1,
    "to": 1,
    "total": 1
  }
}
```

---

## Endpoint Store

### Crear Lista de Precios

**`POST /api/modules/price-lists`**

Crea una nueva lista de precios.

**Headers:**

```
Accept: application/json
Content-Type: application/json
Cookie: _session={cookie}
```

**Request Body:**

```json
{
  "nombre": "Lista Premium",
  "descripcion": "Precios para clientes premium",
  "porcentaje": 25.00,
  "sucursal_id": 1,
  "activa": true,
  "tenant_id": 1
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | **Sí** | Nombre de la lista de precios |
| `descripcion` | `string` | No | Descripción |
| `porcentaje` | `numeric` | **Sí** | Porcentaje de aumento (≥ 0) |
| `sucursal_id` | `integer` | **Sí** | ID de sucursal (existe) |
| `activa` | `boolean` | No | Si la lista está activa (default: `true`) |
| `tenant_id` | `integer` | **Sí** | ID de tenant (existe) |

**Validations:**

```
nombre: required|string|max:255
porcentaje: required|numeric|min:0
sucursal_id: required|exists:sucursales,id
tenant_id: required|exists:tenants,id
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 5,
    "nombre": "Lista Premium",
    "descripcion": "Precios para clientes premium",
    "porcentaje": 25.00,
    "sucursal_id": 1,
    "activa": true,
    "tenant_id": 1,
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Lista de Precios

**`GET /api/modules/price-lists/{id}`**

Retorna una sola lista de precios por ID.

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
    "nombre": "Lista Premium",
    "descripcion": "Precios para clientes premium",
    "porcentaje": 25.00,
    "sucursal_id": 1,
    "activa": true,
    "tenant_id": 1,
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-20T14:30:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Lista de Precios

**`PUT /api/modules/price-lists/{id}`**
**`PATCH /api/modules/price-lists/{id}`**

Actualiza una lista de precios existente.

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
    "nombre": "Lista Premium Actualizada",
    "porcentaje": 30.00,
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Lista de Precios

**`DELETE /api/modules/price-lists/{id}`**

Elimina una lista de precios por ID.

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

- `porcentaje` representa el incremento porcentual sobre el precio base
- `activa` controla si la lista se aplica automáticamente en cálculos
- Cada sucursal puede tener múltiples listas de precios
- `tenant_id` asegura aislamiento multi-tenant
