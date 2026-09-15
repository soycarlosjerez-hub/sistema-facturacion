# Printers

Impresoras térmicas configuradas por sucursal con puertos, IPs y estado activo.

---

## Endpoint Index

### Listar Impresoras

**`GET /api/printers`**

Retorna impresoras con búsqueda y filtros por estado y sucursal.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `activa` | `boolean` | Filtrar por estado activo |
| `sucursal_id` | `integer` | Filtrar por sucursal |
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
      "nombre": "Impresora Tickets Principal",
      "tipo": "thermal",
      "sucursal_id": 1,
      "puerto": "USB0",
      "ip": "192.168.1.50",
      "activa": true,
      "configuracion": {
        "width": 58,
        "encoding": "UTF-8",
        "cut_paper": true
      },
      "tenant_id": 1,
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
    "total": 2
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Impresora

**`POST /api/printers`**

Crea una nueva impresora configurada.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Impresora Tickets Principal",
  "tipo": "thermal",
  "sucursal_id": 1,
  "puerto": "USB0",
  "ip": "192.168.1.50",
  "activa": true,
  "configuracion": {
    "width": 58,
    "encoding": "UTF-8",
    "cut_paper": true
  }
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | **Sí** | Nombre de la impresora |
| `tipo` | `string` | **Sí** | Tipo: `thermal`, `network`, `usb` |
| `sucursal_id` | `integer` | **Sí** | ID de sucursal (existe) |
| `puerto` | `string` | No | Puerto de conexión |
| `ip` | `string` | No | Dirección IP |
| `activa` | `boolean` | No | Estado activo (default: `true`) |
| `configuracion` | `object` | No | Configuración JSON |

**Validations:**

```
nombre: required|string|max:255
tipo: required|in:thermal,network,usb
sucursal_id: required|exists:sucursales,id
ip: nullable|string|ip
configuracion: nullable|json
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 3,
    "nombre": "Impresora Tickets Principal",
    "tipo": "thermal",
    "sucursal_id": 1,
    "puerto": "USB0",
    "ip": "192.168.1.50",
    "activa": true,
    "configuracion": {
      "width": 58,
      "encoding": "UTF-8",
      "cut_paper": true
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

### Obtener Impresora

**`GET /api/printers/{printer}`**

Retorna una impresora con su sucursal.

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
    "nombre": "Impresora Tickets Principal",
    "tipo": "thermal",
    "sucursal_id": 1,
    "puerto": "USB0",
    "ip": "192.168.1.50",
    "activa": true,
    "configuracion": {
      "width": 58,
      "encoding": "UTF-8",
      "cut_paper": true
    },
    "tenant_id": 1,
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-20T14:30:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Impresora

**`PUT /api/printers/{printer}`**
**`PATCH /api/printers/{printer}`**

Actualiza parcialmente una impresora.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "ip": "192.168.1.51",
  "configuracion": {
    "width": 80,
    "encoding": "UTF-8",
    "cut_paper": true
  }
}
```

**Campos aceptados:** Mismos que Store (todos opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "ip": "192.168.1.51",
    "configuracion": {
      "width": 80,
      "encoding": "UTF-8",
      "cut_paper": true
    },
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Impresora

**`DELETE /api/printers/{printer}`**

Elimina una impresora.

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

- `tipo` clasifica el hardware: `thermal` (térmica), `network` (red), `usb` (directa)
- `puerto` identifica el puerto físico o lógico
- `ip` se usa para impresoras de red
- `configuracion` contiene parámetros como ancho de papel, codificación y corte
- `activa` controla si la impresora aparece en selecciones de impresión
