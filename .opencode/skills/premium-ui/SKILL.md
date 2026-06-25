---
name: premium-ui
description: Use when the user says "Aplica el UI premium al módulo de [nombre]" or similar. Applies the premium glassmorphism UI pattern with animated gradient headers, floating bubbles, frosted glass cards, and accent color strips to any Laravel Blade module views. Trigger keywords: "ui premium", "premium ui", "aplica premium", "interfaz premium", "diseño premium".
---

# Premium UI - Skill de Implementación

## Trigger

Cuando el usuario diga **cualquiera** de estas frases:
- "Aplica el UI premium al módulo de [nombre]"
- "Pon la interfaz premium en [nombre]"
- "Diseña [nombre] con el UI premium"
- "Haz que [nombre] se vea como el profile"

**Acción**: Implementar el patrón UI premium en TODAS las vistas del módulo indicado.

---

## Partial Location

```
resources/views/partials/premium-ui.blade.php
```

## Cómo Incluirlo

En CADA vista que se modifique, agregar dentro de `@push('styles')`:

```blade
@push('styles')
@include('partials.premium-ui')
<style>
    /* Estilos ESPECÍFICOS del módulo (tablas, stats, badges) */
</style>
@endpush
```

**NUNCA** duplicar el CSS del partial. Siempre usar `@include`.

---

## Referencia Completa de Clases

### Layout & Animación
| Clase | Descripción |
|---|---|
| `.premium-page` | Wrapper de página con animación slide-up |
| `.premium-header` | Header con gradiente animado + burbujas flotantes |
| `.premium-avatar-circle` | Avatar circular dentro del header (64px) |

### Cards
| Clase | Descripción |
|---|---|
| `.premium-card` | Card glassmorphism (fondo frosted glass) |
| `.premium-card-title` | Título con icono dentro de card |
| `.premium-card-subtitle` | Subtítulo debajo del título |
| `.premium-stat-card` | Card de estadísticas |
| `.premium-user-avatar` | Avatar de usuario (agregar `.avatar-amber`, `.avatar-green` o `.avatar-blue`) |

