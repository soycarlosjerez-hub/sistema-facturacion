@extends('layouts.app')

@section('title', 'Conduce ' . $conduce->numero)

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .table { color: #e2e8f0; }
body.dark-mode .table-light { background: rgba(30,41,59,.8); }
body.dark-mode .table-light th { color: #94a3b8; border-color: #334155; }
body.dark-mode .table-hover tbody tr:hover { background: rgba(51,65,85,.3); }
body.dark-mode dl dt { color: #94a3b8; }
body.dark-mode dl dd { color: #e2e8f0; }
body.dark-mode .modal-content { background: #1e293b; color: #e2e8f0; }
body.dark-mode .modal-header { border-color: #334155; }
body.dark-mode .modal-footer { border-color: #334155; }
body.dark-mode .card-footer { background: rgba(15,23,42,.8); border-color: #334155; }
</style>
@endpush

@section('content')
<div class="container-fluid premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="mb-1 fw-bold">
                        {{ $conduce->numero }}
                        <span class="badge bg-{{ $conduce->estado_color }} ms-2 align-middle">
                            <i class="bi bi-{{ $conduce->estado_icon }} me-1" aria-hidden="true"></i>
                            {{ $conduce->estado_label }}
                        </span>
                        @if($conduce->esta_vencido)
                            <span class="badge bg-danger ms-1">
                                <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>Vencido
                            </span>
                        @endif
                    </h4>
                    <p class="mb-0 opacity-75 small">Creado {{ $conduce->created_at->diffForHumans() }}</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('conduces.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div class="btn-group">
                    <a href="{{ route('conduces.ticket', [$conduce, 'paper' => 80]) }}" target="_blank"
                       class="btn btn-outline-primary" aria-label="Imprimir ticket 80mm">
                        <i class="bi bi-printer me-1"></i>80mm
                    </a>
                    <a href="{{ route('conduces.ticket', [$conduce, 'paper' => 58]) }}" target="_blank"
                       class="btn btn-outline-primary" aria-label="Imprimir ticket 58mm">
                        <i class="bi bi-printer me-1"></i>58mm
                    </a>
                </div>
                <a href="{{ route('conduces.pdf', $conduce) }}" class="btn btn-outline-danger"
                   aria-label="Descargar PDF">
                    <i class="bi bi-file-pdf me-1"></i>PDF
                </a>
                @if(in_array($conduce->estado, ['borrador', 'en_transito']))
                    @can('conduces.edit')
                    <a href="{{ route('conduces.edit', $conduce) }}" class="premium-btn-edit">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <div class="row g-3">
        {{-- Columna izquierda --}}
        <div class="col-lg-8">
            {{-- Items --}}
            <div class="premium-card mb-3">
                <div class="card-accent purple"></div>
                <div class="card-body">
                    <h6 class="premium-card-title">
                        <i class="bi bi-box-seam icon-purple"></i>Productos a Entregar
                        <span class="badge bg-secondary ms-2">{{ $conduce->items->count() }} items</span>
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" role="table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:60px">#</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    @if($conduce->estado === 'entregado')
                                    <th class="text-center">Recibido</th>
                                    <th class="text-center">%</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($conduce->items as $idx => $item)
                                <tr>
                                    <td class="text-muted">{{ str_pad($idx + 1, 3, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $item->nombre }}</div>
                                        <small class="text-muted">{{ $item->codigo ?? 'Sin código' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ number_format($item->cantidad, 2) }}</strong>
                                        <small class="text-muted d-block">{{ $item->unidad }}</small>
                                    </td>
                                    @if($conduce->estado === 'entregado')
                                    <td class="text-center">
                                        {{ number_format($item->cantidad_recibida ?? $item->cantidad, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($item->entregado_completo)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check"></i> 100%
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                {{ number_format($item->porcentaje_entregado, 0) }}%
                                            </span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Notas --}}
            @if($conduce->observaciones)
            <div class="premium-card mb-3">
                <div class="card-accent purple"></div>
                <div class="card-body">
                    <h6 class="premium-card-title"><i class="bi bi-chat-left-text icon-purple"></i>Observaciones</h6>
                    <p class="mb-0">{{ $conduce->observaciones }}</p>
                </div>
            </div>
            @endif

            {{-- Cambio de estado --}}
            @if(!in_array($conduce->estado, ['entregado', 'cancelado']))
            <div class="premium-card">
                <div class="card-accent purple"></div>
                <div class="card-body">
                    <h6 class="premium-card-title"><i class="bi bi-arrow-left-right icon-purple"></i>Cambiar Estado</h6>
                    <form method="POST" action="{{ route('conduces.cambiarEstado', $conduce) }}">
                        @csrf
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach(\App\Models\Conduce::ESTADOS as $key => $estado)
                                @if($key !== $conduce->estado && !in_array($key, ['entregado']))
                                <button type="submit" name="estado" value="{{ $key }}"
                                        class="btn btn-outline-{{ $estado['color'] }}">
                                    <i class="bi bi-{{ $estado['icon'] }} me-1" aria-hidden="true"></i>
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

        {{-- Columna derecha --}}
        <div class="col-lg-4">
            {{-- Marcar como entregado --}}
            @if($conduce->puede_entregarse)
            <div class="premium-card mb-3 border-start border-success border-4">
                <div class="card-body">
                    <h6 class="premium-card-title"><i class="bi bi-check-circle icon-green"></i>Marcar como Entregado</h6>
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                            data-bs-target="#modalEntregar" aria-label="Abrir formulario de entrega">
                        <i class="bi bi-check2 me-1"></i>Confirmar Entrega
                    </button>
                </div>
            </div>
            @endif

            {{-- Info general --}}
            <div class="premium-card mb-3">
                <div class="card-accent purple"></div>
                <div class="card-body">
                    <h6 class="premium-card-title"><i class="bi bi-info-circle icon-purple"></i>Información</h6>
                    <dl class="row mb-0 small">
                        <dt class="col-5 text-muted">Fecha emisión:</dt>
                        <dd class="col-7">{{ $conduce->fecha->format('d/m/Y') }}</dd>

                        @if($conduce->fecha_entrega)
                        <dt class="col-5 text-muted">Fecha entrega:</dt>
                        <dd class="col-7">{{ $conduce->fecha_entrega->format('d/m/Y') }}</dd>
                        @endif

                        @if($conduce->fecha_recibido)
                        <dt class="col-5 text-muted">Recibido:</dt>
                        <dd class="col-7">{{ $conduce->fecha_recibido->format('d/m/Y H:i') }}</dd>
                        @endif

                        <dt class="col-5 text-muted">Cliente:</dt>
                        <dd class="col-7">
                            {{ $conduce->cliente?->nombre }}
                            @if($conduce->cliente?->rnc_cedula)
                                <br><small class="text-muted">{{ $conduce->cliente->rnc_cedula }}</small>
                            @endif
                        </dd>

                        @if($conduce->venta)
                        <dt class="col-5 text-muted">Venta:</dt>
                        <dd class="col-7">
                            <a href="{{ route('ventas.show', $conduce->venta) }}">
                                #{{ $conduce->venta->id }}
                            </a>
                        </dd>
                        @endif

                        <dt class="col-5 text-muted">Emitido por:</dt>
                        <dd class="col-7">{{ $conduce->user?->name ?? 'N/A' }}</dd>

                        <dt class="col-5 text-muted">Total items:</dt>
                        <dd class="col-7"><strong>{{ $conduce->total_items }}</strong></dd>

                        @if($conduce->peso_total)
                        <dt class="col-5 text-muted">Peso total:</dt>
                        <dd class="col-7">{{ number_format($conduce->peso_total, 2) }} kg</dd>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Entrega --}}
            <div class="premium-card mb-3">
                <div class="card-accent purple"></div>
                <div class="card-body">
                    <h6 class="premium-card-title"><i class="bi bi-geo-alt icon-purple"></i>Entrega</h6>
                    <p class="small mb-2">
                        <i class="bi bi-house-door me-1 text-muted"></i>
                        {{ $conduce->direccion_entrega }}
                    </p>
                    @if($conduce->referencia)
                    <p class="small text-muted mb-2">
                        <i class="bi bi-bookmark me-1"></i>{{ $conduce->referencia }}
                    </p>
                    @endif
                    @if($conduce->contacto_entrega)
                    <p class="small mb-1">
                        <i class="bi bi-person me-1 text-muted"></i>{{ $conduce->contacto_entrega }}
                    </p>
                    @endif
                    @if($conduce->telefono_entrega)
                    <p class="small mb-0">
                        <i class="bi bi-telephone me-1 text-muted"></i>{{ $conduce->telefono_entrega }}
                    </p>
                    @endif
                </div>
            </div>

            {{-- Transporte --}}
            @if($conduce->transportista || $conduce->chofer)
            <div class="premium-card mb-3">
                <div class="card-accent purple"></div>
                <div class="card-body">
                    <h6 class="premium-card-title"><i class="bi bi-truck icon-purple"></i>Transporte</h6>
                    <dl class="row mb-0 small">
                        @if($conduce->transportista)
                        <dt class="col-5 text-muted">Empresa:</dt>
                        <dd class="col-7">{{ $conduce->transportista }}</dd>
                        @endif
                        @if($conduce->vehiculo)
                        <dt class="col-5 text-muted">Vehículo:</dt>
                        <dd class="col-7">{{ $conduce->vehiculo }}</dd>
                        @endif
                        @if($conduce->placa)
                        <dt class="col-5 text-muted">Placa:</dt>
                        <dd class="col-7">{{ $conduce->placa }}</dd>
                        @endif
                        @if($conduce->chofer)
                        <dt class="col-5 text-muted">Chofer:</dt>
                        <dd class="col-7">{{ $conduce->chofer }}</dd>
                        @endif
                        @if($conduce->chofer_cedula)
                        <dt class="col-5 text-muted">Cédula:</dt>
                        <dd class="col-7">{{ $conduce->chofer_cedula }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
            @endif

            {{-- Recepción --}}
            @if($conduce->estado === 'entregado')
            <div class="premium-card mb-3 border-start border-success border-4">
                <div class="card-accent green"></div>
                <div class="card-body">
                    <h6 class="premium-card-title"><i class="bi bi-check-circle-fill icon-green"></i>Recibido por</h6>
                    <p class="mb-1 fw-bold">{{ $conduce->recibido_por }}</p>
                    @if($conduce->recibido_cedula)
                    <p class="small text-muted mb-1">Cédula: {{ $conduce->recibido_cedula }}</p>
                    @endif
                    @if($conduce->fecha_recibido)
                    <p class="small text-muted mb-0">
                        <i class="bi bi-clock me-1"></i>{{ $conduce->fecha_recibido->format('d/m/Y H:i') }}
                    </p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Eliminar --}}
            @if($conduce->estado !== 'entregado')
            @can('conduces.delete')
            <div class="premium-card">
                <div class="card-body">
                    <form method="POST" action="{{ route('conduces.destroy', $conduce) }}"
                          onsubmit="return confirm('¿Eliminar el conduce {{ $conduce->numero }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="premium-btn-delete w-100">
                            <i class="bi bi-trash me-1"></i>Eliminar Conduce
                        </button>
                    </form>
                </div>
            </div>
            @endcan
            @endif
        </div>
    </div>
</div>

{{-- Modal: Confirmar Entrega --}}
@if($conduce->puede_entregarse)
<div class="modal fade" id="modalEntregar" tabindex="-1" aria-labelledby="modalEntregarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('conduces.entregar', $conduce) }}">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalEntregarLabel">
                        <i class="bi bi-check-circle me-2"></i>Confirmar Entrega
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Ingresa las cantidades recibidas (pueden ser parciales) y los datos de quien recibe.
                    </p>

                    <h6 class="small fw-bold text-uppercase text-muted mb-2">Cantidades Recibidas</h6>
                    @foreach($conduce->items as $item)
                    <div class="mb-2 row align-items-center">
                        <div class="col-7">
                            <label class="form-label small mb-0">{{ $item->nombre }}</label>
                            <small class="text-muted d-block">Enviado: {{ $item->cantidad }} {{ $item->unidad }}</small>
                        </div>
                        <div class="col-5">
                            <input type="number" step="0.01" min="0"
                                   class="form-control form-control-sm"
                                   name="items_recibidos[{{ $item->id }}]"
                                   value="{{ $item->cantidad }}"
                                   placeholder="Recibido"
                                   aria-label="Cantidad recibida de {{ $item->nombre }}">
                        </div>
                    </div>
                    @endforeach

                    <hr>

                    <h6 class="small fw-bold text-uppercase text-muted mb-2">Datos del Receptor</h6>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="recibido_por" class="form-label">Recibido por <span class="required-indicator">*</span></label>
                            <input type="text" id="recibido_por" name="recibido_por" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label for="recibido_cedula" class="form-label">Cédula</label>
                            <input type="text" id="recibido_cedula" name="recibido_cedula" class="form-control" placeholder="000-0000000-0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check2 me-1"></i>Confirmar Entrega
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
