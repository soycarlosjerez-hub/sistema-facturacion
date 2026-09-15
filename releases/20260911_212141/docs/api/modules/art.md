# Art Gallery API

Módulo de galería de arte/escultura: catálogo público, CRUD de obras, encargos, consignaciones, exhibiciones, certificados de autenticidad y reportes.

---

## Endpoint Resumen

### APIs Públicas (Sitio Web)

| Endpoint | Método | Ruta | Auth | Descripción |
|----------|--------|------|------|-------------|
| Catálogo | `GET` | `/api/art/catalog` | No | Listar obras disponibles con filtros |
| Obra | `GET` | `/api/art/catalog/{slug}` | No | Detalle de obra por slug |
| SEO JSON-LD | `GET` | `/api/art/catalog/{slug}/seo-jsonld` | No | Datos estructurados Schema.org |
| Exhibiciones | `GET` | `/api/art/exhibitions` | No | Listar exhibiciones activas |
| Exhibición | `GET` | `/api/art/exhibitions/{slug}` | No | Detalle de exhibición por slug |
| About | `GET` | `/api/art/about` | No | Info del artista |
| Contacto | `POST` | `/api/art/contact` | No | Formulario de contacto |
| Solicitud Cotización | `POST` | `/api/art/request-quote` | No | Solicitud de encargo |

### APIs Administrativas (Admin Panel)

| Endpoint | Método | Ruta | Auth | Descripción |
|----------|--------|------|------|-------------|
| Listar Obras | `GET` | `/api/art/obras` | Sí | CRUD de obras |
| Crear Obra | `POST` | `/api/art/obras` | Sí | Crear nueva obra |
| Ver Obra | `GET` | `/api/art/obras/{obra}` | Sí | Detalle de obra |
| Actualizar Obra | `PUT/PATCH` | `/api/art/obras/{obra}` | Sí | Actualizar obra |
| Eliminar Obra | `DELETE` | `/api/art/obras/{obra}` | Sí | Eliminar obra |
| Subir Fotos | `POST` | `/api/art/obras/{obra}/photos` | Sí | Subir fotos a obra |
| Eliminar Foto | `DELETE` | `/api/art/obras/{obra}/photos/{filename}` | Sí | Eliminar foto específica |
| Actualizar Estado | `PATCH` | `/api/art/obras/{obra}/status` | Sí | Cambiar estado de obra |
| Listar Encargos | `GET` | `/api/art/encargos` | Sí | CRUD de encargos |
| Crear Encargo | `POST` | `/api/art/encargos` | Sí | Crear nuevo encargo |
| Ver Encargo | `GET` | `/api/art/encargos/{encargo}` | Sí | Detalle de encargo |
| Actualizar Encargo | `PUT/PATCH` | `/api/art/encargos/{encargo}` | Sí | Actualizar encargo |
| Eliminar Encargo | `DELETE` | `/api/art/encargos/{encargo}` | Sí | Eliminar encargo |
| Actualizar Progreso | `PATCH` | `/api/art/encargos/{encargo}/progress` | Sí | Actualizar avance % |
| Subir Foto Progreso | `POST` | `/api/art/encargos/{encargo}/progress-photos` | Sí | Foto de avance |
| Listar Consignaciones | `GET` | `/api/art/consignaciones` | Sí | CRUD de consignaciones |
| Crear Consignación | `POST` | `/api/art/consignaciones` | Sí | Crear consignación |
| Ver Consignación | `GET` | `/api/art/consignaciones/{consignacion}` | Sí | Detalle de consignación |
| Actualizar Consignación | `PUT/PATCH` | `/api/art/consignaciones/{consignacion}` | Sí | Actualizar consignación |
| Eliminar Consignación | `DELETE` | `/api/art/consignaciones/{consignacion}` | Sí | Eliminar consignación |
| Listar Certificados | `GET` | `/api/art/certificates` | Sí | CRUD de certificados |
| Crear Certificado | `POST` | `/api/art/certificates` | Sí | Crear certificado con QR |
| Ver Certificado | `GET` | `/api/art/certificates/{certificate}` | Sí | Detalle de certificado |
| Actualizar Certificado | `PUT/PATCH` | `/api/art/certificates/{certificate}` | Sí | Actualizar certificado |
| Eliminar Certificado | `DELETE` | `/api/art/certificates/{certificate}` | Sí | Eliminar certificado |
| Listar Exhibiciones | `GET` | `/api/art/exhibitions` | Sí | CRUD de exhibiciones |
| Crear Exhibición | `POST` | `/api/art/exhibitions` | Sí | Crear exhibición |
| Ver Exhibición | `GET` | `/api/art/exhibitions/{exhibicion}` | Sí | Detalle de exhibición |
| Actualizar Exhibición | `PUT/PATCH` | `/api/art/exhibitions/{exhibicion}` | Sí | Actualizar exhibición |
| Eliminar Exhibición | `DELETE` | `/api/art/exhibitions/{exhibicion}` | Sí | Eliminar exhibición |
| Asignar Obras | `POST` | `/api/art/exhibitions/{exhibicion}/obras` | Sí | Asignar obras a exhibición |
| Remover Obra | `DELETE` | `/api/art/exhibitions/{exhibicion}/obras/{obra}` | Sí | Remover obra de exhibición |
| Resumen Ventas | `GET` | `/api/art/reports/sales-summary` | Sí | Dashboard de métricas |
| Estadísticas Catálogo | `GET` | `/api/art/reports/catalog-stats` | Sí | Estadísticas del catálogo |

