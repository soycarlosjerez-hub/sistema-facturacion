# Authentication Guide

El sistema utiliza un esquema de autenticación de tres niveles que soporta operadores internos, aplicaciones SPA y clientes externos.

---

## Arquitectura de Autenticación

```
                    ┌──────────────────────────────────────────────┐
                    │           /api/* — Todas las rutas            │
                    │  Pipeline: [api-auth → tenant → logger]      │
                    └──────────────────┬───────────────────────────┘
                                       │
                    ┌──────────────────▼───────────────────────────┐
                    │  AuthenticateApiKey (api-auth)               │
                    │                                              │
                    │  ¿Token comienza con "iak_"?                 │
                    │  ├─ SÍ → InstanceApiKey → Auth::user = User  │
                    │  ├─ NO → ClientApiToken → request.user =     │
                    │  │              Cliente                       │
                    │  └─ NO MATCH → Sanctum PAT → Auth::user =    │
                    │                        User                  │
                    └──────────────────┬───────────────────────────┘
                                       │
                    ┌──────────────────▼───────────────────────────┐
                    │  TenantMiddleware (tenant)                   │
                    │  Verifica aislamiento multi-tenant           │
                    │  Clientes saltan esta verificación           │
                    └──────────────────┬───────────────────────────┘
                                       │
                    ┌──────────────────▼───────────────────────────┐
                    │  ApiRequestLogger                             │
                    │  Registra cada petición en BD                │
                    └──────────────────┬───────────────────────────┘
                                       │
                              ┌────────▼────────┐
                              │ Controller      │
                              └─────────────────┘
```

Para rutas públicas y cliente:

```
    ┌──────────────────────────────────────────────────────────┐
    │  /api/auth/*  — Sin middleware (públicas)                │
    │  /api/cliente/* — Middleware: [auth.cliente]             │
    │    Solo acepta Client API Tokens                         │
    └──────────────────────────────────────────────────────────┘
```

---

## 1. Instance API Key (`iak_*`)

Claves de API de nivel de instancia para operaciones administrativas y server-to-server.

### Características

- Prefijo obligatorio: `iak_`
- Autentica como modelo `User`
- Sujeto a aislamiento de tenant
- Hash SHA-256 almacenado en `instance_api_keys.key`
- Tracking de último uso en `last_used_at`
- Activable/desactivable con `is_active`

### Crear una Instance API Key

Las claves se generan desde el panel administrativo. El sistema:

1. Genera un string aleatorio de 64 caracteres hexadecimales
2. Prepend `iak_` al inicio
3. Almacena solo el hash SHA-256 en la base de datos
4. Muestra la clave plana UNA SOLA VEZ al momento de crearla

### Ejemplo de Uso

```bash
curl -X GET "https://api.tu-dominio.com/api/products?per_page=20" \
  -H "Authorization: Bearer iak_a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

### Flujo de Validación

```
1. Recibir Bearer token
2. ¿Comienza con "iak_"? → SÍ
3. Hash SHA-256 del token recibido
4. Buscar en instance_api_keys WHERE key = hash_sha256 AND is_active = TRUE
5. Si no encontrado → 401 "API Key inválida o desactivada."
6. Actualizar last_used_at = NOW()
7. Encontrar primer User con mismo business_instance_id
8. Si no hay usuarios → 401 "No hay usuarios activos en la instancia."
9. Auth::guard('web')->setUser(user) → continuar
```

---

## 2. Sanctum Personal Access Token

Tokens estándar de Laravel Sanctum para aplicaciones SPA y web apps.

### Características

- Sin prefijo especial
- Autentica como modelo `User`
- Almacenado en `personal_access_tokens`
- Sujeto a aislamiento de tenant
- Manejado por Laravel Sanctum nativamente

### Ejemplo de Uso

```bash
curl -X GET "https://api.tu-dominio.com/api/users/me" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIs..." \
  -H "Accept: application/json"
