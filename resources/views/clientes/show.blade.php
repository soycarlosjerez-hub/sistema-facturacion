@extends('layouts.app')

@section('title', 'Detalle del Cliente')

@push('styles')
@include('partials.premium-ui')
<style>
.info-item {
    background: #f8fafc;
    border-radius: 0.75rem;
    padding: 1rem;
    border-left: 3px solid #10b981;
}
.info-item .label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    font-weight: 700;
    margin-bottom: 4px;
}
.info-item .value {
    font-weight: 600;
    color: #1e293b;
}
.venta-card {
    background: white;
    border-radius: 0.75rem;
    border: 1px solid #e2e8f0;
    padding: 1rem 1.25rem;
    transition: all 0.2s;
}
.venta-card:hover {
    border-color: #10b981;
    box-shadow: 0 4px 12px rgba(16,185,129,0.1);
}
.stat-badge {
    background: rgba(16,185,129,0.1);
    color: #059669;
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-weight: 700;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
body.dark-mode .info-item {
    background: rgba(30,41,59,.8);
}
body.dark-mode .info-item .label { color: #94a3b8; }
body.dark-mode .info-item .value { color: #f1f5f9; }
body.dark-mode .venta-card {
    background: rgba(15,23,42,.6);
    border-color: #334155;
}
.credit-gauge {
    height: 8px;
    border-radius: 4px;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">{{ $cliente->nombre }}</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-person me-1"></i>
                        Detalle del cliente
                    </small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-light rounded-pill px-4 fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-pencil-square me-1"></i>Editar
                </a>
                <a href="{{ route('clientes.index') }}" class="btn btn-outline-light rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="premium-stat-card" style="animation-delay:.05s;">
                <div class="card-accent green"></div>
                <div class="card-body p-3 text-center">
                    <div class="stat-label mb-1">Ventas</div>
                    <div class="stat-value" style="color:#10b981;">{{ $cliente->cantidad_ventas }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="premium-stat-card" style="animation-delay:.1s;">
                <div class="card-accent green"></div>
                <div class="card-body p-3 text-center">
                    <div class="stat-label mb-1">Total Compras</div>
                    <div class="stat-value" style="color:#10b981;">RD$ {{ number_format($cliente->total_compras, 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="premium-stat-card" style="animation-delay:.15s;">
                <div class="card-accent amber"></div>
                <div class="card-body p-3 text-center">
                    <div class="stat-label mb-1">Balance Pendiente</div>
                    <div class="stat-value" style="color:#f59e0b;">RD$ {{ number_format($cliente->balance_pendiente ?? 0, 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="premium-stat-card" style="animation-delay:.2s;">
                <div class="card-accent blue"></div>
                <div class="card-body p-3 text-center">
                    <div class="stat-label mb-1">Tipo</div>
                    <div class="stat-value" style="color:#3b82f6;font-size:1.2rem;">
                        <span class="badge bg-{{ $cliente->color_badge }}">{{ $cliente->tipo_cliente_label }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="premium-card" style="animation-delay:.25s;">
                <div class="card-accent green"></div>
                <div class="premium-card-title">
                    <i class="bi bi-person-vcard icon-green"></i>
                    Información del Cliente
                </div>
                <div class="card-body">
                    <div class="info-item mb-3">
                        <div class="label">Nombre</div>
                        <div class="value">{{ $cliente->nombre }}</div>
                    </div>
                    <div class="info-item mb-3" style="border-left-color: #3b82f6;">
                        <div class="label">Email</div>
                        <div class="value">{{ $cliente->email ?? '—' }}</div>
                    </div>
                    <div class="info-item mb-3" style="border-left-color: #f59e0b;">
                        <div class="label">Teléfono</div>
                        <div class="value">{{ $cliente->telefono ?? '—' }}</div>
                    </div>
                    @if($cliente->whatsapp)
                    <div class="info-item mb-3" style="border-left-color: #25D366;">
                        <div class="label">WhatsApp</div>
                        <div class="value">
                            <i class="bi bi-whatsapp text-success me-1"></i>{{ $cliente->whatsapp }}
                        </div>
                    </div>
                    @endif
                    <div class="info-item mb-3" style="border-left-color: #8b5cf6;">
                        <div class="label">RNC / Cédula</div>
                        <div class="value">
                            @if($cliente->rnc_cedula)
                                <span class="badge bg-dark rounded-pill px-3 py-1">{{ $cliente->rnc_cedula }}</span>
                                @if($cliente->tipo_documento)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-1 ms-1" style="font-size: 0.7rem;">{{ strtoupper($cliente->tipo_documento) }}</span>
                                @endif
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div class="info-item mb-3" style="border-left-color: #ec4899;">
                        <div class="label">Dirección</div>
                        <div class="value">{{ $cliente->direccion ?? '—' }}</div>
                    </div>
                    @if($cliente->ciudad)
                    <div class="info-item mb-3" style="border-left-color: #14b8a6;">
                        <div class="label">Ciudad / Provincia</div>
                        <div class="value">{{ $cliente->ciudad }}{{ $cliente->provincia ? ', '.$cliente->provincia : '' }}</div>
                    </div>
                    @endif
                    @if($cliente->persona_contacto)
                    <div class="info-item mb-3" style="border-left-color: #f97316;">
                        <div class="label">Contacto / Cargo</div>
                        <div class="value">{{ $cliente->persona_contacto }}{{ $cliente->cargo_contacto ? ' — '.$cliente->cargo_contacto : '' }}</div>
                    </div>
                    @endif
                    @if($cliente->sector_actividad)
                    <div class="info-item mb-3" style="border-left-color: #a855f7;">
                        <div class="label">Sector</div>
                        <div class="value">{{ $cliente->sector_actividad }}</div>
                    </div>
                    @endif
                    <div class="info-item mb-3" style="border-left-color: #06b6d4;">
                        <div class="label">Cliente desde</div>
                        <div class="value">{{ $cliente->created_at->format('d/m/Y') }}</div>
                    </div>
                    @if($cliente->ultima_compra)
                    <div class="info-item mb-3" style="border-left-color: #8b5cf6;">
                        <div class="label">Última compra</div>
                        <div class="value">{{ \Carbon\Carbon::parse($cliente->ultima_compra)->format('d/m/Y') }}</div>
                    </div>
                    @endif
                    <div class="info-item" style="border-left-color: {{ $cliente->activo ? '#10b981' : '#6b7280' }};">
                        <div class="label">Estado</div>
                        <div class="value">
                            <span class="badge bg-{{ $cliente->color_badge_activo }} rounded-pill px-3 py-1">
                                <i class="bi bi-{{ $cliente->activo ? 'check-circle-fill' : 'x-circle-fill' }} me-1"></i>
                                {{ $cliente->activo_label }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if($cliente->segmento || $cliente->origen_cliente || $cliente->notas_internas)
            <div class="premium-card mt-4" style="animation-delay:.3s;">
                <div class="card-accent purple"></div>
                <div class="premium-card-title">
                    <i class="bi bi-tags icon-purple"></i>
                    Segmentación
                </div>
                <div class="card-body">
                    @if($cliente->segmento)
                    <div class="info-item mb-3" style="border-left-color: #a855f7;">
                        <div class="label">Segmento</div>
                        <div class="value">
                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1">
                                {{ $cliente->segmento_label }}
                            </span>
                        </div>
                    </div>
                    @endif
                    @if($cliente->origen_cliente)
                    <div class="info-item mb-3" style="border-left-color: #f59e0b;">
                        <div class="label">Origen</div>
                        <div class="value">{{ $cliente->origen_label }}</div>
                    </div>
                    @endif
                    @if($cliente->notas_internas)
                    <div class="info-item" style="border-left-color: #64748b;">
                        <div class="label">Notas Internas</div>
                        <div class="value" style="white-space:pre-wrap;">{{ $cliente->notas_internas }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="premium-card" style="animation-delay:.3s;">
                <div class="card-accent {{ $cliente->color_badge_estado_credito }}"></div>
                <div class="premium-card-title">
                    <i class="bi bi-credit-card @if($cliente->estado_credito === 'excedido')text-danger @else icon-blue @endif"></i>
                    Estado de Crédito
                    <span class="badge bg-{{ $cliente->color_badge_estado_credito }} ms-auto rounded-pill">
                        {{ $cliente->estado_credito_label }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="text-center p-3 rounded-3" style="background:rgba(16,185,129,.08);">
                                <small class="text-muted d-block text-uppercase fw-bold small">Límite</small>
                                <span class="fs-5 fw-bold" style="color:#059669;">
                                    {{ $cliente->moneda_label }}{{ number_format($cliente->limite_credito, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 rounded-3" style="background:rgba(245,158,11,.08);">
                                <small class="text-muted d-block text-uppercase fw-bold small">Balance</small>
                                <span class="fs-5 fw-bold {{ $cliente->balance_pendiente > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $cliente->moneda_label }}{{ number_format($cliente->balance_pendiente, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($cliente->limite_credito > 0)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Utilización</span>
                            <span class="fw-bold">{{ $cliente->utilizacion_credito }}%</span>
                        </div>
                        <div class="progress credit-gauge">
                            @php $pct = min($cliente->utilizacion_credito, 100); @endphp
                            <div class="progress-bar bg-{{ $cliente->color_badge_estado_credito }}"
                                style="width: {{ $pct }}%;">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between small mb-3">
                        <span class="text-muted">Disponible</span>
                        <span class="fw-bold">{{ $cliente->moneda_label }}{{ number_format($cliente->credito_disponible, 2) }}</span>
                    </div>
                    @if($cliente->exceso_credito > 0)
                    <div class="alert alert-danger py-2 px-3 small mb-3 rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Excede el límite por {{ $cliente->moneda_label }}{{ number_format($cliente->exceso_credito, 2) }}
                    </div>
                    @endif
                    @endif

                    <hr class="my-3">
                    <div class="row g-2 small">
                        <div class="col-6">
                            <span class="text-muted">Plazo de Pago:</span>
                            <span class="fw-bold ms-1">{{ $cliente->plazo_pago_dias ? "Net {$cliente->plazo_pago_dias}" : 'Contado' }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted">Dto. Pronto Pago:</span>
                            <span class="fw-bold ms-1">{{ $cliente->tasa_descuento_pct ? "{$cliente->tasa_descuento_pct}%" : '—' }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted">Moneda:</span>
                            <span class="fw-bold ms-1">{{ $cliente->moneda_label }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted">Bloqueo Auto.:</span>
                            <span class="fw-bold ms-1">{{ $cliente->auto_bloquear_credito ? 'Sí' : 'No' }}</span>
                        </div>
                    </div>

                    <hr class="my-3">
                    <div class="row g-2 small">
                        <div class="col-6">
                            <span class="text-muted">Promedio Compra:</span>
                            <span class="fw-bold ms-1">{{ $cliente->moneda_label }}{{ number_format($cliente->promedio_compra, 0) }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted">Total Comprado:</span>
                            <span class="fw-bold ms-1">{{ $cliente->moneda_label }}{{ number_format($cliente->total_compras, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="premium-card" style="animation-delay:.3s;">
                <div class="card-accent green"></div>
                <div class="premium-card-title">
                    <i class="bi bi-receipt icon-green"></i>
                    Ventas Recientes
                    <span class="stat-badge ms-auto"><i class="bi bi-receipt"></i> {{ $cliente->ventas->count() }} ventas</span>
                </div>
                <div class="card-body p-0">
                    @forelse($cliente->ventas->take(10) as $venta)
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-receipt"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Venta #{{ $venta->id }}</div>
                                    <small class="text-muted">{{ $venta->created_at->format('d/m/Y h:i A') }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">RD$ {{ number_format($venta->total, 2) }}</div>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill" style="font-size: 0.7rem;">{{ $venta->estado ?? 'completada' }}</span>
                            </div>
                            <a href="{{ route('ventas.show', $venta) }}" class="premium-btn-edit">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-receipt fs-1" style="color:#cbd5e1;"></i>
                            <h6 class="fw-bold text-muted mt-2">Sin ventas registradas</h6>
                            <p class="text-muted small mb-0">Este cliente aún no tiene ventas.</p>
                        </div>
                    @endforelse
                </div>

                @if($cliente->ventas->count() > 10)
                    <div class="card-footer bg-transparent border-0 text-center py-3">
                        <a href="{{ route('ventas.index', ['cliente_id' => $cliente->id]) }}" class="btn btn-outline-success rounded-pill px-4">
                            <i class="bi bi-arrow-right me-1"></i>Ver todas las ventas
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection