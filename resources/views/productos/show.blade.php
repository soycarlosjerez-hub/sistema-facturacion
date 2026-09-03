{{-- resources/views/productos/show.blade.php
    Vista de detalle premium del producto — Glassmorphism UI
--}}
@extends('layouts.app')

@section('title', $producto->nombre)

@push('styles')
@include('partials.premium-ui')

<style>
    /* ============================================================
       PRODUCTO SHOW — Estilos específicos
       ============================================================ */

    /* Acento: blue (productos) */
    .producto-show { --accent:#3b82f6; --accent-rgb:59,130,246; --accent-hover:#2563eb; }

    /* ——— Imagen de producto ——— */
    .producto-img-wrap {
        width: 100%;
        aspect-ratio: 1 / 1;
        border-radius: var(--radius-xl);
        overflow: hidden;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-lg);
    }
    .producto-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 1rem;
        transition: transform .3s ease;
    }
    .producto-img-wrap:hover img { transform: scale(1.05); }

    .producto-img-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        color: #94a3b8;
        text-align: center;
        padding: 2rem;
    }
    .producto-img-placeholder i { font-size: 3.5rem; }
    .producto-img-placeholder span { font-size: .85rem; font-weight: 500; }

    /* ——— Stock grande ——— */
    .producto-stock-lg {
        font-size: 2.75rem;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -.02em;
    }
    .producto-stock-lg.stock-critical { color: #dc2626; }
    .producto-stock-lg.stock-low       { color: #d97706; }
    .producto-stock-lg.stock-ok        { color: #16a34a; }

    /* ——— Info-row (label → value) ——— */
    .prod-info-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding: .65rem 0;
        border-bottom: 1px solid rgba(0,0,0,.05);
        gap: 1rem;
    }
    .prod-info-row:last-child { border-bottom: none; }
    .prod-info-label {
        font-size: .78rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .4px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: .4rem;
    }
    .prod-info-value {
        font-size: .92rem;
        font-weight: 600;
        color: #1e293b;
        text-align: right;
        word-break: break-word;
    }

    /* ——— Stat mini card ——— */
    .prod-stat-mini {
        text-align: center;
        padding: 1rem .5rem;
        border-radius: var(--radius);
        background: rgba(0,0,0,.02);
        border: 1px solid rgba(0,0,0,.04);
    }
    .prod-stat-mini .value { font-size: 1.5rem; font-weight: 800; line-height: 1.2; }
    .prod-stat-mini .label { font-size: .7rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .4px; margin-top: .2rem; }

    /* ——— Precio grande ——— */
    .prod-precio-lg { font-size: 2.25rem; font-weight: 800; color: var(--accent); line-height: 1.1; letter-spacing: -.02em; }

    /* ——— Sección card header ——— */
    .prod-section-head {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: 1.15rem 1.75rem .6rem;
        margin: 0;
        font-weight: 700;
        font-size: 1.05rem;
        color: #1e293b;
    }
    .prod-section-head i { font-size: 1.15rem; }

    /* ——— Accent strips por sección ——— */
    .accent-green  { background: linear-gradient(90deg, #10b981, rgba(16,185,129,.2)) !important; }
    .accent-blue   { background: linear-gradient(90deg, #3b82f6, rgba(59,130,246,.2)) !important; }
    .accent-purple { background: linear-gradient(90deg, #8b5cf6, rgba(139,92,246,.2)) !important; }
    .accent-red    { background: linear-gradient(90deg, #ef4444, rgba(239,68,68,.2)) !important; }
    .accent-amber  { background: linear-gradient(90deg, #f59e0b, rgba(245,158,11,.2)) !important; }

    /* ——— Descripcion body ——— */
    .prod-desc-body {
        background: rgba(99,102,241,.04);
        border-radius: var(--radius);
        padding: 1rem 1.25rem;
        font-size: .9rem;
        color: #475569;
        line-height: 1.6;
        border: 1px solid rgba(99,102,241,.08);
    }

    /* ——— UI Badge ——— */
    .ui-badge {
        display: inline-block;
        padding: .25rem .6rem;
        border-radius: 9999px;
        font-size: .75rem;
        font-weight: 600;
        line-height: 1;
    }
    .ui-badge-success { background: rgba(34,197,94,.12); color: #16a34a; }
    .ui-badge-warning { background: rgba(217,119,6,.12); color: #d97706; }
    .ui-badge-danger  { background: rgba(239,68,68,.12); color: #dc2626; }
    .ui-badge-info    { background: rgba(59,130,246,.12); color: #3b82f6; }

    /* ——— Header divider ——— */
    .ui-header-meta .divider { margin: 0 .5rem; color: rgba(255,255,255,.4); }

    /* ——— Avatar circle ——— */
    .ui-avatar-circle {
        width: 48px; height: 48px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.15);
        font-size: 1.25rem; color: white;
        flex-shrink: 0;
    }

    /* ——— Dark mode ——— */
    body.dark-mode .prod-info-row    { border-bottom-color: rgba(255,255,255,.06); }
    body.dark-mode .prod-info-label  { color: #94a3b8; }
    body.dark-mode .prod-info-value  { color: #e2e8f0; }
    body.dark-mode .prod-stat-mini   { background: rgba(255,255,255,.03); border-color: rgba(255,255,255,.06); }
    body.dark-mode .prod-stat-mini .label { color: #94a3b8; }
    body.dark-mode .prod-section-head { color: #f1f5f9; }
    body.dark-mode .prod-desc-body   { background: rgba(99,102,241,.06); border-color: rgba(99,102,241,.1); color: #cbd5e1; }

    @media (max-width: 767.98px) {
        .producto-stock-lg { font-size: 2rem; }
        .prod-precio-lg    { font-size: 1.75rem; }
        .prod-info-row     { flex-direction: column; gap: .15rem; }
        .prod-info-value   { text-align: left; }
    }
</style>
@endpush

@section('content')
<div class="producto-show">

    {{-- ============================================
        HEADER — Avatar, título, barra de acción
        ============================================ --}}
    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div class="ui-header-title">{{ $producto->nombre }}</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-upc-scan me-1"></i>
                        {{ $producto->codigo_barras ?? 'Sin código de barras' }}
                        @if($producto->categoria)
                            <span class="divider">•</span>
                            <i class="bi bi-tag me-1"></i>
                            {{ $producto->categoria->nombre }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('productos.edit')
                <a href="{{ route('productos.edit', $producto) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil-square me-1"></i>Editar
                </a>
                @endcan
                <a href="{{ route('productos.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================
        FILA SUPERIOR: Imagen izquierda + Stats derecha
        ============================================ --}}
    <div class="row g-4 mb-4">

        {{-- ——— Columna izquierda: Imagen + resumen ——— --}}
        <div class="col-lg-5 col-xl-4">
            <div class="ui-card h-100" style="--delay:.1s;">
                <div class="ui-card-accent accent-blue"></div>
                <div class="card-body p-3 p-lg-4">

                    {{-- Imagen del producto --}}
                    <div class="producto-img-wrap mb-3">
                        @if($producto->tiene_imagen)
                            <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}">
                        @else
                            <div class="producto-img-placeholder">
                                <i class="bi bi-box-seam"></i>
                                <span>Sin imagen disponible</span>
                            </div>
                        @endif
                    </div>

                    {{-- Nombre corto + badges --}}
                    <h4 class="fw-bold mb-2 text-truncate">{{ $producto->nombre }}</h4>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="ui-badge ui-badge-{{ $producto->color_badge_activo }}">
                            <i class="bi bi-{{ $producto->activo ? 'check-circle-fill' : 'x-circle-fill' }} me-1"></i>
                            {{ $producto->activo_label }}
                        </span>
                        <span class="ui-badge {{ $producto->estado_stock === 'critical' ? 'ui-badge-danger' : ($producto->estado_stock === 'low' ? 'ui-badge-warning' : 'ui-badge-success') }}">
                            <i class="bi bi-{{ $producto->estado_stock === 'critical' ? 'exclamation-triangle-fill' : ($producto->estado_stock === 'low' ? 'arrow-down-circle' : 'check-circle') }} me-1"></i>
                            {{ $producto->estado_stock === 'critical' ? 'Crítico' : ($producto->estado_stock === 'low' ? 'Bajo' : 'Normal') }}
                        </span>
                    </div>

                    {{-- Stock grande --}}
                    <div class="text-center py-3 mb-3" style="background:rgba(59,130,246,.04);border-radius:var(--radius-lg);border:1px solid rgba(59,130,246,.08);">
                        <div class="text-muted small fw-semibold text-uppercase mb-1" style="letter-spacing:.5px;">
                            <i class="bi bi-box-seam me-1"></i>Stock Actual
                        </div>
                        <div class="producto-stock-lg stock-{{ $producto->estado_stock }}">
                            {{ number_format($producto->stock, 0, '.', ',') }}
                        </div>
                        <div class="text-muted small mt-1">
                            {{ $producto->unidad_medida ?? 'Unidad' }}
                            @if($producto->stock_minimo > 0)
                                · Mín: {{ $producto->stock_minimo }}
                            @endif
                        </div>
                    </div>

                    {{-- Fechas --}}
                    <div class="prod-info-row">
                        <span class="prod-info-label"><i class="bi bi-calendar-plus"></i> Creado</span>
                        <span class="prod-info-value small">{{ $producto->created_at ? $producto->created_at->format('d M Y, h:i A') : '—' }}</span>
                    </div>
                    <div class="prod-info-row">
                        <span class="prod-info-label"><i class="bi bi-calendar-check"></i> Actualizado</span>
                        <span class="prod-info-value small">{{ $producto->updated_at ? $producto->updated_at->format('d M Y, h:i A') : '—' }}</span>
                    </div>

                </div>
            </div>
        </div>

        {{-- ——— Columna derecha: Stats card + Info general ——— --}}
        <div class="col-lg-7 col-xl-8">

            {{-- ——— Sección 6: Estadísticas (stats mini cards arriba) ——— --}}
            <div class="row g-3 mb-4">
                <div class="col-4">
                    <div class="ui-card text-center" style="--delay:.15s;">
                        <div class="ui-card-accent accent-green"></div>
                        <div class="card-body py-3">
                            <i class="bi bi-cart-check mb-2" style="font-size:1.5rem;color:#10b981;"></i>
                            <div class="value fw-bold fs-3" style="color:#10b981;">{{ $producto->detallesCompras->count() }}</div>
                            <div class="label text-muted text-uppercase small fw-semibold" style="font-size:.65rem;">Compras</div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="ui-card text-center" style="--delay:.2s;">
                        <div class="ui-card-accent accent-purple"></div>
                        <div class="card-body py-3">
                            <i class="bi bi-receipt mb-2" style="font-size:1.5rem;color:#8b5cf6;"></i>
                            <div class="value fw-bold fs-3" style="color:#8b5cf6;">{{ $producto->ventaDetalles->count() }}</div>
                            <div class="label text-muted text-uppercase small fw-semibold" style="font-size:.65rem;">Ventas</div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="ui-card text-center" style="--delay:.25s;">
                        <div class="ui-card-accent accent-amber"></div>
                        <div class="card-body py-3">
                            <i class="bi bi-graph-up-arrow mb-2" style="font-size:1.5rem;color:#d97706;"></i>
                            <div class="value fw-bold fs-6" style="color:#d97706;white-space:nowrap;">
                                @php $proyectada = $producto->ganancia * $producto->stock; @endphp
                                @if($proyectada > 0) RD$ {{ number_format($proyectada, 2) }}
                                @elseif($producto->stock > 0) P. Pérdida: RD$ {{ number_format(abs($proyectada), 2) }}
                                @else —
                                @endif
                            </div>
                            <div class="label text-muted text-uppercase small fw-semibold" style="font-size:.65rem;">Ganancia Proyectada</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ——— Sección 1: Información General ——— --}}
            <div class="ui-card mb-4" style="--delay:.3s;">
                <div class="ui-card-accent accent-blue"></div>
                <div class="prod-section-head">
                    <i class="bi bi-info-circle-fill" style="color:#3b82f6;"></i>
                    Información General
                </div>
                <div class="card-body px-3 px-lg-4 pb-3">
                    <div class="row g-3">

                        <div class="col-6 col-md-4">
                            <div class="prod-info-row" style="flex-direction:column;gap:0;">
                                <span class="prod-info-label"><i class="bi bi-tag-fill"></i> Categoría</span>
                                @if($producto->categoria)
                                    <a href="{{ route('categorias.show', $producto->categoria) }}" class="prod-info-value" style="color:#3b82f6;text-decoration:none;">
                                        {{ $producto->categoria->nombre }}
                                    </a>
                                @else
                                    <span class="prod-info-value text-muted">Sin categoría</span>
                                @endif
                            </div>
                        </div>

                        @if($producto->marca)
                        <div class="col-6 col-md-4">
                            <div class="prod-info-row" style="flex-direction:column;gap:0;">
                                <span class="prod-info-label"><i class="bi bi-badge-tm"></i> Marca</span>
                                <span class="prod-info-value">{{ $producto->marca }}</span>
                            </div>
                        </div>
                        @endif

                        @if($producto->marcaTecnologica)
                        <div class="col-6 col-md-4">
                            <div class="prod-info-row" style="flex-direction:column;gap:0;">
                                <span class="prod-info-label"><i class="bi bi-gpu-card"></i> Marca Tec.</span>
                                <a href="{{ route('marcas-tecnologicas.show', $producto->marcaTecnologica) }}" class="prod-info-value" style="color:#8b5cf6;text-decoration:none;">
                                    @if($producto->marcaTecnologica->logo_url)
                                        <img src="{{ asset('storage/' . $producto->marcaTecnologica->logo_url) }}" alt="" style="width:16px;height:16px;object-fit:contain;vertical-align:middle;margin-right:4px;border-radius:3px;">
                                    @endif
                                    {{ $producto->marcaTecnologica->nombre }}
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($producto->especializacion)
                        <div class="col-6 col-md-4">
                            <div class="prod-info-row" style="flex-direction:column;gap:0;">
                                <span class="prod-info-label"><i class="bi bi-grid-3x3-gap"></i> Especialización</span>
                                <span class="prod-info-value text-capitalize">{{ $producto->especializacion }}</span>
                            </div>
                        </div>
                        @endif

                        @if($producto->linea_negocio)
                        <div class="col-6 col-md-4">
                            <div class="prod-info-row" style="flex-direction:column;gap:0;">
                                <span class="prod-info-label"><i class="bi bi-diagram-3"></i> Línea Negocio</span>
                                <span class="prod-info-value text-capitalize">{{ $producto->linea_negocio }}</span>
                            </div>
                        </div>
                        @endif

                        <div class="col-6 col-md-4">
                            <div class="prod-info-row" style="flex-direction:column;gap:0;">
                                <span class="prod-info-label"><i class="bi bi-layers"></i> Tipo Servicio</span>
                                <span class="prod-info-value text-capitalize">{{ $producto->tipo_servicio ?? '—' }}</span>
                            </div>
                        </div>

                        @if($producto->tipo_producto)
                        <div class="col-6 col-md-4">
                            <div class="prod-info-row" style="flex-direction:column;gap:0;">
                                <span class="prod-info-label"><i class="bi bi-box"></i> Tipo Producto</span>
                                <span class="prod-info-value text-capitalize">{{ $producto->tipo_producto }}</span>
                            </div>
                        </div>
                        @endif

                        {{-- Unidad de medida (siempre visible) --}}
                        <div class="col-6 col-md-4">
                            <div class="prod-info-row" style="flex-direction:column;gap:0;">
                                <span class="prod-info-label"><i class="bi bi-rulers"></i> Unidad Medida</span>
                                <span class="prod-info-value">{{ $producto->unidad_medida ?? 'Unidad' }}</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ——— Sección 2: Precios y Rentabilidad ——— --}}
            <div class="ui-card mb-4" style="--delay:.35s;">
                <div class="ui-card-accent accent-green"></div>
                <div class="prod-section-head">
                    <i class="bi bi-currency-dollar" style="color:#10b981;"></i>
                    Precios y Rentabilidad
                </div>
                <div class="card-body px-3 px-lg-4 pb-3">
                    <div class="row g-3">

                        <div class="col-12 col-md-4">
                            <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background:rgba(16,185,129,.05);border:1px solid rgba(16,185,129,.1);">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-tag" style="font-size:1.5rem;color:#10b981;"></i>
                                </div>
                                <div class="w-100">
                                    <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing:.4px;">Precio de Venta</div>
                                    <div class="prod-precio-lg" style="color:#10b981;">RD$ {{ number_format($producto->precio, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <div class="d-flex flex-column gap-1">
                                <span class="prod-info-label"><i class="bi bi-bag-check"></i> Precio Compra</span>
                                <span class="prod-info-value fs-5">RD$ {{ number_format($producto->precio_compra ?? 0, 2) }}</span>
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <div class="d-flex flex-column gap-1">
                                <span class="prod-info-label"><i class="bi bi-graph-up"></i> Ganancia/U</span>
                                <span class="prod-info-value fs-5 {{ $producto->ganancia >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $producto->ganancia >= 0 ? '+' : '-' }}RD$ {{ number_format(abs($producto->ganancia), 2) }}
                                </span>
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <div class="d-flex flex-column gap-1">
                                <span class="prod-info-label"><i class="bi bi-percent"></i> Margen</span>
                                <span class="prod-info-value fs-5" style="color:#06b6d4;">{{ number_format($producto->margen_porcentaje, 2) }}%</span>
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <div class="d-flex flex-column gap-1">
                                <span class="prod-info-label"><i class="bi bi-receipt"></i> ITBIS</span>
                                <span class="prod-info-value fs-5">{{ number_format($producto->itbis_porcentaje ?? ($systemItbis ?? 18), 2) }}%</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ——— Sección 3: Stock y Control ——— --}}
            <div class="ui-card mb-4" style="--delay:.4s;">
                <div class="ui-card-accent accent-blue"></div>
                <div class="prod-section-head">
                    <i class="bi bi-box-seam" style="color:#3b82f6;"></i>
                    Stock y Control
                </div>
                <div class="card-body px-3 px-lg-4 pb-3">
                    <div class="row g-3">

                        <div class="col-6 col-md-3">
                            <div class="d-flex flex-column gap-1">
                                <span class="prod-info-label"><i class="bi bi-box-seam"></i> Stock Actual</span>
                                <span class="prod-info-value fs-4 stock-{{ $producto->estado_stock }}">
                                    {{ number_format($producto->stock, 0, '.', ',') }}
                                </span>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="d-flex flex-column gap-1">
                                <span class="prod-info-label"><i class="bi bi-gauge"></i> Stock Mínimo</span>
                                <span class="prod-info-value fs-5">{{ $producto->stock_minimo ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="d-flex flex-column gap-1">
                                <span class="prod-info-label"><i class="bi bi-rulers"></i> Unidad</span>
                                <span class="prod-info-value fs-5">{{ $producto->unidad_medida ?? 'Unidad' }}</span>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="d-flex flex-column gap-1">
                                <span class="prod-info-label"><i class="bi bi-shield-check"></i> Trazabilidad</span>
                                <div>
                                    @if($producto->requiere_serial || $producto->vendible_imei)
                                        @if($producto->requiere_serial)
                                            <span class="ui-badge ui-badge-info me-1 mb-1">
                                                <i class="bi bi-upc-scan me-1"></i>Serial
                                            </span>
                                        @endif
                                        @if($producto->vendible_imei)
                                            <span class="ui-badge ui-badge-info me-1 mb-1">
                                                <i class="bi bi-phone me-1"></i>IMEI
                                            </span>
                                        @endif
                                    @else
                                        <span class="prod-info-value text-muted small">No aplica</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($producto->serial_imei)
                        <div class="col-12 col-md-6">
                            <div class="d-flex flex-column gap-1">
                                <span class="prod-info-label"><i class="bi bi-qr-code"></i> IMEI / Serial</span>
                                <span class="prod-info-value font-monospace fs-5" style="letter-spacing:.5px;">{{ $producto->serial_imei }}</span>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- ——— Sección 4: Tecnología (solo si aplica) ——— --}}
            @if($producto->modelo || $producto->garantia_dias || $producto->es_licencia)
            <div class="ui-card mb-4" style="--delay:.45s;">
                <div class="ui-card-accent accent-purple"></div>
                <div class="prod-section-head">
                    <i class="bi bi-gpu-card" style="color:#8b5cf6;"></i>
                    Tecnología
                </div>
                <div class="card-body px-3 px-lg-4 pb-3">
                    <div class="row g-3">

                        @if($producto->modelo)
                        <div class="col-6 col-md-4">
                            <div class="d-flex flex-column gap-1">
                                <span class="prod-info-label"><i class="bi bi-card-list"></i> Modelo</span>
                                <span class="prod-info-value">{{ $producto->modelo }}</span>
                            </div>
                        </div>
                        @endif

                        @if($producto->garantia_dias)
                        <div class="col-6 col-md-4">
                            <div class="d-flex flex-column gap-1">
                                <span class="prod-info-label"><i class="bi bi-shield-plus"></i> Garantía</span>
                                <span class="prod-info-value">
                                    {{ $producto->garantia_dias }} {{ $producto->garantia_dias == 1 ? 'día' : 'días' }}
                                    @if($producto->garantia_dias >= 90)
                                        <span class="ui-badge ui-badge-info ms-2" style="font-size:.65rem;">
                                            <i class="bi bi-star me-1"></i>Extendida
                                        </span>
                                    @elseif($producto->garantia_dias >= 30)
                                        <span class="ui-badge ui-badge-success ms-2" style="font-size:.65rem;">
                                            <i class="bi bi-check-lg me-1"></i>Estándar
                                        </span>
                                    @else
                                        <span class="ui-badge ui-badge-warning ms-2" style="font-size:.65rem;">
                                            <i class="bi bi-exclamation-triangle me-1"></i>Corta
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        @endif

                        @if($producto->es_licencia)
                        <div class="col-12 col-md-4">
                            <div class="p-3 rounded-3" style="background:rgba(139,92,246,.05);border:1px solid rgba(139,92,246,.1);">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-key" style="font-size:1.25rem;color:#8b5cf6;"></i>
                                    <span class="fw-bold text-uppercase" style="font-size:.7rem;color:#8b5cf6;letter-spacing:.5px;">Es Licencia</span>
                                </div>
                                <div class="d-flex flex-column gap-1">
                                    <span class="prod-info-label small">Tipo de Licencia</span>
                                    <span class="prod-info-value text-capitalize">{{ $producto->tipo_licencia ?? '—' }}</span>
                                    @if($producto->licencia_max_usuarios)
                                        <span class="prod-info-label small">Máx. Usuarios</span>
                                        <span class="prod-info-value">{{ $producto->licencia_max_usuarios }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
            @endif

            {{-- ——— Sección 5: Descripciones (solo si aplica) ——— --}}
            @if($producto->descripcion || $producto->especializacion)
            <div class="ui-card mb-4" style="--delay:.5s;">
                <div class="ui-card-accent accent-amber"></div>
                <div class="prod-section-head">
                    <i class="bi bi-card-text" style="color:#d97706;"></i>
                    Descripciones
                </div>
                <div class="card-body px-3 px-lg-4 pb-3">

                    @if($producto->descripcion)
                    <div class="mb-3">
                        <span class="prod-info-label mb-2 d-block"><i class="bi bi-text-paragraph"></i> Descripción del Producto</span>
                        <div class="prod-desc-body">
                            {{ $producto->descripcion }}
                        </div>
                    </div>
                    @endif

                    @if($producto->especializacion)
                    <div>
                        <span class="prod-info-label mb-2 d-block"><i class="bi bi-grid-3x3-gap"></i> Especialización</span>
                        <div class="prod-desc-body">
                            {{ $producto->especializacion }}
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
