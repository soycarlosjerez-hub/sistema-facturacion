---
name: premium-ui
description: Use when the user says "Aplica el UI premium al módulo de [nombre]" or similar. Applies the premium glassmorphism UI pattern with animated gradient headers, floating bubbles, frosted glass cards, and accent color strips to any Laravel Blade module views. Trigger keywords: "ui premium", "premium ui", "aplica premium", "interfaz premium", "diseño premium".
---

# UI System v2 — Skill de Implementación

## Trigger

Cuando el usuario diga **cualquiera** de estas frases:
- "Aplica el UI premium al módulo de [nombre]"
- "Pon la interfaz premium en [nombre]"
- "Diseña [nombre] con el UI premium"
- "Haz que [nombre] se vea como el profile"

**Acción**: Implementar el sistema UI v2 en TODAS las vistas del módulo indicado.

---

## Partial Location

```
resources/views/partials/premium-ui.blade.php
```

## Cómo Incluirlo

En CADA vista, agregar dentro de `@push('styles')`:

```blade
@push('styles')
@include('partials.premium-ui')
@endpush
```

**NUNCA** duplicar el CSS del partial. Siempre usar `@include`.

---

## Sistema de Variables CSS

Cada módulo define su **acento** en el wrapper principal `.ui-page`:

```html
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">
```

| Variable | Ejemplo (Gastos) | Descripción |
|---|---|---|
| `--accent` | `#10b981` | Color principal del módulo |
| `--accent-rgb` | `16, 185, 129` | RGB para rgba() en sombras |
| `--accent-hover` | `#059669` | Variante hover más oscura |

### Colores por Módulo

| Módulo | Color | --accent | --accent-rgb | --accent-hover |
|---|---|---|---|---|
| Gastos | Verde | `#10b981` | `16,185,129` | `#059669` |
| Clientes | Verde | `#10b981` | `16,185,129` | `#059669` |
| Ventas | Azul | `#3b82f6` | `59,130,246` | `#2563eb` |
| Productos | Índigo | `#6366f1` | `99,102,241` | `#4f46e5` |
| Compras | Ámbar | `#f59e0b` | `245,158,11` | `#d97706` |
| Categorías | Rosa | `#ec4899` | `236,72,153` | `#db2777` |
| Cajas | Púrpura | `#8b5cf6` | `139,92,246` | `#7c3aed` |
| Listas Precio | Púrpura | `#8b5cf6` | `139,92,246` | `#7c3aed` |
| Alquileres | Ámbar | `#f59e0b` | `245,158,11` | `#d97706` |
| Cotizaciones | Índigo | `#6366f1` | `99,102,241` | `#4f46e5` |

---

## Referencia Completa de Clases `.ui-*`

### Layout

| Clase | Descripción |
|---|---|
| `.ui-page` | Wrapper principal con padding estandarizado y animación slide-up |
| `.ui-header` | Header con gradiente animado + 3 burbujas flotantes |
| `.ui-header-body` | Flex container dentro del header (left + actions) |
| `.ui-header-left` | Lado izquierdo (avatar + título + meta) |
| `.ui-header-title` | Título del header |
| `.ui-header-meta` | Subtítulo del header |
| `.ui-header-actions` | Lado derecho (botones) |
| `.ui-avatar-circle` | Avatar circular dentro del header (56px) |

### Cards

| Clase | Descripción |
|---|---|
| `.ui-card` | Card glassmorphism con hover suave |
| `.ui-card-accent` | Franja de color superior (usa `--accent`) |
| `.ui-card-body` | Padding interior estandarizado |
| `.ui-card-title` | Título con icono (usa `--accent` para el icono) |
| `.ui-card-subtitle` | Subtítulo gris |

### Stat Cards

| Clase | Descripción |
|---|---|
| `.ui-stat` | Card de estadísticas |
| `.ui-stat-body` | Contenedor del contenido |
| `.ui-stat-value` | Valor numérico grande (usa `--accent`) |
| `.ui-stat-label` | Label en mayúsculas |
| `.ui-stat-sub` | Texto secundario |

### Botones

