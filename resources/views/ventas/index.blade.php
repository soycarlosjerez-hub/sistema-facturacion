@extends('layouts.app')

@section('title', 'Historial de Ventas')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-receipt text-primary me-2"></i>
                Ventas
            </h2>
            <p class="text-muted mb-0">Gestión de ventas y facturación</p>
        </div>
        <div>
            @can('ventas.create')
            <a href="{{ route('ventas.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> Nueva Venta
            </a>
            @endcan
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3 bg-light bg-opacity-50">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <div class="input-group input-group-merge border-0 shadow-none">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="cliente" class="form-control border-0 bg-white" 
                               placeholder="Buscar por cliente..." value="{{ request('cliente') }}">
                    </div>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="desde" class="form-control border-0 bg-white" value="{{ request('desde') }}">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="hasta" class="form-control border-0 bg-white" value="{{ request('hasta') }}">
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill w-100">Filtrar</button>
                </div>
                <div class="col-lg-2 text-end">
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill dropdown-toggle w-100" data-bs-toggle="dropdown">
                            Exportar
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                            <li><a class="dropdown-item" href="{{ route('ventas.pdf') }}"><i class="bi bi-file-pdf text-danger me-2"></i> PDF</a></li>
                            <li><a class="dropdown-item" href="{{ route('ventas.exportar', request()->query()) }}"><i class="bi bi-file-excel text-success me-2"></i> Excel</a></li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Ventas -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Folio</th>
                        <th>Cliente</th>
                        <th>Fecha y Hora</th>
                        <th>Tipo de Venta</th>
                        <th>Total Facturado</th>
                        <th>Estado de Pago</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $v)
                    @php
                        $totalPagado = $v->pagos?->sum('monto') ?? 0;
                        $esFiado = in_array($v->tipoVenta?->nombre, ['Fiado', 'Crédito']);
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">#{{ str_pad($v->id, 5, '0', STR_PAD_LEFT) }}</div>
                            @if($v->ncf)
                                <small class="badge bg-light text-muted border-0 p-0">{{ $v->ncf }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark small">{{ $v->cliente->nombre ?? 'Consumidor Final' }}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">Cajero: {{ $v->usuario->name ?? 'Sistema' }}</small>
                        </td>
                        <td>
                            <div class="small">{{ $v->created_at->format('d/m/Y') }}</div>
                            <div class="text-muted small" style="font-size: 0.7rem;">{{ $v->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $esFiado ? 'bg-warning bg-opacity-10 text-warning' : 'bg-info bg-opacity-10 text-info' }} rounded-pill px-3">
                                {{ $v->tipoVenta->nombre ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">RD$ {{ number_format($v->total, 2) }}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">ITBIS: RD$ {{ number_format($v->impuestos, 2) }}</small>
                        </td>
                        <td>
                            @if ($v->estado == 'completada')
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Pagada</span>
                            @elseif ($v->estado == 'cuenta_abierta')
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">CTA. ABIERTA</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">FIAO</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                    <li><a class="dropdown-item" href="{{ route('ventas.show', $v->id) }}"><i class="bi bi-eye text-primary me-2"></i> Detalles</a></li>
                                    <li><a class="dropdown-item" href="{{ route('pagos.realizar', $v->id) }}"><i class="bi bi-cash-coin text-success me-2"></i> Cobrar / Abono</a></li>
                                    <li><a class="dropdown-item" href="{{ route('venta.pdf', $v->id) }}"><i class="bi bi-printer text-muted me-2"></i> Reimprimir</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('ventas.destroy', $v->id) }}" method="POST" onsubmit="return confirm('¿Anular esta venta?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-x-circle me-2"></i> Anular Venta</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-receipt display-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-3">No hay registros de ventas que mostrar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $ventas->withQueryString()->links() }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="cliente"]');
        const tableBody = document.querySelector('tbody');
        const pagination = document.querySelector('.mt-4');
        let timeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const query = this.value;
                const url = `{{ route('ventas.index') }}?cliente=${query}`;

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    tableBody.innerHTML = doc.querySelector('tbody').innerHTML;
                    if (pagination && doc.querySelector('.mt-4')) {
                        pagination.innerHTML = doc.querySelector('.mt-4').innerHTML;
                    }
                });
            }, 300);
        });
    });
</script>
@endsection