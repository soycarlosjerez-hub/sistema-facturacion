@extends('layouts.app')

@section('title', $listaPrecio->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
    .premium-header {
        background: linear-gradient(135deg, #8b5cf6, #a855f7, #7c3aed, #8b5cf6);
        background-size: 300% 300%;
        box-shadow: 0 8px 32px rgba(139,92,246,.25);
    }
    .premium-header::before {
        background:
            radial-gradient(circle at 30% 44%, rgba(255,255,255,.12) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.08) 0%, transparent 50%);
    }
    .margin-positive { color: #198754; font-weight: 600; }
    .margin-negative { color: #dc3545; font-weight: 600; }
    .margin-zero { color: #6c757d; font-weight: 600; }
    .precios-show-table thead th {
        background: rgba(241,245,249,.8);
        color: #64748b;
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-weight: 700;
        padding: .85rem 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .precios-show-table tbody td {
        padding: .85rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: .9rem;
    }
    .precios-show-table tbody tr:last-child td { border-bottom: none; }
    body.dark-mode .precios-show-table thead th {
        background: rgba(15,23,42,.5);
        color: #94a3b8;
        border-color: #1e293b;
    }
    body.dark-mode .precios-show-table tbody td {
        border-bottom-color: #1e293b;
        color: #cbd5e1;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-tag"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">{{ $listaPrecio->nombre }}</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-upc-scan me-1"></i>
                        {{ $listaPrecio->codigo }} &middot; {{ $listaPrecio->items->count() }} productos
                    </small>
                </div>
            </div>
            <div class="d-flex gap-2">
                @can('listas-precio.edit')
                <a href="{{ route('listas-precio.edit', $listaPrecio) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(245,158,11,.2);border:1.5px solid rgba(245,158,11,.35);color:#fff;">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                @endcan
                <a href="{{ route('listas-precio.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @php
        $itemsList = $listaPrecio->items;
        $precioPromedio = $itemsList->avg('precio');
        $precioMin = $itemsList->min('precio');
        $precioMax = $itemsList->max('precio');
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="premium-stat-card p-3" style="animation-delay:.05s;">
                <div class="card-accent purple"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(139,92,246,0.1);color:#8b5cf6;font-size:1.4rem;">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="color:#8b5cf6;">{{ $itemsList->count() }}</div>
                        <div class="stat-label">Total Productos</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="premium-stat-card p-3" style="animation-delay:.1s;">
                <div class="card-accent purple"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(13,202,240,0.1);color:#0dcaf0;font-size:1.4rem;">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <div class="stat-value text-info">RD$ {{ number_format($precioPromedio ?? 0, 0) }}</div>
                        <div class="stat-label">Precio Promedio</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="premium-stat-card p-3" style="animation-delay:.15s;">
                <div class="card-accent purple"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(25,135,84,0.1);color:#198754;font-size:1.4rem;">
                        <i class="bi bi-arrow-down-circle"></i>
                    </div>
                    <div>
                        <div class="stat-value text-success">RD$ {{ number_format($precioMin ?? 0, 0) }}</div>
                        <div class="stat-label">Precio M&iacute;nimo</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="premium-stat-card p-3" style="animation-delay:.2s;">
                <div class="card-accent purple"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(220,53,69,0.1);color:#dc3545;font-size:1.4rem;">
                        <i class="bi bi-arrow-up-circle"></i>
                    </div>
                    <div>
                        <div class="stat-value text-danger">RD$ {{ number_format($precioMax ?? 0, 0) }}</div>
                        <div class="stat-label">Precio M&aacute;ximo</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="premium-card" style="animation-delay:.1s;">
                <div class="card-accent purple"></div>
                <div class="premium-card-title">
                    <i class="bi bi-box-seam icon-purple"></i>
                    Productos en esta lista
                </div>
                <div class="premium-card-subtitle">{{ $itemsList->count() }} productos configurados</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 precios-show-table" id="tablaPrecios">
                        <thead class="table-light">
                            <tr class="text-muted text-uppercase small">
                                <th class="ps-4">C&oacute;digo</th>
                                <th>Producto</th>
                                <th class="text-end">Costo</th>
                                <th class="text-end">Margen %</th>
                                <th class="text-end">Precio Actual</th>
                                <th class="text-end pe-4">Precio Lista</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($itemsList as $item)
                            @php
                                $producto = $item->producto;
                                $costo = $producto ? (float) ($producto->precio_compra ?? 0) : 0;
                                $precioLista = (float) $item->precio;
                                $margen = $costo > 0 ? ((($precioLista - $costo) / $costo) * 100) : 0;
                                $marginClass = $margen > 0 ? 'margin-positive' : ($margen < 0 ? 'margin-negative' : 'margin-zero');
                                $marginSign = $margen > 0 ? '+' : '';
                            @endphp
                            <tr>
                                <td class="ps-4"><span class="badge bg-light text-muted rounded-pill">{{ $producto?->codigo ?? $producto?->codigo_barras ?? '&mdash;' }}</span></td>
                                <td class="fw-bold small">{{ $producto?->nombre ?? 'Producto Eliminado' }}</td>
                                <td class="text-end text-muted">RD$ {{ number_format($costo, 2) }}</td>
                                <td class="text-end {{ $marginClass }}">
                                    @if($costo > 0)
                                        {{ $marginSign }}{{ number_format($margen, 1) }}%
                                    @else
                                        &mdash;
                                    @endif
                                </td>
                                <td class="text-end text-muted">RD$ {{ number_format($producto?->precio ?? 0, 2) }}</td>
                                <td class="text-end pe-4 fw-bold text-success">
                                    RD$ {{ number_format($precioLista, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Esta lista a&uacute;n no tiene productos configurados.
                                    @can('listas-precio.edit')
                                    <br><a href="{{ route('listas-precio.edit', $listaPrecio) }}" class="btn btn-sm btn-outline-primary mt-2" style="border-color: #8b5cf6; color: #8b5cf6;">Agregar productos</a>
                                    @endcan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="premium-card mb-3" style="animation-delay:.15s;">
                <div class="card-accent purple"></div>
                <div class="premium-card-title">
                    <i class="bi bi-gear icon-purple"></i>
                    Informaci&oacute;n
                </div>
                <div class="card-body">
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Estado</div>
                        <div class="premium-detail-value">
                            @if($listaPrecio->activa)
                                <span class="badge bg-success rounded-pill">Activa</span>
                            @else
                                <span class="badge bg-danger rounded-pill">Inactiva</span>
                            @endif
                        </div>
                    </div>
                    @if($listaPrecio->vigencia_desde)
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Vigencia</div>
                        <div class="premium-detail-value">{{ $listaPrecio->vigencia_desde->format('d/m/Y') }} - {{ $listaPrecio->vigencia_hasta?->format('d/m/Y') ?? 'Indefinida' }}</div>
                    </div>
                    @endif
                    @if($listaPrecio->descripcion)
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Descripci&oacute;n</div>
                        <div class="premium-detail-value">{{ $listaPrecio->descripcion }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="premium-card" style="animation-delay:.2s;">
                <div class="card-accent purple"></div>
                <div class="premium-card-title">
                    <i class="bi bi-lightning icon-purple"></i>
                    Herramientas
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('listas-precio.edit')
                        <a href="{{ route('listas-precio.impacto', $listaPrecio) }}" class="btn btn-outline-warning w-100 rounded-pill btn-sm">
                            <i class="bi bi-graph-up me-1"></i> Impacto de Precios
                        </a>
                        <a href="{{ route('listas-precio.logs', $listaPrecio) }}" class="btn btn-outline-secondary w-100 rounded-pill btn-sm">
                            <i class="bi bi-clock-history me-1"></i> Historial de Cambios
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection