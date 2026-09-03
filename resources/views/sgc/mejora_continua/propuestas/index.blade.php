@extends('layouts.app')

@section('title', 'Propuestas de Mejora')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-pendiente { background: #fef3c7; color: #d97706; }
    .badge-aprobada { background: #dcfce7; color: #16a34a; }
    .badge-rechazada { background: #fee2e2; color: #dc2626; }
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
                    <i class="bi bi-lightbulb"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Propuestas de Mejora</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.mejora.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Propuestas enviadas por colaboradores
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('sgc.mejora.propuestas.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Propuesta
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="propuestas-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Fecha</th>
                        <th>Mejora Asignada</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($propuestas as $prop)
                    <tr>
                        <td>{{ $prop->titulo_truncado }}</td>
                        <td>{{ $prop->autor_label }}</td>
                        <td>{{ $prop->fecha_label }}</td>
                        <td>{{ $prop->mejora_label ?: '-' }}</td>
                        <td><span class="badge-status badge-{{ $prop->estado }}">{{ $prop->estado_label }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                @if($prop->estado === 'pendiente' && $prop->mejora_continua_id)
                                <form action="{{ route('sgc.mejora.propuestas.aprobar', $prop) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success rounded-pill" title="Aprobar"><i class="bi bi-check-lg"></i></button>
                                </form>
                                <form action="{{ route('sgc.mejora.propuestas.rechazar', $prop) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger rounded-pill" title="Rechazar"><i class="bi bi-x-lg"></i></button>
                                </form>
                                @endif
                            </div>
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
    const table = document.getElementById('propuestas-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron propuestas',
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_ total)'
            },
            columnDefs: [
                { orderable: false, targets: [5] }
            ],
            order: [[2, 'desc']],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    }
});
</script>
@endpush
@endsection
