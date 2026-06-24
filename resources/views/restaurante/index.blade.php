@extends('layouts.app')
@section('title', 'Terminal Restaurante')
@section('content_class', 'px-0')
@section('topbar_extra')
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-white text-dark rounded-pill px-3 py-2 shadow-sm">
            <i class="bi bi-people me-1"></i> <span id="mesas-count">0</span> mesas
        </span>
        <button class="btn btn-light btn-sm rounded-pill shadow-sm" onclick="mostrarWaitlist()">
            <i class="bi bi-clock me-1"></i> Espera
        </button>
        <button class="btn btn-light btn-sm rounded-pill shadow-sm" onclick="toggleMapa()" id="btn-toggle-mapa">
            <i class="bi bi-map"></i> Mapa
        </button>
    </div>
@endsection

@section('content')
<div class="restaurant-pos d-flex" style="height: calc(100vh - 70px);">
    {{-- Panel izquierdo: Grid de mesas --}}
    <div class="mesas-panel p-3 overflow-auto" style="width: 420px; min-width: 420px; background: #f8fafc;">
        {{-- Caja status bar --}}
        <div id="caja-status-bar" class="mb-3"></div>

        <div class="input-group mb-3 shadow-sm rounded-3 overflow-hidden">
            <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="buscar-mesa" class="form-control border-0" placeholder="Buscar mesa...">
        </div>
        <div class="row g-2" id="mesas-grid">
            @foreach($mesas as $mesa)
            @php
                $catColor = $mesa->categoria->color ?? null;
                $reservacion = $mesa->reservacion;
                $reservaInfo = $reservacion ? $reservacion : null;
                $estadoIcono = match($mesa->estado) {
                    'disponible' => 'verde',
                    'ocupada' => 'amarillo',
                    'reservada' => 'azul',
                    default => 'gris',
                };
                $tooltipParts = [];
                if ($reservaInfo) {
                    $tooltipParts[] = 'Reserva: ' . $reservaInfo->cliente_nombre;
                    $tooltipParts[] = $reservaInfo->personas . ' pers';
                    if ($reservaInfo->cliente_telefono) $tooltipParts[] = $reservaInfo->cliente_telefono;
                    if ($reservaInfo->notas) $tooltipParts[] = '"' . $reservaInfo->notas . '"';
                }
            @endphp
            <div class="col-6">
                <button class="mesa-btn w-100 text-start p-3 rounded-4 border-0 shadow-sm position-relative
                    {{ $mesa->estado === 'disponible' ? 'mesa-libre' : '' }}
                    {{ $mesa->estado === 'ocupada' ? 'mesa-ocupada' : '' }}
                    {{ $mesa->estado === 'reservada' ? 'mesa-reservada' : '' }}
                    {{ $mesa->estado === 'inactiva' ? 'mesa-inactiva' : '' }}
                " data-mesa-id="{{ $mesa->id }}" data-estado="{{ $mesa->estado }}" data-pos-x="{{ $mesa->pos_x ?? 0 }}" data-pos-y="{{ $mesa->pos_y ?? 0 }}"
                    onclick="seleccionarMesa(this)"
                    @if($catColor) style="border-left-color: {{ $catColor }} !important;" @endif
                    @if($tooltipParts) title="{{ implode(' | ', $tooltipParts) }}" @endif>
                    @if($reservaInfo)
                    <span class="reserva-ribbon"><i class="bi bi-bookmark-check-fill"></i> {{ \Carbon\Carbon::parse($reservaInfo->fecha_hora)->format('H:i') }}</span>
                    @endif
                    <div class="d-flex justify-content-between align-items-start">
                        <div style="min-width:0;">
                            <div class="fw-bold fs-5 text-truncate d-flex align-items-center gap-2">
                                <span class="estado-icon {{ $estadoIcono }}"></span>
                                {{ $mesa->nombre ?? 'Mesa ' . $mesa->numero }}
                                @if($catColor) <span class="d-inline-block rounded-circle" style="width:10px;height:10px;background:{{ $catColor }};flex-shrink:0;"></span> @endif
                            </div>
                            <small class="text-muted d-block">#{{ $mesa->numero }} · Cap. {{ $mesa->capacidad }}</small>
                            <span class="badge rounded-pill mt-1
                                {{ $mesa->estado === 'disponible' ? 'bg-success' : '' }}
                                {{ $mesa->estado === 'ocupada' ? 'bg-warning text-dark' : '' }}
                                {{ $mesa->estado === 'reservada' ? 'bg-info' : '' }}
                                {{ $mesa->estado === 'inactiva' ? 'bg-secondary' : '' }}
                            ">{{ ucfirst($mesa->estado) }}</span>
                            @if($reservaInfo)
                                <div class="mt-1 p-1 rounded-2" style="font-size:.9rem;line-height:1.4;background:rgba(255,255,255,.7);">
                                    <div class="fw-semibold text-dark"><i class="bi bi-person-fill me-1"></i>{{ Str::limit($reservaInfo->cliente_nombre, 14) }}</div>
                                    <div class="d-flex gap-2 flex-wrap text-dark">
                                        <span><i class="bi bi-people me-1"></i>{{ $reservaInfo->personas }} pers</span>
                                        @if($reservaInfo->cliente_telefono)
                                        <span><i class="bi bi-telephone me-1"></i>{{ $reservaInfo->cliente_telefono }}</span>
                                        @endif
                                    </div>
                                    <div class="reserva-timer" data-fecha="{{ $reservaInfo->fecha_hora instanceof \Carbon\Carbon ? $reservaInfo->fecha_hora->toIso8601String() : $reservaInfo->fecha_hora }}"></div>
                                </div>
                            @endif
                        </div>
                        @if($mesa->ordenActiva)
                            <span class="badge bg-dark rounded-pill flex-shrink-0 mt-1">RD$ {{ number_format($mesa->ordenActiva->total, 0) }}</span>
                        @endif
                    </div>
                </button>
            </div>
            @endforeach
        </div>
        {{-- Mapa de mesas (oculto por defecto) --}}
        <div id="mesas-mapa" class="position-relative d-none" style="min-height:500px;background:#f0f4f8;border-radius:16px;overflow:hidden;">
            <div class="position-absolute top-0 end-0 m-2 d-flex gap-1" style="z-index:10;">
                <button class="btn btn-sm btn-light rounded-pill shadow-sm" onclick="guardarMapa()"><i class="bi bi-save me-1"></i> Guardar</button>
                <button class="btn btn-sm btn-light rounded-pill shadow-sm" onclick="toggleMapa()"><i class="bi bi-grid"></i> Grid</button>
            </div>
            <div id="mapa-canvas" style="position:relative;width:100%;height:100%;min-height:500px;">
                @foreach($mesas as $mesa)
                @php $catColor = $mesa->categoria->color ?? null; @endphp
                <div class="mesa-mapa-btn position-absolute rounded-4 border-0 shadow-sm text-center p-2
                    {{ $mesa->estado === 'disponible' ? 'bg-white mesa-libre' : '' }}
                    {{ $mesa->estado === 'ocupada' ? 'bg-warning bg-opacity-10 mesa-ocupada' : '' }}
                    {{ $mesa->estado === 'reservada' ? 'bg-info bg-opacity-10 mesa-reservada' : '' }}
                    {{ $mesa->estado === 'inactiva' ? 'bg-secondary bg-opacity-10 mesa-inactiva opacity-50' : '' }}
                " data-mesa-id="{{ $mesa->id }}" data-estado="{{ $mesa->estado }}"
                    style="left:{{ $mesa->pos_x ?? 20 }}px;top:{{ $mesa->pos_y ?? 20 }}px;width:140px;cursor:grab;{{ $catColor ? 'border-left:4px solid '.$catColor.' !important;' : '' }}">
                    <div class="fw-bold small">{{ $mesa->nombre ?? 'Mesa '.$mesa->numero }}</div>
                    <small class="text-muted" style="font-size:.6rem;">Cap. {{ $mesa->capacidad }}</small>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Panel derecho: Orden activa --}}
    <div class="orden-panel flex-grow-1 d-flex flex-column bg-white border-start">
        <div class="orden-header p-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0" id="orden-titulo">Selecciona una mesa</h5>
                    <small class="text-muted" id="orden-subtitulo">Haz clic en una mesa para ver su orden</small>
                </div>
                <div class="d-none" id="orden-actions">
                    <button class="btn btn-outline-info btn-sm rounded-pill me-1" onclick="mostrarHistorial()" title="Historial">
                        <i class="bi bi-clock-history"></i>
                    </button>
                    <button class="btn btn-outline-warning btn-sm rounded-pill me-1" onclick="mostrarTrasladar()" title="Trasladar a otra mesa">
                        <i class="bi bi-arrows-move"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm rounded-pill" onclick="cerrarMesa()" title="Cerrar mesa">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="d-none mt-2" id="cliente-selector">
                <small class="text-muted">Cliente:</small>
                <span class="fw-semibold small" id="cliente-nombre">Consumidor Final</span>
                <button class="btn btn-sm btn-link text-decoration-none p-0 ms-1" onclick="mostrarBuscarCliente()">
                    <i class="bi bi-pencil"></i>
                </button>
            </div>
        </div>

        {{-- Buscador de platos (abre modal con teclado táctil) --}}
        <div class="p-3 border-bottom d-none" id="productos-search-bar">
            <div class="input-group shadow-sm rounded-3 cursor-pointer" onclick="abrirModalProductos()">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="buscar-producto" class="form-control bg-white" placeholder="Toca aquí para buscar platos..." readonly>
            </div>
            <div class="mt-1 d-flex gap-1">
                <select id="categoria-filtro" class="form-select form-select-sm rounded-3" style="max-width:140px;">
                    <option value="">Todas las categorías</option>
                </select>
                <select id="item-curso" class="form-select form-select-sm rounded-3" style="max-width:110px;">
                    <option value="entrada">Entrada</option>
                    <option value="fuerte" selected>Plato Fuerte</option>
                    <option value="postre">Postre</option>
                    <option value="bebida">Bebida</option>
                </select>
                <input type="text" id="item-notas" class="form-control form-control-sm rounded-3" placeholder="Notas" maxlength="200">
            </div>
        </div>

        {{-- Menú rápido / Populares --}}
        <div class="p-2 border-bottom d-none" id="quick-menu">
            <div class="d-flex align-items-center mb-1">
                <small class="text-muted fw-semibold me-2"><i class="bi bi-fire me-1"></i>Platos populares</small>
                <button class="btn btn-sm p-0 text-muted" onclick="toggleQuickMenu()" type="button"><i class="bi bi-chevron-up" id="quick-menu-toggle-icon"></i></button>
            </div>
            <div id="quick-menu-items" class="d-flex gap-1 flex-wrap overflow-auto" style="max-height:80px;"></div>
        </div>

        {{-- Items de la orden --}}
        <div class="orden-items flex-grow-1 overflow-auto p-3" id="orden-items">
            <div class="text-center text-muted mt-5">
                <i class="bi bi-hand-index fs-1 d-block mb-2"></i>
                <p>Selecciona una mesa para comenzar</p>
            </div>
        </div>

        {{-- Footer con totales y botones --}}
        <div class="orden-footer border-top p-3 d-none" id="orden-footer">
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <small class="text-muted d-block">Subtotal</small>
                    <span class="fw-bold" id="orden-subtotal">RD$ 0.00</span>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted d-block">ITBIS</small>
                    <span class="fw-bold" id="orden-itbis">RD$ 0.00</span>
                </div>
                <div class="col-12">
                    <small class="text-muted d-none" id="orden-descuento">Descuento</small>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between small d-none" id="orden-delivery-fee"></div>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center bg-primary bg-opacity-10 rounded-3 p-2">
                        <small class="fw-bold">TOTAL</small>
                        <span class="fs-4 fw-bold text-primary" id="orden-total">RD$ 0.00</span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mb-2">
                @can('restaurante.descuento')
                <button class="btn btn-sm btn-outline-secondary rounded-pill flex-fill" onclick="mostrarDescuento()" id="btn-descuento">
                    <i class="bi bi-percent me-1"></i> Descuento
                </button>
                @endcan
                @can('restaurante.anular')
                <button class="btn btn-sm btn-outline-danger rounded-pill flex-fill" onclick="anularOrden()" id="btn-anular">
                    <i class="bi bi-x-circle me-1"></i> Anular
                </button>
                @endcan
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <button class="btn btn-outline-primary w-100 rounded-pill py-2" onclick="abrirModalProductos()" id="btn-agregar">
                        <i class="bi bi-plus-circle me-1"></i> Agregar
                    </button>
                </div>
                <div class="col-6">
                    <button class="btn btn-success w-100 rounded-pill py-2 fw-bold" onclick="mostrarPago()" id="btn-cobrar">
                        <i class="bi bi-cash-coin me-1"></i> Cobrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de platos con teclado virtual --}}
<div class="modal fade" id="productosModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content rounded-4 border-0 shadow" style="max-height:95vh;">
            <div class="modal-header border-0 bg-primary text-white rounded-top-4 py-2">
                <h6 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Agregar Plato</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="cerrarModalProductos()"></button>
            </div>
            <div class="modal-body p-3 d-flex flex-column" style="height: calc(95vh - 60px);">
                {{-- Búsqueda --}}
                <div class="input-group shadow-sm rounded-3 mb-2">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="modal-buscar-producto" class="form-control" placeholder="Buscar plato..." autocomplete="off" oninput="modalBuscarProductos()">
                    <button class="btn btn-outline-secondary" type="button" id="modal-btn-limpiar" style="display:none;" onclick="modalLimpiarBusqueda()"><i class="bi bi-x-lg"></i></button>
                </div>
                {{-- Curso, categoría y notas --}}
                <div class="d-flex gap-2 mb-2">
                    <select id="modal-item-curso" class="form-select form-select-sm rounded-3" style="max-width:120px;">
                        <option value="entrada">Entrada</option>
                        <option value="fuerte" selected>Plato Fuerte</option>
                        <option value="postre">Postre</option>
                        <option value="bebida">Bebida</option>
                    </select>
                    <select id="modal-categoria-filtro" class="form-select form-select-sm rounded-3" onchange="modalBuscarProductos()">
                        <option value="">Todas</option>
                    </select>
                    <input type="text" id="modal-item-notas" class="form-control form-control-sm rounded-3" placeholder="Notas" maxlength="200">
                </div>
                {{-- Grid de platos --}}
                <div id="modal-productos-grid" class="row g-2 overflow-auto mb-2" style="flex:1; min-height:0;"></div>
                {{-- Teclado virtual --}}
                <div class="border-top pt-1 mt-1" id="teclado-virtual">
                    <div class="d-flex justify-content-between align-items-center mb-0">
                        <small class="text-muted fw-semibold" style="font-size:.65rem;">Teclado</small>
                        <div class="btn-group btn-group-xs">
                            <button class="btn btn-outline-secondary rounded-start-pill" style="font-size:.65rem;padding:1px 6px;" onclick="tecladoIdioma('us')" id="btn-idioma-us">US</button>
                            <button class="btn btn-outline-secondary rounded-end-pill" style="font-size:.65rem;padding:1px 6px;" onclick="tecladoIdioma('es')" id="btn-idioma-es">ES</button>
                        </div>
                    </div>
                    <div id="teclado-rows"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Descuento --}}
