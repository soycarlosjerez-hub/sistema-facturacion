@extends('layouts.app')
@section('title', 'Técnico: ' . $tecnico->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
.status-badge {
    padding: 0.4em 0.8em;
    border-radius: 2rem;
    font-weight: 500;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}
.info-item {
    padding: 0.75rem 1rem;
    border-radius: 12px;
    background: rgba(var(--accent-rgb, 99,102,241), .05);
    border: 1px solid rgba(255,255,255,.08);
}
.info-item .info-label {
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #64748b;
    font-weight: 600;
}
.info-item .info-value {
    font-size: .95rem;
    font-weight: 600;
    color: #1e293b;
}
body.dark-mode .info-item .info-value { color: #f1f5f9; }
body.dark-mode .info-item .info-label { color: #94a3b8; }
.spec-chip {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .75rem;
    border-radius: 2rem;
    background: rgba(var(--accent-rgb, 99,102,241), .1);
    color: var(--accent, #6366f1);
    font-size: .75rem;
    font-weight: 600;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle" style="width:52px;height:52px;font-size:1.4rem;">
                    <i class="bi bi-person-gear"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $tecnico->nombre }}</h4>
                    <div class="ui-header-meta">
                        <span class="status-badge {{ $tecnico->activo ? 'bg-success' : 'bg-secondary' }} text-white">
                            <i class="bi {{ $tecnico->activo ? 'bi-check-circle-fill' : 'bi-pause-circle-fill' }}"></i>
                            {{ $tecnico->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                        <span class="divider">·</span>
                        <i class="bi bi-tools me-1"></i>{{ $tecnico->especialidad }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('tecnicos.edit')
                <a href="{{ route('tecnicos.edit', $tecnico) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Órdenes Totales</div>
                    <div class="ui-stat-value">{{ $tecnico->ordenesReparacion->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">En Reparación</div>
                    <div class="ui-stat-value" style="color:#f59e0b;">{{ $tecnico->ordenesReparacion->where('estado','en_reparacion')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Entregadas</div>
                    <div class="ui-stat-value" style="color:#22c55e;">{{ $tecnico->ordenesReparacion->where('estado','entregado')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Pendientes</div>
                    <div class="ui-stat-value" style="color:#ef4444;">{{ $tecnico->ordenesReparacion->whereNotIn('estado',['entregado','cancelado'])->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-vcard me-2 text-primary"></i>Información Personal</h6>
                    <div class="row g-2">
                        <div class="col-md-6"><div class="info-item"><div class="info-label">Cédula</div><div class="info-value">{{ $tecnico->cedula ?? '—' }}</div></div></div>
                        <div class="col-md-6"><div class="info-item"><div class="info-label">Teléfono</div><div class="info-value">{{ $tecnico->telefono ?? '—' }}</div></div></div>
                        <div class="col-md-6"><div class="info-item"><div class="info-label">Email</div><div class="info-value">{{ $tecnico->email ?? '—' }}</div></div></div>
                        <div class="col-md-6"><div class="info-item"><div class="info-label">Usuario Vinculado</div><div class="info-value">{{ $tecnico->user?->name ?? '—' }}</div></div></div>
                    </div>

                    <h6 class="fw-bold mt-4 mb-3"><i class="bi bi-cash-coin me-2 text-primary"></i>Tarifas</h6>
                    <div class="row g-2">
                        <div class="col-md-6"><div class="info-item"><div class="info-label">Tarifa por Hora</div><div class="info-value text-primary">RD$ {{ number_format($tecnico->tarifa_hora ?? 0, 2) }}</div></div></div>
                        <div class="col-md-6"><div class="info-item"><div class="info-label">Tarifa Fija</div><div class="info-value text-success">RD$ {{ number_format($tecnico->tarifa_fija ?? 0, 2) }}</div></div></div>
                    </div>

                    <h6 class="fw-bold mt-4 mb-3"><i class="bi bi-award me-2 text-primary"></i>Especialidades</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($tecnico->especialidades as $esp)
                            <span class="spec-chip"><i class="bi bi-patch-check"></i>{{ $esp->nombre }}</span>
                        @empty
                            <span class="text-muted small">{{ $tecnico->especialidad }}</span>
                        @endforelse
                    </div>

                    @if($tecnico->notas)
                    <h6 class="fw-bold mt-4 mb-3"><i class="bi bi-journal-text me-2 text-primary"></i>Notas</h6>
                    <p class="text-muted mb-0" style="white-space:pre-wrap;">{{ $tecnico->notas }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="ui-card h-100" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-wrench-adjustable me-2 text-primary"></i>Últimas Órdenes de Reparación</h6>
                    @forelse($tecnico->ordenesReparacion->sortByDesc('created_at')->take(6) as $orden)
                        <a href="{{ route('tecnicas.show', $orden) }}" class="text-decoration-none">
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2" style="border-color:#f1f5f9 !important;">
                                <div>
                                    <div class="fw-semibold" style="color:#1e293b;">{{ $orden->numero_orden }}</div>
                                    <small class="text-muted">{{ $orden->equipo?->serial_imei ?? $orden->equipo?->marca ?? 'Sin equipo' }} · {{ $orden->cliente?->nombre ?? 'Sin cliente' }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="status-badge bg-info bg-opacity-10 text-info">{{ $orden->estado_label ?? $orden->estado }}</span>
                                    <div class="fw-bold mt-1" style="color:#1e293b;">RD$ {{ number_format($orden->total ?? 0, 2) }}</div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-clipboard-x d-block mb-2" style="font-size:2rem;"></i>
                            Sin órdenes asignadas
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if($tecnico->ordenesReparacion->count() > 6)
    <div class="text-center mb-4">
        <a href="{{ route('tecnicas.index') }}?tecnico_id={{ $tecnico->id }}" class="ui-btn ui-btn-ghost rounded-pill">
            <i class="bi bi-list-ul me-1"></i> Ver todas las órdenes
        </a>
    </div>
    @endif
</div>
@endsection