---

## Autenticación

### APIs Públicas
No requieren autenticación. Accesibles desde el sitio web público.

### APIs Administrativas
Requieren middleware `api-auth` + `tenant`. Headers:

```
Authorization: Bearer {token}
Accept: application/json
```

### Permisos Requeridos

| Recurso | Permiso |
|---------|---------|
| Obras | `arte.obras.*` |
| Encargos | `arte.encargos.*` |
| Certificados | `arte.certificados.*` |
| Exhibiciones | `arte.exhibiciones.*` |
| Reportes | `arte.reportes.view` |

---

## APIs Públicas

### Catálogo de Obras

**`GET /api/art/catalog`**

Retorna obras disponibles/en exposición con filtros y paginación.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `per_page` | `integer` | Ítems por página (default: 12, max: 48) |
| `search` | `string` | Buscar por título |
| `medium` | `string` | Filtrar por material: `bronce`, `marmol`, `madera`, `hierro`, `mixed_media`, `arcilla`, `yeso`, `otros` |
| `technique` | `string` | Buscar por técnica |
| `year_from` | `integer` | Año mínimo de creación |
| `year_to` | `integer` | Año máximo de creación |
| `original_only` | `boolean` | Solo originales |
| `edition_only` | `boolean` | Solo ediciones |
| `sort` | `string` | Campo de orden (default: `created_at`) |
| `order` | `string` | `asc` o `desc` (default: `desc`) |

**Response `200 OK`:**

```json
{
  "data": [
    {
      "id": 1,
      "titulo": "El Pensador",
      "slug": "el-pensador",
      "medium": "bronce",
      "technique": "Fundición a cera perdida",
      "dimensiones": "180cm x 70cm x 80cm",
      "year_created": 2023,
      "is_original": true,
      "status": "disponible",
      "photos": ["storage/obras/1/photo1.jpg"],
      "precio": null
    }
  ],
  "links": []
}
```

---

### Detalle de Obra

**`GET /api/art/catalog/{slug}`**

