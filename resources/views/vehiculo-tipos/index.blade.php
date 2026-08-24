@extends('layouts.app')

@section('title', 'Tipos de Vehículo')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">
    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-car-front"></i></div>
                <div>
                    <h4 class="ui-header-title">Tipos de Vehículo</h4>
                    <div class="ui-header-meta"><i class="bi bi-truck me-1"></i><span>Configurar tipos de vehículo para lavado</span></div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('vehiculo-tipos.create')
                <a href="{{ route('vehiculo-tipos.create') }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Tipo
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="ui-card">
        <div class="ui-card-body">
            <table class="table ui-table nowrap" id="vehiculos-table">
                <thead>
                    <tr>
                        <th>Icono / Nombre</th>
                        <th>Slug</th>
                        <th>Orden</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-none" id="edit-url">{{ route('vehiculo-tipos.edit', '__ID__') }}</div>
<div class="d-none" id="del-url">{{ route('vehiculo-tipos.destroy', '__ID__') }}</div>
<div class="d-none" id="toggle-url">{{ route('vehiculo-tipos.toggle', '__ID__') }}</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#vehiculos-table').DataTable({
        ajax: { url: '{{ route("vehiculo-tipos.index") }}?json=1', dataSrc: 'data' },
        columns: [
            { data: null, render: function(row) {
                return '<div class="d-flex align-items-center gap-2">' +
                       '<div style="width:36px;height:36px;border-radius:50%;background:rgba(6,182,212,0.1);display:flex;align-items:center;justify-content:center;color:#06b6d4;">' +
                       '<i class="bi ' + (row.icono || 'bi-car-front') + '"></i></div>' +
                       '<div><div class="fw-bold">' + row.nombre + '</div>' +
                       '<small class="text-muted">' + row.nombre_completo + '</small></div></div>';
            }},
            { data: 'slug', render: function(d) { return '<code class="small">' + d + '</code>'; }},
            { data: 'orden', render: function(d) { return '<span class="badge bg-secondary">' + d + '</span>'; }},
            { data: 'activo', render: function(d) {
                return '<div class="form-check form-switch mb-0">' +
                       '<input class="form-check-input" type="checkbox" ' + (d ? 'checked' : '') +
                       ' onchange="toggleVehiculoTipo(' + row.id + ', this.checked)" style="width:3em;height:1.5em;">' +
                       '</div>';
            }},
            { data: null, orderable: false, render: function(row) {
                return '<a href="' + $('#edit-url').text().replace('__ID__', row.id) + '" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>' +
                       '<button class="ui-action ui-action-delete" onclick="UI.confirm.delete(\'{{ route("vehiculo-tipos.destroy", "__ID__") }}'.replace('__ID__', 'ROW_ID') + '\', \'' + row.nombre + '\')"><i class="bi bi-trash"></i></button>';
            }}
        ],
        dom: '<"row"<"col-12"tr>>',
        pageLength: 50,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES' }
    });
});

function toggleVehiculoTipo(id, activo) {
    const url = $('#toggle-url').text().replace('__ID__', id);
    fetch(url, { method: 'POST', headers: {'Content-Type':'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With':'XMLHttpRequest'},
        body: JSON.stringify({ activo })
    }).then(r => r.json()).then(res => {
        if (res.success) UI.toast.success('Estado actualizado');
        else UI.toast.error(res.error);
    });
}
</script>
@endsection
