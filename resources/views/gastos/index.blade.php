@extends('layouts.app')

@section('title', 'Gastos')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-cash-coin text-warning me-2"></i>
                Gastos
            </h2>
            <p class="text-muted mb-0">Registro de gastos operativos</p>
        </div>
        <div>
            @can('gastos.create')
            <a href="{{ route('gastos.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Gasto
            </a>
            @endcan
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;letter-spacing:.5px;">Total Gastos</small>
                    <h4 class="fw-bold text-warning mb-0 mt-1">RD$ {{ number_format($totalGastos, 2) }}</h4>
                    <small class="text-muted">{{ $gastos->total() }} registro(s)</small>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="d-flex flex-wrap gap-2">
                        <span class="text-muted small fw-bold me-1">Categorías:</span>
                        <a href="{{ route('gastos.index') }}" class="badge bg-light text-dark rounded-pill text-decoration-none px-3 py-2 {{ !request('categoria') ? 'bg-primary bg-opacity-10 text-primary' : '' }}">Todas</a>
                        @foreach($categorias as $key => $label)
                            <a href="{{ route('gastos.index', array_merge(request()->all(), ['categoria' => $key, 'page' => null])) }}" 
                               class="badge bg-light text-dark rounded-pill text-decoration-none px-3 py-2 {{ request('categoria') === $key ? 'bg-primary bg-opacity-10 text-primary' : '' }}">
                                {{ $label }}
                                @if(isset($totalPorCategoria[$key]))
                                    <span class="ms-1">(RD${{ number_format($totalPorCategoria[$key], 0) }})</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('gastos.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-0 bg-white" placeholder="Buscar por descripción o comprobante..." value="{{ request('search') }}" autocomplete="off">
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
                    <a href="{{ route('gastos.index') }}" class="btn btn-light rounded-pill"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-lg-2 text-end">
                    <span class="fw-bold text-muted small">Filtrado: RD$ {{ number_format($totalGastos, 2) }}</span>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3">Descripción</th>
                        <th>Categoría</th>
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
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">{{ $categorias[$gasto->categoria] ?? $gasto->categoria }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="fw-bold text-warning">RD$ {{ number_format($gasto->monto, 2) }}</td>
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
                                    <span class="badge bg-light text-dark rounded-pill">{{ $gasto->comprobante }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @can('gastos.edit')
                                <a href="{{ route('gastos.edit', $gasto) }}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('gastos.delete')
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" 
                                        onclick="confirmDelete('{{ route('gastos.destroy', $gasto) }}', '{{ addslashes($gasto->descripcion) }}')"
                                        title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2 mb-0">No hay gastos registrados</p>
                                @can('gastos.create')
                                <a href="{{ route('gastos.create') }}" class="btn btn-primary rounded-pill mt-2">Registrar primer gasto</a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0">
            {{ $gastos->links() }}
        </div>
    </div>
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
