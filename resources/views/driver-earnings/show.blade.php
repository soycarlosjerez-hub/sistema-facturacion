@extends('layouts.app')
@section('title', 'Ganancias del Repartidor')
@section('topbar_class', 'px-4')

@push('styles')
<style>
.ui-stat { background: var(--glass-card); border: 1px solid rgba(255,255,255,.1); border-radius: var(--radius-xl); padding: 1.25rem; }
.ui-stat-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); }
.ui-stat-value { font-size: 1.5rem; font-weight: 800; }
.detail-row { background: var(--glass-card); border: 1px solid rgba(255,255,255,.08); border-radius: var(--radius-lg); padding: .75rem 1rem; margin-bottom: .5rem; }
.detail-header { font-weight: 600; color: var(--muted); font-size: .75rem; text-transform: uppercase; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('driver-earnings.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
            <h4 class="mb-1"><i class="bi bi-person-badge me-2"></i>Ganancias: {{ $earning->driver->nombreCompleto }}</h4>
            <p class="text-muted mb-0">{{ $earning->periodo_inicio->format('d/m/Y') }} - {{ $earning->periodo_fin->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ui-stat text-center">
                <div class="ui-stat-label">Total Entregas</div>
                <div class="ui-stat-value">{{ $earning->total_entregas }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat text-center">
                <div class="ui-stat-label">Total Ganancias</div>
                <div class="ui-stat-value text-success">RD$ {{ number_format($earning->total_ganancias, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat text-center">
                <div class="ui-stat-label">Total Propinas</div>
                <div class="ui-stat-value text-info">RD$ {{ number_format($earning->propinas, 2) }}</div>
            </div>
        </div>
    </div>

    <h5 class="mb-3"><i class="bi bi-list-check me-2"></i>Detalles de Entregas</h5>

    @forelse($details as $detalle)
    <div class="detail-row">
        <div class="row align-items-center">
            <div class="col-md-3">
                <div class="detail-header">Fecha</div>
                <div>{{ $detalle->fecha->format('d/m/Y H:i') }}</div>
            </div>
            <div class="col-md-3">
                <div class="detail-header">Orden</div>
                <div>
                    @if($detalle->orden)
                        <a href="{{ route('ordenes.show', $detalle->orden) }}" class="text-decoration-none">
                            {{ $detalle->orden->ncf }}
                        </a>
                    @elseif($detalle->venta)
                        <a href="{{ route('ventas.show', $detalle->venta) }}" class="text-decoration-none">
                            {{ $detalle->venta->ncf }}
                        </a>
                    @else
                        N/A
                    @endif
                </div>
            </div>
            <div class="col-md-2">
                <div class="detail-header">Ganancia</div>
                <div class="text-success fw-bold">RD$ {{ number_format($detalle->monto_ganancia, 2) }}</div>
            </div>
            <div class="col-md-2">
                <div class="detail-header">Propina</div>
                <div class="text-info">RD$ {{ number_format($detalle->propina, 2) }}</div>
            </div>
            <div class="col-md-2">
                <div class="detail-header">Total</div>
                <div class="fw-bold">RD$ {{ number_format($detalle->monto_ganancia + $detalle->propina, 2) }}</div>
            </div>
        </div>
    </div>
    @empty
    <div class="card border-0" style="background: var(--glass-card);">
        <div class="card-body text-center text-muted py-4">
            <i class="bi bi-inbox display-4"></i>
            <p class="mt-2 mb-0">No hay detalles de ganancias para este período</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
