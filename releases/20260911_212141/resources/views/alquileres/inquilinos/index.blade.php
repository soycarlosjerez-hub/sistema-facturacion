@extends('layouts.app')

@section('title', 'Inquilinos')

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
    .dt-table tbody tr:hover { background: rgba(16,185,129,.03); }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Inquilinos</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i> Gestión de inquilinos de alquileres
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('alquileres.inquilinos.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Inquilino
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="inquilinos-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cédula</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Dirección</th>
                        <th>Contratos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inquilinos as $inq)
                    <tr>
                        <td class="fw-semibold">{{ $inq->nombre }}</td>
                        <td>{{ $inq->cedula ?? '—' }}</td>
                        <td>{{ $inq->telefono ?? '—' }}</td>
                        <td>{{ $inq->email ?? '—' }}</td>
                        <td>{{ Str::limit($inq->direccion, 30) ?? '—' }}</td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info rounded-pill">
                                {{ $inq->contratos->count() ?? 0 }} contrato(s)
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('alquileres.inquilinos.show', $inq) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('alquileres.inquilinos.edit', $inq) }}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('alquileres.inquilinos.destroy', $inq) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este inquilino?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('inquilinos-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron inquilinos',
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_ total)'
            },
            columnDefs: [
                { orderable: false, targets: [6] }
            ],
            order: [[0, 'asc']],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    }
});
</script>
@endpush
@endsection