<div class="modal fade" id="descuentoModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="fw-bold"><i class="bi bi-percent me-2"></i>Aplicar Descuento</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Tipo</label>
                    <select id="descuento-tipo" class="form-select rounded-3" onchange="document.getElementById('descuento-valor').placeholder = this.value === 'porcentaje' ? 'Ej: 10' : 'Ej: 500'">
                        <option value="porcentaje">Porcentaje (%)</option>
                        <option value="monto">Monto fijo (RD$)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Valor</label>
                    <input type="number" id="descuento-valor" class="form-control rounded-3" step="0.01" min="0" placeholder="Ej: 10">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Motivo <span class="text-danger">*</span></label>
                    <input type="text" id="descuento-motivo" class="form-control rounded-3" maxlength="200" placeholder="Ej: Cliente frecuente">
                </div>
                <button class="btn btn-primary w-100 rounded-pill" onclick="aplicarDescuento()">
                    <i class="bi bi-check-lg me-1"></i> Aplicar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Trasladar Mesa --}}
<div class="modal fade" id="trasladarModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="fw-bold">Trasladar a otra mesa</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select id="mesa-destino" class="form-select rounded-3 mb-3">
                    <option value="">Seleccionar mesa destino...</option>
                    @foreach($mesas as $m)
                        @if(!$m->ordenActiva && $m->estado !== 'inactiva')
                        <option value="{{ $m->id }}">{{ $m->nombre ?? 'Mesa '.$m->numero }} (Cap. {{ $m->capacidad }})</option>
                        @endif
                    @endforeach
                </select>
                <button class="btn btn-primary w-100 rounded-pill" onclick="trasladarMesa()">
                    <i class="bi bi-arrows-move me-1"></i> Trasladar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Historial --}}
<div class="modal fade" id="historialModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="fw-bold"><i class="bi bi-clock-history me-2"></i>Historial</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="historial-content">
                <div class="text-center text-muted py-4"><div class="spinner-border spinner-border-sm me-2"></div>Cargando...</div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Pago Premium --}}
<div class="modal fade cobrar-premium" id="pagoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
        <div class="modal-content">
            <!-- Premium Animated Header -->
            <div class="cobrar-header text-white position-relative" style="z-index:2;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;">
                            <i class="bi bi-cash-coin" style="font-size:1.6rem;"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0" style="font-size:1.25rem;">Cobrar Mesa</h5>
                            <small class="text-white text-opacity-75" style="font-size:.8rem;" id="pago-mesa-label">Mesa #--</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" style="width:32px;height:32px;" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <!-- Body -->
            <div class="cobrar-body px-4 pt-4 pb-3">

                <!-- Total Card -->
                <div class="cobrar-total-card mb-4">
                    <small class="text-muted d-block mb-1" style="font-size:.72rem;letter-spacing:.5px;text-transform:uppercase;">Total a Cobrar</small>
                    <h2 class="fw-bold mb-0" style="font-size:2rem;color:#059669;" id="pago-total">RD$ 0.00</h2>
                </div>

                <!-- Payment Methods -->
                <div class="d-flex gap-2 mb-4 flex-wrap" id="pago-metodos">
                    <button class="metodo-btn efectivo active-metodo flex-fill" data-metodo="efectivo">
                        <i class="bi bi-cash me-1"></i> Efectivo
                    </button>
                    <button class="metodo-btn tarjeta flex-fill" data-metodo="tarjeta">
                        <i class="bi bi-credit-card me-1"></i> Tarjeta
                    </button>
                    <button class="metodo-btn transferencia flex-fill" data-metodo="transferencia">
                        <i class="bi bi-phone me-1"></i> Transferencia
                    </button>
                    <button class="metodo-btn mixto flex-fill" data-metodo="mixto">
                        <i class="bi bi-layers me-1"></i> Mixto
                    </button>
                </div>

                <!-- Efectivo Section -->
                <div id="pago-efectivo" class="pago-detalle">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted" style="font-size:.78rem;">MONTO RECIBIDO</label>
                        <div class="input-group">
                            <span class="input-prefix">RD$</span>
                            <input type="number" id="monto-recibido" class="form-control input-premium" step="0.01" min="0" placeholder="0.00">
                        </div>
                    </div>
                    <div id="cambio-info" class="d-none">
                        <div class="cambio-card">
                            <small class="text-muted d-block" style="font-size:.72rem;">Cambio</small>
                            <span class="fw-bold" style="font-size:1.5rem;color:#059669;" id="cambio-monto">RD$ 0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Mixto Section -->
                <div id="pago-mixto" class="pago-detalle" style="display:none;">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted" style="font-size:.75rem;">EFECTIVO</label>
                            <input type="number" id="mixto-efectivo" class="form-control input-premium" style="font-size:1rem;padding:.65rem .75rem;" step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted" style="font-size:.75rem;">TARJETA</label>
                            <input type="number" id="mixto-tarjeta" class="form-control input-premium" style="font-size:1rem;padding:.65rem .75rem;" step="0.01" min="0" placeholder="0.00">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted" style="font-size:.75rem;">TRANSFERENCIA</label>
                            <input type="number" id="mixto-transferencia" class="form-control input-premium" style="font-size:1rem;padding:.65rem .75rem;" step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="col-6 d-flex align-items-end pb-1">
                            <small class="text-muted" id="mixto-restante" style="font-size:.78rem;"></small>
                        </div>
                    </div>
                </div>

                <!-- Propina & Dividir -->
                <div class="mt-3 pt-3" style="border-top:1px solid rgba(0,0,0,0.06);">
                    <div class="row g-3 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted" style="font-size:.75rem;">PROPINA</label>
                            <div class="d-flex gap-1 mb-2">
                                <input type="number" id="propina-input" class="form-control input-premium" style="font-size:.95rem;padding:.55rem .65rem;" step="0.01" min="0" value="0" placeholder="0.00" oninput="actualizarTotalPago()">
                            </div>
                            <div class="d-flex gap-1">
                                <button class="propina-btn" onclick="document.getElementById('propina-input').value=(parseFloat(ordenActual?.total||0)*0.10).toFixed(2);actualizarTotalPago()">10%</button>
                                <button class="propina-btn" onclick="document.getElementById('propina-input').value=(parseFloat(ordenActual?.total||0)*0.15).toFixed(2);actualizarTotalPago()">15%</button>
                                <button class="propina-btn" onclick="document.getElementById('propina-input').value=(parseFloat(ordenActual?.total||0)*0.18).toFixed(2);actualizarTotalPago()">18%</button>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted" style="font-size:.75rem;">DIVIDIR CUENTA</label>
                            <div class="d-flex gap-1">
                                <button class="split-btn" onclick="abrirSplitBill(2)">2</button>
                                <button class="split-btn" onclick="abrirSplitBill(3)">3</button>
                                <button class="split-btn" onclick="abrirSplitBill(4)">4</button>
                                <button class="split-btn" onclick="abrirSplitBill(5)">5</button>
                            </div>
                        </div>
                    </div>
                    <div id="cargo-servicio-row" class="servicio-check mt-2" style="display:none;">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" id="cargo-servicio-check" onchange="actualizarTotalPago()">
                            <label class="form-check-label small fw-bold ms-2" for="cargo-servicio-check" style="font-size:.8rem;">
                                Aplicar cargo por servicio (<span id="cargo-servicio-pct">0</span>%)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-4 py-3 d-flex justify-content-between align-items-center" style="background:rgba(248,250,252,0.9);border-top:1px solid rgba(0,0,0,0.04);">
                <button type="button" class="btn btn-cancelar-premium" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-cobrar-premium" onclick="procesarPago()">
                    <i class="bi bi-check-circle me-1"></i> Cobrar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Post-Pago --}}
<div class="modal fade" id="postPagoModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 bg-success text-white rounded-top-4">
                <h6 class="modal-title fw-bold"><i class="bi bi-check-circle me-2"></i>Pago Exitoso</h6>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                </div>
                <h4 class="fw-bold" id="post-mesa-info">Mesa #1</h4>
                <p class="text-muted" id="post-cliente">Cliente: Consumidor Final</p>
                <div class="bg-light rounded-3 p-3 mb-3">
                    <small class="text-muted d-block">Total Cobrado</small>
                    <span class="fs-2 fw-bold text-success" id="post-total">RD$ 0.00</span>
                    <small class="text-muted d-block mt-1" id="post-metodo">Efectivo</small>
                </div>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="#" class="btn btn-outline-primary rounded-pill" id="btn-ticket" target="_blank">
                        <i class="bi bi-receipt me-1"></i> Ticket
                    </a>
                    <button class="btn btn-outline-dark rounded-pill" id="btn-imprimir" onclick="imprimirTicket()">
                        <i class="bi bi-printer me-1"></i> Imprimir
                    </button>
                    <button class="btn btn-primary rounded-pill" id="btn-facturar" onclick="facturarMesa()">
                        <i class="bi bi-shield-check me-1"></i> Facturar (e-CF)
                    </button>
                </div>
                <div id="factura-status" class="mt-2 small d-none"></div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-success rounded-pill px-5" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Split Bill --}}
<div class="modal fade" id="splitBillModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-layers me-2"></i>Dividir Cuenta por Items</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="split-bill-body">
                <div class="text-center text-muted py-4">Cargando...</div>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-between" id="split-bill-footer" style="display:none !important;">
                <div>
                    <small class="text-muted" id="split-totals"></small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" onclick="confirmarSplitBill()">
                        <i class="bi bi-check-lg me-1"></i> Confirmar y Pagar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Waitlist --}}
<div class="modal fade" id="waitlistModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-clock me-2"></i>Lista de Espera</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="waitlist-form" onsubmit="agregarWaitlist(event)" class="mb-3">
                    @csrf
                    <div class="row g-2">
                        <div class="col-8">
                            <input type="text" id="wl-nombre" class="form-control rounded-3" placeholder="Nombre del cliente *" required>
                        </div>
                        <div class="col-4">
                            <input type="number" id="wl-personas" class="form-control rounded-3" placeholder="Personas" value="2" min="1" required>
                        </div>
                        <div class="col-8">
                            <input type="text" id="wl-telefono" class="form-control rounded-3" placeholder="Teléfono">
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill">
                                <i class="bi bi-plus"></i> Agregar
                            </button>
                        </div>
                        <div class="col-12">
                            <input type="text" id="wl-notas" class="form-control rounded-3" placeholder="Notas...">
                        </div>
                    </div>
                </form>
                <div id="waitlist-entries" class="mt-2">
                    <div class="text-center text-muted py-3 small">Cargando...</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Buscar Cliente --}}
<div class="modal fade" id="clienteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-person me-2"></i>Seleccionar Cliente</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="buscar-cliente" class="form-control rounded-3" placeholder="Buscar por nombre o RNC..." autocomplete="off">
                </div>
                <div id="clientes-resultados" style="max-height: 250px; overflow-y: auto;"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Consumidor Final</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tipo Orden (Delivery / Para llevar) --}}
