@extends('layouts.app')

@section('title', 'Cierre de Caja')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-safe fs-2"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Cierre de Caja</h4>
                    <div class="ui-header-meta">
                        {{ $caja->nombre }} - Turno iniciado {{ $sesion->fecha_apertura->format('d/m/Y h:i A') }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('cajas.index') }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form id="form-cierre" action="{{ route('sesiones.cerrar', $sesion->id) }}" method="POST">
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
                                    </div>
                                    <input type="hidden" name="total_esperado" value="{{ $totalEsperado }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="p-3 bg-white border border-primary border-opacity-25 rounded-4 h-100 shadow-sm">
                                    <h6 class="fw-bold text-primary text-uppercase mb-3"><i class="bi bi-cash-coin me-2"></i>Declaración Física - Denominaciones</h6>
                                    
                                    <table class="table table-sm table-borderless mb-3" id="denom-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="small fw-bold text-muted">Denominación</th>
                                                <th class="small fw-bold text-muted text-center" style="width:120px">Cantidad</th>
                                                <th class="small fw-bold text-muted text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                             $denominations = [
                                                 ['value' => 5000,  'label' => 'RD$5,000',  'type' => 'bill'],
                                                 ['value' => 2000,  'label' => 'RD$2,000',  'type' => 'bill'],
                                                 ['value' => 1000,  'label' => 'RD$1,000',  'type' => 'bill'],
                                                 ['value' => 500,   'label' => 'RD$500',    'type' => 'bill'],
                                                 ['value' => 200,   'label' => 'RD$200',    'type' => 'bill'],
                                                 ['value' => 100,   'label' => 'RD$100',    'type' => 'bill'],
                                                 ['value' => 50,    'label' => 'RD$50',     'type' => 'bill'],
                                                 ['value' => 20,    'label' => 'RD$20',     'type' => 'bill'],
                                             ];
                                             $coins = [
                                                 ['value' => 25, 'label' => 'RD$25'],
                                                 ['value' => 10, 'label' => 'RD$10'],
                                                 ['value' => 5,  'label' => 'RD$5'],
                                                 ['value' => 1,  'label' => 'RD$1'],
                                             ];
                                            @endphp
                                            @foreach($denominations as $d)
                                            <tr>
                                                <td class="small fw-bold align-middle">
                                                    <i class="bi bi-file-earmark me-1 text-primary"></i>{{ $d['label'] }}
                                                </td>
                                                <td>
                                                    <input type="number" min="0" value="0" class="form-control form-control-sm text-center denom-input" data-value="{{ $d['value'] }}">
                                                </td>
                                                <td class="small text-end align-middle denom-subtotal">RD$0</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <thead class="table-light">
                                            <tr>
                                                <th colspan="3" class="small fw-bold text-muted">
                                                    <i class="bi bi-coin me-1"></i>Monedas
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($coins as $c)
                                            <tr>
                                                <td class="small fw-bold align-middle">
                                                    <i class="bi bi-circle me-1 text-warning"></i>{{ $c['label'] }}
                                                </td>
                                                <td>
                                                    <input type="number" min="0" value="0" class="form-control form-control-sm text-center denom-input" data-value="{{ $c['value'] }}">
                                                </td>
                                                <td class="small text-end align-middle denom-subtotal">RD$0</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="border-top border-2">
                                                <td colspan="2" class="fw-bold text-end">TOTAL CONTADO:</td>
                                                <td class="fw-bold text-end fs-6" id="denom-total">RD$0</td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <input type="hidden" name="monto_declarado" id="monto-declarado" value="0">
                                    <input type="hidden" name="denominaciones" id="denominaciones-json" value="">

                                    <div class="p-3 rounded-3 mb-3 text-center" id="descuadre-box">
                                        <span class="d-block text-muted small fw-bold mb-1">DIFERENCIA (DESCUADRE)</span>
                                        <span class="fs-3 fw-bold" id="descuadre-display">RD$0.00</span>
                                    </div>

                                    <!-- Admin key section - hidden by default, shown only when faltante -->
                                    <div id="admin-key-section" class="d-none mt-3 p-3 bg-warning bg-opacity-10 rounded-3 border border-warning">
                                        <label class="form-label fw-bold text-warning small">
                                            <i class="bi bi-shield-lock me-1"></i>Clave de Administrador (requerida para cerrar con faltante)
                                        </label>
                                        <input type="password" name="admin_key" id="admin-key" class="form-control" placeholder="Ingrese la clave del administrador">
                                        <small class="text-muted d-block mt-1">Solo un administrador puede autorizar el cierre con faltante.</small>
                                    </div>

                                    <div class="mb-0 mt-3">
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
                                <button type="button" id="btn-cerrar" class="btn btn-warning w-100 rounded-pill py-3 fw-bold shadow-sm" onclick="confirmAction({title:'Cerrar Caja', text:'¿Estás seguro de que deseas cerrar la caja? Esta acción no se puede deshacer.', icon:'warning', color:'#f59e0b', confirmText:'Sí, cerrar caja', onSubmit:function(){ document.getElementById('form-cierre').submit(); }})">
                                    <i class="bi bi-lock-fill me-1"></i>PROCESAR CIERRE
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
</div>

