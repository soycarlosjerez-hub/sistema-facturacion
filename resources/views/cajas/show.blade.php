@extends('layouts.app')

@section('title', $caja->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .caja-detail-card {
    background: rgba(15,23,42,.8);
    border-color: rgba(255,255,255,.08);
}
body.dark-mode .caja-detail-card .text-muted { color: #94a3b8; }
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
                    <i class="bi bi-safe"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">
                        {{ $caja->nombre }}
                        @if($caja->activo)
                            <span class="premium-badge active" style="font-size:.6rem;"><i class="bi bi-check-circle me-1"></i>Activa</span>
                        @else
                            <span class="premium-badge" style="font-size:.6rem;"><i class="bi bi-x-circle me-1"></i>Inactiva</span>
                        @endif
                    </h4>
                    <small class="text-white opacity-75">{{ $caja->codigo ?? 'Sin código' }} &middot; {{ $caja->sucursal->nombre ?? 'Sin sucursal' }}</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                @can('cajas.edit')
                <a href="{{ route('cajas.edit', $caja) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-pencil me-2"></i>Editar
                </a>
                @endcan
                <a href="{{ route('cajas.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 caja-detail-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-circle me-2"></i>Información General</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted small ps-0" style="width:120px;">Nombre</td>
                            <td class="fw-bold">{{ $caja->nombre }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small ps-0">Código</td>
                            <td class="fw-bold">{{ $caja->codigo ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small ps-0">Ubicación</td>
                            <td class="fw-bold">{{ $caja->ubicacion ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small ps-0">Sucursal</td>
                            <td class="fw-bold">{{ $caja->sucursal->nombre ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small ps-0">Estado</td>
                            <td>
                                @if($caja->activo)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Activo</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted small ps-0">Sesión Actual</td>
                            <td>
                                @if($sesionActiva)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                        <i class="bi bi-play-fill me-1"></i>Abierta
                                    </span>
                                    <small class="d-block text-muted mt-1">Desde {{ $sesionActiva->fecha_apertura->format('d/m/Y h:i A') }}</small>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">Cerrada</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        @if($sesionActiva && $stats)
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 caja-detail-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2"></i>Resumen del Turno</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted small ps-0">Ventas Totales</td>
                            <td class="fw-bold text-end">RD${{ number_format($stats['ventasTotales'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small ps-0">Efectivo</td>
                            <td class="fw-bold text-end text-success">RD${{ number_format($stats['pagosEfectivo'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small ps-0">Tarjeta</td>
                            <td class="fw-bold text-end text-info">RD${{ number_format($stats['pagosTarjeta'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small ps-0">Transferencias</td>
                            <td class="fw-bold text-end text-primary">RD${{ number_format($stats['pagosTransferencia'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-muted small ps-0 pt-2">Efectivo Esperado</td>
                            <td class="fw-bold text-end pt-2 fs-5 text-success">RD${{ number_format($stats['totalEsperado'] ?? 0, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
