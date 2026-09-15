# Erpipos v3 Compatible API — Documentación

## Índice
- [Autenticación](#autenticación)
- [Productos](#productos)
- [Clientes](#clientes)
- [Carritos](#carritos)
- [Checkout](#checkout)
- [Promociones (Deals)](#promociones-deals)
- [Lealtad (Rewards)](#lealtad-rewards)
- [Códigos de Error](#códigos-de-error)

---

## Autenticación

Todas las rutas Erpipos requieren un token de autenticación vía header `Authorization`.

```http
Authorization: Bearer {token}
```

El token se obtiene del login principal del ERP:
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@empresa.com",
  "password": "tu_password"
}
```

---

## Base URL

```
/api/erpipos/v3
```

---

## Convenciones

| Concepto | Descripción |
|----------|-------------|
| **flowId** | UUID v4 generado por el adaptador. Se usa como ID externo (Erpipos convention) |
| **internal_id** | ID BIGINT interno del ERP. Se incluye para debugging |
| **Dinero** | Todos los montos en **centavos** (ej: RD$150.00 = `15000`) |
| **IDs** | UUIDs de Erpipos en responses, BIGINT en queries internas |

---

## Productos

### Listar productos

```http
GET /api/erpipos/v3/products
```

**Parámetros query:**

| Parámetro | Tipo | Default | Descripción |
|-----------|------|---------|-------------|
| `search` | string | — | Búsqueda por nombre, SKU, marca o modelo |
| `page` | int | 1 | Página |
| `limit` | int | 25 | Items por página (máx 100) |

**Respuesta (200):**

```json
{
  "status": "success",
  "data": {
    "products": [
      {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "internal_id": 42,
        "name": "Laptop HP Pavilion",
        "description": "Laptop 15 pulgadas, 8GB RAM",
        "price": 3500000,
        "qtyOnHand": 15,
        "sku": "2001234567890",
        "brand": "HP",
        "model": "Pavilion 15",
        "imageUrl": "https://erp.example.com/storage/productos/laptop-hp.webp",
        "taxRate": 18,
        "unitOfMeasure": "Unidad"
      }
    ],
    "pagination": {
      "total": 150,
      "page": 1,
      "limit": 25,
      "totalPages": 6
    }
  },
  "message": "Success"
}
```

### Obtener producto por ID

```http
GET /api/erpipos/v3/products/{id}
```

`{id}` puede ser un `flowId` (UUID) o un `internal_id` (BIGINT).

**Respuesta (200):**

```json
{
  "status": "success",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "internal_id": 42,
    "name": "Laptop HP Pavilion",
    "description": "Laptop 15 pulgadas, 8GB RAM",
    "price": 3500000,
    "costPrice": 2500000,
    "qtyOnHand": 15,
    "qtyMinimum": 5,
    "sku": "2001234567890",
    "brand": "HP",
    "model": "Pavilion 15",
    "imageUrl": "https://erp.example.com/storage/productos/laptop-hp.webp",
    "taxRate": 18,
    "unitOfMeasure": "Unidad",
    "active": true
  },
  "message": "Success"
}
```

---

## Clientes

### Listar clientes

```http
GET /api/erpipos/v3/customers
```

**Parámetros query:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `search` | string | Búsqueda por nombre, email, teléfono o cédula |
| `page` | int | Página |
| `limit` | int | Items por página (máx 100) |

**Respuesta (200):**

```json
{
  "status": "success",
  "data": {
    "customers": [
      {
        "id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
        "internal_id": 15,
        "firstName": "Juan",
        "lastName": "Pérez",
        "email": "juan@email.com",
        "phone": "809-555-1234",
        "identification": "001-1234567-8"
      }
    ],
    "pagination": {
      "total": 50,
      "page": 1,
      "limit": 25,
      "totalPages": 2
    }
  },
  "message": "Success"
}
```

### Crear cliente

```http
POST /api/erpipos/v3/customers
Content-Type: application/json

{
  "firstName": "María",
  "lastName": "García",
  "email": "maria@email.com",
  "phone": "809-555-9876",
  "identification": "001-8765432-1"
}
```

**Respuesta (201):**

```json
{
  "status": "success",
  "data": {
    "id": "f8e7d6c5-b4a3-2109-fedc-ba9876543210",
    "internal_id": 16,
    "firstName": "María",
    "lastName": "García",
    "email": "maria@email.com",
    "phone": "809-555-9876"
  },
  "message": "Customer created."
}
```

---

## Carritos

### Crear carrito

```http
POST /api/erpipos/v3/carts
Content-Type: application/json

{
  "customerFlowId": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
  "sessionId": "session_abc123",
  "orderType": "web"
}
```

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `customerFlowId` | string | No | UUID del cliente |
| `sessionId` | string | No | ID de sesión del navegador |
| `orderType` | string | No | `pos`, `web`, `phone` |

**Respuesta (201):**

```json
{
  "status": "success",
  "data": {
    "id": "123e4567-e89b-12d3-a456-426614174000",
    "internal_id": 1,
    "status": "active"
  },
  "message": "Cart created."
}
```

### Ver carrito

```http
GET /api/erpipos/v3/carts/{id}
```

**Respuesta (200):**

```json
{
  "status": "success",
  "data": {
    "id": "123e4567-e89b-12d3-a456-426614174000",
    "internal_id": 1,
    "status": "active",
    "subtotal": 3500000,
    "tax": 630000,
    "discount": 0,
    "total": 4130000,
    "items": [
      {
        "id": "item-uuid-1",
        "internal_id": 1,
        "productId": "550e8400-e29b-41d4-a716-446655440000",
        "productName": "Laptop HP Pavilion",
        "quantity": 1,
        "unitPrice": 3500000,
        "subtotal": 3500000,
        "taxRate": 18,
        "notes": null
      }
    ]
  },
  "message": "Success"
}
```

### Agregar item al carrito

```http
POST /api/erpipos/v3/carts/{id}/items
Content-Type: application/json

{
  "productId": "550e8400-e29b-41d4-a716-446655440000",
  "quantity": 2,
  "priceOverride": null,
  "notes": "Color negro"
}
```

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `productId` | string | Sí | UUID del producto |
| `quantity` | int | Sí | Cantidad |
| `priceOverride` | int | No | Precio en centavos (sobreescribe el precio del catálogo) |
| `notes` | string | No | Notas del item |

**Respuesta (201):**

```json
{
  "status": "success",
  "data": {
    "id": "123e4567-e89b-12d3-a456-426614174000",
    "internal_id": 1,
    "status": "active",
    "subtotal": 7000000,
    "tax": 1260000,
    "discount": 0,
    "total": 8260000,
    "items": [
      {
        "id": "item-uuid-1",
        "internal_id": 1,
        "productId": "550e8400-e29b-41d4-a716-446655440000",
        "productName": "Laptop HP Pavilion",
        "quantity": 2,
        "unitPrice": 3500000,
        "subtotal": 7000000,
        "taxRate": 18,
        "notes": "Color negro"
      }
    ]
  },
  "message": "Item added."
}
```

### Actualizar cantidad de item

```http
PUT /api/erpipos/v3/carts/{cartId}/items/{itemId}
Content-Type: application/json

{
  "quantity": 3
}
```

### Eliminar item del carrito

```http
DELETE /api/erpipos/v3/carts/{cartId}/items/{itemId}
```

---

## Checkout

### Procesar checkout

```http
POST /api/erpipos/v3/checkout
Content-Type: application/json

{
  "cartId": "123e4567-e89b-12d3-a456-426614174000",
  "paymentMethod": "efectivo",
  "amountPaid": 4130000,
  "reference": null,
  "customerFlowId": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
  "email": "juan@email.com"
}
```

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `cartId` | string | Sí | UUID del carrito |
| `paymentMethod` | string | Sí | `efectivo`, `tarjeta`, `transferencia`, `mixto`, `fiado` |
| `amountPaid` | int | No | Monto pagado en centavos (default: total del carrito) |
| `reference` | string | No | Referencia de pago (tarjeta, transferencia) |
| `customerFlowId` | string | No | UUID del cliente |
| `email` | string | No | Email para crear cliente walk-in |

**Respuesta (201):**

```json
{
  "status": "success",
  "data": {
    "orderId": "orden-uuid-abc123",
    "internal_id": 123,
    "total": 4130000,
    "paid": 4130000,
    "status": "completed",
    "pointsEarned": 4130
  },
  "message": "Checkout completed."
}
```

**Qué hace internamente:**
1. Valida stock de productos
2. Resuelve o crea cliente walk-in
3. Crea `Venta` con totales del carrito
4. Crea `VentaDetalle` por cada item del carrito
5. Decrementa stock (solo para `tipo_servicio = producto`)
6. Incrementa `ventas_count` en Producto
7. Registra `Pago`
8. Marca carrito como `completed`
9. Registra uso de promoción (si aplica)
10. Acumula puntos de lealtad (1 punto por cada RD$1)

---

## Promociones (Deals)

### Listar promociones activas

```http
GET /api/erpipos/v3/deals
```

**Respuesta (200):**

```json
{
  "status": "success",
  "data": {
    "deals": [
      {
        "id": "promo-uuid-1",
        "internal_id": 1,
        "code": "VERANO2026",
        "name": "Descuento de Verano",
        "description": "10% off en todo el catálogo",
        "type": "porcentaje",
        "value": 10,
        "minPurchase": 500000,
        "validFrom": "2026-06-01",
        "validUntil": "2026-08-31",
        "active": true
      }
    ]
  },
  "message": "Success"
}
```

### Aplicar promoción a carrito

```http
POST /api/erpipos/v3/carts/{cartId}/deals
Content-Type: application/json

{
  "code": "VERANO2026"
}
```

**Respuesta (200):**

```json
{
  "status": "success",
  "data": {
    "deal": {
      "code": "VERANO2026",
      "discount": 700000
    },
    "cart": {
      "id": "123e4567-e89b-12d3-a456-426614174000",
      "internal_id": 1,
      "status": "active",
      "subtotal": 7000000,
      "tax": 1260000,
      "discount": 700000,
      "total": 7560000,
      "items": [...]
    }
  },
  "message": "Deal applied."
}
```

**Tipos de promoción soportados:**

| Tipo | Descripción |
|------|-------------|
| `porcentaje` | Descuento por porcentaje sobre el subtotal |
| `monto` | Descuento fijo en centavos |
| `2x1` | Compra 2 paga 1 (descuenta 33.3%) |
| `envio_gratis` | Envío gratuito (sin descuento monetario) |
| `regalo` | Artículo de regalo |

---

## Lealtad (Rewards)

### Consultar puntos y recompensas

```http
GET /api/erpipos/v3/rewards?customerFlowId={uuid}
```

**Respuesta (200):**

```json
{
  "status": "success",
  "data": {
    "points": 5200,
    "tier": "plata",
    "totalEarned": 8500,
    "totalRedeemed": 3300,
    "availableRewards": [
      {
        "id": "100",
        "name": "5% Descuento",
        "costInPoints": 100,
        "description": "5% off en tu compra"
      },
      {
        "id": "200",
        "name": "10% Descuento",
        "costInPoints": 200,
        "description": "10% off en tu compra"
      },
      {
        "id": "300",
        "name": "15% Descuento",
        "costInPoints": 300,
        "description": "15% off en tu compra"
      },
      {
        "id": "150",
        "name": "Envío Gratis",
        "costInPoints": 150,
        "description": "Envío gratuito"
      }
    ]
  },
  "message": "Success"
}
```

**Niveles de lealtad:**

| Nivel | Puntos requeridos |
|-------|-------------------|
| Bronce | 0 - 4,999 |
| Plata | 5,000 - 9,999 |
| Oro | 10,000+ |

### Canjear recompensa

```http
POST /api/erpipos/v3/rewards/redeem
Content-Type: application/json

{
  "customerFlowId": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
  "rewardId": "200",
  "cartId": "123e4567-e89b-12d3-a456-426614174000"
}
```

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `customerFlowId` | string | Sí | UUID del cliente |
| `rewardId` | string | Sí | ID de la recompensa (`100`, `200`, `300`, `150`) |
| `cartId` | string | No | UUID del carrito (para aplicar descuento) |

**Respuesta (200):**

```json
{
  "status": "success",
  "data": {
    "pointsRemaining": 5000,
    "tier": "plata",
    "rewardRedeemed": "10% Descuento"
  },
  "message": "Reward redeemed."
}
```

---

## Códigos de Error

| Código | HTTP Status | Descripción |
|--------|-------------|-------------|
| `invalid_code` | 400 | Código de promoción inválido o expirado |
| `expired` | 400 | Promoción expirada |
| `does_not_apply` | 400 | Carrito no cumple criterios de la promoción |
| `min_purchase_not_met` | 400 | No alcanza el monto mínimo de compra |
| `insufficient_stock` | 400 | Stock insuficiente |
| `insufficient_points` | 400 | Puntos insuficientes para canjear |
| `invalid_reward` | 400 | Recompensa inválida |
| `missing_customer` | 400 | Se requiere información del cliente |
| `Cart is empty` | 422 | Carrito vacío al hacer checkout |

---

## Ejemplos de Uso

### Flujo completo: Crear carrito → Agregar items → Aplicar promo → Checkout

```bash
# 1. Crear carrito
curl -X POST http://localhost:8000/api/erpipos/v3/carts \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"customerFlowId": "uuid-cliente", "orderType": "web"}'

# Respuesta: {"data": {"id": "cart-uuid", ...}}

# 2. Agregar items
curl -X POST http://localhost:8000/api/erpipos/v3/carts/{cart-uuid}/items \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"productId": "producto-uuid", "quantity": 2}'

# 3. Aplicar promoción
curl -X POST http://localhost:8000/api/erpipos/v3/carts/{cart-uuid}/deals \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"code": "VERANO2026"}'

# 4. Checkout
curl -X POST http://localhost:8000/api/erpipos/v3/checkout \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "cartId": "cart-uuid",
    "paymentMethod": "tarjeta",
    "amountPaid": 7560000,
    "reference": "TXN-12345"
  }'
```

### Verificar puntos de lealtad

```bash
curl "http://localhost:8000/api/erpipos/v3/rewards?customerFlowId=uuid-cliente" \
  -H "Authorization: Bearer {token}"
```

### Canjear puntos por descuento

```bash
curl -X POST http://localhost:8000/api/erpipos/v3/rewards/redeem \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "customerFlowId": "uuid-cliente",
    "rewardId": "200",
    "cartId": "cart-uuid"
  }'
```

---

## Endpoints Resumen

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/erpipos/v3/products` | Listar productos |
| `GET` | `/api/erpipos/v3/products/{id}` | Ver producto |
| `GET` | `/api/erpipos/v3/customers` | Listar clientes |
| `POST` | `/api/erpipos/v3/customers` | Crear cliente |
| `POST` | `/api/erpipos/v3/carts` | Crear carrito |
| `GET` | `/api/erpipos/v3/carts/{id}` | Ver carrito |
| `POST` | `/api/erpipos/v3/carts/{id}/items` | Agregar item |
| `PUT` | `/api/erpipos/v3/carts/{cartId}/items/{itemId}` | Actualizar item |
| `DELETE` | `/api/erpipos/v3/carts/{cartId}/items/{itemId}` | Eliminar item |
| `POST` | `/api/erpipos/v3/checkout` | Procesar checkout |
| `GET` | `/api/erpipos/v3/deals` | Listar promociones |
| `POST` | `/api/erpipos/v3/carts/{cartId}/deals` | Aplicar promoción |
| `GET` | `/api/erpipos/v3/rewards` | Consultar puntos |
| `POST` | `/api/erpipos/v3/rewards/redeem` | Canjear recompensa |

**Total: 14 endpoints**