| Clase | Descripción |
|---|---|
| `.ui-btn` | Botón base |
| `.ui-btn-primary` | Glass transparente (para headers, fondo blanco) |
| `.ui-btn-solid` | Relleno con gradiente del acento |
| `.ui-btn-danger` | Relleno rojo |
| `.ui-btn-ghost` | Outline gris claro |
| `.ui-btn-link` | Link sin bordes |
| `.ui-btn-sm` | Tamaño pequeño |
| `.ui-btn-lg` | Tamaño grande |
| `.ui-btn-pill` | Border-radius pill |

### Acciones (botones de icono cuadrado)

| Clase | Descripción |
|---|---|
| `.ui-action` | Base (34x34px, border-radius .5rem) |
| `.ui-action-view` | Azul — Ver detalle |
| `.ui-action-edit` | Ámbar — Editar |
| `.ui-action-delete` | Rojo — Eliminar |
| `.ui-action-print` | Gris — Imprimir/Exportar |

### Formularios

| Clase | Descripción |
|---|---|
| `.ui-label` | Label del campo |
| `.ui-input` | Input de texto/number/date |
| `.ui-select` | Select desplegable |
| `.ui-textarea` | Textarea |
| `.ui-input-group` | Contenedor de grupo input+addon |
| `.ui-input-group-text` | Texto/icono del addon |
| `.ui-input-group-lg` | Input group tamaño grande |
| `.ui-select-sm` | Select tamaño pequeño |

### Form Section Headers (divisores de sección)

| Estilo | Descripción |
|---|---|
| `border-bottom` + `mb-4 pb-3` | Divisor de sección con borde inferior |
| `h6.fw-bold.mb-0` con `style="color:COLOR"` | Título de sección con color de acento |
| `i.bi.bi-ICON.me-2` | Icono alineado con el título |

### Alertas

| Clase/Estilo | Descripción |
|---|---|
| `.alert.alert-danger.rounded-4.shadow-sm.border-0` con `style="border-left:4px solid #dc3545 !important"` | Alerta de error con barra roja izquierda |
| `.alert.rounded-4.shadow-sm.border-0` con `background:rgba(ACCENT_RGB,.05);border-left:4px solid ACCENT` | Banner informativo con icono circular y barra de acento |

### Detail Cards (show view)

| Patrón | Descripción |
|---|---|
| `.p-3.rounded-3` con `background:rgba(ACCENT_RGB,.05)` | Tarjeta de dato con fondo tenue del acento |
| `small.text-muted.text-uppercase.d-block.small.fw-semibold` | Label en mayúsculas |
| `span.fs-4.fw-bold` con `style="color:ACCENT"` | Valor destacado grande |

### Drop Zone (importar archivos)

| Clase | Descripción |
|---|---|
| `.drop-zone` | Zona de arrastre con borde dashed y padding 3rem |
| `.drop-zone:hover`, `.drop-zone.dragover` | Hover con cambio de color de borde al acento |
| `.drop-zone.has-file` | Estado con archivo seleccionado (borde verde) |

### Toggle / Switch

| Elemento | Descripción |
|---|---|
| `.form-check.form-switch` + `input.form-check-input[role="switch"]` | Toggle on/off con estilo Bootstrap |
| `style="width:3em;height:1.5em"` | Tamaño personalizado del switch |

### Tablas

| Clase | Descripción |
|---|---|
| `.ui-table` | Tabla con thead uppercase + hover row |

### Badges

| Clase | Descripción |
|---|---|
| `.ui-badge-success` | Verde — Activo, Pagado, Completado |
| `.ui-badge-warning` | Ámbar — Pendiente, Por Pagar |
| `.ui-badge-danger` | Rojo — Inactivo, Cancelado, Vencido |
| `.ui-badge-info` | Azul — Información, Comprobante |
| `.ui-badge-neutral` | Gris — Neutral, Sin estado |
| `.ui-badge-primary` | Color del acento del módulo |

### Vistas de Detalle

| Clase | Descripción |
|---|---|
| `.ui-detail-row` | Fila flex (label + value) |
| `.ui-detail-label` | Label gris (180px) |
| `.ui-detail-value` | Valor oscuro |

### Sticky Bar

| Clase | Descripción |
|---|---|
| `.ui-sticky-bar` | Barra fija inferior (usa `--accent` para border-top) |
| `.ui-sticky-bar-inner` | Flex container justify-end |

### Otros

| Clase | Descripción |
|---|---|
| `.ui-user-avatar` | Avatar circular grande (72px) |
| `.ui-user-avatar-sm` | Avatar pequeño (48px) |
| `.ui-user-avatar-lg` | Avatar grande (96px) |
| `.ui-user-avatar-amber` | Variante ámbar |
| `.ui-user-avatar-green` | Variante verde (usa `--accent`) |
| `.ui-user-avatar-blue` | Variante azul |
| `.ui-empty-state` | Estado vacío centrado con icono |

### Animación

| Atributo | Uso |
|---|---|
| `style="--delay: 0.1s"` | Reemplaza `animation-delay: 0.1s` |

---

## Templates HTML Listos para Copiar

### Template: Page Wrapper

```blade
<div class="ui-page" style="--accent:COLOR;--accent-rgb:R,G,B;--accent-hover:HOVER;">
```

### Template: Header

```html
<div class="ui-header mb-4" style="--delay:0s">
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="ui-header-body">
        <div class="ui-header-left">
            <div class="ui-avatar-circle">
                <i class="bi bi-[ICON]"></i>
            </div>
            <div>
                <h4 class="ui-header-title">[TITULO]</h4>
                <div class="ui-header-meta">
                    <i class="bi bi-[ICON] me-1"></i>[SUBTITULO]
                    <span class="divider">·</span>
                    <i class="bi bi-list-ul me-1"></i>
                    <span>{{ $data->total() }} registro(s)</span>
                </div>
            </div>
        </div>
        <div class="ui-header-actions">
            @can('[permiso].create')
            <a href="{{ route('[mod].create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                <i class="bi bi-plus-lg me-1"></i> Nuevo [Elemento]
            </a>
            @endcan
        </div>
    </div>
</div>
```

### Template: Card

```html
<div class="ui-card" style="--delay:.1s">
    <div class="ui-card-accent"></div>
    <div class="ui-card-title">
        <i class="bi bi-[ICON]"></i>[TITULO]
    </div>
    <div class="ui-card-subtitle">[SUBTITULO]</div>
    <div class="ui-card-body">
        <!-- contenido -->
    </div>
</div>
```

### Template: Sticky Bar

```html
<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('[mod].index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
        <button type="submit" form="[FORM_ID]" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>[Guardar|Actualizar]
        </button>
    </div>
</div>
```

### Template: Stat Card

```html
<div class="ui-stat" style="--delay:.05s">
    <div class="ui-card-accent"></div>
    <div class="ui-stat-body text-center">
        <div class="ui-stat-label">[LABEL]</div>
        <div class="ui-stat-value">[VALOR]</div>
        <div class="ui-stat-sub">[DETALLE]</div>
    </div>
</div>
```

### Template: Botones de Acción

```html
<a href="{{ route('[mod].edit', $item) }}" class="ui-action ui-action-edit" title="Editar">
    <i class="bi bi-pencil"></i>
</a>
<button type="button" class="ui-action ui-action-delete"
        onclick="UI.confirm.delete('{{ route('[mod].destroy', $item) }}', '{{ addslashes($item->nombre) }}')"
        title="Eliminar">
    <i class="bi bi-trash"></i>
</button>
```

### Template: Detail Rows

```html
<div class="ui-detail-row">
    <span class="ui-detail-label">[LABEL]</span>
    <span class="ui-detail-value">[VALOR]</span>
</div>
```

### Template: Badge

```html
<span class="ui-badge ui-badge-[success|warning|danger|info|neutral|primary]">
    <i class="bi bi-[ICON] me-1"></i>[TEXTO]
</span>
```

### Template: Empty State

```html
<div class="ui-empty-state">
    <i class="bi bi-[ICON]"></i>
    <p>No hay registros</p>
    @can('[mod].create')
    <a href="{{ route('[mod].create') }}" class="ui-btn ui-btn-solid ui-btn-sm mt-2 rounded-pill">
        <i class="bi bi-plus-lg"></i> Crear primero
    </a>
    @endcan
</div>
```

