# Instances

Instancias de negocio multi-tenant. Cada instancia representa un cliente del SaaS con su propio espacio de datos, configuración y módulos.

---

## Endpoint Index

### Listar Instancias

**`GET /api/instances`**

Retorna instancias de negocio con relaciones (tipo, propietario, usuarios, sucursales, módulos).

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `active` | `boolean` | Filtrar por estado activo |
| `al_dia` | `boolean` | Instancias al día (sin vencimiento) |
| `bloqueadas` | `boolean` | Instancias bloqueadas |
| `owner_id` | `integer` | Filtrar por ID del propietario |

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
      "nombre": "Restaurante El Buen Sabor",
      "slug": "el-buen-sabor",
      "rnc": "13012345678",
      "email": "contacto@elbuensabor.com",
      "telefono": "+1-809-555-1234",
      "direccion": "Av. Principal #45, Santo Domingo",
      "business_type": {
        "id": 1,
        "key": "restaurant",
        "nombre": "Restaurante"
      },
      "owner": {
        "id": 5,
        "name": "Carlos Martínez",
        "email": "carlos@elbuensabor.com"
      },
      "users_count": 12,
      "sucursales_count": 2,
      "modules": [
        { "modulo_key": "restaurant", "visible": true, "orden": 1 },
        { "modulo_key": "inventory", "visible": true, "orden": 2 },
        { "modulo_key": "reservations", "visible": true, "orden": 3 }
      ],
      "activo": true,
      "bloqueado": false,
      "setup_completed": true,
      "fecha_vencimiento": "2025-12-31",
      "costo_mensual": 2999.00,
      "ultimo_pago": "2024-01-01",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-20T10:00:00.000000Z"
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

### Crear Instancia

**`POST /api/instances`**

Crea una nueva instancia de negocio. Requiere un `business_type_id` válido.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Tienda Tech Solutions",
  "slug": "tech-solutions",
  "rnc": "13198765432",
  "email": "admin@techsolutions.com",
  "telefono": "+1-809-555-5678",
  "direccion": "Plaza Nacional, Local 205",
  "business_type_id": 2,
  "owner_user_id": 10,
  "configuracion": {
    "zona_horaria": "America/Santo_Domingo",
    "moneda": "RD$",
    "itbis_default": 18
  },
  "activo": true,
  "fecha_vencimiento": "2025-06-30",
  "costo_mensual": 1999.00,
  "setup_completed": false
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | **Sí** | Nombre comercial de la instancia |
| `slug` | `string` | **Sí** | Slug único para URLs |
| `rnc` | `string` | No | RNC/Cédula del negocio |
| `email` | `string` | No | Email corporativo |
| `telefono` | `string` | No | Teléfono de contacto |
| `direccion` | `string` | No | Dirección física |
| `business_type_id` | `integer` | **Sí** | ID del tipo de negocio (existe) |
| `owner_user_id` | `integer` | No | ID del usuario propietario (existe) |
| `configuracion` | `object` | No | Configuración JSON personalizada |
| `activo` | `boolean` | No | Estado activo (default: `true`) |
| `fecha_vencimiento` | `date` | No | Fecha de vencimiento de suscripción |
| `costo_mensual` | `decimal` | No | Precio mensual (≥ 0) |
| `bloqueado` | `boolean` | No | Bloqueo manual (default: `false`) |
| `motivo_bloqueo` | `string` | No | Razón del bloqueo |
| `setup_completed` | `boolean` | No | Setup wizard completado (default: `false`) |

**Validations:**

```
nombre: required|string|max:255
slug: required|string|unique:business_instances,slug
email: nullable|email
business_type_id: required|exists:business_types,id
owner_user_id: nullable|exists:users,id
fecha_vencimiento: nullable|date
costo_mensual: nullable|numeric|min:0
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 4,
    "nombre": "Tienda Tech Solutions",
    "slug": "tech-solutions",
    "rnc": "13198765432",
    "email": "admin@techsolutions.com",
    "business_type": { "id": 2, "key": "retail", "nombre": "Tienda" },
    "owner": { "id": 10, "name": "Ana Rodríguez" },
    "activo": true,
    "setup_completed": false,
    "created_at": "2024-01-21T10:00:00.000000Z",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Instancia

**`GET /api/instances/{businessInstance}`**

Retorna una instancia con todas sus relaciones cargadas.

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
    "nombre": "Restaurante El Buen Sabor",
    "slug": "el-buen-sabor",
    "rnc": "13012345678",
    "email": "contacto@elbuensabor.com",
    "telefono": "+1-809-555-1234",
    "direccion": "Av. Principal #45, Santo Domingo",
    "business_type": {
      "id": 1,
      "key": "restaurant",
      "nombre": "Restaurante",
      "modules": [
        { "id": 1, "key": "restaurant", "nombre": "Restaurante" },
        { "id": 2, "key": "inventory", "nombre": "Inventario" }
      ]
    },
    "owner": { "id": 5, "name": "Carlos Martínez" },
    "users_count": 12,
    "sucursales_count": 2,
    "modules": [
      { "modulo_key": "restaurant", "visible": true, "orden": 1 },
      { "modulo_key": "inventory", "visible": true, "orden": 2 },
      { "modulo_key": "reservations", "visible": true, "orden": 3 }
    ],
    "activo": true,
    "bloqueado": false,
    "setup_completed": true,
    "fecha_vencimiento": "2025-12-31",
    "costo_mensual": 2999.00,
    "ultimo_pago": "2024-01-01",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-20T10:00:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Instancia

**`PUT /api/instances/{businessInstance}`**
**`PATCH /api/instances/{businessInstance}`**

Actualiza parcialmente una instancia. Todos los campos son opcionales.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Restaurante El Buen Sabor - Sucursal Central",
  "fecha_vencimiento": "2026-12-31",
  "configuracion": {
    "moneda": "RD$",
    "itbis_default": 18
  }
}
```

**Campos aceptados:** Mismos que Store (todos `sometimes`/opcionales).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "nombre": "Restaurante El Buen Sabor - Sucursal Central",
    "fecha_vencimiento": "2026-12-31",
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Instancia

**`DELETE /api/instances/{businessInstance}`**

Elimina (soft delete) una instancia de negocio.

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

## Special Endpoints

### Toggle Módulo

Activar/desactivar la visibilidad de un módulo para una instancia.

**`PATCH /api/instances/{businessInstance}/toggle-module`**

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "modulo_key": "loyalty_program",
  "visible": true,
  "orden": 5
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `modulo_key` | `string` | **Sí** | Key del módulo (existe en business_types.modules) |
| `visible` | `boolean` | **Sí** | Mostrar u ocultar módulo |
| `orden` | `integer` | No | Posición en menú (≥ 0) |

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "modules": [
      { "modulo_key": "restaurant", "visible": true, "orden": 1 },
      { "modulo_key": "loyalty_program", "visible": true, "orden": 5 }
    ]
  },
  "message": "Module visibility updated"
}
```

---

## Notas

- Cada instancia tiene su propio **espacio de datos aislado** por tenant
- Los **módulos** se gestionan por tipo de negocio y se habilitan individualmente por instancia
- Una instancia **bloqueada** (`bloqueado = true`) rechaza todas las peticiones con 403
- El **propietario** (`owner_user_id`) tiene rol `owner` y bypass de tenant
- `setup_completed` controla si el wizard de configuración inicial fue completado
- `fecha_vencimiento` se usa para calcular si la instancia está `al_dia`
- Los módulos visibles determinan qué opciones aparecen en el menú lateral del panel
