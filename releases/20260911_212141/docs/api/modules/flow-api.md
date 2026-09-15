# FlowApi — Documentación Completa

API de e-commerce compatible con FlowHub para tiendas en línea, carritos, checkout, promociones, lealtad, POS y gestión de inventario. Autenticación via Instance API Key (`iak_*`) — cada instancia solo accede a sus propios datos.

---

## Cómo Funciona la API (Guía Paso a Paso)

### Paso 1: Obtener tu API Key

Cada instancia de negocio tiene una API Key única que comienza con `iak_*`. La encuentras en **Configuración > API** del panel de administración.

```bash
# Tu API Key se ve así:
iak_abc123def456ghi789jkl012mno345pq
```

**Nunca** compartas tu API Key públicamente. Cada API Key está vinculada a una sola instancia y solo puede acceder a los datos de esa instancia (aislamiento multi-tenant).

### Paso 2: Autenticar las Peticiones

Incluye la API Key en el header `Authorization` de cada petición HTTP:

```bash
curl -X GET "https://tudominio.com/api/ecomm/carts" \
  -H "Authorization: Bearer iak_abc123def456ghi789jkl012mno345pq" \
  -H "Content-Type: application/json"
```

Si la API Key es inválida o está ausente, recibirás un error `401 Unauthorized`.

### Paso 3: Entender la Respuesta

Todas las respuestas siguen un formato estándar con `data`, `errors` y `message`:

```json
{
    "data": { ... },
    "errors": [],
    "message": "Success"
}
```

- **`data`**: Contiene el resultado de la operación (objeto o array).
- **`errors`**: Array vacío si la operación fue exitosa, o lista de errores con código y descripción.
- **`message`**: Mensaje descriptivo del resultado.

### Paso 4: Multi-Tenant (Aislamiento de Datos)

Cada API Key pertenece a una instancia de negocio. No puedes acceder a datos de otra instancia. Las peticiones se filtran automáticamente por `business_instance_id`. Si intentas acceder a un recurso que no pertenece a tu instancia, obtendrás `404 Not Found`.

### Paso 5: Flujo Completo de Venta

El flujo típico de una venta e-commerce es:

```
1. Crear carrito           → POST /api/ecomm/carts
2. Agregar productos       → POST /api/ecomm/carts/{id}/items
3. Aplicar promoción       → POST /api/ecomm/carts/{id}/promo (opcional)
4. Canjear puntos lealtad  → POST /api/ecomm/lealtad/canjear (opcional)
5. Procesar checkout       → POST /api/ecomm/carts/{cartId}/checkout
```

El checkout crea la venta, descuenta el stock, genera el NCF, registra el pago y acumula puntos de lealtad automáticamente.

### Formato de Moneda

| API | Formato | Ejemplo |
|-----|---------|---------|
| Ecomm / Tienda / POS | Decimal (RD$) | `"250.00"` |
| Erpipos v3 | Centavos (FlowHub) | `25000` = RD$250.00 |

---

## Endpoint Listar Carritos Ecomm

Lista los carritos de la instancia actual.

**`GET /api/ecomm/carts`**

### Autenticación

```
Authorization: Bearer iak_{tu_api_key}
```

### Parámetros Query

| Campo | Tipo | Descripción |
|-------|------|-------------|
| cliente_id | integer | Filtrar por cliente |
| estado | string | Filtrar por estado: active, checked-out, completed |
| session_id | string | Filtrar por sesión |

### Response `200 OK`:

```json
{
    "data": {
        "data": [
            {
                "id": 1,
                "tenant_id": 10,
                "cliente_id": 5,
                "estado": "active",
                "subtotal": "250.00",
                "impuestos": "37.50",
                "descuento": "0.00",
                "total": "287.50",
                "items": []
            }
        ],
        "current_page": 1,
        "last_page": 1,
        "per_page": 20,
        "total": 1
    },
    "errors": [],
    "message": "Success"
}
```

---

## Endpoint Crear Carrito Ecomm

Crea un carrito nuevo con items iniciales opcionales.

**`POST /api/ecomm/carts`**

### Request Body:

```json
{
    "cliente_id": 5,
    "type": "REC",
    "order_type": "pickup",
    "email": "cliente@email.com",
    "notas": "Pedido especial",
    "items": [
        {
            "producto_id": 12,
            "cantidad": 2
        }
    ]
}
```

### Campos:

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| cliente_id | integer | No | ID del cliente |
| type | string | No | Tipo de carrito: REC, MED |
| order_type | string | No | Tipo de orden: pickup, delivery |
| email | string | No | Email del cliente |
| notas | string | No | Notas del carrito (max 500) |
| items | array | No | Items iniciales |
| items.*.producto_id | integer | Sí | ID del producto |
| items.*.cantidad | integer | Sí | Cantidad (min 1) |

### Response `201 Created`:

```json
{
    "data": {
        "id": 2,
        "estado": "active",
        "subtotal": "500.00",
        "impuestos": "75.00",
        "total": "575.00",
        "items": [...]
    },
    "errors": [],
    "message": "Cart created successfully."
}
```

---

## Endpoint Ver Carrito Ecomm

**`GET /api/ecomm/carts/{id}`**

### Response `200 OK`:

```json
{
    "data": {
        "id": 1,
        "estado": "active",
        "subtotal": "250.00",
        "impuestos": "37.50",
        "descuento": "0.00",
        "total": "287.50",
        "items": [
            {
                "id": 1,
                "producto_id": 12,
                "cantidad": 2,
                "precio_unitario": "125.00",
                "subtotal": "250.00"
            }
        ]
    },
    "errors": [],
    "message": "Success"
}
```

---

## Endpoint Actualizar Carrito Ecomm

**`PUT /api/ecomm/carts/{id}`**

### Request Body:

```json
{
    "order_type": "delivery",
    "notas": "Agregar cubiertos"
}
```

### Campos:

| Campo | Tipo | Descripción |
|-------|------|-------------|
| type | string | Tipo: REC, MED |
| order_type | string | pickup, delivery |
| email | string | Email |
| notas | string | Notas (max 500) |
| estado | string | active, checked-out |

### Response `200 OK`

---

## Endpoint Agregar Item al Carrito

**`POST /api/ecomm/carts/{id}/items`**

### Request Body:

```json
{
    "producto_id": 15,
    "cantidad": 3
}
```

### Campos:

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| producto_id | integer | Sí | ID del producto |
| cantidad | integer | Sí | Cantidad (min 1) |

### Validaciones:
- Stock insuficiente retorna 400 con código `insufficient_stock`
- Producto debe pertenecer a la instancia

### Response `200 OK`:

Carrito actualizado con el item nuevo.

---

## Endpoint Actualizar Item del Carrito

**`PUT /api/ecomm/carts/{cartId}/items/{itemId}`**

### Request Body:

```json
{
    "cantidad": 5,
    "precio_unitario": "120.00",
    "descuento": "10.00",
    "sin_itbis": false
}
```

### Response `200 OK`

---

## Endpoint Eliminar Item del Carrito

**`DELETE /api/ecomm/carts/{cartId}/items/{itemId}`**

### Response `200 OK`

---

## Endpoint Limpiar Carrito

**`DELETE /api/ecomm/carts/{id}`**

Elimina todos los items del carrito y pone totales en 0.

### Response `200 OK`

---

## Endpoint Checkout Ecomm (Autenticado)

Procesa el checkout del carrito, crea la venta y registra el pago.

**`POST /api/ecomm/carts/{cartId}/checkout`**

### Request Body:

```json
{
    "customer": {
        "id": 5,
        "email": "cliente@email.com",
        "phone": "8095551234",
        "name": "Juan Perez"
    },
    "order_type": "delivery",
    "delivery_zone_id": 3,
    "delivery_company_id": 1,
    "payment_method": "efectivo",
    "notas": "Entregar antes de las 5pm",
    "redirect_url": "https://tienda.com/gracias"
}
```

### Campos:

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| cart_id | integer | Sí | ID del carrito |
| order_type | string | No | pickup, delivery |
| customer | object | No | Datos del cliente |
| customer.id | integer | No | ID de cliente existente |
| customer.email | string | No | Email para crear/buscar cliente |
| customer.phone | string | No | Teléfono |
| customer.name | string | No | Nombre |
| customer.address | string | No | Dirección |
| delivery_zone_id | integer | No | ID de zona de delivery |
| delivery_company_id | integer | No | ID de empresa de delivery |
| payment_method | string | No | efectivo, tarjeta, transferencia, mixto, fiado |
| notas | string | No | Notas (max 500) |
| redirect_url | string | No | URL de redirección post-checkout |

### Validaciones:
- Stock insuficiente retorna 400
- Carrito debe estar en estado `active`
- Si no existe cliente y no se provee email, retorna error `missing_customer`
- Sesión de caja requerida (el usuario autenticado debe tener caja abierta)

