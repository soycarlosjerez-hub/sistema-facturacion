@extends('layouts.app')

@section('title', 'Ganancias del Repartidor')

@push('styles')
@include('partials.premium-ui')
<style>
.earning-detail-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#16a34a;--accent-rgb:22,163,74;--accent-hover:#15803d;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Ganancias: {{ $earning->driver->nombreCompleto }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-calendar-range me-1"></i>
                        <span>{{ $earning->periodo_inicio->format('d/m/Y') }} - {{ $earning->periodo_fin->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('driver-earnings.index') }}" class="ui-btn ui-btn-outline rounded-pill mb-4" style="border-color:#16a34a;color:#16a34a;">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="earning-detail-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;">
                            <i class="bi bi-box-seam"></i>
                        </span>
                    </div>
                    <div class="ui-stat-label">Total Entregas</div>
                    <div class="ui-stat-value" style="color:#f59e0b;">{{ $earning->total_entregas }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-card h-100" style="--delay:.15s">
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="earning-detail-icon" style="background:rgba(22,163,74,.1);color:#16a34a;">
                            <i class="bi bi-cash-coin"></i>
                        </span>
                    </div>
                    <div class="ui-stat-label">Total Ganancias</div>
                    <div class="ui-stat-value" style="color:#16a34a;">RD$ {{ number_format($earning->total_ganancias, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-card h-100" style="--delay:.2s">
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="earning-detail-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9;">
                            <i class="bi bi-hand-thumbs-up"></i>
                        </span>
                    </div>
                    <div class="ui-stat-label">Total Propinas</div>
                    <div class="ui-stat-value" style="color:#0ea5e9;">RD$ {{ number_format($earning->propinas ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Details --}}
    <div class="ui-card" style="--delay:.25s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <h6 class="fw-bold mb-3" style="color:#16a34a;">
                <i class="bi bi-list-check me-2"></i>Detalles de Entregas
            </h6>
        </div>

        @forelse($details as $detalle)
        <div class="px-4 py-3" style="border-top:1px solid rgba(0,0,0,.04);">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <small class="text-muted fw-semibold" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;">Fecha</small>
                    <div style="font-size:.88rem;">{{ $detalle->fecha?->format('d/m/Y H:i') ?? 'N/A' }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted fw-semibold" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;">Referencia</small>
                    <div style="font-size:.88rem;">
                        @if($detalle->orden)
                            <a href="{{ route('ordenes.show', $detalle->orden) }}" class="text-decoration-none" style="color:#0ea5e9;">
                                #{{ $detalle->orden->id }}
                            </a>
                        @elseif($detalle->venta)
                            <a href="{{ route('ventas.show', $detalle->venta) }}" class="text-decoration-none" style="color:#0ea5e9;">
                                #{{ $detalle->venta->id }}
                            </a>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-2">
                    <small class="text-muted fw-semibold" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;">Ganancia</small>
                    <div class="fw-bold" style="color:#16a34a;font-size:.88rem;">RD$ {{ number_format($detalle->monto_ganancia, 2) }}</div>
                </div>
                <div class="col-md-2">
                    <small class="text-muted fw-semibold" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;">Propina</small>
                    <div style="color:#0ea5e9;font-size:.88rem;">RD$ {{ number_format($detalle->propina, 2) }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted fw-semibold" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;">Total</small>
                    <div class="fw-bold" style="font-size:.95rem;">RD$ {{ number_format($detalle->monto_ganancia + $detalle->propina, 2) }}</div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-4 text-center text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            No hay detalles de ganancias para este período
        </div>
        @endforelse
    </div>
</div>
@endsection