<div class="modal fade" id="tipoOrdenModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-cup-straw me-2"></i>Abrir Mesa</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Tipo de Orden</label>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary flex-fill rounded-pill tipo-orden-btn active" data-value="mesa" onclick="seleccionarTipoOrden('mesa')">
                            <i class="bi bi-shop me-1"></i> Mesa
                        </button>
                        <button type="button" class="btn btn-outline-primary flex-fill rounded-pill tipo-orden-btn" data-value="delivery" onclick="seleccionarTipoOrden('delivery')">
                            <i class="bi bi-truck me-1"></i> Delivery
                        </button>
                        <button type="button" class="btn btn-outline-primary flex-fill rounded-pill tipo-orden-btn" data-value="para_llevar" onclick="seleccionarTipoOrden('para_llevar')">
                            <i class="bi bi-box-seam me-1"></i> Llevar
                        </button>
                    </div>
                </div>
                <div class="mb-3" id="delivery-company-group" style="display:none;">
                    <label class="form-label small fw-bold">Empresa de Delivery</label>
                    <select id="delivery-company-select" class="form-select rounded-3">
                        <option value="">Seleccionar...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary rounded-pill btn-lg px-4" onclick="confirmarAbrirMesa()">
                    <i class="bi bi-check-lg me-1"></i> Abrir Mesa
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Abrir Caja --}}
<div class="modal fade" id="abrirCajaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-cash-stack me-2"></i>Abrir Caja</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Seleccionar Caja</label>
                    <select id="caja-select" class="form-select rounded-3"></select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Monto Inicial (RD$)</label>
                    <input type="number" id="caja-monto-inicial" class="form-control rounded-3" value="0" min="0" step="0.01">
                </div>
                @can('cajas.create')
                <hr>
                <div class="mb-3">
                    <label class="form-label small fw-bold">¿No hay cajas? Crear una nueva</label>
                    <div class="input-group">
                        <input type="text" id="nueva-caja-nombre" class="form-control rounded-start-3" placeholder="Nombre de la caja">
                        <button class="btn btn-outline-primary" onclick="crearCaja()"><i class="bi bi-plus"></i></button>
                    </div>
                </div>
                @endcan
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="abrirCaja()">
                    <i class="bi bi-check-lg me-1"></i> Abrir
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Cerrar Caja --}}
<div class="modal fade" id="cerrarCajaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-cash-stack me-2"></i>Cerrar Caja</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="cerrar-caja-resumen" class="mb-3 small"></div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Total Declarado (RD$) <span class="text-danger">*</span></label>
                    <input type="number" id="cierre-monto-declarado" class="form-control rounded-3" step="0.01" min="0">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Cobros Efectivo (RD$) <span class="text-danger">*</span></label>
                    <input type="number" id="cierre-cobros-efectivo" class="form-control rounded-3" step="0.01" min="0" value="0">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Cobros Tarjeta (RD$) <span class="text-danger">*</span></label>
                    <input type="number" id="cierre-cobros-tarjeta" class="form-control rounded-3" step="0.01" min="0" value="0">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Cobros Transferencia (RD$) <span class="text-danger">*</span></label>
                    <input type="number" id="cierre-cobros-transferencia" class="form-control rounded-3" step="0.01" min="0" value="0">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Notas</label>
                    <input type="text" id="cierre-notas" class="form-control rounded-3" maxlength="500" placeholder="Opcional">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger rounded-pill px-4" onclick="cerrarCaja()">
                    <i class="bi bi-check-lg me-1"></i> Cerrar Caja
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* ============ Premium Cobrar Modal ============ */
@keyframes cobrarGradientShift {
    0%   { background-position: 0% 50%; }
    50%  { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
@keyframes cobrarPulse {
    0%, 100% { transform: scale(1); opacity: 0.6; }
    50%      { transform: scale(1.2); opacity: 1; }
}
@keyframes cobrarSlideUp {
    from { opacity: 0; transform: translateY(24px) scale(0.96); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes cobrarShine {
    0%   { left: -100%; }
    100% { left: 200%; }
}
.cobrar-premium .modal-content {
    border: none;
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 25px 60px rgba(0,0,0,0.18), 0 0 0 1px rgba(255,255,255,0.08);
    animation: cobrarSlideUp 0.35s ease-out;
}
.cobrar-premium .cobrar-header {
    background: linear-gradient(135deg, #059669, #10b981, #06b6d4, #059669);
    background-size: 300% 300%;
    animation: cobrarGradientShift 6s ease infinite;
    padding: 1.75rem 1.5rem 2.25rem;
    position: relative;
    overflow: hidden;
}
.cobrar-premium .cobrar-header::before {
    content: '';
    position: absolute;
    top: -40%;
    right: -15%;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
    animation: cobrarPulse 4s ease-in-out infinite;
}
.cobrar-premium .cobrar-header::after {
    content: '';
    position: absolute;
    bottom: -50%;
    left: -10%;
    width: 160px;
    height: 160px;
    background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
}
.cobrar-premium .cobrar-body {
    background: rgba(248,250,252,0.85);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}
.cobrar-premium .cobrar-total-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.6));
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.5);
    border-radius: 1rem;
    padding: 1.25rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.cobrar-premium .cobrar-total-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 60%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: cobrarShine 4s ease-in-out infinite;
}
.cobrar-premium .metodo-btn {
    border-radius: 1rem;
    padding: 0.75rem 0.5rem;
    font-weight: 700;
    font-size: 0.88rem;
    border: 2px solid transparent;
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
}
.cobrar-premium .metodo-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}
.cobrar-premium .metodo-btn:active {
    transform: translateY(0) scale(0.97);
}
.cobrar-premium .metodo-btn.active-metodo {
    border-color: currentColor;
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    transform: translateY(-1px);
}
.cobrar-premium .metodo-btn.efectivo { background: rgba(16,185,129,0.08); color: #059669; }
.cobrar-premium .metodo-btn.efectivo.active-metodo { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
.cobrar-premium .metodo-btn.tarjeta { background: rgba(59,130,246,0.08); color: #2563eb; }
.cobrar-premium .metodo-btn.tarjeta.active-metodo { background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff; }
.cobrar-premium .metodo-btn.transferencia { background: rgba(6,182,212,0.08); color: #0891b2; }
.cobrar-premium .metodo-btn.transferencia.active-metodo { background: linear-gradient(135deg, #06b6d4, #0891b2); color: #fff; }
.cobrar-premium .metodo-btn.mixto { background: rgba(245,158,11,0.08); color: #d97706; }
.cobrar-premium .metodo-btn.mixto.active-metodo { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }
.cobrar-premium .input-premium {
    border: 2px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 0.85rem 1rem;
    font-size: 1.15rem;
    font-weight: 700;
    text-align: center;
    transition: all 0.25s ease;
    background: rgba(255,255,255,0.8);
}
.cobrar-premium .input-premium:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 4px rgba(16,185,129,0.1), 0 4px 12px rgba(16,185,129,0.06);
    background: #fff;
    outline: none;
}
.cobrar-premium .input-prefix {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    border-radius: 0.75rem 0 0 0.75rem;
    padding: 0.85rem 1rem;
    font-weight: 700;
    font-size: 1rem;
    display: flex;
    align-items: center;
}
.cobrar-premium .cambio-card {
    background: linear-gradient(135deg, rgba(16,185,129,0.08), rgba(6,182,212,0.06));
    border: 1px solid rgba(16,185,129,0.2);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
}
.cobrar-premium .propina-btn {
    border-radius: 2rem;
    padding: 0.4rem 0.75rem;
    font-weight: 700;
    font-size: 0.78rem;
    border: 1.5px solid rgba(16,185,129,0.25);
    background: rgba(255,255,255,0.7);
    color: #059669;
    transition: all 0.2s ease;
}
.cobrar-premium .propina-btn:hover {
    background: rgba(16,185,129,0.1);
    border-color: rgba(16,185,129,0.5);
    transform: scale(1.05);
}
.cobrar-premium .split-btn {
    border-radius: 2rem;
    width: 38px;
    height: 38px;
    font-weight: 700;
    font-size: 0.85rem;
    border: 1.5px solid rgba(99,102,241,0.2);
    background: rgba(255,255,255,0.7);
    color: #6366f1;
    transition: all 0.2s ease;
}
.cobrar-premium .split-btn:hover {
    background: rgba(99,102,241,0.1);
    border-color: rgba(99,102,241,0.4);
    transform: scale(1.08);
}
.cobrar-premium .btn-cobrar-premium {
    background: linear-gradient(135deg, #059669, #10b981);
    border: none;
    border-radius: 2rem;
    padding: 0.85rem 2.5rem;
    font-weight: 700;
    font-size: 1.05rem;
    color: white;
    transition: all 0.25s ease;
    box-shadow: 0 4px 16px rgba(16,185,129,0.3);
    position: relative;
    overflow: hidden;
}
.cobrar-premium .btn-cobrar-premium:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(16,185,129,0.4);
    background: linear-gradient(135deg, #047857, #059669);
}
.cobrar-premium .btn-cobrar-premium:active {
    transform: translateY(0);
}
.cobrar-premium .btn-cancelar-premium {
    background: rgba(255,255,255,0.8);
    border: 1.5px solid #e2e8f0;
    border-radius: 2rem;
    padding: 0.85rem 2rem;
    font-weight: 700;
    font-size: 0.95rem;
    color: #64748b;
    transition: all 0.2s ease;
}
.cobrar-premium .btn-cancelar-premium:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}
.cobrar-premium .servicio-check {
    background: rgba(16,185,129,0.06);
    border: 1px solid rgba(16,185,129,0.15);
    border-radius: 0.75rem;
    padding: 0.6rem 0.75rem;
}

.restaurant-pos .mesa-btn {
    transition: all .25s ease, border-width 0s;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    touch-action: manipulation;
}
.restaurant-pos .mesa-btn:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.12) !important;
    z-index: 2;
}
.restaurant-pos .mesa-btn:active { transform: translateY(0) scale(0.98); }
.restaurant-pos .mesa-btn.mesa-libre {
    border-left: 5px solid #198754 !important;
    background: linear-gradient(135deg, #fff 0%, #f0fdf4 100%) !important;
}
.restaurant-pos .mesa-btn.mesa-ocupada {
    border-left: 5px solid #f59e0b !important;
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%) !important;
}
.restaurant-pos .mesa-btn.mesa-reservada {
    border-left: 5px solid #06b6d4 !important;
    background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%) !important;
    box-shadow: 0 0 0 2px rgba(6,182,212,.15), 0 .125rem .25rem rgba(0,0,0,.05) !important;
    animation: reserva-pulse 2s ease-in-out infinite;
}
@keyframes reserva-pulse {
    0%, 100% { box-shadow: 0 0 0 2px rgba(6,182,212,.15), 0 .125rem .25rem rgba(0,0,0,.05); }
    50% { box-shadow: 0 0 0 4px rgba(6,182,212,.25), 0 .125rem .5rem rgba(0,0,0,.08); }
}
.restaurant-pos .mesa-btn.mesa-inactiva {
    border-left: 5px solid #6c757d !important;
    opacity: .55;
}
.restaurant-pos .mesa-btn .estado-icon {
    width: 10px; height: 10px; border-radius: 50%;
    display: inline-block; flex-shrink: 0;
}
.restaurant-pos .mesa-btn .estado-icon.verde { background: #198754; box-shadow: 0 0 0 2px rgba(25,135,84,.2); }
.restaurant-pos .mesa-btn .estado-icon.amarillo { background: #f59e0b; box-shadow: 0 0 0 2px rgba(245,158,11,.2); }
.restaurant-pos .mesa-btn .estado-icon.azul { background: #06b6d4; box-shadow: 0 0 0 2px rgba(6,182,212,.2); }
.restaurant-pos .mesa-btn .estado-icon.gris { background: #6c757d; box-shadow: 0 0 0 2px rgba(108,117,125,.2); }

.restaurant-pos .reserva-ribbon {
    position: absolute; top: 10px; right: 10px;
    background: #06b6d4; color: #fff;
    font-size: .6rem; font-weight: 700;
    padding: 2px 8px; border-radius: 999px;
    box-shadow: 0 2px 6px rgba(6,182,212,.3);
    z-index: 2;
    display: flex; align-items: center; gap: 3px;
}
.restaurant-pos .reserva-timer {
    font-size: .6rem; font-weight: 600;
    display: inline-flex; align-items: center; gap: 3px;
}
.restaurant-pos .reserva-timer.pon-vencida { color: #dc2626; }
.restaurant-pos .reserva-timer.pon-proximo { color: #d97706; }

.orden-items .item-qty { min-width: 28px; text-align: center; }
.cobrar-premium .metodo-btn.active-metodo { transform: scale(1.05); box-shadow: 0 .15rem .3rem rgba(0,0,0,.15); }
#caja-status-bar .caja-activa { background: linear-gradient(135deg, #059669, #10b981); }
#caja-status-bar .caja-inactiva { background: linear-gradient(135deg, #dc2626, #ef4444); cursor: pointer; }
#caja-status-bar .caja-inactiva:hover { transform: translateY(-1px); }
.producto-item:hover { background-color: #f8f9fa; }
.producto-item:active { background-color: #e9ecef; }

@media (max-width: 991.98px) {
    .restaurant-pos { flex-direction: column !important; height: auto !important; }
    .restaurant-pos .mesas-panel { width: 100% !important; min-width: unset !important; max-height: 50vh; }
    .restaurant-pos .orden-panel { min-height: 50vh; }
}

.tecla {
    flex: 1;
    height: 38px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    background: #fff;
    font-size: .78rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    touch-action: manipulation;
    user-select: none;
    -webkit-user-select: none;
    transition: background .08s, transform .08s;
    padding: 0 3px;
    min-width: 0;
}
.tecla:active {
    background: #dbeafe;
    transform: scale(0.93);
    box-shadow: 0 0 0 2px rgba(59,130,246,.2);
}
.tecla-func {
    background: #f1f5f9;
    font-size: .72rem;
}
.tecla-shift {
    flex: 1.6;
}
.tecla-shift.active {
    background: #bfdbfe;
    box-shadow: inset 0 2px 4px rgba(0,0,0,.15);
    border-color: #93c5fd;
}
.tecla-backspace {
    flex: 1.3;
}
.tecla-space {
    flex: 4;
}
.tecla-enter {
    flex: 1.3;
}
.tecla-punct {
    flex: 1;
}
.tecla-func:active {
    background: #bfdbfe;
}
.tecla-func.active {
    background: #bfdbfe;
    box-shadow: inset 0 2px 4px rgba(0,0,0,.15);
    border-color: #93c5fd;
}
.tecla-row {
    display: flex;
    gap: 4px;
    justify-content: center;
    margin-bottom: 4px;
}
#teclado-rows {
    max-width: 100%;
}
#teclado-rows::-webkit-scrollbar {
    height: 0;
}

@media (max-width: 576px) {
    .tecla {
        height: 34px;
        font-size: .7rem;
        border-radius: 6px;
    }
}

/* Plato card con imagen y cantidad */
.prod-card {
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: transform .1s, box-shadow .1s;
    user-select: none;
    -webkit-user-select: none;
    touch-action: manipulation;
}
.prod-card:active {
    transform: scale(0.97);
    box-shadow: 0 .1rem .2rem rgba(0,0,0,.1) !important;
}
.prod-card .prod-nombre {
    font-size: .8rem;
    line-height: 1.2;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.prod-card .prod-precio {
    font-size: .9rem;
}
.prod-card-img {
    width: 100%;
    height: 80px;
    object-fit: cover;
    background: #f8fafc;
}
.prod-card-img-placeholder {
    width: 100%;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.prod-card-initial {
    font-size: 1.8rem;
    font-weight: 700;
    line-height: 1;
}
.prod-card .card-body {
    padding: 6px 8px 4px;
}
.prod-qty {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2px;
    margin-top: 2px;
}
.prod-qty button {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 1px solid #dee2e6;
    background: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    cursor: pointer;
    touch-action: manipulation;
    user-select: none;
    transition: background .1s, transform .1s;
    line-height: 1;
    padding: 0;
}
.prod-qty button:active {
    background: #dbeafe;
    transform: scale(0.88);
    box-shadow: 0 0 0 3px rgba(59,130,246,.2);
}
.prod-qty span {
    min-width: 28px;
    text-align: center;
    font-weight: 700;
    font-size: 1rem;
}
.prod-qty {
    gap: 4px;
    margin-top: 4px;
}
.prod-card-stock-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    font-size: .55rem;
    padding: 1px 6px;
    border-radius: 999px;
    z-index: 2;
}
.prod-card .card-body {
    position: relative;
}

.mesa-selected {
    box-shadow: 0 0 0 3px #0d6efd, 0 .5rem 1rem rgba(13,110,253,.2) !important;
    transform: translateY(-2px) scale(1.02);
}

#modal-categoria-filtro {
    max-width: 160px;
    font-size: .8rem;
}

/* Orden item quantity controls */
.orden-qty {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.orden-qty button {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 1px solid #dee2e6;
    background: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    cursor: pointer;
    touch-action: manipulation;
    user-select: none;
    transition: background .1s, transform .1s;
    padding: 0;
    line-height: 1;
}
.orden-qty button:active {
    background: #dbeafe;
    transform: scale(0.88);
    box-shadow: 0 0 0 3px rgba(59,130,246,.2);
}
.orden-qty span {
    min-width: 28px;
    text-align: center;
    font-weight: 700;
    font-size: 1rem;
}
</style>

<script>
let mesaActual = null;
let ordenActual = null;
const restauranteValidaStock = {{ $restauranteValidaStock ? 'true' : 'false' }};
let sesionCajaActiva = null;
let postPagoData = null;
let totalPagoFinal = 0;
let productosData = [];
let categoriasData = [];

// Cargar catálogo completo al inicio (filtrado del lado del cliente)
document.addEventListener('DOMContentLoaded', function () {
    fetch('{{ route("restaurante.catalogo") }}')
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(data => {
            productosData = data.productos || data;
            categoriasData = data.categorias || [];
            renderizarFiltroCategorias();
        })
        .catch(err => console.error('Error cargando catálogo:', err));
});

function renderizarFiltroCategorias() {
    const container = document.getElementById('categoria-filtro');
    if (!container || categoriasData.length === 0) return;
    let html = '<option value="">Todas las categorías</option>';
    categoriasData.forEach(c => {
        html += `<option value="${c.id}">${escapeHtml(c.nombre)}</option>`;
    });
    container.innerHTML = html;
}

// Inicializar caja status
document.addEventListener('DOMContentLoaded', renderCajaStatus);

function renderCajaStatus() {
    fetch('{{ route("restaurante.sesion-activa") }}')
        .then(r => {
            if (!r.ok) throw new Error('Error al obtener estado de caja');
            return r.json();
        })
        .then(data => {
            sesionCajaActiva = data.sesion;
            const bar = document.getElementById('caja-status-bar');
            if (data.sesion) {
                bar.innerHTML = `
                    <div class="caja-activa text-white rounded-3 p-2 d-flex justify-content-between align-items-center shadow-sm">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-cash-stack"></i>
                            <span class="fw-bold small">${data.sesion.caja.nombre}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-white text-success">Caja Activa</span>
                            <small class="ms-2 opacity-75">RD$ ${Number(data.sesion.monto_inicial).toFixed(0)}</small>
@can('cajas.cerrar')
                                <button class="btn btn-sm btn-light rounded-pill text-danger fw-bold ms-2" onclick="mostrarCerrarCaja(${data.sesion.caja_id})" title="Cerrar Caja">
                                    <i class="bi bi-x-circle me-1"></i> Cerrar
                                </button>
@endcan


                        </div>
                    </div>
                `;
            } else {
                bar.innerHTML = `
                    <div class="caja-inactiva text-white rounded-3 p-2 d-flex justify-content-between align-items-center shadow-sm" onclick="mostrarAbrirCaja()">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-x-circle"></i>
                            <span class="fw-bold small">Sin caja activa</span>
                        </div>
                        <span class="badge bg-white text-danger">Abrir Caja →</span>
                    </div>
                `;
            }
        });
}

function mostrarAbrirCaja() {
    fetch('{{ route("restaurante.cajas") }}')
        .then(r => {
            if (!r.ok) throw new Error('Error al cargar cajas');
            return r.json();
        })
        .then(data => {
            const select = document.getElementById('caja-select');
            select.innerHTML = data.cajas.map(c =>
                `<option value="${c.id}">${c.nombre} (${c.codigo || 'Sin código'}) ${c.estado === 'abierta' ? '🔴' : ''}</option>`
            ).join('');
        });
    new bootstrap.Modal(document.getElementById('abrirCajaModal')).show();
}

function abrirCaja() {
    const cajaId = document.getElementById('caja-select').value;
    const monto = document.getElementById('caja-monto-inicial').value;

    fetch('{{ route("restaurante.abrir-caja") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ caja_id: cajaId, monto_inicial: monto })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { Swal.fire({icon:'error', title:'Error', text: data.error}); return; }
        bootstrap.Modal.getInstance(document.getElementById('abrirCajaModal')).hide();
        renderCajaStatus();
    });
}

function crearCaja() {
    const nombre = document.getElementById('nueva-caja-nombre').value.trim();
    if (!nombre) { Swal.fire({icon:'warning', title:'Campo requerido', text:'Ingresa un nombre para la caja'}); return; }

    fetch('{{ route("restaurante.crear-caja") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ nombre: nombre })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); return; }
        document.getElementById('nueva-caja-nombre').value = '';
        mostrarAbrirCaja();
    });
}

let cerrarCajaId = null;

function mostrarCerrarCaja(cajaId) {
    cerrarCajaId = cajaId;
    fetch(`{{ url("restaurante/caja/resumen") }}?caja_id=${cajaId}`)
        .then(r => r.json())
        .then(data => {
            if (data.error) { alert(data.error); return; }
            document.getElementById('cerrar-caja-resumen').innerHTML = `
                <div class="bg-light rounded-3 p-2">
                    <div class="d-flex justify-content-between"><span>Ventas:</span><span class="fw-bold">${data.total_ventas}</span></div>
                    <div class="d-flex justify-content-between"><span>Efectivo:</span><span class="fw-bold">RD$ ${Number(data.efectivo).toFixed(2)}</span></div>
                    <div class="d-flex justify-content-between"><span>Tarjeta:</span><span class="fw-bold">RD$ ${Number(data.tarjeta).toFixed(2)}</span></div>
                    <div class="d-flex justify-content-between"><span>Transferencia:</span><span class="fw-bold">RD$ ${Number(data.transferencia).toFixed(2)}</span></div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between"><span>Total cobrado:</span><span class="fw-bold text-primary">RD$ ${Number(data.total).toFixed(2)}</span></div>
                </div>
            `;
            document.getElementById('cierre-cobros-efectivo').value = data.efectivo.toFixed(2);
            document.getElementById('cierre-cobros-tarjeta').value = data.tarjeta.toFixed(2);
            document.getElementById('cierre-cobros-transferencia').value = data.transferencia.toFixed(2);
        });
    new bootstrap.Modal(document.getElementById('cerrarCajaModal')).show();
}

function cerrarCaja() {
    const montoDeclarado = document.getElementById('cierre-monto-declarado').value;
    const efectivo = document.getElementById('cierre-cobros-efectivo').value;
    const tarjeta = document.getElementById('cierre-cobros-tarjeta').value;
    const transferencia = document.getElementById('cierre-cobros-transferencia').value;
    const notas = document.getElementById('cierre-notas').value.trim();

    if (!montoDeclarado || parseFloat(montoDeclarado) < 0) {
        alert('Ingresa el monto declarado');
        return;
    }

    fetch('{{ route("restaurante.caja.cerrar") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({
            caja_id: cerrarCajaId,
            monto_declarado: montoDeclarado,
            cobros_efectivo: efectivo,
            cobros_tarjeta: tarjeta,
            cobros_transferencia: transferencia,
            notas: notas
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); return; }
        bootstrap.Modal.getInstance(document.getElementById('cerrarCajaModal')).hide();
        renderCajaStatus();
        Swal.fire({
            icon: data.descuadre === 0 ? 'success' : 'warning',
            title: 'Caja Cerrada',
            text: data.message,
        });
    });
}

let quickMenuVisible = true;
function toggleQuickMenu() {
    quickMenuVisible = !quickMenuVisible;
    document.getElementById('quick-menu-items').style.display = quickMenuVisible ? '' : 'none';
    document.getElementById('quick-menu-toggle-icon').className = quickMenuVisible ? 'bi bi-chevron-up' : 'bi bi-chevron-down';
}

function cargarPopulares() {
    fetch('/restaurante/productos/populares')
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('quick-menu-items');
            if (!data || data.length === 0) {
                container.innerHTML = '<small class="text-muted">Sin platos populares</small>';
                return;
            }
            container.innerHTML = data.map(p => `
                <button class="btn btn-sm btn-outline-danger rounded-pill d-inline-flex align-items-center gap-1 flex-shrink-0"
                    onpointerdown="agregarProductoQuick(${p.id}, '${escapeHtml(p.nombre)}', ${p.precio}, ${p.stock})"
                    title="${escapeHtml(p.nombre)} - RD$ ${p.precio.toFixed(2)} · Stock: ${p.stock}">
                    <span class="badge bg-danger rounded-circle p-1" style="width:18px;height:18px;font-size:10px;">${p.iniciales}</span>
                    <span class="small">${escapeHtml(p.nombre)}</span>
                    <small class="text-muted">RD$${p.precio.toFixed(0)}</small>
                </button>
            `).join('');
        });
}

function agregarProductoQuick(productoId, nombre, precio, stock) {
    if (!ordenActual) return;
    if (restauranteValidaStock) {
        if (stock !== undefined && stock <= 0) {
            Swal.fire({icon:'error', title:'Sin stock', text:'Este plato no tiene disponible'});
            return;
        }
        if (stock !== undefined && stock <= 3) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock bajo',
                text: `Solo quedan ${stock} unidades de "${nombre}". ¿Agregar de todas formas?`,
                showCancelButton: true,
                confirmButtonText: 'Sí, agregar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    enviarAgregarProductoQuick(productoId);
                }
            });
            return;
        }
    }
    enviarAgregarProductoQuick(productoId);
}

