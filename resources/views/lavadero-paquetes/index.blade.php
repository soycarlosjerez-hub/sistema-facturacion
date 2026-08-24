@extends('layouts.app')

@section('title', 'Paquetes de Lavado')

@push('styles')
@include('partials.premium-ui')
<style>
.paquete-item-badge { font-size: 0.7rem; }
.paquete-preview-card { background: rgba(14,165,233,0.04); border-radius: 0.75rem; border: 1px solid rgba(14,165,233,0.1); }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#0ea5e9;--accent-rgb:14,165,233;--accent-hover:#0284c7;">
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
            { data: 'aplicable_a_tipo', render: function(d) { return '<span class="badge bg-info">' + (d || 'Todos') + '</span>'; }},
            { data: 'duracion_minutos', render: function(d) { return d ? d + ' min' : '-'; }},
            { data: 'precio', render: function(d) { return '<span class="fw-bold text-primary">RD$ ' + parseFloat(d).toFixed(2) + '</span>'; }},
            { data: 'items_count', render: function(d) { return '<span class="badge bg-secondary">' + d + ' items</span>'; }},
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
