# Cash Registers

Cajas registradoras con gestión de sesiones y estados.

---

## Endpoint Index

### Listar Cajas

**`GET /api/cash-registers`**

Retorna cajas con su sucursal y sesión activa.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `activo` | `boolean` | Filtrar por estado activo |

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
      "nombre": "Caja Principal",
      "codigo": "CAJA-001",
      "sucursal_id": 1,
      "ubicacion": "Entrada principal",
      "estado": "disponible",
      "activo": true,
      "tenant_id": 1,
      "sucursal": {
        "id": 1,
        "nombre": "Matriz"
      },
      "sesiones_count": 45,
      "sesion_activa": null,
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

### Crear Caja

**`POST /api/cash-registers`**

Crea una nueva caja registradora.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Caja Principal",
  "codigo": "CAJA-001",
  "sucursal_id": 1,
  "ubicacion": "Entrada principal",
  "estado": "disponible",
  "activo": true
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | **Sí** | Nombre de la caja |
| `codigo` | `string` | No | Código único |
| `sucursal_id` | `integer` | **Sí** | ID de sucursal (existe) |
| `ubicacion` | `string` | No | Ubicación física |
| `estado` | `string` | No | Estado: `disponible`, `ocupada`, `mantenimiento` |
| `activo` | `boolean` | No | Estado activo (default: `true`) |

**Validations:**

```
nombre: required|string|max:255
codigo: nullable|string|unique:cajas,codigo
sucursal_id: required|exists:sucursales,id
estado: nullable|in:disponible,ocupada,mantenimiento
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 4,
    "nombre": "Caja Principal",
    "codigo": "CAJA-001",
    "sucursal_id": 1,
    "ubicacion": "Entrada principal",
    "estado": "disponible",
    "activo": true,
    "tenant_id": 1,
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Caja

**`GET /api/cash-registers/{cashRegister}`**

Retorna una caja con su sesión activa.

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
    "nombre": "Caja Principal",
    "codigo": "CAJA-001",
    "sucursal_id": 1,
    "ubicacion": "Entrada principal",
    "estado": "disponible",
    "activo": true,
    "tenant_id": 1,
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "sesiones_count": 45,
    "sesion_activa": null,
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-20T14:30:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Caja

**`PUT /api/cash-registers/{cashRegister}`**
**`PATCH /api/cash-registers/{cashRegister}`**

Actualiza parcialmente una caja. Todos los campos son opcionales.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Caja Principal - Matriz",
  "ubicacion": "Mostrador central"
}
```

**Campos aceptados:** Mismos que Store (todos `sometimes`/opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "nombre": "Caja Principal - Matriz",
    "ubicacion": "Mostrador central",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Caja

**`DELETE /api/cash-registers/{cashRegister}`**

Elimina una caja.

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
- `estado` refleja el estado actual: `disponible`, `ocupada` o `mantenimiento`
- `sesion_activa` muestra la sesión abierta actualmente (si existe)
- Las relaciones incluyen: `sesiones`, `sesionActiva`
- Cada sucursal puede tener múltiples cajas
