# Frontend Pro Skill

Aplica el frontend profesional al módulo de [nombre] — patrón Enterprise UI con DataTables, responsive cards, y sistema de componentes consistentes.

## Cuándo usar

- "Aplica UI profesional a [módulo]"
- "Haz que [módulo] se vea como dashboard enterprise"
- "Refactoriza [vista] con el patrón Pro UI"
- "Agrega DataTables + responsive cards a [módulo]"
- "Aplica el frontend pro a [nombre]"
- "Mejora el frontend de [módulo]"

## Arquitectura del Patrón Pro UI

```
┌─────────────────────────────────────────────────────────────────┐
│  HEADER animado (gradient + avatar + título + acciones)         │
├─────────────────────────────────────────────────────────────────┤
│  KPI SUMMARY ROW (4-6 cards con ícono, valor, trend, subtext)  │
├─────────────────────────────────────────────────────────────────┤
│  FILTER BAR (iconos, chips, select, botón filtrar/limpiar)     │
├─────────────────────────────────────────────────────────────────┤
│  CONTENT AREA                                                   │
│  ├── Desktop: Tabla con avatar + dropdown acciones             │
│  ├── Mobile: Cards grid con resumen                            │
│  └── Empty state: Ilustrado + CTA                              │
├─────────────────────────────────────────────────────────────────┤
│  PAGINATION (card-footer con pagination links)                  │
└─────────────────────────────────────────────────────────────────┘
```

## Componentes Clave

### 1. KPI Summary Card

```blade
<div class="ui-card" style="--delay:.{n}s">
    <div class="ui-card-accent" style="background:{color}"></div>
    <div class="ui-card-body py-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div class="ui-stat-label text-muted mb-1">{{ LABEL }}</div>
                <div class="fw-bold" style="font-size:1.75rem;line-height:1.1;color:{color}">{{ VALUE }}</div>
                <small class="text-muted">{{ SUBTEXT }}</small>
            </div>
            <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;
                         background:rgba({color-rgb},.1);">
                <i class="bi {icon}" style="font-size:1.5rem;color:{color};"></i>
            </div>
        </div>
    </div>
</div>
```

