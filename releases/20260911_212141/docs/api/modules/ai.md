# AI Assistant API

Asistente de inteligencia artificial integrado: chat conversacional con streaming, historial de conversaciones y herramientas disponibles.

---

## Endpoint Resumen

| Endpoint | Método | Ruta | Auth | Descripción |
|----------|--------|------|------|-------------|
| Chat | `POST` | `/api/ai/chat` | Sí | Enviar mensaje y obtener respuesta |
| Conversaciones | `GET` | `/api/ai/conversations` | Sí | Listar conversaciones del usuario |
| Ver Conversación | `GET` | `/api/ai/conversations/{conversation}` | Sí | Mensajes de una conversación |
| Herramientas | `GET` | `/api/ai/tools` | Sí | Listar herramientas disponibles |

---

## Autenticación

Requiere middleware `ai` que valida:

1. **Autenticación**: El usuario debe estar logueado (`auth()->check()`)
2. **Instancia**: El usuario debe tener una `business_instance_id` asignada
3. **No bloqueado**: La instancia del negocio no debe estar bloqueada

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Errores de Autenticación:**

| Código | Mensaje | Causa |
|--------|---------|-------|
| `401` | `No autenticado.` | Token inválido o ausente |
| `403` | `Usuario sin instancia asignada.` | Usuario sin `business_instance_id` |
| `403` | `Instancia bloqueada.` | La instancia del negocio está bloqueada |

---

## Endpoint Chat

### Enviar Mensaje

**`POST /api/ai/chat`**

Envía un mensaje al asistente IA y retorna la respuesta. Soporta respuesta estándar (JSON) y streaming (SSE).

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**

```json
{
  "message": "¿Cuáles son mis ventas de hoy?",
  "conversation_id": "550e8400-e29b-41d4-a716-446655440000",
  "stream": false
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `message` | `string` | **Sí** | Mensaje del usuario (max: 2000 caracteres) |
| `conversation_id` | `string` | No | ID de conversación existente para continuar el contexto (UUID, max: 36) |
| `stream` | `boolean` | No | `true` para respuesta streaming SSE (default: `false`) |

**Validations:**

```
message: required|string|max:2000
conversation_id: nullable|string|max:36
```

---

### Respuesta Estándar (JSON)

**Response `200 OK`:**

```json
{
  "response": "Hoy has registrado 12 ventas por un total de RD$ 45,680.00. El ticket promedio es de RD$ 3,806.67.",
  "conversation_id": "550e8400-e29b-41d4-a716-446655440000",
  "tool_used": null,
  "tool_result": null
}
```

**Campos de Respuesta:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `response` | `string` | Respuesta generada por el asistente |
| `conversation_id` | `string` | ID de la conversación (crear nueva o continuar) |
| `tool_used` | `string\|null` | Nombre de la herramienta ejecutada (si aplica) |
| `tool_result` | `any\|null` | Resultado de la herramienta ejecutada (si aplica) |

---

### Respuesta Streaming (SSE)

Cuando `stream: true`, retorna un `StreamedResponse` con formato Server-Sent Events:

**Request:**

```json
{
  "message": "Resume las ventas de esta semana",
  "stream": true
}
```

**Response `200 OK` (Content-Type: text/event-stream):**

```
data: {"type":"token","content":"Hoy"}

data: {"type":"token","content":" has"}

data: {"type":"token","content":" registrado"}

data: {"type":"token","content":" 12"}

data: {"type":"token","content":" ventas"}

data: {"type":"done","conversation_id":"550e8400-e29b-41d4-a716-446655440000"}
```

**Eventos SSE:**

| Tipo | Descripción |
|------|-------------|
| `token` | Fragmento parcial de la respuesta |
| `done` | Fin de la respuesta, incluye `conversation_id` |
| `error` | Error durante el streaming |

---

### Errores del Chat

| Código | Mensaje | Causa |
|--------|---------|-------|
| `400` | `Error del proveedor IA` | Error de comunicación con el servicio de IA |
| `400` | `Mensaje demasiado largo` | `message` excede 2000 caracteres |
| `400` | `Conversación no encontrada` | `conversation_id` no existe o no pertenece al usuario |
| `500` | `Error interno del servidor.` | Error inesperado del sistema |

---

## Endpoint Conversaciones

### Listar Conversaciones

**`GET /api/ai/conversations`**

Retorna las últimas 50 conversaciones del usuario autenticado, ordenadas por actividad reciente.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
[
  {
    "id": 1,
    "conversation_id": "550e8400-e29b-41d4-a716-446655440000",
    "title": "Ventas del día",
    "created_at": "2024-01-28T10:30:00.000000Z",
    "updated_at": "2024-01-28T14:22:00.000000Z"
  },
  {
    "id": 2,
    "conversation_id": "6ba7b810-9dad-11d1-80b4-00c04fd430c8",
    "title": "Inventario bajo",
    "created_at": "2024-01-27T09:15:00.000000Z",
    "updated_at": "2024-01-27T11:45:00.000000Z"
  }
]
```

**Campos:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | `integer` | ID interno de la conversación |
| `conversation_id` | `string` | UUID de la conversación (se usa en `chat`) |
| `title` | `string` | Título autogenerado de la conversación |
| `created_at` | `datetime` | Fecha de creación |
| `updated_at` | `datetime` | Última actividad |

---

## Endpoint Ver Conversación

### Obtener Mensajes

**`GET /api/ai/conversations/{conversation}`**

