@extends('layouts.app')
@section('title', 'Impacto de Precios — ' . $listaPrecio->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
    .premium-header {
        background: linear-gradient(135deg, #f59e0b, #fbbf24, #d97706, #f59e0b);
        background-size: 300% 300%;
        box-shadow: 0 8px 32px rgba(245,158,11,.25);
    }
    .premium-header::before {
        background:
            radial-gradient(circle at 30% 44%, rgba(255,255,255,.12) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.08) 0%, transparent 50%);
    }
    .impact-stat-card {
        border-radius: 1rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        transition: all 0.2s ease;
    }
    .impact-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
    .impact-stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        font-family: 'SF Mono', 'Fira Code', monospace;
    }
    .impact-stat-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }
    .comparison-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.85rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    .comparison-row:last-child {
        border-bottom: none;
    }
    .comparison-label {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .comparison-value {
        font-weight: 600;
        font-family: 'SF Mono', 'Fira Code', monospace;
    }
    .difference-positive { color: #198754; }
    .difference-negative { color: #dc3545; }
    .difference-neutral { color: #6c757d; }
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
                    <div class="premium-avatar-circle" style="background: rgba(245,158,11,0.2);">
                        <i class="bi bi-graph-up" style="color: #fff;"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-0 text-white">Impacto de Precios</h2>
                        <p class="text-white text-opacity-75 mb-0">{{ $listaPrecio->nombre }}</p>
                    </div>
                </div>
                <a href="{{ route('listas-precio.show', $listaPrecio) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>

        <div class="row g-4 mt-3">
            <div class="col-md-3 col-sm-6">
                <div class="impact-stat-card p-4" style="background: rgba(13,202,240,0.08); border-color: rgba(13,202,240,0.2);">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <i class="bi bi-box-seam fs-4" style="color: #0dcaf0;"></i>
                        <span class="impact-stat-label text-info">Productos</span>
                    </div>
                    <div class="impact-stat-value text-info">{{ $totalProductos }}</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="impact-stat-card p-4" style="background: rgba(108,117,125,0.08); border-color: rgba(108,117,125,0.2);">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <i class="bi bi-receipt fs-4" style="color: #6c757d;"></i>
                        <span class="impact-stat-label text-secondary">Costo Total</span>
                    </div>
                    <div class="impact-stat-value text-secondary">RD$ {{ number_format($sumaPreciosBase, 2) }}</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="impact-stat-card p-4" style="background: rgba(13,110,253,0.08); border-color: rgba(13,110,253,0.2);">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <i class="bi bi-tag fs-4" style="color: #0d6efd;"></i>
                        <span class="impact-stat-label" style="color: #0d6efd;">Precio Lista</span>
                    </div>
                    <div class="impact-stat-value" style="color: #0d6efd;">RD$ {{ number_format($sumaPreciosLista, 2) }}</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="impact-stat-card p-4" style="background: {{ $diferencia >= 0 ? 'rgba(25,135,84,0.08)' : 'rgba(220,53,69,0.08)' }}; border-color: {{ $diferencia >= 0 ? 'rgba(25,135,84,0.2)' : 'rgba(220,53,69,0.2)' }};">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <i class="bi bi-{{ $diferencia >= 0 ? 'arrow-down-right' : 'arrow-up-right' }} fs-4" style="color: {{ $diferencia >= 0 ? '#198754' : '#dc3545' }};"></i>
                        <span class="impact-stat-label" style="color: {{ $diferencia >= 0 ? '#198754' : '#dc3545' }};">{{ $diferencia >= 0 ? 'Ahorro Cliente' : 'Pérdida Empresa' }}</span>
                    </div>
                    <div class="impact-stat-value difference-{{ $diferencia > 0 ? 'positive' : ($diferencia < 0 ? 'negative' : 'neutral') }}">
                        RD$ {{ number_format(abs($diferencia), 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <div class="premium-card h-100">
                    <div class="card-accent amber"></div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4">
                            <i class="bi bi-pie-chart me-2" style="color: #f59e0b;"></i>Resumen Financiero
                        </h6>

                        <div class="comparison-row">
                            <span class="comparison-label">Suma precios base (costo)</span>
                            <span class="comparison-value text-secondary">RD$ {{ number_format($sumaPreciosBase, 2) }}</span>
                        </div>
                        <div class="comparison-row">
                            <span class="comparison-label">Suma precios lista</span>
                            <span class="comparison-value" style="color: #0d6efd;">RD$ {{ number_format($sumaPreciosLista, 2) }}</span>
                        </div>
                        <div class="comparison-row" style="border-top: 2px solid rgba(0,0,0,0.08); padding-top: 1rem; margin-top: 0.5rem;">
                            <span class="comparison-label fw-bold">Diferencia</span>
                            <span class="comparison-value difference-{{ $diferencia > 0 ? 'positive' : ($diferencia < 0 ? 'negative' : 'neutral') }}">
                                {{ $diferencia > 0 ? '-' : '+' }}RD$ {{ number_format(abs($diferencia), 2) }}
                            </span>
                        </div>
                        <div class="comparison-row">
                            <span class="comparison-label fw-bold">Descuento Promedio</span>
                            <span class="comparison-value difference-{{ $porcentajeDescuento > 0 ? 'positive' : ($porcentajeDescuento < 0 ? 'negative' : 'neutral') }}">
                                {{ $porcentajeDescuento > 0 ? '-' : '+' }}{{ number_format(abs($porcentajeDescuento), 1) }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="premium-card h-100">
                    <div class="card-accent {{ $diferencia >= 0 ? 'green' : 'blue' }}"></div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4">
                            <i class="bi bi-info-circle me-2" style="color: {{ $diferencia >= 0 ? '#198754' : '#0d6efd' }};"></i>Análisis
                        </h6>

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

        {{-- Product breakdown table --}}
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="premium-card">
                    <div class="card-accent purple"></div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-table me-2" style="color: #8b5cf6;"></i>Desglose por Producto
                        </h6>
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
</div>
@endsection
