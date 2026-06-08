@extends('layouts.app')

@section('title', 'Devoluciones')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-arrow-return-left text-primary me-2"></i>Devoluciones</h2>
            <p class="text-muted mb-0">Gesti&oacute;n de devoluciones de productos y Notas de Cr&eacute;dito.</p>
        </div>
        @can('devoluciones.create')
        <a href="{{ route('devoluciones.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-plus-lg me-2"></i>Nueva Devoluci&oacute;n
        </a>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <input type="text" name="cliente" class="form-control" placeholder="Buscar cliente..." value="{{ request('cliente') }}">
                </div>
                <div class="col-md-2">
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="borrador" {{ request('estado') === 'borrador' ? 'selected' : '' }}>Borrador</option>
                        <option value="completada" {{ request('estado') === 'completada' ? 'selected' : '' }}>Completada</option>
                        <option value="anulada" {{ request('estado') === 'anulada' ? 'selected' : '' }}>Anulada</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}" placeholder="Desde">
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}" placeholder="Hasta">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100 rounded-pill"><i class="bi bi-search me-1"></i>Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">C&oacute;digo</th>
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
                            <a href="{{ route('devoluciones.show', $d) }}" class="btn btn-sm btn-outline-info rounded-pill me-1" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($d->estado === 'borrador')
                            <form action="{{ route('devoluciones.destroy', $d) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta devolución?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay devoluciones registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $devoluciones->links() }}</div>
</div>
@endsection
