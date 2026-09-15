@extends('layouts.app')
@section('title', 'Impacto de Precios — ' . $listaPrecio->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
    .difference-positive { color: #198754; }
    .difference-negative { color: #dc3545; }
    .difference-neutral { color: #6c757d; }
    body.dark-mode .ui-detail-row { border-bottom-color: #1e293b; }
</style>
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
                    <i class="bi bi-graph-up"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Impacto de Precios</h4>
                    <div class="ui-header-meta">{{ $listaPrecio->nombre }}</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('listas-precio.show', $listaPrecio) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-3">
        <div class="col-md-3 col-sm-6">
            <div class="ui-stat p-3" style="--delay:.05s">
                <div class="ui-card-accent"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(13,202,240,0.1);color:#0dcaf0;font-size:1.4rem;">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <div class="ui-stat-value text-info">{{ $totalProductos }}</div>
                        <div class="ui-stat-label">Productos</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="ui-stat p-3" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(108,117,125,0.1);color:#6c757d;font-size:1.4rem;">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <div class="ui-stat-value text-secondary">RD$ {{ number_format($sumaPreciosBase, 2) }}</div>
                        <div class="ui-stat-label">Costo Total</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="ui-stat p-3" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(13,110,253,0.1);color:#0d6efd;font-size:1.4rem;">
                        <i class="bi bi-tag"></i>
                    </div>
                    <div>
                        <div class="ui-stat-value" style="color:#0d6efd;">RD$ {{ number_format($sumaPreciosLista, 2) }}</div>
                        <div class="ui-stat-label">Precio Lista</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="ui-stat p-3" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:{{ $diferencia >= 0 ? 'rgba(25,135,84,0.1)' : 'rgba(220,53,69,0.1)' }};color:{{ $diferencia >= 0 ? '#198754' : '#dc3545' }};font-size:1.4rem;">
                        <i class="bi bi-{{ $diferencia >= 0 ? 'arrow-down-right' : 'arrow-up-right' }}"></i>
                    </div>
                    <div>
                        <div class="ui-stat-value difference-{{ $diferencia > 0 ? 'positive' : ($diferencia < 0 ? 'negative' : 'neutral') }}">
                            RD$ {{ number_format(abs($diferencia), 2) }}
                        </div>
                        <div class="ui-stat-label">{{ $diferencia >= 0 ? 'Ahorro Cliente' : 'Pérdida Empresa' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-pie-chart me-2"></i>
                    Resumen Financiero
                </div>
                <div class="card-body">
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Suma precios base (costo)</span>
                        <span class="ui-detail-value fw-semibold text-secondary">RD$ {{ number_format($sumaPreciosBase, 2) }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Suma precios lista</span>
                        <span class="ui-detail-value fw-semibold" style="color: #0d6efd;">RD$ {{ number_format($sumaPreciosLista, 2) }}</span>
                    </div>
                    <div class="ui-detail-row" style="border-top: 2px solid rgba(0,0,0,0.08); padding-top: 1rem; margin-top: 0.5rem;">
                        <span class="ui-detail-label fw-bold">Diferencia</span>
                        <span class="ui-detail-value difference-{{ $diferencia > 0 ? 'positive' : ($diferencia < 0 ? 'negative' : 'neutral') }}">
                            {{ $diferencia > 0 ? '-' : '+' }}RD$ {{ number_format(abs($diferencia), 2) }}
                        </span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label fw-bold">Descuento Promedio</span>
                        <span class="ui-detail-value difference-{{ $porcentajeDescuento > 0 ? 'positive' : ($porcentajeDescuento < 0 ? 'negative' : 'neutral') }}">
                            {{ $porcentajeDescuento > 0 ? '-' : '+' }}{{ number_format(abs($porcentajeDescuento), 1) }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="ui-card h-100" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi {{ $diferencia >= 0 ? 'bi-check-circle' : 'bi-info-circle' }} me-2"></i>
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
                        <a href="{{ route('listas-precio.edit', $listaPrecio) }}" class="ui-btn ui-btn-ghost rounded-pill w-100">
                            <i class="bi bi-pencil me-1"></i>Editar Precios
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-table me-2"></i>
                    Desglose por Producto
                </div>
                <div class="ui-card-subtitle">Detalle del impacto por cada producto en la lista</div>
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
                                    <td colspan="6">
                                        <div class="ui-empty-state py-4">
                                            <i class="bi bi-box-seam ui-empty-state-icon"></i>
                                            <p class="ui-empty-state-text">No hay productos en esta lista.</p>
                                        </div>
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