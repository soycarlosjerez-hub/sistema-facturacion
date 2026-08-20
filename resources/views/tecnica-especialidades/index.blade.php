@extends('layouts.app')
@section('title', 'Especialidades Técnicas')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #f59e0b;
    --dt-accent-gradient: linear-gradient(135deg, #f59e0b, #d97706);
    --dt-accent-rgb: 245,158,11;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Especialidades Técnicas</h4>
                    <div class="ui-header-meta">Gestiona las especialidades de los técnicos</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('tecnica-especialidades.create')
                <a href="{{ route('tecnica-especialidades.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Especialidad
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
            <form method="GET" action="{{ route('tecnica-especialidades.index') }}" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Buscar especialidad..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="activo" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activas</option>
                        <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                </div>
                <div class="col-md-3">
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
                <table class="table table-hover dt-table" id="especialidadesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Técnicos</th>
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
    var table = $('#especialidadesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("tecnica-especialidades.ajax") }}',
            type: 'GET',
            data: function(d) {
                d.activo = $('select[name="activo"]').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, width: '50px' },
            { 
                data: 'nombre', 
                name: 'nombre',
                render: function(data) {
                    return '<strong>' + data + '</strong>';
                }
            },
            { 
                data: 'descripcion', 
                name: 'descripcion',
                render: function(data) {
                    return (data && data.length > 50) ? data.substring(0, 50) + '...' : (data || '-');
                }
            },
            { 
                data: 'tecnicos_count', 
                name: 'tecnicos_count',
                orderable: false,
                render: function(data) {
                    return '<span class="badge bg-info">' + (data || 0) + '</span>';
                }
            },
            { 
                data: 'activo', 
                name: 'activo',
                render: function(data) {
                    var cls = data ? 'success' : 'secondary';
                    var label = data ? 'Activa' : 'Inactiva';
                    return '<span class="badge bg-' + cls + '">' + label + '</span>';
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