Mapeos de color estándar:
- `--accent` (var(--accent,#8b5cf6)): Color principal del módulo
- `#10b981` (verde): Activas, éxito, completado
- `#3b82f6` (azul): Total, info
- `#ef4444` (rojo): Bloqueadas, errores
- `#f59e0b` (naranja): Advertencias, pendientes

### 2. Filter Bar

```blade
<div class="ui-card mb-4" style="--delay:.15s">
    <div class="ui-card-body p-3">
        <form method="GET" action="{{ route('...') }}" class="row g-2 align-items-center">
            {{-- Search --}}
            <div class="col-lg-4 col-md-6">
                <div class="input-group input-group-merge">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="ui-input border-0 bg-white" placeholder="Buscar..." value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>
            {{-- Select filters --}}
            <div class="col-lg-3 col-md-6">
                <select name="filter" class="ui-select border-0 bg-white">
                    <option value="">Todas las opciones</option>
                    @foreach($options as $opt)
                        <option value="{{ $opt->id }}" {{ request('filter') == $opt->id ? 'selected' : '' }}>{{ $opt->nombre }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Action buttons --}}
            <div class="col-lg-auto col-md-auto d-flex gap-2">
                <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                <a href="{{ route('...') }}" class="ui-btn ui-btn-primary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>
```

### 3. Data Table (Desktop)

```blade
<div class="ui-card d-none d-md-block" style="--delay:.2s">
    <div class="table-responsive">
        <table class="ui-table mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Nombre</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:36px;height:36px;background:rgba(139,92,246,.1);color:#8b5cf6;font-size:13px;font-weight:600;">
                                {{ strtoupper(substr($item->nombre, 0, 1)) }}
                            </div>
                            <div style="min-width:0;">
                                <div class="fw-bold text-truncate">{{ $item->nombre }}</div>
                                <small class="text-muted">{{ $item->email ?? $item->id }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="ui-badge ui-badge-success rounded-pill">
                            <i class="bi bi-check-circle me-1"></i>Activo
                        </span>
                    </td>
                    <td class="text-muted small">{{ $item->created_at->format('d/m/Y') }}</td>
                    <td class="text-end pe-4">
                        <div class="dropdown">
                            <button class="ui-btn ui-btn-ghost btn-sm rounded-pill" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0 py-2">
                                <li><a class="dropdown-item small" href="{{ route('...') }}"><i class="bi bi-eye me-2"></i>Ver</a></li>
                                <li><a class="dropdown-item small" href="{{ route('...') }}"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item small text-danger" href="#" onclick="UI.confirm.delete('{{ route('...') }}')"><i class="bi bi-trash me-2"></i>Eliminar</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ $columnCount }}" class="text-center text-muted py-5">
                    <div class="ui-empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No hay resultados</p>
                        <small>Intenta cambiar los filtros de búsqueda</small>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="card-footer bg-transparent border-0 py-3 px-4">
        {{ $items->links() }}
    </div>
    @endif
</div>
```

### 4. Cards Grid (Mobile/Responsive)

```blade
<div class="d-md-none">
    @foreach($items as $item)
    <div class="ui-card mb-3" style="--delay:.{{ $loop->index + 1 }}s">
        <div class="ui-card-body p-3">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:44px;height:44px;background:rgba(139,92,246,.1);color:#8b5cf6;font-size:16px;font-weight:600;">
                    {{ strtoupper(substr($item->nombre, 0, 1)) }}
                </div>
                <div class="flex-grow-1" style="min-width:0;">
                    <div class="fw-bold text-truncate">{{ $item->nombre }}</div>
                    <small class="text-muted">{{ $item->email ?? 'Sin email' }}</small>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="ui-badge ui-badge-success rounded-pill">Activo</span>
                <span class="ui-badge ui-badge-neutral rounded-pill">{{ $item->created_at->diffForHumans() }}</span>
            </div>
            <div class="d-grid gap-2">
                <a href="{{ route('...') }}" class="ui-btn ui-btn-solid btn-sm">
                    <i class="bi bi-eye me-1"></i>Ver detalles
                </a>
                <a href="{{ route('...') }}" class="ui-btn ui-btn-ghost btn-sm">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
```

### 5. Status Badges (Profesionales)

Usar el sistema `ui-badge` del premium-ui:

```blade
<span class="ui-badge ui-badge-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Activo</span>
<span class="ui-badge ui-badge-warning rounded-pill"><i class="bi bi-exclamation-triangle me-1"></i>Pendiente</span>
<span class="ui-badge ui-badge-danger rounded-pill"><i class="bi bi-x-circle me-1"></i>Inactivo</span>
<span class="ui-badge ui-badge-neutral rounded-pill">Sin datos</span>
<span class="ui-badge ui-badge-info rounded-pill">Info</span>
```

### 6. Empty State (Profesional)

```blade
<div class="ui-empty-state">
    <i class="bi bi-inbox"></i>
    <p>No hay resultados</p>
    <small>Intenta cambiar los filtros de búsqueda</small>
</div>
```

### 7. Header Animado

```blade
<div class="ui-header mb-4" style="--delay:.1s">
    <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
    <div class="ui-header-body">
        <div class="ui-header-left">
            <div class="ui-avatar-circle">
                <i class="bi bi-{module-icon}"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-1">Título Principal</h2>
                <p class="mb-0 opacity-75">Descripción breve del módulo.</p>
            </div>
        </div>
        <div class="ui-header-actions">
            <a href="{{ route('...') }}" class="ui-btn ui-btn-solid">
                <i class="bi bi-plus-lg me-2"></i>Nuevo
            </a>
        </div>
    </div>
</div>
```

## Estructura de Archivo Resultante

```blade
@extends('layouts.app')
@section('title', 'Título del Módulo')

@push('styles')
    @include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    {{-- 1. HEADER --}}
    <div class="ui-header mb-4" style="--delay:.1s">
        {{-- gradient + avatar + título + botón --}}
    </div>

    {{-- 2. KPI SUMMARY (4 cards) --}}
    <div class="row g-3 mb-4">
        @include('partials.kpi_card', ['label' => 'Total', 'value' => $total, 'color' => '#3b82f6', 'icon' => 'bi-list-ul', 'sub' => 'Registros'])
        {{-- ... más cards --}}
    </div>

    {{-- 3. FILTER BAR --}}
    <div class="ui-card mb-4" style="--delay:.15s">
        <form>...</form>
    </div>

    {{-- 4. DATA TABLE (Desktop) --}}
    <div class="ui-card d-none d-md-block" style="--delay:.2s">...</div>

    {{-- 5. CARDS GRID (Mobile) --}}
    <div class="d-md-none">...</div>

</div>
</div>
@endsection
```

## Integración con Controller

El controller debe pasar al view:
- `$items` con paginación (`$query->paginate()`)
- Variables de KPI (`$total`, `$activas`, `$bloqueadas`, etc.)
- `$options` para filtros select (`$businessTypes`, `$categories`, etc.)
- `$systemMoneda` para símbolos de moneda
- `$filters` con estado actual de filtros (para pre-seleccionar en selects)

## Reglas Estrictas

1. **SIEMPRE** incluir `@include('partials.premium-ui')` en `@push('styles')`
2. **NUNCA** usar estilos inline hardcodeados para botones — usar `.ui-btn-*` o `.ui-action-*`
3. **SIEMPRE** usar `.ui-badge-*` en vez de `.badge bg-*` de Bootstrap
4. **SIEMPRE** incluir avatar + nombre + subtexto al inicio de cada fila de tabla
5. **Siempre** las acciones van en dropdown `.dropdown` (nunca inline buttons)
6. **Siempre** incluir empty state con `.ui-empty-state` para cuando no hay datos
7. Las cards de KPI deben tener `style="--delay:.{n}s"` para animación escalonada
8. El color acento se define en `--accent` del wrapper `ui-page`
9. **Siempre** usar `.card-footer` para la paginación, nunca inline
10. Para responsive: `d-md-none` para cards/mobile, `d-none d-md-table` para tabla/desktop
11. Las acciones inline con `style="background:..."` están prohibidas — usar `.ui-btn-solid` o `.ui-btn-ghost`
12. No usar `colspan` hardcoded — usar `$columnCount` variable o `count($headers)`

## Checklist de Validación

- [ ] Header animado con gradient + bubbles + avatar
- [ ] 4-6 KPI summary cards con íconos y `--delay`
- [ ] Filter bar con input-group + selects + botones
- [ ] Tabla con avatar circular + nombre + subtexto
- [ ] Badges usan `.ui-badge-*` (no `.badge bg-*`)
- [ ] Acciones en dropdown (no inline buttons)
- [ ] Empty state con `.ui-empty-state`
- [ ] Cards grid para móvil (`d-md-none`)
- [ ] Paginación en `.card-footer`
- [ ] Sin estilos inline para botones (gradient backgrounds)
- [ ] Animación escalonada con `--delay` en cards
- [ ] Responsive probado en mobile

## Mapeo de Colores Estándar

| Color | Hex | Uso |
|-------|-----|-----|
| Primary | `var(--accent,#8b5cf6)` | Color principal del módulo |
| Success | `#10b981` | Activas, completado, éxito |
| Info | `#3b82f6` | Total, datos neutrales |
| Warning | `#f59e0b` | Pendientes, warning, por vencer |
| Danger | `#ef4444` | Bloqueadas, errores, cancelado |
| Secondary | `#64748b` | Neutro, sin datos |

## Mapeo de Íconos Estándar

| Ícono | Uso |
|-------|-----|
| `bi-building` | Instancias, empresas, negocios |
| `bi-list-ul` | Total, lista general |
| `bi-check-circle` | Activo, completado, éxito |
| `bi-exclamation-triangle` | Pendiente, warning, atraso |
| `bi-x-circle` | Inactivo, cancelado, error |
| `bi-lock-fill` | Bloqueado |
| `bi-unlock` | Desbloqueado |
| `bi-cash-coin` | Pagos, finanzas |
| `bi-graph-up` | Crecimiento, trending |
| `bi-bug` | Errores, logs |
| `bi-person-badge` | Usuarios |
| `bi-card-checklist` | Planes, planes |
| `bi-gear` | Configuración |
| `bi-search` | Búsqueda |
| `bi-funnel` | Filtros |
