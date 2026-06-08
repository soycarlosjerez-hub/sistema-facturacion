@extends('layouts.app')

@section('title', 'Gestión de Productos')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-box-seam text-primary me-2"></i>
                Productos
            </h2>
            <p class="text-muted mb-0">Gestión de productos e inventario</p>
        </div>
        <div>
            @can('productos.create')
            <a href="{{ route('productos.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
            </a>
            @endcan
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('productos.index') }}" id="filtros-form" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="input-group input-group-merge border-0 shadow-none">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="nombre" id="busqueda-producto" class="form-control border-0 bg-white"
                               placeholder="Buscar por nombre o código..." value="{{ request('nombre') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="stock_status" class="form-select border-0 shadow-none bg-white">
                        <option value="">Todos los stocks</option>
                        <option value="critical" {{ request('stock_status') == 'critical' ? 'selected' : '' }}>Cr&iacute;tico (&le; 5)</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Bajo (6 - 15)</option>
                        <option value="ok" {{ request('stock_status') == 'ok' ? 'selected' : '' }}>Normal (&gt; 15)</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="number" name="precio_min" class="form-control border-0 shadow-none bg-white"
                           placeholder="Precio mín." value="{{ request('precio_min') }}" step="0.01" min="0">
                </div>
                <div class="col-lg-2">
                    <input type="number" name="precio_max" class="form-control border-0 shadow-none bg-white"
                           placeholder="Precio máx." value="{{ request('precio_max') }}" step="0.01" min="0">
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button class="btn btn-primary rounded-pill px-3 flex-grow-1"><i class="bi bi-funnel"></i> Filtrar</button>
                    <a href="{{ route('productos.index') }}" class="btn btn-light rounded-pill px-3"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-lg-12 mt-2 text-end">
                    <div class="btn-group">
                        <a href="{{ route('productos.pdf', request()->all()) }}" class="btn btn-sm btn-light rounded-pill">
                            <i class="bi bi-file-pdf text-danger me-1"></i> PDF
                        </a>
                        <a href="{{ route('productos.exportar', request()->all()) }}" class="btn btn-sm btn-light rounded-pill">
                            <i class="bi bi-file-excel text-success me-1"></i> Excel
                        </a>
                        <a href="{{ route('productos.import') }}" class="btn btn-sm btn-light rounded-pill">
                            <i class="bi bi-upload text-info me-1"></i> Importar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="products-container" class="list-view">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-muted text-uppercase small">
                            <th class="ps-4">Producto</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>ITBIS</th>
                            <th>Costo</th>
                            <th>Ganancia</th>
                            <th>Margen</th>
                            <th>Stock</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="products-tbody">
                        @forelse($productos as $p)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="product-img-wrapper me-3">
                                        <img src="{{ $p->imagen_url }}" class="rounded-pill shadow-sm" width="48" height="48" style="object-fit: cover; background: #f1f5f9;" alt="{{ $p->nombre }}">
                                    </div>
                                    <div>
                                        <div class="fw-bold mb-0 text-dark">{{ $p->nombre }}</div>
                                        <small class="text-muted">
                                            <i class="bi bi-upc-scan"></i> {{ $p->codigo_barras ?? 'Sin código' }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($p->categoria)
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill">{{ $p->categoria->nombre }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-primary">RD$ {{ number_format($p->precio, 2) }}</span>
                            </td>
                            <td>
                                <span class="text-muted">{{ number_format($p->itbis_porcentaje ?? 18, 2) }}%</span>
                            </td>
                            <td>
                                <span class="text-muted small">RD$ {{ number_format($p->precio_compra ?? 0, 2) }}</span>
                            </td>
                            <td>
                                @php $profit = $p->ganancia; @endphp
                                <span class="badge {{ $profit >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $profit >= 0 ? 'success' : 'danger' }} rounded-pill px-2">
                                    {{ $profit >= 0 ? '+' : '' }}RD$ {{ number_format($profit, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ number_format($p->margen_porcentaje, 1) }}%</span>
                            </td>
                            <td>
                                @if($p->estado_stock === 'critical')
                                    <span class="badge bg-danger rounded-pill px-3">Cr&iacute;tico: {{ $p->stock }}</span>
                                @elseif($p->estado_stock === 'low')
                                    <span class="badge bg-warning text-dark rounded-pill px-3">Bajo: {{ $p->stock }}</span>
                                @else
                                    <span class="badge bg-light text-dark border rounded-pill px-3">{{ $p->stock }} u.</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('productos.show', $p) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Ver detalles">
                                        <i class="bi bi-eye text-info"></i>
                                    </a>
                                    <a href="{{ route('productos.edit', $p) }}" class="btn btn-sm btn-outline-warning rounded-pill me-1" title="Editar">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </a>
                                    <form action="{{ route('productos.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el producto {{ $p->nombre }}? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar">
                                            <i class="bi bi-trash text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="bi bi-search display-1 text-muted opacity-25"></i>
                                <p class="text-muted mt-3">No se encontraron productos con esos criterios.</p>
                                <a href="{{ route('productos.index') }}" class="btn btn-sm btn-light rounded-pill mt-2">Limpiar filtros</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4" id="pagination-container">
        {{ $productos->links() }}
    </div>
</div>

<style>
    .product-img-wrapper { transition: transform 0.2s; }
    tr:hover .product-img-wrapper { transform: scale(1.1); }
    .table > :not(caption) > * > * { padding: 0.85rem 0.5rem; }
    .table thead th { font-weight: 700; letter-spacing: 0.03em; border-bottom: 1px solid #e2e8f0; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('busqueda-producto');
    const tbody = document.getElementById('products-tbody');
    const pagination = document.getElementById('pagination-container');
    const filtrosForm = document.getElementById('filtros-form');
    let timeout = null;

    if (!searchInput) return;

    function recargarProductos(url) {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newTbody = doc.getElementById('products-tbody');
                const newPagination = doc.getElementById('pagination-container');
                if (newTbody && tbody) tbody.innerHTML = newTbody.innerHTML;
                if (newPagination && pagination) pagination.innerHTML = newPagination.innerHTML;
            })
            .catch(err => console.error('Error al buscar:', err));
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
        }, 350);
    });
});
</script>
@endsection