### Template: Form Fields

```html
<label class="ui-label">[LABEL] <span class="text-danger">*</span></label>
<input type="text" name="[name]" class="ui-input @error('[name]') is-invalid @enderror" value="{{ old('[name]') }}">

<select name="[name]" class="ui-select @error('[name]') is-invalid @enderror">
    <option value="">Seleccionar...</option>
</select>

<textarea name="[name]" rows="3" class="ui-textarea @error('[name]') is-invalid @enderror"></textarea>

<div class="ui-input-group">
    <span class="ui-input-group-text">RD$</span>
    <input type="number" class="ui-input" name="[name]">
</div>

<div class="ui-input-group input-group-lg">
    <span class="ui-input-group-text bg-light fw-bold">$</span>
    <input type="number" class="ui-input" name="[name]" step="0.01" min="0" placeholder="0.00">
</div>
```

### Template: Input Group with Action Button

```html
<div class="ui-input-group input-group-lg">
    <input type="text" name="[name]" class="ui-input" placeholder="Valor...">
    <button class="ui-btn ui-btn-ghost px-3" type="button" id="btnAction" title="Acción">
        <i class="bi bi-magic"></i>
    </button>
</div>
```

### Template: Form Section Header

```html
<div class="mb-4 pb-3 border-bottom">
    <h6 class="fw-bold mb-0" style="color: ACCENT_HEX;">
        <i class="bi bi-ICON me-2"></i>TÍTULO DE SECCIÓN
    </h6>
</div>
```

Usar estos colores para secciones:
| Contexto | Icono | Color |
|---|---|---|
| Información básica | `bi-box-seam` | `#4f46e5` (acento del módulo) |
| Precios y existencias | `bi-currency-dollar` | `#059669` (verde) |
| Imagen del producto | `bi-image` | `#0891b2` (cyan) |
| Estado del producto | `bi-toggle-on` | `#059669` (verde) |

### Template: Error/Validation Alert

```html
@if ($errors->any())
    <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
```

### Template: Info/Banner Alert (para edit)

```html
<div class="alert rounded-4 shadow-sm border-0 mb-4" style="background:rgba(ACCENT_RGB,.05);border-left:4px solid ACCENT_HEX !important;">
    <div class="d-flex align-items-center">
        <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;color:ACCENT_HEX;background:rgba(ACCENT_RGB,.1);">
            <i class="bi bi-info-circle fs-5"></i>
        </div>
        <div>
            <span class="text-muted">Estás editando [elemento]:</span>
            <strong class="d-block" style="font-size:1.1rem;color:#1e293b;">{{ $[modelo]->nombre }}</strong>
        </div>
    </div>
</div>
```

### Template: Show Detail Card with Accent Background

```html
<div class="p-3 rounded-3" style="background:rgba(ACCENT_RGB,.05);">
    <small class="text-muted text-uppercase d-block small fw-semibold">LABEL</small>
    <span class="fs-5 fw-bold" style="color:ACCENT_HEX;">VALOR</span>
</div>
```

Usar en un grid `.row.g-4` dentro de la columna derecha de `show`.

### Template: Drop Zone (File Upload)

```html
<style>
.drop-zone {
    border: 2px dashed #cbd5e1;
    border-radius: 1rem;
    padding: 3rem;
    text-align: center;
    cursor: pointer;
    transition: all .3s;
    background: #f8fafc;
}
.drop-zone:hover, .drop-zone.dragover {
    border-color: ACCENT_HEX;
    background: rgba(ACCENT_RGB,.05);
}
.drop-zone.has-file {
    border-color: #22c55e;
    background: rgba(34,197,94,.05);
}
</style>

<div class="drop-zone" id="dropZone" onclick="document.getElementById('fileInput').click()" role="button" tabindex="0">
    <div id="dropContent">
        <i class="bi bi-file-earmark-spreadsheet" style="font-size:3rem;color:ACCENT_HEX;"></i>
        <h6 class="fw-bold mt-3 mb-1">Arrastra el archivo aquí o haz clic para seleccionar</h6>
        <p class="text-muted small mb-0">Formatos: CSV, XLSX • Máx. 10 MB</p>
    </div>
    <div id="dropFileInfo" class="d-none">
        <i class="bi bi-check-circle-fill text-success" style="font-size:2rem;"></i>
        <h6 class="fw-bold mt-2 mb-0" id="fileName"></h6>
        <p class="text-muted small mb-0" id="fileSize"></p>
    </div>
    <input type="file" name="file" id="fileInput" class="d-none" accept=".csv,.txt,.xlsx,.xls" required>
</div>
```

