@extends('layouts.app')

@section('title', 'Devoluciones')

@push('styles')
@include('partials.premium-ui')
<style>
.devoluciones-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(239,68,68,.04);
    margin: 0;
}
.devoluciones-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.devoluciones-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.devoluciones-table tbody tr:last-child td { border-bottom: none; }
.devoluciones-table tbody tr { transition: background .15s; }
.devoluciones-table tbody tr:hover { background: rgba(239,68,68,.03); }
body.dark-mode .devoluciones-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .devoluciones-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#ef4444;--accent-rgb:239,68,68;--accent-hover:#dc2626;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-arrow-return-left"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Devoluciones</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-arrow-return-left me-1"></i>
                        <span>Gestión de devoluciones de productos y Notas de Crédito</span>
                        <span class="divider">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        <span>{{ $devoluciones->total() }} registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('devoluciones.create')
                <a href="{{ route('devoluciones.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Devolución
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-lg-3">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="cliente" class="ui-input" placeholder="Buscar cliente..." value="{{ request('cliente') }}">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="estado" class="ui-select">
                        <option value="">Todos los estados</option>
                        <option value="borrador" {{ request('estado') === 'borrador' ? 'selected' : '' }}>Borrador</option>
                        <option value="completada" {{ request('estado') === 'completada' ? 'selected' : '' }}>Completada</option>
                        <option value="anulada" {{ request('estado') === 'anulada' ? 'selected' : '' }}>Anulada</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="fecha_desde" class="ui-input" value="{{ request('fecha_desde') }}" placeholder="Desde">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="fecha_hasta" class="ui-input" value="{{ request('fecha_hasta') }}" placeholder="Hasta">
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('devoluciones.index') }}" class="ui-btn ui-btn-ghost"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="table devoluciones-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Código</th>
                            <th>Cliente</th>
                            <th>Venta</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devoluciones as $d)
                        <tr>
                            <td class="ps-4"><span class="ui-badge ui-badge-neutral rounded-pill px-3">{{ $d->codigo }}</span></td>
                            <td class="fw-bold small">{{ $d->cliente?->nombre ?? 'N/A' }}</td>
                            <td>
                                @if($d->venta)
                                    <a href="{{ route('ventas.show', $d->venta) }}" class="text-decoration-none">#{{ str_pad($d->venta_id, 5, '0', STR_PAD_LEFT) }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="small">{{ $d->fecha?->format('d/m/Y') ?? $d->created_at->format('d/m/Y') }}</td>
                            <td><span class="ui-badge ui-badge-info rounded-pill px-3">{{ ucfirst($d->tipo) }}</span></td>
                            <td class="text-end fw-bold">RD$ {{ number_format($d->total, 2) }}</td>
                            <td class="text-center">
                                @php
                                    $estados = ['borrador' => ['warning', 'clock'], 'completada' => ['success', 'check-circle'], 'anulada' => ['danger', 'x-circle']];
                                    $e = $estados[$d->estado] ?? ['secondary', 'circle'];
                                @endphp
                                <span class="ui-badge ui-badge-{{ $e[0] }} rounded-pill px-3">
                                    <i class="bi bi-{{ $e[1] }} me-1"></i>{{ ucfirst($d->estado) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('devoluciones.show', $d) }}" class="ui-action ui-action-view me-1" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($d->estado === 'borrador')
                                <form action="{{ route('devoluciones.destroy', $d) }}" method="POST" class="d-inline" onsubmit="return UI.confirm.delete('¿Eliminar esta devolución?')">
                                    @csrf @method('DELETE')
                                    <button class="ui-action ui-action-delete" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-arrow-return-left fs-1" style="color:#cbd5e1;"></i>
                                <p class="mt-2 mb-0 fw-semibold">No hay devoluciones registradas</p>
                                @can('devoluciones.create')
                                <a href="{{ route('devoluciones.create') }}" class="ui-btn ui-btn-solid rounded-pill mt-2">Registrar primera devolución</a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($devoluciones->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $devoluciones->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection