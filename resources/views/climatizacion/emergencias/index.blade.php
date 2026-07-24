@extends('layouts.app')

@section('title', 'Órdenes de Emergencia')

@push('styles')
@include('partials.premium-ui')
<style>
:root {
    --dt-accent: #ef4444;
    --dt-accent-gradient: linear-gradient(135deg, #ef4444, #f97316);
    --dt-accent-rgb: 239, 68, 68;
}
/* Prioridad badges */
.badge-prioridad-critica  { background: rgba(239,68,68,.12); color: #dc2626; border: 1px solid rgba(239,68,68,.25); }
.badge-prioridad-alta     { background: rgba(245,158,11,.12); color: #d97706; border: 1px solid rgba(245,158,11,.25); }
.badge-prioridad-media    { background: rgba(59,130,246,.12); color: #2563eb; border: 1px solid rgba(59,130,246,.25); }
.badge-prioridad-baja     { background: rgba(100,116,139,.12); color: #475569; border: 1px solid rgba(100,116,139,.25); }

body.dark-mode .badge-prioridad-critica { background: rgba(239,68,68,.15); color: #f87171; border-color: rgba(239,68,68,.3); }
body.dark-mode .badge-prioridad-alta    { background: rgba(245,158,11,.15); color: #fbbf24; border-color: rgba(245,158,11,.3); }
body.dark-mode .badge-prioridad-media   { background: rgba(59,130,246,.15); color: #60a5fa; border-color: rgba(59,130,246,.3); }
body.dark-mode .badge-prioridad-baja    { background: rgba(100,116,139,.15); color: #94a3b8; border-color: rgba(100,116,139,.3); }

/* SLA badge */
.badge-sla-ok    { background: rgba(34,197,94,.12); color: #16a34a; border: 1px solid rgba(34,197,94,.25); }
.badge-sla-fail  { background: rgba(239,68,68,.12); color: #dc2626; border: 1px solid rgba(239,68,68,.25); }
.badge-sla-na    { background: rgba(100,116,139,.12); color: #64748b; border: 1px solid rgba(100,116,139,.2); }

body.dark-mode .badge-sla-ok   { background: rgba(34,197,94,.15); color: #4ade80; border-color: rgba(34,197,94,.3); }
body.dark-mode .badge-sla-fail { background: rgba(239,68,68,.15); color: #f87171; border-color: rgba(239,68,68,.3); }
body.dark-mode .badge-sla-na   { background: rgba(100,116,139,.15); color: #94a3b8; border-color: rgba(100,116,139,.3); }

/* Técnico badge */
.tecnico-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .25rem .65rem;
    border-radius: 999px;
    background: rgba(99,102,241,.1);
    color: #6366f1;
    border: 1px solid rgba(99,102,241,.2);
    font-size: .78rem;
    font-weight: 500;
}
body.dark-mode .tecnico-badge {
    background: rgba(99,102,241,.15);
    color: #818cf8;
    border-color: rgba(99,102,241,.3);
}

/* Filtros card */
.filtros-card .ui-select, .filtros-card .ui-input {
    font-size: .82rem;
    padding: .4rem .85rem;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#ef4444;--accent-rgb:239,68,68;--accent-hover:#dc2626;">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="ui-header" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Órdenes de Emergencia</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-info-circle me-1"></i>Gestión de emergencias de climatización
                        <span class="divider">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        <span>{{ $ordenes->total() }} registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('climatizacion.ordenes-emergencia.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Emergencia
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================================
         STATS CARDS
         ============================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6">
            <div class="ui-stat" style="--delay:.05s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">
                        <i class="bi bi-fire me-1"></i>Críticas sin Asignar
                    </div>
                    <div class="ui-stat-value">{{ $criticas }}</div>
                    <div class="ui-stat-sub">Requieren atención inmediata</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">
                        <i class="bi bi-clock-history me-1"></i>Órdenes Activas
                    </div>
                    <div class="ui-stat-value">{{ $activas }}</div>
                    <div class="ui-stat-sub">En proceso (no resueltas/cerradas)</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         FILTROS
         ============================================================ --}}
    <div class="ui-card filtros-card" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-lg-3">
                    <label class="ui-label" style="font-size:.75rem;">Buscar</label>
                    <input type="text" name="search" class="ui-input" placeholder="Código, cliente, dirección..." value="{{ request('search') }}" autocomplete="off">
                </div>
                <div class="col-lg-2">
                    <label class="ui-label" style="font-size:.75rem;">Prioridad</label>
                    <select name="prioridad" class="ui-select">
                        <option value="">Todas</option>
                        @foreach(\App\Models\OrdenEmergencia::PRIORIDADES as $key => $label)
                            <option value="{{ $key }}" {{ request('prioridad') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="ui-label" style="font-size:.75rem;">Estado</label>
                    <select name="estado" class="ui-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\OrdenEmergencia::ESTADOS as $key => $label)
                            <option value="{{ $key }}" {{ request('estado') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="ui-label" style="font-size:.75rem;">Tipo Falla</label>
                    <select name="tipo_falla" class="ui-select">
                        <option value="">Todas</option>
                        @foreach(\App\Models\OrdenEmergencia::TIPOS_FALLA as $key => $label)
                            <option value="{{ $key }}" {{ request('tipo_falla') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid ui-btn-sm flex-grow-1 rounded-pill">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('climatizacion.ordenes-emergencia.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================
         TABLA
         ============================================================ --}}
    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Cliente</th>
                            <th>Prioridad</th>
                            <th>Tipo Falla</th>
                            <th>Dirección</th>
                            <th>Técnico</th>
                            <th>Estado</th>
                            <th>SLA</th>
                            <th class="text-end">Costo Est.</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ordenes as $orden)
                        <tr>
                            <td class="fw-bold" style="color:var(--accent);">
                                <a href="{{ route('climatizacion.ordenes-emergencia.show', $orden) }}" class="text-decoration-none" style="color:inherit;">
                                    {{ $orden->codigo }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $orden->cliente?->nombre ?? '—' }}</div>
                                @if($orden->contacto_telefono)
                                    <small class="text-muted">{{ $orden->contacto_telefono }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $prioridadClass = match($orden->prioridad) {
                                        'critica' => 'badge-prioridad-critica',
                                        'alta'    => 'badge-prioridad-alta',
                                        'media'   => 'badge-prioridad-media',
                                        'baja'    => 'badge-prioridad-baja',
                                        default   => 'badge-prioridad-media',
                                    };
                                @endphp
                                <span class="badge rounded-pill {{ $prioridadClass }}">
                                    {{ \App\Models\OrdenEmergencia::PRIORIDADES[$orden->prioridad] ?? $orden->prioridad }}
                                </span>
                            </td>
                            <td>
                                <span class="small">{{ \App\Models\OrdenEmergencia::TIPOS_FALLA[$orden->tipo_falla] ?? $orden->tipo_falla }}</span>
                            </td>
                            <td>
                                <span class="small text-muted d-block" style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $orden->direccion }}">
                                    {{ $orden->direccion ?: '—' }}
                                </span>
                            </td>
                            <td>
                                @if($orden->tecnico)
                                    <span class="tecnico-badge">
                                        <i class="bi bi-person-badge"></i>
                                        {{ $orden->tecnico->name }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $estadoBadge = match($orden->estado) {
                                        'reportada' => 'ui-badge-danger',
                                        'asignada'  => 'ui-badge-warning',
                                        'en_camino' => 'ui-badge-primary',
                                        'en_lugar'  => 'ui-badge-info',
                                        'resuelta'  => 'ui-badge-success',
                                        'cerrada'   => 'ui-badge-neutral',
                                        default     => 'ui-badge-neutral',
                                    };
                                    $estadoIcon = match($orden->estado) {
                                        'reportada' => 'exclamation-circle',
                                        'asignada'  => 'person-check',
                                        'en_camino' => 'truck',
                                        'en_lugar'  => 'tools',
                                        'resuelta'  => 'check-circle',
                                        'cerrada'   => 'lock',
                                        default     => 'circle',
                                    };
                                @endphp
                                <span class="ui-badge {{ $estadoBadge }}">
                                    <i class="bi bi-{{ $estadoIcon }} me-1"></i>
                                    {{ \App\Models\OrdenEmergencia::ESTADOS[$orden->estado] ?? $orden->estado }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $sla = $orden->slaCumplido();
                                @endphp
                                @if(is_null($sla))
                                    <span class="badge rounded-pill badge-sla-na">
                                        <i class="bi bi-dash-circle me-1"></i> N/A
                                    </span>
                                @elseif($sla)
                                    <span class="badge rounded-pill badge-sla-ok">
                                        <i class="bi bi-check-circle me-1"></i> OK
                                    </span>
                                @else
                                    <span class="badge rounded-pill badge-sla-fail">
                                        <i class="bi bi-x-circle me-1"></i> Vencido
                                    </span>
                                @endif
                            </td>
                            <td class="text-end fw-bold" style="color:var(--accent);">
                                {{ $orden->costo_estimado ? 'RD$ '.number_format($orden->costo_estimado, 2) : '—' }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('climatizacion.ordenes-emergencia.show', $orden) }}"
                                       class="ui-action ui-action-view" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($orden->estado !== 'cerrada')
                                        <a href="{{ route('climatizacion.ordenes-emergencia.edit', $orden) }}"
                                           class="ui-action ui-action-edit" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('climatizacion.ordenes-emergencia.destroy', $orden) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return UI?.confirm?.delete?.('{{ route('climatizacion.ordenes-emergencia.destroy', $orden) }}', '{{ addslashes($orden->codigo) }}') || confirm('¿Eliminar {{ addslashes($orden->codigo) }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="ui-action ui-action-delete border-0" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10">
                                <div class="ui-empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>No hay órdenes de emergencia</p>
                                    <a href="{{ route('climatizacion.ordenes-emergencia.create') }}" class="ui-btn ui-btn-solid ui-btn-sm mt-2 rounded-pill">
                                        <i class="bi bi-plus-lg"></i> Crear primera
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($ordenes->hasPages())
            <div class="p-3 border-top border-light">
                {{ $ordenes->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

</div>
@endsection