Retorna todos los mensajes de una conversación específica, incluyendo herramientas utilizadas.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Parámetros de Ruta:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `conversation` | `string` | UUID de la conversación |

**Response `200 OK`:**

```json
[
  {
    "id": 1,
    "conversation_id": "550e8400-e29b-41d4-a716-446655440000",
    "role": "user",
    "content": "¿Cuáles son mis ventas de hoy?",
    "tool_name": null,
    "tool_arguments": null,
    "tool_result": null,
    "created_at": "2024-01-28T14:20:00.000000Z"
  },
  {
    "id": 2,
    "conversation_id": "550e8400-e29b-41d4-a716-446655440000",
    "role": "assistant",
    "content": "Hoy has registrado 12 ventas por un total de RD$ 45,680.00.",
    "tool_name": "get_sales_summary",
    "tool_arguments": {"period": "today"},
    "tool_result": {"total": 12, "total_amount": 45680.00},
    "created_at": "2024-01-28T14:20:05.000000Z"
  }
]
```

**Campos de Mensaje:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | `integer` | ID del mensaje |
| `conversation_id` | `string` | UUID de la conversación |
| `role` | `string` | `user` o `assistant` |
| `content` | `string` | Contenido del mensaje |
| `tool_name` | `string\|null` | Nombre de la herramienta ejecutada |
| `tool_arguments` | `object\|null` | Argumentos enviados a la herramienta |
| `tool_result` | `any\|null` | Resultado devuelto por la herramienta |
| `created_at` | `datetime` | Timestamp del mensaje |

---

## Endpoint Herramientas

### Listar Herramientas Disponibles

**`GET /api/ai/tools`**

Retorna la lista de herramientas que el asistente IA puede utilizar para responder preguntas.

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
{
  "tools": [
    {
      "name": "get_sales_summary",
      "description": "Obtener resumen de ventas por período",
      "parameters": {
        "period": {
          "type": "string",
          "description": "Período: today, week, month, year",
          "required": false
        }
      }
    },
    {
      "name": "get_inventory_status",
      "description": "Consultar estado del inventario",
      "parameters": {
        "category_id": {
          "type": "integer",
          "description": "ID de categoría",
          "required": false
        }
      }
    },
    {
      "name": "search_products",
      "description": "Buscar productos por nombre o código",
      "parameters": {
        "query": {
          "type": "string",
          "description": "Término de búsqueda",
          "required": true
        }
      }
    }
  ]
}
```

---

## Ejemplo de Flujo Completo

### 1. Iniciar Conversación

```bash
curl -X POST http://localhost/api/ai/chat \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "message": "¿Cómo van las ventas este mes?",
    "stream": false
  }'
```

**Response:**

```json
{
  "response": "Este mes has registrado 156 ventas por un total de RD$ 523,450.00. Estás un 12% por encima del mes anterior.",
  "conversation_id": "550e8400-e29b-41d4-a716-446655440000",
  "tool_used": "get_sales_summary",
  "tool_result": {
    "total_ventas": 156,
    "total_ingresos": 523450.00,
    "variacion_porcentual": 12.0
  }
}
```

### 2. Continuar Conversación

```bash
curl -X POST http://localhost/api/ai/chat \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "message": "¿Y cuáles son los productos más vendidos?",
    "conversation_id": "550e8400-e29b-41d4-a716-446655440000",
    "stream": false
  }'
```

**Response:**

```json
{
  "response": "Los 3 productos más vendidos este mes son:\n1. Cerveza Corona 355ml - 320 unidades\n2. Arroz Premium 5kg - 150 unidades\n3. Aceite Vegetal 1L - 120 unidades",
  "conversation_id": "550e8400-e29b-41d4-a716-446655440000",
  "tool_used": "get_top_products",
  "tool_result": {
    "top_products": [
      {"name": "Cerveza Corona 355ml", "quantity": 320},
      {"name": "Arroz Premium 5kg", "quantity": 150},
      {"name": "Aceite Vegetal 1L", "quantity": 120}
    ]
  }
}
```

### 3. Streaming en Tiempo Real

```bash
curl -X POST http://localhost/api/ai/chat \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "message": "Dame un análisis detallado del inventario",
    "stream": true
  }'
```

**Response (SSE):**

```
data: {"type":"token","content":"Basándome"}

data: {"type":"token","content":" en"}

data: {"type":"token","content":" los"}

data: {"type":"token","content":" datos"}

data: {"type":"token","content":" actuales"}

data: {"type":"token","content:", "..."}

data: {"type":"done","conversation_id":"550e8400-e29b-41d4-a716-446655440000"}
```

### 4. Ver Historial

```bash
curl -X GET http://localhost/api/ai/conversations \
  -H "Authorization: Bearer {token}"
```

### 5. Ver Mensajes de una Conversación

```bash
curl -X GET http://localhost/api/ai/conversations/550e8400-e29b-41d4-a716-446655440000 \
  -H "Authorization: Bearer {token}"
```

---

## Notas

- Las **conversaciones** se limitan a las últimas 50 por usuario
- El **streaming** usa Server-Sent Events (SSE) con formato `data: {json}\n\n`
- El **conversation_id** es un UUID v4 que se genera automáticamente al iniciar una conversación
- Las **herramientas** permiten al asistente consultar datos reales del sistema (ventas, inventario, productos)
- Los **errores** del proveedor IA se loguean automáticamente para debugging
- El middleware `ai.chat.method` valida adicionalmente el método HTTP en el endpoint de chat
