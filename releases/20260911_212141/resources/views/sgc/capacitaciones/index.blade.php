@extends('layouts.app')

@section('title', 'Capacitaciones')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-programada { background: #dbeafe; color: #2563eb; }
    .badge-en_curso { background: #fef3c7; color: #d97706; }
    .badge-completada { background: #dcfce7; color: #16a34a; }
    .badge-cancelada { background: #f1f5f9; color: #64748b; }
    .badge-presencial { background: #dbeafe; color: #2563eb; }
    .badge-virtual { background: #e0f2fe; color: #0284c7; }
    .badge-hibrido { background: #fef3c7; color: #d97706; }
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
                    <i class="bi bi-mortarboard"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Capacitaciones</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i> Programación y seguimiento de capacitaciones del SGC
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('sgc.capacitaciones.competencias') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill me-2">
                    <i class="bi bi-award me-1"></i> Competencias
                </a>
                <a href="{{ route('sgc.capacitaciones.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Capacitación
                </a>
            </div>
        </div>
        <div class="mt-3">
            <form method="GET" action="{{ route('sgc.capacitaciones.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <i class="bi bi-search text-white-50 small"></i>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar..." class="border-0 bg-transparent text-white small" style="outline:none;width:200px;">
                </div>
                <select name="estado" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todos los estados</option>
                    <option value="programada" {{ request('estado')=='programada' ? 'selected' : '' }}>Programada</option>
                    <option value="en_curso" {{ request('estado')=='en_curso' ? 'selected' : '' }}>En Curso</option>
                    <option value="completada" {{ request('estado')=='completada' ? 'selected' : '' }}>Completada</option>
                    <option value="cancelada" {{ request('estado')=='cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
                <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-3">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                @if(request('buscar') || request('estado'))
                    <a href="{{ route('sgc.capacitaciones.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-3 text-white border-white border-opacity-25">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="capacitaciones-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Modalidad</th>
                        <th>Instructor</th>
                        <th>Participantes</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($capacitaciones as $cap)
                    <tr>
                        <td>{{ Str::limit($cap->titulo, 40) }}</td>
                        <td>{{ $cap->fecha ? $cap->fecha->format('d/m/Y') : '-' }}</td>
                        <td>{{ $cap->horario_label }}</td>
                        <td><span class="badge-status badge-{{ $cap->modalidad }}">{{ $cap->modalidad_label }}</span></td>
                        <td>{{ $cap->instructor_label }}</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:.7rem;">{{ $cap->participantes->count() }}</span></td>
                        <td><span class="badge-status badge-{{ $cap->estado }}">{{ $cap->estado_label }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('sgc.capacitaciones.show', $cap) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('sgc.capacitaciones.edit', $cap) }}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
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
    const table = document.getElementById('capacitaciones-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron capacitaciones',
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_ total)'
            },
            columnDefs: [
                { orderable: false, targets: [7] }
            ],
            order: [[1, 'desc']],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    }
});
</script>
@endpush
@endsection