```

### Flujo de Validación

```
1. Recibir Bearer token
2. ¿Comienza con "iak_"? → NO
3. ¿Coincide con ClientApiToken? → NO
4. PersonalAccessToken::findToken(token)
5. Si no encontrado → 401 "Token inválido."
6. Si expirado → 401 "Token expirado."
7. Auth::guard('web')->setUser(tokenable) → continuar
```

---

## 3. Client API Token

Tokens para clientes externos (modelo `Cliente`) que acceden al portal del cliente.

### Características

- Sin prefijo especial
- Autentica como modelo `Cliente`
- Almacenado como hash SHA-256 en `client_api_tokens.token`
- NO sujeto a tenant (escopeado por `cliente_id`)
- Puede tener `expires_at` opcional
- Requiere `acceso_api = TRUE` en el registro `Cliente`
- Abilities opcionales: `["*"]` o `["invoices.read"]`

### Generación del Token

```php
// En Cliente::createToken()
$plain = bin2hex(random_bytes(32));  // 64 chars hex, 256 bits entropy
$token = $this->apiTokens()->create([
    'name'       => 'Mi Aplicación',
    'token'      => hash('sha256', $plain),  // solo hash en BD
    'abilities'  => ['*'],
    'expires_at' => null,  // opcional
]);
$token->plain_text = $plain;  // devolver UNA vez
```

### Ejemplo de Uso

```bash
# Login para obtener token
curl -X POST "https://api.tu-dominio.com/api/auth/cliente/login" \
  -H "Content-Type: application/json" \
  -d '{"telefono":"8095551234","password":"miPasswordSeguro123!"}'

# Respuesta:
{
  "cliente": { "id": 1, "nombre": "Juan Pérez", ... },
  "access_token": "a1b2c3d4e5f6...",
  "token_type": "Bearer"
}

# Usar el token
curl -X GET "https://api.tu-dominio.com/api/cliente/me" \
  -H "Authorization: Bearer a1b2c3d4e5f6..." \
  -H "Accept: application/json"
```

### Flujo de Validación

```
1. Recibir Bearer token
2. ¿Comienza con "iak_"? → NO
3. Hash SHA-256 del token
4. Buscar en client_api_tokens WHERE token = hash AND cliente.api_access = TRUE
5. Si no encontrado → intentar Sanctum
6. Si expirado (expires_at < NOW()) → 401 "Token expirado."
7. Actualizar last_used_at = NOW()
8. request.attributes['client_api_token'] = token
9. request.setUserResolver() → retorna $token->cliente
```

---

## Client Authentication Middleware (`auth.cliente`)

Middleware dedicado exclusivo para `/api/cliente/*`. Solo acepta Client API Tokens.

### Flujo

```
1. ¿Header Authorization presente? → SI
2. Hash SHA-256 del Bearer token
3. Buscar en client_api_tokens con eager-load 'cliente'
4. Si no encontrado → 401 "Token inválido."
5. Si cliente.api_access = FALSE → borrar token, 403 "Acceso API deshabilitado."
6. Si token.expired → 401 "Token expirado."
7. Actualizar last_used_at
8. request.attributes['client_api_token'] = token
9. request.setUserResolver() → retorna $token->cliente
```

---

## Tabla Comparativa

| Característica | Instance API Key | Sanctum PAT | Client Token |
|---------------|------------------|-------------|--------------|
| Prefijo | `iak_` | Ninguno | Ninguno |
| Tabla | `instance_api_keys` | `personal_access_tokens` | `client_api_tokens` |
| Modelo | `User` | `User` | `Cliente` |
| Tenant Scope | Sí | Sí | No (por `cliente_id`) |
| Expires | No | No | Opcional |
| Abilites | No | No | Sí |
| Middleware | `api-auth` | `api-auth` | `auth.cliente` |
| Rutas | `/api/*` | `/api/*` | `/api/cliente/*` |
| Uso típico | Admin, integraciones | SPA, web app | Portal cliente externo |

---

## Mensajes de Error de Autenticación

| Código | Mensaje | Causa |
|--------|---------|-------|
| 401 | `"Token no proporcionado."` | Falta header Authorization |
| 401 | `"API Key inválida o desactivada."` | Key no encontrada o `is_active = false` |
| 401 | `"Token inválido."` | Token no coincide en ninguna tabla |
| 401 | `"Token expirado."` | `expires_at` superado |
| 403 | `"Acceso API deshabilitado para este cliente."` | `cliente.acceso_api = false` |
| 401 | `"No hay usuarios activos en la instancia."` | Key válida pero sin usuarios en esa instancia |
| 401 | `"El usuario no tiene una instancia asignada."` | User sin `business_instance_id` |
| 403 | `"Instancia bloqueada: {motivo}"` | `business_instance.bloqueado = true` |

---

## Headers Requeridos

Todas las peticiones deben incluir:

```
Authorization: Bearer TU_TOKEN
Accept: application/json
Content-Type: application/json
```

### X-Tenant-ID (opcional)

En algunos casos puede ser necesario especificar explícitamente el tenant:

```
X-Tenant-ID: 1
```

---

## Rate Limiting

Los endpoints están protegidos por `ApiRequestLogger` que registra cada petición. Para producción se recomienda configurar rate limiting adicional vía Laravel's built-in throttle middleware.
