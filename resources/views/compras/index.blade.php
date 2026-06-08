@extends('layouts.app')

@section('title', 'Historial de Compras')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-cart-check text-primary me-2"></i>
                Compras
            </h2>
            <p class="text-muted mb-0">Gestión de compras y proveedores</p>
        </div>
        <div>
            @can('compras.create')
            <a href="{{ route('compras.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> Nueva Compra
            </a>
            @endcan
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3 bg-light bg-opacity-50">
            <form method="GET" id="filtros-form" action="{{ route('compras.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="input-group input-group-merge border-0 shadow-none">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="proveedor" id="busqueda-proveedor" class="form-control border-0 bg-white" placeholder="Buscar por proveedor o RNC..." value="{{ request('proveedor') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="desde" class="form-control border-0 bg-white" value="{{ request('desde') }}" placeholder="Desde">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="hasta" class="form-control border-0 bg-white" value="{{ request('hasta') }}" placeholder="Hasta">
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('compras.index') }}" class="btn btn-light rounded-pill"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-lg-2 text-end">
                    <div class="btn-group">
                        <a href="{{ route('compras.pdf', request()->all()) }}" class="btn btn-sm btn-light rounded-pill">
                            <i class="bi bi-file-pdf text-danger me-1"></i> PDF
                        </a>
                        <a href="{{ route('compras.exportar', request()->all()) }}" class="btn btn-sm btn-light rounded-pill">
                            <i class="bi bi-file-excel text-success me-1"></i> Excel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Folio</th>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Almacén</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">ITBIS</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Ret.</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody id="compras-tbody">
                    @forelse($compras as $c)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">{{ $c->folio }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark small">{{ $c->proveedor->nombre ?? 'Desconocido' }}</div>
                             <small class="text-muted" style="font-size: 0.7rem;">RNC: {{ $c->proveedor->rnc ?? $c->proveedor->rnc_cedula ?? '—' }}</small>
                        </td>
                        <td>
                            <div class="small fw-bold">{{ $c->fecha ? $c->fecha->format('d/m/Y') : $c->created_at->format('d/m/Y') }}</div>
                            <div class="text-muted small" style="font-size: 0.7rem;">{{ $c->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">
                                {{ $c->tipoCompra?->nombre ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            @if($c->almacen)
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">
                                    <i class="bi bi-building me-1"></i>{{ $c->almacen->nombre }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-end text-muted small">RD$ {{ number_format($c->subtotal ?? 0, 2) }}</td>
                        <td class="text-end text-muted small">RD$ {{ number_format($c->itbis_total ?? 0, 2) }}</td>
                        <td class="text-end fw-bold text-primary">RD$ {{ number_format($c->total, 2) }}</td>
                        <td class="text-center">
                            @if($c->aplica_retencion_isr || $c->aplica_retencion_itbis)
                                <span class="badge bg-warning text-dark rounded-pill px-2" title="ISR: {{ number_format($c->retencion_isr, 2) }} / ITBIS: {{ number_format($c->retencion_itbis, 2) }}">
                                    <i class="bi bi-percent"></i>
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('compras.show', $c) }}" class="btn btn-sm btn-outline-info rounded-pill me-1" title="Ver detalles">
                                    <i class="bi bi-eye text-info"></i>
                                </a>
                                <a href="{{ route('compras.edit', $c) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Editar">
                                    <i class="bi bi-pencil text-primary"></i>
                                </a>
                                <form action="{{ route('compras.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la compra {{ $c->folio }}? Se revertirá el stock.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar">
                                        <i class="bi bi-trash text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr class="collapse-row bg-light bg-opacity-25" id="details-{{ $c->id }}">
                        <td colspan="9" class="p-3">
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                <strong class="small text-muted"><i class="bi bi-list-ul me-1"></i>Productos ({{ $c->detalles->count() }}):</strong>
                            </div>
                            <table class="table table-sm mb-0 small">
                                <thead>
                                    <tr class="text-muted">
                                        <th>Producto</th>
                                        <th class="text-end">Cant.</th>
                                        <th class="text-end">Precio</th>
                                        <th class="text-end">ITBIS</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($c->detalles as $d)
                                    <tr>
                                        <td>{{ $d->producto->nombre ?? '—' }}</td>
                                        <td class="text-end">{{ $d->cantidad }}</td>
                                        <td class="text-end">RD$ {{ number_format($d->precio_unitario, 2) }}</td>
                                        <td class="text-end">{{ number_format($d->itbis_porcentaje ?? 18, 2) }}%</td>
                                        <td class="text-end fw-bold">RD$ {{ number_format($d->subtotal, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="bi bi-cart-x display-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-3">No hay registros de compras.</p>
                            <a href="{{ route('compras.create') }}" class="btn btn-sm btn-primary rounded-pill mt-2">Registrar primera compra</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($compras->hasPages())
    <div class="mt-4" id="pagination-container">
        {{ $compras->withQueryString()->links() }}
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('busqueda-proveedor');
    const tbody = document.getElementById('compras-tbody');
    const pagination = document.getElementById('pagination-container');
    const form = document.getElementById('filtros-form');
    let timeout = null;

    if (!searchInput) return;

    function recargar(url) {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newTbody = doc.getElementById('compras-tbody');
                const newPag = doc.getElementById('pagination-container');
                if (newTbody && tbody) tbody.innerHTML = newTbody.innerHTML;
                if (newPag && pagination) pagination.innerHTML = newPag.innerHTML;
            });
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        const termino = this.value;
        timeout = setTimeout(() => {
            const url = new URL(form.action);
            const fd = new FormData(form);
            fd.set('proveedor', termino);
            url.search = new URLSearchParams(fd).toString();
            recargar(url.toString());
        }, 350);
    });
});
</script>
@endsection
