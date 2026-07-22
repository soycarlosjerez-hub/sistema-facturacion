@extends('layouts.app')
@section('title', 'Impacto de Precios — ' . $listaPrecio->nombre)

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
    .difference-positive { color: #198754; }
    .difference-negative { color: #dc3545; }
    .difference-neutral { color: #6c757d; }
    body.dark-mode .premium-detail-row { border-bottom-color: #1e293b; }
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
                <div class="premium-avatar-circle" style="background: rgba(139,92,246,0.2);">
                    <i class="bi bi-graph-up" style="color: #fff;"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Impacto de Precios</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-graph-up me-1"></i>
                        {{ $listaPrecio->nombre }}
                    </small>
                </div>
            </div>
            <a href="{{ route('listas-precio.show', $listaPrecio) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row g-4 mt-3">
        <div class="col-md-3 col-sm-6">
            <div class="premium-stat-card p-3" style="animation-delay:.05s;">
                <div class="card-accent purple"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(13,202,240,0.1);color:#0dcaf0;font-size:1.4rem;">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <div class="stat-value text-info">{{ $totalProductos }}</div>
                        <div class="stat-label">Productos</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="premium-stat-card p-3" style="animation-delay:.1s;">
                <div class="card-accent purple"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(108,117,125,0.1);color:#6c757d;font-size:1.4rem;">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <div class="stat-value text-secondary">RD$ {{ number_format($sumaPreciosBase, 2) }}</div>
                        <div class="stat-label">Costo Total</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="premium-stat-card p-3" style="animation-delay:.15s;">
                <div class="card-accent purple"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(13,110,253,0.1);color:#0d6efd;font-size:1.4rem;">
                        <i class="bi bi-tag"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="color:#0d6efd;">RD$ {{ number_format($sumaPreciosLista, 2) }}</div>
                        <div class="stat-label">Precio Lista</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="premium-stat-card p-3" style="animation-delay:.2s;">
                <div class="card-accent {{ $diferencia >= 0 ? 'green' : 'red' }}"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:{{ $diferencia >= 0 ? 'rgba(25,135,84,0.1)' : 'rgba(220,53,69,0.1)' }};color:{{ $diferencia >= 0 ? '#198754' : '#dc3545' }};font-size:1.4rem;">
                        <i class="bi bi-{{ $diferencia >= 0 ? 'arrow-down-right' : 'arrow-up-right' }}"></i>
                    </div>
                    <div>
                        <div class="stat-value difference-{{ $diferencia > 0 ? 'positive' : ($diferencia < 0 ? 'negative' : 'neutral') }}">
                            RD$ {{ number_format(abs($diferencia), 2) }}
                        </div>
                        <div class="stat-label">{{ $diferencia >= 0 ? 'Ahorro Cliente' : 'Pérdida Empresa' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="premium-card h-100" style="animation-delay:.1s;">
                <div class="card-accent amber"></div>
                <div class="premium-card-title">
                    <i class="bi bi-pie-chart icon-amber"></i>
                    Resumen Financiero
                </div>
                <div class="card-body">
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Suma precios base (costo)</div>
                        <div class="premium-detail-value fw-semibold text-secondary">RD$ {{ number_format($sumaPreciosBase, 2) }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Suma precios lista</div>
                        <div class="premium-detail-value fw-semibold" style="color: #0d6efd;">RD$ {{ number_format($sumaPreciosLista, 2) }}</div>
                    </div>
                    <div class="premium-detail-row" style="border-top: 2px solid rgba(0,0,0,0.08); padding-top: 1rem; margin-top: 0.5rem;">
                        <div class="premium-detail-label fw-bold">Diferencia</div>
                        <div class="premium-detail-value difference-{{ $diferencia > 0 ? 'positive' : ($diferencia < 0 ? 'negative' : 'neutral') }}">
                            {{ $diferencia > 0 ? '-' : '+' }}RD$ {{ number_format(abs($diferencia), 2) }}
                        </div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label fw-bold">Descuento Promedio</div>
                        <div class="premium-detail-value difference-{{ $porcentajeDescuento > 0 ? 'positive' : ($porcentajeDescuento < 0 ? 'negative' : 'neutral') }}">
                            {{ $porcentajeDescuento > 0 ? '-' : '+' }}{{ number_format(abs($porcentajeDescuento), 1) }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="premium-card h-100" style="animation-delay:.15s;">
                <div class="card-accent {{ $diferencia >= 0 ? 'green' : 'blue' }}"></div>
                <div class="premium-card-title">
                    <i class="bi {{ $diferencia >= 0 ? 'bi-check-circle' : 'bi-info-circle' }} icon-{{ $diferencia >= 0 ? 'green' : 'blue' }}"></i>
                    Análisis
                </div>
                <div class="card-body">
                    @if($diferencia > 0)
                    <div class="alert alert-success d-flex align-items-start gap-2 mb-3" style="background: rgba(25,135,84,0.08); border-color: rgba(25,135,84,0.2);">
                        <i class="bi bi-check-circle-fill mt-1"></i>
                        <div>
                            <strong>Precios competitivos:</strong> Esta lista ofrece un ahorro promedio del {{ number_format($porcentajeDescuento, 1) }}% respecto al costo. Es atractiva para clientes mayoristas o promocionales.
                        </div>
                    </div>
                    <div class="small text-muted">
                        <i class="bi bi-lightbulb me-1"></i>
                        Verifique que los márgenes cubran costos operativos y gastos generales.
                    </div>
                    @elseif($diferencia < 0)
                    <div class="alert alert-danger d-flex align-items-start gap-2 mb-3" style="background: rgba(220,53,69,0.08); border-color: rgba(220,53,69,0.2);">
                        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                        <div>
                            <strong>Precios por encima del costo:</strong> Esta lista tiene un incremento promedio del {{ number_format(abs($porcentajeDescuento), 1) }}% respecto al costo. Verifique si corresponde a una lista especial (clientes VIP, urgencias, etc.).
                        </div>
                    </div>
                    <div class="small text-muted">
                        <i class="bi bi-lightbulb me-1"></i>
                        Considere ajustar precios si superan el margen objetivo.
                    </div>
                    @else
                    <div class="alert alert-secondary d-flex align-items-start gap-2 mb-3" style="background: rgba(108,117,125,0.08); border-color: rgba(108,117,125,0.2);">
                        <i class="bi bi-info-circle-fill mt-1"></i>
                        <div>
                            <strong>Sin diferencia:</strong> Los precios de lista son iguales a los costos. No hay margen de ganancia en esta lista.
                        </div>
                    </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('listas-precio.edit', $listaPrecio) }}" class="btn btn-outline-primary w-100 rounded-pill btn-sm">
                            <i class="bi bi-pencil me-1"></i>Editar Precios
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="premium-card" style="animation-delay:.2s;">
                <div class="card-accent purple"></div>
                <div class="premium-card-title">
                    <i class="bi bi-table icon-purple"></i>
                    Desglose por Producto
                </div>
                <div class="premium-card-subtitle">Detalle del impacto por cada producto en la lista</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width: 50px;">#</th>
                                    <th>Producto</th>
                                    <th class="text-end">Costo</th>
                                    <th class="text-end">Precio Lista</th>
                                    <th class="text-end">Diferencia</th>
                                    <th class="text-end">% Var.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $itemsWithProducts = $listaPrecio->items->map(function($item) {
                                        return [
                                            'producto' => $item->producto,
                                            'precio_lista' => (float)$item->precio,
                                            'costo' => (float)($item->producto->precio_compra ?? 0),
                                        ];
                                    })->sortBy('producto.nombre')->values();
                                @endphp
                                @forelse($itemsWithProducts as $idx => $row)
                                @php
                                    $diff = $row['precio_lista'] - $row['costo'];
                                    $pct = $row['costo'] > 0 ? (($diff / $row['costo']) * 100) : 0;
                                    $diffClass = $diff > 0 ? 'difference-positive' : ($diff < 0 ? 'difference-negative' : 'difference-neutral');
                                @endphp
                                <tr>
                                    <td class="text-muted small">{{ $idx + 1 }}</td>
                                    <td class="fw-bold small">{{ $row['producto']->nombre ?? 'Eliminado' }}</td>
                                    <td class="text-end text-muted">RD$ {{ number_format($row['costo'], 2) }}</td>
                                    <td class="text-end fw-bold" style="color: #0d6efd;">RD$ {{ number_format($row['precio_lista'], 2) }}</td>
                                    <td class="text-end {{ $diffClass }}">{{ $diff > 0 ? '-' : '+' }}RD$ {{ number_format(abs($diff), 2) }}</td>
                                    <td class="text-end {{ $diffClass }}">{{ $pct > 0 ? '-' : '+' }}{{ number_format(abs($pct), 1) }}%</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No hay productos en esta lista.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="ps-3 fw-bold">Totales</td>
                                    <td class="text-end fw-bold text-secondary">RD$ {{ number_format($sumaPreciosBase, 2) }}</td>
                                    <td class="text-end fw-bold" style="color: #0d6efd;">RD$ {{ number_format($sumaPreciosLista, 2) }}</td>
                                    <td class="text-end fw-bold {{ $diferencia > 0 ? 'difference-positive' : ($diferencia < 0 ? 'difference-negative' : 'difference-neutral') }}">
                                        {{ $diferencia > 0 ? '-' : '+' }}RD$ {{ number_format(abs($diferencia), 2) }}
                                    </td>
                                    <td class="text-end fw-bold {{ $porcentajeDescuento > 0 ? 'difference-positive' : ($porcentajeDescuento < 0 ? 'difference-negative' : 'difference-neutral') }}">
                                        {{ $porcentajeDescuento > 0 ? '-' : '+' }}{{ number_format(abs($porcentajeDescuento), 1) }}%
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection