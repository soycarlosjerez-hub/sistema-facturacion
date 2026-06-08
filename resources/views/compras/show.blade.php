@extends('layouts.app')

@section('title', 'Compra ' . $compra->folio)

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-receipt text-primary me-2"></i>Compra {{ $compra->folio }}</h2>
            <p class="text-muted mb-0">Detalle completo de la compra y productos recibidos.</p>
        </div>
        <div>
            <a href="{{ route('compras.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold me-2">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
            <a href="{{ route('compras.edit', $compra) }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-pencil-square me-2"></i>Editar
            </a>
            @if($compra->puede_generar_ecf)
            <form action="{{ route('compras.generar-ecf', $compra) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-warning rounded-pill px-4 shadow-sm fw-bold" onclick="return confirm('¿Generar e-CF E41 para esta compra?')">
                    <i class="bi bi-shield-check me-2"></i>Generar e-CF E41
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <small class="text-muted text-uppercase fw-bold d-block mb-2"><i class="bi bi-truck me-1"></i>Proveedor</small>
                    <h6 class="fw-bold mb-1">{{ $compra->proveedor->nombre ?? 'N/A' }}</h6>
                    <small class="text-muted">RNC: {{ $compra->proveedor->rnc_cedula ?? '—' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <small class="text-muted text-uppercase fw-bold d-block mb-2"><i class="bi bi-calendar3 me-1"></i>Fecha</small>
                    <h6 class="fw-bold mb-1">{{ $compra->fecha ? $compra->fecha->format('d/m/Y') : $compra->created_at->format('d/m/Y') }}</h6>
                    <small class="text-muted">{{ $compra->created_at->format('h:i A') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <small class="text-muted text-uppercase fw-bold d-block mb-2"><i class="bi bi-tag me-1"></i>Tipo</small>
                    <h6 class="fw-bold mb-1">{{ $compra->tipoCompra->nombre ?? 'N/A' }}</h6>
                    <small class="text-muted">Registrado por {{ $compra->user->name ?? '—' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <small class="text-muted text-uppercase fw-bold d-block mb-2"><i class="bi bi-building me-1"></i>Almacén</small>
                    <h6 class="fw-bold mb-1">{{ $compra->almacen->nombre ?? 'Sin asignar' }}</h6>
                    <small class="text-muted">Destino de los productos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary bg-opacity-10">
                <div class="card-body p-4">
                    <small class="text-muted text-uppercase fw-bold d-block mb-2"><i class="bi bi-cash-stack me-1"></i>Total</small>
                    <h3 class="fw-bold text-primary mb-0">RD$ {{ number_format($compra->total, 2) }}</h3>
                    <small class="text-muted">Incluye ITBIS</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-list-check text-primary me-2"></i>Productos recibidos</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Producto</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">Precio Unit.</th>
                        <th class="text-end">ITBIS %</th>
                        <th class="text-end">Base</th>
                        <th class="text-end pe-4">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compra->detalles as $detalle)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold">{{ $detalle->producto->nombre ?? '—' }}</div>
                            @if($detalle->producto)
                                <small class="text-muted">
                                    Stock actual: <strong>{{ $detalle->producto->stock }}</strong>
                                </small>
                            @endif
                        </td>
                        <td class="text-end fw-bold">{{ $detalle->cantidad }}</td>
                        <td class="text-end">RD$ {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="text-end text-muted">{{ number_format($detalle->itbis_porcentaje ?? 18, 2) }}%</td>
                        <td class="text-end text-muted">RD$ {{ number_format($detalle->base, 2) }}</td>
                        <td class="text-end pe-4 fw-bold text-success">RD$ {{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay productos registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light bg-opacity-50">
                    <tr>
                        <td colspan="5" class="text-end fw-bold ps-4">Subtotal:</td>
                        <td class="text-end pe-4">RD$ {{ number_format($compra->subtotal ?? $compra->detalles->sum('base'), 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end fw-bold ps-4">ITBIS:</td>
                        <td class="text-end pe-4">RD$ {{ number_format($compra->itbis_total ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end fw-bold fs-5 ps-4">TOTAL:</td>
                        <td class="text-end pe-4 fw-bold fs-5 text-primary">RD$ {{ number_format($compra->total, 2) }}</td>
                    </tr>
                    @if($compra->aplica_retencion_isr || $compra->aplica_retencion_itbis)
                    <tr class="text-danger">
                        <td colspan="5" class="text-end fw-bold ps-4">Retención ISR:</td>
                        <td class="text-end pe-4">- RD$ {{ number_format($compra->retencion_isr, 2) }}</td>
                    </tr>
                    <tr class="text-danger">
                        <td colspan="5" class="text-end fw-bold ps-4">Retención ITBIS:</td>
                        <td class="text-end pe-4">- RD$ {{ number_format($compra->retencion_itbis, 2) }}</td>
                    </tr>
                    <tr class="text-success">
                        <td colspan="5" class="text-end fw-bold fs-5 ps-4">Total a Pagar:</td>
                        <td class="text-end pe-4 fw-bold fs-5 text-success">RD$ {{ number_format($compra->total_neto, 2) }}</td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>

    @if($compra->observaciones)
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-2"><i class="bi bi-chat-left-text text-primary me-1"></i>Observaciones</h6>
                <p class="mb-0 text-muted">{{ $compra->observaciones }}</p>
            </div>
        </div>
    @endif
</div>
@endsection