### Template: Toggle / Switch Field

```html
<div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(ACCENT_RGB,.05);">
    <div class="form-check form-switch mb-0">
        <input class="form-check-input" type="checkbox" name="activo" value="1" id="chk-activo"
               {{ old('activo', true) ? 'checked' : '' }} role="switch"
               style="width:3em;height:1.5em;">
        <label class="form-check-label fw-semibold ms-2" for="chk-activo">Elemento Activo</label>
    </div>
    <small class="text-muted">Si está inactivo no aparecerá en las listas.</small>
</div>
```

---

## Instrucciones Paso a Paso por Tipo de Vista

### 1. `index.blade.php`

1. Incluir partial en `@push('styles')`
2. Envolver en `<div class="ui-page" style="--accent:...">`
3. Header con template de Header, icono del módulo, contador de registros
4. Filtros en `ui-card` con `ui-input-group` y `ui-btn-solid`/`ui-btn-ghost`
5. Tabla en `ui-card` con `ui-table` + `ui-action-*` + `UI.confirm.delete()`
6. Paginación después de la card
7. Empty state con `ui-empty-state`

**DataTables con UI Premium:**
- El contenedor wrapper de DataTables debe ir dentro de `.ui-card .card-body.p-0`
- Usar `<table class="table productos-table nowrap">` con CSS personalizado:
  - `--bs-table-bg: transparent; --bs-table-hover-bg: rgba(ACCENT_RGB,.04);`
  - `thead th`: `font-size:.7rem; text-transform:uppercase; letter-spacing:.5px;`
  - `tbody td`: `font-size:.9rem; border-bottom: 1px solid var(--dt-gray-100);`
- Input de búsqueda: `border-radius:2rem; padding-left:2.2rem;` con icono search SVG inline
- Paginación: botones con `.paginate_button` (border redondeado, gradient purple en active)
- Responsive: usar `responsive: { details: { type: 'column', target: 'tr' } }` en config DataTables
- DOM personalizado: `dom: '<"row px-3 pt-2"<"col-sm-6"l><"col-sm-6"f>>' + '<"row"<"col-12"tr>>' + '<"row px-3 pb-2"<"col-sm-5"i><"col-sm-7"p>>'`
- Imagen en celda: usar `<img class="producto-img me-3 shadow-sm">` con `border-radius:50%; width:48px; height:48px; object-fit:cover;`
- **Dark mode en DataTables**: Agregar estilos `body.dark-mode` para `.productos-table`, `.paginate_button`, `.dataTables_filter input`, etc. (ver productos como referencia)

### 2. `create.blade.php` / `edit.blade.php`

1. Incluir partial
2. Wrapper `ui-page`
3. Header con título + botón Volver
4. Formulario en `ui-card` con `ui-card-accent`
5. Campos con `ui-label` + `ui-input`/`ui-select`/`ui-textarea`
6. Sticky bar con Cancelar + Guardar/Actualizar

**Para edit, agregar banner informativo** (Template: Info/Banner Alert):
- Mostrar qué elemento se está editando con su nombre.

**Formularios extensos**: dividir en secciones con `border-bottom` + colored title:
- Template: Form Section Header
- Usar colores distintos por sección (indigo → info, verde → precios, cyan → imagen)

### 3. `show.blade.php`

