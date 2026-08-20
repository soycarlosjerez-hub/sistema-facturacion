@extends('layouts.app')
@section('title', 'Redes de Configuración')

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
                    <i class="bi bi-hdd-network"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Redes de Configuración</h4>
                    <div class="ui-header-meta">Administra infraestructura de red para clientes empresariales</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('redes-config.create')
                <a href="{{ route('redes-config.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Red
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
            <form method="GET" action="{{ route('redes-config.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar red/SSID..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="cliente_id" class="form-select">
                        <option value="">Todos los clientes</option>
                        @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="vlan_id" class="form-select">
                        <option value="">Todas las VLANs</option>
                        @foreach($redes->pluck('vlan_id')->filter()->unique()->sort() as $vlan)
                        <option value="{{ $vlan }}" {{ request('vlan_id') == $vlan ? 'selected' : '' }}>
                            VLAN {{ $vlan }}
                        </option>
                        @endforeach
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
                <table class="table table-hover dt-table" id="redesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre de Red</th>
                            <th>SSID WiFi</th>
                            <th>VLAN</th>
                            <th>Cliente</th>
                            <th>DHCP</th>
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
    var table = $('#redesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("redes-config.ajax") }}',
            type: 'GET',
            data: function(d) {
                d.cliente_id = $('select[name="cliente_id"]').val();
                d.vlan_id = $('select[name="vlan_id"]').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, width: '50px' },
            { 
                data: 'nombre_red', 
                name: 'nombre_red',
                render: function(data) {
                    return '<strong>' + data + '</strong>';
                }
            },
            { data: 'ssid_wifi', name: 'ssid_wifi' },
            { 
                data: 'vlan_id', 
                name: 'vlan_id',
                render: function(data) {
                    if (data) return '<span class="badge bg-info">VLAN ' + data + '</span>';
                    return '<span class="text-muted">-</span>';
                }
            },
            { data: 'cliente', name: 'cliente' },
            { 
                data: 'dhcp_activado', 
                name: 'dhcp_activado',
                render: function(data) {
                    return data ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>';
                }
            },
            { 
                data: 'activo_label', 
                name: 'activo',
                render: function(data, type, row) {
                    var cls = row.activo ? 'success' : 'secondary';
                    return '<span class="badge bg-' + cls + '">' + data + '</span>';
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
