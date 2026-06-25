@extends('layouts.app')

@section('title', 'Historial de Compras')

@push('styles')
@include('partials.premium-ui')
<style>
.compras-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(59,130,246,.04);
    margin: 0;
}
.compras-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.compras-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.compras-table tbody tr:last-child td { border-bottom: none; }
.compras-table tbody tr { transition: background .15s; }
.compras-table tbody tr:hover { background: rgba(59,130,246,.03); }
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
body.dark-mode .compras-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .compras-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4" style="background:linear-gradient(135deg,#3b82f6,#6366f1,#8b5cf6,#3b82f6);box-shadow:0 8px 32px rgba(59,130,246,.25);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle" style="background:rgba(255,255,255,.2);border-color:rgba(255,255,255,.35);">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Gestión de Compras</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-cart me-1"></i>
                        Administra tus compras, proveedores y retenciones
                        <span class="mx-2">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        {{ $compras->total() }} registro(s)
                    </small>
                </div>
            </div>
            <div>
                @can('compras.create')
                <a href="{{ route('compras.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Registrar Compra
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="premium-card mb-4" style="animation-delay:.1s;">
        <div class="card-accent blue"></div>
        <div class="card-body p-3">
            <form method="GET" id="filtros-form" action="{{ route('compras.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="proveedor" id="busqueda-proveedor" class="form-control" placeholder="Proveedor o RNC..." value="{{ request('proveedor') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <a href="{{ route('compras.exportar', request()->all()) }}" class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-file-excel me-1"></i> Excel
                    </a>
                    <a href="{{ route('compras.pdf', request()->all()) }}" class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-file-pdf me-1"></i> PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card" style="animation-delay:.15s;">
        <div class="card-accent blue"></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table compras-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Comprobante</th>
                            <th>Proveedor</th>
                            <th>Fecha &amp; Hora</th>
                            <th>Detalles</th>
                            <th class="text-end">Montos</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="compras-tbody">
                        @forelse($compras as $c)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark fs-6">{{ $c->folio }}</span>
                                        <span class="premium-badge" style="background:rgba(100,116,139,.1);color:#475569;font-size:.7rem;">
                                            {{ $c->tipoCompra?->nombre ?? 'N/A' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $firstLetter = strtoupper(substr($c->proveedor->nombre ?? 'D', 0, 1));
                                        $colors = ['#f87171', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa', '#f472b6'];
                                        $color = $colors[crc32($c->proveedor->nombre ?? '') % count($colors)];
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle text-white me-3 shadow-sm" style="background-color: {{ $color }}; width:40px;height:40px;font-size:1.1rem;">
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
                                            <span class="premium-badge" style="background:rgba(59,130,246,.1);color:#3b82f6;">
                                                <i class="bi bi-building me-1"></i>{{ $c->almacen->nombre }}
                                            </span>
                                        @endif
                                        @if($c->aplica_retencion_isr || $c->aplica_retencion_itbis)
                                            <span class="premium-badge" style="background:rgba(245,158,11,.1);color:#d97706;">
                                                <i class="bi bi-shield-exclamation me-1"></i>Retenciones
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="text-muted small mb-1">Sub: RD$ {{ number_format($c->subtotal ?? 0, 2) }}</div>
                                    <div class="fw-bold text-primary fs-5">RD$ {{ number_format($c->total, 2) }}</div>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="premium-btn-edit" type="button" data-bs-toggle="collapse" data-bs-target="#details-{{ $c->id }}" title="Ver productos">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                    <a href="{{ route('compras.show', $c) }}" class="premium-btn-edit" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('compras.edit', $c) }}" class="premium-btn-edit" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('compras.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la compra {{ $c->folio }}? Se revertirá el stock.')">
                                        @csrf @method('DELETE')
                                        <button class="premium-btn-delete" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="collapse" id="details-{{ $c->id }}">
                                <td colspan="6" class="p-4 border-0" style="background:rgba(241,245,249,.5);">
                                    <div class="rounded p-3 bg-white border">
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
                                                        <td class="text-center">{{ $d->cantidad }}</td>
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
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-cart-x fs-1" style="color:#cbd5e1;"></i>
                                    <p class="mt-2 mb-0 fw-semibold">No hay compras registradas</p>
                                    @can('compras.create')
                                    <a href="{{ route('compras.create') }}" class="btn btn-primary rounded-pill mt-2">Registrar primera compra</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($compras->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $compras->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('busqueda-proveedor');
    const tbody = document.getElementById('compras-tbody');
    const form = document.getElementById('filtros-form');
    let timeout = null;

    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        const termino = this.value;
        timeout = setTimeout(() => {
            const url = new URL(form.action);
            const fd = new FormData(form);
            fd.set('proveedor', termino);
            url.search = new URLSearchParams(fd).toString();

            tbody.style.opacity = '0.5';
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newTbody = doc.getElementById('compras-tbody');
                    if (newTbody && tbody) {
                        tbody.innerHTML = newTbody.innerHTML;
                        tbody.style.opacity = '1';
                    }
                })
                .catch(() => { tbody.style.opacity = '1'; });
        }, 400);
    });
});
</script>
@endpush
