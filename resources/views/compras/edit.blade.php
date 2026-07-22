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
                        <label class="ui-label small fw-semibold">Almacén <span class="text-danger">*</span></label>
                        <select name="almacen_id" class="ui-select ui-select-lg" required>
                            <option value="">Seleccionar almacén</option>
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
                <h5 class="fw-bold mb-0 ui-card-title"><i class="bi bi-list-check me-2"></i>Detalle de la Compra</h5>
                <button type="button" class="ui-btn ui-btn-solid rounded-pill px-3 btn-sm" id="btnAgregarFila" style="background:var(--accent);border-color:var(--accent);">
                    <i class="bi bi-plus-lg me-1"></i>Agregar fila
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="detalleCompra">
                    <thead class="table-light">
                        <tr class="text-muted text-uppercase small">
                            <th>Producto</th>
                            <th style="width: 160px;">Cód. Barras</th>
                            <th style="width: 90px;">Cantidad</th>
                            <th style="width: 130px;">Precio Unit.</th>
                            <th style="width: 90px;">ITBIS %</th>
                            <th style="width: 130px;">Subtotal</th>
                            <th style="width: 60px;"></th>
                        </tr>
                    </thead>
                    <tbody id="detalle-body">
                        @foreach($detalles as $detalle)
                            <tr data-detalle-id="{{ $detalle->id }}">
                                <td>
                                    <input type="text" name="productos[{{ $loop->index }}][nombre]" class="ui-input nombre" list="productList" value="{{ $detalle->producto->nombre ?? '' }}" required>
                                    <input type="hidden" name="productos[{{ $loop->index }}][producto_id]" class="producto-id" value="{{ $detalle->producto_id }}">
                                </td>
                                <td>
                                    <input type="text" name="productos[{{ $loop->index }}][codigo_barras]" class="ui-input codigo-barras" value="{{ $detalle->producto->codigo_barras ?? '' }}" placeholder="Escanear o escribir" autocomplete="off">
                                </td>
                                <td><input type="number" min="0.01" step="0.01" name="productos[{{ $loop->index }}][cantidad]" class="ui-input cantidad" value="{{ $detalle->cantidad }}" required></td>
                                <td><input type="number" min="0" step="0.01" name="productos[{{ $loop->index }}][precio]" class="ui-input precio" value="{{ $detalle->precio_unitario }}" required></td>
                                <td><input type="number" min="0" max="100" step="0.01" name="productos[{{ $loop->index }}][itbis_porcentaje]" class="ui-input itbis" value="{{ $detalle->itbis_porcentaje ?? 18 }}" required></td>
                                <td class="subtotal fw-bold text-end">RD$ {{ number_format($detalle->subtotal, 2) }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btnEliminarFila" title="Quitar de la compra">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light bg-opacity-50">
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Subtotal:</td>
                            <td class="fw-bold text-end" id="subtotal-display">RD$ {{ number_format($compra->subtotal ?? 0, 2) }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end fw-bold">ITBIS:</td>
                            <td class="fw-bold text-end" id="itbis-display">RD$ {{ number_format($compra->itbis_total ?? 0, 2) }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end fw-bold fs-5">TOTAL:</td>
                            <td class="fw-bold text-end fs-5 text-primary" id="total-display">RD$ {{ number_format($compra->total, 2) }}</td>
                            <td></td>
                        </tr>
                        <tr class="retenciones-row" style="display:none">
                            <td colspan="5" class="text-end text-danger fw-bold">Retenciones:</td>
                            <td class="text-end fw-bold" id="retenciones-display">RD$ 0.00</td>
                            <td></td>
                        </tr>
                        <tr class="total-neto-row" style="display:none">
                            <td colspan="5" class="text-end fw-bold fs-5">Total a Pagar:</td>
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
        <option value="{{ $producto->nombre }}" data-id="{{ $producto->id }}" data-precio="{{ $producto->precio_compra }}" data-barcode="{{ $producto->codigo_barras }}"></option>
    @endforeach
</datalist>

<template id="fila-template">
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
        <td><input type="number" min="0" max="100" step="0.01" class="ui-input itbis" value="18" required></td>
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
    const template = document.getElementById('fila-template');
    const btnAdd = document.getElementById('btnAgregarFila');
    const totalHidden = document.getElementById('total-hidden');

    function formatRD(n) { return 'RD$ ' + (n || 0).toLocaleString('es-DO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    function recalcularFila(row) {
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
            const r = recalcularFila(row);
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

    function attachEvents(row) {
        row.querySelector('.cantidad').addEventListener('input', recalcularTotal);
        row.querySelector('.precio').addEventListener('input', recalcularTotal);
        row.querySelector('.itbis').addEventListener('input', recalcularTotal);

        const nombre = row.querySelector('.nombre');
        const hidden = row.querySelector('.producto-id');
        const barcode = row.querySelector('.codigo-barras');
        nombre.addEventListener('input', function () {
            const val = this.value.trim();
            const option = document.querySelector(`#productList option[value="${CSS.escape(val)}"]`);
            if (option) {
                hidden.value = option.dataset.id;
                if (option.dataset.barcode && barcode && !barcode.value) {
                    barcode.value = option.dataset.barcode;
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

    btnAdd.addEventListener('click', () => {
        const newRow = template.content.cloneNode(true).querySelector('tr');
        tbody.appendChild(newRow);
        renumerarIndices();
        attachEvents(newRow);
        recalcularTotal();
    });

    tbody.querySelectorAll('tr').forEach(row => attachEvents(row));
    recalcularTotal();
});
</script>
@endsection
