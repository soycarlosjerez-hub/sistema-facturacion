@extends('layouts.app')

@section('title', 'Objetivos de Calidad')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-en_curso { background: #dbeafe; color: #2563eb; }
    .badge-cumplido { background: #dcfce7; color: #16a34a; }
    .badge-no_cumplido { background: #fee2e2; color: #dc2626; }
    .badge-atrasado { background: #fef3c7; color: #d97706; }
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
                    <i class="bi bi-bullseye"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Objetivos de Calidad</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i> Seguimiento de objetivos e indicadores de calidad
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('sgc.objetivos.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Objetivo
                </a>
            </div>
        </div>
        <div class="mt-3">
            <form method="GET" action="{{ route('sgc.objetivos.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <i class="bi bi-search text-white-50 small"></i>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar..." class="border-0 bg-transparent text-white small" style="outline:none;width:200px;">
                </div>
                <select name="estado" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todos los estados</option>
                    <option value="en_curso" {{ request('estado')=='en_curso' ? 'selected' : '' }}>En Curso</option>
                    <option value="cumplido" {{ request('estado')=='cumplido' ? 'selected' : '' }}>Cumplido</option>
                    <option value="no_cumplido" {{ request('estado')=='no_cumplido' ? 'selected' : '' }}>No Cumplido</option>
                    <option value="atrasado" {{ request('estado')=='atrasado' ? 'selected' : '' }}>Atrasado</option>
                </select>
                <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-3">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                @if(request('buscar') || request('estado'))
                    <a href="{{ route('sgc.objetivos.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-3 text-white border-white border-opacity-25">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="objetivos-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Título</th>
                        <th>Indicador</th>
                        <th>Meta</th>
                        <th>Valor Actual</th>
                        <th>Cumplimiento</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($objetivos as $obj)
                    <tr>
                        <td><code>{{ $obj->codigo }}</code></td>
                        <td>{{ Str::limit($obj->titulo, 40) }}</td>
                        <td>{{ $obj->indicador ?? '-' }}</td>
                        <td>{{ $obj->meta }}</td>
                        <td>{{ $obj->valor_actual ?? '-' }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:6px;max-width:80px;">
                                    <div class="progress-bar {{ $obj->cumplimiento_bar }}" style="width:{{ min($obj->cumplimiento ?? 0, 100) }}%"></div>
                                </div>
                                <small class="text-muted">{{ $obj->cumplimiento ?? 0 }}%</small>
                            </div>
                        </td>
                        <td><span class="badge-status badge-{{ $obj->estado }}">{{ $obj->estado_label }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('sgc.objetivos.show', $obj) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('sgc.objetivos.edit', $obj) }}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar">
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
    const table = document.getElementById('objetivos-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron objetivos',
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_ total)'
            },
            columnDefs: [
                { orderable: false, targets: [7] }
            ],
            order: [[0, 'asc']],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    }
});
</script>
@endpush
@endsection