function enviarAgregarProductoQuick(productoId) {
    const curso = 'fuerte';
    const notas = '';
    fetch(`/restaurante/mesa/${mesaActual}/agregar`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ producto_id: productoId, cantidad: 1, curso: curso, notas: notas })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); return; }
        ordenActual = data.orden;
        renderOrden(data.orden);
    });
}

function seleccionarMesa(btn) {
    const mesaId = btn.dataset.mesaId;
    if (!mesaId) return;
    mesaActual = mesaId;
    cargarMesa(mesaId);
}

function cargarMesa(mesaId) {
    fetch(`/restaurante/mesa/${mesaId}`)
        .then(r => {
            if (!r.ok) return r.text().then(t => { throw new Error(t.includes('<!DOCTYPE') ? 'Error del servidor (página no encontrada)' : t.substring(0,200)); });
            return r.json();
        })
        .then(data => {
            const mesa = data.mesa;
            const orden = data.orden;

            document.querySelectorAll('.mesa-btn').forEach(b => b.classList.remove('mesa-selected'));
            document.querySelector(`.mesa-btn[data-mesa-id="${mesaId}"]`)?.classList.add('mesa-selected');

            document.getElementById('orden-titulo').textContent = mesa.nombre || 'Mesa ' + mesa.numero;
            const tipoBadge = orden && orden.tipo_orden && orden.tipo_orden !== 'mesa'
                ? ` <span class="badge bg-info rounded-pill">${orden.tipo_orden.replace('_', ' ')}</span>`
                : '';
            let deliveryBadge = '';
            if (orden && orden.delivery_company_id) {
                const dc = deliveryCompanies.find(d => d.id === orden.delivery_company_id);
                if (dc) deliveryBadge = ` <span class="badge bg-dark rounded-pill"><i class="bi bi-truck me-1"></i>${dc.nombre}</span>`;
            }
            document.getElementById('orden-subtitulo').innerHTML = '# Cap. ' + mesa.capacidad + ' · ' + (mesa.ubicacion || '') + tipoBadge + deliveryBadge;
            
            if (!orden && mesa.estado === 'reservada') {
                const diffMs = data.reservacion ? new Date(data.reservacion.fecha_hora + 'Z') - new Date() : 0;
                const atrasada = diffMs < 0;
                document.getElementById('orden-subtitulo').innerHTML +=
                    atrasada
                        ? ' <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Atrasada</span>'
                        : ' <span class="badge bg-warning text-dark">Reserva Pendiente</span>';
            }
            
            document.getElementById('orden-actions').classList.remove('d-none');

            const searchBar = document.getElementById('productos-search-bar');
            const clienteSelector = document.getElementById('cliente-selector');
            if (orden) {
                ordenActual = orden;
                renderOrden(orden);
                document.getElementById('orden-footer').classList.remove('d-none');
                searchBar.classList.remove('d-none');
                clienteSelector.classList.remove('d-none');
                document.getElementById('cliente-nombre').textContent = orden.cliente?.nombre || 'Consumidor Final';
                document.getElementById('buscar-producto').value = '';
                    document.getElementById('quick-menu').classList.remove('d-none');
                    cargarPopulares();
            } else if (mesa.estado === 'reservada' && data.reservacion) {
                ordenActual = null;
                searchBar.classList.add('d-none');
                clienteSelector.classList.add('d-none');
                document.getElementById('orden-footer').classList.add('d-none');
                const r = data.reservacion;
                const reservaDate = r.fecha_hora ? new Date(r.fecha_hora + 'Z') : null;
                const now = new Date();
                const diffMs = reservaDate ? reservaDate - now : 0;
                const diffMin = Math.round(diffMs / 60000);
                const atrasada = diffMs < 0;
                const fechaHora = reservaDate ? reservaDate.toLocaleString('es-DO', {day:'2-digit', month:'2-digit', hour:'2-digit', minute:'2-digit'}) : '—';
                const timerHtml = atrasada
                    ? `<div class="alert alert-danger py-1 px-2 small fw-bold rounded-3 mt-2"><i class="bi bi-exclamation-triangle-fill me-1"></i>Reserva atrasada ${Math.abs(diffMin)} min</div>`
                    : diffMin <= 30
                        ? `<div class="alert alert-warning py-1 px-2 small fw-bold rounded-3 mt-2"><i class="bi bi-hourglass-split me-1"></i>Próxima reserva — Dentro de ${diffMin} min</div>`
                        : '';
                document.getElementById('orden-items').innerHTML = `
                    <div class="text-center mt-3">
                        <div class="bg-info bg-opacity-10 rounded-4 p-4 mx-2">
                            <i class="bi bi-bookmark-check-fill fs-1 d-block mb-2 text-info"></i>
                            <h6 class="fw-bold mb-3">Reserva</h6>
                            <div class="mb-2">
                                <span class="fw-bold fs-5">${escapeHtml(r.cliente_nombre)}</span>
                            </div>
                            <div class="d-flex justify-content-center gap-3 small text-muted mb-2 flex-wrap">
                                <span class="bg-white rounded-3 px-3 py-1 shadow-sm"><i class="bi bi-people me-1"></i>${r.personas} pers</span>
                                <span class="bg-white rounded-3 px-3 py-1 shadow-sm"><i class="bi bi-clock me-1"></i>${fechaHora}</span>
                            </div>
                            ${r.cliente_telefono ? `<div class="small mb-1"><i class="bi bi-telephone me-1 text-muted"></i>${escapeHtml(r.cliente_telefono)}</div>` : ''}
                            ${r.notas ? `<div class="alert alert-light border mt-2 small py-1 px-2 rounded-3">📝 ${escapeHtml(r.notas)}</div>` : ''}
                            ${timerHtml}
                        </div>
                        <div class="d-flex gap-2 justify-content-center mt-3 px-3">
                            ${sesionCajaActiva
                                ? `<button class="btn btn-success rounded-pill flex-fill py-2 fw-bold" onclick="confirmarReserva(${mesaId}, ${r.id})"><i class="bi bi-play-circle-fill me-1"></i> Ocupar ahora</button>`
                                : `<button class="btn btn-danger rounded-pill flex-fill py-2 fw-bold" onclick="mostrarAbrirCaja()"><i class="bi bi-cash-stack me-1"></i> Abrir Caja primero</button>`
                            }
                            <button class="btn btn-outline-danger rounded-pill" onclick="liberarMesa(${mesaId})">
                                <i class="bi bi-x-circle me-1"></i> Liberar
                            </button>
                        </div>
                    </div>
                `;
            } else {
                ordenActual = null;
                searchBar.classList.add('d-none');
                clienteSelector.classList.add('d-none');
                if (sesionCajaActiva) {
                    document.getElementById('orden-items').innerHTML = `
                        <div class="text-center text-muted mt-5">
                            <i class="bi bi-cup-straw fs-1 d-block mb-2"></i>
                            <p>Mesa vacía</p>
                            <button class="btn btn-primary rounded-pill btn-lg mt-2" onclick="mostrarAbrirMesa(${mesaId})">
                                <i class="bi bi-plus-circle me-1"></i> Abrir Mesa
                            </button>
                        </div>
                    `;
                } else {
                    document.getElementById('orden-items').innerHTML = `
                        <div class="text-center mt-5">
                            <div class="bg-danger bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                                <i class="bi bi-cash-stack text-danger fs-2"></i>
                            </div>
                            <p class="fw-bold text-danger mb-1">Sin caja activa</p>
                            <small class="text-muted d-block mb-3">Debes abrir una caja antes de poder abrir mesas.</small>
                            <button class="btn btn-danger rounded-pill fw-bold" onclick="mostrarAbrirCaja()">
                                <i class="bi bi-plus-circle me-1"></i> Abrir Caja
                            </button>
                        </div>
                    `;
                }
                document.getElementById('orden-footer').classList.add('d-none');
            }
        })
        .catch(err => {
            Swal.fire({icon:'error', title:'Error', text:'No se pudo cargar la mesa: ' + err.message});
        });
}

