@extends('layouts.app')

@section('title', 'Devoluci&oacute;n ' . $devolucion->codigo)

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-arrow-return-left text-primary me-2"></i>Devoluci&oacute;n {{ $devolucion->codigo }}</h2>
            <p class="text-muted mb-0">Detalle completo de la devoluci&oacute;n.</p>
        </div>
        <div>
            <a href="{{ route('devoluciones.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold me-2">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
            @can('devoluciones.create')
            <a href="{{ route('devoluciones.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-plus-lg me-2"></i>Nueva
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 mb-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-3 mb-3">{{ session('error') }}</div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <small class="text-muted text-uppercase fw-bold d-block mb-2">C&oacute;digo</small>
                    <h5 class="fw-bold mb-0">{{ $devolucion->codigo }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <small class="text-muted text-uppercase fw-bold d-block mb-2">Cliente</small>
                    <h6 class="fw-bold mb-1">{{ $devolucion->cliente?->nombre ?? 'N/A' }}</h6>
                    <small class="text-muted">{{ $devolucion->cliente?->rnc_cedula ?? '' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <small class="text-muted text-uppercase fw-bold d-block mb-2">Fecha / Tipo</small>
                    <h6 class="fw-bold mb-1">{{ $devolucion->fecha?->format('d/m/Y') }}</h6>
                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill">{{ ucfirst($devolucion->tipo) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary bg-opacity-10">
                <div class="card-body p-4">
                    <small class="text-muted text-uppercase fw-bold d-block mb-2">Total Devuelto</small>
                    <h3 class="fw-bold text-primary mb-0">RD$ {{ number_format($devolucion->total, 2) }}</h3>
                    <small class="text-muted">Incluye ITBIS</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-box-seam text-primary me-2"></i>Productos Devueltos</h5>
            @php
                $estadoInfo = match($devolucion->estado) {
                    'borrador' => ['warning', 'clock', 'Borrador'],
                    'completada' => ['success', 'check-circle', 'Completada'],
                    'anulada' => ['danger', 'x-circle', 'Anulada'],
                    default => ['secondary', 'circle', $devolucion->estado],
                };
            @endphp
            <span class="badge bg-{{ $estadoInfo[0] }} bg-opacity-10 text-{{ $estadoInfo[0] }} rounded-pill px-3 fs-6">
                <i class="bi bi-{{ $estadoInfo[1] }} me-1"></i>{{ $estadoInfo[2] }}
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Producto</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">Precio Unit.</th>
                        <th class="text-end">ITBIS</th>
                        <th class="text-end pe-4">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($devolucion->detalles as $item)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $item->producto?->nombre ?? 'N/A' }}</td>
                        <td class="text-end">{{ $item->cantidad }}</td>
                        <td class="text-end">RD$ {{ number_format($item->precio_unitario, 2) }}</td>
                        <td class="text-end">{{ number_format($item->itbis_porcentaje, 2) }}%</td>
                        <td class="text-end pe-4 fw-bold">RD$ {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light bg-opacity-50">
                    <tr>
                        <td colspan="4" class="text-end fw-bold ps-4">Subtotal:</td>
                        <td class="text-end pe-4">RD$ {{ number_format($devolucion->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end fw-bold ps-4">ITBIS:</td>
                        <td class="text-end pe-4">RD$ {{ number_format($devolucion->itbis, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end fw-bold fs-5 ps-4">TOTAL:</td>
                        <td class="text-end pe-4 fw-bold fs-5 text-primary">RD$ {{ number_format($devolucion->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-2"><i class="bi bi-chat-left-text text-primary me-1"></i>Motivo</h6>
            <p class="mb-0 text-muted">{{ $devolucion->motivo }}</p>
            @if($devolucion->venta)
            <hr>
            <small class="text-muted">
                <i class="bi bi-receipt me-1"></i>Venta asociada:
                <a href="{{ route('ventas.show', $devolucion->venta) }}" class="text-decoration-none fw-bold">#{{ str_pad($devolucion->venta_id, 5, '0', STR_PAD_LEFT) }}</a>
                &middot; {{ $devolucion->user?->name ?? 'N/A' }}
            </small>
            @endif
        </div>
    </div>

    @if($devolucion->notaCredito)
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-success bg-opacity-5">
        <div class="card-body p-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill text-success fs-3 me-3"></i>
                <div>
                    <h6 class="fw-bold mb-1 text-success">Nota de Cr&eacute;dito Electr&oacute;nica Generada</h6>
                    <a href="{{ route('ecf.show', $devolucion->notaCredito) }}" class="text-decoration-none">
                        {{ $devolucion->notaCredito->encf }}
                        <span class="badge bg-{{ $devolucion->notaCredito->estado_info['color'] }} ms-2">{{ $devolucion->notaCredito->estado_info['label'] }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="d-flex gap-2 justify-content-end">
        @if($devolucion->estado === 'borrador')
            <form action="{{ route('devoluciones.confirmar', $devolucion) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" onclick="return confirm('¿Confirmar devolución? El stock será reintegrado.')">
                    <i class="bi bi-check-lg me-2"></i>Confirmar y Reintegrar Stock
                </button>
            </form>
        @endif
        @if($devolucion->estado === 'completada' && $devolucion->tiene_ecf && !$devolucion->nota_credito_id)
            <form action="{{ route('devoluciones.generar-nc', $devolucion) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-file-earmark-minus me-2"></i>Generar Nota de Cr&eacute;dito E34
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
