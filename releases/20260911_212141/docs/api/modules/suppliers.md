# Suppliers

Proveedores con datos fiscales, historial de compras y contactos comerciales.

---

## Endpoint Index

### Listar Proveedores

**`GET /api/suppliers`**

Retorna proveedores con conteo de compras realizadas.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `search` | `string` | Buscar por nombre, RNC/Cédula o email |
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
      "id": 5,
      "nombre": "Importadora Nacional",
      "email": "pedidos@importadoranacional.com",
      "telefono": "+1-809-555-7777",
      "whatsapp": "+1-809-555-7777",
      "direccion": "Zona Industrial, Sector 4",
      "ciudad": "Santo Domingo",
      "provincia": "Distrito Nacional",
      "codigo_postal": "10XXX",
      "rnc_cedula": "13098765432",
      "tipo_documento": "RNC",
      "activo": true,
      "contacto_principal": "Luis Ramírez",
      "nota_interna": "Proveedor oficial de bebidas",
      "compras_count": 45,
      "total_comprado": 2450000.00,
      "created_at": "2024-01-01T08:00:00.000000Z",
      "updated_at": "2024-01-20T14:30:00.000000Z"
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

### Crear Proveedor

**`POST /api/suppliers`**

Crea un nuevo proveedor con datos fiscales y comerciales.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Distribuidora Cibao",
  "email": "ventas@distribuidoracibao.com",
  "telefono": "+1-809-555-3333",
  "whatsapp": "+1-809-555-3333",
  "direccion": "Calle Independencia #78, San Pedro de Macorís",
  "ciudad": "San Pedro de Macorís",
  "provincia": "San Pedro de Macorís",
  "codigo_postal": "21000",
  "rnc_cedula": "13187654321",
  "tipo_documento": "RNC",
  "activo": true,
  "contacto_principal": "Ana Torres",
  "nota_interna": "Proveedor de granos y víveres"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | **Sí** | Nombre del proveedor |
| `email` | `string` | No | Email único |
| `telefono` | `string` | No | Teléfono único |
| `whatsapp` | `string` | No | WhatsApp |
| `direccion` | `string` | No | Dirección completa |
| `ciudad` | `string` | No | Ciudad |
| `provincia` | `string` | No | Provincia |
| `codigo_postal` | `string` | No | Código postal |
| `rnc_cedula` | `string` | No | RNC/Cédula (único) |
| `tipo_documento` | `string` | No | `RNC` o `CEDULA` |
| `activo` | `boolean` | No | Estado activo (default: `true`) |
| `contacto_principal` | `string` | No | Nombre del contacto |
| `nota_interna` | `string` | No | Notas internas |

**Validations:**

```
nombre: required|string|max:255
email: nullable|string|email|unique:proveedores,email
telefono: nullable|string|unique:proveedores,telefono
rnc_cedula: nullable|string|unique:proveedores,rnc_cedula
tipo_documento: nullable|in:RNC,CEDULA
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 13,
    "nombre": "Distribuidora Cibao",
    "email": "ventas@distribuidoracibao.com",
    "telefono": "+1-809-555-3333",
    "rnc_cedula": "13187654321",
    "activo": true,
    "contacto_principal": "Ana Torres",
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Proveedor

**`GET /api/suppliers/{supplier}`**

Retorna un proveedor con historial de compras.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
{
  "data": {
    "id": 5,
    "nombre": "Importadora Nacional",
    "email": "pedidos@importadoranacional.com",
    "telefono": "+1-809-555-7777",
    "whatsapp": "+1-809-555-7777",
    "direccion": "Zona Industrial, Sector 4",
    "ciudad": "Santo Domingo",
    "provincia": "Distrito Nacional",
    "codigo_postal": "10XXX",
    "rnc_cedula": "13098765432",
    "tipo_documento": "RNC",
    "activo": true,
    "contacto_principal": "Luis Ramírez",
    "nota_interna": "Proveedor oficial de bebidas",
    "compras_count": 45,
    "total_comprado": 2450000.00,
    "ultima_compra": "2024-01-28T10:00:00.000000Z",
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-20T14:30:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Proveedor

**`PUT /api/suppliers/{supplier}`**
**`PATCH /api/suppliers/{supplier}`**

Actualiza parcialmente un proveedor. Todos los campos son opcionales.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Distribuidora Cibao SRL",
  "contacto_principal": "María Torres",
  "nota_interna": "Proveedor de granos y víveres - Nuevo contrato 2024"
}
```

**Campos aceptados:** Mismos que Store (todos `sometimes`/opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 13,
    "nombre": "Distribuidora Cibao SRL",
    "contacto_principal": "María Torres",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Proveedor

**`DELETE /api/suppliers/{supplier}`**

Elimina un proveedor del sistema.

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

- `rnc_cedula`, `email` y `telefono` son únicos por tenant
- `compras_count` y `total_comprado` se calculan dinámicamente
- `ultima_compra` muestra la fecha de la última orden de compra
- `nota_interna` es para uso exclusivo del equipo operativo
- Desactivar un proveedor (`activo = false`) no elimina su historial
