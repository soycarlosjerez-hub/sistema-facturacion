# Rentals API

Service module for managing vehicle/alquiler rentals.

## Endpoints

### LIST `/api/modules/rentals`

Retrieve paginated list of rentals with filters.

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `cliente_id` | integer | Filter by client |
| `sucursal_id` | integer | Filter by branch |
| `estado` | string | Filter by status |

**Response:** Paginated collection of rental objects.

---

### CREATE `/api/modules/rentals`

Create a new rental.

**Request Body:**

| Field | Required | Type | Description |
|-------|----------|------|-------------|
| `folio` | Yes | string | Rental identifier |
| `cliente_id` | Yes | integer | Client ID (must exist) |
| `sucursal_id` | Yes | integer | Branch ID (must exist) |
| `user_id` | Yes | integer | User ID (must exist) |
| `vehiculo_id` | No | integer | Vehicle ID |
| `fecha_inicio` | Yes | date | Start date |
| `fecha_fin` | Yes | date | End date (must be after `fecha_inicio`) |
| `estado` | Yes | string | Rental status |
| `total` | Yes | numeric | Total amount (>= 0) |
| `deposito` | No | numeric | Deposit amount |
| `notas` | No | string | Notes |

---

### SHOW `/api/modules/rentals/{id}`

Retrieve a single rental by ID.

**Response:** Rental object with all fields.

---

### UPDATE `/api/modules/rentals/{id}`

Update an existing rental.

**Request Body:** Same fields as CREATE (all optional for partial updates).

---

### DELETE `/api/modules/rentals/{id}`

Delete a rental by ID.

**Response:** Success confirmation.
