# NCF Sequences

Secuencias NCF (Número de Control de Factura) para facturación fiscal dominicana.

---

## Endpoint Index

### Listar Secuencias NCF

**`GET /api/ncf-sequences`**

Retorna secuencias NCF con filtros por sucursal, tipo y estado.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `sucursal_id` | `integer` | Filtrar por sucursal |
| `tipo_ncf` | `string` | Filtrar por tipo de NCF |
| `activa` | `boolean` | Filtrar por estado activo |

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
      "sucursal_id": 1,
      "tipo_ncf": "Factura de Crédito Fiscal",
      "ncf": "B0100000001",
      "hasta_ncf": "B0199999999",
      "usado_hasta": "B0100000042",
      "activa": true,
      "tenant_id": 1,
      "sucursal": { "id": 1, "nombre": "Matriz" },
      "created_at": "2024-01-01T08:00:00.000000Z",
      "updated_at": "2024-01-28T14:22:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 1,
    "per_page": 15,
    "total": 5
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Secuencia NCF

**`POST /api/ncf-sequences`**

Crea una nueva secuencia NCF para una sucursal.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "sucursal_id": 1,
  "tipo_ncf": "Factura de Crédito Fiscal",
  "ncf": "B0100000001",
  "hasta_ncf": "B0199999999",
  "activa": true
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `sucursal_id` | `integer` | **Sí** | ID de sucursal (existe) |
| `tipo_ncf` | `string` | **Sí** | Tipo de NCF |
| `ncf` | `string` | **Sí** | NCF inicial de la secuencia |
| `hasta_ncf` | `string` | **Sí** | NCF final de la secuencia |
| `activa` | `boolean` | No | Estado activo (default: `true`) |

**Validations:**

```
sucursal_id: required|exists:sucursales,id
tipo_ncf: required|string|max:255
ncf: required|string|max:255
hasta_ncf: required|string|max:255
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 6,
    "sucursal_id": 1,
    "tipo_ncf": "Factura de Crédito Fiscal",
    "ncf": "B0100000001",
    "hasta_ncf": "B0199999999",
    "usado_hasta": "B0100000001",
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

### Obtener Secuencia NCF

**`GET /api/ncf-sequences/{ncfSequence}`**

Retorna una secuencia NCF individual.

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
    "sucursal_id": 1,
    "tipo_ncf": "Factura de Crédito Fiscal",
    "ncf": "B0100000001",
    "hasta_ncf": "B0199999999",
    "usado_hasta": "B0100000042",
    "activa": true,
    "tenant_id": 1,
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-28T14:22:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Secuencia NCF

**`PUT /api/ncf-sequences/{ncfSequence}`**
**`PATCH /api/ncf-sequences/{ncfSequence}`**

Actualiza parcialmente una secuencia NCF.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "activa": false
}
```

**Campos aceptados:** Mismos que Store (todos opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "activa": false,
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Secuencia NCF

**`DELETE /api/ncf-sequences/{ncfSequence}`**

Elimina una secuencia NCF.

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

- `usado_hasta` se actualiza automáticamente al generar ventas
- `activa` determina si la secuencia puede usarse para nuevas ventas
- Cada sucursal necesita secuencias para cada tipo de NCF que utilice
- Tipos comunes: `Factura de Crédito Fiscal`, `Factura de Consumidor Final`, `Nota de Débito`, `Nota de Crédito`
- `ncf` y `hasta_ncf` definen el rango de números válidos
