@extends('layouts.app')

@section('title', 'Historial de Compras')

@push('styles')
<style>
    /* Premium UI Styles */
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
    .table-custom {
        border-collapse: separate;
        border-spacing: 0 0.5rem;
    }
    .table-custom tbody tr {
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: all 0.2s ease-in-out;
        border-radius: 0.75rem;
    }
    .table-custom tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
    }
    .table-custom td:first-child {
        border-top-left-radius: 0.75rem;
        border-bottom-left-radius: 0.75rem;
    }
    .table-custom td:last-child {
        border-top-right-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
    }
    .table-custom td {
        border-top: 1px solid transparent;
        border-bottom: 1px solid transparent;
        padding: 1rem 1.25rem;
        vertical-align: middle;
    }
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1.1rem;
    }
    .status-badge {
        padding: 0.4em 0.8em;
        border-radius: 2rem;
        font-weight: 500;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .row-details {
        background-color: #f8fafc !important;
        border-radius: 0.75rem;
        margin-top: -0.5rem;
        margin-bottom: 0.5rem;
    }
    .btn-icon-hover {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background-color 0.2s;
    }
    .btn-icon-hover:hover {
        background-color: rgba(0,0,0,0.05);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Header Section -->
    <div class="premium-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1 d-flex align-items-center">
                <i class="bi bi-cart-check me-3 fs-1 opacity-75"></i>
                Gestión de Compras
            </h2>
            <p class="mb-0 opacity-75 fs-5">Administra tus compras, proveedores y retenciones</p>
        </div>
        <div>
            @can('compras.create')
            <a href="{{ route('compras.create') }}" class="btn btn-light text-primary fw-bold rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Registrar Compra
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filter-card p-4 mb-4">
        <form method="GET" id="filtros-form" action="{{ route('compras.index') }}" class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label class="form-label text-muted small fw-bold text-uppercase tracking-wider">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="proveedor" id="busqueda-proveedor" class="form-control border-start-0 ps-0" placeholder="Proveedor o RNC..." value="{{ request('proveedor') }}" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-2">
                <label class="form-label text-muted small fw-bold text-uppercase tracking-wider">Desde</label>
                <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
            </div>
            <div class="col-lg-2">
                <label class="form-label text-muted small fw-bold text-uppercase tracking-wider">Hasta</label>
                <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
            </div>
            <div class="col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill flex-grow-1"><i class="bi bi-funnel me-2"></i>Filtrar</button>
                <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary rounded-circle" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
            <div class="col-lg-2 text-end">
                <div class="btn-group shadow-sm rounded-pill">
                    <a href="{{ route('compras.exportar', request()->all()) }}" class="btn btn-light text-success border-0 px-3">
                        <i class="bi bi-file-excel me-1"></i> Excel
                    </a>
                    <a href="{{ route('compras.pdf', request()->all()) }}" class="btn btn-light text-danger border-0 px-3">
                        <i class="bi bi-file-pdf me-1"></i> PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="table-responsive" style="min-height: 400px;">
        <table class="table table-custom mb-0 w-100">
            <thead class="text-muted small text-uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">
                <tr>
                    <th class="ps-4 pb-3">Comprobante</th>
                    <th class="pb-3">Proveedor</th>
                    <th class="pb-3">Fecha & Hora</th>
                    <th class="pb-3">Detalles</th>
                    <th class="text-end pb-3">Montos</th>
                    <th class="text-end pe-4 pb-3">Acciones</th>
                </tr>
            </thead>
            <tbody id="compras-tbody">
                @forelse($compras as $c)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark fs-6">{{ $c->folio }}</span>
                            <span class="status-badge bg-secondary bg-opacity-10 text-secondary mt-1 d-inline-block text-truncate" style="max-width: 120px;">
                                {{ $c->tipoCompra?->nombre ?? 'N/A' }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            @php
                                $firstLetter = strtoupper(substr($c->proveedor->nombre ?? 'D', 0, 1));
                                $colors = ['#f87171', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa', '#f472b6'];
                                $color = $colors[crc32($c->proveedor->nombre ?? '') % count($colors)];
                            @endphp
                            <div class="avatar-circle text-white me-3 shadow-sm" style="background-color: {{ $color }};">
                                {{ $firstLetter }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark fs-6">{{ $c->proveedor->nombre ?? 'Desconocido' }}</div>
                                <div class="text-muted small">RNC: {{ $c->proveedor->rnc ?? $c->proveedor->rnc_cedula ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-medium text-dark">{{ $c->fecha ? $c->fecha->format('d/m/Y') : $c->created_at->format('d/m/Y') }}</div>
                        <div class="text-muted small"><i class="bi bi-clock me-1"></i>{{ $c->created_at->format('h:i A') }}</div>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1">
                            @if($c->almacen)
                                <span class="status-badge bg-info bg-opacity-10 text-info w-auto d-inline-block">
                                    <i class="bi bi-building me-1"></i>{{ $c->almacen->nombre }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                            
                            @if($c->aplica_retencion_isr || $c->aplica_retencion_itbis)
                                <span class="status-badge bg-warning bg-opacity-25 text-dark mt-1" title="ISR: {{ number_format($c->retencion_isr, 2) }} / ITBIS: {{ number_format($c->retencion_itbis, 2) }}">
                                    <i class="bi bi-shield-exclamation me-1"></i>Retenciones Aplicadas
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="text-end">
                        <div class="text-muted small mb-1">Sub: RD$ {{ number_format($c->subtotal ?? 0, 2) }}</div>
                        <div class="fw-bold text-primary fs-5">RD$ {{ number_format($c->total, 2) }}</div>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-1">
                            <button class="btn btn-icon-hover text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#details-{{ $c->id }}" aria-expanded="false" title="Ver productos">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <a href="{{ route('compras.show', $c) }}" class="btn btn-icon-hover text-info" title="Ver detalle completo">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('compras.edit', $c) }}" class="btn btn-icon-hover text-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('compras.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la compra {{ $c->folio }}? Se revertirá el stock.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-icon-hover text-danger border-0 bg-transparent" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <tr class="collapse row-details shadow-sm" id="details-{{ $c->id }}">
                    <td colspan="6" class="p-4 border-0">
                        <div class="bg-white rounded p-3 border border-light">
                            <h6 class="text-muted fw-bold mb-3 small text-uppercase"><i class="bi bi-box-seam me-2"></i>Productos Adquiridos ({{ $c->detalles->count() }})</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless mb-0">
                                    <thead class="border-bottom border-light">
                                        <tr class="text-muted small">
                                            <th>Producto</th>
                                            <th class="text-center">Cant.</th>
                                            <th class="text-end">Precio</th>
                                            <th class="text-end">ITBIS</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($c->detalles as $d)
                                        <tr>
                                            <td class="fw-medium text-dark">{{ $d->producto->nombre ?? '—' }}</td>
                                            <td class="text-center bg-light rounded">{{ $d->cantidad }}</td>
                                            <td class="text-end text-muted">RD$ {{ number_format($d->precio_unitario, 2) }}</td>
                                            <td class="text-end text-muted">{{ number_format($d->itbis_porcentaje ?? 18, 2) }}%</td>
                                            <td class="text-end fw-bold text-dark">RD$ {{ number_format($d->subtotal, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center p-5">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                                <i class="bi bi-cart-x text-muted opacity-50" style="font-size: 3rem;"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">No hay compras registradas</h4>
                            <p class="text-muted mb-4 text-center" style="max-width: 400px;">Aún no se han registrado compras para esta instancia. Comienza agregando tu primera orden de compra.</p>
                            @can('compras.create')
                            <a href="{{ route('compras.create') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                                <i class="bi bi-plus-lg me-2"></i> Registrar Nueva Compra
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($compras->hasPages())
    <div class="mt-4 d-flex justify-content-center" id="pagination-container">
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
        // Add a visual loading state
        tbody.style.opacity = '0.5';
        
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newTbody = doc.getElementById('compras-tbody');
                const newPag = doc.getElementById('pagination-container');
                
                if (newTbody && tbody) {
                    tbody.innerHTML = newTbody.innerHTML;
                    tbody.style.opacity = '1';
                }
                if (newPag && pagination) pagination.innerHTML = newPag.innerHTML;
            })
            .catch(() => {
                tbody.style.opacity = '1';
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
        }, 400);
    });
});
</script>
@endsection
