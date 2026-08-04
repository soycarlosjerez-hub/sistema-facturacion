@extends('layouts.app')

@section('title', 'Seguimiento #' . $tracking->orden_id)

@push('styles')
@include('partials.premium-ui')
<style>
/* Tracking Show Styles */
.timeline-container {
    position: relative;
    padding-left: 2.5rem;
}
.timeline-container::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
}
.timeline-item {
    position: relative;
    padding-bottom: 1.75rem;
}
.timeline-item:last-child { padding-bottom: 0; }
.timeline-dot {
    position: absolute;
    left: -2.5rem;
    top: 2px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2.5px solid;
    background: #fff;
    z-index: 1;
}
.timeline-dot.completed { background: #16a34a; border-color: #16a34a; }
.timeline-dot.current { background: #0ea5e9; border-color: #0ea5e9; animation: uiPulse 2s infinite; }
.timeline-dot.pending { background: #fff; border-color: #cbd5e1; }
.timeline-dot.failed { background: #dc2626; border-color: #dc2626; }
@keyframes uiPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(14,165,233,.4); }
    50% { box-shadow: 0 0 0 8px rgba(14,165,233,0); }
}
.timeline-time {
    font-size: .75rem;
    color: #94a3b8;
    font-weight: 500;
}
.timeline-title {
    font-weight: 600;
    font-size: .9rem;
    color: #1e293b;
}
.timeline-desc {
    font-size: .82rem;
    color: #64748b;
    margin-top: .15rem;
}
.info-card {
    background: rgba(241,245,249,.5);
    border-radius: var(--radius-lg);
    padding: 1.25rem;
    border: 1px solid #e2e8f0;
}
.map-frame {
    border-radius: var(--radius-lg);
    overflow: hidden;
    border: 1px solid #e2e8f0;
    min-height: 300px;
}
.status-badge-lg {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .5rem 1rem;
    border-radius: 9999px;
    font-size: .85rem;
    font-weight: 700;
}
@media (max-width: 767.98px) {
    .timeline-container { padding-left: 2rem; }
    .timeline-dot { left: -2rem; }
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#0ea5e9;--accent-rgb:14,165,233;--accent-hover:#0284c7;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-signpost-2"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Seguimiento #{{ $tracking->orden_id }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-clock me-1"></i>
                        <span>Creado {{ $tracking->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('delivery-tracking.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Order & Driver Info --}}
    <div class="row g-4 mb-4">
        {{-- Order Info --}}
        <div class="col-lg-7">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3" style="color:#0ea5e9;">
                        <i class="bi bi-bag-check me-2"></i>Información de la Orden
                    </h6>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label"><i class="bi bi-hash me-2 text-muted"></i>Número de Orden</span>
                        <span class="ui-detail-value fw-bold">#{{ $tracking->orden_id }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label"><i class="bi bi-person me-2 text-muted"></i>Cliente</span>
                        <span class="ui-detail-value">{{ $tracking->orden?->cliente?->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label"><i class="bi bi-geo-alt me-2 text-muted"></i>Dirección</span>
                        <span class="ui-detail-value">{{ $tracking->orden?->direccion_entrega ?? 'N/A' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label"><i class="bi bi-telephone me-2 text-muted"></i>Teléfono</span>
                        <span class="ui-detail-value">{{ $tracking->orden?->cliente?->telefono ?? 'N/A' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label"><i class="bi bi-tag me-2 text-muted"></i>Tipo de Orden</span>
                        <span class="ui-detail-value">
                            <span class="ui-badge ui-badge-info">
                                {{ ucfirst($tracking->orden?->tipo_orden ?? 'delivery') }}
                            </span>
                        </span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label"><i class="bi bi-currency-dollar me-2 text-muted"></i>Total</span>
                        <span class="ui-detail-value fw-bold">${{ number_format($tracking->orden?->total ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Driver Info --}}
        <div class="col-lg-5">
            <div class="ui-card h-100" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3" style="color:#0ea5e9;">
                        <i class="bi bi-person-badge me-2"></i>Driver Asignado
                    </h6>
                    @if($tracking->driver)
                        <div class="text-center mb-3">
                            <div class="driver-avatar mx-auto mb-2" style="width:64px;height:64px;font-size:1.3rem;">
                                {{ strtoupper(substr($tracking->driver->nombre, 0, 1) . substr($tracking->driver->apellido, 0, 1)) }}
                            </div>
                            <h5 class="fw-bold mb-1">{{ $tracking->driver->nombre }} {{ $tracking->driver->apellido }}</h5>
                            <div class="small text-muted">
                                <i class="bi bi-telephone me-1"></i>{{ $tracking->driver->telefono }}
                            </div>
                            @if($tracking->driver->whatsapp)
                            <div class="mt-1">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tracking->driver->whatsapp) }}" target="_blank" class="text-decoration-none" style="color:#25D366;">
                                    <i class="bi bi-whatsapp me-1"></i>WhatsApp
                                </a>
                            </div>
                            @endif
                        </div>
                        <hr>
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Licencia</span>
                            <span class="ui-detail-value">{{ $tracking->driver->licencia_conducir ?? '—' }}</span>
                        </div>
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Estado</span>
                            <span class="ui-detail-value">
                                @if($tracking->driver->activo)
                                    <span class="ui-badge ui-badge-success">Activo</span>
                                @else
                                    <span class="ui-badge ui-badge-neutral">Inactivo</span>
                                @endif
                            </span>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-person-x fs-2 d-block mb-2"></i>
                            <p class="mb-0">Sin driver asignado</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Map --}}
    @if($tracking->latitud && $tracking->longitud)
    <div class="ui-card mb-4" style="--delay:.2s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold mb-0" style="color:#0ea5e9;">
                    <i class="bi bi-map me-2"></i>Ubicación Actual
                </h6>
            </div>
            <div class="map-frame">
                <iframe
                    src="https://www.google.com/maps?q={{ $tracking->latitud }},{{ $tracking->longitud }}&z=15&output=embed"
                    width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
    @endif

    {{-- Timeline --}}
    <div class="ui-card" style="--delay:.25s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0" style="color:#0ea5e9;">
                    <i class="bi bi-diagram-3 me-2"></i>Historial de Seguimiento
                </h6>
                <span class="status-badge-lg ui-badge-{{ $tracking->status === 'entregado' ? 'success' : ($tracking->status === 'en_camino' ? 'info' : ($tracking->status === 'fallido' ? 'danger' : ($tracking->status === 'cancelado' ? 'warning' : 'neutral'))) }}">
                    @php
                        $statusLabels = [
                            'creado' => 'Creado', 'en_camino' => 'En Camino',
                            'entregado' => 'Entregado', 'fallido' => 'Fallido', 'cancelado' => 'Cancelado'
                        ];
                    @endphp
                    <i class="bi bi-{{ $tracking->status === 'entregado' ? 'check-circle' : ($tracking->status === 'en_camino' ? 'truck' : ($tracking->status === 'fallido' ? 'x-circle' : ($tracking->status === 'cancelado' ? 'slash-circle' : 'clock'))) }} me-1"></i>
                    {{ $statusLabels[$tracking->status] ?? $tracking->status }}
                </span>
            </div>

            <div class="timeline-container">
                @foreach($events as $event)
                <div class="timeline-item">
                    <div class="timeline-dot {{ $event->completed ? 'completed' : ($event->is_current ? 'current' : 'pending') }}"></div>
                    <div class="timeline-time">{{ $event->created_at->format('d/m/Y H:i:s') }}</div>
                    <div class="timeline-title">{{ $event->descripcion }}</div>
                    @if($event->nota)
                    <div class="timeline-desc">{{ $event->nota }}</div>
                    @endif
                    @if($event->usuario)
                    <div class="timeline-desc">
                        <i class="bi bi-person me-1"></i>Por: {{ $event->usuario }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="ui-card mt-4" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <h6 class="fw-bold mb-3" style="color:#0ea5e9;">
                <i class="bi bi-lightning me-2"></i>Acciones Rápidas
            </h6>
            <div class="d-flex gap-2 flex-wrap">
                @if(in_array($tracking->status, ['creado', 'cancelado']))
                <form action="{{ route('delivery-tracking.updateStatus', [$tracking, 'en_camino']) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="ui-btn ui-btn-solid rounded-pill">
                        <i class="bi bi-truck me-1"></i>Marcar en Camino
                    </button>
                </form>
                @endif

                @if($tracking->status === 'en_camino')
                <button type="button" class="ui-btn ui-btn-solid rounded-pill" data-bs-toggle="modal" data-bs-target="#confirmDeliveryModal">
                    <i class="bi bi-check-all me-1"></i>Confirmar Entrega
                </button>
                @endif

                @if(in_array($tracking->status, ['creado', 'en_camino']))
                <form action="{{ route('delivery-tracking.updateStatus', [$tracking, 'fallido']) }}" method="POST" class="d-inline" onsubmit="return UI.confirm.delete('¿Marcar esta entrega como fallida?')">
                    @csrf
                    <button type="submit" class="ui-btn ui-btn-danger rounded-pill">
                        <i class="bi bi-x-circle me-1"></i>Marcar Fallido
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Confirm Delivery Modal --}}
<div class="modal fade" id="confirmDeliveryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 bg-success text-white rounded-top-4">
                <h6 class="modal-title fw-bold"><i class="bi bi-check-all me-2"></i>Confirmar Entrega</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('delivery-tracking.updateStatus', [$tracking, 'entregado']) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="ui-label">Foto de Entrega (opcional)</label>
                        <input type="file" name="foto_entrega" accept="image/*" class="form-control rounded-3">
                    </div>
                    <div class="mb-3">
                        <label class="ui-label">Firma del Cliente (opcional)</label>
                        <input type="file" name="firma_entrega" accept="image/*" class="form-control rounded-3">
                    </div>
                    <div class="mb-3">
                        <label class="ui-label">Notas de Entrega</label>
                        <textarea name="nota_entrega" class="ui-textarea" rows="2" placeholder="Detalles adicionales de la entrega..."></textarea>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="ui-btn ui-btn-solid rounded-pill">
                            <i class="bi bi-check-lg me-1"></i>Confirmar Entrega
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
