@extends('layouts.app')

@section('title', 'Gestión de Riesgos')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-identificado { background: #dbeafe; color: #2563eb; }
    .badge-en_tratamiento { background: #fef3c7; color: #d97706; }
    .badge-cerrado { background: #dcfce7; color: #16a34a; }
    .badge-bajo { background: #dcfce7; color: #16a34a; }
    .badge-medio { background: #dbeafe; color: #2563eb; }
    .badge-alto { background: #fef3c7; color: #d97706; }
    .badge-critico { background: #fee2e2; color: #dc2626; }
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
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Gestión de Riesgos</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i> Identificación y tratamiento de riesgos del SGC
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('sgc.riesgos.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Riesgo
                </a>
            </div>
        </div>
        <div class="mt-3">
            <form method="GET" action="{{ route('sgc.riesgos.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <i class="bi bi-search text-white-50 small"></i>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar..." class="border-0 bg-transparent text-white small" style="outline:none;width:200px;">
                </div>
                <select name="estado" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todos los estados</option>
                    <option value="identificado" {{ request('estado')=='identificado' ? 'selected' : '' }}>Identificado</option>
                    <option value="en_tratamiento" {{ request('estado')=='en_tratamiento' ? 'selected' : '' }}>En Tratamiento</option>
                    <option value="cerrado" {{ request('estado')=='cerrado' ? 'selected' : '' }}>Cerrado</option>
                </select>
                <select name="clasificacion" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todas las clasificaciones</option>
                    <option value="bajo" {{ request('clasificacion')=='bajo' ? 'selected' : '' }}>Bajo</option>
                    <option value="medio" {{ request('clasificacion')=='medio' ? 'selected' : '' }}>Medio</option>
                    <option value="alto" {{ request('clasificacion')=='alto' ? 'selected' : '' }}>Alto</option>
                    <option value="critico" {{ request('clasificacion')=='critico' ? 'selected' : '' }}>Crítico</option>
                </select>
                <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-3">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                @if(request('buscar') || request('estado') || request('clasificacion'))
                    <a href="{{ route('sgc.riesgos.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-3 text-white border-white border-opacity-25">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="riesgos-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Área</th>
                        <th>Descripción</th>
                        <th>Prob. × Impacto</th>
                        <th>Clasificación</th>
                        <th>Estado</th>
                        <th>Responsable</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riesgos as $riesgo)
                    <tr>
                        <td><code>{{ $riesgo->codigo }}</code></td>
                        <td>{{ $riesgo->area }}</td>
                        <td>{{ Str::limit($riesgo->descripcion, 40) }}</td>
                        <td>{{ $riesgo->probabilidad }} × {{ $riesgo->impacto }} = {{ $riesgo->nivel }}</td>
                        <td><span class="badge-status badge-{{ $riesgo->clasificacion }}">{{ $riesgo->clasificacion_label }}</span></td>
                        <td><span class="badge-status badge-{{ $riesgo->estado }}">{{ $riesgo->estado_label }}</span></td>
                        <td>{{ $riesgo->responsable ? $riesgo->responsable->name : '-' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('sgc.riesgos.show', $riesgo) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('sgc.riesgos.edit', $riesgo) }}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar">
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
    const table = document.getElementById('riesgos-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron riesgos',
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_ total)'
            },
            columnDefs: [
                { orderable: false, targets: [7] }
            ],
            order: [[0, 'desc']],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    }
});
</script>
@endpush
@endsection
