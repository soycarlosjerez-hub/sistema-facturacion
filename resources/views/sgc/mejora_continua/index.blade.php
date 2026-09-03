@extends('layouts.app')

@section('title', 'Mejora Continua')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-propuesta { background: #f1f5f9; color: #64748b; }
    .badge-evaluando { background: #dbeafe; color: #2563eb; }
    .badge-aprobada { background: #e0f2fe; color: #0284c7; }
    .badge-en_curso { background: #fef3c7; color: #d97706; }
    .badge-completada { background: #dcfce7; color: #16a34a; }
    .badge-verificada { background: #d1fae5; color: #059669; }
    .badge-cerrada { background: #f1f5f9; color: #475569; }
    .badge-baja { background: #dbeafe; color: #2563eb; }
    .badge-media { background: #fef3c7; color: #d97706; }
    .badge-alta { background: #fed7aa; color: #ea580c; }
    .badge-urgente { background: #fee2e2; color: #dc2626; }
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
                    <i class="bi bi-arrow-up-circle"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Mejora Continua</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i> Gestión de mejoras continuas del SGC
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('sgc.mejora.propuestas') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill me-2">
                    <i class="bi bi-lightbulb me-1"></i> Propuestas
                </a>
                <a href="{{ route('sgc.mejora.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Mejora
                </a>
            </div>
        </div>
        <div class="mt-3">
            <form method="GET" action="{{ route('sgc.mejora.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <i class="bi bi-search text-white-50 small"></i>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar..." class="border-0 bg-transparent text-white small" style="outline:none;width:200px;">
                </div>
                <select name="fase" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todas las fases</option>
                    <option value="propuesta" {{ request('fase')=='propuesta' ? 'selected' : '' }}>Propuesta</option>
                    <option value="evaluando" {{ request('fase')=='evaluando' ? 'selected' : '' }}>Evaluando</option>
                    <option value="aprobada" {{ request('fase')=='aprobada' ? 'selected' : '' }}>Aprobada</option>
                    <option value="en_curso" {{ request('fase')=='en_curso' ? 'selected' : '' }}>En Curso</option>
                    <option value="completada" {{ request('fase')=='completada' ? 'selected' : '' }}>Completada</option>
                    <option value="verificada" {{ request('fase')=='verificada' ? 'selected' : '' }}>Verificada</option>
                    <option value="cerrada" {{ request('fase')=='cerrada' ? 'selected' : '' }}>Cerrada</option>
                </select>
                <select name="prioridad" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todas las prioridades</option>
                    <option value="baja" {{ request('prioridad')=='baja' ? 'selected' : '' }}>Baja</option>
                    <option value="media" {{ request('prioridad')=='media' ? 'selected' : '' }}>Media</option>
                    <option value="alta" {{ request('prioridad')=='alta' ? 'selected' : '' }}>Alta</option>
                    <option value="urgente" {{ request('prioridad')=='urgente' ? 'selected' : '' }}>Urgente</option>
                </select>
                <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-3">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                @if(request('buscar') || request('fase') || request('prioridad'))
                    <a href="{{ route('sgc.mejora.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-3 text-white border-white border-opacity-25">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="mejora-table">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Título</th>
                        <th>Origen</th>
                        <th>Prioridad</th>
                        <th>Fase</th>
                        <th>Responsable</th>
                        <th>Fecha Límite</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mejoras as $mejora)
                    <tr>
                        <td><code>{{ $mejora->numero_label }}</code></td>
                        <td>{{ $mejora->titulo_truncado }}</td>
                        <td>{{ ucfirst($mejora->origen ?? '-') }}</td>
                        <td><span class="badge-status badge-{{ $mejora->prioridad }}">{{ $mejora->prioridad_label }}</span></td>
                        <td><span class="badge-status badge-{{ $mejora->fase }}">{{ $mejora->fase_label }}</span></td>
                        <td>{{ $mejora->responsable_label }}</td>
                        <td>{{ $mejora->fecha_limite ? $mejora->fecha_limite->format('d/m/Y') : '-' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('sgc.mejora.show', $mejora) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver">
                                    <i class="bi bi-eye"></i>
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
    const table = document.getElementById('mejora-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron mejoras',
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
