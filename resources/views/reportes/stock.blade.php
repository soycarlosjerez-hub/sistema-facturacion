@extends('layouts.app')
@section('title', 'Inventario / Stock')

@push('styles')
<style>
.premium-header {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 1rem; padding: 2rem; color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px -5px rgba(59,130,246,0.4);
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
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="premium-header d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-box-seam text-warning me-2"></i>Inventario / Stock</h2>
            <p class="text-muted mb-0">{{ $totalProductos }} producto(s) &middot; {{ $bajoStock }} bajo stock &middot; {{ $sinStock }} sin stock</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reportes.stock.csv', request()->all()) }}" class="btn btn-success rounded-pill"><i class="bi bi-download me-1"></i> CSV</a>
            <a href="{{ route('reportes.stock.pdf', request()->all()) }}" class="btn btn-danger rounded-pill"><i class="bi bi-file-pdf me-1"></i> PDF</a>
            <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
        </div>
    </div>

    <div class="filter-card p-3 mb-4">
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
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Total Productos</small><h4 class="fw-bold mb-0 mt-1">{{ $totalProductos }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Valor Inventario</small><h4 class="fw-bold mb-0 mt-1 text-primary">RD$ {{ number_format($totalValorInventario, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Stock Bajo</small><h4 class="fw-bold mb-0 mt-1 text-warning">{{ $bajoStock }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Sin Stock</small><h4 class="fw-bold mb-0 mt-1 text-danger">{{ $sinStock }}</h4></div></div></div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
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