<script>
    const totalEsperadoJs = {{ $totalEsperado }};

    function formatRD(amount) {
        return 'RD$' + amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function recalculateDenominations() {
        let totalDenom = 0;
        const inputs = document.querySelectorAll('.denom-input');
        const denomArray = [];

        inputs.forEach(function(input, index) {
            const value = parseFloat(input.getAttribute('data-value')) || 0;
            const qty = parseInt(input.value) || 0;
            const subtotal = value * qty;
            totalDenom += subtotal;

            // Build denominations array for server
            denomArray.push({
                denominacion: value,
                cantidad: qty,
            });

            // Update the subtotal cell in the same row
            const row = input.closest('tr');
            if (row) {
                const subCell = row.querySelector('.denom-subtotal');
                if (subCell) {
                    subCell.innerText = formatRD(subtotal);
                }
            }
        });

        // Update total display
        document.getElementById('denom-total').innerText = formatRD(totalDenom);

        // Update hidden fields
        document.getElementById('monto-declarado').value = totalDenom;
        document.getElementById('denominaciones-json').value = JSON.stringify(denomArray);

        // Calculate descuadre
        const diferencia = totalDenom - totalEsperadoJs;

        const display = document.getElementById('descuadre-display');
        const box = document.getElementById('descuadre-box');

        display.innerText = formatRD(diferencia);

        box.classList.remove('bg-success', 'bg-opacity-10', 'bg-danger', 'bg-opacity-10', 'bg-warning', 'bg-opacity-10');
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

        // Show/hide admin key section based on descuadre
        const adminKeySection = document.getElementById('admin-key-section');
        const adminKeyInput = document.getElementById('admin-key');
        const btnCerrar = document.getElementById('btn-cerrar');

        if (diferencia < 0) {
            adminKeySection.classList.remove('d-none');
            adminKeyInput.required = true;
        } else {
            adminKeySection.classList.add('d-none');
            adminKeyInput.required = false;
            adminKeyInput.value = '';
        }

        // Update button state
        updateButtonState();
    }

    function updateButtonState() {
        const btnCerrar = document.getElementById('btn-cerrar');
        const adminKeyInput = document.getElementById('admin-key');
        const adminKeySection = document.getElementById('admin-key-section');
        const montoDeclarado = parseFloat(document.getElementById('monto-declarado').value) || 0;
        const diferencia = montoDeclarado - totalEsperadoJs;

        if (diferencia < 0 && !adminKeySection.classList.contains('d-none')) {
            // There's a shortage - button is enabled only if admin key is filled
            if (adminKeyInput.value.trim() === '') {
                btnCerrar.disabled = true;
                btnCerrar.classList.add('opacity-50');
            } else {
                btnCerrar.disabled = false;
                btnCerrar.classList.remove('opacity-50');
            }
        } else {
            btnCerrar.disabled = false;
            btnCerrar.classList.remove('opacity-50');
        }
    }

    // Bind events
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.denom-input');
        inputs.forEach(function(input) {
            input.addEventListener('input', recalculateDenominations);
        });

        // Admin key input listener
        const adminKeyInput = document.getElementById('admin-key');
        if (adminKeyInput) {
            adminKeyInput.addEventListener('input', updateButtonState);
        }

        // Initial calculation
        recalculateDenominations();
    });
</script>
@endsection
