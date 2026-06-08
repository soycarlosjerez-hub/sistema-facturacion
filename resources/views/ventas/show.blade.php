@extends('layouts.app')

@section('title', 'Detalle de Factura #' . str_pad($venta->id, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="container-fluid px-4 animate__animated animate__fadeIn">
    <!-- Header con Acciones -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}" class="text-decoration-none">Ventas</a></li>
                    <li class="breadcrumb-item active">Detalle #{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0">Comprobante de Venta</h3>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ventas.pdf', $venta->id) }}" class="btn btn-light rounded-pill px-3 shadow-sm">
                <i class="bi bi-file-pdf me-2"></i>PDF
            </a>
            <button onclick="window.print()" class="btn btn-light rounded-pill px-3 shadow-sm">
                <i class="bi bi-printer me-2"></i>Imprimir
            </button>
            <a href="{{ route('ventas.create') }}" class="btn btn-primary rounded-pill px-3 shadow-sm">
                <i class="bi bi-plus-circle me-2"></i>Nueva
            </a>
            <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
            @if($venta->estado != 'completada')
            <a href="{{ route('pagos.realizar', $venta->id) }}" class="btn btn-success rounded-pill px-4 shadow">
                <i class="bi bi-cash-coin me-2"></i>Registrar Pago
            </a>
            @endif
            @if(auth()->user()->role === 'admin')
            <button type="button" class="btn btn-outline-danger rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAnularVenta">
                <i class="bi bi-x-circle me-2"></i>Anular
            </button>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Izquierda: Factura Estilo Ticket -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden invoice-card" style="background: white; color: #1e293b;">
                <!-- Decoración Superior -->
                <div class="p-4 text-center border-bottom border-dashed">
                    <h4 class="fw-bold mb-0">COLMADO PREMIUM</h4>
                    <small class="text-muted text-uppercase fw-bold" style="letter-spacing: 2px;">Venta Realizada</small>
                </div>
                
                <div class="card-body p-4">
                    <!-- Info Factura -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Folio</small>
                            <span class="fw-bold text-primary">#{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Fecha</small>
                            <span class="fw-bold small">{{ $venta->created_at->format('d/m/Y h:i A') }}</span>
                        </div>
                    </div>

                    <!-- Cliente y Vendedor -->
                    <div class="mb-4 p-3 rounded-3 bg-light">
                        <div class="mb-2">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Cliente</small>
                            <span class="fw-bold">{{ $venta->cliente->nombre ?? 'Consumidor Final' }}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Atendido por</small>
                            <span class="small">{{ $venta->usuario->name ?? 'Sistema' }}</span>
                        </div>
                    </div>

                    <!-- Estado Badge -->
                    <div class="text-center mb-4">
                        @if($venta->estado == 'completada')
                            <span class="badge bg-success rounded-pill px-4 py-2 shadow-sm">PAGADA COMPLETAMENTE</span>
                        @elseif($venta->estado == 'cuenta_abierta')
                            <span class="badge bg-primary rounded-pill px-4 py-2 shadow-sm">CUENTA ABIERTA</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-4 py-2 shadow-sm">FIAO PENDIENTE</span>
                        @endif
                    </div>

                    <!-- Desglose -->
                    <div class="border-top border-bottom border-dashed py-3 mb-3">
                        @foreach($venta->detalles as $d)
                        <div class="d-flex justify-content-between mb-2">
                            <div class="small pe-3" style="d-flex: 1;">
                                <div class="fw-bold">{{ $d->producto->nombre }}</div>
                                <small class="text-muted">{{ $d->cantidad }} x RD${{ number_format($d->precio_unitario, 2) }}</small>
                            </div>
                            <div class="fw-bold text-end small">RD${{ number_format($d->subtotal, 2) }}</div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Totales -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Subtotal</span>
                            <span class="small">RD${{ number_format($venta->subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">ITBIS (Impuestos)</span>
                            <span class="small">RD${{ number_format($venta->impuestos, 2) }}</span>
                        </div>
                        @if($venta->descuento > 0)
                        <div class="d-flex justify-content-between mb-1 text-danger">
                            <span class="small">Descuento</span>
                            <span class="small">-RD${{ number_format($venta->descuento, 2) }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                            <span class="fw-bold h5">TOTAL</span>
                            <span class="fw-bold h5 text-primary">RD${{ number_format($venta->total, 2) }}</span>
                        </div>
                    </div>

                    <!-- NCF if applies -->
                    @if($venta->ncf)
                    <div class="alert alert-secondary border-0 rounded-3 text-center py-2 mb-2">
                        <small class="text-uppercase fw-bold opacity-50 d-block" style="font-size: 0.6rem;">NCF</small>
                        <span class="fw-bold" style="letter-spacing: 1px;">{{ $venta->ncf }}</span>
                    </div>
                    @endif

                    @if($venta->ecf)
                    @php
                        $ecfEstado = $venta->ecf->estado_info;
                    @endphp
                    <div class="alert alert-{{ $ecfEstado['color'] }} border-0 rounded-3 py-2 mb-0">
                        <small class="text-uppercase fw-bold opacity-75 d-block" style="font-size: 0.6rem;">
                            <i class="bi bi-shield-check me-1"></i>e-CF - {{ $ecfEstado['label'] }}
                        </small>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="fw-bold" style="letter-spacing: 1px;">{{ $venta->ecf->encf }}</span>
                            <a href="{{ route('ecf.show', $venta->ecf) }}" class="btn btn-sm btn-light rounded-pill ms-2">
                                Ver <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Decoración Inferior -->
                <div class="p-3 text-center opacity-50 bg-light">
                    <small>Gracias por su compra</small>
                </div>
            </div>
        </div>

        <!-- Derecha: Historial de Pagos y Auditoría -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-cash-coin text-primary me-2"></i>Seguimiento de Pagos</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Fecha de Pago</th>
                                <th>Monto Pagado</th>
                                <th>Método / Nota</th>
                                <th class="text-end pe-4">Referencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($venta->pagos as $p)
                            <tr>
                                <td class="ps-4 small">{{ \Carbon\Carbon::parse($p->fecha_pago)->format('d/m/Y h:i A') }}</td>
                                <td><span class="fw-bold text-success">RD${{ number_format($p->monto, 2) }}</span></td>
                                <td class="small text-muted">{{ $p->nota ?? 'Pago registrado' }}</td>
                                <td class="text-end pe-4"><small class="badge bg-light text-muted">ID-{{ $p->id }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">
                                    No se han registrado pagos para esta venta todavía.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($venta->pagos->count())
                        <tfoot class="bg-light bg-opacity-50 fw-bold">
                            <tr>
                                <td class="ps-4">Resumen de Cobros</td>
                                <td class="text-success">RD${{ number_format($venta->pagos->sum('monto'), 2) }}</td>
                                <td class="text-danger">Pendiente: RD${{ number_format($venta->total - $venta->pagos->sum('monto'), 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Auditoría de Almacén -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-archive text-primary me-2"></i>Movimientos de Inventario (Kardex)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Producto</th>
                                <th>Almacén</th>
                                <th>Tipo</th>
                                <th class="text-center">Cantidad</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta->detalles as $det)
                            @php 
                                $mov = \App\Models\AlmacenMovimiento::where('producto_id', $det->producto_id)
                                        ->where('nota', 'like', '%' . $venta->id . '%')
                                        ->first();
                            @endphp
                            <tr class="small">
                                <td class="ps-4">{{ $det->producto->nombre }}</td>
                                <td>{{ $det->almacen->nombre ?? 'N/A' }}</td>
                                <td><span class="badge bg-danger bg-opacity-10 text-danger px-2">Salida</span></td>
                                <td class="text-center fw-bold">{{ $det->cantidad }}</td>
                                <td class="text-muted">{{ $venta->usuario->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .breadcrumb, .btn, .nav-section-title, .nav, header { display: none !important; }
        .invoice-card { box-shadow: none !important; border: 1px solid #eee !important; }
        .container-fluid { padding: 0 !important; }
    }
</style>

@if(auth()->user()->role === 'admin')
<!-- Modal Anular Venta -->
<div class="modal fade" id="modalAnularVenta" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('ventas.destroy', $venta->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header border-0 pb-0 text-white" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <h5 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Anular Venta #{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 rounded-3 d-flex align-items-start gap-2 mb-3">
                        <i class="bi bi-info-circle-fill mt-1"></i>
                        <div class="small">
                            Esta acción es <strong>irreversible</strong>. Se revertirá el stock al inventario
                            @if($venta->cliente_id && ($venta->estado == 'pendiente' || $venta->estado == 'cuenta_abierta'))
                                y se ajustará el balance pendiente del cliente
                            @endif.
                        </div>
                    </div>
                    <div class="row g-2 small mb-3">
                        <div class="col-6"><strong>Total:</strong> RD$ {{ number_format($venta->total, 2) }}</div>
                        <div class="col-6"><strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'N/A' }}</div>
                        <div class="col-6"><strong>Pagado:</strong> RD$ {{ number_format($venta->pagos->sum('monto'), 2) }}</div>
                        <div class="col-6"><strong>Estado:</strong> {{ ucfirst($venta->estado) }}</div>
                    </div>
                    <label class="form-label fw-bold small text-uppercase">Motivo de anulación <span class="text-danger">*</span></label>
                    <textarea name="motivo" class="form-control border-0 bg-light" rows="3" required minlength="5" placeholder="Ej: Error en productos, cliente devolvió, etc."></textarea>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="confirmar" value="1" id="confirmAnular" required>
                        <label class="form-check-label small fw-bold" for="confirmAnular">Confirmo que deseo anular esta venta</label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        <i class="bi bi-x-circle me-1"></i>Anular Venta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection