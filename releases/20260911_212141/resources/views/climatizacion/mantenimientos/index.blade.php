@extends('layouts.app')

@section('title', 'Mantenimientos')

@push('styles')
@include('partials.premium-ui')
<style>
    body.dark-mode .ui-page { --accent: #3b82f6; --accent-rgb: 59,130,246; --accent-hover: #2563eb; }
    
    /* DataTables custom styles */
    .mantenimientos-table_wrapper { background: transparent !important; }
    .mantenimientos-table thead th {
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #64748b;
        font-weight: 700;
        border-bottom: 2px solid #e2e8f0 !important;
        padding: .75rem .75rem;
        white-space: nowrap;
    }
    .mantenimientos-table tbody td {
        font-size: .9rem;
        vertical-align: middle;
        padding: .75rem .75rem;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .mantenimientos-table tbody tr:hover { background: rgba(59,130,246,.04) !important; }
    .mantenimientos-table .dataTable-selector {
        border-radius: 2rem;
        padding-left: 1rem;
        font-size: .85rem;
    }
    .mantenimientos-table .dataTable-input {
        border-radius: 2rem;
        padding-left: 2.2rem;
        font-size: .85rem;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: left .7rem center;
        background-size: 14px;
    }
    .mantenimientos-table .paginate_button {
        border-radius: .5rem !important;
        border: none !important;
    }
    .mantenimientos-table .paginate_button.current,
    .mantenimientos-table .paginate_button.current:hover {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: #fff !important;
        box-shadow: 0 2px 8px rgba(59,130,246,.3);
    }
    .mantenimientos-table .paginate_button:hover:not(.disabled):not(.current) {
        background: rgba(59,130,246,.08) !important;
        color: #3b82f6 !important;
    }
    .mantenimientos-table .dataTables_info,
    .mantenimientos-table .dataTables_length { font-size: .85rem; }
    
    body.dark-mode .mantenimientos-table thead th { border-bottom-color: #334155 !important; color: #94a3b8; }
    body.dark-mode .mantenimientos-table tbody td { border-bottom-color: #1e293b !important; }
    body.dark-mode .mantenimientos-table tbody tr:hover { background: rgba(59,130,246,.08) !important; }
    body.dark-mode .mantenimientos-table .dataTable-input { background-color: #1e293b; border-color: #334155; color: #f1f5f9; }
    body.dark-mode .mantenimientos-table .paginate_button.current,
    body.dark-mode .mantenimientos-table .paginate_button.current:hover { background: linear-gradient(135deg, #60a5fa, #3b82f6) !important; }
    body.dark-mode .mantenimientos-table .paginate_button:hover:not(.disabled):not(.current) { background: rgba(59,130,246,.12) !important; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">
    {{-- HEADER --}}
    <div class="ui-header" style="--delay:0s;">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h1 class="ui-header-title">Mantenimientos</h1>
                    <div class="ui-header-meta">
                        <i class="bi bi-wrench-adjustable-circle me-1"></i>Gestión de mantenimientos de equipos
                        <span class="divider">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        <span id="totalRecords">0 registros</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('climatizacion.mantenimientos.export') }}" class="ui-btn ui-btn-primary" title="Exportar Excel">
                    <i class="bi bi-download me-1"></i> Exportar
                </a>
                @can('mantenimientos.create')
                <a href="{{ route('climatizacion.mantenimientos.create') }}" class="ui-btn ui-btn-solid rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Mantenimiento
                </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="ui-card" style="--delay:.05s;">
        <div class="ui-card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="ui-label">Buscar</label>
                    <input type="text" name="search" class="ui-input" placeholder="Número, cliente o falla..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="ui-label">Tipo</label>
                    <select name="tipo" class="ui-select">
                        <option value="">Todos los tipos</option>
                        @foreach(\App\Models\Mantenimiento::TIPOS as $val => $label)
                            <option value="{{ $val }}" {{ request('tipo') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="ui-label">Estado</label>
                    <select name="estado" class="ui-select">
                        <option value="">Todos los estados</option>
                        @foreach(\App\Models\Mantenimiento::ESTADOS as $val => $label)
                            <option value="{{ $val }}" {{ request('estado') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-fill">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('climatizacion.mantenimientos.index') }}" class="ui-btn ui-btn-ghost flex-fill">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="ui-card" style="--delay:.1s;">
        <div class="ui-card-body p-0">
            <table class="table mantenimientos-table nowrap" id="mantenimientosTable">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Técnico</th>
                        <th>Descripción</th>
                        <th class="text-end">Total</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script>
document.addEventListener('DOMContentLoaded', function () {
    const accentColor = '#3b82f6';
    
    $('#mantenimientosTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("climatizacion.mantenimientos.index") }}',
            data: function(d) {
                d.search = '{{ request("search") }}';
                d.tipo = '{{ request("tipo") }}';
                d.estado = '{{ request("estado") }}';
            }
        },
        columns: [
            { data: 'numero', name: 'numero', orderable: true },
            { data: 'cliente', name: 'cliente', orderable: true },
            { 
                data: 'tipo', 
                name: 'tipo',
                render: function(data, type, row) {
                    const tipoColor = row.tipo === 'preventivo' ? 'info' : 'warning';
                    return '<span class="ui-badge ui-badge-' + tipoColor + '">' + data + '</span>';
                }
            },
            { data: 'tecnico', name: 'tecnico', orderable: true },
            { 
                data: 'descripcion_falla', 
                name: 'descripcion_falla',
                render: function(data) {
                    return '<span class="d-inline-block text-truncate" style="max-width:200px;" title="' + (data || '') + '">' + (data || '-') + '</span>';
                }
            },
            { 
                data: 'total', 
                name: 'total', 
                orderable: false,
                render: function(data) { return '<span class="fw-bold">RD$ ' + data + '</span>'; }
            },
            { 
                data: 'estado', 
                name: 'estado',
                render: function(data, type, row) {
                    return '<span class="ui-badge ui-badge-' + row.badge_color + '">' + row.estado_label + '</span>';
                }
            },
            { 
                data: 'acciones', 
                name: 'acciones', 
                orderable: false,
                searchable: false,
                render: function(data) { return data; }
            }
        ],
        dom: '<"row px-3 pt-2"<"col-sm-6"l><"col-sm-6"f>>' + '<"row"<"col-12"tr>>' + '<"row px-3 pb-2"<"col-sm-5"i><"col-sm-7"p>>',
        language: {
            search: '',
            lengthMenu: '_MENU_ registros',
            info: 'Mostrando _START_-_END_ de _TOTAL_',
            infoEmpty: 'No hay registros',
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            },
            zeroRecords: 'No se encontraron resultados',
            emptyTable: 'No hay datos disponibles en la tabla'
        },
        drawCallback: function() {
            $('#totalRecords').text($('#mantenimientosTable_wrapper .dataTables_info').text().replace(/Mostrando.*de /, '') + ' registros');
        }
    });
});
</script>
@endpush
