# Delivery API

Service module for managing delivery/conduce services.

## Endpoints

### LIST `/api/modules/delivery`

Retrieve paginated list of deliveries with filters.

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `cliente_id` | integer | Filter by client |
| `sucursal_id` | integer | Filter by branch |
| `estado` | string | Filter by status |

**Response:** Paginated collection of delivery objects.

---

### CREATE `/api/modules/delivery`

Create a new delivery record.

**Request Body:**

| Field | Required | Type | Description |
|-------|----------|------|-------------|
| `folio` | Yes | string | Delivery identifier |
| `cliente_id` | Yes | integer | Client ID (must exist) |
| `sucursal_id` | Yes | integer | Branch ID (must exist) |
| `user_id` | Yes | integer | User ID (must exist) |
| `vehiculo_id` | No | integer | Vehicle ID |
| `fecha_recepcion` | No | datetime | Reception date/time |
| `fecha_entrega` | No | datetime | Delivery date/time |
| `estado` | Yes | string | Delivery status |
| `total` | Yes | numeric | Total amount (>= 0) |
| `kilometraje` | No | integer | Kilometer reading |
| `combustible` | No | string | Fuel level/status |
| `danios` | No | string | Damage notes |
| `notas` | No | string | Additional notes |

---

### SHOW `/api/modules/delivery/{id}`

Retrieve a single delivery by ID.

**Response:** Delivery object with all fields.

---

### UPDATE `/api/modules/delivery/{id}`

Update an existing delivery record.

**Request Body:** Same fields as CREATE (all optional for partial updates).

---

### DELETE `/api/modules/delivery/{id}`

Delete a delivery by ID.

**Response:** Success confirmation.
