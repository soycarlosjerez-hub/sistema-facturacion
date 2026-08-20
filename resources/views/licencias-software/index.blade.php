@extends('layouts.app')
@section('title', 'Licencias de Software')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #06b6d4;
    --dt-accent-gradient: linear-gradient(135deg, #06b6d4, #0891b2);
    --dt-accent-rgb: 6,182,212;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-key"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Licencias de Software</h4>
                    <div class="ui-header-meta">Administra claves de licencia y sus estados</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('licencias-software.create')
                <a href="{{ route('licencias-software.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Licencia
                </a>
                @endcan
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('licencias-software.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar clave/licencia..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="plataforma" class="form-select">
                        <option value="">Todas las plataformas</option>
                        <option value="Windows" {{ request('plataforma') == 'Windows' ? 'selected' : '' }}>Windows</option>
                        <option value="macOS" {{ request('plataforma') == 'macOS' ? 'selected' : '' }}>macOS</option>
                        <option value="Linux" {{ request('plataforma') == 'Linux' ? 'selected' : '' }}>Linux</option>
                        <option value="Cloud" {{ request('plataforma') == 'Cloud' ? 'selected' : '' }}>Cloud</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="tipo_licencia" class="form-select">
                        <option value="">Todos los tipos</option>
                        <option value="perpetua" {{ request('tipo_licencia') == 'perpetua' ? 'selected' : '' }}>Perpetua</option>
                        <option value="suscripcion" {{ request('tipo_licencia') == 'suscripcion' ? 'selected' : '' }}>Suscripción</option>
                        <option value="open_source" {{ request('tipo_licencia') == 'open_source' ? 'selected' : '' }}>Open Source</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover dt-table" id="licenciasTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Clave de Licencia</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Plataforma</th>
                            <th>Usuario</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="mt-3 dt-table-footer"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#licenciasTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("licencias-software.ajax") }}',
            type: 'GET',
            data: function(d) {
                d.licencia_activa = $('select[name="licencia_activa"]').val();
                d.plataforma = $('select[name="plataforma"]').val();
                d.tipo_licencia = $('select[name="tipo_licencia"]').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, width: '50px' },
            { 
                data: 'clave_licencia', 
                name: 'clave_licencia',
                render: function(data) {
                    return '<code>' + (data || '-') + '</code>';
                }
            },
            { data: 'producto', name: 'producto' },
            { data: 'tipo_licencia', name: 'tipo_licencia' },
            { 
                data: 'plataforma', 
                name: 'plataforma',
                render: function(data) {
                    return '<span class="badge bg-secondary">' + (data || '-') + '</span>';
                }
            },
            { data: 'usuario_asignado', name: 'usuario_asignado' },
            { 
                data: 'fecha_vencimiento', 
                name: 'fecha_vencimiento',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'estado', 
                name: 'estado',
                render: function(data) {
                    var map = {
                        'Activa': 'success',
                        'Inactiva': 'secondary',
                        'Vencida': 'danger',
                        'Por Vencer': 'warning'
                    };
                    var cls = map[data] || 'secondary';
                    return '<span class="badge bg-' + cls + '">' + (data || '-') + '</span>';
                }
            },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, width: '140px' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true
    });

    // Filter on form submit
    $('form').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
    });
});
</script>
@endpush
@endsection
