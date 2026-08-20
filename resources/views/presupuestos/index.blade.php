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

    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Total Presupuestos</div>
                    <div class="ui-stat-value">{{ $presupuestos->total() ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Por Aprobar</div>
                    <div class="ui-stat-value" style="color:#f59e0b;">
                        {{ $presupuestos->where('estado', 'borrador')->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Aprobados</div>
                    <div class="ui-stat-value" style="color:#22c55e;">
                        {{ $presupuestos->where('estado', 'aprobada')->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Total Monto</div>
                    <div class="ui-stat-value" style="color:#3b82f6;">
                        RD$ {{ number_format($presupuestos->sum('total') ?? 0, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    <tbody>
                        @forelse($presupuestos as $presupuesto)
                        <tr>
                            <td>{{ $presupuesto->id }}</td>
                            <td><strong>{{ $presupuesto->numero }}</strong></td>
                            <td>{{ $presupuesto->cliente->nombre ?? '-' }}</td>
                            <td>RD$ {{ number_format($presupuesto->subtotal, 2) }}</td>
                            <td>RD$ {{ number_format($presupuesto->itbis, 2) }}</td>
                            <td class="fw-bold">RD$ {{ number_format($presupuesto->total, 2) }}</td>
                            <td>
                                @php
                                    $estadoBadge = [
                                        'borrador' => 'secondary',
                                        'enviada' => 'info',
                                        'aprobada' => 'success',
                                        'rechazada' => 'danger',
                                        'vencida' => 'warning',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $estadoBadge[$presupuesto->estado] ?? 'secondary' }}">
                                    {{ $presupuesto->estado_label }}
                                </span>
                            </td>
                            <td>{{ $presupuesto->valido_hasta ? $presupuesto->valido_hasta->format('Y-m-d') : '-' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('presupuestos.show', $presupuesto) }}" class="btn btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('presupuestos.edit')
                                    <a href="{{ route('presupuestos.edit', $presupuesto) }}" class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('presupuestos.delete')
                                    @if($presupuesto->estado === 'borrador')
                                    <form action="{{ route('presupuestos.destroy', $presupuesto) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este presupuesto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox d-block fs-1 mb-2"></i>
                                No hay presupuestos registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $presupuestos->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#presupuestosTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "order": [[0, "desc"]],
        "pageLength": 10,
        "responsive": true
    });
});
</script>
@endpush
@endsection
