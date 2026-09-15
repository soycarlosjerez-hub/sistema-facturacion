@extends('layouts.app')
@section('title', 'Ver Presupuesto')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Presupuesto: {{ $presupuesto->numero }}</h4>
                    <div class="ui-header-meta">Detalles del presupuesto técnico</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('presupuestos.edit')
                <a href="{{ route('presupuestos.edit', $presupuesto) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                @endcan
                <a href="{{ route('presupuestos.index') }}" class="ui-btn ui-btn-sm rounded-pill" style="background:rgba(255,255,255,.15);">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Información General</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Cliente</small>
                        <strong>{{ $presupuesto->cliente->nombre ?? '-' }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Estado</small>
                        @php
                            $estadoBadge = [
                                'borrador' => 'secondary',
                                'enviada' => 'info',
                                'aprobada' => 'success',
                                'rechazada' => 'danger',
                                'vencida' => 'warning',
                            ];
                        @endphp
                        <span class="badge bg-{{ $estadoBadge[$presupuesto->estado] ?? 'secondary' }} fs-6">
                            {{ $presupuesto->estado_label }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Válido Hasta</small>
                        @if($presupuesto->valido_hasta)
                        <strong>{{ $presupuesto->valido_hasta->format('d/m/Y') }}</strong>
                        @if(!$presupuesto->isVigente())
                        <span class="ms-2 text-danger">(Vencido)</span>
                        @endif
                        @else
                        <span class="text-muted">Sin límite</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Creado el</small>
                        <strong>{{ $presupuesto->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                </div>
            </div>

            @if($presupuesto->notas)
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Notas</h6>
                    <p class="text-muted">{{ $presupuesto->notas }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-bold mb-0">Ítems del Presupuesto</h6>
                </div>
                <div class="card-body">
                    @if($presupuesto->items && $presupuesto->items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Descripción</th>
                                    <th>Tipo</th>
                                    <th>Cant.</th>
                                    <th>Precio Unit.</th>
                                    <th>Desc. %</th>
                                    <th>ITBIS %</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($presupuesto->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->descripcion }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $item->tipo_item_label }}</span>
                                    </td>
                                    <td>{{ $item->cantidad }}</td>
                                    <td>RD$ {{ number_format($item->precio_unitario, 2) }}</td>
                                    <td>{{ $item->descuento }}%</td>
                                    <td>{{ $item->itbis_porcentaje }}%</td>
                                    <td class="fw-bold">RD$ {{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="7" class="text-end"><strong>Subtotal:</strong></td>
                                    <td class="fw-bold">RD$ {{ number_format($presupuesto->subtotal, 2) }}</td>
                                </tr>
                                <tr class="table-light">
                                    <td colspan="7" class="text-end"><strong>ITBIS:</strong></td>
                                    <td class="fw-bold">RD$ {{ number_format($presupuesto->itbis, 2) }}</td>
                                </tr>
                                <tr class="table-light">
                                    <td colspan="7" class="text-end"><strong>Descuento:</strong></td>
                                    <td class="fw-bold">RD$ {{ number_format($presupuesto->descuento, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-end"><strong>TOTAL:</strong></td>
                                    <td class="fw-bold fs-5" style="color:#8b5cf6;">RD$ {{ number_format($presupuesto->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox d-block fs-1 mb-2"></i>
                        No hay ítems en este presupuesto
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
