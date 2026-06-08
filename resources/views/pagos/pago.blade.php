@extends('layouts.app')

@section('title', 'Cerrar Cuenta / Cobro')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <!-- Header Animado -->
            <div class="text-center mb-4 animate__animated animate__fadeInDown">
                <div class="d-inline-block bg-success bg-opacity-10 p-3 rounded-circle mb-3">
                    <i class="bi bi-cash-coin fs-1 text-success"></i>
                </div>
                <h3 class="fw-bold mb-0">Procesar Cobro de Venta</h3>
                <p class="text-muted">Liquidación de cuenta para el cliente</p>
            </div>

            <div class="row g-4">
                <!-- Columna Info -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                        <div class="card-body p-4 bg-primary text-white position-relative" style="min-height: 180px;">
                            <div class="position-absolute top-0 end-0 p-3 opacity-10">
                                <i class="bi bi-person-badge-fill" style="font-size: 5rem;"></i>
                            </div>
                            <h6 class="text-uppercase small fw-bold opacity-75 mb-3">Resumen de Cliente</h6>
                            <h4 class="fw-bold mb-1">{{ $venta->cliente->nombre }}</h4>
                            <p class="small mb-4 opacity-75"><i class="bi bi-telephone me-2"></i>{{ $venta->cliente->telefono ?? 'N/A' }}</p>
                            
                            <div class="bg-white bg-opacity-20 rounded-3 p-3 mt-auto">
                                <small class="d-block opacity-75 small fw-bold text-uppercase">Venta #{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</small>
                                <div class="fs-4 fw-bold">RD${{ number_format($venta->total, 2) }}</div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                <span class="text-muted small">Monto Pagado:</span>
                                <span class="fw-bold text-success">RD${{ number_format($venta->montoPagado(), 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small fw-bold">RESTANTE A PAGAR:</span>
                                <span class="fs-4 fw-bold text-danger" id="deuda-total">RD${{ number_format($venta->total - $venta->montoPagado(), 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Formulario -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <form action="{{ route('pagos.store') }}" method="POST" id="form-pago">
                                @csrf
                                <input type="hidden" name="venta_id" value="{{ $venta->id }}">
                                @php $maxPago = $venta->total - $venta->montoPagado(); @endphp

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-uppercase text-primary">Monto a Recibir</label>
                                    <div class="input-group input-group-lg border rounded-4 overflow-hidden shadow-sm mb-2">
                                        <span class="input-group-text bg-white border-0 text-muted">RD$</span>
                                        <input type="number" name="monto" id="input-monto" class="form-control border-0 fw-bold" 
                                               step="0.01" min="0.01" max="{{ $maxPago }}" 
                                               value="{{ $maxPago }}" required>
                                    </div>
                                    
                                    <!-- Botones Rápidos -->
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="setMonto({{ $maxPago * 0.25 }})">25%</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="setMonto({{ $maxPago * 0.5 }})">50%</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick="setMonto({{ $maxPago }})">Total</button>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-uppercase text-primary">Método de Pago</label>
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <input type="radio" class="btn-check" name="metodo_pago" id="pago_efectivo" value="efectivo" checked>
                                            <label class="btn btn-outline-primary w-100 rounded-3 py-2" for="pago_efectivo">
                                                <i class="bi bi-cash d-block fs-5"></i> <small>Efectivo</small>
                                            </label>
                                        </div>
                                        <div class="col-4">
                                            <input type="radio" class="btn-check" name="metodo_pago" id="pago_tarjeta" value="tarjeta">
                                            <label class="btn btn-outline-primary w-100 rounded-3 py-2" for="pago_tarjeta">
                                                <i class="bi bi-credit-card d-block fs-5"></i> <small>Tarjeta</small>
                                            </label>
                                        </div>
                                        <div class="col-4">
                                            <input type="radio" class="btn-check" name="metodo_pago" id="pago_transf" value="transferencia">
                                            <label class="btn btn-outline-primary w-100 rounded-3 py-2" for="pago_transf">
                                                <i class="bi bi-bank d-block fs-5"></i> <small>Transf.</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Calculadora de Cambio (Solo visible si es efectivo) -->
                                <div id="calc-cambio" class="p-4 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25 mb-4">
                                    <label class="form-label fw-bold text-uppercase mb-3 text-success"><i class="bi bi-calculator me-2"></i>Calculadora de Cambio</label>
                                    <div class="input-group input-group-lg mb-3 shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text bg-white border-0 text-muted">Recibido RD$</span>
                                        <input type="number" id="recibido" class="form-control border-0 bg-white fw-bold fs-4" placeholder="0.00">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-3 shadow-sm">
                                        <span class="fw-bold text-muted text-uppercase">Su Cambio:</span>
                                        <span class="fs-2 fw-bold text-success mb-0" id="cambio-val">RD$0.00</span>
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="{{ route('clientes.cuentas') }}" class="btn btn-light w-100 rounded-pill py-2">Atrás</a>
                                    </div>
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-bold shadow-sm">
                                            Procesar Pago
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setMonto(val) {
        document.getElementById('input-monto').value = val.toFixed(2);
        actualizarCambio();
    }

    const inputMonto = document.getElementById('input-monto');
    const inputRecibido = document.getElementById('recibido');
    const displayCambio = document.getElementById('cambio-val');

    function actualizarCambio() {
        const monto = parseFloat(inputMonto.value) || 0;
        const recibido = parseFloat(inputRecibido.value) || 0;
        const cambio = recibido - monto;
        
        displayCambio.innerText = 'RD$' + (cambio > 0 ? cambio.toLocaleString(undefined, {minimumFractionDigits: 2}) : '0.00');
        
        if (cambio < 0) {
            displayCambio.classList.remove('text-success');
            displayCambio.classList.add('text-danger');
        } else {
            displayCambio.classList.remove('text-danger');
            displayCambio.classList.add('text-success');
        }
    }

    inputMonto.addEventListener('input', actualizarCambio);
    inputRecibido.addEventListener('input', actualizarCambio);

    // Toggle calculadora
    document.querySelectorAll('input[name="metodo_pago"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const isCash = this.value === 'efectivo';
            document.getElementById('calc-cambio').style.opacity = isCash ? '1' : '0.3';
            inputRecibido.required = isCash;
        });
    });

    document.getElementById('form-pago').addEventListener('submit', function(e) {
        const metodo = document.querySelector('input[name="metodo_pago"]:checked').value;
        const monto = parseFloat(inputMonto.value) || 0;
        const recibido = parseFloat(inputRecibido.value) || 0;

        if (metodo === 'efectivo' && recibido < monto) {
            e.preventDefault();
            alert('El monto recibido debe ser mayor o igual al monto a pagar.');
            inputRecibido.focus();
        }
    });
</script>
@endsection
