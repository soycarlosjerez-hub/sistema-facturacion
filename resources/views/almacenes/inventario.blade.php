@extends('layouts.app')
@section('title', 'Inventario por Almacén')
@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-box-seam me-2"></i>Inventario por Almacén
        </h2>
        @php
            $totalProductos = 0;
            $totalValorGeneral = 0;
            foreach ($almacenes as $alm) {
                if ($almacenId && $almacenId != $alm->id) continue;
                foreach ($stocks->get($alm->id, collect()) as $it) {
                    if ((int)$it->stock <= 0) continue;
                    $p = $productos->firstWhere('id', $it->producto_id);
                    if (!$p) continue;
                    $totalProductos++;
                    $totalValorGeneral += $it->stock * ($p->precio_compra ?? 0);
                }
            }
        @endphp
        <div class="d-flex gap-3">
            <div class="text-end">
                <small class="text-muted d-block">Productos</small>
                <span class="fw-bold fs-5">{{ $totalProductos }}</span>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">Valor Total</small>
                <span class="fw-bold fs-5 text-success">RD$ {{ number_format($totalValorGeneral, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="buscar" id="buscar-instant" class="form-control border-start-0 ps-0" placeholder="Buscar producto por nombre o código..." value="{{ $buscar }}" autocomplete="off">
            </div>
        </div>
        <div class="col-lg-3 col-md-4">
            <select name="almacen_id" class="form-select" onchange="this.form.submit()">
                <option value="">Todos los almacenes</option>
                @foreach($almacenes as $a)
                    <option value="{{ $a->id }}" {{ $almacenId == $a->id ? 'selected' : '' }}>{{ $a->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-2">
            <button class="btn btn-primary w-100 d-none d-lg-inline-block"><i class="bi bi-funnel me-1"></i>Filtrar</button>
        </div>
        <div class="col-lg-2 col-md-2">
            <a href="{{ route('almacenes.inventario') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
        </div>
    </form>

    {{-- Cards por almacén --}}
    @foreach($almacenes as $almacen)
        @if($almacenId && $almacenId != $almacen->id) @continue @endif
        @php
            $items = $stocks->get($almacen->id, collect());
            $totalValor = 0;
            $totalUnidades = 0;
        @endphp
        <div class="card shadow-sm rounded-4 border-0 mb-4 overflow-hidden">
            {{-- Card header con color de sucursal --}}
            <div class="card-header border-0 py-3" style="background: linear-gradient(135deg, #0d6efd0d, #0d6efd1a);">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-building me-2 text-primary"></i>{{ $almacen->nombre }}
                        </h5>
                        @if($almacen->sucursal)
                            <small class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i>{{ $almacen->sucursal->nombre }}
                                @if($almacen->ubicacion) · {{ $almacen->ubicacion }} @endif
                            </small>
                        @endif
                    </div>
                    <div class="d-flex gap-3 mt-2 mt-sm-0">
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill fs-6 px-3 py-2">
                            <i class="bi bi-box me-1"></i>{{ $items->count() }} productos
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase text-muted">
                            <tr>
                                <th class="py-3 ps-4">Código</th>
                                <th class="py-3">Producto</th>
                                <th class="py-3 text-end">Stock</th>
                                <th class="py-3 text-end d-none d-md-table-cell">Costo Prom.</th>
                                <th class="py-3 text-end pe-4">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rowCount = 0; @endphp
                            @foreach($items as $item)
                                @php
                                    if ((int)$item->stock <= 0) continue;
                                    $producto = $productos->firstWhere('id', $item->producto_id);
                                                    if (!$producto) continue;
                                    $rowCount++;
                                    $totalValor += $item->stock * ($producto->precio_compra ?? 0);
                                    $totalUnidades += $item->stock;
                                    $stock = (int)$item->stock;
                                    if ($stock <= 5) $badgeClass = 'bg-danger';
                                    elseif ($stock <= 20) $badgeClass = 'bg-warning text-dark';
                                    else $badgeClass = 'bg-success';
                                    $pct = min($stock, 100) / 100;
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <code class="text-muted small">{{ $producto->codigo_barras ?? '—' }}</code>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $producto->nombre }}</span>
                                        @if($producto->categoria)
                                            <br><small class="text-muted">{{ $producto->categoria->nombre }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span class="badge {{ $badgeClass }} rounded-pill fs-6 px-3">{{ $stock }}</span>
                                    </td>
                                    <td class="text-end text-muted d-none d-md-table-cell">
                                        <small>RD$ {{ number_format($producto->precio_compra ?? 0, 2) }}</small>
                                    </td>
                                    <td class="text-end pe-4 fw-semibold">
                                        RD$ {{ number_format($stock * ($producto->precio_compra ?? 0), 2) }}
                                    </td>
                                </tr>
                            @endforeach
                            @if($rowCount === 0)
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-muted opacity-50"></i>
                                        Sin productos en este almacén
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        @if($rowCount > 0)
                        <tfoot class="table-light border-top">
                            <tr>
                                <td colspan="2" class="ps-4 py-3 fw-bold">Totales</td>
                                <td class="text-end py-3">
                                    <span class="fw-bold">{{ $totalUnidades }} unidades</span>
                                </td>
                                <td class="text-end d-none d-md-table-cell py-3"></td>
                                <td class="text-end pe-4 py-3 fw-bold fs-6 text-primary">
                                    RD$ {{ number_format($totalValor, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('buscar-instant');
    if (!input) return;
    input.addEventListener('keydown', e => { if (e.key === 'Enter') e.preventDefault(); });
    function filtrar() {
        const q = input.value.toLowerCase();
        document.querySelectorAll('.table tbody tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }
    input.addEventListener('input', filtrar);
    filtrar();
});
</script>
@endpush
@endsection
