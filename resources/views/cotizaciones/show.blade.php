@extends('layouts.app')

@section('title', 'Cotización ' . $cotizacion->numero)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-file-earmark-text text-primary me-2"></i>
                {{ $cotizacion->numero }}
            </h2>
            <p class="text-muted mb-0">
                Creada {{ $cotizacion->created_at->diffForHumans() }}
                @if($cotizacion->user)
                    por <strong>{{ $cotizacion->user->name }}</strong>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
            <a href="{{ route('cotizaciones.pdf', $cotizacion) }}" class="btn btn-outline-primary rounded-pill" target="_blank">
                <i class="bi bi-file-pdf me-1"></i> PDF
            </a>
            @if($cotizacion->puede_convertirse && auth()->user()->can('cotizaciones.convertir'))
                <button type="button" class="btn btn-success rounded-pill" onclick="confirmarConvertir()">
                    <i class="bi bi-arrow-right-circle me-1"></i> Convertir a Venta
                </button>
            @endif
            @can('cotizaciones.edit')
                @if(!in_array($cotizacion->estado, ['convertida', 'anulada']))
                <a href="{{ route('cotizaciones.edit', $cotizacion) }}" class="btn btn-warning rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                @endif
            @endcan
        </div>
    </div>

    <div class="row g-3">
        <!-- Columna principal -->
        <div class="col-lg-8">
            <!-- Estado y datos -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <small class="text-muted d-block">Estado</small>
                            <span class="badge bg-{{ $cotizacion->estado_color }} bg-opacity-10 text-{{ $cotizacion->estado_color }} rounded-pill px-3 py-2 mt-1">
                                <i class="bi bi-{{ $cotizacion->estado_icon }} me-1"></i>
                                {{ $cotizacion->estado_label }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Fecha</small>
                            <div class="fw-semibold">{{ $cotizacion->fecha->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Válida hasta</small>
                            <div class="fw-semibold {{ $cotizacion->esta_vencida ? 'text-danger' : '' }}">
                                {{ $cotizacion->fecha_validez->format('d/m/Y') }}
                                @if($cotizacion->esta_vencida)
                                    <i class="bi bi-exclamation-circle"></i>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Cliente</small>
                            <div class="fw-semibold">{{ $cotizacion->cliente?->nombre ?? 'Consumidor Final' }}</div>
                            @if($cotizacion->cliente?->documento)
                                <small class="text-muted">{{ $cotizacion->cliente->documento }}</small>
                            @endif
                        </div>
                    </div>

                    @if($cotizacion->venta)
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Esta cotización fue convertida en 
                        <a href="{{ route('ventas.show', $cotizacion->venta) }}" class="alert-link">
                            Venta #{{ $cotizacion->venta->id }}
                        </a>
                        el {{ $cotizacion->convertida_en->format('d/m/Y H:i') }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Items -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        Items de la Cotización
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Desc.</th>
                                <th class="text-end">ITBIS</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cotizacion->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->nombre }}</div>
                                        <small class="text-muted">{{ $item->codigo ?? '' }} · {{ $item->unidad }}</small>
                                    </td>
                                    <td class="text-center">{{ number_format($item->cantidad, 2) }}</td>
                                    <td class="text-end">RD${{ number_format($item->precio_unitario, 2) }}</td>
                                    <td class="text-end">RD${{ number_format($item->descuento, 2) }}</td>
                                    <td class="text-end">RD${{ number_format($item->itbis, 2) }}</td>
                                    <td class="text-end fw-bold">RD${{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notas y condiciones -->
            @if($cotizacion->notas || $cotizacion->condiciones)
            <div class="row g-3">
                @if($cotizacion->notas)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold">
                                <i class="bi bi-sticky me-1"></i> Notas
                            </h6>
                            <p class="mb-0">{{ $cotizacion->notas }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @if($cotizacion->condiciones)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold">
                                <i class="bi bi-file-text me-1"></i> Términos y Condiciones
                            </h6>
                            <p class="mb-0">{{ $cotizacion->condiciones }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Columna derecha: resumen y acciones -->
        <div class="col-lg-4">
            @if($cotizacion->cliente && $cotizacion->cliente->email && !in_array($cotizacion->estado, ['convertida', 'anulada']))
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-info text-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-envelope me-2"></i>
                        Enviar por Email
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Enviar esta cotización al cliente con un PDF adjunto
                    </p>
                    <button type="button" 
                            class="btn btn-info w-100 mb-2" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalEnviarEmail"
                            aria-label="Abrir formulario para enviar cotización por email">
                        <i class="bi bi-send me-1"></i>
                        Enviar a {{ $cotizacion->cliente->email }}
                    </button>
                    <div class="btn-group w-100" role="group" aria-label="Opciones de impresión">
                        <a href="{{ route('cotizaciones.ticket', [$cotizacion, 'paper' => 80]) }}" 
                           target="_blank"
                           class="btn btn-outline-primary"
                           aria-label="Imprimir ticket en 80mm">
                            <i class="bi bi-printer me-1"></i>Ticket 80mm
                        </a>
                        <a href="{{ route('cotizaciones.ticket', [$cotizacion, 'paper' => 58]) }}" 
                           target="_blank"
                           class="btn btn-outline-primary"
                           aria-label="Imprimir ticket en 58mm">
                            <i class="bi bi-printer me-1"></i>58mm
                        </a>
                    </div>
                    <a href="{{ route('cotizaciones.ticketText', $cotizacion) }}" 
                       class="btn btn-outline-secondary w-100 mt-2"
                       aria-label="Descargar ticket como texto">
                        <i class="bi bi-download me-1"></i>Descargar .txt
                    </a>
                </div>
            </div>
            @endif

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-calculator me-2"></i>
                        Totales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-semibold">RD${{ number_format($cotizacion->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">ITBIS:</span>
                        <span class="fw-semibold">RD${{ number_format($cotizacion->itbis, 2) }}</span>
                    </div>
                    @if($cotizacion->descuento > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Descuento:</span>
                        <span class="fw-semibold text-danger">-RD${{ number_format($cotizacion->descuento, 2) }}</span>
                    </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5">Total:</span>
                        <span class="fw-bold fs-4 text-primary">RD${{ number_format($cotizacion->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Cambiar estado -->
            @if(!in_array($cotizacion->estado, ['convertida', 'anulada']))
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-arrow-left-right me-1"></i> Cambiar Estado
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cotizaciones.cambiarEstado', $cotizacion) }}">
                        @csrf
                        <div class="d-grid gap-2">
                            @foreach(\App\Models\Cotizacion::ESTADOS as $key => $estado)
                                @if($key !== $cotizacion->estado && !in_array($key, ['convertida']))
                                    <button type="submit" name="estado" value="{{ $key }}" 
                                            class="btn btn-outline-{{ $estado['color'] }} text-start">
                                        <i class="bi bi-{{ $estado['icon'] }} me-1"></i>
                                        {{ $estado['label'] }}
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Enviar por Email -->
@if($cotizacion->cliente && $cotizacion->cliente->email && !in_array($cotizacion->estado, ['convertida', 'anulada']))
<div class="modal fade" id="modalEnviarEmail" tabindex="-1" aria-labelledby="modalEnviarEmailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('cotizaciones.enviar', $cotizacion) }}" aria-label="Formulario de envío de cotización por email">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEnviarEmailLabel">
                        <i class="bi bi-envelope me-2" aria-hidden="true"></i>Enviar Cotización por Email
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email_destino" class="form-label">Email del destinatario</label>
                        <input type="email" 
                               class="form-control" 
                               id="email_destino" 
                               name="email_destino"
                               value="{{ $cotizacion->cliente->email }}" 
                               required
                               aria-describedby="emailHelp">
                        <small id="emailHelp" class="form-text text-muted">
                            Si lo deja vacío, se usará el email del cliente
                        </small>
                    </div>
                    <div class="mb-3">
                        <label for="mensaje_email" class="form-label">Mensaje adicional (opcional)</label>
                        <textarea class="form-control" 
                                  id="mensaje_email" 
                                  name="mensaje" 
                                  rows="3" 
                                  maxlength="1000"
                                  placeholder="Ej: Esta cotización tiene un 10% de descuento por pronto pago..."
                                  aria-describedby="mensajeHelp"></textarea>
                        <small id="mensajeHelp" class="form-text text-muted">Se incluirá en el cuerpo del email</small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="incluir_pdf" 
                               name="incluir_pdf" 
                               value="1" 
                               checked
                               aria-describedby="pdfHelp">
                        <label class="form-check-label" for="incluir_pdf">
                            Adjuntar PDF de la cotización
                        </label>
                        <small id="pdfHelp" class="form-text text-muted d-block">
                            Genera un PDF con todos los detalles y lo adjunta al email
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1" aria-hidden="true"></i>Enviar Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<form id="form-convertir" method="POST" style="display:none;" action="{{ route('cotizaciones.convertir', $cotizacion) }}">
    @csrf
</form>

@push('scripts')
<script>
function confirmarConvertir() {
    Swal.fire({
        title: '¿Convertir a venta?',
        html: `La cotización <strong>{{ $cotizacion->numero }}</strong> se convertirá en una venta.<br>Esta acción no se puede deshacer.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, convertir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-convertir').submit();
        }
    });
}
</script>
@endpush
@endsection
