@extends('layouts.app')

@section('title', 'Detalle Paquete: ' . $paquete->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
.paquet-detail-section-title {
    font-weight: 700;
    font-size: .85rem;
    color: var(--accent, #06b6d4);
    text-transform: uppercase;
    letter-spacing: .5px;
    padding-bottom: .5rem;
    margin-bottom: .75rem;
    border-bottom: 2px solid rgba(var(--accent-rgb, 6,182,212), .15);
}
.paquete-items-table th { font-size: .7rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 700; background: rgba(241,245,249,.8); padding: .7rem .75rem; border-bottom: 2px solid #e2e8f0; }
.paquete-items-table td { padding: .65rem .75rem; font-size: .875rem; }
.paquete-items-table tbody tr { border-bottom: 1px solid #f1f5f9; }
.paquete-items-table tbody tr:last-child { border-bottom: none; }
body.dark-mode .paquete-items-table th { background: rgba(15,23,42,.5); border-bottom-color: #1e293b; color: #94a3b8; }
body.dark-mode .paquete-items-table td { color: #cbd5e1; }
body.dark-mode .paquete-items-table tbody tr { border-bottom-color: #1e293b; }
body.dark-mode .paquet-detail-section-title { border-bottom-color: rgba(var(--accent-rgb, 6,182,212), .2); }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">
    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-gift"></i></div>
                <div>
                    <h4 class="ui-header-title">{{ $paquete->nombre }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-box-seam me-1"></i>
                        <span>Detalle del paquete de lavado</span>
                        @if($paquete->activo)
                        <span class="divider">·</span>
                        <span class="ui-badge ui-badge-success"><i class="bi bi-check-lg"></i> Activo</span>
                        @else
                        <span class="divider">·</span>
                        <span class="ui-badge ui-badge-neutral"><i class="bi bi-x-lg"></i> Inactivo</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('lavadero.paquetes.edit', $paquete) }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <a href="{{ route('lavadero.paquetes.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Columna izquierda: Info básica + items --}}
        <div class="col-lg-8">
            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="paquet-detail-section-title"><i class="bi bi-box-seam me-2"></i>Información Básica</div>

                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Nombre</div>
                        <div class="ui-detail-value fw-bold">{{ $paquete->nombre }}</div>
                    </div>
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Descripción</div>
                        <div class="ui-detail-value">{{ $paquete->descripcion ?: '<span class="text-muted">Sin descripción</span>' }}</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ui-detail-row">
                                <div class="ui-detail-label">Tipo de Vehículo</div>
                                <div class="ui-detail-value">
                                    <span class="ui-badge ui-badge-info">{{ ucfirst($paquete->aplicable_a_tipo ?? 'Todos') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ui-detail-row">
                                <div class="ui-detail-label">Duración</div>
                                <div class="ui-detail-value">{{ $paquete->duracion_minutos ? $paquete->duracion_minutos . ' minutos' : 'No definida' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Estado</div>
                        <div class="ui-detail-value">
                            @if($paquete->activo)
                                <span class="ui-badge ui-badge-success"><i class="bi bi-check-lg"></i> Activo</span>
                            @else
                                <span class="ui-badge ui-badge-neutral"><i class="bi bi-x-lg"></i> Inactivo</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="paquet-detail-section-title"><i class="bi bi-list-check me-2"></i>Items del Paquete</div>

                    @if($paquete->items->count() > 0)
                    <div class="table-responsive">
                        <table class="paquete-items-table table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tipo</th>
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th class="text-end">Precio Ind.</th>
                                    <th class="text-end">Subtotal</th>
                                    <th>Auto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paquete->items as $idx => $item)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        <span class="ui-badge {{ $item->tipo === 'servicio' ? 'ui-badge-info' : 'ui-badge-neutral' }}">
                                            {{ $item->tipo === 'servicio' ? 'Servicio' : 'Producto' }}
                                        </span>
                                    </td>
                                    <td class="fw-medium">
                                        {{ $item->tipo === 'servicio' ? ($item->servicio->nombre ?? '—') : ($item->producto->nombre ?? '—') }}
                                    </td>
                                    <td>{{ number_format($item->cantidad, 2) }}</td>
                                    <td class="text-end">RD$ {{ number_format($item->precio_individual ?? 0, 2) }}</td>
                                    <td class="text-end fw-bold">RD$ {{ number_format(($item->cantidad ?? 1) * ($item->precio_individual ?? 0), 2) }}</td>
                                    <td class="text-center">
                                        @if($item->incluir_automatico)
                                            <span class="ui-badge ui-badge-success"><i class="bi bi-check-lg"></i></span>
                                        @else
                                            <span class="ui-badge ui-badge-neutral"><i class="bi bi-x-lg"></i></span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="ui-empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>Sin items en este paquete</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna derecha: Precio + Tags --}}
        <div class="col-lg-4">
            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="paquet-detail-section-title"><i class="bi bi-currency-dollar me-2"></i>Precios</div>

                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Precio del Paquete</div>
                        <div class="ui-detail-value fw-bold fs-4" style="color:#06b6d4;">RD$ {{ number_format($paquete->precio, 2) }}</div>
                    </div>
                    @if($paquete->precio_anterior)
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Precio Anterior</div>
                        <div class="ui-detail-value text-decoration-line-through text-muted">RD$ {{ number_format($paquete->precio_anterior, 2) }}</div>
                    </div>
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Ahorro</div>
                        <div class="ui-detail-value">
                            <span class="ui-badge ui-badge-success">RD$ {{ number_format($paquete->precio_anterior - $paquete->precio, 2) }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($paquete->tags && count($paquete->tags) > 0)
            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="paquet-detail-section-title"><i class="bi bi-tags me-2"></i>Tags</div>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($paquete->tags as $tag)
                        <span class="ui-badge ui-badge-primary">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            @if($paquete->max_usos_cliente)
            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="paquet-detail-section-title"><i class="bi bi-arrow-repeat me-2"></i>Uso</div>
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Máx. Usos</div>
                        <div class="ui-detail-value">{{ $paquete->max_usos_cliente }}</div>
                    </div>
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Veces Usado</div>
                        <div class="ui-detail-value">{{ $paquete->veces_usado ?? 0 }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
