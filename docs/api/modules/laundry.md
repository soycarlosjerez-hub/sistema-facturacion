# Laundry API

Service module for managing laundry/lavadero services.

## Endpoints

### LIST `/api/modules/laundry`

Retrieve paginated list of laundry records with filters.

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `cliente_id` | integer | Filter by client |
| `sucursal_id` | integer | Filter by branch |
| `estado` | string | Filter by status |

**Response:** Paginated collection of laundry objects.

---

### CREATE `/api/modules/laundry`

Create a new laundry record.

**Request Body:**

| Field | Required | Type | Description |
|-------|----------|------|-------------|
| `folio` | Yes | string | Laundry identifier |
| `cliente_id` | Yes | integer | Client ID (must exist) |
| `sucursal_id` | Yes | integer | Branch ID (must exist) |
| `user_id` | Yes | integer | User ID (must exist) |
| `vehiculo_id` | No | integer | Vehicle ID |
| `fecha_ingreso` | No | datetime | Entry date/time |
| `fecha_entrega` | No | datetime | Delivery date/time |
| `estado` | Yes | string | Service status |
| `servicio` | Yes | string | Service type |
| `total` | Yes | numeric | Total amount (>= 0) |
| `notas` | No | string | Notes |

---

### SHOW `/api/modules/laundry/{id}`

Retrieve a single laundry record by ID.

**Response:** Laundry object with all fields.

---

### UPDATE `/api/modules/laundry/{id}`

Update an existing laundry record.

**Request Body:** Same fields as CREATE (all optional for partial updates).

---

### DELETE `/api/modules/laundry/{id}`

Delete a laundry record by ID.

**Response:** Success confirmation.
