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
.premium-stat-card { background:rgba(255,255,255,.85);border-radius:1.2rem;box-shadow:0 4px 24px rgba(0,0,0,.04);transition:transform .2s,box-shadow .2s;position:relative;overflow:hidden; }
.premium-stat-card:hover { transform:translateY(-2px);box-shadow:0 8px 32px rgba(0,0,0,.08); }
.premium-stat-card .stat-label { font-size:.65rem;text-transform:uppercase;letter-spacing:.5px;color:#64748b;font-weight:700;margin-bottom:4px; }
.premium-stat-card .stat-value { font-size:1.5rem;font-weight:800; }
body.dark-mode .premium-stat-card { background:rgba(15,23,42,.7);border:1px solid rgba(255,255,255,.06); }
body.dark-mode .premium-stat-card .stat-label { color:#94a3b8; }
body.dark-mode .premium-stat-card .stat-value { color:#f1f5f9; }
.report-card { transition: transform .2s, box-shadow .2s; cursor: pointer; }
.report-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,.1) !important; }
.report-card h5 { transition: color .2s; }
.report-card:hover h5 { color: #3b82f6 !important; }
body.dark-mode .report-card h5 { color: #f1f5f9 !important; }
body.dark-mode .report-card .text-muted { color: #94a3b8 !important; }
body.dark-mode .report-card:hover { box-shadow: 0 12px 40px rgba(0,0,0,.3) !important; }
body.dark-mode .report-card:hover h5 { color: #60a5fa !important; }
body.dark-mode .report-card .rounded-circle { background: rgba(59,130,246,.15) !important; }
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
                <p class="mb-0" style="color:rgba(255,255,255,.8);">
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
                <div class="premium-stat-card text-center p-3">
                    <div class="text-success mb-1"><i class="bi bi-cart-check fs-4"></i></div>
                    <div class="stat-label">Ventas Hoy</div>
                    <div class="stat-value">RD$ {{ number_format($ventasHoy, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="premium-stat-card text-center p-3">
                    <div class="text-primary mb-1"><i class="bi bi-graph-up-arrow fs-4"></i></div>
                    <div class="stat-label">Ventas del Mes</div>
                    <div class="stat-value">RD$ {{ number_format($ventasMes, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="premium-stat-card text-center p-3">
                    <div class="text-warning mb-1"><i class="bi bi-cart-check fs-4"></i></div>
                    <div class="stat-label">Compras del Mes</div>
                    <div class="stat-value">RD$ {{ number_format($comprasMes, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="premium-stat-card text-center p-3">
                    <div class="text-info mb-1"><i class="bi bi-cash-stack fs-4"></i></div>
                    <div class="stat-label">Utilidad del Mes</div>
                    <div class="stat-value" style="color:#059669;">RD$ {{ number_format($utilidadMes, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.ventas') }}" class="text-decoration-none">
                <div class="premium-card h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-receipt fs-2"></i>
                        </div>
                        <h5 class="fw-bold">Resumen de Ventas</h5>
                        <p class="text-muted small mb-0">Ventas por período, método de pago, sucursal. Exporta a PDF y CSV.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.compras') }}" class="text-decoration-none">
                <div class="premium-card h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-cart-check fs-2"></i>
                        </div>
                        <h5 class="fw-bold">Resumen de Compras</h5>
                        <p class="text-muted small mb-0">Compras por período, proveedor, retenciones. Exporta a PDF y CSV.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.stock') }}" class="text-decoration-none">
                <div class="premium-card h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-box-seam fs-2"></i>
                        </div>
                        <h5 class="fw-bold">Inventario / Stock</h5>
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
                <div class="premium-card h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-info bg-opacity-10 text-info mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-cash-stack fs-2"></i>
                        </div>
                        <h5 class="fw-bold">Caja / Turnos</h5>
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
                <div class="premium-card h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-cash-coin fs-2"></i>
                        </div>
                        <h5 class="fw-bold">Gastos / Egresos</h5>
                        <p class="text-muted small mb-0">Gastos por período, categoría, método de pago. Exporta a PDF y CSV.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.utilidades') }}" class="text-decoration-none">
                <div class="premium-card h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-graph-up fs-2"></i>
                        </div>
                        <h5 class="fw-bold">Utilidades / Rentabilidad</h5>
                        <p class="text-muted small mb-0">Ganancia por producto, márgenes, utilidad bruta por período.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.fiscales') }}" class="text-decoration-none">
                <div class="premium-card h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-file-earmark-text fs-2"></i>
                        </div>
                        <h5 class="fw-bold">606/607 ITBIS</h5>
                        <p class="text-muted small mb-0">Reportes fiscales DGII: Formato 606 (Compras) y 607 (Ventas).</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.retenciones') }}" class="text-decoration-none">
                <div class="premium-card h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-purple bg-opacity-10 mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;color:#7c3aed;background:rgba(124,58,237,0.1);">
                            <i class="bi bi-percent fs-2"></i>
                        </div>
                        <h5 class="fw-bold">Retenciones</h5>
                        <p class="text-muted small mb-0">ISR e ITBIS retenidos en compras y ventas por período.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.restaurante') }}" class="text-decoration-none">
                <div class="premium-card h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;color:#d97706;background:rgba(217,119,6,0.1);">
                            <i class="bi bi-cup-straw fs-2"></i>
                        </div>
                        <h5 class="fw-bold">Restaurante</h5>
                        <p class="text-muted small mb-0">Ventas por mesero, por mesa, por turno, productos más vendidos.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('reportes.resumen') }}" class="text-decoration-none">
                <div class="premium-card h-100 report-card">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-bar-chart-line fs-2"></i>
                        </div>
                        <h5 class="fw-bold">Resumen Anual</h5>
                        <p class="text-muted small mb-0">Comparativo mensual Ventas vs Compras, ITBIS a pagar/compensar.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
<!-- Spacing --><div class="mb-5"></div>
@endsection