Retorna obra completa por slug (disponible, en_exposicion, vendido, reservado, en_consulta).

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "titulo": "El Pensador",
    "slug": "el-pensador",
    "codigo_unico": "ESC-2023-001",
    "medium": "bronce",
    "technique": "Fundición a cera perdida",
    "dimensiones": "180cm x 70cm x 80cm",
    "peso_kg": 85.5,
    "year_created": 2023,
    "creation_date": "2023-06-15",
    "is_original": true,
    "status": "disponible",
    "condition_status": "excelente",
    "descripcion": "Escultura inspirada en la obra clásica...",
    "photos": ["storage/obras/1/photo1.jpg"],
    "certificate_number": "CERT-2023-001"
  }
}
```

---

### SEO JSON-LD

**`GET /api/art/catalog/{slug}/seo-jsonld`**

Retorna datos estructurados Schema.org para SEO.

**Headers:**

```
Accept: application/ld+json
```

**Response `200 OK`:**

```json
{
  "@context": "https://schema.org/",
  "@type": "CreativeWork",
  "name": "El Pensador",
  "creator": {
    "@type": "Person",
    "name": "Escultor"
  },
  "dateCreated": "2023-06-15",
  "artMedium": "Bronce",
  "artform": "Sculpture",
  "description": "Escultura inspirada en la obra clásica...",
  "dimensions": {
    "@type": "QuantitativeValue",
    "value": "180cm x 70cm x 80cm"
  },
  "image": ["storage/obras/1/photo1.jpg"],
  "offers": {
    "@type": "Offer",
    "availability": "https://schema.org/InStock",
    "priceCurrency": "DOP"
  }
}
```

---

### Exhibiciones Públicas

**`GET /api/art/exhibitions`**

Retorna exhibiciones activas y no finalizadas.

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `per_page` | `integer` | Ítems por página (default: 12, max: 48) |
| `tipo` | `string` | `individual` o `colectiva` |
| `search` | `string` | Buscar por título o lugar |

**Response `200 OK`:**

```json
{
  "data": [
    {
      "id": 1,
      "titulo": "Exposición Verano 2024",
      "slug": "exposicion-verano-2024",
      "lugar": "Galería Nacional",
      "tipo": "individual",
      "fecha_inicio": "2024-06-01",
      "fecha_fin": "2024-08-31",
      "obras_count": 12
    }
  ],
  "links": []
}
```

---

### Detalle de Exhibición

**`GET /api/art/exhibitions/{slug}`**

**Response `200 OK`:**

```json
{
  "data": {
    "id": 1,
    "titulo": "Exposición Verano 2024",
    "slug": "exposicion-verano-2024",
    "lugar": "Galería Nacional",
    "tipo": "individual",
    "fecha_inicio": "2024-06-01",
    "fecha_fin": "2024-08-31",
    "descripcion": "Muestra de esculturas en bronce...",
    "obras": [
      {
        "id": 1,
        "titulo": "El Pensador",
        "slug": "el-pensador",
        "medium": "bronce"
      }
    ]
  }
}
```

---

### About (Info del Artista)

**`GET /api/art/about`**

**Response `200 OK`:**

```json
{
  "data": {
    "artist_name": "Juan Escultor",
    "bio": "Escultor dominicano con 20 años de trayectoria...",
    "bio_short": "Escultor dominicano contemporáneo",
    "foto_perfil": "http://example.com/storage/foto.jpg",
    "biography_full": "Nacido en Santo Domingo...",
    "education": ["BFA - Universidad Autónoma de Santo Domingo"],
    "awards": ["Premio Nacional de Escultura 2020"],
    "social_media": {
      "instagram": "@escultor",
      "facebook": "escultor.art",
      "twitter": "@escultor_art",
      "youtube": "EscultorTV",
      "website": "https://escultor.com"
    },
    "contact_email": "info@escultor.com",
    "studio_location": "Santo Domingo, RD"
  }
}
```

---

### Formulario de Contacto

**`POST /api/art/contact`**

**Request Body:**

```json
{
  "nombre": "María García",
  "email": "maria@example.com",
  "telefono": "+1-809-555-1234",
  "asunto": "Interés en compra",
  "mensaje": "Me interesa la obra El Pensador, ¿tiene disponibilidad?"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | **Sí** | Nombre del remitente (max: 255) |
| `email` | `string` | **Sí** | Email del remitente |
| `telefono` | `string` | No | Teléfono de contacto (max: 20) |
| `asunto` | `string` | **Sí** | Asunto del mensaje (max: 255) |
| `mensaje` | `string` | **Sí** | Mensaje (min: 10, max: 2000) |

**Response `200 OK`:**

```json
{
  "message": "Mensaje enviado correctamente. Te responderemos pronto."
}
```

---

### Solicitud de Cotización

**`POST /api/art/request-quote`**

**Request Body:**

```json
{
  "nombre": "María García",
  "email": "maria@example.com",
  "telefono": "+1-809-555-1234",
  "titulo_encargo": "Escultura personalizada",
  "descripcion": "Me gustaría una escultura de mi familia de aproximadamente 50cm...",
  "presupuesto_estimado": 15000.00,
  "fecha_deseada": "2024-12-01",
  "como_conoce": "redes_sociales"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `nombre` | `string` | **Sí** | Nombre completo (max: 255) |
| `email` | `string` | **Sí** | Email de contacto |
| `telefono` | `string` | **Sí** | Teléfono (max: 20) |
| `titulo_encargo` | `string` | **Sí** | Título del encargo (max: 255) |
| `descripcion` | `string` | **Sí** | Descripción detallada (min: 20, max: 2000) |
| `presupuesto_estimado` | `decimal` | No | Presupuesto en DOP (≥ 0) |
| `fecha_deseada` | `date` | No | Fecha deseada de entrega (futuro) |
| `como_conoce` | `string` | No | Origen: `redes_sociales`, `google`, `referencia`, `galeria`, `evento`, `otro` |

**Response `200 OK`:**

```json
{
  "message": "Solicitud enviada correctamente. Nos pondremos en contacto contigo pronto."
}
```

---

## APIs Administrativas — Obras

### Listar Obras

**`GET /api/art/obras`**

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `status` | `string` | Filtrar por estado |
| `medium` | `string` | Filtrar por material |
| `technique` | `string` | Buscar por técnica |
| `search` | `string` | Buscar por título, código o material |
| `is_original` | `boolean` | Filtrar originales/ediciones |
| `condition` | `string` | Filtrar por condición |
| `has_certificate` | `boolean` | Filtrar con/sin certificado |
| `year_from` | `integer` | Año mínimo |
| `year_to` | `integer` | Año máximo |
| `sort_by` | `string` | Campo de orden (default: `created_at`) |
| `sort_order` | `string` | `asc` o `desc` (default: `desc`) |

**Response `200 OK`:**

```json
{
  "data": [
    {
      "id": 1,
      "titulo": "El Pensador",
      "codigo_unico": "ESC-2023-001",
      "medium": "bronce",
      "status": "disponible",
      "is_original": true,
      "condition_status": "excelente",
      "certificate_number": "CERT-2023-001",
      "categoria": { "id": 1, "nombre": "Esculturas" }
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 42
  }
}
```

---

### Crear Obra

**`POST /api/art/obras`**

**Request Body:**

```json
{
  "titulo": "El Pensador",
  "codigo_unico": "ESC-2023-001",
  "dimensiones": "180cm x 70cm x 80cm",
  "peso_kg": 85.5,
  "medium": "bronce",
  "technique": "Fundición a cera perdida",
  "year_created": 2023,
  "edition_number": null,
  "edition_total": null,
  "certificate_number": "CERT-2023-001",
  "condition_status": "excelente",
  "creation_date": "2023-06-15",
  "exhibition_history": ["Exposición Verano 2024"],
  "is_original": true,
  "status": "disponible",
  "cost_materials": 5000.00,
  "categoria_id": 1
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `titulo` | `string` | **Sí** | Título de la obra (max: 255) |
| `codigo_unico` | `string` | No | Código único (max: 50, único) |
| `dimensiones` | `string` | No | Dimensiones (max: 100) |
| `peso_kg` | `decimal` | No | Peso en kg (≥ 0) |
| `medium` | `string` | **Sí** | Material: `bronce`, `marmol`, `madera`, `hierro`, `mixed_media`, `arcilla`, `yeso`, `otros` |
| `technique` | `string` | No | Técnica utilizada (max: 100) |
| `year_created` | `integer` | No | Año de creación (1900 - año actual+1) |
| `edition_number` | `integer` | No | Número de edición (≥ 1) |
| `edition_total` | `integer` | No | Total de ediciones (requerido si `edition_number` existe) |
| `certificate_number` | `string` | No | Número de certificado (max: 100) |
| `condition_status` | `string` | **Sí** | Condición: `excelente`, `bueno`, `regular`, `necesita_restauracion` |
| `creation_date` | `date` | No | Fecha de creación |
| `exhibition_history` | `array` | No | Historial de exhibiciones |
| `is_original` | `boolean` | **Sí** | `true` = original, `false` = edición |
| `status` | `string` | **Sí** | Estado: `disponible`, `vendido`, `reservado`, `en_consulta`, `en_exposicion`, `en_consignacion` |
| `cost_materials` | `decimal` | No | Costo de materiales (≥ 0) |
| `categoria_id` | `integer` | No | ID de categoría |

**Validations:**

```
titulo: required|string|max:255
medium: required|in:bronce,marmol,madera,hierro,mixed_media,arcilla,yeso,otros
condition_status: required|in:excelente,bueno,regular,necesita_restauracion
is_original: required|boolean
status: required|in:disponible,vendido,reservado,en_consulta,en_exposicion,en_consignacion
```

**Response `201 Created`:**

```json
{
  "data": {
    "id": 1,
    "titulo": "El Pensador",
    "codigo_unico": "ESC-2023-001",
    "medium": "bronce",
    "status": "disponible",
    "categoria": { "id": 1, "nombre": "Esculturas" }
  }
}
```

---

### Subir Fotos a Obra

**`POST /api/art/obras/{obra}/photos`**

**Headers:**

```
Content-Type: multipart/form-data
```

**Form Data:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `photos` | `file[]` | **Sí** | Array de archivos imagen (jpg, jpeg, png, webp; max: 5MB c/u) |

**Response `200 OK`:**

```json
{
  "message": "Fotos subidas correctamente.",
  "photos": [
    "http://example.com/storage/obras/1/photo1.jpg",
    "http://example.com/storage/obras/1/photo2.jpg"
  ]
}
```

---

### Eliminar Foto de Obra

**`DELETE /api/art/obras/{obra}/photos/{filename}`**

**Response `200 OK`:**

```json
{
  "message": "Foto eliminada correctamente."
}
```

---

### Actualizar Estado de Obra

**`PATCH /api/art/obras/{obra}/status`**

**Request Body:**

```json
{
  "status": "vendido"
}
```

**Validación:**

```
status: required|in:disponible,vendido,reservado,en_consulta,en_exposicion,en_consignacion
```

**Response `200 OK`:**

```json
{
  "message": "Estado de la obra actualizado a 'Vendido'.",
  "data": {
    "id": 1,
    "titulo": "El Pensador",
    "status": "vendido"
  }
}
```

---

## APIs Administrativas — Encargos

### Listar Encargos

**`GET /api/art/encargos`**

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `status` | `string` | Filtrar por estado |
| `cliente_id` | `integer` | Filtrar por cliente |
| `search` | `string` | Buscar por título o nombre de cliente |
| `active_only` | `boolean` | Solo encargos no completados/cancelados |
| `overdue_only` | `boolean` | Solo encargos vencidos |
| `sort_by` | `string` | Campo de orden (default: `created_at`) |
| `sort_order` | `string` | `asc` o `desc` (default: `desc`) |

**Response `200 OK`:**

```json
{
  "data": [
    {
      "id": 1,
      "titulo": "Escultura para Hotel",
      "status": "progreso",
      "avance_porcentaje": 65,
      "precio_total": 25000.00,
      "deposito": 12500.00,
      "saldo": 12500.00,
      "estimated_completion": "2024-06-15",
      "cliente": { "id": 5, "nombre": "Hotel Paradiso" },
      "user": { "id": 2, "name": "Admin" }
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 8
  }
}
```

---

### Crear Encargo

**`POST /api/art/encargos`**

**Request Body:**

```json
{
  "cliente_id": 5,
  "titulo": "Escultura para Hotel",
  "descripcion": "Escultura de 2 metros para el lobby del hotel",
  "precio_total": 25000.00,
  "deposito": 12500.00,
  "estimated_completion": "2024-06-15",
  "notas": "Cliente prefiere bronce pulido"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `cliente_id` | `integer` | **Sí** | ID de cliente (existe) |
| `titulo` | `string` | **Sí** | Título del encargo (max: 255) |
| `descripcion` | `string` | No | Descripción detallada |
| `precio_total` | `decimal` | **Sí** | Precio total (≥ 0) |
| `deposito` | `decimal` | No | Depósito inicial (≥ 0) |
| `estimated_completion` | `date` | No | Fecha estimada de entrega |
| `notas` | `string` | No | Notas adicionales |

**Nota:** El campo `saldo` se calcula automáticamente: `precio_total - deposito`.

**Response `201 Created`:**

```json
{
  "data": {
    "id": 1,
    "titulo": "Escultura para Hotel",
    "status": "solicitado",
    "precio_total": 25000.00,
    "deposito": 12500.00,
    "saldo": 12500.00,
    "cliente": { "id": 5, "nombre": "Hotel Paradiso" }
  }
}
```

---

### Actualizar Progreso de Encargo

**`PATCH /api/art/encargos/{encargo}/progress`**

**Request Body:**

```json
{
  "avance_porcentaje": 75,
  "actual_completion": "2024-05-20",
  "notas": "Fase de pulido completada"
}
```

**Campos:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `avance_porcentaje` | `integer` | Porcentaje de avance (0-100). Si ≥ 100 → status = `completado`. Si ≥ 60 → status = `progreso` |
| `actual_completion` | `date` | Fecha real de completado |
| `notas` | `string` | Notas de progreso |

**Response `200 OK`:**

```json
{
  "message": "Avance del encargo actualizado.",
  "data": {
    "id": 1,
    "titulo": "Escultura para Hotel",
    "status": "progreso",
    "avance_porcentaje": 75,
    "cliente": { "id": 5, "nombre": "Hotel Paradiso" }
  }
}
```

---

### Subir Foto de Progreso

**`POST /api/art/encargos/{encargo}/progress-photos`**

**Form Data:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `photo` | `file` | **Sí** | Imagen (jpg, jpeg, png, webp; max: 5MB) |

**Response `200 OK`:**

```json
{
  "message": "Foto de progreso subida correctamente.",
  "progress_photo": "http://example.com/storage/encargos/1/photo1.jpg"
}
```

---

## APIs Administrativas — Consignaciones

### Listar Consignaciones

**`GET /api/art/consignaciones`**

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `estado` | `string` | Filtrar por estado: `activa`, `vendida`, `devuelta` |
| `galeria` | `string` | Buscar por nombre de galería |
| `search` | `string` | Buscar por galería u obra |
| `expired_only` | `boolean` | Solo consignaciones expiradas |
| `active_only` | `boolean` | Solo consignaciones activas |

**Response `200 OK`:**

```json
{
  "data": [
    {
      "id": 1,
      "galeria_nombre": "Galería Arte Moderno",
      "obra": { "id": 1, "titulo": "El Pensador" },
      "fecha_inicio": "2024-01-15",
      "fecha_fin": "2024-06-15",
      "comision_percentage": 30,
      "estado": "activa"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 5
  }
}
```

---

### Crear Consignación

**`POST /api/art/consignaciones`**

**Request Body:**

```json
{
  "galeria_nombre": "Galería Arte Moderno",
  "obra_id": 1,
  "fecha_inicio": "2024-01-15",
  "fecha_fin": "2024-06-15",
  "comision_percentage": 30,
  "notas": "Consignación por 6 meses"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `galeria_nombre` | `string` | **Sí** | Nombre de la galería (max: 255) |
| `obra_id` | `integer` | **Sí** | ID de obra (existe) |
| `fecha_inicio` | `date` | **Sí** | Fecha de inicio |
| `fecha_fin` | `date` | No | Fecha de fin (≥ fecha_inicio) |
| `comision_percentage` | `decimal` | No | % de comisión (0-100) |
| `notas` | `string` | No | Notas |

**Response `201 Created`:**

```json
{
  "data": {
    "id": 1,
    "galeria_nombre": "Galería Arte Moderno",
    "obra": { "id": 1, "titulo": "El Pensador" },
    "estado": "activa",
    "comision_percentage": 30
  }
}
```

---

### Actualizar Consignación

**`PUT/PATCH /api/art/consignaciones/{consignacion}`**

Al cambiar `estado` a `vendida`, se calcula automáticamente `comision_monto`:

```
comision_monto = (precio_venta * comision_percentage) / 100
```

**Request Body:**

```json
{
  "estado": "vendida",
  "fecha_venta": "2024-03-15",
  "precio_venta": 18000.00,
  "pago_recibido": true,
  "pago_fecha": "2024-03-20"
}
```

**Campos adicionales para venta:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `estado` | `string` | `activa`, `vendida`, `devuelta` |
| `fecha_venta` | `date` | Fecha de venta |
| `precio_venta` | `decimal` | Precio de venta (≥ 0) |
| `comision_monto` | `decimal` | Calculado automáticamente |
| `pago_recibido` | `boolean` | Si el pago fue recibido |
| `pago_fecha` | `date` | Fecha de pago |

---

## APIs Administrativas — Certificados

### Listar Certificados

**`GET /api/art/certificates`**

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `obra_id` | `integer` | Filtrar por obra |
| `number` | `string` | Buscar por número de certificado |

**Response `200 OK`:**

```json
{
  "data": [
    {
      "id": 1,
      "numero_certificado": "CERT-2023-001",
      "obra": { "id": 1, "titulo": "El Pensador" },
      "firmado_en": "2023-07-01",
      "qr_code": "certs/qr-CERT-2023-001.png",
      "expirado": false
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 12
  }
}
```

---

### Crear Certificado

**`POST /api/art/certificates`**

Genera automáticamente un código QR que enlaza a la página pública de la obra.

**Request Body:**

```json
{
  "obra_id": 1,
  "numero_certificado": "CERT-2023-001",
  "firmado_en": "2023-07-01"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `obra_id` | `integer` | **Sí** | ID de obra (existe) |
| `numero_certificado` | `string` | **Sí** | Número único (max: 100, único) |
| `firmado_en` | `date` | No | Fecha de firma |

**Response `201 Created`:**

```json
{
  "data": {
    "id": 1,
    "numero_certificado": "CERT-2023-001",
    "obra": { "id": 1, "titulo": "El Pensador", "slug": "el-pensador" },
    "firmado_en": "2023-07-01",
    "qr_code": "certs/qr-CERT-2023-001.png",
    "expirado": false
  }
}
```

---

## APIs Administrativas — Exhibiciones

### Listar Exhibiciones

**`GET /api/art/exhibitions`**

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `activo` | `boolean` | Filtrar por activo/inactivo |
| `tipo` | `string` | `individual` o `colectiva` |
| `search` | `string` | Buscar por título o lugar |
| `past_only` | `boolean` | Solo exhibiciones finalizadas |
| `future_only` | `boolean` | Solo exhibiciones activas/futuras |

**Response `200 OK`:**

```json
{
  "data": [
    {
      "id": 1,
      "titulo": "Exposición Verano 2024",
      "lugar": "Galería Nacional",
      "tipo": "individual",
      "fecha_inicio": "2024-06-01",
      "fecha_fin": "2024-08-31",
      "activo": true,
      "obras_count": 12
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 6
  }
}
```

---

### Crear Exhibición

**`POST /api/art/exhibitions`**

**Request Body:**

```json
{
  "titulo": "Exposición Verano 2024",
  "lugar": "Galería Nacional",
  "fecha_inicio": "2024-06-01",
  "fecha_fin": "2024-08-31",
  "descripcion": "Muestra de esculturas en bronce y mármol",
  "tipo": "individual",
  "activo": true,
  "featured_image": "exhibitions/verano2024.jpg"
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `titulo` | `string` | **Sí** | Título (max: 255) |
| `lugar` | `string` | **Sí** | Lugar (max: 255) |
| `fecha_inicio` | `date` | **Sí** | Fecha de inicio |
| `fecha_fin` | `date` | No | Fecha de fin (≥ fecha_inicio) |
| `descripcion` | `string` | No | Descripción |
| `tipo` | `string` | **Sí** | `individual` o `colectiva` |
| `activo` | `boolean` | No | Default: true |
| `featured_image` | `string` | No | Imagen destacada |

**Response `201 Created`:**

```json
{
  "data": {
    "id": 1,
    "titulo": "Exposición Verano 2024",
    "lugar": "Galería Nacional",
    "tipo": "individual",
    "activo": true,
    "obras_count": 0
  }
}
```

---

### Asignar Obras a Exhibición

**`POST /api/art/exhibitions/{exhibicion}/obras`**

**Request Body:**

```json
{
  "obra_ids": [1, 3, 5, 7]
}
```

**Campos:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `obra_ids` | `integer[]` | **Sí** | Array de IDs de obras (min: 1) |

**Response `200 OK`:**

```json
{
  "message": "Obras asignadas a la exhibicion correctamente.",
  "assigned_count": 4
}
```

---

### Remover Obra de Exhibición

**`DELETE /api/art/exhibitions/{exhibicion}/obras/{obra}`**

**Response `200 OK`:**

```json
{
  "message": "Obra removida de la exhibicion correctamente."
}
```

---

## APIs Administrativas — Reportes

### Resumen de Ventas

**`GET /api/art/reports/sales-summary`**

**Query Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `period` | `string` | `week`, `month`, `quarter`, `year`, `custom` (default: `month`) |
| `start_date` | `date` | Fecha inicio (requerido si `period=custom`) |
| `end_date` | `date` | Fecha fin (requerido si `period=custom`) |

**Headers:**

```
Authorization: Bearer {token}
Accept: application/json
```

**Response `200 OK`:**

```json
{
  "data": {
    "total_ventas": 8,
    "total_ingresos": 125000.00,
    "promedio_venta": 15625.00,
    "obras_disponibles": 34,
    "obras_vendidas_mes": 8,
    "encargos_activos": 5,
    "valor_encargos_pendientes": 62500.00,
    "consignaciones_activas": 3,
    "top_mediums": [
      { "medium": "bronce", "count": 5, "total_sales": 0 },
      { "medium": "marmol", "count": 3, "total_sales": 0 }
    ],
    "estadisticas_periodo": {
      "mes_actual": {
        "ventas": 8,
        "ingresos": 125000.00
      },
      "mes_anterior": {
        "ventas": 6,
        "ingresos": 0
      },
      "variacion_porcentual": 33.3
    }
  }
}
```

---

### Estadísticas del Catálogo

**`GET /api/art/reports/catalog-stats`**

**Response `200 OK`:**

```json
{
  "data": {
    "total_obras": 56,
    "por_status": {
      "disponible": 34,
      "vendido": 12,
      "en_exposicion": 6,
      "reservado": 3,
      "en_consulta": 1
    },
    "por_medium": {
      "bronce": 22,
      "marmol": 15,
      "madera": 8,
      "hierro": 6,
      "otros": 5
    },
    "obras_con_certificado": 18,
    "obras_originales": 40,
    "ediciones": 16,
    "valor_inventario": 385000.00
  }
}
```

---

## Notas

- Los **slugs** del catálogo público se generan automáticamente a partir del título
- El **QR** de certificados enlaza a la URL pública de la obra (`/art/{slug}`)
- El **JSON-LD** sigue el estándar Schema.org `CreativeWork` para SEO
- Las **fotos** se almacenan en `storage/app/public/obras/{id}/`
- Las **fotos de progreso** se almacenan en `storage/app/public/encargos/{id}/`
- El **saldo** de encargos se calcula automáticamente: `precio_total - deposito`
- La **comisión** de consignaciones se calcula automáticamente al marcar como `vendida`
- El middleware `ai` protege las rutas de IA verificando autenticación e instancia
