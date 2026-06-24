@extends('layouts.app')

@section('title', 'Detalle del Cliente')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
        position: relative;
        overflow: hidden;
    }
    .premium-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .info-card {
        background: rgba(255,255,255,0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
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
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- Premium Header -->
    <div class="premium-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="bi bi-person-fill fs-1"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0">{{ $cliente->nombre }}</h2>
                    <p class="mb-0 opacity-75">Detalle del cliente</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-light rounded-pill px-4 fw-bold">
                    <i class="bi bi-pencil-square me-1"></i>Editar
                </a>
                <a href="{{ route('clientes.index') }}" class="btn btn-outline-light rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Ventas</div>
                        <div class="fs-3 fw-bold">{{ $cliente->ventas->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Compras</div>
                        <div class="fs-3 fw-bold">RD$ {{ number_format($cliente->ventas->sum('total'), 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Balance Pendiente</div>
                        <div class="fs-3 fw-bold">RD$ {{ number_format($cliente->balance_pendiente ?? 0, 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-info bg-opacity-10 text-info rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-tag fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Tipo</div>
                        <div class="fs-5 fw-bold">{{ ucfirst($cliente->tipo_cliente ?? 'consumo') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Datos del cliente -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 info-card">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person-vcard text-success me-2"></i>Información del Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="info-item">
                                <div class="label">Nombre</div>
                                <div class="value">{{ $cliente->nombre }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-item" style="border-left-color: #3b82f6;">
                                <div class="label">Email</div>
                                <div class="value">{{ $cliente->email ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-item" style="border-left-color: #f59e0b;">
                                <div class="label">Teléfono</div>
                                <div class="value">{{ $cliente->telefono ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-item" style="border-left-color: #8b5cf6;">
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
                        </div>
                        <div class="col-12">
                            <div class="info-item" style="border-left-color: #ec4899;">
                                <div class="label">Dirección</div>
                                <div class="value">{{ $cliente->direccion ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-item" style="border-left-color: #06b6d4;">
                                <div class="label">Cliente desde</div>
                                <div class="value">{{ $cliente->created_at->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ventas del cliente -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-receipt text-success me-2"></i>Ventas Recientes</h5>
                    <span class="stat-badge"><i class="bi bi-receipt"></i> {{ $cliente->ventas->count() }} ventas</span>
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
                            <a href="{{ route('ventas.show', $venta) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                <i class="bi bi-receipt fs-1 text-muted"></i>
                            </div>
                            <h6 class="fw-bold text-muted">Sin ventas registradas</h6>
                            <p class="text-muted small mb-0">Este cliente aún no tiene ventas.</p>
                        </div>
                    @endforelse
                </div>

                @if($cliente->ventas->count() > 10)
                    <div class="card-footer bg-white border-0 text-center py-3">
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
