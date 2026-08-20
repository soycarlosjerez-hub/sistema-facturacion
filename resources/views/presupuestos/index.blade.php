@extends('layouts.app')
@section('title', 'Presupuestos Técnicos')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #8b5cf6;
    --dt-accent-gradient: linear-gradient(135deg, #8b5cf6, #7c3aed);
    --dt-accent-rgb: 139,92,246;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
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
                    <h4 class="ui-header-title">Presupuestos Técnicos</h4>
                    <div class="ui-header-meta">Gestiona presupuestos de servicios técnicos y ventas</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('presupuestos.create')
                <a href="{{ route('presupuestos.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Presupuesto
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
            <form method="GET" action="{{ route('presupuestos.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar presupuesto..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="borrador" {{ request('estado') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                        <option value="enviada" {{ request('estado') == 'enviada' ? 'selected' : '' }}>Enviada</option>
                        <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                        <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                        <option value="vencida" {{ request('estado') == 'vencida' ? 'selected' : '' }}>Vencida</option>
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
                <table class="table table-hover dt-table" id="presupuestosTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Subtotal</th>
                            <th>ITBIS</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Válida Hasta</th>
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
    var table = $('#presupuestosTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("presupuestos.ajax") }}',
            type: 'GET',
            data: function(d) {
                d.estado = $('select[name="estado"]').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, width: '50px' },
            { 
                data: 'numero', 
                name: 'numero',
                render: function(data) {
                    return '<strong>' + data + '</strong>';
                }
            },
            { data: 'cliente', name: 'cliente' },
            { 
                data: 'subtotal', 
                name: 'subtotal',
                render: function(data) {
                    return 'RD$ ' + (data || '0.00');
                }
            },
            { 
                data: 'itbis', 
                name: 'itbis',
                render: function(data) {
                    return 'RD$ ' + (data || '0.00');
                }
            },
            { 
                data: 'total', 
                name: 'total',
                className: 'fw-bold',
                render: function(data) {
                    return 'RD$ ' + (data || '0.00');
                }
            },
            { data: 'estado', name: 'estado' },
            { 
                data: 'valido_hasta', 
                name: 'valido_hasta',
                render: function(data) {
                    return data || '-';
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
