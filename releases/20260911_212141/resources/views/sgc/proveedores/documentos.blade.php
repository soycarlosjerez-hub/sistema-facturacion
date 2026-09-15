@extends('layouts.app')

@section('title', 'Documentos - ' . $proveedor->nombre)

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
.dt-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .75rem 1rem;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
}
.dt-table tbody td {
    padding: .75rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .85rem;
}
.dt-table tbody tr:last-child td { border-bottom: none; }
.dt-table tbody tr { transition: background .15s; }
.dt-table tbody tr:hover { background: rgba(99,102,241,.03); }
.badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
.badge-vigente { background: #dcfce7; color: #16a34a; }
.badge-por_cargar { background: #e0e7ff; color: #4338ca; }
.badge-verificado { background: #dcfce7; color: #16a34a; }
.badge-pendiente { background: #fef3c7; color: #d97706; }
.badge-vencido { background: #fee2e2; color: #dc2626; }
</style>
@endpush

@section('content')
<div class="ui-page">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $proveedor->nombre }}</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.dashboard') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> SGC
                        </a>
                        Documentos del proveedor
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('sgc.documentos-proveedor.create', $proveedor) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Cargar Documento
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="documentos-proveedores-table">
                <thead>
                    <tr>
                        <th>Documento SGC</th>
                        <th>Fecha Carga</th>
                        <th>Fecha Vencimiento</th>
                        <th>Estado</th>
                        <th>Archivo</th>
                        <th>Subido Por</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documentos as $dp)
                    <tr>
                        <td>{{ $dp->documentoSgc ? $dp->documentoSgc->codigo : 'N/A' }}</td>
                        <td>{{ $dp->fechaCarga ? $dp->fechaCarga->format('d/m/Y') : '-' }}</td>
                        <td>{{ $dp->fechaVencimiento ? $dp->fechaVencimiento->format('d/m/Y') : '-' }}</td>
                        <td><span class="badge-status badge-{{ $dp->estado }}">{{ $dp->estado }}</span></td>
                        <td>
                            @if($dp->archivo_path)
                            <a href="{{ route('sgc.documentos-proveedor.archivo.show', $dp) }}" class="text-decoration-none" style="color:#6366f1;">
                                <i class="bi bi-download"></i> {{ $dp->archivo_original_name ?? 'Descargar' }}
                            </a>
                            @else
                            <span class="text-muted small">Sin archivo</span>
                            @endif
                        </td>
                        <td>{{ $dp->uploader ? $dp->uploader->name : '-' }}</td>
                        <td>
                            <form action="{{ route('sgc.documentos-proveedor.destroy', $dp) }}" method="POST" onsubmit="return confirm('¿Eliminar documento?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('documentos-proveedores-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '\u00AB', last: '\u00BB', next: '\u203A', previous: '\u2039' },
                zeroRecords: 'No se encontraron documentos',
                infoEmpty: 'Sin registros'
            },
            columnDefs: [{ orderable: false, targets: [6] }],
            order: [[1, 'desc']],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    }
});
</script>
@endpush
@endsection
