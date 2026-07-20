# Settings

Configuración general del sistema con pares clave-valor.

---

## Endpoint Index

### Listar Configuraciones

**`GET /api/settings`**

Retorna todas las configuraciones del sistema.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |

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
      "clave": "empresa_nombre",
      "valor": "Empresa Demo SRL",
      "descripcion": "Nombre legal de la empresa",
      "created_at": "2024-01-01T08:00:00.000000Z",
      "updated_at": "2024-01-01T08:00:00.000000Z"
    },
    {
      "id": 2,
      "clave": "empresa_logo",
      "valor": "logo.png",
      "descripcion": "Logo de la empresa",
      "created_at": "2024-01-01T08:00:00.000000Z",
      "updated_at": "2024-01-01T08:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 2,
    "per_page": 15,
    "total": 2
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Configuración

**`POST /api/settings`**

Crea un nuevo par clave-valor de configuración.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "clave": "impuesto_renta_anual",
  "valor": "27",
  "descripcion": "Tasa anual de impuesto sobre la renta (%)"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `clave` | `string` | **Sí** | Identificador único de la configuración |
| `valor` | `string` | **Sí** | Valor de la configuración |
| `descripcion` | `string` | No | Descripción del parámetro |

**Validations:**

```
clave: required|string|unique:settings,clave
valor: required|string
descripcion: nullable|string|max:500
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 3,
    "clave": "impuesto_renta_anual",
    "valor": "27",
    "descripcion": "Tasa anual de impuesto sobre la renta (%)",
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Configuración

**`GET /api/settings/{setting}`**

Retorna una configuración individual.

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
    "clave": "empresa_nombre",
    "valor": "Empresa Demo SRL",
    "descripcion": "Nombre legal de la empresa",
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-01T08:00:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Configuración

**`PUT /api/settings/{setting}`**
**`PATCH /api/settings/{setting}`**

Actualiza parcialmente una configuración.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "valor": "Nueva Empresa SRL",
  "descripcion": "Nombre legal actualizado 2024"
}
```

**Campos aceptados:** Mismos que Store (todos opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "clave": "empresa_nombre",
    "valor": "Nueva Empresa SRL",
    "descripcion": "Nombre legal actualizado 2024",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Configuración

**`DELETE /api/settings/{setting}`**

Elimina una configuración.

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

- `clave` es única y sirve como identificador técnico
- `valor` siempre se almacena como string (se convierte según necesidad)
- Las configuraciones críticas no deben eliminarse
- Se recomienda usar nombres descriptivos para `clave` (snake_case)