### Franjas de Color (Accent Strips)
| Clase | Colores |
|---|---|
| `.card-accent.green` | Verde → Cyan (#10b981 → #06b6d4) |
| `.card-accent.amber` | Ámbar → Naranja (#f59e0b → #f97316) |
| `.card-accent.red` | Rojo → Naranja (#ef4444 → #f97316) |
| `.card-accent.blue` | Azul → Índigo (#3b82f6 → #6366f1) |
| `.card-accent.purple` | Púrpura → Rosa (#8b5cf6 → #a855f7) |

### Badges y Botones de Acción
| Clase | Descripción |
|---|---|
| `.premium-badge` | Badge de filtro (agregar `.active` para estado activo) |
| `.premium-btn-edit` | Botón de editar (color ámbar) |
| `.premium-btn-delete` | Botón de eliminar (color rojo) |

### Formularios
| Clase | Descripción |
|---|---|
| `.premium-sticky-bar` | Barra fija inferior para formularios |
| `.premium-sticky-bar .btn-save` | Botón guardar (gradiente verde) |
| `.premium-sticky-bar .btn-cancel` | Botón cancelar |

### Vistas de Detalle
| Clase | Descripción |
|---|---|
| `.premium-detail-row` | Fila contenedora |
| `.premium-detail-label` | Label (ancho 180px) |
| `.premium-detail-value` | Valor |

---

## Templates HTML Listos para Copiar

### Template: Header

```html
<div class="premium-header mb-4">
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
        <div class="d-flex align-items-center gap-3">
            <div class="premium-avatar-circle">
                <i class="bi bi-[ICON]"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-white">[TITULO]</h4>
                <small class="text-white opacity-75">
                    <i class="bi bi-[ICON] me-1"></i>[SUBTITULO]
                </small>
            </div>
        </div>
        <a href="[URL_INDEX]" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>
```

### Template: Card

```html
<div class="premium-card" style="animation-delay:.1s;">
    <div class="card-accent [green|amber|red|blue|purple]"></div>
    <div class="premium-card-title">
        <i class="bi bi-[ICON] icon-[green|amber|red|blue|purple]"></i>[TITULO]
    </div>
    <div class="premium-card-subtitle">[SUBTITULO]</div>
    <div class="card-body">
        <!-- contenido -->
    </div>
</div>
```

### Template: Sticky Bar (Formularios)

```html
<div class="premium-sticky-bar">
    <div class="d-flex justify-content-end align-items-center">
        <a href="[URL_CANCEL]" class="btn-cancel me-2">Cancelar</a>
        <button type="submit" form="[FORM_ID]" class="btn-save">
            <i class="bi bi-check-lg me-2"></i>[GUARDAR|ACTUALIZAR]
        </button>
    </div>
</div>
```

### Template: Stat Card

```html
<div class="premium-stat-card" style="animation-delay:.05s;">
    <div class="card-accent [COLOR]"></div>
    <div class="card-body p-3 text-center">
        <div class="stat-label mb-1">[LABEL]</div>
        <div class="stat-value" style="color:[COLOR_HEX];">[VALOR]</div>
        <small class="text-muted">[DETALLE]</small>
    </div>
</div>
```

### Template: Badge de Filtro

```html
<a href="[URL]" class="premium-badge [active]">[TEXTO]</a>
```

### Template: Botones de Acción

```html
<a href="[URL_EDIT]" class="premium-btn-edit" title="Editar">
    <i class="bi bi-pencil"></i>
</a>
<button type="button" class="premium-btn-delete" onclick="..." title="Eliminar">
    <i class="bi bi-trash"></i>
</button>
```

### Template: Fila de Detalle (Show)

```html
<div class="premium-detail-row">
    <div class="premium-detail-label">[LABEL]</div>
    <div class="premium-detail-value">[VALOR]</div>
</div>
```

### Template: Avatar de Usuario (Show)

```html
<div class="premium-user-avatar [avatar-amber|avatar-green|avatar-blue] mx-auto mb-3">
    <i class="bi bi-person-circle fs-2" style="color:[COLOR_HEX];"></i>
</div>
```

---

## Instrucciones Paso a Paso por Tipo de Vista

### 1. `index.blade.php`

1. Reemplazar `@push('styles') <style>...</style> @endpush` por:
   ```blade
   @push('styles')
   @include('partials.premium-ui')
   <style>
       /* Estilos de tabla específicos del módulo */
   </style>
   @endpush
   ```

2. Envolver contenido en `<div class="container-fluid px-4 py-3 premium-page">`

3. Reemplazar header por template de Header con:
   - Icono representativo del módulo
   - Título del módulo
   - Subtítulo descriptivo
   - Botón "Nuevo [elemento]" con estilo transparente

4. Reemplazar cards de stats por `premium-stat-card` con `card-accent [color]`

5. Reemplazar card de filtros por `premium-card` con `card-accent [color]`

6. Reemplazar tabla existente:
   - Agregar clase de tabla específica (ej: `.productos-table`, `.gastos-table`)
   - Estilos de thead: `background: rgba(241,245,249,.8); color: #64748b; font-size: .7rem; text-transform: uppercase;`
   - Estilos de tbody td: `padding: .85rem 1rem; border-bottom: 1px solid #f1f5f9;`
   - Hover: `background: rgba([COLOR_RGB],.03);`

7. Reemplazar botones de acción por `premium-btn-edit` / `premium-btn-delete`

8. Agregar dark mode para tabla:
   ```css
   body.dark-mode .[module]-table thead th {
       background: rgba(15,23,42,.5);
       color: #94a3b8;
       border-color: #1e293b;
   }
   body.dark-mode .[module]-table tbody td {
       border-bottom-color: #1e293b;
       color: #cbd5e1;
   }
   ```

### 2. `create.blade.php`

1. Incluir partial + estilo específico (si hay)

2. Envolver en `premium-page`

3. Header con:
   - Avatar: icono de creación (ej: `bi-plus-circle`, `bi-wallet2`, `bi-box-seam`)
   - Título: "Nuevo [Elemento]"
   - Subtítulo: "Registra un nuevo [elemento] en el sistema"
   - Botón "Volver"

4. Errores después del header

5. Formulario dentro de `premium-card` con `card-accent [color]`:
   ```html
   <div class="premium-card" style="animation-delay:.1s;">
       <div class="card-accent [COLOR]"></div>
       <form id="[formId]" ...>
           @csrf
           <!-- contenido del form -->
       </form>
   </div>
   ```

6. Agregar `<div style="height: 80px;"></div>` antes del sticky bar

7. Sticky bar al final:
   ```html
   <div class="premium-sticky-bar">
       <div class="d-flex justify-content-end align-items-center">
           <a href="[URL_CANCEL]" class="btn-cancel me-2">Cancelar</a>
           <button type="submit" form="[formId]" class="btn-save">
               <i class="bi bi-check-lg me-2"></i>Guardar [Elemento]
           </button>
       </div>
   </div>
   ```

### 3. `edit.blade.php`

Mismo patrón que create, con diferencias:

1. Header: Avatar con `bi-pencil-square`, título "Editar [Elemento]"
2. Info banner antes del formulario:
   ```html
   <div class="alert rounded-4 shadow-sm border-0 mb-4" style="background:rgba([COLOR_RGB],.05);border-left:4px solid [COLOR_HEX] !important;">
       <div class="d-flex align-items-center">
           <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;color:[COLOR_HEX];background:rgba([COLOR_RGB],.1);">
               <i class="bi bi-info-circle fs-5"></i>
           </div>
           <div>
               <span class="text-muted">Estás editando el/la [elemento]:</span>
               <strong class="d-block" style="font-size:1.1rem;color:#1e293b;">[NOMBRE]</strong>
           </div>
       </div>
   </div>
   ```
3. Formulario con `@method('PUT')`
4. Sticky bar: "Guardar Cambios"

### 4. `show.blade.php`

1. Header con:
   - Avatar: icono de vista (ej: `bi-eye`, `bi-box-seam`)
   - Título: nombre del elemento
   - Subtítulo: código o identificador
   - Botones: Editar + Volver

2. Layout de 2 columnas (col-lg-4 + col-lg-8):
   - **Columna izquierda**: Card con imagen + badges de estado
   - **Columna derecha**: Card con información general + cards de estadísticas

3. Información general en `premium-card` con `premium-detail-row`:
   ```html
   <div class="premium-detail-row">
       <div class="premium-detail-label">[Label]</div>
       <div class="premium-detail-value">[Valor]</div>
   </div>
   ```

4. Cards de estadísticas con `premium-card` + `premium-user-avatar`

### 5. `import.blade.php` (si existe)

1. Header con icono `bi-upload`
2. Upload zone en `premium-card`
3. Tips en `premium-card`
4. Mapping table en `premium-card`
5. Botones: "Subir otro archivo" + "Importar"

---

## Sistema de Colores Accent

Elegir UN color principal para el módulo y usarlo consistentemente:

| Módulo | Color Recomendado | Hex |
|---|---|---|
| Gastos | green | #10b981 |
| Productos | blue | #4f46e5 / #3b82f6 |
| Clientes | green | #10b981 |
| Compras | amber | #f59e0b |
| Ventas | purple | #8b5cf6 |
| Proveedores | blue | #3b82f6 |
| Categorías | amber | #f59e0b |
| Usuarios | blue | #3b82f6 |
| Configuración | purple | #8b5cf6 |

Para usar el color en el HTML:
- `card-accent [color]` → para la franja
- `icon-[color]` → para el icono del título
- `style="color:[hex];"` → para valores específicos

---

## Reglas de Dark Mode

Los estilos de dark mode van SIEMPRE en el bloque `<style>` específico del módulo (después del `@include`), NUNCA en el partial.

```css
/* Dark mode - específico del módulo */
body.dark-mode .premium-card,
body.dark-mode .premium-stat-card {
    background: rgba(15,23,42,.8);
    border-color: rgba(255,255,255,.08);
}
body.dark-mode .premium-card-title { color: #f1f5f9; }
body.dark-mode .premium-card-subtitle { color: #94a3b8; }
```

---

## Módulos Ya Aplicados (Referencia)

### Gastos (`resources/views/gastos/`)
- Accent: green
- Index: stats + filtros + tabla + paginación
- Create/Edit: formulario + sticky bar
- Show: detalle rows + user card

### Productos (`resources/views/productos/`)
- Accent: blue
- Index: tabla compleja con imágenes + acciones
- Create/Edit: formulario incluido via `@include('productos.form')` + sticky bar
- Show: 2 columnas (imagen + info) + stats cards
- Import: drop zone + mapping table

### Profile (`resources/views/profile/`)
- Accent: green/amber/red
- 3 cards: perfil (green), contraseña (amber), eliminar cuenta (red)
- Modal de confirmación para eliminar

---

## Checklist de Implementación

Antes de entregar, verificar:

- [ ] `@include('partials.premium-ui')` está en cada vista
- [ ] No hay CSS duplicado del partial
- [ ] Header tiene 3 bubbles
- [ ] Header tiene `premium-avatar-circle`
- [ ] Cards usan `premium-card` + `card-accent [color]`
- [ ] Formularios tienen `premium-sticky-bar`
- [ ] Tablas tienen estilos específicos
- [ ] Dark mode funciona
- [ ] Animaciones stagger funcionan (`animation-delay`)
- [ ] Todos los botones de acción usan `premium-btn-edit` / `premium-btn-delete`
