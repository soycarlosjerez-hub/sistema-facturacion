@extends('layouts.app')

@section('title', 'Cotizaciones')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-file-earmark-text text-primary me-2"></i>
                Cotizaciones
            </h2>
            <p class="text-muted mb-0">Gestión de cotizaciones y presupuestos</p>
        </div>
        <div>
            @can('cotizaciones.create')
            <a href="{{ route('cotizaciones.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> Nueva Cotización
            </a>
            @endcan
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 col-lg-2">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                                <i class="bi bi-file-earmark-text fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Total</div>
                            <div class="fw-bold fs-5">{{ number_format($stats['total']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                                <i class="bi bi-clock fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Pendientes</div>
                            <div class="fw-bold fs-5">{{ number_format($stats['pendientes']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                                <i class="bi bi-check-circle fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Aprobadas</div>
                            <div class="fw-bold fs-5">{{ number_format($stats['aprobadas']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                                <i class="bi bi-exclamation-triangle fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Vencidas</div>
                            <div class="fw-bold fs-5">{{ number_format($stats['vencidas']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 p-2">
                                <i class="bi bi-arrow-right-circle fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Convertidas</div>
                            <div class="fw-bold fs-5">{{ number_format($stats['convertidas']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary bg-gradient">
                <div class="card-body p-3 text-white">
                    <div class="small opacity-75">Monto Activo</div>
                    <div class="fw-bold fs-5">RD${{ number_format($stats['monto_total'], 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('cotizaciones.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Buscar</label>
                    <input type="text" name="buscar" class="form-control" placeholder="Número, cliente..." value="{{ request('buscar') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Cotizacion::ESTADOS as $key => $estado)
                            <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>
                                {{ $estado['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de cotizaciones -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Número</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Validez</th>
                        <th>Estado</th>
                        <th class="text-end">Total</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cotizaciones as $cot)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('cotizaciones.show', $cot) }}" class="text-decoration-none fw-bold">
                                    {{ $cot->numero }}
                                </a>
                                <div class="small text-muted">{{ $cot->items->count() }} items</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $cot->cliente?->nombre ?? 'Consumidor Final' }}</div>
                                @if($cot->user)
                                    <div class="small text-muted">por {{ $cot->user->name }}</div>
                                @endif
                            </td>
                            <td>
                                <div>{{ $cot->fecha->format('d/m/Y') }}</div>
                                <div class="small text-muted">{{ $cot->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <div>{{ $cot->fecha_validez->format('d/m/Y') }}</div>
                                @if($cot->esta_vencida)
                                    <span class="badge bg-danger bg-opacity-10 text-danger small">
                                        <i class="bi bi-exclamation-circle me-1"></i>Vencida
                                    </span>
                                @else
                                    <div class="small text-muted">{{ $cot->fecha_validez->diffForHumans() }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $cot->estado_color }} bg-opacity-10 text-{{ $cot->estado_color }} rounded-pill px-3">
                                    <i class="bi bi-{{ $cot->estado_icon }} me-1"></i>
                                    {{ $cot->estado_label }}
                                </span>
                            </td>
                            <td class="text-end fw-bold">RD${{ number_format($cot->total, 2) }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('cotizaciones.show', $cot) }}" class="btn btn-outline-primary" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('cotizaciones.pdf', $cot) }}" class="btn btn-outline-secondary" title="PDF" target="_blank">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                    @if($cot->puede_convertirse && auth()->user()->can('cotizaciones.convertir'))
                                        <button type="button" class="btn btn-outline-success" 
                                                onclick="confirmarConvertir({{ $cot->id }}, '{{ $cot->numero }}')" 
                                                title="Convertir a venta">
                                            <i class="bi bi-arrow-right-circle"></i>
                                        </button>
                                    @endif
                                    @can('cotizaciones.edit')
                                        @if(!in_array($cot->estado, ['convertida', 'anulada']))
                                        <a href="{{ route('cotizaciones.edit', $cot) }}" class="btn btn-outline-warning" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endif
                                    @endcan
                                    @can('cotizaciones.delete')
                                        @if($cot->estado !== 'convertida')
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirmarEliminar({{ $cot->id }}, '{{ $cot->numero }}')" 
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 opacity-50"></i>
                                    <p class="mt-2">No hay cotizaciones registradas</p>
                                    @can('cotizaciones.create')
                                        <a href="{{ route('cotizaciones.create') }}" class="btn btn-primary rounded-pill">
                                            <i class="bi bi-plus-lg me-1"></i> Crear la primera
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($cotizaciones->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $cotizaciones->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Form para eliminar -->
<form id="form-eliminar" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function confirmarEliminar(id, numero) {
    Swal.fire({
        title: '¿Eliminar cotización?',
        text: `La cotización ${numero} será eliminada`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('form-eliminar');
            form.action = `{{ url('cotizaciones') }}/${id}`;
            form.submit();
        }
    });
}

function confirmarConvertir(id, numero) {
    Swal.fire({
        title: '¿Convertir a venta?',
        html: `La cotización <strong>${numero}</strong> se convertirá en una venta.<br>Esta acción no se puede deshacer.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, convertir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('cotizaciones') }}/${id}/convertir`;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
