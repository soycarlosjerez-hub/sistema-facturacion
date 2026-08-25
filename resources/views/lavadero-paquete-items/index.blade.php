@extends('layouts.app')

@section('title', 'Items de Paquete')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
#paquete-items-table_wrapper { --dt-accent: #06b6d4; --dt-accent-rgb: 6,182,212; --dt-accent-gradient: linear-gradient(135deg, #06b6d4, #0891b2); }
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
                <div class="ui-avatar-circle"><i class="bi bi-list-ul"></i></div>
                <div>
                    <h4 class="ui-header-title">Items de Paquete</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-check me-1"></i>
                        <span>Gestión de items/servicios de los paquetes</span>
                        <span class="divider">·</span>
                        <i class="bi bi-list-ol me-1"></i>
                        <span>{{ $items->total() ?: 0 }} registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('lavadero-paquete-items.create') }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Item
                </a>
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
        <div class="ui-card-body p-0">
            <table class="table dt-table nowrap" id="paquete-items-table">
                <thead>
                    <tr>
                        <th>Paquete</th>
                        <th>Tipo</th>
                        <th>Servicio/Producto</th>
                        <th>Cantidad</th>
                        <th class="text-end">Precio Ind.</th>
                        <th class="text-center">Auto</th>
                        <th>Orden</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-none" id="paquete-item-edit-url">{{ route('lavadero-paquete-items.edit', ['paqueteItem' => '__ID__']) }}</div>
<div class="d-none" id="paquete-item-show-url">{{ route('lavadero-paquete-items.show', ['paqueteItem' => '__ID__']) }}</div>
<div class="d-none" id="paquete-item-delete-url">{{ route('lavadero-paquete-items.destroy', ['paqueteItem' => '__ID__']) }}</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var items = @json($items->toArray()['data'] ?? []);

    $('#paquete-items-table').DataTable({
        data: items,
        columns: [
            {
                data: 'paquete',
                render: function(d) {
                    if (typeof d === 'object' && d !== null && d.nombre) {
                        return '<div class="fw-bold">' + escapeHtml(d.nombre) + '</div>';
                    }
                    return '<span class="text-muted">—</span>';
                }
            },
            {
                data: 'tipo',
                render: function(d) {
                    if (d === 'servicio') {
                        return '<span class="ui-badge ui-badge-info">Servicio</span>';
                    }
                    if (d === 'producto') {
                        return '<span class="ui-badge ui-badge-neutral">Producto</span>';
                    }
                    return '<span class="ui-badge ui-badge-neutral">' + d + '</span>';
                }
            },
            {
                data: null,
                render: function(row) {
                    if (row.tipo === 'servicio' && row.servicio) {
                        var nombre = typeof row.servicio === 'object' ? row.servicio.nombre : row.servicio;
                        return '<span class="fw-medium">' + escapeHtml(nombre) + '</span>';
                    }
                    if (row.tipo === 'producto' && row.producto) {
                        var nombre = typeof row.producto === 'object' ? row.producto.nombre : row.producto;
                        return '<span class="fw-medium">' + escapeHtml(nombre) + '</span>';
                    }
                    return '<span class="text-muted">—</span>';
                }
            },
            { data: 'cantidad', render: function(d) { return parseFloat(d).toFixed(2); }},
            {
                data: 'precio_individual',
                render: function(d) { return '<span class="fw-bold">RD$ ' + parseFloat(d).toFixed(2) + '</span>'; }
            },
            {
                data: 'incluir_automatico',
                render: function(d) {
                    return d ? '<span class="ui-badge ui-badge-success"><i class="bi bi-check-lg"></i></span>' : '<span class="ui-badge ui-badge-neutral"><i class="bi bi-x-lg"></i></span>';
                }
            },
            { data: 'orden', render: function(d) { return d ?? '<span class="text-muted">—</span>'; }},
            { data: null, orderable: false, searchable: false, render: function(row) {
                var editUrl = $('#paquete-item-edit-url').text().replace('__ID__', row.id);
                var deleteUrl = $('#paquete-item-delete-url').text().replace('__ID__', row.id);
                var itemName = '';
                if (row.tipo === 'servicio' && row.servicio) {
                    itemName = typeof row.servicio === 'object' ? row.servicio.nombre : row.servicio;
                } else if (row.tipo === 'producto' && row.producto) {
                    itemName = typeof row.producto === 'object' ? row.producto.nombre : row.producto;
                }
                return '<a href="' + editUrl + '" class="ui-action ui-action-edit me-1" title="Editar"><i class="bi bi-pencil"></i></a>' +
                       '<button class="ui-action ui-action-delete" onclick="UI.confirm.delete(\'' + deleteUrl + '\', \'' + escapeHtml(itemName) + '\')" title="Eliminar"><i class="bi bi-trash"></i></button>' +
                       '<a href="' + $('#paquete-item-show-url').text().replace('__ID__', row.id) + '" class="ui-action ui-action-view" title="Ver"><i class="bi bi-eye"></i></a>';
            }}
        ],
        dom: '<"row px-3 pt-2"<"col-sm-6"l><"col-sm-6"f>>' + '<"row"<"col-12"tr>>' + '<"row px-3 pb-2"<"col-sm-5"i><"col-sm-7"p>>',
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES' },
        responsive: { details: { type: 'column' } }
    });
});
</script>
@endsection
