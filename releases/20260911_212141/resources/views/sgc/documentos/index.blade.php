@extends('layouts.app')

@section('title', 'Documentos SGC')

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
.badge-borrador { background: #f1f5f9; color: #64748b; }
.badge-revision { background: #fef3c7; color: #d97706; }
.badge-aprobado { background: #dbeafe; color: #2563eb; }
.badge-vigente { background: #dcfce7; color: #16a34a; }
.badge-obsoleto { background: #fee2e2; color: #dc2626; }
.badge-archivado { background: #f3e8ff; color: #7c3aed; }
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
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Documentos SGC</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i> Gestión de documentos del sistema de gestión de calidad
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('sgc.documentos.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Documento
                </a>
            </div>
        </div>
        <div class="mt-3">
            <form method="GET" action="{{ route('sgc.documentos.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <i class="bi bi-search text-white-50 small"></i>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar..." class="border-0 bg-transparent text-white small" style="outline:none;width:200px;">
                </div>
                <select name="categoria" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todas las categorías</option>
                    <option value="politica" {{ request('categoria')=='politica' ? 'selected' : '' }}>Política</option>
                    <option value="trabajo_instructivo" {{ request('categoria')=='trabajo_instructivo' ? 'selected' : '' }}>Trabajo/Instructivo</option>
                    <option value="procedimiento" {{ request('categoria')=='procedimiento' ? 'selected' : '' }}>Procedimiento</option>
                    <option value="formulario" {{ request('categoria')=='formulario' ? 'selected' : '' }}>Formulario</option>
                    <option value="registro" {{ request('categoria')=='registro' ? 'selected' : '' }}>Registro</option>
                    <option value="matriz" {{ request('categoria')=='matriz' ? 'selected' : '' }}>Matriz</option>
                    <option value="reporte" {{ request('categoria')=='reporte' ? 'selected' : '' }}>Reporte</option>
                    <option value="otro" {{ request('categoria')=='otro' ? 'selected' : '' }}>Otro</option>
                </select>
                <select name="estado" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todos los estados</option>
                    <option value="borrador" {{ request('estado')=='borrador' ? 'selected' : '' }}>Borrador</option>
                    <option value="revision" {{ request('estado')=='revision' ? 'selected' : '' }}>En Revisión</option>
                    <option value="aprobado" {{ request('estado')=='aprobado' ? 'selected' : '' }}>Aprobado</option>
                    <option value="vigente" {{ request('estado')=='vigente' ? 'selected' : '' }}>Vigente</option>
                    <option value="obsoleto" {{ request('estado')=='obsoleto' ? 'selected' : '' }}>Obsoleto</option>
                    <option value="archivado" {{ request('estado')=='archivado' ? 'selected' : '' }}>Archivado</option>
                </select>
                <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-3">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                @if(request('buscar') || request('categoria') || request('estado'))
                    <a href="{{ route('sgc.documentos.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-3 text-white border-white border-opacity-25">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="documentos-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Versión</th>
                        <th>Estado</th>
                        <th>Fecha Emisión</th>
                        <th>Fecha Revisión</th>
                        <th>Proveedor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documentos as $doc)
                    <tr>
                        <td><code>{{ $doc->codigo }}</code></td>
                        <td>{{ Str::limit($doc->titulo, 40) }}</td>
                        <td>{{ $doc->categoria }}</td>
                        <td>v{{ $doc->version }}</td>
                        <td><span class="badge-status badge-{{ $doc->estado }}">{{ $doc->estado }}</span></td>
                        <td>{{ $doc->fecha_emision ? $doc->fecha_emision->format('d/m/Y') : '-' }}</td>
                        <td>{{ $doc->fecha_revision ? $doc->fecha_revision->format('d/m/Y') : '-' }}</td>
                        <td>{{ $doc->proveedor ? $doc->proveedor->nombre : '-' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('sgc.documentos.show', $doc) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('sgc.documentos.edit', $doc) }}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($doc->estado !== 'obsoleto' && $doc->estado !== 'archivado')
                                <form action="{{ route('sgc.documentos.destroy', $doc) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar documento?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3">
                {{ $documentos->links() }}
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('documentos-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron documentos',
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_ total)'
            },
            columnDefs: [
                { orderable: false, targets: [8] }
            ],
            order: [[0, 'desc']],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    }
});
</script>
@endpush
@endsection
