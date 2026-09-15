# Users

Gestión de usuarios internos del sistema con roles, sucursales y estados de conexión en tiempo real.

---

## Endpoint Me

### Perfil del Usuario Autenticado

**`GET /api/users/me`**

Retorna el perfil completo del usuario autenticado con todas sus relaciones.

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
    "name": "Carlos Martínez",
    "email": "carlos@elbuensabor.com",
    "email_verified_at": "2024-01-01T10:00:00.000000Z",
    "business_instance": {
      "id": 1,
      "nombre": "Restaurante El Buen Sabor",
      "slug": "el-buen-sabor"
    },
    "instance_role": {
      "id": 2,
      "nombre": "Gerente",
      "permissions": ["ventas.view", "ventas.create", "productos.manage"]
    },
    "sucursal": {
      "id": 1,
      "nombre": "Matriz"
    },
    "last_seen": "2024-01-21T09:45:00.000000Z",
    "is_online": true,
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-20T14:30:00.000000Z"
  }
}
```

---

## Endpoint Index

### Listar Usuarios

**`GET /api/users`**

Retorna usuarios del tenant con información de rol, sucursal y estado de conexión.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15) |
| `business_instance_id` | `integer` | Filtrar por instancia |
| `role` | `string` | Filtrar por rol (`user`, `admin`, `supervisor`) |
| `online` | `boolean` | `true` = solo desconectados, `null` = últimos vistos al final |

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
      "name": "Carlos Martínez",
      "email": "carlos@elbuensabor.com",
      "business_instance": {
        "id": 1,
        "nombre": "Restaurante El Buen Sabor",
        "business_type": { "key": "restaurant", "nombre": "Restaurante" }
      },
      "instance_role": { "id": 2, "nombre": "Gerente" },
      "sucursal": { "id": 1, "nombre": "Matriz" },
      "last_seen": "2024-01-21T09:45:00.000000Z",
      "is_online": true,
      "created_at": "2024-01-01T08:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 12
  },
  "links": {}
}
```

---

## Endpoint Store

### Crear Usuario

**`POST /api/users`**

Crea un nuevo usuario con rol y asignación de sucursal.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "name": "María López",
  "email": "maria@elbuensabor.com",
  "password": "SecurePass123!",
  "role": "supervisor",
  "business_instance_id": 1,
  "instance_role_id": 3,
  "sucursal_id": 1,
  "business_type_id": 1
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `name` | `string` | **Sí** | Nombre completo |
| `email` | `string` | **Sí** | Email único |
| `password` | `string` | **Sí** | Mínimo 8 caracteres (ya hasheada si viene del panel) |
| `role` | `string` | **Sí** | Rol: `user`, `admin`, `supervisor` |
| `business_instance_id` | `integer` | No | ID de instancia (existe) |
| `instance_role_id` | `integer` | No | ID de rol dentro de la instancia |
| `sucursal_id` | `integer` | No | ID de sucursal asignada |
| `business_type_id` | `integer` | No | Tipo de negocio |

**Validations:**

```
name: required|string|max:255
email: required|string|email|unique:users,email
password: required|string|min:8
role: required|in:user,admin,supervisor
business_instance_id: nullable|exists:business_instances,id
instance_role_id: nullable|exists:instance_roles,id
sucursal_id: nullable|exists:sucursales,id
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 13,
    "name": "María López",
    "email": "maria@elbuensabor.com",
    "role": "supervisor",
    "business_instance": { "id": 1, "nombre": "Restaurante El Buen Sabor" },
    "instance_role": { "id": 3, "nombre": "Cajero Senior" },
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "created_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Created successfully"
}
```

---

## Endpoint Show

### Obtener Usuario

**`GET /api/users/{user}`**

Retorna un usuario individual con todas sus relaciones.

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
    "name": "Carlos Martínez",
    "email": "carlos@elbuensabor.com",
    "email_verified_at": "2024-01-01T10:00:00.000000Z",
    "business_instance": {
      "id": 1,
      "nombre": "Restaurante El Buen Sabor",
      "business_type": { "key": "restaurant" }
    },
    "instance_role": { "id": 2, "nombre": "Gerente" },
    "sucursal": { "id": 1, "nombre": "Matriz" },
    "last_seen": "2024-01-21T09:45:00.000000Z",
    "is_online": true,
    "created_at": "2024-01-01T08:00:00.000000Z",
    "updated_at": "2024-01-20T14:30:00.000000Z"
  }
}
```

---

## Endpoint Update

### Actualizar Usuario

**`PUT /api/users/{user}`**
**`PATCH /api/users/{user}`**

Actualiza parcialmente un usuario. Todos los campos son opcionales.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "name": "Carlos Andrés Martínez",
  "role": "admin",
  "sucursal_id": 2
}
```

**Campos aceptados:** Mismos que Store (todos `sometimes`/opcionales). La contraseña se vuelve a hashear si se proporciona.

**Response `200 OK`:**

```json
{
  "data": {
    "id": 5,
    "name": "Carlos Andrés Martínez",
    "role": "admin",
    "sucursal": { "id": 2, "nombre": "Sucursal Norte" },
    "updated_at": "2024-01-21T10:00:00.000000Z"
  },
  "message": "Updated successfully"
}
```

---

## Endpoint Destroy

### Eliminar Usuario

**`DELETE /api/users/{user}`**

Elimina un usuario del sistema.

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

- El campo `last_seen` se actualiza automáticamente por el middleware `TrackLastSeen`
- `is_online` = `last_seen` es posterior a hace 5 minutos
- Los usuarios con rol `owner` o `root` tienen bypass de tenant
- El password se espera **ya hasheado** cuando viene del panel interno
- El filtro `online=null` ordena los usuarios con último visto más antiguo al final
- Cada usuario pertenece a exactamente **una** instancia de negocio (`business_instance_id`)
