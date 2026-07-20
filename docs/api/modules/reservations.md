# Reservations API

Service module for managing restaurant table reservations.

## Endpoints

### LIST `/api/modules/reservations`

Retrieve paginated list of reservations with filters.

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `cliente_id` | integer | Filter by client |
| `mesa_id` | integer | Filter by table |
| `estado` | string | Filter by status |
| `fecha` | date | Filter by date |

**Response:** Paginated collection of reservation objects.

---

### CREATE `/api/modules/reservations`

Create a new reservation.

**Request Body:**

| Field | Required | Type | Description |
|-------|----------|------|-------------|
| `cliente_nombre` | Yes | string | Client name |
| `cliente_id` | No | integer | Client ID (if exists, auto-updates client info) |
| `cliente_telefono` | No | string | Client phone |
| `cliente_email` | No | string | Client email (validates email format) |
| `mesa_id` | Yes | integer | Table ID (must exist) |
| `fecha_hora` | Yes | datetime | Reservation date/time |
| `personas` | Yes | integer | Number of people (>= 1) |
| `estado` | Yes | string | Reservation status |
| `notas` | No | string | Notes |

**Notes:**
- `user_id` and `tenant_id` are automatically set from authentication.
- If `cliente_id` is provided, existing client information is auto-updated.
- If `cliente_email` is present, sends `ReservacionRecibidaMail` notification.
- Confirmation and cancellation emails are sent automatically on state changes.
- Blocks table opening if a pending reservation exists for that table (within >1 hour window).

---

### SHOW `/api/modules/reservations/{id}`

Retrieve a single reservation by ID.

**Response:** Reservation object with all fields.

---

### UPDATE `/api/modules/reservations/{id}`

Update an existing reservation.

**Request Body:** Same fields as CREATE (all optional for partial updates).

**Notes:** State changes trigger automatic email notifications (confirmation on confirm, cancellation on cancel).

---

### DELETE `/api/modules/reservations/{id}`

Delete a reservation by ID.

**Response:** Success confirmation.