let clienteActualId = null;

function mostrarBuscarClienteAbrir(mesaId) {
    document.getElementById('buscar-cliente').value = '';
    document.getElementById('clientes-resultados').innerHTML = '<div class="text-muted small text-center py-2">Escribe para buscar...</div>';
    const modal = new bootstrap.Modal(document.getElementById('clienteModal'));
    modal.show();
    document.getElementById('clienteModal').dataset.mesaId = mesaId;
    document.getElementById('buscar-cliente').focus();
}

document.getElementById('buscar-cliente').addEventListener('input', function () {
    const q = this.value.trim();
    const container = document.getElementById('clientes-resultados');
    if (q.length < 2) {
        container.innerHTML = '<div class="text-muted small text-center py-2">Escribe al menos 2 caracteres...</div>';
        return;
    }
    fetch(`/clientes/search?q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(data => {
            if (!data || data.length === 0) {
                container.innerHTML = '<div class="text-muted small text-center py-2">Sin resultados</div>';
                return;
            }
            container.innerHTML = data.map(c =>
                `<div class="list-group-item list-group-item-action px-3 py-2 border rounded-3 mb-1" style="cursor:pointer;" onclick="seleccionarCliente(${c.id}, '${escapeHtml(c.nombre)}')">
                    <div class="fw-semibold small">${escapeHtml(c.nombre)}</div>
                    <small class="text-muted">${c.rnc || c.rnc_cedula || '—'}</small>
                </div>`
            ).join('');
        });
    });

function seleccionarCliente(id, nombre) {
    const mesaId = document.getElementById('clienteModal').dataset.mesaId;
    bootstrap.Modal.getInstance(document.getElementById('clienteModal')).hide();
    document.getElementById('cliente-nombre').textContent = nombre;
    clienteActualId = id;
    if (mesaId) {
        abrirMesa(parseInt(mesaId), id);
    }
}

function mostrarBuscarCliente() {
    document.getElementById('buscar-cliente').value = '';
    document.getElementById('clientes-resultados').innerHTML = '<div class="text-muted small text-center py-2">Escribe para buscar...</div>';
    delete document.getElementById('clienteModal').dataset.mesaId;
    new bootstrap.Modal(document.getElementById('clienteModal')).show();
    document.getElementById('buscar-cliente').focus();
}

let mesaAbrirId = null;
let ordenTipoSeleccionado = 'mesa';
let deliveryCompanies = [];
let servicioPorcentaje = {{ $servicioPorcentaje ?? 0 }};
let servicioMinPersonas = {{ $servicioMinPersonas ?? 8 }};

function mostrarAbrirMesa(mesaId) {
    mesaAbrirId = mesaId;
    ordenTipoSeleccionado = 'mesa';
    document.getElementById('delivery-company-group').style.display = 'none';
    document.querySelectorAll('.tipo-orden-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.tipo-orden-btn[data-value="mesa"]').classList.add('active');
    if (deliveryCompanies.length === 0) {
        fetch('/delivery-companies/listar-activas')
            .then(r => r.json())
            .then(data => {
                deliveryCompanies = data;
                const sel = document.getElementById('delivery-company-select');
                sel.innerHTML = '<option value="">Seleccionar...</option>'
                    + data.map(d => `<option value="${d.id}" data-comision="${d.comision_porcentaje}">${d.nombre}</option>`).join('');
            }).catch(() => {});
    }
    new bootstrap.Modal(document.getElementById('tipoOrdenModal')).show();
}

function seleccionarTipoOrden(tipo) {
    ordenTipoSeleccionado = tipo;
    document.querySelectorAll('.tipo-orden-btn').forEach(b => b.classList.remove('active'));
    document.querySelector(`.tipo-orden-btn[data-value="${tipo}"]`).classList.add('active');
    document.getElementById('delivery-company-group').style.display = tipo === 'delivery' ? 'block' : 'none';
}

function confirmarAbrirMesa() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('tipoOrdenModal'));
    modal.hide();
    const deliveryCompanyId = ordenTipoSeleccionado === 'delivery'
        ? document.getElementById('delivery-company-select').value
        : null;
    if (ordenTipoSeleccionado === 'delivery' && !deliveryCompanyId) {
        Swal.fire({icon:'error', title:'Selecciona empresa', text:'Debes elegir una empresa de delivery'});
        return;
    }
    abrirMesa(mesaAbrirId, null, ordenTipoSeleccionado, deliveryCompanyId);
}

function abrirMesa(mesaId, clienteId, tipoOrden, deliveryCompanyId) {
    const payload = {};
    if (clienteId) payload.cliente_id = clienteId;
    if (tipoOrden) payload.tipo_orden = tipoOrden;
    if (deliveryCompanyId) payload.delivery_company_id = deliveryCompanyId;
    fetch(`/restaurante/mesa/${mesaId}/abrir`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => {
        if (!r.ok) return r.json().then(d => { throw new Error(d.error || 'Error del servidor'); });
        return r.json();
    })
    .then(data => {
        if (data.error) { Swal.fire({icon:'error', title:'No se pudo abrir', text: data.error}); return; }
        cargarMesa(mesaId);
        actualizarGridMesa(mesaId, 'ocupada');
    })
    .catch(err => {
        Swal.fire({icon:'error', title:'Error', text: err.message || 'Error de conexión'});
    });
}

function confirmarReserva(mesaId, reservacionId) {
    mostrarAbrirMesa(mesaId);
    document.getElementById('tipoOrdenModal').dataset.reservacionId = reservacionId;
    document.querySelector('#tipoOrdenModal .btn-primary').onclick = function() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('tipoOrdenModal'));
        modal.hide();
        const deliveryCompanyId = ordenTipoSeleccionado === 'delivery'
            ? document.getElementById('delivery-company-select').value
            : null;
        if (ordenTipoSeleccionado === 'delivery' && !deliveryCompanyId) {
            Swal.fire({icon:'error', title:'Selecciona empresa', text:'Debes elegir una empresa de delivery'});
            return;
        }
        fetch(`/restaurante/mesa/${mesaId}/abrir`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({
                tipo_orden: ordenTipoSeleccionado,
                delivery_company_id: deliveryCompanyId || null
            })
        })
        .then(r => {
            if (!r.ok) return r.json().then(d => { throw new Error(d.error || 'Error del servidor'); });
            return r.json();
        })
        .then(data => {
            if (data.error) { Swal.fire({icon:'error', title:'No se pudo abrir', text: data.error}); return; }
            fetch(`/restaurante/reservaciones/${reservacionId}/estado`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ estado: 'cumplida' })
            }).catch(() => {});
            cargarMesa(mesaId);
            actualizarGridMesa(mesaId, 'ocupada');
            document.querySelector('#tipoOrdenModal .btn-primary').onclick = confirmarAbrirMesa;
        })
        .catch(err => {
            Swal.fire({icon:'error', title:'Error', text: err.message || 'Error de conexión'});
            document.querySelector('#tipoOrdenModal .btn-primary').onclick = confirmarAbrirMesa;
        });
    };
}

function liberarMesa(mesaId) {
    if (!confirm('¿Liberar esta mesa? La reservación será cancelada.')) return;
    fetch(`/restaurante/mesa/${mesaId}/estado`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ estado: 'disponible' })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { Swal.fire({icon:'error', title:'Error', text: data.error}); return; }
        cargarMesa(mesaId);
        actualizarGridMesa(mesaId, 'disponible');
    });
}

function renderOrden(orden) {
    let html = '';
    if (orden.detalles && orden.detalles.length > 0) {
        orden.detalles.forEach(d => {
            const nombre = d.producto ? d.producto.nombre : 'Plato #' + d.producto_id;
            const notasHtml = d.notas ? `<div class="small text-muted fst-italic mt-1" style="font-size:.65rem;"><i class="bi bi-chat-text me-1"></i>${escapeHtml(d.notas)}</div>` : '';
            const cursoLabel = d.curso && d.curso !== 'fuerte' ? ` <span class="badge bg-secondary bg-opacity-25 text-dark rounded-pill" style="font-size:.6rem;">${d.curso}</span>` : '';
            const stock = d.producto ? d.producto.stock : 999;
            html += `
            <div class="d-flex justify-content-between align-items-center p-2 rounded-3 mb-1 bg-light bg-opacity-50">
                <div class="d-flex align-items-center gap-1 flex-grow-1" style="min-width:0;">
                    <div class="orden-qty">
                        <button onclick="cambiarCantidadItem(${d.id}, -1, ${stock})" type="button">−</button>
                        <span id="orden-qty-${d.id}">${d.cantidad}</span>
                        <button onclick="cambiarCantidadItem(${d.id}, 1, ${stock})" type="button">+</button>
                    </div>
                    <div class="ms-1" style="min-width:0;">
                        <div class="fw-semibold small text-truncate">${nombre}${cursoLabel}</div>
                        <small class="text-muted">RD$ ${Number(d.precio_unitario).toFixed(2)} c/u</small>
                        ${notasHtml}
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                    <span class="fw-bold small">RD$ ${Number(d.subtotal).toFixed(2)}</span>
                    <button class="btn btn-sm btn-light rounded-pill" onclick="quitarItem(${d.id})" title="Quitar" style="width:38px;height:38px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                        <i class="bi bi-x text-danger fs-5"></i>
                    </button>
                </div>
            </div>`;
        });
    } else {
        html = '<p class="text-muted text-center py-4">Sin platos aún</p>';
    }
    document.getElementById('orden-items').innerHTML = html;
    document.getElementById('orden-subtotal').textContent = 'RD$ ' + Number(orden.subtotal).toFixed(2);
    document.getElementById('orden-itbis').textContent = 'RD$ ' + Number(orden.impuestos).toFixed(2);
    document.getElementById('orden-total').textContent = 'RD$ ' + Number(orden.total).toFixed(2);

    const descLabel = document.getElementById('orden-descuento');
    if (orden.descuento && orden.descuento > 0) {
        descLabel.textContent = '- RD$ ' + Number(orden.descuento).toFixed(2);
        descLabel.classList.remove('d-none');
    } else {
        descLabel.classList.add('d-none');
    }

    const deliveryFeeLabel = document.getElementById('orden-delivery-fee');
    if (orden.delivery_fee && orden.delivery_fee > 0) {
        const dc = deliveryCompanies.find(d => d.id === orden.delivery_company_id);
        const name = dc ? dc.nombre : 'Delivery';
        deliveryFeeLabel.innerHTML = `<span class="text-muted"><i class="bi bi-truck me-1"></i>${name}</span> <span class="text-danger">-RD$ ${Number(orden.delivery_fee).toFixed(2)}</span>`;
        deliveryFeeLabel.classList.remove('d-none');
    } else {
        deliveryFeeLabel.classList.add('d-none');
    }
}

// Búsqueda de mesas en el panel lateral
document.getElementById('buscar-mesa').addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    document.querySelectorAll('.mesa-btn').forEach(btn => {
        const text = (btn.textContent || '').toLowerCase();
        btn.closest('.col-6').style.display = (!q || text.includes(q)) ? '' : 'none';
    });
});

function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
}

// Split bill — asignación de items por persona
    let splitPersonas = 2;
    let splitAsignaciones = {};
    let splitPercentages = {};
    let splitCompartidos = {};









































function abrirSplitBill(n) {
    if (!ordenActual || !ordenActual.detalles || ordenActual.detalles.length === 0) {
        alert('La orden no tiene items para dividir');
        return;
    }
    splitPersonas = n;
    splitAsignaciones = {};
    renderSplitBill();
    new bootstrap.Modal(document.getElementById('splitBillModal')).show();
}

function renderSplitBill() {
    const body = document.getElementById('split-bill-body');
    const detalles = ordenActual.detalles;
    if (!detalles || detalles.length === 0) {
        body.innerHTML = '<div class="text-center text-muted py-4">No hay items en la orden</div>';
        return;
    }

    // Inicializar asignaciones si es primera vez
    if (Object.keys(splitAsignaciones).length === 0) {
        // Por defecto, asignar items secuencialmente a personas
        detalles.forEach((d, i) => {
            splitAsignaciones[d.id] = (i % splitPersonas) + 1;
        });
    }

    let html = `<div class="table-responsive"><table class="table table-sm table-hover mb-0">
        <thead class="table-light small"><tr><th>Item</th><th>Precio</th>`;
    for (let p = 1; p <= splitPersonas; p++) {
        html += `<th class="text-center" style="min-width:60px;">Persona ${p}</th>`;
    }
    html += `</tr></thead><tbody>`;
    
    let totales = new Array(splitPersonas).fill(0);
    detalles.forEach(d => {
        const asignado = splitAsignaciones[d.id] || 1;
        html += `<tr data-id="${d.id}">
            <td class="py-2">
                <div class="d-flex align-items-center">
                    <div class="form-check">
                        <input class="form-check-input share-checkbox" type="checkbox" id="share-${d.id}" 
                            ${splitCompartidos[d.id] ? 'checked' : ''} 
                            onchange="toggleShare(${d.id})">
                        <label class="form-check-label small" for="share-${d.id}">Comp.</label>
                    </div>
                    <small class="ms-1">${escapeHtml(d.producto?.nombre || 'Item #' + d.producto_id)}${d.cantidad > 1 ? ` <span class="badge bg-secondary">${d.cantidad}</span>` : ''}</small>
                </div>
            </td>
            <td class="py-2"><small>RD$ ${Number(d.subtotal).toFixed(2)}</small></td>`;
        for (let p = 1; p <= splitPersonas; p++) {
            const active = asignado === p;
            if (active && !splitCompartidos[d.id]) totales[p - 1] += parseFloat(d.subtotal);
            html += `<td class="text-center py-2">
                <input type="radio" name="split-${d.id}" value="${p}" ${active ? 'checked' : ''} 
                    onchange="splitAsignaciones[${d.id}]=${p};actualizarTotalesSplit()" class="form-check-input">
            </td>`;
        }
        html += `</tr>`;
    });
    
    html += `</tbody></table></div>`;
    html += `<div class="mt-3 p-3 bg-light rounded-3"><div class="row g-2">`;
    for (let p = 1; p <= splitPersonas; p++) {
        html += `<div class="col-md-${Math.floor(12 / splitPersonas)}">
            <small class="fw-bold d-block">Persona ${p}</small>
            <div class="d-flex align-items-center gap-2">
                <input type="number" class="form-control form-control-sm percent-input" 
                    data-idx="${p-1}" value="${splitPercentages[p-1] || 100}" 
                    onchange="actualizarTotalesSplit()" min="0" max="100">
                <span class="fs-5 fw-bold text-primary" id="split-total-${p}">RD$ ${totales[p-1].toFixed(2)}</span>
            </div>
        </div>`;
    }
    html += `</div></div>`;
    
    body.innerHTML = html;
    document.getElementById('split-bill-footer').style.display = 'flex';
    const sumaTotal = totales.reduce((a, b) => a + b, 0);
    document.getElementById('split-totals').textContent = `Total: RD$ ${sumaTotal.toFixed(2)} · ${splitPersonas} personas`;
}

function calcularDatosSplit() {
    const detalles = ordenActual.detalles;
    let totales = new Array(splitPersonas).fill(0);
    let itemsPorPersona = {};
    
    document.querySelectorAll('.percent-input').forEach(input => {
        const idx = input.dataset.idx;
        splitPercentages[idx] = parseInt(input.value) || 0;
    });

    detalles.forEach(d => {
        const asignado = splitAsignaciones[d.id] || 1;
        if (splitCompartidos[d.id]) {
            const participantes = splitPercentages.filter(p => p > 0).length;
            const divisor = participantes > 0 ? participantes : 1;
            for (let p = 1; p <= splitPersonas; p++) {
                totales[p - 1] += parseFloat(d.subtotal) / divisor;
                if (splitPercentages[p-1] > 0) {
                    if (!itemsPorPersona[p]) itemsPorPersona[p] = [];
                    itemsPorPersona[p].push(d.id);
                }
            }
        } else if (asignado) {
            const pct = splitPercentages[asignado - 1] || 100;
            totales[asignado - 1] += parseFloat(d.subtotal) * (pct / 100);
            if (!itemsPorPersona[asignado]) itemsPorPersona[asignado] = [];
            itemsPorPersona[asignado].push(d.id);
        }
    });
    
    return { totales, itemsPorPersona };
}

function actualizarTotalesSplit() {
    const { totales, itemsPorPersona } = calcularDatosSplit();
    
    for (let p = 1; p <= splitPersonas; p++) {
        const el = document.getElementById(`split-total-${p}`);
        if (el) el.textContent = 'RD$ ' + totales[p - 1].toFixed(2);
    }

    const sumaTotal = totales.reduce((a, b) => a + b, 0);
    document.getElementById('split-totals').textContent = `Total: RD$ ${sumaTotal.toFixed(2)} · ${splitPersonas} personas`;
    
    if (window.splitData) {
        window.splitData.totales = totales;
        window.splitData.itemsPorPersona = itemsPorPersona;
    }
}

function confirmarSplitBill() {
    const { totales, itemsPorPersona } = calcularDatosSplit();
    
    const suma = totales.reduce((a, b) => a + b, 0);
    if (suma <= 0) { alert('Asigna al menos un item a cada persona'); return; }

    bootstrap.Modal.getInstance(document.getElementById('splitBillModal')).hide();
    
    window.splitData = { activo: true, personas: splitPersonas, totales, itemsPorPersona };
    
    const pagoModal = document.getElementById('pagoModal');
    if (!pagoModal.classList.contains('show')) {
        document.querySelector('.metodo-btn[data-metodo="mixto"]')?.click();
        const propina = parseFloat(document.getElementById('propina-input').value) || 0;
        const totalConPropina = suma + propina;
        const porPersona = totalConPropina / splitPersonas;
        document.getElementById('mixto-efectivo').value = porPersona.toFixed(2);
        new bootstrap.Modal(pagoModal).show();
    }
}

function mostrarDescuento() {
    if (!ordenActual) { alert('No hay orden activa'); return; }
    document.getElementById('descuento-valor').value = '';
    document.getElementById('descuento-motivo').value = '';
    new bootstrap.Modal(document.getElementById('descuentoModal')).show();
}

function aplicarDescuento() {
    const tipo = document.getElementById('descuento-tipo').value;
    const valor = document.getElementById('descuento-valor').value;
    const motivo = document.getElementById('descuento-motivo').value.trim();
    if (!valor || valor <= 0) { alert('Ingresa un valor válido'); return; }
    if (!motivo) { alert('Ingresa el motivo del descuento'); return; }
    fetch(`/restaurante/mesa/${mesaActual}/descuento`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ tipo, valor, motivo })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); return; }
        bootstrap.Modal.getInstance(document.getElementById('descuentoModal')).hide();
        ordenActual = data.orden;
        renderOrden(data.orden);
    });
}

function anularOrden() {
    if (!ordenActual) { alert('No hay orden activa'); return; }
    if (!confirm('¿Estás seguro de anular esta orden? Se devolverá el stock.')) return;
    fetch(`/restaurante/mesa/${mesaActual}/anular`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ motivo: prompt('Motivo de anulación:', 'Anulación manual') || 'Anulación manual' })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); return; }
        actualizarGridMesa(mesaActual, 'disponible');
        document.getElementById('orden-items').innerHTML = '<div class="text-center text-muted mt-5"><i class="bi bi-hand-index fs-1 d-block mb-2"></i><p>Orden anulada</p></div>';
        document.getElementById('orden-footer').classList.add('d-none');
        document.getElementById('orden-actions').classList.add('d-none');
        document.getElementById('productos-search-bar').classList.add('d-none');
        document.getElementById('orden-titulo').textContent = 'Selecciona una mesa';
        document.getElementById('orden-subtitulo').textContent = 'Haz clic en una mesa para ver su orden';
        ordenActual = null;
    });
}

function mostrarTrasladar() {
    if (!mesaActual) return;
    new bootstrap.Modal(document.getElementById('trasladarModal')).show();
}

function trasladarMesa() {
    const destino = document.getElementById('mesa-destino').value;
    if (!destino) { alert('Selecciona una mesa destino'); return; }
    fetch(`/restaurante/mesa/${mesaActual}/trasladar`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ destino_id: destino })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); return; }
        bootstrap.Modal.getInstance(document.getElementById('trasladarModal')).hide();
        actualizarGridMesa(mesaActual, 'disponible');
        mesaActual = parseInt(destino);
        actualizarGridMesa(mesaActual, 'ocupada');
        ordenActual = data.orden;
        renderOrden(data.orden);
        document.getElementById('orden-titulo').textContent = document.querySelector(`.mesa-btn[data-mesa-id="${mesaActual}"]`)?.querySelector('.fw-bold')?.textContent || 'Mesa';
    });
}

function mostrarHistorial() {
    if (!mesaActual) return;
    document.getElementById('historial-content').innerHTML = '<div class="text-center text-muted py-4"><div class="spinner-border spinner-border-sm me-2"></div>Cargando...</div>';
    fetch(`/restaurante/mesa/${mesaActual}/historial`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('historial-content').innerHTML = data.html;
            new bootstrap.Modal(document.getElementById('historialModal')).show();
        });
}

function quitarItem(detalleId) {
    Swal.fire({
        icon: 'question',
        title: '¿Quitar plato?',
        showCancelButton: true,
        confirmButtonText: 'Sí, quitar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (!result.isConfirmed) return;
        fetch(`/restaurante/mesa/${mesaActual}/quitar/${detalleId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { Swal.fire({icon:'error', title:'Error', text: data.error}); return; }
            ordenActual = data.orden;
            if (data.orden.detalles && data.orden.detalles.length > 0) {
                renderOrden(data.orden);
            } else {
                cargarMesa(mesaActual);
            }
        });
    });
}

