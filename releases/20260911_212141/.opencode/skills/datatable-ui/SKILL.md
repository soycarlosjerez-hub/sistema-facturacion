---
name: datatable-ui
description: Use when the user says "Agrega DataTables al módulo de [nombre]", "Pon tabla con búsqueda a [nombre]", "Haz que [nombre] tenga DataTables como productos", "Aplica DataTables UI a [nombre]", or similar. Applies a client-side jQuery DataTable with responsive design, search, pagination, and column helpers matching the productos index style. Trigger keywords: "datatable", "datatables", "tabla con búsqueda", "tabla responsive", "data table".
---

# DataTables UI - Skill de Implementación

## Trigger

Cuando el usuario diga **cualquiera** de estas frases:
- "Agrega DataTables al módulo de [nombre]"
- "Pon tabla con búsqueda a [nombre]"
- "Haz que [nombre] tenga DataTables como productos"
- "Aplica DataTables UI a [nombre]"
- "Pon una tabla con búsqueda, paginación y responsive a [nombre]"

**Acción**: Implementar DataTables client-side en la vista `index` del módulo indicado, con el mismo estilo visual que la tabla de productos.

---

## Archivos de Referencia

| Archivo | Propósito |
|---------|-----------|
| `resources/views/partials/datatable-ui.blade.php` | CSS reutilizable de DataTables (incluir con `@include`) |
| `resources/views/productos/index.blade.php` | Implementación completa de referencia |
| `resources/views/layouts/app.blade.php` | jQuery 3.7.1 + DataTables 1.13.7 + Responsive 2.5.0 (ya incluidos) |

---

## Dependencias

Ya están cargadas en `layouts/app.blade.php`:
- jQuery 3.7.1
- DataTables 1.13.7 (JS + Bootstrap 5 CSS)
- Responsive 2.5.0 (JS + Bootstrap 5 CSS)
- Bootstrap Icons

**No** instalar nada adicional.

---

## Paso a Paso

### 1. Controlador / Servicio

Agregar un método `listAll()` en el Service correspondiente que retorne **todos** los registros del tenant como Collection (sin paginación):

```php
// Ej: app/Services/ProductoService.php
public function listAll()
{
    $query = Producto::query();
    if ($tenantId = auth()->user()->business_instance_id) {
        $query->where('tenant_id', $tenantId);
    }
    return $query->orderBy('nombre')->get();
}
```

En el Controller, `index()` pasa el Collection a la vista (además de la paginación si se necesita para otras vistas):

```php
public function index()
{
    $items = $this->service->listAll(); // Para DataTables
    $paginated = $this->service->list(); // Paginación normal (opcional)
    return view('modulo.index', compact('items', 'paginated'));
}
```

> **Nota**: `listAll()` solo es necesario si no existe ya un método que devuelva todos los registros.

### 2. Vista `index.blade.php`

#### 2a. Agregar los partials en `@push('styles')`:

```blade
@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #8b5cf6;
    --dt-accent-gradient: linear-gradient(135deg, #8b5cf6, #a855f7);
    --dt-accent-rgb: 139,92,246;
}
/* Estilos específicos del módulo (avatars, badges, etc.) */
</style>
@endpush
```

#### 2b. Header (premium-ui):

```blade
@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-[ICONO]"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">[TÍTULO MÓDULO]</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-info-circle me-1"></i>[SUBTÍTULO]
                    </small>
                </div>
            </div>
            <div>
                <a href="{{ route('[modulo].create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo [Elemento]
                </a>
            </div>
        </div>
    </div>
```

#### 2c. Card de filtros (opcional):

```blade
    <div class="premium-card mb-4" style="animation-delay:.1s;">
        <div class="card-accent [COLOR]"></div>
        <div class="card-body p-3">
            <form id="filtros-form" class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label small fw-bold text-muted">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="busqueda-[modulo]" class="form-control bg-light border-0" placeholder="Buscar..." autocomplete="off">
                    </div>
                </div>
                <!-- Más filtros si aplica -->
                <div class="col-lg-2 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('[modulo].index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>
```

#### 2d. Card con la tabla:

```blade
    <div class="premium-card" style="animation-delay:.15s;">
        <div class="card-accent [COLOR]"></div>
        <div class="card-body p-0">
            <table id="[modulo]-table" class="table dt-table nowrap no-footer" style="width:100%">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>[Columna 1]</th>
                        <th>[Columna 2]</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
```

### 3. Script DataTables (`@push('scripts')`)

