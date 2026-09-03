@extends('layouts.app')

@section('title', 'No Conformidades')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-abierta { background: #fee2e2; color: #dc2626; }
    .badge-en_analisis { background: #fef3c7; color: #d97706; }
    .badge-en_accion { background: #dbeafe; color: #2563eb; }
    .badge-verificando { background: #e0f2fe; color: #0284c7; }
    .badge-cerrada { background: #dcfce7; color: #16a34a; }
    .badge-grave { background: #fee2e2; color: #dc2626; }
    .badge-menor { background: #fef3c7; color: #d97706; }
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
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">No Conformidades</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i> Control y seguimiento de no conformidades del SGC
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('sgc.no-conformidades.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva NC
                </a>
            </div>
        </div>
        <div class="mt-3">
            <form method="GET" action="{{ route('sgc.no-conformidades.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <i class="bi bi-search text-white-50 small"></i>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar..." class="border-0 bg-transparent text-white small" style="outline:none;width:200px;">
                </div>
                <select name="estado" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todos los estados</option>
                    <option value="abierta" {{ request('estado')=='abierta' ? 'selected' : '' }}>Abierta</option>
                    <option value="en_analisis" {{ request('estado')=='en_analisis' ? 'selected' : '' }}>En Análisis</option>
                    <option value="en_accion" {{ request('estado')=='en_accion' ? 'selected' : '' }}>En Acción</option>
                    <option value="verificando" {{ request('estado')=='verificando' ? 'selected' : '' }}>Verificando</option>
                    <option value="cerrada" {{ request('estado')=='cerrada' ? 'selected' : '' }}>Cerrada</option>
                </select>
                <select name="gravedad" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todas las gravedades</option>
                    <option value="mayor" {{ request('gravedad')=='mayor' ? 'selected' : '' }}>Mayor</option>
                    <option value="menor" {{ request('gravedad')=='menor' ? 'selected' : '' }}>Menor</option>
                </select>
                <select name="origen" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todos los orígenes</option>
                    <option value="auditoria" {{ request('origen')=='auditoria' ? 'selected' : '' }}>Auditoría</option>
                    <option value="proceso_interno" {{ request('origen')=='proceso_interno' ? 'selected' : '' }}>Proceso Interno</option>
                    <option value="reclamo_cliente" {{ request('origen')=='reclamo_cliente' ? 'selected' : '' }}>Reclamo Cliente</option>
                    <option value="observacion_direccion" {{ request('origen')=='observacion_direccion' ? 'selected' : '' }}>Observación Dirección</option>
                </select>
                <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-3">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                @if(request('buscar') || request('estado') || request('gravedad') || request('origen'))
                    <a href="{{ route('sgc.no-conformidades.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-3 text-white border-white border-opacity-25">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="nc-table">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Fecha</th>
                        <th>Origen</th>
                        <th>Gravedad</th>
                        <th>Estado</th>
                        <th>Asignado A</th>
                        <th>Fecha Límite</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($noConformidades as $nc)
                    <tr>
                        <td><code>{{ $nc->numero_label }}</code></td>
                        <td>{{ $nc->fecha_identificacion ? $nc->fecha_identificacion->format('d/m/Y') : ($nc->fecha_ocurrencia ? $nc->fecha_ocurrencia->format('d/m/Y') : '-') }}</td>
                        <td><span class="badge-status badge-{{ $nc->origen }}">{{ $nc->origen_label }}</span></td>
                        <td><span class="badge-status badge-{{ $nc->gravedad === 'mayor' ? 'grave' : 'menor' }}">{{ $nc->gravedad_label }}</span></td>
                        <td><span class="badge-status badge-{{ $nc->estado }}">{{ $nc->estado_label }}</span></td>
                        <td>{{ $nc->asignado_a ? $nc->asignadoA->name : '-' }}</td>
                        <td>
                            @if($nc->fecha_limite)
                                <span class="{{ $nc->es_vencida ? 'text-danger fw-bold' : '' }}">{{ $nc->fecha_limite->format('d/m/Y') }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('sgc.no-conformidades.show', $nc) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('sgc.no-conformidades.edit', $nc) }}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar">
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
    const table = document.getElementById('nc-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron no conformidades',
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