function cambiarCantidadItem(detalleId, delta, stock) {
    if (!ordenActual) {
        Swal.fire({icon:'error', title:'Error', text:'No hay orden activa'});
        return;
    }
    const detalle = ordenActual.detalles.find(d => d.id === detalleId);
    if (!detalle) {
        Swal.fire({icon:'error', title:'Error', text:'Comida no encontrada en la orden'});
        return;
    }
    let nueva = parseInt(detalle.cantidad) + parseInt(delta);
    if (nueva <= 0) {
        Swal.fire({
            icon: 'question',
            title: '¿Quitar plato?',
            text: `¿Eliminar "${detalle.producto?.nombre || 'Plato'}" de la orden?`,
            showCancelButton: true,
            confirmButtonText: 'Sí, quitar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) quitarItem(detalleId);
        });
        return;
    }
    if (restauranteValidaStock) {
        const stk = parseInt(stock);
        if (!isNaN(stk) && stk < nueva) {
            Swal.fire({icon:'error', title:'Stock insuficiente', text:`Solo hay ${stk} unidades disponibles`});
            return;
        }
    }
    enviarCambiarCantidad(detalleId, nueva);
}

function enviarCambiarCantidad(detalleId, cantidad) {
    fetch(`/restaurante/mesa/${mesaActual}/actualizar/${detalleId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ cantidad: cantidad })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { Swal.fire({icon:'error', title:'Error', text: data.error}); return; }
        ordenActual = data.orden;
        if (data.orden.detalles && data.orden.detalles.length > 0) {
            renderOrden(data.orden);
        } else {
            cargarMesa(mesaActual);
        }
    });
}

function mostrarPago() {
    if (!ordenActual || ordenActual.total <= 0) { alert('La orden está vacía'); return; }
    document.getElementById('pago-total').textContent = 'RD$ ' + Number(ordenActual.total).toFixed(2);
    document.getElementById('pago-mesa-label').textContent = 'Mesa #' + (mesaActual || '--');
    document.getElementById('propina-input').value = '0';
    document.getElementById('monto-recibido').value = ordenActual.total;
    document.getElementById('cambio-info').classList.add('d-none');
    document.getElementById('mixto-efectivo').value = '';
    document.getElementById('mixto-tarjeta').value = '';
    document.getElementById('mixto-transferencia').value = '';
    document.getElementById('pago-efectivo').style.display = 'block';
    document.getElementById('pago-mixto').style.display = 'none';
    document.querySelectorAll('.metodo-btn').forEach(b => b.classList.remove('active-metodo'));
    document.querySelector('.metodo-btn[data-metodo="efectivo"]').classList.add('active-metodo');
    // Cargo por servicio
    const mesaBtn = document.querySelector(`.mesa-btn[data-mesa-id="${mesaActual}"]`);
    const capacidadTexto = mesaBtn?.querySelector('small')?.textContent || '';
    const capMatch = capacidadTexto.match(/Cap\.\s*(\d+)/);
    const capacidad = capMatch ? parseInt(capMatch[1]) : 0;
    const row = document.getElementById('cargo-servicio-row');
    const pctEl = document.getElementById('cargo-servicio-pct');
    if (servicioPorcentaje > 0 && capacidad >= servicioMinPersonas) {
        pctEl.textContent = servicioPorcentaje;
        document.getElementById('cargo-servicio-check').checked = true;
        row.style.display = 'block';
    } else {
        document.getElementById('cargo-servicio-check').checked = false;
        row.style.display = 'none';
    }
    new bootstrap.Modal(document.getElementById('pagoModal')).show();
    actualizarTotalPago();
}

function actualizarTotalPago() {
    const totalBase = parseFloat(ordenActual?.total || 0);
    const propina = parseFloat(document.getElementById('propina-input').value) || 0;
    const cargoServicio = document.getElementById('cargo-servicio-check')?.checked
        ? totalBase * (parseFloat(document.getElementById('cargo-servicio-pct').textContent) || 0) / 100
        : 0;
    totalPagoFinal = totalBase + propina + cargoServicio;
    document.getElementById('pago-total').textContent = 'RD$ ' + totalPagoFinal.toFixed(2);
    document.getElementById('monto-recibido').value = '';
    document.getElementById('cambio-info').classList.add('d-none');
}

document.querySelectorAll('.metodo-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.metodo-btn').forEach(b => b.classList.remove('active-metodo'));
        this.classList.add('active-metodo');
        const metodo = this.dataset.metodo;
        document.querySelectorAll('.pago-detalle').forEach(d => d.style.display = 'none');
        if (metodo === 'efectivo') {
            document.getElementById('pago-efectivo').style.display = 'block';
            document.getElementById('monto-recibido').value = totalPagoFinal || ordenActual?.total || 0;
        } else if (metodo === 'mixto') {
            document.getElementById('pago-mixto').style.display = 'block';
        }
    });
});

document.getElementById('monto-recibido').addEventListener('input', function () {
    const recibido = parseFloat(this.value) || 0;
    const total = totalPagoFinal || parseFloat(ordenActual?.total || 0);
    if (recibido >= total) {
        document.getElementById('cambio-monto').textContent = 'RD$ ' + (recibido - total).toFixed(2);
        document.getElementById('cambio-info').classList.remove('d-none');
    } else {
        document.getElementById('cambio-info').classList.add('d-none');
    }
});

document.querySelectorAll('#mixto-efectivo, #mixto-tarjeta, #mixto-transferencia').forEach(inp => {
    inp.addEventListener('input', function () {
        const total = totalPagoFinal || parseFloat(ordenActual?.total || 0);
        const efec = parseFloat(document.getElementById('mixto-efectivo').value) || 0;
        const tarj = parseFloat(document.getElementById('mixto-tarjeta').value) || 0;
        const tran = parseFloat(document.getElementById('mixto-transferencia').value) || 0;
        const suma = efec + tarj + tran;
        const restante = total - suma;
        document.getElementById('mixto-restante').textContent = restante > 0 ? 'Faltan: RD$ ' + restante.toFixed(2) : 'Sobrante: RD$ ' + Math.abs(restante).toFixed(2);
    });
});

function procesarPago() {
    const metodo = document.querySelector('.metodo-btn.active-metodo').dataset.metodo;
    const propina = parseFloat(document.getElementById('propina-input').value) || 0;
    let payload = { metodo_pago: metodo, propina: propina, cargo_servicio: document.getElementById('cargo-servicio-check')?.checked || false };

    // Split bill data
    if (window.splitData?.activo) {
        payload.split = true;
        payload.personas = window.splitData.personas;
        payload.totales = window.splitData.totales;
        // Build split_persons array for backend (SplitBillPerson records)
        const persons = [];
        for (let p = 1; p <= window.splitData.personas; p++) {
            persons.push({
                num: p,
                nombre: '',
                items: window.splitData.itemsPorPersona[p] || [],
                subtotal: window.splitData.totales[p - 1] || 0,
            });
        }
        payload.split_persons = persons;
    }

    if (metodo === 'efectivo') {
        payload.monto_recibido = document.getElementById('monto-recibido').value;
    } else if (metodo === 'mixto') {
        payload.monto_recibido = document.getElementById('mixto-efectivo').value || 0;
        payload.monto_tarjeta = document.getElementById('mixto-tarjeta').value || 0;
        payload.monto_transferencia = document.getElementById('mixto-transferencia').value || 0;
    }

    fetch(`/restaurante/mesa/${mesaActual}/cobrar`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); return; }
        bootstrap.Modal.getInstance(document.getElementById('pagoModal')).hide();
        postPagoData = data.venta;
        mostrarPostPago(data.venta);
        actualizarGridMesa(mesaActual, 'disponible');
        ordenActual = null;
        window.splitData = null;
        document.getElementById('orden-items').innerHTML = '<div class="text-center text-muted mt-5"><i class="bi bi-hand-index fs-1 d-block mb-2"></i><p>Selecciona una mesa</p></div>';
        document.getElementById('orden-footer').classList.add('d-none');
        document.getElementById('orden-actions').classList.add('d-none');
        document.getElementById('productos-search-bar').classList.add('d-none');
    });
}

function mostrarPostPago(venta) {
    document.getElementById('post-mesa-info').textContent = venta.mesa_nombre || 'Mesa #' + venta.mesa_numero;
    document.getElementById('post-cliente').textContent = 'Cliente: ' + (venta.cliente || 'Consumidor Final');
    document.getElementById('post-total').textContent = 'RD$ ' + Number(venta.total).toFixed(2);
    document.getElementById('post-metodo').textContent = venta.metodo_pago.charAt(0).toUpperCase() + venta.metodo_pago.slice(1);
    document.getElementById('factura-status').classList.add('d-none');
    document.getElementById('btn-facturar').disabled = false;
    document.getElementById('btn-facturar').innerHTML = '<i class="bi bi-shield-check me-1"></i> Facturar (e-CF)';
    const ticketUrl = `/restaurante/mesa/${mesaActual}/ticket?venta_id=${venta.id}`;
    document.getElementById('btn-ticket').href = ticketUrl;
    new bootstrap.Modal(document.getElementById('postPagoModal')).show();
}

function facturarMesa() {
    if (!postPagoData) return;
    const btn = document.getElementById('btn-facturar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Facturando...';

    fetch(`/restaurante/mesa/${mesaActual}/facturar`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ venta_id: postPagoData.id })
    })
    .then(r => r.json())
    .then(data => {
        const status = document.getElementById('factura-status');
        status.classList.remove('d-none');
        if (data.error) {
            status.innerHTML = `<div class="alert alert-danger rounded-3 py-1 small mb-0">${data.error}</div>`;
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-shield-check me-1"></i> Facturar (e-CF)';
        } else {
            status.innerHTML = `<div class="alert alert-success rounded-3 py-1 small mb-0"><i class="bi bi-check-circle me-1"></i> ${data.message}</div>`;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Facturado';
        }
    })
    .catch(() => {
        document.getElementById('factura-status').classList.remove('d-none');
        document.getElementById('factura-status').innerHTML = '<div class="alert alert-danger rounded-3 py-1 small mb-0">Error de conexión</div>';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-shield-check me-1"></i> Facturar (e-CF)';
    });
}

function imprimirTicket() {
    if (!postPagoData || !mesaActual) return;
    const btn = document.getElementById('btn-imprimir');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Imprimiendo...';

    fetch(`/restaurante/mesa/${mesaActual}/ticket/print`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ venta_id: postPagoData.id })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) {
            Swal.fire({icon:'error', title:'Error de impresión', text: data.error});
        } else {
            Swal.fire({icon:'success', title:'Impreso', text: data.message, timer: 1500, showConfirmButton: false});
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-printer me-1"></i> Imprimir';
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-printer me-1"></i> Imprimir';
        Swal.fire({icon:'error', title:'Error', text:'No se pudo conectar con el servidor'});
    });
}

function reimprimirTicket(mesaId, ventaId) {
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    fetch(`/restaurante/mesa/${mesaId}/ticket/print`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ venta_id: ventaId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) {
            Swal.fire({icon:'error', title:'Error de impresión', text: data.error});
        } else {
            Swal.fire({icon:'success', title:'Impreso', text: data.message, timer: 1500, showConfirmButton: false});
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-printer"></i> Reimprimir';
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-printer"></i> Reimprimir';
        Swal.fire({icon:'error', title:'Error', text:'No se pudo conectar con el servidor'});
    });
}

// Waitlist functions
function mostrarWaitlist() {
    const modal = new bootstrap.Modal(document.getElementById('waitlistModal'));
    modal.show();
    cargarWaitlist();
}

function cargarWaitlist() {
    const container = document.getElementById('waitlist-entries');
    container.innerHTML = '<div class="text-center text-muted py-3 small">Cargando...</div>';
    fetch('{{ route("restaurante.waitlist.index") }}')
        .then(r => r.json())
        .then(data => {
            const entries = data.entries || [];
            if (entries.length === 0) {
                container.innerHTML = '<div class="text-center text-muted py-3 small">Sin clientes en espera</div>';
                return;
            }
            container.innerHTML = entries.map(e => `
                <div class="d-flex justify-content-between align-items-center p-2 rounded-3 mb-1 bg-light">
                    <div>
                        <div class="fw-semibold small">${escapeHtml(e.cliente_nombre)}</div>
                        <small class="text-muted">${e.personas} pers. ${e.cliente_telefono ? '· ' + e.cliente_telefono : ''}</small>
                        ${e.notas ? `<br><small class="fst-italic" style="font-size:.65rem;">📝 ${escapeHtml(e.notas)}</small>` : ''}
                    </div>
                    <div class="d-flex gap-1">
                        ${e.estado === 'esperando' ? `
                            <button class="btn btn-sm btn-success rounded-pill" onclick="cambiarEstadoWaitlist(${e.id}, 'llamando')" title="Llamar"><i class="bi bi-telephone"></i></button>
                            <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="eliminarWaitlist(${e.id})" title="Cancelar"><i class="bi bi-x"></i></button>
                        ` : e.estado === 'llamando' ? `
                            <span class="badge bg-warning text-dark d-flex align-items-center gap-1 rounded-pill px-2">Llamando</span>
                            <button class="btn btn-sm btn-success rounded-pill" onclick="cambiarEstadoWaitlist(${e.id}, 'sentado')" title="Sentado"><i class="bi bi-check"></i></button>
                            <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="cambiarEstadoWaitlist(${e.id}, 'cancelado')"><i class="bi bi-x"></i></button>
                        ` : e.estado === 'sentado' ? `<span class="badge bg-success rounded-pill">Sentado</span>` : `<span class="badge bg-secondary rounded-pill">${e.estado}</span>`}
                    </div>
                </div>
            `).join('');
        });
}

function agregarWaitlist(e) {
    e.preventDefault();
    const nombre = document.getElementById('wl-nombre').value.trim();
    const personas = document.getElementById('wl-personas').value;
    const telefono = document.getElementById('wl-telefono').value.trim();
    const notas = document.getElementById('wl-notas').value.trim();
    if (!nombre) { alert('Nombre requerido'); return; }
    fetch('{{ route("restaurante.waitlist.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ cliente_nombre: nombre, personas, cliente_telefono: telefono, notas })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); return; }
        document.getElementById('wl-nombre').value = '';
        document.getElementById('wl-personas').value = '2';
        document.getElementById('wl-telefono').value = '';
        document.getElementById('wl-notas').value = '';
        cargarWaitlist();
    });
}

function cambiarEstadoWaitlist(id, estado) {
    fetch(`/restaurante/waitlist/${id}/estado`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ estado })
    })
    .then(r => r.json())
    .then(data => { if (data.success) cargarWaitlist(); });
}

function eliminarWaitlist(id) {
    if (!confirm('¿Eliminar esta entrada?')) return;
    fetch(`/restaurante/waitlist/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => { if (data.success) cargarWaitlist(); });
}