```blade
@push('scripts')
<script>
$(function() {
    const data = @json($items); // Client-side: todos los registros
    const csrfToken = '{{ csrf_token() }}';
    const canEdit = {{ auth()->user()->can('[modulo].edit') ? 'true' : 'false' }};
    const canDelete = {{ auth()->user()->can('[modulo].delete') ? 'true' : 'false' }};

    const table = $('#[modulo]-table').DataTable({
        data: data,
        columns: [
            // Columna 0: # (índice)
            {
                data: null,
                className: 'text-center ps-4',
                orderable: false,
                searchable: false,
                width: '50px',
                render: function(data, type, row, meta) {
                    return '<span class="text-muted fw-bold">' + (meta.row + meta.settings._iDisplayStart + 1) + '</span>';
                }
            },
            // Columna 1: Nombre / identificador
            {
                data: null,
                orderable: true,
                searchable: true,
                render: function(data) {
                    // Personalizar por módulo
                    return '<div class="fw-bold">' + escapeHtml(data.nombre) + '</div>';
                }
            },
            // Columna(s) adicional(es) - personalizar por módulo
            {
                data: '[campo]',
                className: 'text-center',
                render: function(data) {
                    return renderEstado(data, { 1: 'Activo', 0: 'Inactivo' });
                }
            },
            // Columna N: Acciones
            {
                data: null,
                className: 'text-end pe-4',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return renderAcciones(data.id, {
                        view: '/[modulo]/' + data.id,
                        edit: canEdit ? '/[modulo]/' + data.id + '/edit' : null,
                        delete: canDelete ? '/[modulo]/' + data.id : null,
                        csrf: csrfToken,
                        nombre: data.nombre
                    });
                }
            }
        ],
        language: {
            search: '',
            lengthMenu: '_MENU_',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            infoEmpty: 'No hay registros',
            infoFiltered: '(de _MAX_ totales)',
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            },
            zeroRecords: '<div class="text-center py-5">' +
                '<i class="bi bi-inbox d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i>' +
                '<p class="fw-semibold mb-1" style="color:#475569;">No se encontraron registros</p>' +
                '<p class="text-muted small mb-0">Intenta ajustar los filtros de búsqueda.</p></div>'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
        order: [[1, 'asc']],
        responsive: {
            details: {
                type: 'column',
                target: 'tr',
                renderer: function(api, rowIdx, columns) {
                    let data = '';
                    columns.forEach(function(col) {
                        if (col.hidden) {
                            data += '<li>' +
                                '<span class="child-label">' + col.title + '</span>' +
                                '<span class="child-value">' + col.data + '</span>' +
                            '</li>';
                        }
                    });
                    return data ? $('<ul class="d-flex flex-wrap gap-2 p-2 mb-0">' + data + '</ul>') : false;
                }
            }
        },
        dom: '<"row px-3 pt-2"<"col-sm-6"l><"col-sm-6"f>>' +
             '<"row"<"col-12"tr>>' +
             '<"row px-3 pb-2"<"col-sm-5"i><"col-sm-7"p>>'
    });

    // Filtros (si existe el formulario)
    $('#filtros-form').on('submit', function(e) {
        e.preventDefault();
        aplicarFiltros(table);
    });

    // Búsqueda en tiempo real
    let searchTimeout;
    $('#busqueda-[modulo]').on('input', function() {
        clearTimeout(searchTimeout);
        const val = $(this).val();
        searchTimeout = setTimeout(function() {
            table.search(val).draw();
        }, 300);
    });

    // ============================================================
    // HELPERS
    // ============================================================

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }
});
</script>
@endpush
```

---

## Helpers Reutilizables (copiar según necesidad)

### renderMoneda - Formato RD$

```javascript
function renderMoneda(valor) {
    const num = parseFloat(valor || 0);
    const signo = num < 0 ? '- ' : '';
    return '<div class="fw-bold" style="color:var(--dt-accent, #3b82f6);">' +
        signo + 'RD$ ' + Math.abs(num).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) +
    '</div>';
}
```

### renderStock - Badge de inventario

```javascript
function renderStock(cantidad) {
    const stock = parseInt(cantidad || 0);
    if (stock <= 5) {
        return '<span class="badge bg-danger rounded-pill">' +
            '<i class="bi bi-exclamation-triangle-fill me-1"></i> ' + stock + ' unid.</span>';
    }
    if (stock <= 15) {
        return '<span class="badge bg-warning text-dark rounded-pill">' +
            '<i class="bi bi-exclamation-circle-fill me-1"></i> ' + stock + ' unid.</span>';
    }
    return '<span class="badge rounded-pill" style="background:rgba(34,197,94,.1);color:#16a34a;font-weight:600;">' +
        '<i class="bi bi-check-circle-fill me-1"></i> ' + stock + ' unid.</span>';
}
```

