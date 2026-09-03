@extends('layouts.app')

@section('title', 'Revisión por Dirección')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-programada { background: #dbeafe; color: #2563eb; }
    .badge-en_ejecucion { background: #fef3c7; color: #d97706; }
    .badge-completada { background: #dcfce7; color: #16a34a; }
    .badge-programada_type { background: #dbeafe; color: #2563eb; }
    .badge-extraordinaria { background: #fee2e2; color: #dc2626; }
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
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Revisión por Dirección</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i> Actas de revisión por la dirección del SGC
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('sgc.revision-direccion.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Revisión
                </a>
            </div>
        </div>
        <div class="mt-3">
            <form method="GET" action="{{ route('sgc.revision-direccion.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <i class="bi bi-search text-white-50 small"></i>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar..." class="border-0 bg-transparent text-white small" style="outline:none;width:200px;">
                </div>
                <select name="estado" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todos los estados</option>
                    <option value="programada" {{ request('estado')=='programada' ? 'selected' : '' }}>Programada</option>
                    <option value="en_ejecucion" {{ request('estado')=='en_ejecucion' ? 'selected' : '' }}>En Ejecución</option>
                    <option value="completada" {{ request('estado')=='completada' ? 'selected' : '' }}>Completada</option>
                </select>
                <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-3">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                @if(request('buscar') || request('estado'))
                    <a href="{{ route('sgc.revision-direccion.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-3 text-white border-white border-opacity-25">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="revision-table">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Duración</th>
                        <th>Asistentes</th>
                        <th>Salidas</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($revisiones as $rev)
                    <tr>
                        <td><code>{{ $rev->numero ? '#' . $rev->numero : '-' }}</code></td>
                        <td>{{ $rev->fecha ? $rev->fecha->format('d/m/Y') : '-' }}</td>
                        <td><span class="badge-status badge-{{ $rev->tipo }}">{{ $rev->tipo_label }}</span></td>
                        <td>{{ $rev->duracion_label }}</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:.7rem;">{{ $rev->asistentes_count }}</span></td>
                        <td><span class="badge bg-warning bg-opacity-10 text-warning" style="font-size:.7rem;">{{ $rev->salidas_count }}</span></td>
                        <td><span class="badge-status badge-{{ $rev->estado }}">{{ $rev->estado_label }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('sgc.revision-direccion.show', $rev) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver">
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
    const table = document.getElementById('revision-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron revisiones',
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
