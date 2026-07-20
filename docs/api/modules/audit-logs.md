# Audit Logs API

List and filter system audit logs. **READ ONLY.**

## Base URL

```
/api/audit-logs
```

## Authentication

Requires authentication with `auth` session cookie.

---

## Endpoint Index

### Listar Registros de Auditoría

**`GET /api/audit-logs`**

Lista entradas de registros de auditoría con información de usuario y auditable. Paginado a 15 registros por página.

**Query Parameters:**

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `user_id` | `integer` | No | Filtrar por ID de usuario |
| `action` | `string` | No | Filtrar por acción (created, updated, deleted, etc.) |
| `model_type` | `string` | No | Filtrar por tipo de modelo |
| `fecha_desde` | `date` | No | Fecha inicio (YYYY-MM-DD) |
| `fecha_hasta` | `date` | No | Fecha fin (YYYY-MM-DD) |
| `page` | `integer` | No | Número de página (default: 1) |

**Headers:**

```
Accept: application/json
Cookie: _session={cookie}
```

**Response `200 OK` — Envuelto en `{ data: [...] }` con metadatos de paginación:**

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

**Descripción de Campos:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | `integer` | ID de entrada de auditoría |
| `user_id` | `integer` | ID del usuario que realizó la acción |
| `user` | `object` | Objeto usuario anidado con id, nombre, email |
| `auditable_id` | `integer` | ID del registro del modelo afectado |
| `auditable_type` | `string` | Nombre clase completamente calificado del modelo afectado |
| `action` | `string` | Acción realizada (created, updated, deleted) |
| `values` | `object` | Cambios realizados (valores viejos/nuevos) |
| `fecha` | `datetime` | Timestamp de la acción (ISO 8601) |
| `ip_address` | `string` | IP desde la cual se realizó la acción |

**Ejemplos de Request:**

```
GET /api/audit-logs
GET /api/audit-logs?action=deleted
GET /api/audit-logs?user_id=5&fecha_desde=2026-07-01&fecha_hasta=2026-07-20
```

---

## Notas

- Solo lectura — no hay endpoints de escritura
- `auditable_type` usa el namespace completo del modelo (ej: `App\Models\Orden`)
- `values` contiene un objeto JSON con los cambios detectados
- `ip_address` registra la IP del cliente que realizó la acción
- Los registros se mantienen indefinidamente para cumplimiento
