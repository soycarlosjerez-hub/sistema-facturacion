@extends('layouts.app')

@section('title', 'Competencias de Empleados')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-alto { background: #dcfce7; color: #16a34a; }
    .badge-medio { background: #fef3c7; color: #d97706; }
    .badge-bajo { background: #fee2e2; color: #dc2626; }
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
                    <i class="bi bi-award"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Competencias de Empleados</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.capacitaciones.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Evaluación de competencias del personal
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="competencias-table">
                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th>Competencia</th>
                        <th>Nivel Actual</th>
                        <th>Nivel Requerido</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($competencias ?? [] as $comp)
                    <tr>
                        <td>{{ $comp->empleado ? $comp->empleado->name : '-' }}</td>
                        <td>{{ $comp->nombre ?? '-' }}</td>
                        <td><span class="badge-status badge-{{ $comp->nivel_actual ?? 'medio' }}">{{ ucfirst($comp->nivel_actual ?? '-') }}</span></td>
                        <td>{{ $comp->nivel_requerido ?? '-' }}</td>
                        <td>{{ Str::limit($comp->observaciones ?? '-', 40) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No hay competencias registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('competencias-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron competencias',
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_ total)'
            },
            order: [[0, 'asc']],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    }
});
</script>
@endpush
@endsection
