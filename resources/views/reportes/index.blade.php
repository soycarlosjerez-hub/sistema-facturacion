@extends('layouts.app')
@section('title', 'Centro de Reportes')

@push('styles')
@include('partials.premium-ui')
<style>
.premium-header {
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #06b6d4 100%) !important;
    background-size: 300% 300% !important;
    animation: premiumGradientShift 6s ease infinite !important;
    box-shadow: 0 8px 32px rgba(59,130,246,.25) !important;
}
body.dark-mode .premium-card { background: rgba(15,23,42,.8); border-color: rgba(255,255,255,.08); }
body.dark-mode .premium-card-title { color: #f1f5f9; }
body.dark-mode .premium-card-subtitle { color: #94a3b8; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-bar-chart-line text-white me-2"></i>Centro de Reportes</h2>
                <p class="text-white-50 mb-0">
                    @if($sucursalActiva)
                        Sucursal: <strong>{{ $sucursalActiva->nombre }}</strong>
                    @else
                        Todas las sucursales
                    @endif
                    &middot; {{ now()->format('d/m/Y') }}
                </p>
            </div>
            <div class="premium-avatar-circle">
                <i class="bi bi-bar-chart-line"></i>
            </div>
        </div>

        <div class="row g-3 mt-3 position-relative" style="z-index:2;">
            <div class="col-md-3 col-6">
                <div class="premium-card card-accent blue h-100">
                    <div class="card-body p-3 text-center">
                        <div class="text-success mb-2"><i class="bi bi-cart-check fs-2"></i></div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Ventas Hoy</small>
                        <h4 class="fw-bold mb-0 mt-1">RD$ {{ number_format($ventasHoy, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="premium-card card-accent blue h-100">
                    <div class="card-body p-3 text-center">
                        <div class="text-primary mb-2"><i class="bi bi-graph-up-arrow fs-2"></i></div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Ventas del Mes</small>
                        <h4 class="fw-bold mb-0 mt-1">RD$ {{ number_format($ventasMes, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="premium-card card-accent blue h-100">
                    <div class="card-body p-3 text-center">
                        <div class="text-warning mb-2"><i class="bi bi-cart-check fs-2"></i></div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Compras del Mes</small>
                        <h4 class="fw-bold mb-0 mt-1">RD$ {{ number_format($comprasMes, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="premium-card card-accent blue h-100">
                    <div class="card-body p-3 text-center">
                        <div class="text-info mb-2"><i class="bi bi-cash-stack fs-2"></i></div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Utilidad del Mes</small>
                        <h4 class="fw-bold mb-0 mt-1 text-success">RD$ {{ number_format($utilidadMes, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.ventas') }}" class="text-decoration-none">
                <div class="premium-card card-accent blue h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-receipt fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Resumen de Ventas</h5>
                        <p class="text-muted small mb-0">Ventas por período, método de pago, sucursal. Exporta a PDF y CSV.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.compras') }}" class="text-decoration-none">
                <div class="premium-card card-accent blue h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-cart-check fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Resumen de Compras</h5>
                        <p class="text-muted small mb-0">Compras por período, proveedor, retenciones. Exporta a PDF y CSV.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.stock') }}" class="text-decoration-none">
                <div class="premium-card card-accent blue h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-box-seam fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Inventario / Stock</h5>
                        <p class="text-muted small mb-0">Estado del inventario, productos bajo stock, valor total. Exporta a PDF y CSV.</p>
                        @if($productosBajoStock > 0)
                            <span class="badge bg-danger mt-2">{{ $productosBajoStock }} producto(s) bajo stock</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.caja') }}" class="text-decoration-none">
                <div class="premium-card card-accent blue h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-info bg-opacity-10 text-info mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-cash-stack fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Caja / Turnos</h5>
                        <p class="text-muted small mb-0">Sesiones de caja, aperturas, cierres, descuadres. Exporta a CSV.</p>
                        @if($sesionesAbiertas > 0)
                            <span class="badge bg-success mt-2">{{ $sesionesAbiertas }} sesión(es) abierta(s)</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.gastos') }}" class="text-decoration-none">
                <div class="premium-card card-accent blue h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-cash-coin fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Gastos / Egresos</h5>
                        <p class="text-muted small mb-0">Gastos por período, categoría, método de pago. Exporta a PDF y CSV.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.utilidades') }}" class="text-decoration-none">
                <div class="premium-card card-accent blue h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-graph-up fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Utilidades / Rentabilidad</h5>
                        <p class="text-muted small mb-0">Ganancia por producto, márgenes, utilidad bruta por período.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.fiscales') }}" class="text-decoration-none">
                <div class="premium-card card-accent blue h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-file-earmark-text fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark">606/607 ITBIS</h5>
                        <p class="text-muted small mb-0">Reportes fiscales DGII: Formato 606 (Compras) y 607 (Ventas).</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.retenciones') }}" class="text-decoration-none">
                <div class="premium-card card-accent blue h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-purple bg-opacity-10 mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;color:#7c3aed;background:rgba(124,58,237,0.1);">
                            <i class="bi bi-percent fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Retenciones</h5>
                        <p class="text-muted small mb-0">ISR e ITBIS retenidos en compras y ventas por período.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.restaurante') }}" class="text-decoration-none">
                <div class="premium-card card-accent blue h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;color:#d97706;background:rgba(217,119,6,0.1);">
                            <i class="bi bi-cup-straw fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Restaurante</h5>
                        <p class="text-muted small mb-0">Ventas por mesero, por mesa, por turno, productos más vendidos.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.resumen') }}" class="text-decoration-none">
                <div class="premium-card card-accent blue h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-bar-chart-line fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Resumen Anual</h5>
                        <p class="text-muted small mb-0">Comparativo mensual Ventas vs Compras, ITBIS a pagar/compensar.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
