# Client Authentication API

Customer portal authentication endpoints. **These routes are PUBLIC** — no authentication required.

## Base URL

```
/api/auth/cliente
```

---

## Endpoint Register

### Registrar Cliente

**`POST /api/auth/cliente/register`**

Registra un nuevo cliente. Asigna automáticamente `tipo_cliente="consumo"`, `activo=true`, `acceso_api=true` y envía email de verificación.

El `tenant_id` se resuelve automáticamente en este orden si no se envía:
1. Del `User` autenticado por API Key (`iak_`) o Sanctum token (`business_instance_id`)
2. Del `Cliente` autenticado por `client_api_token` (`tenant_id`)
3. Primera instancia disponible en la BD

**Headers:**

```
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "nombre": "Ana García",
  "email": "ana@example.com",
  "telefono": "+1-809-555-0100",
  "password": "miPasswordSeguro123",
  "password_confirmation": "miPasswordSeguro123"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | **Sí** | Nombre completo del cliente |
| `email` | `string` | **Sí** | Email único |
| `telefono` | `string` | **Sí** | Teléfono único |
| `password` | `string` | **Sí** | Mínimo 12 caracteres |
| `password_confirmation` | `string` | **Sí** | Debe coincidir con `password` |
| `tenant_id` | `integer` | No | ID del tenant. Si no se envía, se resuelve del token de autenticación |

**Validations:**

```
nombre: required|string|max:255
email: required|string|email|unique:clientes,email
telefono: required|string|unique:clientes,telefono
password: required|string|min:12
password_confirmation: required|string|same:password
tenant_id: nullable|exists:business_instances,id
```

**Response `201 Created`:**

```json
{
  "data": {
    "cliente": {
      "id": 50,
      "nombre": "Ana García",
      "email": "ana@example.com",
      "telefono": "+1-809-555-0100",
      "tipo_cliente": "consumo",
      "activo": true,
      "email_verified_at": null,
      "acceso_api": true,
      "created_at": "2026-07-20T14:00:00.000000Z",
      "updated_at": "2026-07-20T14:00:00.000000Z"
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGci...",
    "token_type": "Bearer"
  }
}
```

---

## Endpoint Login

### Autenticar Cliente

**`POST /api/auth/cliente/login`**

Autentica un cliente usando email y contraseña. Valida `acceso_api` y estado de verificación de email.

**Headers:**

```
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "email": "ana@example.com",
  "password": "miPasswordSeguro123"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `email` | `string` | **Sí** | Email registrado |
| `password` | `string` | **Sí** | Contraseña de la cuenta |

**Response `200 OK`:**

```json
{
  "data": {
    "cliente": {
      "id": 50,
      "nombre": "Ana García",
      "email": "ana@example.com",
      "telefono": "+1-809-555-0100",
      "tipo_cliente": "consumo",
      "activo": true,
      "email_verified_at": "2026-07-20T14:05:00.000000Z",
      "acceso_api": true,
      "created_at": "2026-07-20T14:00:00.000000Z",
      "updated_at": "2026-07-20T14:00:00.000000Z"
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGci...",
    "token_type": "Bearer"
  }
}
```

---

## Endpoint Forgot Password

### Olvidé Contraseña

**`POST /api/auth/cliente/forgot-password`**

Envía un enlace de restablecimiento al email del cliente.

**Headers:**

```
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "email": "ana@example.com"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `email` | `string` | **Sí** | Email registrado |

**Response `200 OK`:**

```json
{
  "message": "Se ha enviado un enlace de restablecimiento a su correo electrónico"
}
```

---

## Endpoint Reset Password

### Restablecer Contraseña

**`POST /api/auth/cliente/reset-password`**

Restablece la contraseña usando el token recibido por email. Token expira después de 60 minutos.

**Headers:**

```
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "email": "ana@example.com",
  "token": "abc123resettoken",
  "password": "nuevoPasswordSeguro123",
  "password_confirmation": "nuevoPasswordSeguro123"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `email` | `string` | **Sí** | Email registrado |
| `token` | `string` | **Sí** | Token de restablecimiento |
| `password` | `string` | **Sí** | Mínimo 12 caracteres |
| `password_confirmation` | `string` | **Sí** | Debe coincidir |

**Response `200 OK`:**

```json
{
  "message": "Contraseña restablecida correctamente"
}
```

---

## Endpoint Resend Verification

### Reenviar Verificación

**`POST /api/auth/cliente/resend-verification`**

Reenvía el enlace de verificación por email.

**Headers:**

```
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "email": "ana@example.com"
}
```

**Response `200 OK`:**

```json
{
  "message": "Se ha reenviado el correo de verificación"
}
```

---

## Endpoint Verify Email

### Verificar Email

**`GET /api/auth/cliente/verify-email/{id}/{hash}`**

Verifica el email del cliente. El hash debe coincir con SHA1 del email.

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `id` | `integer` | ID del cliente |
| `hash` | `string` | Hash SHA1 del email |

**Example Request:**

```
GET /api/auth/cliente/verify-email/50/a94a8fe5ccb19ba61c4c0873d391e987982fbbd3
```

**Response `200 OK`:**

```json
{
  "message": "Correo electrónico verificado correctamente"
}
```

---

## Field Reference

### Cliente Model Fields

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | `integer` | Primary key |
| `nombre` | `string` | Nombre completo |
| `email` | `string` | Email |
| `telefono` | `string` | Teléfono |
| `tipo_cliente` | `string` | Tipo de cliente (auto-set a `"consumo"`) |
| `activo` | `boolean` | Estado activo (auto-set a `true`) |
| `email_verified_at` | `datetime\|null` | Timestamp de verificación |
| `acceso_api` | `boolean` | Flag acceso API (auto-set a `true`) |
| `created_at` | `datetime` | Registro |
| `updated_at` | `datetime` | Última actualización |

### Flujo de Autenticación

1. **Registrar** → `POST /api/auth/cliente/register` — crea cuenta, envía email de verificación
2. **Verificar Email** → `GET /api/auth/cliente/verify-email/{id}/{hash}` — verifica email
3. **Login** → `POST /api/auth/cliente/login` — obtiene JWT access token
4. **Olvidé Contraseña** → `POST /api/auth/cliente/forgot-password` — dispara email de reset
5. **Restablecer Contraseña** → `POST /api/auth/cliente/reset-password` — establece nueva contraseña
6. **Reenviar Verificación** → `POST /api/auth/cliente/resend-verification` — reenvía email de verificación
