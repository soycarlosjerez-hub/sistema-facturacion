@extends('layouts.app')

@section('title', 'Evaluaciones de Proveedores')

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
    .dt-table tbody tr:hover { background: rgba(139,92,246,.03); }
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-pendiente { background: #fef3c7; color: #d97706; }
    .badge-aprobado { background: #dcfce7; color: #16a34a; }
    .badge-rechazado { background: #fee2e2; color: #dc2626; }
    .badge-en_curso { background: #dbeafe; color: #2563eb; }
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
                    <i class="bi bi-clipboard-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Evaluaciones de Proveedores</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i> Control de calidad de proveedores
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('sgc.evaluaciones-proveedores.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Evaluación
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="evaluaciones-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th>Criterio</th>
                        <th>Puntuación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evaluaciones as $eval)
                    <tr>
                        <td><code>{{ $eval->codigo }}</code></td>
                        <td class="fw-semibold">{{ $eval->proveedor?->nombre ?? '—' }}</td>
                        <td>{{ $eval->fecha ? $eval->fecha->format('d/m/Y') : '—' }}</td>
                        <td>{{ $eval->criterio ?? '—' }}</td>
                        <td>
                            @if($eval->puntuacion)
                                <span class="fw-bold" style="color:{{ $eval->puntuacion >= 70 ? '#16a34a' : ($eval->puntuacion >= 50 ? '#d97706' : '#dc2626') }};">
                                    {{ $eval->puntuacion }}/100
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td><span class="badge-status badge-{{ $eval->estado ?? 'pendiente' }}">{{ ucfirst($eval->estado ?? 'pendiente') }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('sgc.evaluaciones-proveedores.show', $eval) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('sgc.evaluaciones-proveedores.destroy', $eval) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta evaluación?')">
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
    const table = document.getElementById('evaluaciones-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron evaluaciones',
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_ total)'
            },
            columnDefs: [
                { orderable: false, targets: [6] }
            ],
            order: [[0, 'desc']],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    }
});
</script>
@endpush
@endsection