### Response `200 OK`:

```json
{
    "data": {
        "venta": {
            "id": 45,
            "total": "575.00",
            "estado": "completada",
            "ncf": "B0100000045"
        },
        "checkout_url": "https://tienda.com/gracias"
    },
    "errors": [],
    "message": "Order created successfully."
}
```

---

## Endpoint Checkout Ecomm (Invitado)

Permite checkout sin cuenta de cliente, creando un walk-in.

**`POST /api/ecomm/checkout/guest`**

### Request Body:

```json
{
    "cart_id": 2,
    "customer_email": "invitado@email.com",
    "customer_phone": "8095559999",
    "customer_name": "Cliente Invitado",
    "customer_address": "Calle Principal #123",
    "payment_method": "tarjeta",
    "redirect_url": "https://tienda.com/gracias"
}
```

### Campos:

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| cart_id | integer | Sí | ID del carrito |
| customer_email | string | Sí | Email del cliente |
| customer_phone | string | Sí | Teléfono |
| customer_name | string | Sí | Nombre completo |
| customer_address | string | No | Dirección |
| payment_method | string | No | efectivo, tarjeta, transferencia, mixto |
| redirect_url | string | No | URL de redirección |

### Response `200 OK`

---

## Endpoint Listar Promociones Ecomm

Lista las promociones activas de la instancia.

**`GET /api/ecomm/promociones`**

### Parámetros Query

| Campo | Tipo | Descripción |
|-------|------|-------------|
| codigo | string | Filtrar por código de promoción |

### Response `200 OK`:

```json
{
    "data": {
        "data": [
            {
                "id": 1,
                "codigo": "VERANO2026",
                "nombre": "Descuento de Verano",
                "tipo": "porcentaje",
                "valor": "15.00",
                "minimo_compra": "500.00",
                "activa": true,
                "uso_actual": 25,
                "limite_uso": 100
            }
        ]
    },
    "errors": [],
    "message": "Success"
}
```

---

## Endpoint Validar Código de Promoción

Valida si un código de promoción es válido y calcula el descuento.

**`POST /api/ecomm/promociones/validar`**

### Request Body:

```json
{
    "codigo": "VERANO2026",
    "cart_id": 1,
    "subtotal": 750.00,
    "aplica_item_id": null
}
```

### Campos:

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| codigo | string | Sí | Código de promoción |
| cart_id | integer | Sí | ID del carrito |
| subtotal | numeric | Sí | Subtotal del carrito |
| aplica_item_id | integer | No | ID de item específico |

### Response `200 OK`:

```json
{
    "data": {
        "promocion": {
            "id": 1,
            "codigo": "VERANO2026",
            "nombre": "Descuento de Verano",
            "tipo": "porcentaje",
            "valor": "15.00",
            "descuento_aplicable": 112.50
        },
        "aplica": true,
        "mensaje": "Promotion applied successfully."
    },
    "errors": [],
    "message": "Success"
}
```

### Errores:

| Código | Error | Descripción |
|--------|-------|-------------|
| 400 | invalid_code | Código inválido o inactivo |
| 400 | expired | Promoción expirada |
| 400 | does_not_apply | Carrito no cumple criterios |

---

## Endpoint Aplicar Promoción al Carrito

**`POST /api/ecomm/carts/{cartId}/promo`**

### Request Body:

```json
{
    "codigo": "VERANO2026"
}
```

### Response `200 OK`:

Carrito actualizado con descuento aplicado.

### Errores:

| Código | Error | Descripción |
|--------|-------|-------------|
| 400 | invalid_code | Código inválido o expirado |
| 400 | min_purchase_not_met | No cumple compra mínima |
| 400 | does_not_apply | No califica para esta promoción |

---

## Endpoint Eliminar Promoción del Carrito

**`DELETE /api/ecomm/carts/{cartId}/promo`**

Elimina todas las promociones aplicadas al carrito.

### Response `200 OK`

---

## Endpoint Ver Cuenta de Lealtad

**`GET /api/ecomm/lealtad`**

### Parámetros Query

| Campo | Tipo | Descripción |
|-------|------|-------------|
| cliente_id | integer | ID del cliente |
| cart_id | integer | ID del carrito (opcional) |

### Response `200 OK`:

