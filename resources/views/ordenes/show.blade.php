@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Orden #{{ $orden->id }}
            <span class="badge bg-{{ $orden->estado === 'pendiente' ? 'danger' : ($orden->estado === 'completada' ? 'success' : ($orden->estado === 'anulada' ? 'dark' : 'primary')) }} fs-6">
                {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
            </span>
            <span class="badge bg-{{ $orden->tipo_orden === 'delivery' ? 'info' : ($orden->tipo_orden === 'pickup' ? 'warning' : 'secondary') }} fs-6">
                {{ ucfirst($orden->tipo_orden) }}
            </span>
        </h1>
        <div>
            @if(!in_array($orden->estado, ['completada', 'anulada']))
            <form action="{{ route('ordenes.destroy', $orden) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Anular esta orden?')">
                @csrf @method('DELETE')
                <input type="hidden" name="motivo" value="Anulada por usuario">
                <button class="btn btn-danger">Anular</button>
            </form>
            @endif
            @if($orden->estado === 'anulada')
            <form action="{{ route('ordenes.forceDestroy', $orden) }}" method="POST" class="d-inline form-borrar-show">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-dark btn-trigger-borrar-show">Eliminar permanentemente</button>
            </form>
            @endif
            <a href="{{ route('ordenes.create') }}" class="btn btn-primary">Nueva Orden</a>
            <a href="{{ route('ordenes.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between">
                    <h5>Productos</h5>
                    @if(!in_array($orden->estado, ['completada', 'anulada']))
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Agregar</button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cant</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                    <th>Curso</th>
                                    @if(!in_array($orden->estado, ['completada', 'anulada']))
                                    <th></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orden->detalles as $detalle)
                                <tr>
                                    <td>{{ $detalle->producto?->nombre ?? '—' }}
                                        @if($detalle->notas)<br><small class="text-muted">{{ $detalle->notas }}</small>@endif
                                    </td>
                                    <td>{{ $detalle->cantidad }}</td>
                                    <td>RD$ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td>RD$ {{ number_format($detalle->subtotal, 2) }}</td>
                                    <td><span class="badge bg-info">{{ $detalle->curso }}</span></td>
                                    @if(!in_array($orden->estado, ['completada', 'anulada']))
                                    <td>
                                        <form action="{{ route('ordenes.quitarItem', [$orden, $detalle->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar producto?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger">×</button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Subtotal</th>
                                    <th>RD$ {{ number_format($orden->subtotal, 2) }}</th>
                                    <th colspan="{{ !in_array($orden->estado, ['completada', 'anulada']) ? 2 : 1 }}"></th>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Impuestos</td>
                                    <td>RD$ {{ number_format($orden->impuestos, 2) }}</td>
                                    <td colspan="{{ !in_array($orden->estado, ['completada', 'anulada']) ? 2 : 1 }}"></td>
                                </tr>
                                @if($orden->descuento > 0)
                                <tr>
                                    <td colspan="3" class="text-end">Descuento ({{ $orden->descuento_tipo }})</td>
                                    <td class="text-danger">-RD$ {{ number_format($orden->descuento, 2) }}</td>
                                    <td colspan="{{ !in_array($orden->estado, ['completada', 'anulada']) ? 2 : 1 }}"></td>
                                </tr>
                                @endif
                                <tr class="table-active">
                                    <th colspan="3" class="text-end">Total</th>
                                    <th>RD$ {{ number_format($orden->subtotal + $orden->impuestos - $orden->descuento, 2) }}</th>
                                    <th colspan="{{ !in_array($orden->estado, ['completada', 'anulada']) ? 2 : 1 }}"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            @if($orden->estado === 'completada' && $orden->pagos->count() > 0)
            <div class="card">
                <div class="card-header"><h5>Pagos</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Método</th><th>Monto</th><th>Fecha</th></tr>
                        </thead>
                        <tbody>
                            @foreach($orden->pagos as $pago)
                            <tr>
                                <td>{{ ucfirst($pago->metodo_pago) }}</td>
                                <td>RD$ {{ number_format($pago->monto, 2) }}</td>
                                <td>{{ $pago->fecha_pago->format('h:i A d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-5">
            @if($orden->cliente)
            <div class="card mb-3">
                <div class="card-header"><h5>Cliente</h5></div>
                <div class="card-body">
                    <p><strong>{{ $orden->cliente->nombre }}</strong></p>
                    @if($orden->cliente->telefono)<p>Tel: {{ $orden->cliente->telefono }}</p>@endif
                    @if($orden->cliente->email)<p>Email: {{ $orden->cliente->email }}</p>@endif
                </div>
            </div>
            @endif

            @if($orden->tipo_orden === 'delivery')
            <div class="card mb-3">
                <div class="card-header"><h5>Entrega</h5></div>
                <div class="card-body">
                    <p><strong>Dirección:</strong> {{ $orden->direccion_entrega ?? '—' }}</p>
                    <p><strong>Empresa:</strong> {{ $orden->entregaEmpresa?->nombre ?? '—' }}</p>
                    <p><strong>Contacto:</strong> {{ $orden->telefono_contacto ?? '—' }}</p>
                </div>
            </div>
            @elseif($orden->tipo_orden === 'pickup')
            <div class="card mb-3">
                <div class="card-header"><h5>Retiro</h5></div>
                <div class="card-body">
                    <p><strong>Hora de retiro:</strong> {{ $orden->hora_retiro?->format('h:i A d/m/Y') ?? '—' }}</p>
                    <p><strong>Contacto:</strong> {{ $orden->telefono_contacto ?? '—' }}</p>
                </div>
            </div>
            @endif

            @if($orden->notas)
            <div class="card mb-3">
                <div class="card-header"><h5>Notas</h5></div>
                <div class="card-body">{{ $orden->notas }}</div>
            </div>
            @endif

            @if(!in_array($orden->estado, ['completada', 'anulada']))
                <div class="card mb-3">
                    <div class="card-header"><h5>Cobrar</h5></div>
                    <div class="card-body">
                        <form action="{{ route('ordenes.cobrar', $orden) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Método de Pago</label>
                                <select name="metodo_pago" class="form-select" required id="metodo_pago">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="mixto">Mixto</option>
                                </select>
                            </div>
                            <div id="payment_efectivo">
                                <div class="mb-3">
                                    <label class="form-label">Monto Recibido</label>
                                    <input type="number" step="0.01" name="monto_recibido" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                            <div id="payment_tarjeta" style="display:none;">
                                <div class="mb-3">
                                    <label class="form-label">Monto Tarjeta</label>
                                    <input type="number" step="0.01" name="monto_tarjeta" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                            <div id="payment_transferencia" style="display:none;">
                                <div class="mb-3">
                                    <label class="form-label">Monto Transferencia</label>
                                    <input type="number" step="0.01" name="monto_transferencia" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Propina</label>
                                <input type="number" step="0.01" name="propina" class="form-control" value="0">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="cargo_servicio" class="form-check-input" value="1" id="cargo_servicio">
                                <label class="form-check-label" for="cargo_servicio">Cargo por Servicio (10%)</label>
                            </div>
                            <button type="submit" class="btn btn-success w-100 btn-lg">
                                Cobrar — RD$ {{ number_format($orden->subtotal + $orden->impuestos - $orden->descuento, 2) }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><h5>Cambiar Estado</h5></div>
                    <div class="card-body">
                        <form action="{{ route('ordenes.cambiarEstado', $orden) }}" method="POST" class="row g-2">
                            @csrf
                            <div class="col-8">
                                <select name="estado" class="form-select">
                                    <option value="confirmada">Confirmada</option>
                                    <option value="en_proceso">En Proceso</option>
                                    <option value="lista">Lista</option>
                                    @if($orden->tipo_orden === 'delivery')
                                    <option value="en_camino">En Camino</option>
                                    <option value="entregado">Entregado</option>
                                    @elseif($orden->tipo_orden === 'pickup')
                                    <option value="recogida">Recogida</option>
                                    @endif
                                    <option value="completada">Completada</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary w-100">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <div class="d-grid gap-2">
                <a href="{{ route('ordenes.ticket', $orden) }}" class="btn btn-outline-secondary" target="_blank">Ver Ticket</a>
            </div>
        </div>
    </div>
</div>

@if(!in_array($orden->estado, ['completada', 'anulada']))
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('ordenes.agregarItem', $orden) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Producto</label>
                        <select name="producto_id" class="form-select" required id="modal_producto_select">
                            <option value="">Buscar y seleccionar...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" value="1" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Curso</label>
                        <select name="curso" class="form-select">
                            <option value="entrada">Entrada</option>
                            <option value="fuerte" selected>Fuerte</option>
                            <option value="postre">Postre</option>
                            <option value="bebida">Bebida</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <input type="text" name="notas" class="form-control" maxlength="200">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.getElementById('metodo_pago')?.addEventListener('change', function() {
    const v = this.value;
    document.getElementById('payment_efectivo').style.display = (v === 'efectivo' || v === 'mixto') ? 'block' : 'none';
    document.getElementById('payment_tarjeta').style.display = (v === 'tarjeta' || v === 'mixto') ? 'block' : 'none';
    document.getElementById('payment_transferencia').style.display = (v === 'transferencia' || v === 'mixto') ? 'block' : 'none';
});

// Select2-like product search in modal
let modalProductSelect = document.getElementById('modal_producto_select');
if (modalProductSelect) {
    modalProductSelect.addEventListener('focus', function() {
        if (this.options.length <= 1) {
            fetch('{{ route("ordenes.buscarProducto") }}?q=')
                .then(r => r.json())
                .then(data => {
                    data.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = `${p.nombre} - RD$ ${p.precio}`;
                        modalProductSelect.appendChild(opt);
                    });
                });
        }
    });
}

document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-trigger-borrar-show');
    if (!btn) return;
    const form = btn.closest('.form-borrar-show');
    if (!form) return;
    Swal.fire({
        title: 'Eliminar Orden Permanentemente',
        html: 'Esta acción <strong>no se puede deshacer</strong>. Se eliminará la orden y todos sus registros asociados.',
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        preConfirm: () => {
            return Swal.fire({
                title: 'Confirma escribiendo ELIMINAR',
                input: 'text',
                inputPlaceholder: 'Escribe ELIMINAR',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc2626',
                preConfirm: (input) => {
                    if (input !== 'ELIMINAR') {
                        Swal.showValidationMessage('Debes escribir ELIMINAR');
                        return false;
                    }
                }
            }).then(r => r.isConfirmed ? Promise.resolve() : Promise.reject());
        }
    }).then(r => { if (r.isConfirmed) form.submit(); });
});
</script>
@endpush
@endsection
