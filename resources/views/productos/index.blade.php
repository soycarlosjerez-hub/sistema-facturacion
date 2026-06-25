@extends('layouts.app')

@section('title', 'Gestión de Productos')

@push('styles')
@include('partials.premium-ui')
<style>
/* Productos-specific: table */
.productos-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(99,102,241,.04);
    margin: 0;
}
.productos-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.productos-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.productos-table tbody tr:last-child td { border-bottom: none; }
.productos-table tbody tr { transition: background .15s; }
.productos-table tbody tr:hover { background: rgba(99,102,241,.03); }

.producto-img {
    width: 48px; height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #f8fafc;
    flex-shrink: 0;
    transition: transform .2s;
}
tr:hover .producto-img { transform: scale(1.1); }

body.dark-mode .productos-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .productos-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Catálogo de Productos</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-grid me-1"></i>
                        Administración de inventario, precios y existencias
                    </small>
                </div>
            </div>
            <div>
                @can('productos.create')
                <a href="{{ route('productos.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="premium-card mb-4" style="animation-delay:.1s;">
        <div class="card-accent blue"></div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('productos.index') }}" id="filtros-form" class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label small fw-bold text-muted">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="nombre" id="busqueda-producto" class="form-control" placeholder="Nombre, código o SKU..." value="{{ request('nombre') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <label class="form-label small fw-bold text-muted">Stock</label>
                    <select name="stock_status" class="form-select">
                        <option value="">Todos</option>
                        <option value="critical" {{ request('stock_status') == 'critical' ? 'selected' : '' }}>Crítico (≤ 5)</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Bajo (6 - 15)</option>
                        <option value="ok" {{ request('stock_status') == 'ok' ? 'selected' : '' }}>Normal (> 15)</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label small fw-bold text-muted">Precio Mín.</label>
                    <input type="number" name="precio_min" class="form-control" placeholder="RD$ 0.00" value="{{ request('precio_min') }}" step="0.01" min="0">
                </div>
                <div class="col-lg-2">
                    <label class="form-label small fw-bold text-muted">Precio Máx.</label>
                    <input type="number" name="precio_max" class="form-control" placeholder="RD$ 0.00" value="{{ request('precio_max') }}" step="0.01" min="0">
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
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
    </div>

    <div class="premium-card" style="animation-delay:.15s;">
        <div class="card-accent blue"></div>
        <div class="card-body p-0">
            <div class="table-responsive" style="min-height:400px;">
                <table class="table productos-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Producto</th>
                            <th>Categoría</th>
                            <th class="text-end">Venta &amp; Costos</th>
                            <th class="text-center">Rentabilidad</th>
                            <th class="text-center">Inventario</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="products-tbody">
                        @forelse($productos as $p)
                        <tr>
                            <td class="ps-4" style="max-width:300px;">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $p->imagen_url }}" class="producto-img me-3 shadow-sm" alt="{{ $p->nombre }}">
                                    <div class="text-truncate">
                                        <div class="fw-bold fs-6 text-truncate" style="color:#1e293b;" title="{{ $p->nombre }}">{{ $p->nombre }}</div>
                                        <div class="text-muted small"><i class="bi bi-upc-scan me-1"></i>{{ $p->codigo_barras ?? 'Sin código' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($p->categoria)
                                    <span class="badge rounded-pill" style="background:rgba(99,102,241,.1);color:#4f46e5;font-weight:600;">
                                        <i class="bi bi-tags me-1"></i>{{ $p->categoria->nombre }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="fw-bold fs-6" style="color:#4f46e5;">RD$ {{ number_format($p->precio, 2) }}</div>
                                <div class="text-muted" style="font-size:.75rem;">Costo: RD$ {{ number_format($p->precio_compra ?? 0, 2) }}</div>
                                <div class="text-muted" style="font-size:.7rem;">ITBIS: {{ number_format($p->itbis_porcentaje ?? 18, 2) }}%</div>
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
                                    <span class="badge rounded-pill" style="background:rgba(34,197,94,.1);color:#16a34a;font-weight:600;">
                                        <i class="bi bi-check-circle-fill me-1"></i> {{ $p->stock }} unid.
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('productos.show', $p) }}" class="premium-btn-edit" title="Ver" style="background:rgba(59,130,246,.1);color:#3b82f6;border-color:rgba(59,130,246,.2);">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('productos.edit', $p) }}" class="premium-btn-edit" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('productos.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el producto {{ $p->nombre }}? Esta acción no se puede deshacer.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="premium-btn-delete border-0" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam fs-1" style="color:#cbd5e1;"></i>
                                <p class="mt-2 mb-0 fw-semibold">No se encontraron productos</p>
                                <p class="text-muted small mb-3">No hay productos que coincidan con los filtros de búsqueda actuales.</p>
                                @can('productos.create')
                                <a href="{{ route('productos.create') }}" class="btn btn-primary rounded-pill mt-2">Nuevo Producto</a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($productos->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $productos->withQueryString()->links() }}
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('busqueda-producto');
    const tbody = document.getElementById('products-tbody');
    const filtrosForm = document.getElementById('filtros-form');
    let timeout = null;

    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        const termino = this.value;
        timeout = setTimeout(() => {
            const url = new URL(filtrosForm.action);
            const formData = new FormData(filtrosForm);
            formData.set('nombre', termino);
            url.search = new URLSearchParams(formData).toString();
            fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newTbody = doc.getElementById('products-tbody');
                    const newPagination = doc.getElementById('pagination-container');
                    if (newTbody && tbody) tbody.innerHTML = newTbody.innerHTML;
                    const pagination = document.getElementById('pagination-container');
                    if (newPagination && pagination) pagination.innerHTML = newPagination.innerHTML;
                })
                .catch(err => console.error('Error al buscar:', err));
        }, 400);
    });
});
</script>
@endsection
