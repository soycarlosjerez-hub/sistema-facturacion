# Price Lists API

Service module for managing price lists (listas de precios).

## Endpoints

### LIST `/api/modules/price-lists`

Retrieve paginated list of price lists with filters.

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sucursal_id` | integer | Filter by branch |
| `activa` | boolean | Filter by active status |

**Response:** Paginated collection of price list objects.

---

### CREATE `/api/modules/price-lists`

Create a new price list.

**Request Body:**

| Field | Required | Type | Description |
|-------|----------|------|-------------|
| `nombre` | Yes | string | Price list name |
| `descripcion` | No | string | Description |
| `porcentaje` | Yes | numeric | Percentage increase (>= 0) |
| `sucursal_id` | Yes | integer | Branch ID (must exist) |
| `activa` | No | boolean | Whether the list is active |
| `tenant_id` | Yes | integer | Tenant ID (must exist) |

---

### SHOW `/api/modules/price-lists/{id}`

Retrieve a single price list by ID.

**Response:** Price list object with all fields.

---

### UPDATE `/api/modules/price-lists/{id}`

Update an existing price list.

**Request Body:** Same fields as CREATE (all optional for partial updates).

---

### DELETE `/api/modules/price-lists/{id}`

Delete a price list by ID.

**Response:** Success confirmation.
