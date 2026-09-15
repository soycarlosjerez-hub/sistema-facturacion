@extends('layouts.app')

@section('title', 'Programas de Auditoría')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-programada { background: #dbeafe; color: #2563eb; }
    .badge-en_curso { background: #fef3c7; color: #d97706; }
    .badge-completada { background: #dcfce7; color: #16a34a; }
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
                    <i class="bi bi-calendar3"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Programas de Auditoría</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.auditorias.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Programación anual de auditorías internas
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('sgc.auditorias.programas.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Programa
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="programas-table">
                <thead>
                    <tr>
                        <th>Año</th>
                        <th>Descripción</th>
                        <th>Auditor Jefe</th>
                        <th>Periodo</th>
                        <th>Auditorías</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programas as $prog)
                    <tr>
                        <td><code>{{ $prog->ano_label }}</code></td>
                        <td>{{ $prog->descripcion_truncada ?: '-' }}</td>
                        <td>{{ $prog->auditor_jefe_label }}</td>
                        <td>{{ $prog->periodo_label }}</td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:.7rem;">{{ $prog->auditorias_count }}</span>
                            @if($prog->completadas_count > 0)
                            <span class="badge bg-success bg-opacity-10 text-success" style="font-size:.65rem;">{{ $prog->completadas_count }} OK</span>
                            @endif
                        </td>
                        <td><span class="badge-status badge-{{ $prog->estado }}">{{ $prog->estado_label }}</span></td>
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
    const table = document.getElementById('programas-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron programas',
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_ total)'
            },
            order: [[0, 'desc']],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    }
});
</script>
@endpush
@endsection
