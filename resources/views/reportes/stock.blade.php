@extends('layouts.app')
@section('title', 'Inventario / Stock')

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
    <div class="premium-header d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative w-100" style="z-index:2;">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-bar-chart-line text-white me-2"></i>Inventario / Stock</h2>
                <p class="text-white-50 mb-0">{{ $totalProductos }} producto(s) &middot; {{ $bajoStock }} bajo stock &middot; {{ $sinStock }} sin stock</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('reportes.stock.csv', request()->all()) }}" class="btn btn-success rounded-pill"><i class="bi bi-download me-1"></i> CSV</a>
                <a href="{{ route('reportes.stock.pdf', request()->all()) }}" class="btn btn-danger rounded-pill"><i class="bi bi-file-pdf me-1"></i> PDF</a>
                <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
                <div class="premium-avatar-circle ms-2">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card card-accent blue p-3 mb-4">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto"><label class="form-label small fw-semibold mb-0">Filtro</label></div>
            <div class="col-auto">
                <select name="filtro" class="form-select border-0 bg-white">
                    <option value="todos" {{ $filtro === 'todos' ? 'selected' : '' }}>Todos los productos</option>
                    <option value="bajo_stock" {{ $filtro === 'bajo_stock' ? 'selected' : '' }}>Stock bajo</option>
                    <option value="sin_stock" {{ $filtro === 'sin_stock' ? 'selected' : '' }}>Sin stock</option>
                </select>
            </div>
            <div class="col-auto"><input type="text" name="buscar" class="form-control border-0 bg-white" placeholder="Buscar producto..." value="{{ $buscar }}"></div>
            <div class="col-auto"><button class="btn btn-primary rounded-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="premium-card card-accent blue h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Total Productos</small><h4 class="fw-bold mb-0 mt-1">{{ $totalProductos }}</h4></div></div></div>
        <div class="col-md-3"><div class="premium-card card-accent blue h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Valor Inventario</small><h4 class="fw-bold mb-0 mt-1 text-primary">RD$ {{ number_format($totalValorInventario, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="premium-card card-accent blue h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Stock Bajo</small><h4 class="fw-bold mb-0 mt-1 text-warning">{{ $bajoStock }}</h4></div></div></div>
        <div class="col-md-3"><div class="premium-card card-accent blue h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Sin Stock</small><h4 class="fw-bold mb-0 mt-1 text-danger">{{ $sinStock }}</h4></div></div></div>
    </div>

    <div class="premium-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3">Código</th><th>Producto</th>
                        <th class="text-end">Stock</th><th class="text-end">Mínimo</th><th class="text-end">Costo</th><th class="text-end">Precio</th><th class="text-end pe-4">Valor Inv.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $p)
                        @php
                            $estado = $p->stock <= 0 ? 'danger' : ($p->stock <= ($p->stock_minimo ?? 0) ? 'warning' : 'success');
                            $label = $p->stock <= 0 ? 'Sin Stock' : ($p->stock <= ($p->stock_minimo ?? 0) ? 'Stock Bajo' : 'Disponible');
                        @endphp
                        <tr>
                            <td class="ps-4 font-monospace small">{{ $p->codigo_barras ?? $p->referencia ?? '-' }}</td>
                            <td><span class="fw-semibold small">{{ $p->nombre }}</span></td>
                            <td class="text-end"><span class="badge bg-{{ $estado }} bg-opacity-10 text-{{ $estado }} rounded-pill">{{ $p->stock }}</span></td>
                            <td class="text-end">{{ $p->stock_minimo ?? 0 }}</td>
                            <td class="text-end">RD$ {{ number_format($p->precio_compra ?? 0, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($p->precio ?? 0, 2) }}</td>
                            <td class="text-end pe-4 fw-bold">RD$ {{ number_format($p->stock * ($p->precio_compra ?? 0), 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1"></i><p class="mt-2 mb-0">No hay productos</p></td></tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="6" class="ps-4 py-3 text-end text-uppercase small">Valor Total Inventario</td>
                        <td class="text-end pe-4 py-3">RD$ {{ number_format($totalValorInventario, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
