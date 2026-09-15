@extends('layouts.app')

@section('title', $orden->codigo . ' - Emergencia')

@push('styles')
@include('partials.premium-ui')
<style>
.detail-section { margin-bottom: 1.5rem; }
.detail-section:last-child { margin-bottom: 0; }
.detail-section-title {
    font-size: .8rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    color: #94a3b8;
    margin-bottom: .75rem;
    padding-bottom: .5rem;
    border-bottom: 1px solid #f1f5f9;
}
body.dark-mode .detail-section-title { color: #64748b; border-bottom-color: #1e293b; }

/* Timeline vertical simple */
.timeline-step { display: flex; gap: .75rem; padding: .5rem 0; align-items: flex-start; }
.timeline-dot {
    width: 12px; height: 12px; border-radius: 50%;
    margin-top: 5px; flex-shrink: 0;
    border: 2px solid;
}
.timeline-dot.active   { background: var(--accent); border-color: var(--accent); }
.timeline-dot.pending  { background: transparent; border-color: #cbd5e1; }
body.dark-mode .timeline-dot.pending { border-color: #475569; }
.timeline-content { flex: 1; }
.timeline-content .step-label { font-weight: 600; font-size: .9rem; color: #1e293b; }
.timeline-content .step-date { font-size: .78rem; color: #94a3b8; }
body.dark-mode .timeline-content .step-label { color: #f1f5f9; }
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
                    <h4 class="ui-header-title">{{ $orden->codigo }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-info-circle me-1"></i>Detalle de orden de emergencia
                        <span class="divider">·</span>
                        <i class="bi bi-calendar me-1"></i>
                        {{ $orden->created_at?->format('d/m/Y h:i A') ?? '—' }}
                        <span class="divider">·</span>
                        <span class="ui-badge {{ match($orden->estado) {
                            'reportada' => 'ui-badge-danger',
                            'asignada'  => 'ui-badge-warning',
                            'en_camino' => 'ui-badge-primary',
                            'en_lugar'  => 'ui-badge-info',
                            'resuelta'  => 'ui-badge-success',
                            'cerrada'   => 'ui-badge-neutral',
                            default     => 'ui-badge-neutral',
                        } }}" style="font-size:.75rem;">
                            {{ \App\Models\OrdenEmergencia::ESTADOS[$orden->estado] ?? $orden->estado }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('climatizacion.ordenes-emergencia.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
                @if($orden->estado !== 'cerrada')
                    <a href="{{ route('climatizacion.ordenes-emergencia.edit', $orden) }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- ============================================================
             COLUMNA IZQUIERDA — Detalles principales
             ============================================================ --}}
        <div class="col-lg-8">

            {{-- Información General --}}
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-file-text"></i> Información General
                </div>
                <div class="ui-card-body">
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Código</span>
                        <span class="ui-detail-value fw-bold" style="color:var(--accent);">{{ $orden->codigo }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Cliente</span>
                        <span class="ui-detail-value">
                            {{ $orden->cliente?->nombre ?? '—' }}
                            @if($orden->cliente?->identificacion)
                                <small class="text-muted">({{ $orden->cliente->identificacion }})</small>
                            @endif
                        </span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Dirección</span>
                        <span class="ui-detail-value">{{ $orden->direccion ?: '—' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Teléfono Contacto</span>
                        <span class="ui-detail-value">{{ $orden->contacto_telefono ?: '—' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Prioridad</span>
                        <span class="ui-detail-value">
                            @php
                                $pClass = match($orden->prioridad) {
                                    'critica' => 'badge-prioridad-critica',
                                    'alta'    => 'badge-prioridad-alta',
                                    'media'   => 'badge-prioridad-media',
                                    'baja'    => 'badge-prioridad-baja',
                                    default   => 'badge-prioridad-media',
                                };
                            @endphp
                            <span class="badge rounded-pill {{ $pClass }}">
                                {{ \App\Models\OrdenEmergencia::PRIORIDADES[$orden->prioridad] ?? $orden->prioridad }}
                            </span>
                        </span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Tipo de Falla</span>
                        <span class="ui-detail-value">{{ \App\Models\OrdenEmergencia::TIPOS_FALLA[$orden->tipo_falla] ?? $orden->tipo_falla }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Estado</span>
                        <span class="ui-detail-value">
                            @php
                                $eBadge = match($orden->estado) {
                                    'reportada' => 'ui-badge-danger',
                                    'asignada'  => 'ui-badge-warning',
                                    'en_camino' => 'ui-badge-primary',
                                    'en_lugar'  => 'ui-badge-info',
                                    'resuelta'  => 'ui-badge-success',
                                    'cerrada'   => 'ui-badge-neutral',
                                    default     => 'ui-badge-neutral',
                                };
                                $eIcon = match($orden->estado) {
                                    'reportada' => 'exclamation-circle',
                                    'asignada'  => 'person-check',
                                    'en_camino' => 'truck',
                                    'en_lugar'  => 'tools',
                                    'resuelta'  => 'check-circle',
                                    'cerrada'   => 'lock',
                                    default     => 'circle',
                                };
                            @endphp
                            <span class="ui-badge {{ $eBadge }}">
                                <i class="bi bi-{{ $eIcon }} me-1"></i>
                                {{ \App\Models\OrdenEmergencia::ESTADOS[$orden->estado] ?? $orden->estado }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Descripción --}}
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-chat-dots"></i> Descripción del Problema
                </div>
                <div class="ui-card-body">
                    <p class="mb-0" style="color:#334155;line-height:1.7;">
                        {{ $orden->descripcion ?: 'Sin descripción' }}
                    </p>
                </div>
            </div>

            {{-- Costos --}}
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-currency-dollar"></i> Costos
                </div>
                <div class="ui-card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ui-detail-row">
                                <span class="ui-detail-label">Costo Estimado</span>
                                <span class="ui-detail-value fw-bold">
                                    {{ $orden->costo_estimado ? 'RD$ '.number_format($orden->costo_estimado, 2) : '—' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ui-detail-row">
                                <span class="ui-detail-label">Costo Final</span>
                                <span class="ui-detail-value fw-bold">
                                    {{ $orden->costo_final ? 'RD$ '.number_format($orden->costo_final, 2) : '—' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             COLUMNA DERECHA — Técnico, SLA, Timeline
             ============================================================ --}}
        <div class="col-lg-4">

            {{-- Técnico Asignado --}}
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-person-badge"></i> Técnico
                </div>
                <div class="ui-card-body text-center">
                    @if($orden->tecnico)
                        <div class="ui-user-avatar ui-user-avatar-green mx-auto mb-2">
                            <i class="bi bi-person-fs"></i>
                        </div>
                        <div class="fw-bold">{{ $orden->tecnico->name }}</div>
                        <small class="text-muted">{{ $orden->tecnico->email }}</small>

                        @if($orden->estado === 'asignada' || $orden->estado === 'en_camino' || $orden->estado === 'en_lugar')
                            <div class="mt-2">
                                <span class="ui-badge ui-badge-info">
                                    <i class="bi bi-clock me-1"></i>
                                    Tiempo respuesta:
                                    @if($orden->tiempoRespuestaMinutos() !== null)
                                        {{ $orden->tiempoRespuestaMinutos() }} min
                                    @else
                                        En curso...
                                    @endif
                                </span>
                            </div>
                        @endif
                    @else
                        <div class="ui-user-avatar ui-user-avatar-amber mx-auto mb-2">
                            <i class="bi bi-question"></i>
                        </div>
                        <p class="text-muted mb-2">Sin técnico asignado</p>

                        {{-- Botón Asignar — solo si está reportada --}}
                        @if($orden->estado === 'reportada')
                            <button type="button" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill w-100"
                                    data-bs-toggle="modal" data-bs-target="#asignarModal">
                                <i class="bi bi-person-plus me-1"></i> Asignar Técnico
                            </button>
                        @endif
                    @endif
                </div>
            </div>

            {{-- SLA --}}
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-stopwatch"></i> SLA
                </div>
                <div class="ui-card-body">
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Deadline</span>
                        <span class="ui-detail-value">
                            {{ $orden->sla_deadline?->format('d/m/Y h:i A') ?? '—' }}
                        </span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Estado</span>
                        <span class="ui-detail-value">
                            @php
                                $sla = $orden->slaCumplido();
                            @endphp
                            @if(is_null($sla))
                                <span class="ui-badge ui-badge-neutral">
                                    <i class="bi bi-dash-circle me-1"></i> N/A
                                </span>
                            @elseif($sla)
                                <span class="ui-badge ui-badge-success">
                                    <i class="bi bi-check-circle me-1"></i> Cumplido
                                </span>
                            @else
                                <span class="ui-badge ui-badge-danger">
                                    <i class="bi bi-x-circle me-1"></i> Vencido
                                </span>
                            @endif
                        </span>
                    </div>
                    @if($orden->tiempoRespuestaMinutos() !== null)
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Tiempo Respuesta</span>
                            <span class="ui-detail-value">{{ $orden->tiempoRespuestaMinutos() }} min</span>
                        </div>
                    @endif
                    @if(method_exists($orden, 'tiempoResolutionMinutos') && $orden->tiempoResolutionMinutos() !== null)
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Tiempo Resolución</span>
                            <span class="ui-detail-value">{{ $orden->tiempoResolutionMinutos() }} min</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Timeline --}}
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-diagram-3"></i> Progreso
                </div>
                <div class="ui-card-body">
                    @php
                        $estadosTimeline = ['reportada', 'asignada', 'en_camino', 'en_lugar', 'resuelta', 'cerrada'];
                        $currentIdx = array_search($orden->estado, $estadosTimeline);
                    @endphp
                    @foreach (\App\Models\OrdenEmergencia::ESTADOS as $key => $label)
                        @php
                            $idx = array_search($key, $estadosTimeline);
                            $isActive = $idx <= $currentIdx;
                            $isCurrent = $key === $orden->estado;
                        @endphp
                        <div class="timeline-step">
                            <div class="timeline-dot {{ $isActive ? 'active' : 'pending' }}"></div>
                            <div class="timeline-content">
                                <div class="step-label {{ $isCurrent ? '' : 'opacity-75' }}">
                                    {{ $label }}
                                    @if($isCurrent)
                                        <span class="ui-badge ui-badge-primary" style="font-size:.65rem;padding:.15rem .45rem;">Actual</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Creado por --}}
            <div class="ui-card" style="--delay:.25s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-person-circle"></i> Registro
                </div>
                <div class="ui-card-body">
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Creado por</span>
                        <span class="ui-detail-value">{{ $orden->creadoPor?->name ?? '—' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Creado</span>
                        <span class="ui-detail-value">{{ $orden->created_at?->format('d/m/Y h:i A') ?? '—' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Actualizado</span>
                        <span class="ui-detail-value">{{ $orden->updated_at?->format('d/m/Y h:i A') ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="d-grid gap-2" style="--delay:.3s">
                {{-- Asignar Técnico (si está reportada y no tiene técnico) --}}
                @if($orden->estado === 'reportada' && !$orden->tecnico_id)
                    <button type="button" class="ui-btn ui-btn-solid rounded-pill"
                            data-bs-toggle="modal" data-bs-target="#asignarModal">
                        <i class="bi bi-person-plus me-1"></i> Asignar Técnico
                    </button>
                @endif

                {{-- Cerrar (si está resuelta) --}}
                @if($orden->estado === 'resuelta')
                    <form action="{{ route('climatizacion.ordenes-emergencia.cerrar', $orden) }}" method="POST"
                          onsubmit="return confirm('¿Cerrar esta orden de emergencia?');">
                        @csrf @method('PATCH')
                        <button type="submit" class="ui-btn ui-btn-ghost rounded-pill w-100">
                            <i class="bi bi-lock me-1"></i> Cerrar Orden
                        </button>
                    </form>
                @endif

                {{-- Facturar (si está resuelta/cerrada y tiene costo) --}}
                @if(in_array($orden->estado, ['resuelta', 'cerrada']) && ($orden->costo_final ?? 0) > 0)
                    @php
                        $yaFacturado = \App\Models\ClimatizacionFactura::where('origen', 'emergencia')
                            ->where('origen_id', $orden->id)->exists();
                    @endphp
                    @if($yaFacturado)
                        @php
                            $factura = \App\Models\ClimatizacionFactura::where('origen', 'emergencia')
                                ->where('origen_id', $orden->id)->first();
                        @endphp
                        <a href="{{ route('climatizacion.facturas.show', $factura) }}" class="ui-btn ui-btn-solid rounded-pill w-100">
                            <i class="bi bi-receipt me-1"></i> Ver Factura
                        </a>
                    @else
                        <form action="{{ route('climatizacion.facturas.desde.emergencia', $orden) }}" method="POST" class="d-inline w-100">
                            @csrf
                            <button type="submit" class="ui-btn ui-btn-solid rounded-pill w-100"
                                    onclick="return confirm('¿Generar factura por RD$ {{ number_format($orden->costo_final, 2) }}?');">
                                <i class="bi bi-receipt-cutoff me-1"></i> Facturar
                            </button>
                        </form>
                    @endif
                @endif

                {{-- Editar (si no está cerrada) --}}
                @if($orden->estado !== 'cerrada')
                    <a href="{{ route('climatizacion.ordenes-emergencia.edit', $orden) }}" class="ui-btn ui-btn-ghost rounded-pill">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ============================================================
     MODAL ASIGNAR TÉCNICO
     ============================================================ --}}
@if($orden->estado === 'reportada')
<div class="modal fade" id="asignarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:1.25rem;">
            <form action="{{ route('climatizacion.ordenes-emergencia.asignar-tecnico', $orden) }}" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-plus me-2" style="color:var(--accent);"></i>Asignar Técnico
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <label class="ui-label">Seleccionar Técnico <span class="text-danger">*</span></label>
                    <select name="tecnico_id" class="ui-select" required>
                        <option value="">Seleccionar...</option>
                        @foreach ($tecnicos ?? [] as $tecnico)
                            <option value="{{ $tecnico->id }}">{{ $tecnico->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-info-circle me-1"></i>
                        Al asignar, la orden pasará a estado "Asignada".
                    </small>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Asignar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection