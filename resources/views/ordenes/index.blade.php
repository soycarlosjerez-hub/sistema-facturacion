@extends('layouts.app')

@push('styles')
<style>
.ordenes-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(59,130,246,.04);
    margin: 0;
}
.ordenes-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.ordenes-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.ordenes-table tbody tr:last-child td { border-bottom: none; }
.ordenes-table tbody tr { transition: background .15s; }
.ordenes-table tbody tr:hover { background: rgba(59,130,246,.03); }
body.dark-mode .ordenes-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .ordenes-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Órdenes</h1>
        <a href="{{ route('ordenes.create') }}" class="btn btn-primary">Nueva Orden</a>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5>Pendientes</h5>
                    <h3>{{ $totales['pendientes'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5>En Proceso</h5>
                    <h3>{{ $totales['en_proceso'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5>Total Hoy</h5>
                    <h3>RD$ {{ number_format($totales['hoy'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover ordenes-table">
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
                                    <a href="{{ route('ordenes.show', $orden) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @endcan

                                    @if(!in_array($orden->estado, ['completada', 'anulada']))
                                        @can('ordenes.update')
                                        <a href="{{ route('ordenes.show', $orden) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endcan

                                        @can('ordenes.pay')
                                        <a href="{{ route('ordenes.show', $orden) }}" class="btn btn-sm btn-outline-success" title="Cobrar">
                                            <i class="bi bi-cash-coin"></i>
                                        </a>
                                        @endcan

                                        @can('ordenes.cancel')
                                        <form action="{{ route('ordenes.destroy', $orden) }}" method="POST" class="d-inline form-anular">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="motivo" value="Anulada por usuario">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-trigger-anular" title="Anular">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    @endif

                                    @can('ordenes.view')
                                    <a href="{{ route('ordenes.ticket', $orden) }}" class="btn btn-sm btn-outline-secondary" title="Imprimir ticket" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    @endcan

                                    @if($orden->estado === 'anulada')
                                        @can('ordenes.cancel')
                                        <form action="{{ route('ordenes.forceDestroy', $orden) }}" method="POST" class="d-inline form-borrar">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-dark btn-trigger-borrar" title="Eliminar permanentemente">
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
            {{ $ordenes->links() }}
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