1. Incluir partial
2. Wrapper `ui-page`
3. Header con nombre del elemento + Editar + Volver
4. Layout 2 columnas:
   - Columna izquierda (col-lg-4): Imagen del elemento + nombre + código + badge estado + stock
   - Columna derecha (col-lg-8): `ui-card` con `ui-card-title` "Información General" + datos en grid con Template: Show Detail Card
5. Debajo: fila de stat cards (compras, ventas, etc.) con `ui-user-avatar` y contadores
6. User card con `ui-user-avatar`

### 4. `import.blade.php` (cuando aplique)

1. Incluir partial
2. Wrapper `ui-page`
3. Header con icono `bi-upload`, título "Importar [Elementos]"
4. **Step 1 — Upload**: Template Drop Zone dentro de `ui-card` + botón "Vista Previa"
5. Card de consejos con `ui-card` y lista de tips
6. Card de formato de ejemplo con `bg-dark rounded-3` y `<pre>` con datos de muestra
7. **Step 2 — Mapping** (condicional): Tabla de mapeo con `ui-select` por fila, columnas "Campo del Producto" / "Columna del Archivo" / "Obligatorio"
8. Footer con botones "Subir otro archivo" + "Importar [Elementos]"

---

## Reglas de Dark Mode

El dark mode está completamente centralizado en `premium-ui.blade.php`. No agregar estilos dark mode en las vistas.

---

## JavaScript Global

```javascript
// Confirmación de eliminación
UI.confirm.delete(url, label)

// Confirmación genérica
UI.confirm.action({ title, text, icon, color, confirmText, url/form })

// Toasts
UI.toast.success('mensaje')
UI.toast.error('mensaje')
UI.toast.warning('mensaje')
UI.toast.info('mensaje')
```

---

## Módulos Migrados (Referencia)

| Módulo | Vistas | Acento | Estado |
|---|---|---|---|
| Gastos | index, create, edit, show | Verde #10b981 | ✅ |
| Clientes | index, create, edit, show, creditos, cuentas | Verde #10b981 | ✅ |
| Categorías | index, create, edit, show, import | Rosa #ec4899 | ✅ |
| Compras | index, create, edit, show | Ámbar #f59e0b | ✅ |
| Ventas | index, show | Azul #3b82f6 | ✅ |
| Productos | index, create, edit, show, import | Índigo #6366f1 | ✅ |
| Cajas | index, create, edit, show, cierre | Púrpura #8b5cf6 | ✅ |
| Listas Precio | index, create, edit, show, impacto, logs | Púrpura #8b5cf6 | ✅ |

---

## Checklist de Implementación

- [ ] `@include('partials.premium-ui')` en `@push('styles')`
- [ ] `<style>` blocks vacíos eliminados
- [ ] Wrapper `.ui-page` con `--accent`, `--accent-rgb`, `--accent-hover`
- [ ] Header con 3 bubbles + `ui-avatar-circle` + `ui-header-title` + `ui-header-meta`
- [ ] Cards usan `.ui-card` + `.ui-card-accent`
- [ ] Stat cards usan `.ui-stat` + `.ui-stat-value`
- [ ] Botones usan `.ui-btn-*`
- [ ] Acciones usan `.ui-action-*`
- [ ] Formularios: `.ui-label`, `.ui-input`, `.ui-select`, `.ui-textarea`
- [ ] Form sections con `border-bottom` + colored title
- [ ] Sticky bar con `.ui-sticky-bar` + `.ui-sticky-bar-inner` (con info context a la izquierda)
- [ ] Tablas usan `.ui-table` o DataTables con estilos premium
- [ ] Badges usan `.ui-badge-*`
- [ ] Input groups con addon o botón de acción
- [ ] `UI.confirm.delete()` reemplaza `confirm()` nativo
- [ ] Error/validation alerts con `border-left:4px solid`
- [ ] Edit view con banner informativo (Template: Info/Banner)
- [ ] Show view con detail cards de fondo tenue (Template: Show Detail Card)
- [ ] Show view con layout 2 columnas (imagen izq + datos der + stat cards)
- [ ] Dark mode en DataTables cubierto (si se usa DataTables)
- [ ] `--delay` reemplaza `animation-delay:`
- [ ] Import view con Drop Zone + Step mapping (cuando aplique)
