@extends('layouts.app')

@section('title', 'Gastos')

@push('styles')
@include('partials.premium-ui')
<style>
/* Gastos-specific styles */
.gastos-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(16,185,129,.04);
    margin: 0;
}
.gastos-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.gastos-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.gastos-table tbody tr:last-child td { border-bottom: none; }
.gastos-table tbody tr { transition: background .15s; }
.gastos-table tbody tr:hover { background: rgba(16,185,129,.03); }

body.dark-mode .gastos-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .gastos-table tbody td {
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
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Gastos</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-receipt me-1"></i>
                        Registro de gastos operativos
                        <span class="mx-2">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        {{ $gastos->total() }} registro(s)
                    </small>
                </div>
            </div>
            <div>
                @can('gastos.create')
                <a href="{{ route('gastos.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Gasto
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="premium-stat-card" style="animation-delay:.05s;">
                <div class="card-accent green"></div>
                <div class="card-body p-3 text-center">
                    <div class="stat-label mb-1">Total Gastos</div>
                    <div class="stat-value" style="color:#10b981;">RD$ {{ number_format($totalGastos, 2) }}</div>
                    <small class="text-muted">{{ $gastos->total() }} registro(s)</small>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="premium-stat-card" style="animation-delay:.1s;">
                <div class="card-accent green"></div>
                <div class="card-body p-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="stat-label me-1">Categorías:</span>
                        <a href="{{ route('gastos.index') }}" class="premium-badge {{ !request('categoria') ? 'active' : '' }}">Todas</a>
                        @foreach($categorias as $key => $label)
                            <a href="{{ route('gastos.index', array_merge(request()->all(), ['categoria' => $key, 'page' => null])) }}" 
                               class="premium-badge {{ request('categoria') === $key ? 'active' : '' }}">
                                {{ $label }}
                                @if(isset($totalPorCategoria[$key]))
                                    <span class="ms-1 opacity-75">(RD${{ number_format($totalPorCategoria[$key], 0) }})</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card mb-4" style="animation-delay:.15s;">
        <div class="card-accent green"></div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('gastos.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar por descripción o comprobante..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="desde" class="form-control" value="{{ request('desde') }}" placeholder="Desde">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}" placeholder="Hasta">
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('gastos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-lg-2 text-end">
                    <span class="fw-bold text-muted small">Filtrado: RD$ {{ number_format($totalGastos, 2) }}</span>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card" style="animation-delay:.2s;">
        <div class="card-accent green"></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table gastos-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Descripción</th>
                            <th>Categoría</th>
                            <th>Proveedor</th>
                            <th>Monto</th>
                            <th>Método Pago</th>
                            <th>Registrado por</th>
                            <th>Fecha</th>
                            <th class="text-center">Comprobante</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gastos as $gasto)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-semibold">{{ $gasto->descripcion }}</span>
                                    @if($gasto->notas)
                                        <br><small class="text-muted">{{ Str::limit($gasto->notas, 60) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($gasto->categoria)
                                        <span class="badge rounded-pill" style="background:rgba(16,185,129,.1);color:#059669;font-weight:600;">{{ $categorias[$gasto->categoria] ?? $gasto->categoria }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $gasto->proveedor?->nombre ?? '—' }}</span>
                                </td>
                                <td class="fw-bold" style="color:#059669;">RD$ {{ number_format($gasto->monto, 2) }}</td>
                                <td>
                                    @if($gasto->metodo_pago)
                                        <span class="text-muted small">{{ ucfirst($gasto->metodo_pago) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $gasto->user?->name ?? '—' }}</small></td>
                                <td><small>{{ $gasto->fecha_gasto->format('d/m/Y') }}</small></td>
                                <td class="text-center">
                                    @if($gasto->comprobante)
                                        <span class="badge rounded-pill" style="background:rgba(99,102,241,.1);color:#4f46e5;font-weight:600;">{{ $gasto->comprobante }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @can('gastos.edit')
                                    <a href="{{ route('gastos.edit', $gasto) }}" class="premium-btn-edit" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('gastos.delete')
                                    <button type="button" class="premium-btn-delete" 
                                            onclick="confirmDelete('{{ route('gastos.destroy', $gasto) }}', '{{ addslashes($gasto->descripcion) }}')"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1" style="color:#cbd5e1;"></i>
                                    <p class="mt-2 mb-0 fw-semibold">No hay gastos registrados</p>
                                    @can('gastos.create')
                                    <a href="{{ route('gastos.create') }}" class="btn btn-primary rounded-pill mt-2">Registrar primer gasto</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($gastos->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $gastos->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(url, desc) {
    Swal.fire({
        title: '¿Eliminar gasto?',
        text: `Se eliminará: "${desc}"`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = '@csrf @method("DELETE")';
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
