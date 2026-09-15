# Kitchen Display System (KDS) API

Endpoints for the kitchen display system to manage order preparation status.

## Base URL

```
/api/kds
```

## Authentication

Requires authentication with `auth` session cookie.

---

## Endpoint Orders

### Órdenes Activas

**`GET /api/kds/orders`**

Retorna todas las órdenes activas para el sistema de visualización de cocina. Retorna datos crudos del servicio KDS.

**Headers:**

```
Accept: application/json
Cookie: _session={cookie}
```

**Response `200 OK`:**

```json
{
  "data": [
    {
      "id": 100,
      "tipo_orden": "mostrador",
      "estado": "pendiente",
      "numero_orden": "0100",
      "created_at": "2026-07-20T14:00:00.000000Z",
      "detalles": [
        {
          "id": 200,
          "producto_id": 12,
          "producto": {
            "id": 12,
            "nombre": "Pollo Guisado"
          },
          "cantidad": 2,
          "notas": "",
          "curso": "fuerte",
          "estado_cocina": "pendiente"
        },
        {
          "id": 201,
          "producto_id": 8,
          "producto": {
            "id": 8,
            "nombre": "Sopa Tropical"
          },
          "cantidad": 1,
          "notas": "Extra salsa",
          "curso": "entrada",
          "estado_cocina": "en_preparacion"
        }
      ]
    }
  ]
}
```

---

## Endpoint Update Status

### Actualizar Estado de Cocina

**`PATCH /api/kds/orders/{detalle}/status`**

Actualiza el estado de preparación de cocina de un detalle de orden específico (item).

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `detalle` | `integer` | ID del detalle de orden (item) |

**Headers:**

```
Accept: application/json
Content-Type: application/json
Cookie: _session={cookie}
```

**Request Body:**

```json
{
  "estado_cocina": "en_preparacion"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `estado_cocina` | `string` | **Sí** | Estado de cocina |

**Validations:**

```
estado_cocina: required|in:pendiente,en_preparacion,listo,entregado
```

**Response `200 OK`:**

```json
{
  "data": {
    "id": 200,
    "orden_id": 100,
    "producto_id": 12,
    "cantidad": 2,
    "estado_cocina": "en_preparacion",
    "updated_at": "2026-07-20T14:30:00.000000Z"
  }
}
```

---

## Field Reference

### Valores de Estado de Cocina

| Valor | Descripción |
|-------|-------------|
| `pendiente` | Esperando preparación |
| `en_preparacion` | En proceso de preparación |
| `listo` | Listo para retiro/entrega |
| `entregado` | Entregado al cliente |

### Tipos de Orden

| Valor | Descripción |
|-------|-------------|
| `mostrador` | Orden en mostrador |
| `delivery` | Orden de delivery |
| `pickup` | Orden para retirar |

---

## Notas

- Los estados de cocina siguen el flujo: `pendiente` → `en_preparacion` → `listo` → `entregado`
- Cada detalle de orden tiene su propio estado de cocina independiente
- Las notas de cada item se muestran en pantalla para la cocina
