@extends('layouts.app')

@section('title', 'Paquetes de Lavado')

@push('styles')
@include('partials.premium-ui')
<style>
.paquete-item-badge { font-size: 0.7rem; }
.paquete-preview-card { background: rgba(6,182,212,0.04); border-radius: 0.75rem; border: 1px solid rgba(6,182,212,0.1); }

/* ===== DataTables premium (acento vía variables del .ui-page) ===== */
#paquetes-table_wrapper { padding: 0; }
#paquetes-table_wrapper > .row:first-child { padding: 0 1rem; margin-bottom: .5rem; }
#paquetes-table_wrapper .dataTables_length { font-size: .85rem; color: #64748b; }
#paquetes-table_wrapper .dataTables_length label { display:flex; align-items:center; gap:.5rem; margin:0; font-weight:500; }
#paquetes-table_wrapper .dataTables_length select {
    border-radius: .5rem; border: 1.5px solid #e2e8f0; padding: .35rem 2rem .35rem .75rem; font-size: .85rem;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E") no-repeat right .75rem center;
    appearance: none; cursor: pointer; transition: border-color .15s;
}
#paquetes-table_wrapper .dataTables_length select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(var(--accent-rgb), .1); outline: none; }
#paquetes-table_wrapper .dataTables_filter { text-align: right; }
#paquetes-table_wrapper .dataTables_filter label { display:flex; align-items:center; gap:.5rem; margin:0; font-weight:500; font-size:.85rem; color:#64748b; }
#paquetes-table_wrapper .dataTables_filter input {
    border-radius: 2rem; border: 1.5px solid #e2e8f0; padding: .45rem 1rem .45rem 2.2rem; font-size: .85rem;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.156a5 5 0 1 1 0-10 5 5 0 0 1 0 10z'/%3E%3C/svg%3E") no-repeat .75rem center;
    width: 240px; max-width: 100%; transition: all .15s;
}
#paquetes-table_wrapper .dataTables_filter input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(var(--accent-rgb), .1); outline: none; width: 280px; }
#paquetes-table_wrapper .dataTables_info { font-size: .8rem; color: #64748b; padding: .75rem 0; font-weight: 500; }
#paquetes-table_wrapper .dataTables_paginate { padding: .5rem 0; text-align: right; }
.paginate_button {
    display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 .6rem;
    margin: 0 2px; border: 1.5px solid #e2e8f0; border-radius: .5rem; background: #fff; color: #475569;
    font-size: .85rem; font-weight: 500; cursor: pointer; transition: all .15s; text-decoration: none; line-height: 1;
}
.paginate_button:hover { background: #f8fafc; border-color: #cbd5e1; color: #1e293b; }
.paginate_button.current { background: linear-gradient(135deg, var(--accent), var(--accent-hover)); border-color: var(--accent); color: #fff; font-weight: 700; box-shadow: 0 4px 14px rgba(var(--accent-rgb), .3); }
.paginate_button.disabled { opacity: .4; cursor: not-allowed; background: transparent; border-color: #e2e8f0; color: #94a3b8; }

body.dark-mode #paquetes-table_wrapper .dataTables_length select,
body.dark-mode #paquetes-table_wrapper .dataTables_filter input { background-color: #0f172a; color: #f1f5f9; border-color: #334155; }
body.dark-mode #paquetes-table_wrapper .dataTables_filter input::placeholder { color: #64748b; }
body.dark-mode #paquetes-table_wrapper .dataTables_info { color: #94a3b8; }
body.dark-mode .paginate_button { background: #0f172a; border-color: #334155; color: #94a3b8; }
body.dark-mode .paginate_button:hover { background: #1e293b; border-color: #475569; color: #f1f5f9; }
body.dark-mode .paginate_button.current { background: linear-gradient(135deg, var(--accent), var(--accent-hover)); border-color: var(--accent); color: #fff; }
body.dark-mode .paginate_button.disabled { background: transparent; border-color: #1e293b; color: #475569; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">
    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-gift"></i></div>
                <div>
                    <h4 class="ui-header-title">Paquetes de Lavado</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-box-seam me-1"></i>
                        <span>Gestión de paquetes de servicios</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('paquetes.create')
                <a href="{{ route('lavadero.paquetes.create') }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Paquete
                </a>
                @endcan
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success rounded-4 shadow-sm border-0 mb-3" style="border-left: 4px solid #198754 !important;">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
    @endif

    <div class="ui-card">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <table class="table ui-table nowrap" id="paquetes-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo Vehículo</th>
                        <th>Duración</th>
                        <th>Precio</th>
                        <th>Items</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-none" id="paquete-edit-url">{{ route('lavadero.paquetes.edit', ['paquete' => '__ID__']) }}</div>
<div class="d-none" id="paquete-delete-url">{{ route('lavadero.paquetes.destroy', ['paquete' => '__ID__']) }}</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#paquetes-table').DataTable({
        ajax: { url: '{{ route("lavadero.paquetes.index") }}?json=1', dataSrc: 'data' },
        columns: [
            { data: 'nombre', render: function(d) { return '<div class="fw-bold">' + d + '</div>' + (d._desc ? '<small class="text-muted">' + d._desc + '</small>' : ''); }},
            { data: 'aplicable_a_tipo', render: function(d) { return '<span class="ui-badge ui-badge-info">' + (d || 'Todos') + '</span>'; }},
            { data: 'duracion_minutos', render: function(d) { return d ? d + ' min' : '-'; }},
            { data: 'precio', render: function(d) { return '<span class="fw-bold" style="color:#06b6d4;">RD$ ' + parseFloat(d).toFixed(2) + '</span>'; }},
            { data: 'items_count', render: function(d) { return '<span class="ui-badge ui-badge-neutral">' + d + ' items</span>'; }},
            { data: 'activo', render: function(d) { return d ? '<span class="ui-badge ui-badge-success"><i class="bi bi-check-lg"></i> Sí</span>' : '<span class="ui-badge ui-badge-danger"><i class="bi bi-x-lg"></i> No</span>'; }},
            { data: null, orderable: false, render: function(row) {
                return '<a href="' + $('#paquete-edit-url').text().replace('__ID__', row.id) + '" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>' +
                       '<button class="ui-action ui-action-delete" onclick="UI.confirm.delete(\'{{ route("lavadero.paquetes.destroy", "__ID__") }}'.replace('__ID__', 'ROW_ID') + '\', \'' + row.nombre + '\')"><i class="bi bi-trash"></i></button>';
            }}
        ],
        dom: '<"row px-3 pt-2"<"col-sm-6"l><"col-sm-6"f>>' + '<"row"<"col-12"tr>>' + '<"row px-3 pb-2"<"col-sm-5"i><"col-sm-7"p>>',
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES' }
    });
});
</script>
@endsection
