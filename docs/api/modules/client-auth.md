# Client Authentication API

Customer portal authentication endpoints. **These routes are PUBLIC** — no authentication required.

## Base URL

```
/api/auth/cliente
```

---

## POST /api/auth/cliente/register

Register a new customer account. On successful registration, automatically assigns `tipo_cliente="consumo"`, `activo=true`, `acceso_api=true`, and sends a verification email.

### Request Body

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `nombre` | string | Yes | Customer full name |
| `email` | string | Yes | Must be unique |
| `telefono` | string | Yes | Must be unique |
| `password` | string | Yes | Minimum 12 characters, with confirmation |
| `password_confirmation` | string | Yes | Must match `password` |
| `tenant_id` | integer | No | Tenant — must exist |

### Example Request

```json
{
  "nombre": "Ana García",
  "email": "ana@example.com",
  "telefono": "+1-809-555-0100",
  "password": "miPasswordSeguro123",
  "password_confirmation": "miPasswordSeguro123",
  "tenant_id": 1
}
```

### Response

`201 Created`

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

## POST /api/auth/cliente/login

Authenticate a customer using phone number and password. Validates `acceso_api` flag and email verification status.

### Request Body

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `telefono` | string | Yes | Registered phone number |
| `password` | string | Yes | Account password |

### Example Request

```json
{
  "telefono": "+1-809-555-0100",
  "password": "miPasswordSeguro123"
}
```

### Response

`200 OK`

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

## POST /api/auth/cliente/forgot-password

Send a password reset link to the customer's email address.

### Request Body

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `email` | string | Yes | Must exist in database |

### Example Request

```json
{
  "email": "ana@example.com"
}
```

### Response

`200 OK`

Generates a reset token, stores its hash, and sends a `ClienteResetPassword` notification.

```json
{
  "message": "Se ha enviado un enlace de restablecimiento a su correo electrónico"
}
```

---

## POST /api/auth/cliente/reset-password

Reset the customer's password using the token received via email. Token expires after 60 minutes.

### Request Body

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `email` | string | Yes | Must exist in database |
| `token` | string | Yes | Password reset token from email |
| `password` | string | Yes | Minimum 12 characters, with confirmation |
| `password_confirmation` | string | Yes | Must match `password` |

### Example Request

```json
{
  "email": "ana@example.com",
  "token": "abc123resettoken",
  "password": "nuevoPasswordSeguro123",
  "password_confirmation": "nuevoPasswordSeguro123"
}
```

### Response

`200 OK`

Validates token age (60-minute expiry) and deletes old tokens.

```json
{
  "message": "Contraseña restablecida correctamente"
}
```

---

## POST /api/auth/cliente/resend-verification

Re-send the email verification link.

### Request Body

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `email` | string | Yes | Must exist and not yet verified |

### Example Request

```json
{
  "email": "ana@example.com"
}
```

### Response

`200 OK`

```json
{
  "message": "Se ha reenviado el correo de verificación"
}
```

---

## GET /api/auth/cliente/verify-email/{id}/{hash}

Verify the customer's email address. The hash must match SHA1 of the customer's email.

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | integer | Customer ID |
| `hash` | string | SHA1 hash of the customer's email |

### Example Request

```
GET /api/auth/cliente/verify-email/50/a94a8fe5ccb19ba61c4c0873d391e987982fbbd3
```

### Response

`200 OK`

Marks the customer's email as verified.

```json
{
  "message": "Correo electrónico verificado correctamente"
}
```

---

## Field Reference

### Cliente Model Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Primary key |
| `nombre` | string | Full name |
| `email` | string | Email address |
| `telefono` | string | Phone number |
| `tipo_cliente` | string | Customer type (auto-set to `"consumo"` on registration) |
| `activo` | boolean | Account active status (auto-set to `true` on registration) |
| `email_verified_at` | datetime|null | Email verification timestamp |
| `acceso_api` | boolean | API access flag (auto-set to `true` on registration) |
| `created_at` | datetime | Registration timestamp |
| `updated_at` | datetime | Last update timestamp |

### Authentication Flow

1. **Register** → `POST /api/auth/cliente/register` — creates account, sends verification email
2. **Verify Email** → `GET /api/auth/cliente/verify-email/{id}/{hash}` — verifies email address
3. **Login** → `POST /api/auth/cliente/login` — obtains JWT access token
4. **Forgot Password** → `POST /api/auth/cliente/forgot-password` — triggers reset email
5. **Reset Password** → `POST /api/auth/cliente/reset-password` — sets new password
6. **Resend Verification** → `POST /api/auth/cliente/resend-verification` — re-sends verification email
