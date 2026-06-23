@extends('layouts.app')
@section('title', 'Centro de Reportes')

@push('styles')
<style>
.premium-header {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    border-radius: 1rem; padding: 2rem; color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px -5px rgba(79,70,229,0.4);
    position: relative; overflow: hidden;
}
.premium-header::after {
    content: ''; position: absolute; top: -50%; right: -20%;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
}
.filter-card {
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
}
.report-card { transition: all 0.3s; border: 1px solid rgba(15,23,42,0.06) !important; }
.report-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(15,23,42,0.10) !important; border-color: rgba(56,189,248,0.3) !important; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="premium-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-bar-chart-line text-primary me-2"></i>Centro de Reportes</h2>
                <p class="text-muted mb-0">
                    @if($sucursalActiva)
                        Sucursal: <strong>{{ $sucursalActiva->nombre }}</strong>
                    @else
                        Todas las sucursales
                    @endif
                    &middot; {{ now()->format('d/m/Y') }}
                </p>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 text-center">
                        <div class="text-success mb-2"><i class="bi bi-cart-check fs-2"></i></div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Ventas Hoy</small>
                        <h4 class="fw-bold mb-0 mt-1">RD$ {{ number_format($ventasHoy, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 text-center">
                        <div class="text-primary mb-2"><i class="bi bi-graph-up-arrow fs-2"></i></div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Ventas del Mes</small>
                        <h4 class="fw-bold mb-0 mt-1">RD$ {{ number_format($ventasMes, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 text-center">
                        <div class="text-warning mb-2"><i class="bi bi-cart-check fs-2"></i></div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Compras del Mes</small>
                        <h4 class="fw-bold mb-0 mt-1">RD$ {{ number_format($comprasMes, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
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
                <div class="card border-0 shadow-sm rounded-4 h-100 report-card">
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
                <div class="card border-0 shadow-sm rounded-4 h-100 report-card">
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
                <div class="card border-0 shadow-sm rounded-4 h-100 report-card">
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
                <div class="card border-0 shadow-sm rounded-4 h-100 report-card">
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
            <a href="{{ route('reportes.utilidades') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 report-card">
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
                <div class="card border-0 shadow-sm rounded-4 h-100 report-card">
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
                <div class="card border-0 shadow-sm rounded-4 h-100 report-card">
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
                <div class="card border-0 shadow-sm rounded-4 h-100 report-card">
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
                <div class="card border-0 shadow-sm rounded-4 h-100 report-card">
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