function cerrarMesa() {
    if (!mesaActual) return;
    const msg = ordenActual?.detalles?.length > 0
        ? 'La mesa tiene platos sin cobrar. ¿Cerrar y anular la orden?'
        : '¿Cerrar esta mesa?';
    Swal.fire({
        icon: 'question',
        title: 'Cerrar mesa',
        text: msg,
        showCancelButton: true,
        confirmButtonText: 'Sí, cerrar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (!result.isConfirmed) return;
        fetch(`/restaurante/mesa/${mesaActual}/anular`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ motivo: 'Cierre manual desde interfaz' })
        })
        .then(r => {
            if (!r.ok) return r.json().then(d => { throw new Error(d.error || 'Error al cerrar'); });
            return r.json();
        })
        .then(data => {
            if (data.error) { Swal.fire({icon:'error', title:'No se pudo cerrar', text: data.error}); return; }
            actualizarGridMesa(mesaActual, 'disponible');
            document.getElementById('orden-titulo').textContent = 'Selecciona una mesa';
            document.getElementById('orden-items').innerHTML = '<div class="text-center text-muted mt-5"><i class="bi bi-hand-index fs-1 d-block mb-2"></i><p>Selecciona una mesa para comenzar</p></div>';
            document.getElementById('orden-footer').classList.add('d-none');
            document.getElementById('orden-actions').classList.add('d-none');
            document.getElementById('productos-search-bar').classList.add('d-none');
            document.getElementById('cliente-selector').classList.add('d-none');
            ordenActual = null;
            mesaActual = null;
        })
        .catch(err => {
            Swal.fire({icon:'error', title:'Error', text: err.message || 'Error de conexión'});
        });
    });
}

function actualizarGridMesa(mesaId, estado) {
    const btn = document.querySelector(`.mesa-btn[data-mesa-id="${mesaId}"]`);
    if (!btn) return;
    btn.dataset.estado = estado;
    btn.className = 'mesa-btn w-100 text-start p-3 rounded-4 border-0 shadow-sm position-relative ' +
        (estado === 'disponible' ? 'mesa-libre' : '') +
        (estado === 'ocupada' ? 'mesa-ocupada' : '') +
        (estado === 'reservada' ? 'mesa-reservada' : '') +
        (estado === 'inactiva' ? 'mesa-inactiva' : '');
    const badge = btn.querySelector('.badge.rounded-pill.mt-1');
    if (badge) {
        badge.textContent = estado.charAt(0).toUpperCase() + estado.slice(1);
        badge.className = 'badge rounded-pill mt-1 ' +
            (estado === 'disponible' ? 'bg-success' : '') +
            (estado === 'ocupada' ? 'bg-warning text-dark' : '') +
            (estado === 'reservada' ? 'bg-info' : '') +
            (estado === 'inactiva' ? 'bg-secondary' : '');
    }
    const estadoIcon = btn.querySelector('.estado-icon');
    if (estadoIcon) {
        estadoIcon.className = 'estado-icon ' +
            (estado === 'disponible' ? 'verde' : '') +
            (estado === 'ocupada' ? 'amarillo' : '') +
            (estado === 'reservada' ? 'azul' : '') +
            (estado === 'inactiva' ? 'gris' : '');
    }
    const montoBadge = btn.querySelector('.badge.bg-dark');
    if (estado === 'disponible' && montoBadge) montoBadge.remove();
    const reservaRibbon = btn.querySelector('.reserva-ribbon');
    if (reservaRibbon && estado !== 'reservada') reservaRibbon.remove();
    const reservaInfoDiv = btn.querySelector('[style*="background:rgba(255,255,255,.7)"]');
    if (reservaInfoDiv && estado !== 'reservada') reservaInfoDiv.remove();
}

document.getElementById('mesas-count').textContent = document.querySelectorAll('.mesa-btn').length;

// Timer de reservas — actualiza cada 30s
function actualizarTimersReservas() {
    document.querySelectorAll('.reserva-timer[data-fecha]').forEach(el => {
        const fechaHora = el.dataset.fecha;
        if (!fechaHora) return;
        const reserva = new Date(fechaHora.replace(' ', 'T').replace(/\/$/, ''));
        if (isNaN(reserva.getTime())) return;
        const now = new Date();
        const diffMs = reserva - now;
        const diffMin = Math.round(diffMs / 60000);
        if (diffMin > 30) {
            el.innerHTML = '';
        } else if (diffMin >= 0) {
            el.innerHTML = `<span class="reserva-timer pon-proximo"><i class="bi bi-hourglass-split"></i> en ${diffMin} min</span>`;
        } else {
            el.innerHTML = `<span class="reserva-timer pon-vencida"><i class="bi bi-exclamation-triangle-fill"></i> Atrasada ${Math.abs(diffMin)} min</span>`;
        }
    });
}
setInterval(actualizarTimersReservas, 30000);
actualizarTimersReservas();

// Atajos de teclado
document.addEventListener('keydown', function (e) {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) return;
    if (e.key === 'F2') {
        e.preventDefault();
        const input = document.getElementById('buscar-producto');
        if (input && !input.closest('.d-none')) { input.focus(); input.select(); }
    }
    if (e.key === 'F4' && ordenActual && ordenActual.total > 0) {
        e.preventDefault();
        document.querySelector('.metodo-btn[data-metodo="efectivo"]')?.click();
        mostrarPago();
    }
    if (e.key === 'F5' && ordenActual && ordenActual.total > 0) {
        e.preventDefault();
        document.querySelector('.metodo-btn[data-metodo="tarjeta"]')?.click();
        mostrarPago();
    }
    if (e.key === 'F9' && ordenActual && ordenActual.total > 0) {
        e.preventDefault();
        document.querySelector('.metodo-btn[data-metodo="transferencia"]')?.click();
        mostrarPago();
    }
});

// Mapa de mesas
let mapaActivo = false;

