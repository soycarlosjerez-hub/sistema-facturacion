@extends('layouts.app')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-bag-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Órdenes</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i>
                        <span>Gestión de órdenes de mostrador, delivery y pickup</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('ordenes.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Orden
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Pendientes</div>
                    <div class="ui-stat-value">{{ $totales['pendientes'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">En Proceso</div>
                    <div class="ui-stat-value">{{ $totales['en_proceso'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Total Hoy</div>
                    <div class="ui-stat-value">RD$ {{ number_format($totales['hoy'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.25s">
        <div class="ui-card-accent amber"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordenes as $orden)
                        <tr>
                            <td>{{ $orden->id }}</td>
                            <td>
                                <span class="badge bg-{{ $orden->tipo_orden === 'delivery' ? 'info' : ($orden->tipo_orden === 'pickup' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($orden->tipo_orden) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $orden->estado === 'pendiente' ? 'danger' : ($orden->estado === 'completada' ? 'success' : ($orden->estado === 'anulada' ? 'dark' : 'primary')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                                </span>
                            </td>
                            <td>{{ $orden->cliente?->nombre ?? '—' }}</td>
                            <td>RD$ {{ number_format($orden->subtotal + $orden->impuestos, 2) }}</td>
                            <td>{{ $orden->created_at->format('d/m/Y h:i A') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    @can('ordenes.view')
                                    <a href="{{ route('ordenes.show', $orden) }}" class="ui-action ui-action-view" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @endcan

                                    @if(!in_array($orden->estado, ['completada', 'anulada']))
                                        @can('ordenes.update')
                                        <a href="{{ route('ordenes.show', $orden) }}" class="ui-action ui-action-edit" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endcan

                                        @can('ordenes.pay')
                                        <a href="{{ route('ordenes.show', $orden) }}" class="ui-action" style="background:rgba(34,197,94,.1);color:#16a34a;border-color:rgba(34,197,94,.2);" title="Cobrar">
                                            <i class="bi bi-cash-coin"></i>
                                        </a>
                                        @endcan

                                        @can('ordenes.cancel')
                                        <form action="{{ route('ordenes.destroy', $orden) }}" method="POST" class="d-inline form-anular">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="motivo" value="Anulada por usuario">
                                            <button type="button" class="ui-action ui-action-delete btn-trigger-anular" title="Anular">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    @endif

                                    @can('ordenes.view')
                                    <a href="{{ route('ordenes.ticket', $orden) }}" class="ui-action ui-action-print" title="Imprimir ticket" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    @endcan

                                    @if($orden->estado === 'anulada')
                                        @can('ordenes.cancel')
                                        <form action="{{ route('ordenes.forceDestroy', $orden) }}" method="POST" class="d-inline form-borrar">
                                            @csrf @method('DELETE')
                                            <button type="button" class="ui-action" style="background:rgba(100,116,139,.1);color:#64748b;border-color:rgba(100,116,139,.2);" title="Eliminar permanentemente">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($ordenes->hasPages())
            <div class="p-3 border-top border-light">
                {{ $ordenes->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('click', function(e) {
    const btnAnular = e.target.closest('.btn-trigger-anular');
    if (btnAnular) {
        const form = btnAnular.closest('.form-anular');
        if (!form) return;
        Swal.fire({
            title: 'Anular Orden',
            text: '¿Estás seguro de anular esta orden?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ef4444',
        }).then(function(result) {
            if (result.isConfirmed) form.submit();
        });
        return;
    }

    const btnBorrar = e.target.closest('.btn-trigger-borrar');
    if (btnBorrar) {
        const form = btnBorrar.closest('.form-borrar');
        if (!form) return;
        Swal.fire({
            title: 'Eliminar Orden Permanentemente',
            html: '¿Estás seguro? Esta acción <strong>no se puede deshacer</strong> y eliminará la orden y todos sus registros asociados.',
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar permanentemente',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc2626',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise(resolve => {
                    Swal.fire({
                        title: 'Confirma la eliminación',
                        text: 'Escribe "ELIMINAR" para confirmar',
                        input: 'text',
                        inputPlaceholder: 'Escribe ELIMINAR',
                        showCancelButton: true,
                        confirmButtonText: 'Eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#dc2626',
                        preConfirm: (input) => {
                            if (input !== 'ELIMINAR') {
                                Swal.showValidationMessage('Debes escribir ELIMINAR');
                                return false;
                            }
                            return true;
                        }
                    }).then(r => {
                        if (r.isConfirmed) resolve();
                    });
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(function(result) {
            if (result.isConfirmed) form.submit();
        });
        return;
    }
});
</script>
@endpush
@endsection
