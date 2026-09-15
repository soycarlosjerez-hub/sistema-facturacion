# Backups

Historial de respaldos del sistema con búsqueda por nombre, tamaño, tipo y estado.

---

## Endpoint Index

### Listar Respaldo

**`GET /api/backups`**

Retorna respaldos existentes con metadatos.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `search` | `string` | Buscar por nombre, tamaño, tipo o estado |

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
      "filename": "backup_2024-01-28_030000.sql.gz",
      "size": 52428800,
      "type": "database",
      "status": "completed",
      "notes": "Backup diario automático",
      "created_at": "2024-01-28T03:00:00.000000Z",
      "updated_at": "2024-01-28T03:15:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 1,
    "per_page": 15,
    "total": 12
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Respaldo

**`POST /api/backups`**

Genera un nuevo respaldo del sistema.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "type": "database",
  "notes": "Backup manual previo a mantenimiento"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `type` | `string` | No | Tipo: `database`, `full`, `files` |
| `notes` | `string` | No | Notas del respaldo |

**Validations:**

```
type: nullable|in:database,full,files
notes: nullable|string|max:500
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 13,
    "filename": "backup_2024-01-29_100000.sql.gz",
    "size": 53687091,
    "type": "database",
    "status": "processing",
    "notes": "Backup manual previo a mantenimiento",
    "created_at": "2024-01-29T10:00:00.000000Z",
    "updated_at": "2024-01-29T10:00:00.000000Z"
  },
  "message": "Backup initiated"
}
```

---

## Endpoint Show

### Obtener Respaldo

**`GET /api/backups/{backup}`**

Retorna un respaldo individual.

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
    "filename": "backup_2024-01-28_030000.sql.gz",
    "size": 52428800,
    "type": "database",
    "status": "completed",
    "notes": "Backup diario automático",
    "created_at": "2024-01-28T03:00:00.000000Z",
    "updated_at": "2024-01-28T03:15:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Respaldo

**`PUT /api/backups/{backup}`**
**`PATCH /api/backups/{backup}`**

Actualiza parcialmente un respaldo.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "notes": "Backup importante - guardar 90 días"
}
```

**Campos aceptados:** Mismos que Store (todos opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "notes": "Backup importante - guardar 90 días",
    "updated_at": "2024-01-29T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Respaldo

**`DELETE /api/backups/{backup}`**

Elimina un respaldo del disco.

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

- `size` está en bytes
- `status` puede ser: `processing`, `completed`, `failed`
- `type` clasifica el tipo de respaldo: `database`, `full` (BD + archivos), `files`
- Los respaldos se eliminan físicamente del disco al hacer `delete`
