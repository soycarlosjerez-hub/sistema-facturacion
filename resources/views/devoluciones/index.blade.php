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
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4" style="background:linear-gradient(135deg,#ef4444,#f97316,#ef4444);box-shadow:0 8px 32px rgba(239,68,68,.25);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle" style="background:rgba(255,255,255,.2);border-color:rgba(255,255,255,.35);">
                    <i class="bi bi-arrow-return-left"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Devoluciones</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-arrow-return-left me-1"></i>
                        Gestión de devoluciones de productos y Notas de Crédito
                        <span class="mx-2">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        {{ $devoluciones->total() }} registro(s)
                    </small>
                </div>
            </div>
            <div>
                @can('devoluciones.create')
                <a href="{{ route('devoluciones.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Devolución
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="premium-card mb-4" style="animation-delay:.1s;">
        <div class="card-accent red"></div>
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-lg-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="cliente" class="form-control" placeholder="Buscar cliente..." value="{{ request('cliente') }}">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="borrador" {{ request('estado') === 'borrador' ? 'selected' : '' }}>Borrador</option>
                        <option value="completada" {{ request('estado') === 'completada' ? 'selected' : '' }}>Completada</option>
                        <option value="anulada" {{ request('estado') === 'anulada' ? 'selected' : '' }}>Anulada</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}" placeholder="Desde">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}" placeholder="Hasta">
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('devoluciones.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card" style="animation-delay:.15s;">
        <div class="card-accent red"></div>
        <div class="card-body p-0">
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
                            <td class="ps-4"><span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">{{ $d->codigo }}</span></td>
                            <td class="fw-bold small">{{ $d->cliente?->nombre ?? 'N/A' }}</td>
                            <td>
                                @if($d->venta)
                                    <a href="{{ route('ventas.show', $d->venta) }}" class="text-decoration-none">#{{ str_pad($d->venta_id, 5, '0', STR_PAD_LEFT) }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="small">{{ $d->fecha?->format('d/m/Y') ?? $d->created_at->format('d/m/Y') }}</td>
                            <td><span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">{{ ucfirst($d->tipo) }}</span></td>
                            <td class="text-end fw-bold">RD$ {{ number_format($d->total, 2) }}</td>
                            <td class="text-center">
                                @php
                                    $estados = ['borrador' => ['warning', 'clock'], 'completada' => ['success', 'check-circle'], 'anulada' => ['danger', 'x-circle']];
                                    $e = $estados[$d->estado] ?? ['secondary', 'circle'];
                                @endphp
                                <span class="badge bg-{{ $e[0] }} bg-opacity-10 text-{{ $e[0] }} rounded-pill px-3">
                                    <i class="bi bi-{{ $e[1] }} me-1"></i>{{ ucfirst($d->estado) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('devoluciones.show', $d) }}" class="premium-btn-edit me-1" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($d->estado === 'borrador')
                                <form action="{{ route('devoluciones.destroy', $d) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta devolución?')">
                                    @csrf @method('DELETE')
                                    <button class="premium-btn-delete" title="Eliminar"><i class="bi bi-trash"></i></button>
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
                                <a href="{{ route('devoluciones.create') }}" class="btn btn-primary rounded-pill mt-2">Registrar primera devolución</a>
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