```json
{
    "data": {
        "cuenta": {
            "id": 1,
            "puntos_acumulados": 450,
            "puntos_canjeados": 100,
            "puntos_vencidos": 0,
            "puntos_disponibles": 350,
            "nivel": "plata",
            "tasa_cambio": 1.5,
            "ultima_actividad": "2026-08-30T10:00:00"
        },
        "recompensas": [
            {"nombre": "5% Descuento", "costo_puntos": 100, "descuento": 0.05},
            {"nombre": "10% Descuento", "costo_puntos": 200, "descuento": 0.10},
            {"nombre": "15% Descuento", "costo_puntos": 300, "descuento": 0.15},
            {"nombre": "Gratis Envio", "costo_puntos": 150, "descuento": "envio_gratis"}
        ]
    },
    "errors": [],
    "message": "Success"
}
```

---

## Endpoint Canjear Recompensa de Lealtad

**`POST /api/ecomm/lealtad/canjear`**

### Request Body:

```json
{
    "cliente_id": 5,
    "cart_id": 1,
    "recompensa_id": "200"
}
```

### Campos:

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| cliente_id | integer | Sí | ID del cliente |
| cart_id | integer | Sí | ID del carrito |
| recompensa_id | string | Sí | ID de recompensa: 100, 150, 200, 300 |

### Recompensas Disponibles:

| ID | Nombre | Costo Puntos | Descuento |
|----|--------|-------------|-----------|
| 100 | 5% Descuento | 100 | 5% |
| 150 | Gratis Envio | 150 | Envío gratis |
| 200 | 10% Descuento | 200 | 10% |
| 300 | 15% Descuento | 300 | 15% |

### Response `200 OK`:

```json
{
    "data": {
        "cuenta": {
            "puntos_disponibles": 150,
            "nivel": "plata"
        },
        "recompensa": {
            "nombre": "10% Descuento",
            "descuento": 0.10,
            "puntos_gastados": 200
        }
    },
    "errors": [],
    "message": "Reward redeemed successfully."
}
```

### Errores:

| Código | Error | Descripción |
|--------|-------|-------------|
| 400 | invalid_reward | Recompensa no válida |
| 400 | insufficient_points | Puntos insuficientes |

---

## Endpoint Historial de Lealtad

**`GET /api/ecomm/lealtad/historial`**

### Parámetros Query

| Campo | Tipo | Descripción |
|-------|------|-------------|
| cliente_id | integer | ID del cliente |
| limite | integer | Límite de registros (default 20, max 50) |

### Response `200 OK`:

```json
{
    "data": {
        "movimientos": [
            {
                "id": 1,
                "tipo": "ganar",
                "cantidad": 575,
                "notas": "Puntos ganados en venta #45",
                "created_at": "2026-08-30T10:00:00"
            }
        ],
        "total": 1
    },
    "errors": [],
    "message": "Success"
}
```

---

## Endpoint Listar Productos (Erpipos v3)

Lista productos de la instancia en formato FlowHub (centavos, UUIDs).

**`GET /api/erpipos/v3/products`**

### Parámetros Query

| Campo | Tipo | Descripción |
|-------|------|-------------|
| search | string | Buscar por nombre, código, marca, modelo |
| page | integer | Página (default 1) |
| limit | integer | Por página (default 25, max 100) |

### Response `200 OK`:

```json
{
    "status": "success",
    "data": {
        "products": [
            {
                "id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
                "internal_id": 12,
                "name": "Smartphone Galaxy S25",
                "description": "Último modelo Samsung",
                "price": 3500000,
                "qtyOnHand": 25,
                "sku": "SAM-S25-001",
                "brand": "Samsung",
                "model": "Galaxy S25",
                "imageUrl": "https://domain.com/storage/productos/12.jpg",
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

### Nota: Los precios están en centavos (FlowHub convention). Ej: 3500000 = RD$35,000.00

---

## Endpoint Ver Producto (Erpipos v3)

**`GET /api/erpipos/v3/products/{id}`**

El `id` puede ser un UUID de FlowHub o el ID interno numérico.

### Response `200 OK`:

```json
{
    "status": "success",
    "data": {
        "id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
        "internal_id": 12,
        "name": "Smartphone Galaxy S25",
        "description": "Último modelo Samsung",
        "price": 3500000,
        "costPrice": 2800000,
        "qtyOnHand": 25,
        "qtyMinimum": 5,
        "sku": "SAM-S25-001",
        "brand": "Samsung",
        "model": "Galaxy S25",
        "imageUrl": "https://domain.com/storage/productos/12.jpg",
        "taxRate": 18,
        "unitOfMeasure": "Unidad",
        "active": true
    },
    "message": "Success"
}
```

---

## Endpoint Listar Clientes (Erpipos v3)

**`GET /api/erpipos/v3/customers`**

### Parámetros Query

| Campo | Tipo | Descripción |
|-------|------|-------------|
| search | string | Buscar por nombre, email, teléfono, cédula |
| page | integer | Página (default 1) |
| limit | integer | Por página (default 25, max 100) |

### Response `200 OK`:

```json
{
    "status": "success",
    "data": {
        "customers": [
            {
                "id": "b2c3d4e5-f6a7-8901-bcde-f12345678901",
                "internal_id": 5,
                "firstName": "Juan",
                "lastName": "Pérez",
                "email": "juan@email.com",
                "phone": "8095551234",
                "identification": "001-1234567-8"
            }
        ],
        "pagination": {...}
    },
    "message": "Success"
}
```

---

## Endpoint Crear Cliente (Erpipos v3)

**`POST /api/erpipos/v3/customers`**

### Request Body:

```json
{
    "firstName": "Juan",
    "lastName": "Pérez",
    "email": "juan@email.com",
    "phone": "8095551234",
    "identification": "001-1234567-8"
}
```

### Response `201 Created`

---

## Endpoint Crear Carrito (Erpipos v3)

**`POST /api/erpipos/v3/carts`**

### Request Body:

```json
{
    "customerFlowId": "b2c3d4e5-f6a7-8901-bcde-f12345678901",
    "sessionId": "abc-123-def-456",
    "orderType": "pos"
}
```

### Campos:

| Campo | Tipo | Descripción |
|-------|------|-------------|
| customerFlowId | string | UUID del cliente en FlowHub |
| sessionId | string | ID de sesión del navegador/dispositivo |
| orderType | string | pos, web, phone |

### Response `201 Created`:

```json
{
    "status": "success",
    "data": {
        "id": "c3d4e5f6-a7b8-9012-cdef-123456789012",
        "internal_id": 3,
        "status": "active"
    },
    "message": "Cart created."
}
```

---

## Endpoint Ver Carrito (Erpipos v3)

**`GET /api/erpipos/v3/carts/{id}`**

### Response `200 OK`:

```json
{
    "status": "success",
    "data": {
        "id": "c3d4e5f6-a7b8-9012-cdef-123456789012",
        "internal_id": 3,
        "status": "active",
        "subtotal": 3500000,
        "tax": 630000,
        "discount": 0,
        "total": 4130000,
        "items": [
            {
                "id": "d4e5f6a7-b8c9-0123-defa-234567890123",
                "productId": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
                "productName": "Smartphone Galaxy S25",
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

---

## Endpoint Agregar Item al Carrito (Erpipos v3)

**`POST /api/erpipos/v3/carts/{id}/items`**

### Request Body:

```json
{
    "productId": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
    "quantity": 2,
    "priceOverride": 3200000,
    "notes": "Color negro"
}
```

### Campos:

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| productId | string | Sí | UUID o ID del producto |
| quantity | integer | Sí | Cantidad (min 1) |
| priceOverride | integer | No | Precio personalizado en centavos |
| notes | string | No | Notas del item (max 500) |

### Response `201 Created`

---

## Endpoint Actualizar Item del Carrito (Erpipos v3)

**`PUT /api/erpipos/v3/carts/{cartId}/items/{itemId}`**

### Request Body:

```json
{
    "quantity": 3
}
```

### Response `200 OK`

---

## Endpoint Eliminar Item del Carrito (Erpipos v3)

**`DELETE /api/erpipos/v3/carts/{cartId}/items/{itemId}`**

### Response `200 OK`

---

## Endpoint Checkout (Erpipos v3)

Procesa el checkout completo: crea venta, descuenta stock, registra pago, genera NCF.

**`POST /api/erpipos/v3/checkout`**

### Request Body:

```json
{
    "cartId": "c3d4e5f6-a7b8-9012-cdef-123456789012",
    "paymentMethod": "efectivo",
    "amountPaid": 4130000,
    "reference": "Recibo #1234",
    "customerFlowId": "b2c3d4e5-f6a7-8901-bcde-f12345678901",
    "email": "cliente@email.com"
}
```

### Campos:

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| cartId | string | Sí | UUID del carrito |
| paymentMethod | string | Sí | efectivo, tarjeta, transferencia, mixto, fiado |
| amountPaid | integer | No | Monto pagado en centavos (default: total del carrito) |
| reference | string | No | Referencia del pago |
| customerFlowId | string | No | UUID del cliente |
| email | string | No | Email para crear cliente walk-in |

### Response `201 Created`:

```json
{
    "status": "success",
    "data": {
        "orderId": "e5f6a7b8-c9d0-1234-efab-345678901234",
        "internal_id": 45,
        "total": 4130000,
        "paid": 4130000,
        "status": "completed",
        "pointsEarned": 4130
    },
    "message": "Checkout completed."
}
```

### Nota: Se generan puntos de lealtad automáticamente (1 punto por cada RD$1 de la venta).

---

## Endpoint Listar Promociones/Deals (Erpipos v3)

**`GET /api/erpipos/v3/deals`**

### Response `200 OK`:

```json
{
    "status": "success",
    "data": {
        "deals": [
            {
                "id": "f6a7b8c9-d0e1-2345-fabc-456789012345",
                "internal_id": 1,
                "code": "VERANO2026",
                "name": "Descuento de Verano",
                "description": "15% en compras mayores a RD$500",
                "type": "porcentaje",
                "value": 15,
                "minPurchase": 50000,
                "validFrom": "2026-06-01",
                "validUntil": "2026-08-31",
                "active": true
            }
        ]
    },
    "message": "Success"
}
```

---

## Endpoint Aplicar Deal al Carrito (Erpipos v3)

**`POST /api/erpipos/v3/carts/{cartId}/deals`**

### Request Body:

```json
{
    "code": "VERANO2026"
}
```

### Response `200 OK`:

```json
{
    "status": "success",
    "data": {
        "deal": {
            "code": "VERANO2026",
            "discount": 7500
        },
        "cart": {...}
    },
    "message": "Deal applied."
}
```

### Errores:

| Código | Mensaje | Descripción |
|--------|---------|-------------|
| 400 | Invalid or expired deal code | Código inválido o expirado |
| 400 | Cart does not qualify for this deal | Carrito no cumple criteria |

---

## Endpoint Ver Puntos de Lealtad (Erpipos v3)

**`GET /api/erpipos/v3/rewards`**

### Parámetros Query

| Campo | Tipo | Descripción |
|-------|------|-------------|
| customerFlowId | string | UUID del cliente (requerido) |

### Response `200 OK`:

```json
{
    "status": "success",
    "data": {
        "points": 350,
        "tier": "plata",
        "totalEarned": 450,
        "totalRedeemed": 100,
        "availableRewards": [
            {"id": "100", "name": "5% Descuento", "costInPoints": 100, "description": "5% off en tu compra"},
            {"id": "200", "name": "10% Descuento", "costInPoints": 200, "description": "10% off en tu compra"},
            {"id": "300", "name": "15% Descuento", "costInPoints": 300, "description": "15% off en tu compra"},
            {"id": "150", "name": "Envío Gratis", "costInPoints": 150, "description": "Envío gratuito"}
        ]
    },
    "message": "Success"
}
```

---

## Endpoint Canjear Recompensa (Erpipos v3)

**`POST /api/erpipos/v3/rewards/redeem`**

### Request Body:

```json
{
    "customerFlowId": "b2c3d4e5-f6a7-8901-bcde-f12345678901",
    "rewardId": "200",
    "cartId": "c3d4e5f6-a7b8-9012-cdef-123456789012"
}
```

### Campos:

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| customerFlowId | string | Sí | UUID del cliente |
| rewardId | string | Sí | 100, 150, 200, 300 |
| cartId | string | No | UUID del carrito (aplica descuento automáticamente) |

### Response `200 OK`:

```json
{
    "status": "success",
    "data": {
        "pointsRemaining": 150,
        "tier": "plata",
        "rewardRedeemed": "10% Descuento"
    },
    "message": "Reward redeemed."
}
```

### Errores:

| Código | Mensaje |
|--------|---------|
| 400 | Invalid reward |
| 400 | Insufficient points |

---

## Endpoint Listar Productos Tienda

API pública de productos para tienda en línea.

**`GET /api/tienda/productos`**

### Parámetros Query

| Campo | Tipo | Descripción |
|-------|------|-------------|
| linea | string | Filtrar por línea: alimentos, bebidas, accesorios, todos |
| categoria_id | integer | Filtrar por categoría |
| search | string | Buscar por nombre o código de barras |
| in_stock | boolean | Solo productos con stock > 0 |

### Response `200 OK`:

```json
{
    "productos": [
        {
            "id": 12,
            "nombre": "Café Premium",
            "codigo_barras": "7401234567890",
            "precio": "250.00",
            "stock": 100,
            "imagen": "https://domain.com/storage/productos/12.jpg",
            "linea_negocio": "alimentos",
            "categoria": {"id": 1, "nombre": "Bebidas"},
            "subcategoria": {"id": 5, "nombre": "Café"}
        }
    ],
    "total": 1
}
```

---

## Endpoint Listar Categorías Tienda

**`GET /api/tienda/categorias`**

### Response `200 OK`:

```json
{
    "categorias": [
        {
            "id": 1,
            "nombre": "Bebidas",
            "color": "#10b981",
            "icono": "bi-cup-hot",
            "subcategorias": [
                {"id": 5, "nombre": "Café"},
                {"id": 6, "nombre": "Jugos"}
            ]
        }
    ]
}
```

---

## Endpoint Ver Inventario

**`GET /api/tienda/inventario`**

### Parámetros Query

| Campo | Tipo | Descripción |
|-------|------|-------------|
| estado | string | critico, bajo, todos (default: todos) |
| linea | string | Filtrar por línea de negocio |

### Response `200 OK`:

```json
{
    "inventario": [
        {
            "id": 12,
            "nombre": "Café Premium",
            "codigo_barras": "7401234567890",
            "stock": 3,
            "stock_minimo": 10,
            "estado_stock": "bajo",
            "linea_negocio": "alimentos",
            "ultima_actualizacion": "2026-08-30T10:00:00"
        }
    ],
    "total": 1,
    "estado_filtro": "todos"
}
```

### Estados de Stock:

| Estado | Descripción |
|--------|-------------|
| sin_stock | stock <= 0 |
| bajo | stock > 0 pero <= stock_minimo |
| ok | stock > stock_minimo |

---

## Endpoint Ajustar Inventario

**`POST /api/tienda/inventario/ajuste`**

### Request Body:

```json
{
    "producto_id": 12,
    "cantidad": 50,
    "motivo": "ajuste",
    "notas": "Inventario físico confirmado"
}
```

### Campos:

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| producto_id | integer | Sí | ID del producto |
| cantidad | integer | Sí | Cantidad (positiva o negativa) |
| motivo | string | Sí | ajuste, merma, inventario |
| notas | string | No | Notas (max 500) |

### Response `200 OK`:

```json
{
    "message": "Ajuste de inventario realizado correctamente.",
    "nuevo_stock": 150,
    "movimiento_id": 45
}
```

### Errores:

| Código | Mensaje |
|--------|---------|
| 422 | No se puede realizar el ajuste. El stock no puede quedar negativo. |
| 404 | Producto no encontrado. |

---

## Endpoint Ver Kardex del Producto

**`GET /api/tienda/kardex/{productoId}`**

### Parámetros Query

| Campo | Tipo | Descripción |
|-------|------|-------------|
| desde | date | Fecha desde (YYYY-MM-DD) |
| hasta | date | Fecha hasta (YYYY-MM-DD) |

### Response `200 OK`:

```json
{
    "producto": {
        "id": 12,
        "nombre": "Café Premium",
        "codigo_barras": "7401234567890",
        "stock_actual": 150
    },
    "movimientos": [
        {
            "fecha": "2026-08-30T10:00:00",
            "tipo": "ajuste_positivo",
            "cantidad": 50,
            "saldo": 150,
            "motivo": "ajuste - Inventario físico",
            "usuario": "Admin",
            "referencia": null
        }
    ],
    "total_movimientos": 1
}
```

---

## Endpoint Checkout POS

Procesa una venta mixta desde el terminal POS.

**`POST /api/pos/checkout`**

### Request Body:

```json
{
    "cliente_id": 5,
    "vehiculo_id": 3,
    "metodo_pago": "efectivo",
    "tipo_venta_id": 1,
    "servicios": [1, 2],
    "productos": [
        {"id": 12, "cantidad": 2},
        {"id": 15, "cantidad": 1}
    ],
    "paquetes": [
        {"id": 1, "cantidad": 1}
    ]
}
```

### Campos:

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| cliente_id | integer | No | ID del cliente |
| vehiculo_id | integer | No | ID del vehículo (lavadero) |
| metodo_pago | string | Sí | efectivo, tarjeta, transferencia, fiado |
| tipo_venta_id | integer | No | ID del tipo de venta |
| servicios | array | No | IDs de servicios |
| productos | array | No | Array de {id, cantidad} |
| paquetes | array | No | Array de {id, cantidad} |

### Response `200 OK`:

```json
{
    "success": true,
    "venta_id": 46,
    "total": 8500.00
}
```

---

## Endpoint Búsqueda Rápida POS

**`GET /api/pos/quick-sale`**

### Parámetros Query

| Campo | Tipo | Descripción |
|-------|------|-------------|
| search | string | Término de búsqueda (min 2 caracteres) |
| linea | string | Filtrar por línea de negocio |

### Response `200 OK`:

```json
{
    "productos": [
        {
            "id": 12,
            "nombre": "Café Premium",
            "codigo_barras": "7401234567890",
            "precio": 250.00,
            "stock": 100,
            "categoria_id": 1
        }
    ]
}
```

---

## Endpoint Guardar Venta en Espera

**`POST /api/pos/hold`**

### Request Body:

```json
{
    "cliente_id": 5,
    "vehiculo_id": 3,
    "servicios": [1],
    "productos": [{"id": 12, "cantidad": 1}],
    "metodo_pago": "efectivo",
    "total": 250.00
}
```

### Response `200 OK`:

```json
{
    "success": true,
    "hold_id": "hold-abc-123"
}
```

---

## Endpoint Restaurar Venta en Espera

**`POST /api/pos/restore/{holdId}`**

### Response `200 OK`:

```json
{
    "success": true,
    "data": {
        "cliente_id": 5,
        "productos": [...],
        "total": 250.00
    }
}
```

---

## Endpoint Calcular Total

**`POST /api/pos/calculate-total`**

### Request Body:

```json
{
    "items": [
        {"id": 12, "cantidad": 2, "precio": 250.00, "itbis": 18},
        {"id": 15, "cantidad": 1, "precio": 100.00, "itbis": 18}
    ]
}
```

### Response `200 OK`:

```json
{
    "subtotal": 600.00,
    "itbis": 108.00,
    "total": 708.00
}
```

---

## Field Reference — Erpipos v3

### Producto

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | string | UUID de FlowHub |
| internal_id | integer | ID interno BIGINT |
| name | string | Nombre del producto |
| description | string | Descripción |
| price | integer | Precio en centavos |
| costPrice | integer | Precio de compra en centavos |
| qtyOnHand | integer | Stock disponible |
| qtyMinimum | integer | Stock mínimo |
| sku | string | Código de barras |
| brand | string | Marca |
| model | string | Modelo |
| imageUrl | string | URL de imagen |
| taxRate | float | Porcentaje ITBIS |
| unitOfMeasure | string | Unidad de medida |
| active | boolean | Producto activo |

### Customer

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | string | UUID de FlowHub |
| internal_id | integer | ID interno BIGINT |
| firstName | string | Nombre |
| lastName | string | Apellido |
| email | string | Email |
| phone | string | Teléfono |
| identification | string | Cédula/RNC |

### Cart (Erpipos v3)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | string | UUID de FlowHub |
| internal_id | integer | ID interno BIGINT |
| status | string | active, completed |
| subtotal | integer | Subtotal en centavos |
| tax | integer | Impuestos en centavos |
| discount | integer | Descuento en centavos |
| total | integer | Total en centavos |
| items | array | Items del carrito |

### Cart Item (Erpipos v3)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | string | UUID de FlowHub |
| productId | string | UUID del producto |
| productName | string | Nombre del producto |
| quantity | integer | Cantidad |
| unitPrice | integer | Precio unitario en centavos |
| subtotal | integer | Subtotal en centavos |
| taxRate | float | Porcentaje ITBIS |
| notes | string | Notas del item |

---

## Notas

- **Autenticación**: Todas las endpoints usan Instance API Key (`iak_*`). Cada instancia solo accede a sus propios datos.
- **Multi-tenant**: Los datos están aislados por `business_instance_id` de la API key.
- **FlowHub UUIDs**: La API de Erpipos v3 usa UUIDs para compatibilidad con FlowHub. Los IDs internos (BIGINT) también son aceptados.
- **Moneda**: Erpipos v3 usa centavos (ej: 3500000 = RD$35,000.00). Ecomm/Tienda/POS usan decimales.
- **NCF**: El checkout genera automáticamente NCF tipo B01 si la secuencia está configurada.
- **Lealtad**: Los puntos se generan automáticamente al completar checkout (1 punto por RD$1).
- **Rate Limiting**: 60 requests por minuto por IP.
