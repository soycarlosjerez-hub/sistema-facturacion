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

### 2. `create.blade.php` / `edit.blade.php`

1. Incluir partial
2. Wrapper `ui-page`
3. Header con título + botón Volver
4. Formulario en `ui-card` con `ui-card-accent`
5. Campos con `ui-label` + `ui-input`/`ui-select`/`ui-textarea`
6. Sticky bar con Cancelar + Guardar/Actualizar

### 3. `show.blade.php`

1. Incluir partial
2. Wrapper `ui-page`
3. Header con nombre del elemento + Editar + Volver
4. Layout 2 columnas con `ui-stat` cards + `ui-card` con `ui-detail-row`
5. User card con `ui-user-avatar`

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
- [ ] Sticky bar con `.ui-sticky-bar` + `.ui-sticky-bar-inner`
- [ ] Tablas usan `.ui-table`
- [ ] Badges usan `.ui-badge-*`
- [ ] `UI.confirm.delete()` reemplaza `confirm()` nativo
- [ ] Dark mode funciona sin overrides
- [ ] `--delay` reemplaza `animation-delay:`
