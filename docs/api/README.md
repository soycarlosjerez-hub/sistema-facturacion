# Sistema Facturación — REST API Reference

> **Version:** 1.0 · **Base URL:** `https://tu-dominio.com/api` · **Format:** JSON

---

## Quick Start

```bash
# 1. Obtén tu token de autenticación (ver [Authentication](./authentication))
# 2. Haz tu primera petición

curl -X GET "https://tu-dominio.com/api/products?page=1" \
  -H "Authorization: Bearer TU_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

---

## Autenticación

| Método | Prefijo Token | Modelo Autenticado | Alcance |
|--------|---------------|--------------------|---------|
| [Instance API Key](./authentication#instance-api-key) | `iak_` | `User` | Tenant aislado |
| [Sanctum PAT](./authentication#sanctum-personal-access-token) | Ninguno | `User` | Tenant aislado |
| [Client API Token](./authentication#client-api-token) | Ninguno | `Cliente` | Por `cliente_id` |

Consulta la guía completa en **[Authentication Guide](./authentication)**.

---

## Formato de Respuestas

Todas las respuestas son JSON. Las colecciones usan paginación estándar con metadatos.

```json
{
  "data": [ /* recursos */ ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 73
  },
  "links": {
    "first": "?page=1",
    "last": "?page=5",
    "prev": null,
    "next": "?page=2"
  }
}
```

Consulta el detalle en **[Response Format](./response-format)**.

---

## Código de Estado HTTP

| Código | Significado |
|--------|-------------|
| `200` | Éxito — La operación se completó correctamente |
| `201` | Creado — El recurso fue creado exitosamente |
| `400` | Bad Request — Petición mal formada |
| `401` | Unauthorized — Token ausente, inválido o expirado |
| `403` | Forbidden — Token válido pero sin permiso para esta acción |
| `404` | Not Found — El recurso solicitado no existe |
| `422` | Unprocessable Entity — Errores de validación |
| `500` | Internal Server Error — Error inesperado del servidor |

---

## Módulos de la API

### 🏢 Core

| Módulo | Endpoints | Descripción |
|--------|-----------|-------------|
| [Categories](./modules/categories) | 8 | Categorías de productos con tipos de negocio asociados |
| [Business Types](./modules/business-types) | 5 | Tipos de negocio (restaurante, tienda, etc.) con módulos |
| [Instances](./modules/instances) | 6 | Instancias de negocio multi-tenant |
| [Users](./modules/users) | 6 | Gestión de usuarios internos con roles |
| [Products](./modules/products) | 5 | Catálogo de productos con stock e ingredientes |

### 💰 Ventas

| Módulo | Endpoints | Descripción |
|--------|-----------|-------------|
| [Sales](./modules/sales) | 6 | Ventas con NCF, comprobantes fiscales y pagos |
| [Customers](./modules/customers) | 5 | Clientes con datos fiscales dominicanos |
| [Quotes](./modules/quotes) | 5 | Cotizaciones convertibles en ventas |
| [Returns](./modules/returns) | 5 | Devoluciones vinculadas a ventas originales |

### 📦 Compras

| Módulo | Endpoints | Descripción |
|--------|-----------|-------------|
| [Purchases](./modules/purchases) | 5 | Órdenes de compra con retenciones ISR/ITbis |
| [Suppliers](./modules/suppliers) | 5 | Proveedores y su historial de compras |

### ⚙️ Operaciones

| Módulo | Endpoints | Descripción |
|--------|-----------|-------------|
| [Branches](./modules/branches) | 5 | Sucursales de la empresa |
| [Cash Registers](./modules/cash-registers) | 5 | Cajas registradoras y sesiones |
| [Tables](./modules/tables) | 5 | Mesas para restaurantes |
| [Warehouses](./modules/warehouses) | 5 | Almacenes y gestión de inventario |
| [Terminals](./modules/terminals) | 5 | Terminales POS |

### 🔤 Tipos de Referencia

| Módulo | Endpoints | Descripción |
|--------|-----------|-------------|
| [Sale Types](./modules/sale-types) | 5 | Tipos de venta (contado, crédito, etc.) |
| [Purchase Types](./modules/purchase-types) | 5 | Tipos de compra |

### 🍽️ Restaurante

| Módulo | Endpoints | Descripción |
|--------|-----------|-------------|
| [Reservations](./modules/reservations) | 5 | Reservaciones de mesas con notificaciones |
| [Orders](./modules/orders) | 9 | Órdenes de cocina con pagos y detalles |
| [KDS](./modules/kds) | 2 | Kitchen Display System para cocina |

### 🛎️ Servicios

| Módulo | Endpoints | Descripción |
|--------|-----------|-------------|
| [Rentals](./modules/rentals) | 5 | Alquiler de vehículos/equipos |
| [Delivery](./modules/delivery) | 5 | Registros de entrega/distribución |
| [Laundry](./modules/laundry) | 5 | Servicio de lavado de vehículos |
| [Price Lists](./modules/price-lists) | 5 | Listas de precios por sucursal |

### 🔧 Configuración

| Módulo | Endpoints | Descripción |
|--------|-----------|-------------|
| [Backups](./modules/backups) | 4 | Historial de respaldos del sistema |
| [Payment Processors](./modules/payment-processors) | 5 | Pasarelas de pago configuradas |
| [Printers](./modules/printers) | 5 | Impresoras térmicas configuradas |
| [NCF Sequences](./modules/ncfs) | 5 | Secuencias NCF (facturación fiscal RD) |
| [Settings](./modules/settings) | 5 | Configuración general del sistema |

### 📊 Reportes

| Módulo | Endpoints | Descripción |
|--------|-----------|-------------|
| [Reports](./modules/reports) | 4 | Dashboard, top productos, top clientes, inventario bajo |
| [Audit Logs](./modules/audit-logs) | 1 | Registro de auditoría de acciones |

### 👤 Portal del Cliente

| Módulo | Endpoints | Descripción |
|--------|-----------|-------------|
| [Client Auth](./modules/client-auth) | 6 | Registro, login, recuperación de contraseña |
| [Client API](./modules/client-api) | 6 | Perfil, pedidos, cambio de contraseña |

---

## Filtros Universales

La mayoría de los endpoints `GET /api/{recursos}` aceptan estos parámetros:

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | `integer` | Número de página (default: 1) |
| `per_page` | `integer` | Ítems por página (default: 15, máx: 100) |
| `search` | `string` | Búsqueda genérica en campos principales |

Filtros específicos se documentan en cada módulo.

---

## Soporte

- **Issues:** [GitHub Issues](https://github.com/tu-repo/issues)
- **Email:** soporte@tu-dominio.com