### renderEstado - Badge activo/inactivo

```javascript
function renderEstado(valor, labels) {
    // labels por defecto: { 1: 'Activo', 0: 'Inactivo' }
    const lbl = labels || { 1: 'Activo', 0: 'Inactivo' };
    const v = valor ? 1 : 0;
    if (v) {
        return '<span class="badge rounded-pill" style="background:rgba(34,197,94,.1);color:#16a34a;font-weight:600;">' +
            '<i class="bi bi-check-circle-fill me-1"></i>' + (lbl[1] || 'Activo') + '</span>';
    }
    return '<span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary" style="font-weight:600;">' +
        '<i class="bi bi-x-circle-fill me-1"></i>' + (lbl[0] || 'Inactivo') + '</span>';
}
```

### renderFecha - Fecha formateada

```javascript
function renderFecha(fechaISO) {
    if (!fechaISO) return '<span class="text-muted small">—</span>';
    const d = new Date(fechaISO);
    if (isNaN(d.getTime())) return '<span class="text-muted small">—</span>';
    const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    const dia = String(d.getDate()).padStart(2, '0');
    const mes = meses[d.getMonth()];
    const anio = d.getFullYear();
    return '<span class="small">' + dia + ' ' + mes + ' ' + anio + '</span>';
}
```

### renderAvatar - Inicial con color

```javascript
function renderAvatar(nombre) {
    if (!nombre) return '<div class="avatar-circle text-white me-2 shadow-sm" style="background:#94a3b8;">?</div>';
    const initial = nombre.charAt(0).toUpperCase();
    const colors = ['#f87171','#60a5fa','#34d399','#fbbf24','#a78bfa','#f472b6','#f97316','#14b8a6'];
    const color = colors[crc32(nombre) % colors.length];
    return '<div class="avatar-circle text-white me-2 shadow-sm" style="background:' + color + ';">' + initial + '</div>';
}

function crc32(str) {
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
        const char = str.charCodeAt(i);
        hash = ((hash << 5) - hash) + char;
        hash = hash & hash;
    }
    return Math.abs(hash);
}
```

### renderAcciones - Botones premium

```javascript
function renderAcciones(id, opts) {
    let html = '<div class="d-flex justify-content-end gap-1">';
    if (opts.view) {
        html += '<a href="' + opts.view + '" class="premium-btn-edit" title="Ver" style="background:rgba(59,130,246,.1);color:#3b82f6;border-color:rgba(59,130,246,.2);">' +
            '<i class="bi bi-eye"></i></a>';
    }
    if (opts.edit) {
        html += '<a href="' + opts.edit + '" class="premium-btn-edit" title="Editar">' +
            '<i class="bi bi-pencil"></i></a>';
    }
    if (opts.delete) {
        html += '<form action="' + opts.delete + '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar ' + escapeHtml(opts.nombre || 'este registro') + '? Esta acción no se puede deshacer.\');">' +
            '<input type="hidden" name="_token" value="' + opts.csrf + '">' +
            '<input type="hidden" name="_method" value="DELETE">' +
            '<button type="submit" class="premium-btn-delete border-0" title="Eliminar">' +
            '<i class="bi bi-trash"></i></button></form>';
    }
    html += '</div>';
    return html;
}
```

---

## Template de Filtros Avanzados

Cuando el módulo requiere filtros adicionales (estado, stock, rango de precio, etc.):

```javascript
function aplicarFiltros(table) {
    const valBusqueda = $('#busqueda-[modulo]').val();
    const valEstado = $('#filter-estado').val();
    const valMin = parseFloat($('#filter-min').val()) || 0;
    const valMax = parseFloat($('#filter-max').val()) || Infinity;

    table.search(valBusqueda).draw();

    $.fn.dataTable.ext.search.push(function(settings, data) {
        // data es un array con los valores de cada columna visible
        const estado = data[2]; // Ajustar índice según posición de columna
        const valorStr = data[1].replace(/[^0-9.]/g, '');
        const valor = parseFloat(valorStr) || 0;

        if (valEstado && estado !== valEstado) return false;
        if (valor < valMin) return false;
        if (valor > valMax) return false;

        return true;
    });

    table.draw();
    $.fn.dataTable.ext.search.pop();
}
```

---

## Sistema de Colores (--dt-accent)

Elegir UN color y setear las 3 variables CSS en el `<style>` del módulo:

