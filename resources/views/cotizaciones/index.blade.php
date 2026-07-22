@extends('layouts.app')

@section('title', 'Cotizaciones')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .card-footer { background: rgba(15,23,42,.8); border-color: #334155; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5;">
    <!-- Header -->
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div>
                    <h2 class="ui-header-title">Cotizaciones</h2>
                    <div class="ui-header-meta">Gestión de cotizaciones y presupuestos</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('cotizaciones.create')
                <a href="{{ route('cotizaciones.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Cotización
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 col-lg-2">
            <div class="ui-stat h-100" style="--delay:.05s">
                <div class="ui-stat-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                                <i class="bi bi-file-earmark-text fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="ui-stat-label">Total</div>
                            <div class="ui-stat-value">{{ number_format($stats['total']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="ui-stat h-100" style="--delay:.1s">
                <div class="ui-stat-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                                <i class="bi bi-clock fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="ui-stat-label">Pendientes</div>
                            <div class="ui-stat-value">{{ number_format($stats['pendientes']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="ui-stat h-100" style="--delay:.15s">
                <div class="ui-stat-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                                <i class="bi bi-check-circle fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="ui-stat-label">Aprobadas</div>
                            <div class="ui-stat-value">{{ number_format($stats['aprobadas']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="ui-stat h-100" style="--delay:.2s">
                <div class="ui-stat-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                                <i class="bi bi-exclamation-triangle fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="ui-stat-label">Vencidas</div>
                            <div class="ui-stat-value">{{ number_format($stats['vencidas']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="ui-stat h-100" style="--delay:.25s">
                <div class="ui-stat-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 p-2">
                                <i class="bi bi-arrow-right-circle fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="ui-stat-label">Convertidas</div>
                            <div class="ui-stat-value">{{ number_format($stats['convertidas']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-2">
            <div class="ui-stat h-100 bg-primary bg-gradient" style="--delay:.3s">
                <div class="ui-stat-body p-3 text-white">
                    <div class="ui-stat-label opacity-75">Monto Activo</div>
                    <div class="ui-stat-value">RD${{ number_format($stats['monto_total'], 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="ui-card mb-3" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" action="{{ route('cotizaciones.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="ui-label small">Buscar</label>
                    <input type="text" name="buscar" class="ui-input" placeholder="Número, cliente..." value="{{ request('buscar') }}">
                </div>
                <div class="col-md-2">
                    <label class="ui-label small">Estado</label>
                    <select name="estado" class="ui-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Cotizacion::ESTADOS as $key => $estado)
                            <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>
                                {{ $estado['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="ui-label small">Desde</label>
                    <input type="date" name="fecha_desde" class="ui-input" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="ui-label small">Hasta</label>
                    <input type="date" name="fecha_hasta" class="ui-input" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="ui-btn ui-btn-solid w-100">
                        <i class="bi bi-search me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de cotizaciones -->
    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="table-responsive">
            <table class="ui-table table-hover align-middle mb-0">
                <thead>
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
                                        <a href="{{ route('cotizaciones.edit', $cot) }}" class="ui-action ui-action-edit" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endif
                                    @endcan
                                    @can('cotizaciones.delete')
                                        @if($cot->estado !== 'convertida')
                                        <button type="button" class="ui-action ui-action-delete" 
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