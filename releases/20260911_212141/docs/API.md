# Erpipos ERP — Documentación Completa de la API

> **Versión:** 2.0.0 | **Última actualización:** 2026-08-31

## Índice General

### API Core ERP
- [Autenticación](#autenticación)
- [Usuarios](#usuarios)
- [Categorías](#categorías)
- [Productos](#productos)
- [Ventas (POS)](#ventas-pos)
- [Clientes](#clientes)
- [Compras](#compras)
- [Backups](#backups)
- [Configuraciones](#configuraciones)

### API E-commerce
- [Carritos (Ecomm)](#carritos-ecomm)
- [Checkout (Ecomm)](#checkout-ecomm)
- [Promociones](#promociones)
- [Lealtad / Puntos](#lealtad--puntos)

### API Erpipos v3 Compatible
- [Erpipos — Productos](#flowhub--productos)
- [Erpipos — Clientes](#flowhub--clientes)
- [Erpipos — Carritos](#flowhub--carritos)
- [Erpipos — Checkout](#flowhub--checkout)
- [Erpipos — Promociones (Deals)](#flowhub--promociones-deals)
- [Erpipos — Lealtad (Rewards)](#flowhub--lealtad-rewards)

### General
- [Convenciones](#convenciones)
- [Errores](#errores)
- [Paginación](#paginación)
- [Rate Limiting](#rate-limiting)
- [Ejemplos de Código](#ejemplos-de-código)

---

## Autenticación

### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@empresa.com",
  "password": "secreto123"
}
```

**Respuesta (200):**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc6...",
  "token_type": "Bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@empresa.com",
    "role": "admin",
    "business_instance": {
      "id": 1,
      "name": "Mi Empresa",
      "business_type": "retail"
    }
  }
}
```

### Header de Autorización
Todas las peticiones requieren el token JWT:
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

---

## Usuarios

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/users/me` | Obtener usuario actual |
| `GET` | `/api/users` | Listar usuarios |
| `POST` | `/api/users` | Crear usuario |
| `PATCH` | `/api/users/{id}` | Actualizar usuario |
| `DELETE` | `/api/users/{id}` | Eliminar usuario |

### Listar usuarios
```http
GET /api/users?page=1&per_page=15&role=admin
```

### Crear usuario
```http
POST /api/users
Content-Type: application/json

{
  "name": "Juan Pérez",
  "email": "juan@empresa.com",
  "password": "Password123!",
  "role": "empleado",
  "business_instance_id": 1
}
```

---

## Categorías

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/categories` | Listar categorías |
| `POST` | `/api/categories` | Crear categoría |
| `PATCH` | `/api/categories/{category}/toggle-activa` | Toggle activo |
| `POST` | `/api/categories/reorder` | Reordenar |
| `POST` | `/api/categories/{category}/type` | Cambiar tipo |

### Crear categoría
```http
POST /api/categories
Content-Type: application/json

{
  "name": "Electrónica",
  "description": "Dispositivos electrónicos",
  "parent_id": null
}
```

---

## Productos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/products` | Listar productos |
| `POST` | `/api/products` | Crear producto |
| `GET` | `/api/products/{id}` | Ver producto |
| `PATCH` | `/api/products/{id}` | Actualizar producto |
| `DELETE` | `/api/products/{id}` | Eliminar producto |
| `GET` | `/api/products/export` | Exportar (Excel/PDF) |

### Listar productos
```http
GET /api/products?page=1&per_page=15&category_id=1&search=laptop
```

**Respuesta (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Laptop HP ProBook",
      "sku": "LAP-HP-001",
      "category": { "id": 1, "name": "Electrónica" },
      "price": 25000.00,
      "cost": 18000.00,
      "stock": 15,
      "min_stock": 5,
      "is_active": true
    }
  ],
  "current_page": 1,
  "last_page": 1,
  "per_page": 15,
  "total": 1
}
```

### Crear producto
```http
POST /api/products
Content-Type: application/json

{
  "name": "Laptop HP ProBook",
  "sku": "LAP-HP-001",
  "category_id": 1,
  "price": 25000.00,
  "cost": 18000.00,
  "stock": 15,
  "min_stock": 5
}
```

---

## Ventas (POS)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/sales` | Listar ventas |
| `POST` | `/api/sales` | Crear venta |
| `POST` | `/api/sales/{id}/cancel` | Anular venta |

### Crear venta
```http
POST /api/sales
Content-Type: application/json

{
  "cliente_id": 1,
  "ncf": "B3100000001",
  "tipo_comprobante": "ncf",
  "metodo_pago": "efectivo",
  "detalles": [
    {
      "producto_id": 1,
      "cantidad": 2,
      "precio": 25000.00,
      "descuento": 0
    }
  ],
  "propina": 1000.00
}
```

**Respuesta (201):**
```json
{
  "id": 1,
  "ncf": "B3100000001",
  "total": 59000.00,
  "mensaje": "Venta registrada correctamente"
}
```

---

## Clientes

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/customers` | Listar clientes |
| `POST` | `/api/customers` | Crear cliente |
| `PATCH` | `/api/customers/{id}` | Actualizar cliente |
| `DELETE` | `/api/customers/{id}` | Eliminar cliente |
| `GET` | `/api/customers/{id}/purchase-history` | Historial de compras |

### Crear cliente
```http
POST /api/customers
Content-Type: application/json

{
  "nombre": "Juan Pérez",
  "cedula_rnc": "001-1234567-8",
  "email": "juan@empresa.com",
  "telefono": "+1-809-555-1234",
  "direccion": "Calle Principal #123, Santo Domingo",
  "tipo": "persona"
}
```

---

## Compras

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/purchases` | Listar compras |
| `POST` | `/api/purchases` | Crear compra |
| `POST` | `/api/purchases/{id}/cancel` | Anular compra |

### Crear compra
```http
POST /api/purchases
Content-Type: application/json

{
  "proveedor_id": 1,
  "ncf": "E4100000001",
  "detalles": [
    {
      "producto_id": 1,
      "cantidad": 10,
      "precio_compra": 18000.00,
      "descuento": 0
    }
  ],
  "notas": "Compra de inventario"
}
```

---

## Backups

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `POST` | `/api/backups` | Crear backup manual |
| `GET` | `/api/backups` | Listar backups |
| `GET` | `/api/backups/{id}/download` | Descargar backup |
| `DELETE` | `/api/backups/{id}` | Eliminar backup |

### Crear backup
```http
POST /api/backups
Content-Type: application/json

{
  "compress": true,
  "filename": "backup_manual_20260831"
}
```

---

## Configuraciones

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/settings` | Obtener configuraciones |
| `POST` | `/api/settings` | Actualizar configuración |

### Obtener configuraciones
```http
GET /api/settings
```

**Respuesta (200):**
```json
{
  "nit": "1-123456-7",
  "razon_social": "Mi Empresa SRL",
  "direccion": "Calle Principal #123",
  "telefono": "+1-809-555-1234",
  "email": "info@empresa.com",
  "itbis_default": 18,
  "moneda": "RD$"
}
```

---

# API E-commerce

## Carritos (Ecomm)

**Base URL:** `/api/ecomm`

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/ecomm/carts` | Listar carritos |
| `POST` | `/api/ecomm/carts` | Crear carrito |
| `GET` | `/api/ecomm/carts/{id}` | Ver carrito |
| `PUT` | `/api/ecomm/carts/{id}` | Actualizar carrito |
| `DELETE` | `/api/ecomm/carts/{id}` | Vaciar carrito |
| `POST` | `/api/ecomm/carts/{id}/items` | Agregar item |
| `PUT` | `/api/ecomm/carts/{cartId}/items/{itemId}` | Actualizar item |
| `DELETE` | `/api/ecomm/carts/{cartId}/items/{itemId}` | Eliminar item |

### Crear carrito
```http
POST /api/ecomm/carts
Content-Type: application/json

{
  "cliente_id": 1,
  "type": "REC",
  "order_type": "pickup",
  "email": "cliente@email.com",
  "notas": "Pedido urgente",
  "items": [
    { "producto_id": 1, "cantidad": 2 },
    { "producto_id": 5, "cantidad": 1 }
  ]
}
```

**Respuesta (201):**
```json
{
  "data": {
    "id": 1,
    "tenant_id": 10,
    "cliente_id": 1,
    "estado": "active",
    "subtotal": 55000.00,
    "impuestos": 9900.00,
    "descuento": 0,
    "total": 64900.00,
    "items": [
      {
        "id": 1,
        "producto_id": 1,
        "cantidad": 2,
        "precio_unitario": 25000.00,
        "subtotal": 50000.00,
        "itbis_porcentaje": 18
      }
    ]
  },
  "message": "Cart created successfully."
}
```

### Agregar item al carrito
```http
POST /api/ecomm/carts/{id}/items
Content-Type: application/json

{
  "producto_id": 5,
  "cantidad": 3
}
```

### Actualizar item
```http
PUT /api/ecomm/carts/{cartId}/items/{itemId}
Content-Type: application/json

{
  "cantidad": 5,
  "precio_unitario": 24000.00,
  "descuento": 500,
  "sin_itbis": false
}
```

### Eliminar item
```http
DELETE /api/ecomm/carts/{cartId}/items/{itemId}
```

---

## Checkout (Ecomm)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `POST` | `/api/ecomm/carts/{cartId}/checkout` | Checkout autenticado |
| `POST` | `/api/ecomm/checkout/guest` | Checkout guest |

### Checkout autenticado
```http
POST /api/ecomm/carts/{cartId}/checkout
Content-Type: application/json

{
  "order_type": "delivery",
  "customer": {
    "id": 1
  },
  "payment_method": "tarjeta",
  "delivery_zone_id": 3,
  "notas": "Entregar después de las 5pm"
}
```

**Respuesta (200):**
```json
{
  "data": {
    "venta": {
      "id": 123,
      "ncf": "B3100000123",
      "total": 64900.00,
      "estado": "completada"
    },
    "checkout_url": null
  },
  "message": "Order created successfully."
}
```

### Checkout guest
```http
POST /api/ecomm/checkout/guest
Content-Type: application/json

{
  "cart_id": 1,
  "customer_email": "guest@email.com",
  "customer_phone": "809-555-1234",
  "customer_name": "Cliente Walk-in",
  "payment_method": "efectivo"
}
```

---

## Promociones

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/ecomm/promociones` | Listar promociones activas |
| `POST` | `/api/ecomm/promociones/validar` | Validar código de promoción |
| `POST` | `/api/ecomm/carts/{cartId}/promo` | Aplicar promoción al carrito |
| `DELETE` | `/api/ecomm/carts/{cartId}/promo` | Eliminar promoción del carrito |

### Listar promociones
```http
GET /api/ecomm/promociones
```

**Respuesta (200):**
```json
{
  "data": {
    "data": [
      {
        "id": 1,
        "codigo": "VERANO2026",
        "nombre": "Descuento de Verano",
        "tipo": "porcentaje",
        "valor": 10.00,
        "aplica_a": "todos",
        "minimo_compra": 5000.00,
        "activa": true,
        "valido_desde": "2026-06-01",
        "valido_hasta": "2026-08-31"
      }
    ]
  },
  "message": "Success"
}
```

### Validar código
```http
POST /api/ecomm/promociones/validar
Content-Type: application/json

{
  "codigo": "VERANO2026",
  "cart_id": 1,
  "subtotal": 50000.00
}
```

### Aplicar promoción
```http
POST /api/ecomm/carts/{cartId}/promo
Content-Type: application/json

{
  "codigo": "VERANO2026"
}
```

**Respuesta (200):**
```json
{
  "data": {
    "cart": { "id": 1, "subtotal": 50000.00, "descuento": 5000.00, "total": 54000.00, "items": [...] },
    "applied_promo": {
      "codigo": "VERANO2026",
      "descuento": 5000.00
    }
  },
  "message": "Promotion applied successfully."
}
```

### Eliminar promoción
```http
DELETE /api/ecomm/carts/{cartId}/promo
```

---

## Lealtad / Puntos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/ecomm/lealtad` | Consultar puntos |
| `POST` | `/api/ecomm/lealtad/canjear` | Canjear puntos |
| `GET` | `/api/ecomm/lealtad/historial` | Historial de movimientos |

### Consultar puntos
```http
GET /api/ecomm/lealtad?cliente_id=1
```

**Respuesta (200):**
```json
{
  "data": {
    "cuenta": {
      "id": 1,
      "puntos_acumulados": 5200,
      "puntos_canjeados": 300,
      "puntos_vencidos": 0,
      "puntos_disponibles": 4900,
      "nivel": "plata",
      "tasa_cambio": 1.00
    },
    "recompensas": [
      { "nombre": "5% Descuento", "costo_puntos": 100, "descuento": 0.05 },
      { "nombre": "10% Descuento", "costo_puntos": 200, "descuento": 0.10 },
      { "nombre": "15% Descuento", "costo_puntos": 300, "descuento": 0.15 },
      { "nombre": "Gratis Envio", "costo_puntos": 150, "descuento": "envio_gratis" }
    ]
  },
  "message": "Success"
}
```

### Canjear puntos
```http
POST /api/ecomm/lealtad/canjear
Content-Type: application/json

{
  "cliente_id": 1,
  "cart_id": 1,
  "recompensa_id": "200"
}
```

**Niveles de lealtad:**

| Nivel | Puntos requeridos |
|-------|-------------------|
| Bronce | 0 - 4,999 |
| Plata | 5,000 - 9,999 |
| Oro | 10,000+ |

### Historial
```http
GET /api/ecomm/lealtad/historial?cliente_id=1&limite=20
```

---

# API Erpipos v3 Compatible

> **Base URL:** `/api/erpipos/v3`
>
> **Convenciones Erpipos:**
> - Dinero en **centavos** (RD$150.00 = `15000`)
> - IDs externos en **UUID v4**
> - `internal_id` incluido para debugging

## Erpipos — Productos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/erpipos/v3/products` | Listar productos |
| `GET` | `/api/erpipos/v3/products/{id}` | Ver producto |

### Listar productos
```http
GET /api/erpipos/v3/products?search=laptop&page=1&limit=25
```

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

### Ver producto
```http
GET /api/erpipos/v3/products/{id}
```

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

## Erpipos — Clientes

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/erpipos/v3/customers` | Listar clientes |
| `POST` | `/api/erpipos/v3/customers` | Crear cliente |

### Listar clientes
```http
GET /api/erpipos/v3/customers?search=juan&page=1&limit=25
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

## Erpipos — Carritos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `POST` | `/api/erpipos/v3/carts` | Crear carrito |
| `GET` | `/api/erpipos/v3/carts/{id}` | Ver carrito |
| `POST` | `/api/erpipos/v3/carts/{id}/items` | Agregar item |
| `PUT` | `/api/erpipos/v3/carts/{cartId}/items/{itemId}` | Actualizar item |
| `DELETE` | `/api/erpipos/v3/carts/{cartId}/items/{itemId}` | Eliminar item |

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

### Agregar item
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

**Respuesta (200):**
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

---

## Erpipos — Checkout

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `POST` | `/api/erpipos/v3/checkout` | Procesar checkout |

```http
POST /api/erpipos/v3/checkout
Content-Type: application/json

{
  "cartId": "123e4567-e89b-12d3-a456-426614174000",
  "paymentMethod": "efectivo",
  "amountPaid": 8260000,
  "reference": null,
  "customerFlowId": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
  "email": "juan@email.com"
}
```

**Respuesta (201):**
```json
{
  "status": "success",
  "data": {
    "orderId": "orden-uuid-abc123",
    "internal_id": 123,
    "total": 8260000,
    "paid": 8260000,
    "status": "completed",
    "pointsEarned": 8260
  },
  "message": "Checkout completed."
}
```

**Qué hace internamente:**
1. Valida stock de productos
2. Resuelve o crea cliente walk-in
3. Crea `Venta` con totales del carrito
4. Crea `VentaDetalle` por cada item
5. Decrementa stock (solo `tipo_servicio = producto`)
6. Incrementa `ventas_count` en Producto
7. Registra `Pago`
8. Marca carrito como `completed`
9. Registra uso de promoción (si aplica)
10. Acumula puntos de lealtad (1 punto / RD$1)

---

## Erpipos — Promociones (Deals)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/erpipos/v3/deals` | Listar promociones activas |
| `POST` | `/api/erpipos/v3/carts/{cartId}/deals` | Aplicar promoción |

### Listar promociones
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

### Aplicar promoción
```http
POST /api/erpipos/v3/carts/{cartId}/deals
Content-Type: application/json

{
  "code": "VERANO2026"
}
```

**Tipos de promoción:**

| Tipo | Descripción |
|------|-------------|
| `porcentaje` | Descuento por porcentaje |
| `monto` | Descuento fijo en centavos |
| `2x1` | Compra 2 paga 1 (33.3% off) |
| `envio_gratis` | Envío gratuito |
| `regalo` | Artículo de regalo |

---

## Erpipos — Lealtad (Rewards)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/erpipos/v3/rewards` | Consultar puntos |
| `POST` | `/api/erpipos/v3/rewards/redeem` | Canjear recompensa |

### Consultar puntos
```http
GET /api/erpipos/v3/rewards?customerFlowId=a1b2c3d4-e5f6-7890-abcd-ef1234567890
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
      { "id": "100", "name": "5% Descuento", "costInPoints": 100, "description": "5% off" },
      { "id": "200", "name": "10% Descuento", "costInPoints": 200, "description": "10% off" },
      { "id": "300", "name": "15% Descuento", "costInPoints": 300, "description": "15% off" },
      { "id": "150", "name": "Envío Gratis", "costInPoints": 150, "description": "Envío gratuito" }
    ]
  },
  "message": "Success"
}
```

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

## Convenciones

| Concepto | Descripción |
|----------|-------------|
| **flowId** | UUID v4 generado por el adaptador Erpipos |
| **internal_id** | ID BIGINT interno del ERP |
| **Dinero** | Centavos en Erpipos (`RD$150.00 = 15000`), decimales en ERP Core |
| **Autenticación** | Bearer token JWT en header `Authorization` |
| **Multi-tenant** | Todos los datos están aislados por `tenant_id` (business_instance) |

---

## Errores

### Error de Autenticación (401)
```json
{
  "message": "Token no proporcionado.",
  "error": "Unauthenticated"
}
```

### Error de Validación (422)
```json
{
  "message": "La validación falló.",
  "errors": {
    "email": ["El campo email es obligatorio."],
    "password": ["El campo password debe tener al menos 8 caracteres."]
  }
}
```

### Error de No Autorizado (403)
```json
{
  "message": "No autorizado",
  "error": "Forbidden"
}
```

### Error de No Encontrado (404)
```json
{
  "message": "Recurso no encontrado",
  "error": "Not Found"
}
```

### Códigos de Error Ecommerce

| Código | HTTP | Descripción |
|--------|------|-------------|
| `invalid_code` | 400 | Código de promoción inválido |
| `expired` | 400 | Promoción expirada |
| `does_not_apply` | 400 | Carrito no cumple criterios |
| `min_purchase_not_met` | 400 | Monto mínimo no alcanzado |
| `insufficient_stock` | 400 | Stock insuficiente |
| `insufficient_points` | 400 | Puntos insuficientes |
| `invalid_reward` | 400 | Recompensa inválida |
| `missing_customer` | 400 | Información del cliente requerida |
| `Cart is empty` | 422 | Carrito vacío |

---

## Paginación

Todas las respuestas de lista usan paginación estándar:
```json
{
  "data": [...],
  "current_page": 1,
  "last_page": 10,
  "per_page": 15,
  "total": 150
}
```

| Parámetro | Tipo | Default | Descripción |
|-----------|------|---------|-------------|
| `page` | int | 1 | Número de página |
| `per_page` | int | 15 | Items por página (máx 100) |
| `search` | string | — | Término de búsqueda |
| `sort` | string | created_at | Campo de ordenamiento |
| `order` | string | desc | Dirección (asc/desc) |

---

## Rate Limiting

| Endpoint | Límite |
|----------|--------|
| API General | 60 req/min por IP |
| Autenticación | 5 req/min por IP |
| Backups | 3 req/hora por usuario |

```json
{
  "message": "Demasiadas peticiones. Intenta nuevamente en X minutos.",
  "retry_after": 60
}
```

---

## Ejemplos de Código

### curl — Flujo completo Erpipos
```bash
# Login
TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@empresa.com","password":"secret"}' | jq -r '.access_token')

# Crear carrito
CART=$(curl -s -X POST http://localhost:8000/api/erpipos/v3/carts \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"orderType":"web"}' | jq -r '.data.id')

# Agregar item
curl -X POST "http://localhost:8000/api/erpipos/v3/carts/$CART/items" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"productId":"PRODUCTO_UUID","quantity":2}'

# Aplicar promo
curl -X POST "http://localhost:8000/api/erpipos/v3/carts/$CART/deals" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"code":"VERANO2026"}'

# Checkout
curl -X POST http://localhost:8000/api/erpipos/v3/checkout \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{\"cartId\":\"$CART\",\"paymentMethod\":\"efectivo\"}"
```

### JavaScript (Fetch)
```javascript
const API = 'http://localhost:8000/api';

// Login
const login = async (email, password) => {
  const res = await fetch(`${API}/auth/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password })
  });
  const data = await res.json();
  return data.access_token;
};

// Crear carrito
const createCart = async (token) => {
  const res = await fetch(`${API}/flowhub/v3/carts`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ orderType: 'web' })
  });
  return await res.json();
};

// Agregar item
const addItem = async (token, cartId, productId, quantity) => {
  const res = await fetch(`${API}/flowhub/v3/carts/${cartId}/items`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ productId, quantity })
  });
  return await res.json();
};

// Checkout
const checkout = async (token, cartId, paymentMethod) => {
  const res = await fetch(`${API}/flowhub/v3/checkout`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ cartId, paymentMethod })
  });
  return await res.json();
};
```

### Python (Requests)
```python
import requests

API = 'http://localhost:8000/api'

# Login
def login(email, password):
    r = requests.post(f'{API}/auth/login', json={
        'email': email, 'password': password
    })
    return r.json()['access_token']

# Crear carrito
def create_cart(token):
    r = requests.post(f'{API}/flowhub/v3/carts',
        headers={'Authorization': f'Bearer {token}'},
        json={'orderType': 'web'}
    )
    return r.json()['data']['id']

# Agregar item
def add_item(token, cart_id, product_id, quantity):
    r = requests.post(f'{API}/flowhub/v3/carts/{cart_id}/items',
        headers={'Authorization': f'Bearer {token}'},
        json={'productId': product_id, 'quantity': quantity}
    )
    return r.json()

# Checkout
def checkout(token, cart_id, payment_method):
    r = requests.post(f'{API}/flowhub/v3/checkout',
        headers={'Authorization': f'Bearer {token}'},
        json={'cartId': cart_id, 'paymentMethod': payment_method}
    )
    return r.json()
```

---

## Resumen de Endpoints

### API Core ERP (25 endpoints)
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `POST` | `/api/auth/login` | Login |
| `POST` | `/api/auth/register` | Registro |
| `GET` | `/api/users/me` | Usuario actual |
| `GET/POST` | `/api/users` | Listar/Crear usuarios |
| `PATCH/DELETE` | `/api/users/{id}` | Actualizar/Eliminar usuario |
| `GET/POST` | `/api/categories` | Listar/Crear categorías |
| `GET/POST/PATCH/DELETE` | `/api/products` | CRUD productos |
| `GET/POST` | `/api/sales` | Listar/Crear ventas |
| `POST` | `/api/sales/{id}/cancel` | Anular venta |
| `GET/POST/PATCH/DELETE` | `/api/customers` | CRUD clientes |
| `GET/POST` | `/api/purchases` | Listar/Crear compras |
| `POST` | `/api/purchases/{id}/cancel` | Anular compra |
| `POST/GET/DELETE` | `/api/backups` | CRUD backups |
| `GET/POST` | `/api/settings` | Configuraciones |

### API E-commerce (14 endpoints)
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET/POST` | `/api/ecomm/carts` | Listar/Crear carritos |
| `GET/PUT/DELETE` | `/api/ecomm/carts/{id}` | Ver/Actualizar/Vaciar carrito |
| `POST/PUT/DELETE` | `/api/ecomm/carts/{id}/items` | CRUD items |
| `POST` | `/api/ecomm/carts/{cartId}/checkout` | Checkout |
| `POST` | `/api/ecomm/checkout/guest` | Checkout guest |
| `GET/POST/DELETE` | `/api/ecomm/promociones` | CRUD promociones |
| `GET/POST` | `/api/ecomm/lealtad` | Lealtad/Puntos |

### API Erpipos v3 (14 endpoints)
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/erpipos/v3/products` | Listar productos |
| `GET` | `/api/erpipos/v3/products/{id}` | Ver producto |
| `GET/POST` | `/api/erpipos/v3/customers` | Listar/Crear clientes |
| `POST` | `/api/erpipos/v3/carts` | Crear carrito |
| `GET` | `/api/erpipos/v3/carts/{id}` | Ver carrito |
| `POST/PUT/DELETE` | `/api/erpipos/v3/carts/{id}/items` | CRUD items |
| `POST` | `/api/erpipos/v3/checkout` | Checkout |
| `GET` | `/api/erpipos/v3/deals` | Listar promociones |
| `POST` | `/api/erpipos/v3/carts/{cartId}/deals` | Aplicar promoción |
| `GET` | `/api/erpipos/v3/rewards` | Consultar puntos |
| `POST` | `/api/erpipos/v3/rewards/redeem` | Canjear recompensa |

**Total: 53 endpoints documentados**

---

## Changelog

### v2.0.0 (2026-08-31)
- ✅ API E-commerce: Carritos, Checkout, Promociones, Lealtad
- ✅ API Erpipos v3: 14 endpoints compatibles
- ✅ Servicios: CartService, PromocionService, LealtadService
- ✅ Modelo: ErpiposIdMap para mapeo UUID ↔ BIGINT
- ✅ Formulario Producto: Secciones dinámicas (Tecnología, Clima, Arte)
- ✅ Documentación completa actualizada

### v1.0.0 (2024-01-15)
- ✅ Autenticación JWT
- ✅ CRUD de categorías, productos, clientes
- ✅ CRUD de ventas (POS) y compras
- ✅ Gestión de backups
- ✅ Configuraciones del tenant
- ✅ Paginación estándar
- ✅ Rate limiting
- ✅ Soporte multi-tenant
