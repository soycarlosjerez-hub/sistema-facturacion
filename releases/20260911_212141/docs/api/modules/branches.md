# Branches

Sucursales de la empresa con gestión de datos fiscales y relaciones con otros recursos.

---

## Endpoint Index

### Listar Sucursales

**`GET /api/branches`**

Retorna sucursales con conteo de usuarios, ventas, compras, cajas y gastos.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `activa` | `boolean` | Filtrar por estado activo |
| `search` | `string` | Buscar por nombre o código |

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
      "codigo": "MAT-001",
      "nombre": "Matriz",
      "direccion": "Av. Principal #45, Santo Domingo",
      "telefono": "+1-809-555-0001",
      "email": "matriz@empresa.com",
      "rnc": "13012345678",
      "activa": true,
      "es_matriz": true,
      "tenant_id": 1,
      "users_count": 12,
      "ventas_count": 1542,
      "compras_count": 234,
      "cajas_count": 3,
      "gastos_count": 89,
      "mesas_count": 15,
      "created_at": "2024-01-01T08:00:00.000000Z",
      "updated_at": "2024-01-20T14:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 1,
    "per_page": 15,
    "total": 2
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Sucursal

**`POST /api/branches`**

Crea una nueva sucursal.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "codigo": "SUC-NORTE-001",
  "nombre": "Sucursal Norte",
  "direccion": "Av. Winston Churchill #123, Naco",
  "telefono": "+1-809-555-0002",
  "email": "norte@empresa.com",
  "rnc": "13012345678",
  "activa": true,
  "es_matriz": false
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `codigo` | `string` | **Sí** | Código único de la sucursal |
| `nombre` | `string` | **Sí** | Nombre de la sucursal |
| `direccion` | `string` | No | Dirección completa |
| `telefono` | `string` | No | Teléfono |
| `email` | `string` | No | Email |
| `rnc` | `string` | No | RNC fiscal |
| `activa` | `boolean` | No | Estado activo (default: `true`) |
| `es_matriz` | `boolean` | No | Es la sede principal |

**Validations:**

```
codigo: required|string|unique:sucursales,codigo
nombre: required|string|max:255
email: nullable|string|email
rnc: nullable|string
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 2,
    "codigo": "SUC-NORTE-001",
    "nombre": "Sucursal Norte",
    "direccion": "Av. Winston Churchill #123, Naco",
    "telefono": "+1-809-555-0002",
    "email": "norte@empresa.com",
    "activa": true,
    "es_matriz": false,
    "tenant_id": 1,
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Sucursal

**`GET /api/branches/{branch}`**

Retorna una sucursal con todas sus relaciones.

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
    "codigo": "MAT-001",
    "nombre": "Matriz",
    "direccion": "Av. Principal #45, Santo Domingo",
    "telefono": "+1-809-555-0001",
    "email": "matriz@empresa.com",
    "rnc": "13012345678",
    "activa": true,
    "es_matriz": true,
    "tenant_id": 1,
    "users": [
      { "id": 5, "name": "Carlos Martínez", "role": "admin" }
    ],
    "ventas_count": 1542,
    "compras_count": 234,
    "cajas_count": 3,
    "gastos_count": 89,
    "mesas_count": 15,
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-20T14:30:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Sucursal

**`PUT /api/branches/{branch}`**
**`PATCH /api/branches/{branch}`**

Actualiza parcialmente una sucursal. Todos los campos son opcionales.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Matriz Central",
  "telefono": "+1-809-555-9999"
}
```

**Campos aceptados:** Mismos que Store (todos `sometimes`/opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "nombre": "Matriz Central",
    "telefono": "+1-809-555-9999",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Sucursal

**`DELETE /api/branches/{branch}`**

Elimina una sucursal.

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

- `codigo` debe ser único dentro del tenant
- `es_matriz` indica cuál es la sede principal (solo una por tenant)
- Las relaciones incluyen: `usuarios`, `ventas`, `compras`, `cajas`, `gastos`, `mesas`
- Desactivar una sucursal (`activa = false`) previene nuevas transacciones
