@extends('layouts.app')

@section('title', 'Cierre de Caja')

@push('styles')
@include('partials.premium-ui')
<style>
    .premium-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.4);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
            <div class="premium-header">
                <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="bi bi-safe fs-2 text-white"></i>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-0 text-white">Cierre de Caja</h2>
                            <p class="text-white text-opacity-75 mb-0">{{ $caja->nombre }} - Turno iniciado {{ $sesion->fecha_apertura->format('d/m/Y h:i A') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('cajas.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('cajas.cerrar', $caja->id) }}" method="POST">
                        @csrf
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4 h-100">
                                    <h6 class="fw-bold text-muted text-uppercase mb-3"><i class="bi bi-graph-up me-2"></i>Resumen del Sistema</h6>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Fondo Inicial Base:</span>
                                        <span class="fw-bold">RD${{ number_format($sesion->monto_inicial, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Ventas Totales:</span>
                                        <span class="fw-bold">RD${{ number_format($ventasTotales, 2) }}</span>
                                    </div>
                                    <hr class="opacity-25">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Cobros en Efectivo:</span>
                                        <span class="fw-bold text-success">RD${{ number_format($pagosEfectivo, 2) }}</span>
                                        <input type="hidden" name="cobros_efectivo" value="{{ $pagosEfectivo }}">
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Cobros con Tarjeta:</span>
                                        <span class="fw-bold text-info">RD${{ number_format($pagosTarjeta, 2) }}</span>
                                        <input type="hidden" name="cobros_tarjeta" value="{{ $pagosTarjeta }}">
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="text-muted small">Transferencias:</span>
                                        <span class="fw-bold text-primary">RD${{ number_format($pagosTransferencia, 2) }}</span>
                                        <input type="hidden" name="cobros_transferencia" value="{{ $pagosTransferencia }}">
                                    </div>
                                    
                                    <div class="p-2 bg-success bg-opacity-10 rounded-3 d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-success small">EFECTIVO ESPERADO EN CAJA:</span>
                                        <span class="fs-5 fw-bold text-success">RD${{ number_format($totalEsperado, 2) }}</span>
                                        <input type="hidden" name="total_esperado" id="total-esperado" value="{{ $totalEsperado }}">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="p-3 bg-white border border-primary border-opacity-25 rounded-4 h-100 shadow-sm">
                                    <h6 class="fw-bold text-primary text-uppercase mb-3"><i class="bi bi-cash-coin me-2"></i>Declaración Física</h6>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted small">Efectivo Físico Contado</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text bg-light border-0">RD$</span>
                                            <input type="number" name="monto_declarado" id="monto-declarado" class="form-control fw-bold border-0 bg-light" placeholder="0.00" step="0.01" required>
                                        </div>
                                        <small class="text-muted d-block mt-1">Cuenta todo el dinero en la gaveta, incluyendo el fondo inicial.</small>
                                    </div>

                                    <div class="p-3 rounded-3 mb-3 text-center" id="descuadre-box">
                                        <span class="d-block text-muted small fw-bold mb-1">DIFERENCIA (DESCUADRE)</span>
                                        <span class="fs-3 fw-bold" id="descuadre-display">RD$0.00</span>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label fw-bold text-muted small">Notas (Opcional)</label>
                                        <textarea name="notas" class="form-control border-0 bg-light rounded-3" rows="2" placeholder="Explica cualquier sobrante o faltante aquí..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <a href="{{ route('cajas.index') }}" class="btn btn-light w-100 rounded-pill py-3 fw-bold">
                                    <i class="bi bi-x-lg me-1"></i>Cancelar
                                </a>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-warning w-100 rounded-pill py-3 fw-bold shadow-sm" onclick="return confirm('¿Estás seguro de que deseas cerrar la caja? Esta acción no se puede deshacer.')">
                                    <i class="bi bi-lock-fill me-1"></i>PROCESAR CIERRE
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
</div>

<script>
    document.getElementById('monto-declarado').addEventListener('input', function() {
        const esperado = parseFloat(document.getElementById('total-esperado').value) || 0;
        const declarado = parseFloat(this.value) || 0;
        const diferencia = declarado - esperado;
        
        const display = document.getElementById('descuadre-display');
        const box = document.getElementById('descuadre-box');
        
        display.innerText = 'RD$' + diferencia.toLocaleString(undefined, {minimumFractionDigits: 2});
        
        box.classList.remove('bg-success', 'bg-opacity-10', 'bg-danger', 'bg-warning', 'text-success', 'text-danger');
        display.classList.remove('text-success', 'text-danger', 'text-warning');
        
        if (diferencia === 0) {
            box.classList.add('bg-success', 'bg-opacity-10');
            display.classList.add('text-success');
        } else if (diferencia < 0) {
            box.classList.add('bg-danger', 'bg-opacity-10');
            display.classList.add('text-danger');
        } else {
            box.classList.add('bg-warning', 'bg-opacity-10');
            display.classList.add('text-warning');
        }
    });
</script>
@endsection
