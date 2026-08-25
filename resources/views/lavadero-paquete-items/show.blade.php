@extends('layouts.app')

@section('title', 'Detalle Ítem de Paquete')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">
    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-list-ul"></i></div>
                <div>
                    <h4 class="ui-header-title">Detalle del Ítem</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-check me-1"></i>
                        <span>{{ $lavaderoPaqueteItem->tipo === 'servicio' ? 'Servicio' : 'Producto' }} del paquete</span>
                        <span class="divider">·</span>
                        @if($lavaderoPaqueteItem->incluir_automatico)
                        <span class="ui-badge ui-badge-success"><i class="bi bi-check-lg"></i> Auto</span>
                        @else
                        <span class="ui-badge ui-badge-neutral"><i class="bi bi-x-lg"></i> Manual</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('lavadero-paquete-items.edit', $lavaderoPaqueteItem) }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <a href="{{ route('lavadero-paquete-items.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="paquet-detail-section-title"><i class="bi bi-box-seam me-2"></i>Datos del Ítem</div>

                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Paquete</div>
                        <div class="ui-detail-value fw-bold">
                            {{ $lavaderoPaqueteItem->paquete->nombre ?? '—' }}
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ui-detail-row">
                                <div class="ui-detail-label">Tipo</div>
                                <div class="ui-detail-value">
                                    @if($lavaderoPaqueteItem->tipo === 'servicio')
                                        <span class="ui-badge ui-badge-info">Servicio</span>
                                    @else
                                        <span class="ui-badge ui-badge-neutral">Producto</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ui-detail-row">
                                <div class="ui-detail-label">Orden</div>
                                <div class="ui-detail-value">{{ $lavaderoPaqueteItem->orden ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="ui-detail-row">
                        <div class="ui-detail-label">
                            @if($lavaderoPaqueteItem->tipo === 'servicio')
                                Servicio
                            @else
                                Producto
                            @endif
                        </div>
                        <div class="ui-detail-value fw-bold">
                            @if($lavaderoPaqueteItem->tipo === 'servicio')
                                {{ $lavaderoPaqueteItem->servicio->nombre ?? '—' }}
                            @else
                                {{ $lavaderoPaqueteItem->producto->nombre ?? '—' }}
                            @endif
                        </div>
                    </div>

                    @if($lavaderoPaqueteItem->servicio && $lavaderoPaqueteItem->tipo === 'servicio')
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Precio del Servicio</div>
                        <div class="ui-detail-value fw-bold" style="color:#06b6d4;">RD$ {{ number_format($lavaderoPaqueteItem->servicio->precio, 2) }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="paquet-detail-section-title"><i class="bi bi-sliders me-2"></i>Configuración</div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ui-detail-row">
                                <div class="ui-detail-label">Cantidad</div>
                                <div class="ui-detail-value fw-bold">{{ number_format($lavaderoPaqueteItem->cantidad, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ui-detail-row">
                                <div class="ui-detail-label">Precio Individual</div>
                                <div class="ui-detail-value fw-bold">RD$ {{ number_format($lavaderoPaqueteItem->precio_individual ?? 0, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Incluir Automático</div>
                        <div class="ui-detail-value">
                            @if($lavaderoPaqueteItem->incluir_automatico)
                                <span class="ui-badge ui-badge-success"><i class="bi bi-check-lg me-1"></i> Sí</span>
                                <small class="text-muted ms-2">Se agrega al servicio sin confirmación</small>
                            @else
                                <span class="ui-badge ui-badge-neutral"><i class="bi bi-x-lg me-1"></i> No</span>
                                <small class="text-muted ms-2">Requiere confirmación manual</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="paquet-detail-section-title"><i class="bi bi-calculator me-2"></i>Subtotal</div>
                    <div class="text-center py-3">
                        <div class="fs-2 fw-bold" style="color:var(--accent);">RD$ {{ number_format(($lavaderoPaqueteItem->cantidad ?? 1) * ($lavaderoPaqueteItem->precio_individual ?? 0), 2) }}</div>
                        <small class="text-muted">
                            {{ number_format($lavaderoPaqueteItem->cantidad, 2) }} × RD$ {{ number_format($lavaderoPaqueteItem->precio_individual ?? 0, 2) }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="paquet-detail-section-title"><i class="bi bi-clock-history me-2"></i>Metadatos</div>
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Creado</div>
                        <div class="ui-detail-value small">{{ $lavaderoPaqueteItem->created_at ? $lavaderoPaqueteItem->created_at->format('d/m/Y H:i') : '—' }}</div>
                    </div>
                    @if($lavaderoPaqueteItem->updated_at && $lavaderoPaqueteItem->updated_at->gt($lavaderoPaqueteItem->created_at))
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Actualizado</div>
                        <div class="ui-detail-value small">{{ $lavaderoPaqueteItem->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-accent" style="background:linear-gradient(90deg, #ef4444, rgba(239,68,68,.3));"></div>
                <div class="ui-card-body">
                    <div class="paquet-detail-section-title" style="color:#ef4444;border-bottom-color:rgba(239,68,68,.15);">
                        <i class="bi bi-trash me-2"></i>Zona Peligrosa
                    </div>
                    <p class="small text-muted mb-3">Eliminar este ítem del paquete. Esta acción no se puede deshacer.</p>
                    <form action="{{ route('lavadero-paquete-items.destroy', $lavaderoPaqueteItem) }}" method="POST" id="form-delete-item">
                        @csrf @method('DELETE')
                        <button type="button" class="ui-btn ui-btn-danger w-100" onclick="UI.confirm.delete('{{ route('lavadero-paquete-items.destroy', $lavaderoPaqueteItem) }}', 'Este ítem')">
                            <i class="bi bi-trash me-1"></i> Eliminar Ítem
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
