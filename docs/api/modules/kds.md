# Kitchen Display System (KDS) API

Endpoints for the kitchen display system to manage order preparation status.

## Base URL

```
/api/kds
```

## Authentication

Requires authentication with `auth` session cookie.

---

## GET /api/kds/orders

Retrieve all active orders for the kitchen display system. Returns raw data from the KDS service.

### Query Parameters

None.

### Response

`200 OK`

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

## PATCH /api/kds/orders/{detalle}/status

Update the kitchen preparation status of a specific order detail (line item).

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `detalle` | integer | Order detail (line item) ID |

### Request Body

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `estado_cocina` | string | Yes | In: `pendiente`, `en_preparacion`, `listo`, `entregado` |

### Example Request

```json
{
  "estado_cocina": "en_preparacion"
}
```

### Response

`200 OK`

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

### KDS Order Detail Status Values

| Value | Description |
|-------|-------------|
| `pendiente` | Waiting to be prepared |
| `en_preparacion` | Currently being prepared |
| `listo` | Ready for pickup/delivery |
| `entregado` | Delivered to customer |

### Order Types

| Value | Description |
|-------|-------------|
| `mostrador` | Counter/order-at-counter |
| `delivery` | Delivery order |
| `pickup` | Pickup order |