| Módulo | Color | Hex | CSS Variables |
|--------|-------|-----|---------------|
| Clientes | Green | #10b981 | `--dt-accent: #10b981; --dt-accent-gradient: linear-gradient(135deg, #10b981, #06b6d4); --dt-accent-rgb: 16,185,129;` |
| Proveedores | Blue | #3b82f6 | `--dt-accent: #3b82f6; --dt-accent-gradient: linear-gradient(135deg, #3b82f6, #6366f1); --dt-accent-rgb: 59,130,246;` |
| Productos | Blue | #3b82f6 | `--dt-accent: #3b82f6; --dt-accent-gradient: linear-gradient(135deg, #3b82f6, #6366f1); --dt-accent-rgb: 59,130,246;` |
| Categorías | Purple | #8b5cf6 | `--dt-accent: #8b5cf6; --dt-accent-gradient: linear-gradient(135deg, #8b5cf6, #a855f7); --dt-accent-rgb: 139,92,246;` |
| Ventas | Blue | #3b82f6 | `--dt-accent: #3b82f6; --dt-accent-gradient: linear-gradient(135deg, #3b82f6, #6366f1); --dt-accent-rgb: 59,130,246;` |
| Compras | Blue | #3b82f6 | `--dt-accent: #3b82f6; --dt-accent-gradient: linear-gradient(135deg, #3b82f6, #6366f1); --dt-accent-rgb: 59,130,246;` |
| Gastos | Green | #10b981 | `--dt-accent: #10b981; --dt-accent-gradient: linear-gradient(135deg, #10b981, #06b6d4); --dt-accent-rgb: 16,185,129;` |
| Usuarios | Amber | #f59e0b | `--dt-accent: #f59e0b; --dt-accent-gradient: linear-gradient(135deg, #f59e0b, #f97316); --dt-accent-rgb: 245,158,11;` |
| Roles | Purple | #8b5cf6 | `--dt-accent: #8b5cf6; --dt-accent-gradient: linear-gradient(135deg, #8b5cf6, #a855f7); --dt-accent-rgb: 139,92,246;` |
| Almacenes | Blue | #3b82f6 | `--dt-accent: #3b82f6; --dt-accent-gradient: linear-gradient(135deg, #3b82f6, #6366f1); --dt-accent-rgb: 59,130,246;` |
| Devoluciones | Red | #ef4444 | `--dt-accent: #ef4444; --dt-accent-gradient: linear-gradient(135deg, #ef4444, #f97316); --dt-accent-rgb: 239,68,68;` |

---

## Dark Mode

El partial `datatable-ui.blade.php` ya incluye estilos completos de dark mode para todos los elementos DataTables. No es necesario agregar nada adicional.

Si el módulo tiene elementos específicos (avatar, imágenes, badges personalizados), agregar overrides en el `<style>` del módulo:

```css
body.dark-mode .modulo-avatar { border-color: #1e293b; }
body.dark-mode .modulo-badge { background: rgba(255,255,255,.05); }
```

---

## Checklist de Implementación

Antes de entregar, verificar:

- [ ] `@include('partials.premium-ui')` en `@push('styles')`
- [ ] `@include('partials.datatable-ui')` en `@push('styles')`
- [ ] Variables CSS `--dt-accent`, `--dt-accent-gradient`, `--dt-accent-rgb` seteadas
- [ ] Service tiene método `listAll()` para client-side
- [ ] Controller pasa `$items = $service->listAll()` a la vista
- [ ] Tabla usa clase `.dt-table` y `id="[modulo]-table"`
- [ ] Tabla está envuelta en `.premium-card` con `card-accent`
- [ ] Header premium con burbujas y avatar
- [ ] DataTable init en `@push('scripts')`
- [ ] `language` configurado en español
- [ ] `responsive` configurado con child rows
- [ ] `dom` con Bootstrap grid layout
- [ ] `@json($items)` como fuente de datos
- [ ] Búsqueda en tiempo real con debounce
- [ ] Acciones usan `renderAcciones()` o equivalente
- [ ] Permisos respetados (canEdit, canDelete)
- [ ] Dark mode funcional
- [ ] Paginación, búsqueda y responsive funcionan correctamente

---

## Notas Técnicas

- **Client-side**: Todos los registros se cargan en el navegador como JSON. Adecuado para tablas con menos de ~2000 registros.
- **Server-side**: Para tablas más grandes, migrar a server-side con endpoint JSON que responda a `draw`, `recordsTotal`, `recordsFiltered`, `data`.
- **CSRF**: El token se pasa como variable JS desde la vista para los formularios de eliminación.
- **Responsive**: Usa `type: 'column'` con una columna de control. En móviles las columnas se ocultan y los datos se muestran en child rows expandibles.
- **DOM layout**: `l` (length) + `f` (filter) en top row, `t` (table) en middle, `i` (info) + `p` (paginate) en bottom row, con columnas Bootstrap.
