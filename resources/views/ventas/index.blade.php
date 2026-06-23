@extends('layouts.app')

@section('title', 'Historial de Ventas')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.4);
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
                <i class="bi bi-receipt me-3 fs-1 opacity-75"></i> Gestión de Ventas
            </h2>
            <p class="mb-0 opacity-75 fs-5">Administración de ventas, facturación y cuentas por cobrar</p>
        </div>
        <div>
            @can('ventas.create')
            <a href="{{ route('ventas.create') }}" class="btn btn-light text-primary fw-bold rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Nueva Venta
            </a>
            @endcan
        </div>
    </div>

    <div class="filter-card p-4 mb-4">
        <form method="GET" class="row g-3 align-items-end" id="filtros-form">
            <div class="col-lg-4">
                <label class="form-label text-muted small fw-bold text-uppercase tracking-wider">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="cliente" id="busqueda-cliente" class="form-control border-start-0 ps-0" placeholder="Buscar por cliente o NCF..." value="{{ request('cliente') }}" autocomplete="off">
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
                <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary rounded-circle" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
            <div class="col-lg-2 text-end">
                <div class="dropdown">
                    <button class="btn btn-light rounded-pill dropdown-toggle w-100 shadow-sm text-dark fw-medium" data-bs-toggle="dropdown">
                        <i class="bi bi-download me-1"></i> Exportar
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                        <li><a class="dropdown-item py-2" href="{{ route('ventas.pdf', request()->query()) }}"><i class="bi bi-file-pdf text-danger me-2"></i> Descargar PDF</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('ventas.exportar', request()->query()) }}"><i class="bi bi-file-excel text-success me-2"></i> Exportar a Excel</a></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive" style="min-height:400px;">
        <table class="table table-hover align-middle mb-0 w-100">
            <thead class="text-muted small text-uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">
                <tr>
                    <th class="ps-4 pb-3">Comprobante</th>
                    <th class="pb-3">Cliente</th>
                    <th class="pb-3">Fecha &amp; Hora</th>
                    <th class="pb-3">Tipo de Venta</th>
                    <th class="text-end pb-3">Montos</th>
                    <th class="text-center pb-3">Estado</th>
                    <th class="text-end pe-4 pb-3">Acciones</th>
                </tr>
            </thead>
            <tbody id="ventas-tbody">
                @forelse($ventas as $v)
                @php
                    $esFiado = in_array($v->tipoVenta?->nombre, ['Fiado', 'Crédito']);
                @endphp
                <tr>
                    <td class="ps-4">
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark fs-6">#{{ str_pad($v->id, 5, '0', STR_PAD_LEFT) }}</span>
                            @if($v->ncf)
                                <span class="badge bg-light text-muted mt-1 border rounded-pill text-truncate" style="max-width:130px;">
                                    <i class="bi bi-receipt-cutoff me-1"></i>{{ $v->ncf }}
                                </span>
                            @else
                                <span class="text-muted small mt-1">&mdash;</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            @php
                                $nombreCliente = $v->cliente->nombre ?? 'Consumidor Final';
                                $firstLetter = strtoupper(substr($nombreCliente, 0, 1));
                                $colors = ['#f87171', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa', '#f472b6'];
                                $color = $v->cliente ? $colors[crc32($nombreCliente) % count($colors)] : '#9ca3af';
                            @endphp
                            <div class="avatar-circle text-white me-3 shadow-sm" style="background-color: {{ $color }}; width:40px;height:40px;font-size:1.1rem;">
                                {{ $firstLetter }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark fs-6">{{ $nombreCliente }}</div>
                                <div class="text-muted small"><i class="bi bi-person-badge me-1"></i>Cajero: {{ $v->usuario->name ?? 'Sistema' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-medium text-dark">{{ $v->created_at->format('d/m/Y') }}</div>
                        <div class="text-muted small"><i class="bi bi-clock me-1"></i>{{ $v->created_at->format('h:i A') }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $esFiado ? 'bg-warning bg-opacity-25 text-dark' : 'bg-info bg-opacity-10 text-info' }} rounded-pill">
                            @if($esFiado)
                                <i class="bi bi-credit-card me-1"></i>
                            @else
                                <i class="bi bi-cash me-1"></i>
                            @endif
                            {{ $v->tipoVenta->nombre ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="text-muted small mb-1">Sub: RD$ {{ number_format($v->total - $v->impuestos, 2) }}</div>
                        <div class="fw-bold text-primary fs-5">RD$ {{ number_format($v->total, 2) }}</div>
                    </td>
                    <td class="text-center">
                        @if ($v->estado == 'completada')
                            <span class="status-badge bg-success bg-opacity-10 text-success">
                                <i class="bi bi-check-circle-fill me-1"></i>Pagada
                            </span>
                        @elseif ($v->estado == 'cuenta_abierta')
                            <span class="status-badge bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-door-open-fill me-1"></i>Cta. Abierta
                            </span>
                        @else
                            <span class="status-badge bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-exclamation-circle-fill me-1"></i>Por Pagar
                            </span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="dropdown">
                            <button class="btn btn-icon-hover text-muted" data-bs-toggle="dropdown" title="Acciones">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                <li><a class="dropdown-item py-2" href="{{ route('ventas.show', $v->id) }}"><i class="bi bi-eye text-info me-2"></i>Ver Detalle</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('pagos.realizar', $v->id) }}"><i class="bi bi-cash-coin text-primary me-2"></i>Cobrar / Abono</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('venta.pdf', $v->id) }}"><i class="bi bi-printer text-secondary me-2"></i>Reimprimir</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('ventas.destroy', $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Anular esta venta?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item py-2 text-danger"><i class="bi bi-x-circle text-danger me-2"></i>Anular</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center p-5">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-4" style="width:100px;height:100px;">
                                <i class="bi bi-receipt text-muted opacity-50" style="font-size:3rem;"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">No hay ventas registradas</h4>
                            <p class="text-muted mb-4 text-center" style="max-width:400px;">Aún no se han registrado ventas con estos filtros.</p>
                            @can('ventas.create')
                            <a href="{{ route('ventas.create') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                                <i class="bi bi-plus-lg me-2"></i> Nueva Venta
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ventas->hasPages())
    <div class="mt-4 d-flex justify-content-center" id="pagination-container">
        {{ $ventas->withQueryString()->links() }}
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('busqueda-cliente');
        const tableBody = document.getElementById('ventas-tbody');
        const pagination = document.getElementById('pagination-container');
        let timeout = null;

        if (!searchInput) return;

        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const query = this.value;
                const url = new URL(window.location.href);
                url.searchParams.set('cliente', query);

                if (tableBody) tableBody.style.opacity = '0.5';

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTbody = doc.getElementById('ventas-tbody');
                    const newPag = doc.getElementById('pagination-container');

                    if (newTbody && tableBody) {
                        tableBody.innerHTML = newTbody.innerHTML;
                        tableBody.style.opacity = '1';
                    }
                    if (newPag && pagination) {
                        pagination.innerHTML = newPag.innerHTML;
                    } else if (pagination) {
                        pagination.innerHTML = '';
                    }
                })
                .catch(() => {
                    if (tableBody) tableBody.style.opacity = '1';
                });
            }, 400);
        });
    });
</script>
@endsection