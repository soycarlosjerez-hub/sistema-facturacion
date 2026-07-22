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
    .action-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        border-radius: 0.75rem;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .action-link:hover {
        transform: translateX(3px);
        text-decoration: none;
    }
    .action-link-warning {
        background: rgba(255,193,7,0.1);
        color: #b8860b;
        border: 1px solid rgba(255,193,7,0.3);
    }
    .action-link-warning:hover {
        background: rgba(255,193,7,0.2);
    }
    .action-link-secondary {
        background: rgba(108,117,125,0.1);
        color: #495057;
        border: 1px solid rgba(108,117,125,0.3);
    }
    .action-link-secondary:hover {
        background: rgba(108,117,125,0.2);
    }
    .margin-positive { color: #198754; font-weight: 600; }
    .margin-negative { color: #dc3545; font-weight: 600; }
    .margin-zero { color: #6c757d; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="premium-page">
    <div class="container-fluid px-4">
        <div class="premium-header">
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
                <div class="d-flex align-items-center gap-3">
                    <div class="premium-avatar-circle">
                        <i class="bi bi-tag"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1">{{ $listaPrecio->nombre }}</h2>
                        <p class="mb-0 opacity-75">{{ $listaPrecio->codigo }} &middot; {{ $listaPrecio->items->count() }} productos</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('listas-precio.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold me-2">
                        <i class="bi bi-arrow-left me-2"></i>Volver
                    </a>
                    @can('listas-precio.edit')
                    <a href="{{ route('listas-precio.edit', $listaPrecio) }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" style="background: linear-gradient(135deg, #8b5cf6, #a855f7); border: none;">
                        <i class="bi bi-pencil me-2"></i>Editar
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="premium-card">
                    <div class="card-accent purple"></div>
                    <div class="p-4 pb-0">
                        <h5 class="fw-bold mb-0"><i class="bi bi-box-seam me-2" style="color: #8b5cf6;"></i>Productos en esta lista</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tablaPrecios">
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
                                @php $itemsList = $listaPrecio->items; @endphp
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
                <div class="premium-card mb-3">
                    <div class="card-accent purple"></div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-gear me-2" style="color: #8b5cf6;"></i>Informaci&oacute;n</h6>
                        <div class="small">
                            <div class="mb-2"><span class="text-muted">Estado:</span>
                                @if($listaPrecio->activa)
                                    <span class="badge bg-success rounded-pill">Activa</span>
                                @else
                                    <span class="badge bg-danger rounded-pill">Inactiva</span>
                                @endif
                            </div>
                            @if($listaPrecio->vigencia_desde)
                            <div class="mb-2"><span class="text-muted">Vigencia:</span> {{ $listaPrecio->vigencia_desde->format('d/m/Y') }} - {{ $listaPrecio->vigencia_hasta?->format('d/m/Y') ?? 'Indefinida' }}</div>
                            @endif
                            @if($listaPrecio->descripcion)
                            <div class="mb-2"><span class="text-muted">Descripci&oacute;n:</span> {{ $listaPrecio->descripcion }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="premium-card">
                    <div class="card-accent purple"></div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-lightning me-2" style="color: #8b5cf6;"></i>Herramientas</h6>
                        <div class="d-grid gap-2">
                            @can('listas-precio.edit')
                            <a href="{{ route('listas-precio.impacto', $listaPrecio) }}" class="action-link action-link-warning">
                                <i class="bi bi-graph-up"></i> Impacto de Precios
                            </a>
                            <a href="{{ route('listas-precio.logs', $listaPrecio) }}" class="action-link action-link-secondary">
                                <i class="bi bi-clock-history"></i> Historial de Cambios
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
