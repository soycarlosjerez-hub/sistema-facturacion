# Audit Logs API

List and filter system audit logs. **READ ONLY.**

## Base URL

```
/api/audit-logs
```

## Authentication

Requires authentication with `auth` session cookie.

---

## GET /api/audit-logs

Lists audit log entries with user and auditable information. Paginated at 15 records per page.

### Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `user_id` | integer | Optional | Filter by user ID |
| `action` | string | Optional | Filter by action (created, updated, deleted, etc.) |
| `model_type` | string | Optional | Filter by model type |
| `fecha_desde` | date | Optional | Start date filter (YYYY-MM-DD) |
| `fecha_hasta` | date | Optional | End date filter (YYYY-MM-DD) |
| `page` | integer | Optional | Page number (default: 1) |

### Response

`200 OK` — Wrapped in `{ data: [...] }` object with pagination metadata.

```json
{
  "data": [
    {
      "id": 1,
      "user_id": 5,
      "user": {
        "id": 5,
        "nombre": "Juan Pérez",
        "email": "juan@example.com"
      },
      "auditable_id": 42,
      "auditable_type": "App\\Models\\Orden",
      "action": "updated",
      "values": {
        "estado": "completada"
      },
      "fecha": "2026-07-20T14:30:00.000000Z",
      "ip_address": "192.168.1.100"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "last_page": 5,
    "from": 1,
    "to": 15,
    "total": 67
  }
}
```

### Field Descriptions

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Audit log entry ID |
| `user_id` | integer | ID of the user who performed the action |
| `user` | object | Nested user object with id, nombre, email |
| `auditable_id` | integer | ID of the affected model record |
| `auditable_type` | string | Fully qualified class name of the affected model |
| `action` | string | Action performed (created, updated, deleted) |
| `values` | object | Changes made (old/new values) |
| `fecha` | datetime | Timestamp of the action (ISO 8601) |
| `ip_address` | string | IP address from which the action was performed |

### Example Requests

```
GET /api/audit-logs
GET /api/audit-logs?action=deleted
GET /api/audit-logs?user_id=5&fecha_desde=2026-07-01&fecha_hasta=2026-07-20
```
