@extends('layouts.app')

@section('title', 'Gestión de Productos')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
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
    .filter-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    .avatar-circle {
        width: 44px; height: 44px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 1.2rem;
        transition: transform 0.2s;
    }
    tr:hover .avatar-circle { transform: scale(1.1); }
    .status-badge {
        padding: 0.4em 0.8em;
        border-radius: 2rem;
        font-weight: 500;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .btn-icon-hover {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%;
        transition: background-color 0.2s;
    }
    .btn-icon-hover:hover { background-color: rgba(0,0,0,0.05); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="premium-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1 d-flex align-items-center">
                <i class="bi bi-box-seam me-3 fs-1 opacity-75"></i> Catálogo de Productos
            </h2>
            <p class="mb-0 opacity-75 fs-5">Administración de inventario, precios y existencias</p>
        </div>
        <div>
            @can('productos.create')
            <a href="{{ route('productos.create') }}" class="btn btn-light text-primary fw-bold rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Nuevo Producto
            </a>
            @endcan
        </div>
    </div>

    <div class="filter-card p-4 mb-4">
        <form method="GET" action="{{ route('productos.index') }}" id="filtros-form" class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label class="form-label text-muted small fw-bold text-uppercase tracking-wider">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="nombre" id="busqueda-producto" class="form-control border-start-0 ps-0" placeholder="Nombre, código o SKU..." value="{{ request('nombre') }}" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-2">
                <label class="form-label text-muted small fw-bold text-uppercase tracking-wider">Stock</label>
                <select name="stock_status" class="form-select">
                    <option value="">Todos</option>
                    <option value="critical" {{ request('stock_status') == 'critical' ? 'selected' : '' }}>Crítico (&le; 5)</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Bajo (6 - 15)</option>
                    <option value="ok" {{ request('stock_status') == 'ok' ? 'selected' : '' }}>Normal (&gt; 15)</option>
                </select>
            </div>
            <div class="col-lg-2">
                <label class="form-label text-muted small fw-bold text-uppercase tracking-wider">Precio Mín.</label>
                <input type="number" name="precio_min" class="form-control" placeholder="RD$ 0.00" value="{{ request('precio_min') }}" step="0.01" min="0">
            </div>
            <div class="col-lg-2">
                <label class="form-label text-muted small fw-bold text-uppercase tracking-wider">Precio Máx.</label>
                <input type="number" name="precio_max" class="form-control" placeholder="RD$ 0.00" value="{{ request('precio_max') }}" step="0.01" min="0">
            </div>
            <div class="col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill flex-grow-1"><i class="bi bi-funnel me-2"></i>Filtrar</button>
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary rounded-circle" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
            <div class="col-12">
                <div class="d-flex gap-2">
                    <a href="{{ route('productos.import') }}" class="btn btn-light rounded-pill shadow-sm fw-medium">
                        <i class="bi bi-upload me-1"></i> Importar CSV
                    </a>
                    <a href="{{ route('productos.exportar', request()->all()) }}" class="btn btn-light rounded-pill shadow-sm fw-medium">
                        <i class="bi bi-file-excel me-1"></i> Excel
                    </a>
                    <a href="{{ route('productos.pdf', request()->all()) }}" class="btn btn-light rounded-pill shadow-sm fw-medium">
                        <i class="bi bi-file-pdf me-1"></i> PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div id="products-container" class="table-responsive" style="min-height:400px;">
        <table class="table table-hover align-middle mb-0 w-100">
            <thead class="text-muted small text-uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">
                <tr>
                    <th class="ps-4 pb-3">Producto</th>
                    <th class="pb-3">Categoría</th>
                    <th class="text-end pb-3">Venta &amp; Costos</th>
                    <th class="text-center pb-3">Rentabilidad</th>
                    <th class="text-center pb-3">Inventario</th>
                    <th class="text-end pe-4 pb-3">Acciones</th>
                </tr>
            </thead>
            <tbody id="products-tbody">
                @forelse($productos as $p)
                <tr>
                    <td class="ps-4" style="max-width:300px;">
                        <div class="d-flex align-items-center">
                            <div class="me-3 shadow-sm rounded-circle" style="width:48px;height:48px;overflow:hidden;border:2px solid #f8fafc;flex-shrink:0;">
                                <img src="{{ $p->imagen_url }}" width="100%" height="100%" style="object-fit:cover;background:#e2e8f0;" alt="{{ $p->nombre }}">
                            </div>
                            <div class="text-truncate">
                                <div class="fw-bold text-dark fs-6 text-truncate" title="{{ $p->nombre }}">{{ $p->nombre }}</div>
                                <div class="text-muted small"><i class="bi bi-upc-scan me-1"></i>{{ $p->codigo_barras ?? 'Sin código' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($p->categoria)
                            <span class="badge bg-info bg-opacity-10 text-info rounded-pill">
                                <i class="bi bi-tags me-1"></i>{{ $p->categoria->nombre }}
                            </span>
                        @else
                            <span class="text-muted small">&mdash;</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="fw-bold text-primary fs-6">RD$ {{ number_format($p->precio, 2) }}</div>
                        <div class="text-muted" style="font-size:0.75rem;">Costo: RD$ {{ number_format($p->precio_compra ?? 0, 2) }}</div>
                        <div class="text-muted" style="font-size:0.7rem;">ITBIS: {{ number_format($p->itbis_porcentaje ?? 18, 2) }}%</div>
                    </td>
                    <td class="text-center">
                        @php $profit = $p->ganancia; @endphp
                        <div class="d-flex flex-column align-items-center gap-1">
                            <span class="badge rounded-pill {{ $profit >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">
                                {{ $profit >= 0 ? '+' : '' }}RD$ {{ number_format($profit, 2) }}
                            </span>
                            <span class="text-muted small fw-medium">{{ number_format($p->margen_porcentaje, 1) }}% Margen</span>
                        </div>
                    </td>
                    <td class="text-center">
                        @if($p->estado_stock === 'critical')
                            <span class="badge bg-danger rounded-pill">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $p->stock }} unid.
                            </span>
                        @elseif($p->estado_stock === 'low')
                            <span class="badge bg-warning text-dark rounded-pill">
                                <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $p->stock }} unid.
                            </span>
                        @else
                            <span class="badge bg-light text-dark border rounded-pill">
                                <i class="bi bi-check-circle-fill text-success me-1"></i> {{ $p->stock }} unid.
                            </span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('productos.show', $p) }}" class="btn btn-icon-hover text-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('productos.edit', $p) }}" class="btn btn-icon-hover text-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('productos.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el producto {{ $p->nombre }}? Esta acción no se puede deshacer.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-icon-hover text-danger border-0 bg-transparent" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center p-5">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-4" style="width:100px;height:100px;">
                                <i class="bi bi-box-seam text-muted opacity-50" style="font-size:3rem;"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">No se encontraron productos</h4>
                            <p class="text-muted mb-4 text-center" style="max-width:400px;">No hay productos que coincidan con los filtros de búsqueda actuales.</p>
                            <a href="{{ route('productos.create') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                                <i class="bi bi-plus-lg me-2"></i> Nuevo Producto
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-center" id="pagination-container">
        {{ $productos->withQueryString()->links() }}
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('busqueda-producto');
    const tbody = document.getElementById('products-tbody');
    const pagination = document.getElementById('pagination-container');
    const filtrosForm = document.getElementById('filtros-form');
    let timeout = null;

    if (!searchInput) return;

    function recargarProductos(url) {
        if (tbody) tbody.style.opacity = '0.5';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newTbody = doc.getElementById('products-tbody');
                const newPagination = doc.getElementById('pagination-container');

                if (newTbody && tbody) {
                    tbody.innerHTML = newTbody.innerHTML;
                    tbody.style.opacity = '1';
                }

                if (newPagination && pagination) {
                    pagination.innerHTML = newPagination.innerHTML;
                } else if (pagination) {
                    pagination.innerHTML = '';
                }
            })
            .catch(err => {
                console.error('Error al buscar:', err);
                if (tbody) tbody.style.opacity = '1';
            });
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        const termino = this.value;
        timeout = setTimeout(() => {
            const url = new URL(filtrosForm.action);
            const formData = new FormData(filtrosForm);
            formData.set('nombre', termino);
            url.search = new URLSearchParams(formData).toString();
            recargarProductos(url.toString());
        }, 400);
    });
});
</script>
@endsection