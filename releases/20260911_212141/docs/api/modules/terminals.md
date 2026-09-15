# Terminals

Terminales POS con soporte para soft-delete y asignación por sucursal.

---

## Endpoint Index

### Listar Terminales

**`GET /api/terminals`**

Retorna terminales con su sucursal y estado.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `search` | `string` | Buscar por nombre o IP |

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
      "nombre": "Terminal POS Principal",
      "ip": "192.168.1.100",
      "puerto": 8080,
      "sucursal_id": 1,
      "activo": true,
      "deleted_at": null,
      "sucursal": { "id": 1, "nombre": "Matriz" },
      "created_at": "2024-01-01T08:00:00.000000Z",
      "updated_at": "2024-01-20T14:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 1,
    "per_page": 15,
    "total": 4
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Terminal

**`POST /api/terminals`**

Crea una nueva terminal POS.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Terminal POS Principal",
  "ip": "192.168.1.100",
  "puerto": 8080,
  "sucursal_id": 1,
  "activo": true
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | **Sí** | Nombre de la terminal |
| `ip` | `string` | No | Dirección IP |
| `puerto` | `integer` | No | Puerto de conexión |
| `sucursal_id` | `integer` | **Sí** | ID de sucursal (existe) |
| `activo` | `boolean` | No | Estado activo (default: `true`) |

**Validations:**

```
nombre: required|string|max:255
ip: nullable|string|ip
puerto: nullable|integer|min:1|max:65535
sucursal_id: required|exists:sucursales,id
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 5,
    "nombre": "Terminal POS Principal",
    "ip": "192.168.1.100",
    "puerto": 8080,
    "sucursal_id": 1,
    "activo": true,
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Terminal

**`GET /api/terminals/{terminal}`**

Retorna una terminal con su sucursal.

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
    "nombre": "Terminal POS Principal",
    "ip": "192.168.1.100",
    "puerto": 8080,
    "sucursal_id": 1,
    "activo": true,
    "deleted_at": null,
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-20T14:30:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Terminal

**`PUT /api/terminals/{terminal}`**
**`PATCH /api/terminals/{terminal}`**

Actualiza parcialmente una terminal.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "ip": "192.168.1.101",
  "puerto": 9090
}
```

**Campos aceptados:** Mismos que Store (todos opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "ip": "192.168.1.101",
    "puerto": 9090,
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Terminal (Soft Delete)

**`DELETE /api/terminals/{terminal}`**

Elimina lógicamente una terminal (soft delete).

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

- Las terminales usan **soft delete** (`deleted_at`)
- `ip` y `puerto` identifican la dirección de conexión del dispositivo físico
- Cada terminal pertenece a una sucursal específica
- `activo` controla si la terminal puede recibir transacciones