function toggleMapa() {
    mapaActivo = !mapaActivo;
    const grid = document.getElementById('mesas-grid');
    const mapa = document.getElementById('mesas-mapa');
    const btn = document.getElementById('btn-toggle-mapa');
    grid.classList.toggle('d-none', mapaActivo);
    mapa.classList.toggle('d-none', !mapaActivo);
    btn.innerHTML = mapaActivo ? '<i class="bi bi-grid"></i> Grid' : '<i class="bi bi-map"></i> Mapa';
    if (mapaActivo) {
        // Inicializar drag para cada mesa en el mapa
        document.querySelectorAll('.mesa-mapa-btn').forEach(el => {
            let offsetX, offsetY, startX, startY, wasDragged;
            el.addEventListener('mousedown', function (e) {
                if (e.target.tagName === 'BUTTON') return;
                wasDragged = false;
                offsetX = e.clientX - parseInt(this.style.left);
                offsetY = e.clientY - parseInt(this.style.top);
                startX = e.clientX;
                startY = e.clientY;
                el.style.cursor = 'grabbing';
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
                e.preventDefault();
            });
            function onMouseMove(e) {
                const dx = Math.abs(e.clientX - startX);
                const dy = Math.abs(e.clientY - startY);
                if (dx > 5 || dy > 5) wasDragged = true;
                const parent = el.parentElement;
                const rect = parent.getBoundingClientRect();
                let newX = e.clientX - offsetX - rect.left + parent.scrollLeft;
                let newY = e.clientY - offsetY - rect.top + parent.scrollTop;
                newX = Math.max(0, Math.min(newX, rect.width - 150));
                newY = Math.max(0, Math.min(newY, rect.height - 80));
                el.style.left = newX + 'px';
                el.style.top = newY + 'px';
            }
            function onMouseUp() {
                el.style.cursor = 'grab';
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
                if (!wasDragged) {
                    seleccionarMesaMapa(parseInt(el.dataset.mesaId));
                    return;
                }
                // Auto-guardar posición
                const x = parseInt(el.style.left);
                const y = parseInt(el.style.top);
                fetch(`/restaurante/mesa/${el.dataset.mesaId}/posicion`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ pos_x: x, pos_y: y })
                });
            }
            el.addEventListener('click', function (e) {
                if (wasDragged) { e.stopPropagation(); e.preventDefault(); }
    });
    });
}

function guardarMapa() {
    const mesas = [];
    document.querySelectorAll('.mesa-mapa-btn').forEach(el => {
        mesas.push({ id: parseInt(el.dataset.mesaId), pos_x: parseInt(el.style.left), pos_y: parseInt(el.style.top) });
    });
    fetch('{{ route("restaurante.mesas.posiciones") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ mesas })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) alert('Posiciones guardadas');
    });
}

function seleccionarMesaMapa(mesaId) {
    // Abrir mesa desde el mapa igual que en grid
    document.querySelectorAll('.mesa-mapa-btn').forEach(b => b.classList.remove('mesa-selected'));
    const el = document.querySelector(`.mesa-mapa-btn[data-mesa-id="${mesaId}"]`);
    if (el) el.classList.add('mesa-selected');
    cargarMesa(mesaId);
}
}

// ─── Modal de productos con teclado virtual ───
let tecladoIdiomaActual = 'es';
let cantidadesModal = {};
let modalCategoriaFiltro = '';

const TECLADO_LAYOUTS = {
    us: [
        ['q','w','e','r','t','y','u','i','o','p'],
        ['a','s','d','f','g','h','j','k','l'],
        ['z','x','c','v','b','n','m']
    ],
    es: [
        ['q','w','e','r','t','y','u','i','o','p'],
        ['a','s','d','f','g','h','j','k','l','ñ'],
        ['z','x','c','v','b','n','m']
    ]
};

function tecladoIdioma(idioma) {
    tecladoIdiomaActual = idioma;
    document.getElementById('btn-idioma-us').classList.toggle('active', idioma === 'us');
    document.getElementById('btn-idioma-es').classList.toggle('active', idioma === 'es');
    renderizarTeclado();
}

function renderizarTeclado() {
    const container = document.getElementById('teclado-rows');
    const layout = TECLADO_LAYOUTS[tecladoIdiomaActual] || TECLADO_LAYOUTS.es;
    let html = '';

    html += '<div class="tecla-row">';
    ['1','2','3','4','5','6','7','8','9','0'].forEach(n => {
        html += `<button class="tecla tecla-num" onpointerdown="teclaPulsar('${n}')" type="button">${n}</button>`;
    });
    html += '</div>';

    layout.forEach(fila => {
        html += '<div class="tecla-row">';
        fila.forEach(letra => {
            const display = teclaShiftActivo ? letra.toUpperCase() : letra;
            html += `<button class="tecla" onpointerdown="teclaPulsar('${letra}')" type="button">${display}</button>`;
        });
        html += '</div>';
    });

    html += '<div class="tecla-row">';
    const shiftCls = teclaShiftActivo ? ' active' : '';
    html += `<button class="tecla tecla-func tecla-shift${shiftCls}" onpointerdown="teclaMayusculas()" type="button"><i class="bi bi-arrow-up-short fs-5"></i></button>`;
    ['z','x','c','v','b','n','m'].forEach(letra => {
        const display = teclaShiftActivo ? letra.toUpperCase() : letra;
        html += `<button class="tecla" onpointerdown="teclaPulsar('${letra}')" type="button">${display}</button>`;
    });
    html += `<button class="tecla tecla-func tecla-backspace" onpointerdown="teclaBorrar()" type="button"><i class="bi bi-backspace fs-5"></i></button>`;
    html += '</div>';

    html += '<div class="tecla-row">';
    html += `<button class="tecla tecla-punct" onpointerdown="teclaPulsar(',')" type="button">,</button>`;
    html += `<button class="tecla tecla-space" onpointerdown="teclaPulsar(' ')" type="button"><span class="fw-normal">Espacio</span></button>`;
    html += `<button class="tecla tecla-punct" onpointerdown="teclaPulsar('.')" type="button">.</button>`;
    html += `<button class="tecla tecla-func tecla-enter" onpointerdown="teclaEnter()" type="button"><i class="bi bi-arrow-return-left fs-5"></i></button>`;
    html += '</div>';

    container.innerHTML = html;
}

let teclaShiftActivo = false;

function teclaPulsar(caracter) {
    const input = document.getElementById('modal-buscar-producto');
    const start = input.selectionStart || input.value.length;
    const end = input.selectionEnd || input.value.length;
    const val = input.value;
    const letra = teclaShiftActivo ? caracter.toUpperCase() : caracter;
    input.value = val.substring(0, start) + letra + val.substring(end);
    const newPos = start + letra.length;
    input.setSelectionRange(newPos, newPos);
    input.focus();
    if (teclaShiftActivo) {
        teclaShiftActivo = false;
        renderizarTeclado();
    }
    modalBuscarProductos();
}

function teclaMayusculas() {
    teclaShiftActivo = !teclaShiftActivo;
    renderizarTeclado();
}

function teclaBorrar() {
    const input = document.getElementById('modal-buscar-producto');
    const start = input.selectionStart || input.value.length;
    const end = input.selectionEnd || input.value.length;
    if (start === 0 && end === 0) return;
    if (start !== end) {
        const val = input.value;
        input.value = val.substring(0, start) + val.substring(end);
        input.setSelectionRange(start, start);
    } else {
        const val = input.value;
        input.value = val.substring(0, start - 1) + val.substring(start);
        input.setSelectionRange(start - 1, start - 1);
    }
    input.focus();
    modalBuscarProductos();
}

function teclaLimpiar() {
    document.getElementById('modal-buscar-producto').value = '';
    document.getElementById('modal-btn-limpiar').style.display = 'none';
    modalBuscarProductos();
    document.getElementById('modal-buscar-producto').focus();
}

function teclaEnter() {
    cerrarModalProductos();
}

function modalBuscarProductos() {
    const q = document.getElementById('modal-buscar-producto').value.trim();
    const limpiarBtn = document.getElementById('modal-btn-limpiar');
    limpiarBtn.style.display = q.length > 0 ? 'inline-block' : 'none';
    renderizarProductosModal(q);
}

function modalLimpiarBusqueda() {
    teclaLimpiar();
}

function cerrarModalProductos() {
    const el = document.getElementById('productosModal');
    const m = bootstrap.Modal.getInstance(el);
    if (m) m.hide();
}

document.addEventListener('DOMContentLoaded', function () {
    const productosModalEl = document.getElementById('productosModal');
    if (productosModalEl) {
        productosModalEl.addEventListener('hidden.bs.modal', function () {
            const instance = bootstrap.Modal.getInstance(this);
            if (instance) instance.dispose();
        });
    }
});

function abrirModalProductos() {
    const modalEl = document.getElementById('productosModal');
    const old = bootstrap.Modal.getInstance(modalEl);
    if (old) old.dispose();
    const modal = new bootstrap.Modal(modalEl, {keyboard: false});
    document.getElementById('modal-buscar-producto').value = '';
    document.getElementById('modal-btn-limpiar').style.display = 'none';
    document.getElementById('modal-item-notas').value = '';
    document.getElementById('modal-item-curso').value = 'fuerte';
    document.getElementById('modal-categoria-filtro').value = '';
    modalCategoriaFiltro = '';
    cantidadesModal = {};
    teclaShiftActivo = false;
    renderizarFiltroCategoriasModal();
    tecladoIdioma('es');
    renderizarProductosModal('');
    modal.show();
    setTimeout(() => document.getElementById('modal-buscar-producto').focus(), 300);
}

function renderizarFiltroCategoriasModal() {
    const sel = document.getElementById('modal-categoria-filtro');
    if (!sel || categoriasData.length === 0) return;
    let html = '<option value="">Todas</option>';
    categoriasData.forEach(c => {
        html += `<option value="${c.id}">${escapeHtml(c.nombre)}</option>`;
    });
    sel.innerHTML = html;
    // re-aplicar el evento
    sel.onchange = function() {
        modalCategoriaFiltro = this.value;
        modalBuscarProductos();
    };
}

const PALETA_COLORES = [
    { bg: '#fee2e2', fg: '#dc2626' },
    { bg: '#ffedd5', fg: '#ea580c' },
    { bg: '#fef9c3', fg: '#ca8a04' },
    { bg: '#dcfce7', fg: '#16a34a' },
    { bg: '#cffafe', fg: '#0891b2' },
    { bg: '#dbeafe', fg: '#2563eb' },
    { bg: '#ede9fe', fg: '#7c3aed' },
    { bg: '#fce7f3', fg: '#db2777' },
    { bg: '#ccfbf1', fg: '#0d9488' },
    { bg: '#faf5ff', fg: '#a21caf' },
];

function colorProducto(nombre) {
    let h = 0;
    for (let i = 0; i < nombre.length; i++) h = nombre.charCodeAt(i) + ((h << 5) - h);
    return PALETA_COLORES[Math.abs(h) % PALETA_COLORES.length];
}

function renderizarProductosModal(filtro) {
    const container = document.getElementById('modal-productos-grid');
    const q = (filtro || '').toLowerCase();
    const results = productosData.filter(p => {
        const matchNombre = (p.nombre || '').toLowerCase().includes(q);
        const matchCodigo = (p.codigo_barras || '').toLowerCase().includes(q);
        const matchCategoria = !modalCategoriaFiltro || String(p.categoria_id) === modalCategoriaFiltro;
        return (matchNombre || matchCodigo) && matchCategoria;
    });

    if (results.length === 0) {
        container.innerHTML = '<div class="col-12 text-center text-muted py-4">Sin resultados</div>';
        return;
    }

    let html = '';
    results.forEach(p => {
        const id = p.id;
        if (cantidadesModal[id] === undefined) cantidadesModal[id] = 1;
        const qty = cantidadesModal[id];
        const stockWarning = p.stock !== undefined && p.stock <= 3 ? ' border-warning' : '';
        const c = colorProducto(p.nombre);
        const initial = (p.nombre || '?').charAt(0).toUpperCase();
        const imgHtml = p.tiene_imagen && p.imagen_url
            ? `<img class="prod-card-img" src="${p.imagen_url}" alt="${escapeHtml(p.nombre)}" loading="lazy">`
            : `<div class="prod-card-img-placeholder" style="background:${c.bg};color:${c.fg}"><span class="prod-card-initial">${initial}</span></div>`;
        const stockBadge = p.stock !== undefined && p.stock <= 3
            ? `<span class="prod-card-stock-badge badge bg-warning text-dark">${p.stock} uds</span>`
            : '';

        html += `
        <div class="col-4 col-md-3 col-lg-2">
            <div class="card border shadow-sm prod-card${stockWarning}" onclick="agregarProductoDesdeModal(${id}, ${p.stock ?? 999})">
                ${stockBadge}
                ${imgHtml}
                <div class="card-body text-center">
                    <div class="prod-nombre fw-semibold">${escapeHtml(p.nombre)}</div>
                    <div class="prod-precio fw-bold text-primary">RD$ ${Number(p.precio).toFixed(2)}</div>
                    <div class="prod-qty" onclick="event.stopPropagation()">
                        <button onpointerdown="cambiarQtyModal(${id}, -1)" type="button">−</button>
                        <span id="qty-${id}">${qty}</span>
                        <button onpointerdown="cambiarQtyModal(${id}, 1)" type="button">+</button>
                    </div>
                </div>
            </div>
        </div>`;
    });
    container.innerHTML = html;
}

function cambiarQtyModal(productoId, delta) {
    if (cantidadesModal[productoId] === undefined) cantidadesModal[productoId] = 1;
    let nueva = cantidadesModal[productoId] + delta;
    if (nueva < 1) nueva = 1;
    if (nueva > 99) nueva = 99;
    cantidadesModal[productoId] = nueva;
    const span = document.getElementById('qty-' + productoId);
    if (span) span.textContent = nueva;
}

function agregarProductoDesdeModal(productoId, stock) {
    const cantidad = cantidadesModal[productoId] || 1;
    if (restauranteValidaStock) {
        if (stock <= 0) {
            Swal.fire({icon:'error', title:'Sin stock', text:'Este plato no tiene disponible'});
            return;
        }
        if (stock !== undefined && stock < cantidad) {
            Swal.fire({icon:'error', title:'Stock insuficiente', text:`Solo hay ${stock} unidades`});
            return;
        }
        if (stock !== undefined && stock <= 3) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock bajo',
                text: `Solo quedan ${stock} unidades. ¿Agregar de todas formas?`,
                showCancelButton: true,
                confirmButtonText: 'Sí, agregar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) enviarAgregarDesdeModal(productoId, cantidad);
            });
            return;
        }
    }
    enviarAgregarDesdeModal(productoId, cantidad);
}

function enviarAgregarDesdeModal(productoId, cantidad) {
    const notas = document.getElementById('modal-item-notas').value.trim();
    const curso = document.getElementById('modal-item-curso').value;

    fetch(`/restaurante/mesa/${mesaActual}/agregar`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ producto_id: productoId, cantidad: cantidad, notas: notas, curso: curso })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { Swal.fire({icon:'error', title:'Error', text: data.error}); return; }
        ordenActual = data.orden;
        renderOrden(data.orden);
        const modal = bootstrap.Modal.getInstance(document.getElementById('productosModal'));
        if (modal) modal.hide();
    });
}
</script>
@endsection