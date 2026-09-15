@extends('layouts.app')

@section('title', $ticket->codigo . ' - Ticket Garantía')

@push('styles')
@include('partials.premium-ui')
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; }

.vigencia-card {
    border-radius: var(--radius-2xl);
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.vigencia-card.vigente {
    background: rgba(34,197,94,.08);
    border: 1px solid rgba(34,197,94,.2);
}
.vigencia-card.vencida {
    background: rgba(239,68,68,.08);
    border: 1px solid rgba(239,68,68,.2);
}
.vigencia-card .vigencia-icon {
    width: 48px; height: 48px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}
.vigencia-card.vigente .vigencia-icon {
    background: rgba(34,197,94,.15);
    color: #16a34a;
}
.vigencia-card.vencida .vigencia-icon {
    background: rgba(239,68,68,.15);
    color: #dc2626;
}
.vigencia-card .vigencia-text { flex: 1; }
.vigencia-card .vigencia-text h5 {
    font-weight: 700;
    margin: 0;
}
.vigencia-card.vigente .vigencia-text h5 { color: #16a34a; }
.vigencia-card.vencida .vigencia-text h5 { color: #dc2626; }
.vigencia-card .vigencia-text small { color: #64748b; }

/* Timeline / estado */
.estado-timeline {
    display: flex;
    align-items: center;
    gap: .25rem;
    flex-wrap: wrap;
}
.estado-step {
    display: flex; align-items: center; gap: .4rem;
    padding: .5rem 1rem;
    border-radius: 999px;
    font-size: .8rem;
    font-weight: 600;
    background: #f1f5f9;
    color: #94a3b8;
    border: 1px solid #e2e8f0;
}
.estado-step.active {
    background: rgba(6,182,212,.1);
    color: #0891b2;
    border-color: rgba(6,182,212,.25);
}
.estado-step.completed {
    background: rgba(34,197,94,.1);
    color: #16a34a;
    border-color: rgba(34,197,94,.2);
}
.estado-step.rejected {
    background: rgba(239,68,68,.1);
    color: #dc2626;
    border-color: rgba(239,68,68,.2);
}

.action-section {
    background: rgba(255,255,255,.5);
    border-radius: var(--radius-2xl);
    border: 1px solid rgba(255,255,255,.8);
    padding: 1.25rem 1.75rem;
}

body.dark-mode .ui-detail-row { border-bottom-color: #1e293b; }
body.dark-mode .ui-detail-label { color: #94a3b8; }
body.dark-mode .ui-detail-value { color: #cbd5e1; }
body.dark-mode .estado-step { background: #1e293b; border-color: #334155; color: #64748b; }
body.dark-mode .action-section { background: rgba(15,23,42,.5); border-color: rgba(255,255,255,.08); }
body.dark-mode .vigencia-card .vigencia-text small { color: #94a3b8; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="ui-header" style="--delay:0s;">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-ticket-perforated"></i>
                </div>
                <div>
                    <h1 class="ui-header-title">{{ $ticket->codigo }}</h1>
                    <div class="ui-header-meta">
                        <span>Ticket de Garantía</span>
                        <span class="divider">·</span>
                        <span>{{ $ticket->cliente?->nombre ?? 'Sin cliente' }}</span>
                        <span class="divider">·</span>
                        <span>
                            @php
                                $estadoColor = match($ticket->estado) {
                                    'abierto' => 'var(--accent)',
                                    'evaluando' => '#f59e0b',
                                    'aprobado' => '#16a34a',
                                    'rechazado' => '#dc2626',
                                    'cerrado' => '#64748b',
                                    default => '#64748b',
                                };
                            @endphp
                            <span class="ui-badge" style="background:rgba({{ $ticket->estado === 'abierto' ? '6,182,212' : ($ticket->estado === 'evaluando' ? '245,158,11' : ($ticket->estado === 'aprobado' ? '34,197,94' : ($ticket->estado === 'rechazado' ? '239,68,68' : '100,116,139')) ) }},.1);color:{{ $estadoColor }};border-color:rgba({{ $ticket->estado === 'abierto' ? '6,182,212' : ($ticket->estado === 'evaluando' ? '245,158,11' : ($ticket->estado === 'aprobado' ? '34,197,94' : ($ticket->estado === 'rechazado' ? '239,68,68' : '100,116,139')) ) }},.2);">
                                {{ \App\Models\TicketGarantia::ESTADOS[$ticket->estado] ?? $ticket->estado }}
                            </span>
                        </span>
                        <span class="divider">·</span>
                        <a href="{{ route('climatizacion.tickets-garantia.index') }}" style="color:rgba(255,255,255,.8);text-decoration:none;">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                @if(!in_array($ticket->estado, ['abierto']))
                    <a href="{{ route('climatizacion.tickets-garantia.edit', $ticket) }}" class="ui-btn ui-btn-primary ui-btn-pill">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- ============================================================
             COL IZQUIERDA — Detalles del ticket
             ============================================================ --}}
        <div class="col-lg-7">

            {{-- Vigencia Card --}}
            @php $vigente = $ticket->estaVigente(); @endphp
            <div class="vigencia-card {{ $vigente ? 'vigente' : 'vencida' }}" style="--delay:.1s;animation:uiSlideUp .5s ease both;">
                <div class="vigencia-icon">
                    <i class="bi bi-{{ $vigente ? 'shield-check' : 'shield-exclamation' }}"></i>
                </div>
                <div class="vigencia-text">
                    <h5>
                        <i class="bi bi-{{ $vigente ? 'check-circle-fill' : 'x-circle-fill' }} me-1"></i>
                        Garantía {{ $vigente ? 'Vigente' : 'Vencida' }}
                    </h5>
                    <small>
                        @if($vigente)
                            Quedan <strong>{{ $ticket->diasRestantes() }} día{{ $ticket->diasRestantes() !== 1 ? 's' : '' }}</strong>
                            de garantía (vence el {{ $ticket->fecha_vencimiento_garantia?->format('d/m/Y') ?? 'N/A' }})
                        @else
                            Venció el {{ $ticket->fecha_vencimiento_garantia?->format('d/m/Y') ?? 'N/A' }}
                            · {{ $ticket->diasRestantes() }} día{{ $ticket->diasRestantes() !== 1 ? 's' : '' }} desde vencimiento
                        @endif
                    </small>
                </div>
            </div>

            {{-- Estado Timeline --}}
            <div class="ui-card" style="--delay:.15s;">
                <div style="height:4px;background:linear-gradient(90deg, #06b6d4, rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <div class="ui-card-title" style="padding:0 0 .75rem;margin:0;">
                        <i class="bi bi-diagram-3"></i> Estado del Ticket
                    </div>
                    <div class="estado-timeline">
                        @php
                            $order = ['abierto','evaluando','aprobado','rechazado','cerrado'];
                            $currentIdx = array_search($ticket->estado, $order);
                        @endphp
                        @foreach($order as $idx => $est)
                            @if($est === 'rechazado' && $ticket->estado !== 'rechazado') @continue @endif
                            @php
                                $cls = '';
                                if ($idx < $currentIdx) $cls = 'completed';
                                elseif ($idx === $currentIdx) $cls = $ticket->estado === 'rechazado' ? 'rejected' : 'active';
                            @endphp
                            <span class="estado-step {{ $cls }}">
                                @if($cls === 'completed') <i class="bi bi-check-circle-fill"></i>
                                @elseif($cls === 'rejected') <i class="bi bi-x-circle-fill"></i>
                                @elseif($cls === 'active') <i class="bi bi-arrow-right-circle-fill"></i>
                                @else <i class="bi bi-circle"></i>
                                @endif
                                {{ \App\Models\TicketGarantia::ESTADOS[$est] ?? $est }}
                            </span>
                            @if($idx < count($order) - 2 && $idx < $currentIdx && $ticket->estado !== 'rechazado')
                                <i class="bi bi-chevron-right text-muted" style="font-size:.7rem;"></i>
                            @endif
                        @endforeach
                    </div>
                    @if($ticket->cerrado_en)
                        <div class="mt-2 small text-muted">
                            <i class="bi bi-clock-history"></i> Cerrado el {{ $ticket->cerrado_en->format('d/m/Y h:i A') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Detalles --}}
            <div class="ui-card" style="--delay:.2s;">
                <div style="height:4px;background:linear-gradient(90deg, #06b6d4, rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <div class="ui-card-title" style="padding:0 0 .75rem;margin:0;">
                        <i class="bi bi-info-circle"></i> Información del Ticket
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Código</span>
                        <span class="ui-detail-value fw-bold">{{ $ticket->codigo }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Cliente</span>
                        <span class="ui-detail-value">{{ $ticket->cliente?->nombre ?? '-' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Producto</span>
                        <span class="ui-detail-value">{{ $ticket->producto?->nombre ?? 'No especificado' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Instalación</span>
                        <span class="ui-detail-value">{{ $ticket->instalacion ? '#'.$ticket->instalacion->id : 'No relacionada' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Tipo Garantía</span>
                        <span class="ui-detail-value">
                            <span class="ui-badge ui-badge-primary">
                                {{ \App\Models\TicketGarantia::TIPOS[$ticket->tipo_garantia] ?? $ticket->tipo_garantia }}
                            </span>
                        </span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Fecha Compra</span>
                        <span class="ui-detail-value">{{ $ticket->fecha_compra?->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Vencimiento</span>
                        <span class="ui-detail-value">{{ $ticket->fecha_vencimiento_garantia?->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Días Restantes</span>
                        <span class="ui-detail-value">
                            <span class="fw-bold" style="color:{{ $ticket->diasRestantes() > 30 ? '#16a34a' : ($ticket->diasRestantes() > 0 ? '#d97706' : '#dc2626') }};">
                                {{ $ticket->diasRestantes() }} día{{ $ticket->diasRestantes() !== 1 ? 's' : '' }}
                            </span>
                        </span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Técnico Asignado</span>
                        <span class="ui-detail-value">{{ $ticket->tecnicoAsignado?->name ?? 'Sin asignar' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Creado por</span>
                        <span class="ui-detail-value">{{ $ticket->creadoPor?->name ?? '-' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Creado el</span>
                        <span class="ui-detail-value">{{ $ticket->created_at?->format('d/m/Y h:i A') ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             COL DERECHA — Descripción + Evaluación + Acciones
             ============================================================ --}}
        <div class="col-lg-5">

            {{-- Descripción del Problema --}}
            <div class="ui-card" style="--delay:.15s;">
                <div style="height:4px;background:linear-gradient(90deg, #06b6d4, rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <div class="ui-card-title" style="padding:0 0 .75rem;margin:0;">
                        <i class="bi bi-chat-square-text"></i> Problema Reportado
                    </div>
                    <p style="color:#475569;line-height:1.6;font-size:.9rem;white-space:pre-wrap;">{{ $ticket->descripcion_problema ?? 'Sin descripción' }}</p>
                </div>
            </div>

            {{-- Resultado Evaluación --}}
            @if($ticket->resultado_evaluacion)
            <div class="ui-card" style="--delay:.2s;">
                <div style="height:4px;background:linear-gradient(90deg, #f59e0b, rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <div class="ui-card-title" style="padding:0 0 .75rem;margin:0;">
                        <i class="bi bi-clipboard-check" style="color:#f59e0b;"></i> Evaluación
                    </div>
                    <p style="color:#475569;line-height:1.6;font-size:.9rem;white-space:pre-wrap;">{{ $ticket->resultado_evaluacion }}</p>
                    @if($ticket->accion)
                    <div class="mt-2">
                        <span class="ui-label" style="font-size:.75rem;">Acción tomada:</span>
                        <span class="ui-badge ui-badge-primary" style="text-transform:capitalize;">
                            {{ $ticket->accion }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Formulario de Evaluar (solo si abierto) --}}
            @if($ticket->estado === 'abierto')
            <div class="action-section" style="--delay:.25s;animation:uiSlideUp .5s ease both;">
                <h5 class="fw-bold mb-3" style="color:#1e293b;">
                    <i class="bi bi-clipboard-check me-2" style="color:#06b6d4;"></i>Evaluar Ticket
                </h5>

                {{-- Evaluar / Aprobar --}}
                <form action="{{ route('climatizacion.tickets-garantia.evaluar', $ticket) }}" method="POST" class="mb-3">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="ui-label">Resultado de Evaluación <span class="text-danger">*</span></label>
                        <textarea name="resultado_evaluacion" rows="3" class="ui-textarea"
                                  placeholder="Describe el resultado de la evaluación técnica…" required></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="ui-label">Acción <span class="text-danger">*</span></label>
                            <select name="accion" class="ui-select" required>
                                <option value="">Seleccionar</option>
                                <option value="reparar">Reparar</option>
                                <option value="reemplazar">Reemplazar</option>
                                <option value="devolver">Devolver</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="ui-label">Técnico Asignado</label>
                            <select name="tecnico_asignado_id" class="ui-select">
                                <option value="">Sin asignar</option>
                                @foreach(\App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'tecnico'))->orWhere('id', $ticket->tecnico_asignado_id)->orderBy('name')->get() as $u)
                                    <option value="{{ $u->id }}" {{ $ticket->tecnico_asignado_id == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="ui-btn ui-btn-solid w-100">
                        <i class="bi bi-check-circle"></i> Aprobar Ticket
                    </button>
                </form>

                <hr style="border-color:#e2e8f0;">

                {{-- Rechazar --}}
                <form action="{{ route('climatizacion.tickets-garantia.rechazar', $ticket) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="ui-label">Motivo de Rechazo <span class="text-danger">*</span></label>
                        <textarea name="resultado_evaluacion" rows="2" class="ui-textarea"
                                  placeholder="Indica por qué se rechaza la garantía…" required></textarea>
                    </div>
                    <button type="submit" class="ui-btn ui-btn-danger w-100"
                            onclick="return confirm('¿Rechazar este ticket de garantía? Esta acción no se puede deshacer.');">
                        <i class="bi bi-x-circle"></i> Rechazar Ticket
                    </button>
                </form>
            </div>
            @endif

            {{-- Acciones adicionales --}}
            <div class="d-flex gap-2 mt-3" style="--delay:.3s;animation:uiSlideUp .5s ease both;">
                <a href="{{ route('climatizacion.tickets-garantia.index') }}" class="ui-btn ui-btn-ghost flex-fill">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                @if(!in_array($ticket->estado, ['abierto']) && !in_array($ticket->estado, ['evaluando']))
                    <a href="{{ route('climatizacion.tickets-garantia.edit', $ticket) }}" class="ui-btn ui-btn-solid flex-fill">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection