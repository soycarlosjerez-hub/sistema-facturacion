@extends('layouts.app')

@section('title', 'Editar Compra ' . $compra->folio)

@push('styles')
@include('partials.premium-ui')
<style>
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Compra</h4>
                    <div class="ui-header-meta">Compra <strong>{{ $compra->folio }}</strong> · {{ $compra->proveedor->nombre ?? '—' }}</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('compras.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any() || session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <div class="d-flex">
                <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">No se pudo actualizar la compra</h6>
                    <ul class="mb-0 ps-3">
                        @if(session('error'))<li>{{ session('error') }}</li>@endif
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('compras.update', $compra) }}" method="POST" id="compraForm">
        @csrf
        @method('PUT')

        <div class="ui-card mb-4">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="ui-label small fw-semibold">Proveedor <span class="text-danger">*</span></label>
                        <select name="proveedor_id" class="ui-select ui-select-lg" required>
                            <option value="">Seleccionar proveedor</option>
                            @foreach($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}" {{ $compra->proveedor_id == $proveedor->id ? 'selected' : '' }}>{{ $proveedor->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="ui-label small fw-semibold">Tipo de Compra <span class="text-danger">*</span></label>
                        <select name="tipo_compra_id" class="ui-select ui-select-lg" required>
                            <option value="">Seleccionar tipo</option>
                            @foreach($tiposCompra as $tipo)
                                <option value="{{ $tipo->id }}" {{ $compra->tipo_compra_id == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="ui-label small fw-semibold">Almacén</label>
                        <select name="almacen_id" class="ui-select ui-select-lg">
                            <option value="" {{ empty($compra->almacen_id) ? 'selected' : '' }}>Trabajar sin almacén</option>
                            @foreach($almacenes as $almacen)
                                <option value="{{ $almacen->id }}" {{ ($compra->almacen_id ?? old('almacen_id')) == $almacen->id ? 'selected' : '' }}>{{ $almacen->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="ui-label small fw-semibold">Fecha</label>
                        <input type="date" name="fecha" class="ui-input ui-input-lg" value="{{ old('fecha', $compra->fecha ? $compra->fecha->format('Y-m-d') : date('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4">
            <div class="d-flex justify-content-between align-items-center p-4 pb-0">
                <div>
                    <h5 class="fw-bold mb-0 ui-card-title">
                        <i class="bi bi-list-check me-2"></i>Detalle de la Compra
                    </h5>
                    @if ($facturacion_modo === 'equipos')
                        <div class="text-muted small mt-1">Cada equipo se registrará con su IMEI/Serial individual</div>
                    @endif
                </div>
                <button type="button" class="ui-btn ui-btn-solid rounded-pill px-3 btn-sm" id="btnAgregarFila" style="background:var(--accent);border-color:var(--accent);">
                    <i class="bi bi-plus-lg me-1"></i> @if ($facturacion_modo === 'equipos') Agregar equipo @else Agregar fila @endif
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="detalleCompra">
                    <thead class="table-light">
                        @if ($facturacion_modo === 'equipos')
                        <tr class="text-muted text-uppercase small">
                            <th>Producto</th>
                            <th style="width: 140px;">IMEI/Serial</th>
                            <th style="width: 110px;">Marca</th>
                            <th style="width: 130px;">Modelo</th>
                            <th style="width: 110px;">Almacenamiento</th>
                            <th style="width: 100px;">Color</th>
                            <th style="width: 120px;">Precio Compra</th>
                            <th style="width: 120px;">Precio Venta</th>
                            <th style="width: 90px;">ITBIS %</th>
                            <th style="width: 130px;">Subtotal</th>
                            <th style="width: 60px;"></th>
                        </tr>
                        @else
                        <tr class="text-muted text-uppercase small">
                            <th>Producto</th>
                            <th style="width: 160px;">Cód. Barras</th>
                            <th style="width: 90px;">Cantidad</th>
                            <th style="width: 130px;">Precio Unit.</th>
                            <th style="width: 130px;">Precio Venta</th>
                            <th style="width: 90px;">ITBIS %</th>
                            <th style="width: 130px;">Subtotal</th>
                            <th style="width: 60px;"></th>
                        </tr>
                        @endif
                    </thead>
                    <tbody id="detalle-body">
                        @foreach($detalles as $detalle)
                            @php $isEquipo = (bool) $detalle->equipo_id; @endphp
                            <tr data-detalle-id="{{ $detalle->id }}">
                                @if ($facturacion_modo === 'equipos' && $isEquipo)
                                    {{-- Fila modo EQUIPOS --}}
                                    <td>
                                        <input type="text" name="productos[{{ $loop->index }}][nombre]" class="ui-input nombre" list="productList" value="{{ $detalle->producto->nombre ?? '' }}">
                                        <input type="hidden" name="productos[{{ $loop->index }}][producto_id]" class="producto-id" value="{{ $detalle->producto_id }}">
                                    </td>
                                    <td>
                                        <input type="text" name="productos[{{ $loop->index }}][serial_imei]" class="ui-input serial-imei" value="{{ $detalle->equipo->serial_imei ?? '' }}" required>
                                    </td>
                                    <td>
                                        <input type="text" name="productos[{{ $loop->index }}][marca]" class="ui-input marca" value="{{ $detalle->equipo->marca ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="text" name="productos[{{ $loop->index }}][modelo]" class="ui-input modelo" value="{{ $detalle->equipo->modelo ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="text" name="productos[{{ $loop->index }}][almacenamiento_gb]" class="ui-input almacenamiento" value="{{ $detalle->equipo->almacenamiento_gb ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="text" name="productos[{{ $loop->index }}][color]" class="ui-input color" value="{{ $detalle->equipo->color ?? '' }}">
                                    </td>
                                    <td><input type="number" min="0" step="0.01" name="productos[{{ $loop->index }}][precio]" class="ui-input precio" value="{{ $detalle->precio_unitario }}" required></td>
                                    <td><input type="number" min="0" step="0.01" name="productos[{{ $loop->index }}][precio_venta]" class="ui-input precio-venta" value="{{ $detalle->equipo->precio_venta ?? '' }}"></td>
                                    <td><input type="number" min="0" max="100" step="0.01" name="productos[{{ $loop->index }}][itbis_porcentaje]" class="ui-input itbis" value="{{ $detalle->itbis_porcentaje ?? $systemItbis ?? 18 }}" required></td>
                                    <td class="subtotal fw-bold text-end">RD$ {{ number_format($detalle->subtotal, 2) }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btnEliminarFila" title="Quitar de la compra">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                @elseif ($facturacion_modo === 'equipos' && !$isEquipo)
                                    {{-- Fila con detalle sin equipo (legacy) --}}
                                    <td>
                                        <input type="text" name="productos[{{ $loop->index }}][nombre]" class="ui-input nombre" list="productList" value="{{ $detalle->producto->nombre ?? '' }}" required>
                                        <input type="hidden" name="productos[{{ $loop->index }}][producto_id]" class="producto-id" value="{{ $detalle->producto_id }}">
                                    </td>
                                    <td colspan="9">
                                        <small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Este detalle no tiene IMEI registrado</small>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btnEliminarFila" title="Quitar de la compra">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                @else
                                    {{-- Fila modo PRODUCTOS --}}
                                    <td>
                                        <input type="text" name="productos[{{ $loop->index }}][nombre]" class="ui-input nombre" list="productList" value="{{ $detalle->producto->nombre ?? '' }}" required>
                                        <input type="hidden" name="productos[{{ $loop->index }}][producto_id]" class="producto-id" value="{{ $detalle->producto_id }}">
                                    </td>
                                    <td>
                                        <input type="text" name="productos[{{ $loop->index }}][codigo_barras]" class="ui-input codigo-barras" value="{{ $detalle->producto->codigo_barras ?? '' }}" placeholder="Escanear o escribir" autocomplete="off">
                                    </td>
                                    <td><input type="number" min="0.01" step="0.01" name="productos[{{ $loop->index }}][cantidad]" class="ui-input cantidad" value="{{ $detalle->cantidad }}" required></td>
                                    <td><input type="number" min="0" step="0.01" name="productos[{{ $loop->index }}][precio]" class="ui-input precio" value="{{ $detalle->precio_unitario }}" required></td>
                                    <td><input type="number" min="0" step="0.01" name="productos[{{ $loop->index }}][precio_venta]" class="ui-input precio-venta" value="{{ $detalle->producto->precio ?? '' }}"></td>
                                    <td><input type="number" min="0" max="100" step="0.01" name="productos[{{ $loop->index }}][itbis_porcentaje]" class="ui-input itbis" value="{{ $detalle->itbis_porcentaje ?? $systemItbis ?? 18 }}" required></td>
                                    <td class="subtotal fw-bold text-end">RD$ {{ number_format($detalle->subtotal, 2) }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btnEliminarFila" title="Quitar de la compra">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light bg-opacity-50">
                        <tr>
                            <td colspan="{{ $facturacion_modo === 'equipos' ? '8' : '6' }}" class="text-end fw-bold">Subtotal:</td>
                            <td class="fw-bold text-end" id="subtotal-display">RD$ {{ number_format($compra->subtotal ?? 0, 2) }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="{{ $facturacion_modo === 'equipos' ? '8' : '6' }}" class="text-end fw-bold">ITBIS:</td>
                            <td class="fw-bold text-end" id="itbis-display">RD$ {{ number_format($compra->itbis_total ?? 0, 2) }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="{{ $facturacion_modo === 'equipos' ? '8' : '6' }}" class="text-end fw-bold fs-5">TOTAL:</td>
                            <td class="fw-bold text-end fs-5 text-primary" id="total-display">RD$ {{ number_format($compra->total, 2) }}</td>
                            <td></td>
                        </tr>
                        <tr class="retenciones-row" style="display:none">
                            <td colspan="{{ $facturacion_modo === 'equipos' ? '8' : '6' }}" class="text-end text-danger fw-bold">Retenciones:</td>
                            <td class="text-end fw-bold" id="retenciones-display">RD$ 0.00</td>
                            <td></td>
                        </tr>
                        <tr class="total-neto-row" style="display:none">
                            <td colspan="{{ $facturacion_modo === 'equipos' ? '8' : '6' }}" class="text-end fw-bold fs-5">Total a Pagar:</td>
                            <td class="fw-bold text-end fs-5 text-success" id="total-neto-display">RD$ 0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="ui-card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3 ui-card-title"><i class="bi bi-percent me-2"></i>Retenciones</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="aplica_retencion_isr" name="aplica_retencion_isr" value="1" {{ $compra->aplica_retencion_isr ? 'checked' : '' }}>
                            <label class="form-check-label" for="aplica_retencion_isr">
                                <strong>Retención ISR</strong> <small class="text-muted">(10% del total)</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="aplica_retencion_itbis" name="aplica_retencion_itbis" value="1" {{ $compra->aplica_retencion_itbis ? 'checked' : '' }}>
                            <label class="form-check-label" for="aplica_retencion_itbis">
                                <strong>Retención ITBIS</strong> <small class="text-muted">(100% del ITBIS)</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4">
            <div class="card-body p-4">
                <label class="ui-label small fw-semibold">Observaciones</label>
                <textarea name="observaciones" class="ui-textarea" rows="2" placeholder="Notas sobre la compra (opcional)">{{ old('observaciones', $compra->observaciones) }}</textarea>
            </div>
        </div>

        <input type="hidden" name="total" id="total-hidden" value="{{ $compra->total }}">
    </form>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('compras.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
        <button type="submit" form="compraForm" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>Actualizar Compra
        </button>
    </div>
</div>
    </div>
</div>

<datalist id="productList">
    @foreach($productos as $producto)
        <option value="{{ $producto->nombre }}" data-id="{{ $producto->id }}" data-precio="{{ $producto->precio_compra }}" data-precio-venta="{{ $producto->precio }}" data-barcode="{{ $producto->codigo_barras }}" data-marca="{{ $producto->marca ?? '' }}" data-modelo="{{ $producto->modelo ?? '' }}"></option>
    @endforeach
</datalist>

<template id="fila-template-productos">
    <tr>
        <td>
            <input type="text" class="ui-input nombre" list="productList" placeholder="Nombre del producto" required>
            <input type="hidden" class="producto-id" value="">
        </td>
        <td>
            <input type="text" class="ui-input codigo-barras" placeholder="Escanear o escribir" autocomplete="off">
        </td>
        <td><input type="number" min="0.01" step="0.01" class="ui-input cantidad" value="1" required></td>
        <td><input type="number" min="0" step="0.01" class="ui-input precio" value="0.00" required></td>
        <td><input type="number" min="0" step="0.01" class="ui-input precio-venta" value="0.00"></td>
        <td><input type="number" min="0" max="100" step="0.01" class="ui-input itbis" value="{{ $systemItbis ?? 18 }}" required></td>
        <td class="subtotal fw-bold text-end">RD$ 0.00</td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btnEliminarFila">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

<template id="fila-template-equipos">
    <tr>
        <td>
            <input type="text" class="ui-input nombre" list="productList" placeholder="Producto (opcional)">
            <input type="hidden" class="producto-id" value="">
        </td>
        <td>
            <input type="text" class="ui-input serial-imei" placeholder="IMEI o Serial" required>
        </td>
        <td>
            <input type="text" class="ui-input marca" placeholder="Marca">
        </td>
        <td>
            <input type="text" class="ui-input modelo" placeholder="Modelo">
        </td>
        <td>
            <input type="text" class="ui-input almacenamiento" placeholder="GB">
        </td>
        <td>
            <input type="text" class="ui-input color" placeholder="Color">
        </td>
        <td><input type="number" min="0" step="0.01" class="ui-input precio" value="0.00" required></td>
        <td><input type="number" min="0" step="0.01" class="ui-input precio-venta" value="0.00"></td>
        <td><input type="number" min="0" max="100" step="0.01" class="ui-input itbis" value="{{ $systemItbis ?? 18 }}" required></td>
        <td class="subtotal fw-bold text-end">RD$ 0.00</td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btnEliminarFila">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('detalle-body');
    const btnAdd = document.getElementById('btnAgregarFila');
    const totalHidden = document.getElementById('total-hidden');
    const facturacionModo = '{{ $facturacion_modo }}';
    const systemItbis = {{ $systemItbis ?? 18 }};
    const compraId = {{ $compra->id }};

    function formatRD(n) { return 'RD$ ' + (n || 0).toLocaleString('es-DO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    function recalcularFilaEquipo(row) {
        const precio = parseFloat(row.querySelector('.precio').value) || 0;
        const itbis  = parseFloat(row.querySelector('.itbis').value) || 0;
        const total  = precio * (1 + itbis / 100);
        row.querySelector('.subtotal').textContent = formatRD(total);
        return { base: precio, itbis: precio * (itbis / 100), total };
    }

    function recalcularFilaProductos(row) {
        const cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
        const precio   = parseFloat(row.querySelector('.precio').value) || 0;
        const itbis    = parseFloat(row.querySelector('.itbis').value) || 0;
        const base     = cantidad * precio;
        const total    = base * (1 + itbis / 100);
        row.querySelector('.subtotal').textContent = formatRD(total);
        return { base, itbis: base * (itbis / 100), total };
    }

    function recalcularTotal() {
        let subtotal = 0, itbisTotal = 0, total = 0;
        tbody.querySelectorAll('tr').forEach(row => {
            let r;
            if (facturacionModo === 'equipos') {
                r = recalcularFilaEquipo(row);
            } else {
                r = recalcularFilaProductos(row);
            }
            subtotal   += r.base;
            itbisTotal += r.itbis;
            total      += r.total;
        });

        const aplicaIsr = document.getElementById('aplica_retencion_isr').checked;
        const aplicaItbis = document.getElementById('aplica_retencion_itbis').checked;
        const retIsr = aplicaIsr ? total * 0.10 : 0;
        const retItbis = aplicaItbis ? itbisTotal : 0;
        const retenciones = retIsr + retItbis;
        const totalNeto = total - retenciones;

        document.getElementById('subtotal-display').textContent = formatRD(subtotal);
        document.getElementById('itbis-display').textContent   = formatRD(itbisTotal);
        document.getElementById('total-display').textContent   = formatRD(total);
        document.getElementById('retenciones-display').textContent = '- ' + formatRD(retenciones);
        document.getElementById('total-neto-display').textContent = formatRD(totalNeto);
        totalHidden.value = total.toFixed(2);

        const retRows = document.querySelectorAll('.retenciones-row, .total-neto-row');
        retRows.forEach(r => r.style.display = (retenciones > 0) ? '' : 'none');
    }

    function renumerarIndices() {
        tbody.querySelectorAll('tr').forEach((row, index) => {
            row.querySelectorAll('input[name^="productos"]').forEach(input => {
                input.name = input.name.replace(/productos\[\d+\]/, `productos[${index}]`);
            });
        });
    }

    document.getElementById('aplica_retencion_isr').addEventListener('change', recalcularTotal);
    document.getElementById('aplica_retencion_itbis').addEventListener('change', recalcularTotal);

    function attachEventsProductos(row) {
        row.querySelector('.cantidad').addEventListener('input', recalcularTotal);
        row.querySelector('.precio').addEventListener('input', recalcularTotal);
        row.querySelector('.itbis').addEventListener('input', recalcularTotal);

        const nombre = row.querySelector('.nombre');
        const hidden = row.querySelector('.producto-id');
        const barcode = row.querySelector('.codigo-barras');
        const precioVenta = row.querySelector('.precio-venta');

        nombre.addEventListener('input', function () {
            const val = this.value.trim();
            const option = document.querySelector(`#productList option[value="${CSS.escape(val)}"]`);
            if (option) {
                hidden.value = option.dataset.id;
                if (option.dataset.barcode && barcode && !barcode.value) {
                    barcode.value = option.dataset.barcode;
                }
                if (!parseFloat(precioVenta?.value) && option.dataset.precio) {
                    precioVenta.value = parseFloat(option.dataset.precio).toFixed(2);
                }
            } else {
                hidden.value = '';
            }
            renumerarIndices();
            recalcularTotal();
        });

        row.querySelector('.btnEliminarFila').addEventListener('click', () => {
            if (tbody.children.length === 1) {
                if (!confirm('¿Eliminar la compra completa? Se revertirá el stock de todos los productos.')) return;
                document.getElementById('compraForm').action = "{{ route('compras.destroy', $compra) }}";
                let methodInput = document.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    document.getElementById('compraForm').appendChild(methodInput);
                }
                methodInput.value = 'DELETE';
                document.getElementById('compraForm').submit();
                return;
            }
            row.remove();
            renumerarIndices();
            recalcularTotal();
        });
    }

    function attachEventsEquipos(row) {
        row.querySelector('.precio').addEventListener('input', recalcularTotal);
        row.querySelector('.itbis').addEventListener('input', recalcularTotal);

        const nombre = row.querySelector('.nombre');
        const hidden = row.querySelector('.producto-id');
        const precioVenta = row.querySelector('.precio-venta');

        nombre.addEventListener('input', function () {
            const val = this.value.trim();
            const option = document.querySelector(`#productList option[value="${CSS.escape(val)}"]`);
            if (option) {
                hidden.value = option.dataset.id;
                if (!parseFloat(precioVenta?.value) && option.dataset.precio) {
                    precioVenta.value = parseFloat(option.dataset.precio).toFixed(2);
                }
            } else {
                hidden.value = '';
            }
            renumerarIndices();
            recalcularTotal();
        });

        row.querySelector('.btnEliminarFila').addEventListener('click', () => {
            if (tbody.children.length === 1) {
                if (!confirm('¿Eliminar la compra completa?')) return;
                document.getElementById('compraForm').action = "{{ route('compras.destroy', $compra) }}";
                let methodInput = document.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    document.getElementById('compraForm').appendChild(methodInput);
                }
                methodInput.value = 'DELETE';
                document.getElementById('compraForm').submit();
                return;
            }
            row.remove();
            renumerarIndices();
            recalcularTotal();
        });
    }

    btnAdd.addEventListener('click', () => {
        const templateId = facturacionModo === 'equipos' ? 'fila-template-equipos' : 'fila-template-productos';
        const template = document.getElementById(templateId);
        const newRow = template.content.cloneNode(true).querySelector('tr');
        tbody.appendChild(newRow);
        renumerarIndices();
        
        if (facturacionModo === 'equipos') {
            attachEventsEquipos(newRow);
        } else {
            attachEventsProductos(newRow);
        }
        recalcularTotal();
    });

    tbody.querySelectorAll('tr').forEach(row => {
        if (facturacionModo === 'equipos') {
            attachEventsEquipos(row);
        } else {
            attachEventsProductos(row);
        }
    });
    
    recalcularTotal();
});
</script>
@endsection
