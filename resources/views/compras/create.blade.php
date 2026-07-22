@extends('layouts.app')

@section('title', 'Nueva Compra')

@push('styles')
@include('partials.premium-ui')
<style>
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4" style="background:linear-gradient(135deg,#3b82f6,#6366f1,#8b5cf6,#3b82f6);box-shadow:0 8px 32px rgba(59,130,246,.25);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle" style="background:rgba(255,255,255,.2);border-color:rgba(255,255,255,.35);">
                    <i class="bi bi-cart-plus"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Registrar Compra</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-cart me-1"></i>
                        Registra una entrada de inventario desde un proveedor
                    </small>
                </div>
            </div>
            <a href="{{ route('compras.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('compras.store') }}" method="POST" id="compraForm">
        @csrf

        <div class="premium-card mb-4" style="animation-delay:.1s;">
            <div class="card-accent blue"></div>
            <div class="premium-card-title">
                <i class="bi bi-cart icon-blue"></i>
                Datos de la Compra
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Proveedor <span class="text-danger">*</span></label>
                        <select name="proveedor_id" class="form-select form-select-lg" required>
                            <option value="">Seleccionar proveedor</option>
                            @foreach($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>{{ $proveedor->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Tipo de Compra <span class="text-danger">*</span></label>
                        <select name="tipo_compra_id" class="form-select form-select-lg" required>
                            <option value="">Seleccionar tipo</option>
                            @foreach($tiposCompra as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_compra_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Almacén <span class="text-danger">*</span></label>
                        <select name="almacen_id" class="form-select form-select-lg" required>
                            <option value="">Seleccionar almacén</option>
                            @foreach($almacenes as $almacen)
                                <option value="{{ $almacen->id }}" {{ old('almacen_id') == $almacen->id ? 'selected' : '' }}>{{ $almacen->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Fecha</label>
                        <input type="date" name="fecha" class="form-control form-control-lg" value="{{ old('fecha', date('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="premium-card mb-4" style="animation-delay:.15s;">
            <div class="card-accent blue"></div>
            <div class="premium-card-title">
                <i class="bi bi-list-check icon-blue"></i>
                Detalle de la Compra
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
                    <tbody id="detalle-body"></tbody>
                    <tfoot class="bg-light bg-opacity-50">
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Subtotal:</td>
                            <td class="fw-bold text-end" id="subtotal-display">RD$ 0.00</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end fw-bold">ITBIS:</td>
                            <td class="fw-bold text-end" id="itbis-display">RD$ 0.00</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end fw-bold fs-5">TOTAL:</td>
                            <td class="fw-bold text-end fs-5 text-primary" id="total-display">RD$ 0.00</td>
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

        <div class="card border-0 shadow-sm rounded-4 mb-4" id="retencionesCard">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-percent text-warning me-2"></i>Retenciones</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="aplica_retencion_isr" name="aplica_retencion_isr" value="1" {{ old('aplica_retencion_isr') ? 'checked' : '' }}>
                            <label class="form-check-label" for="aplica_retencion_isr">
                                <strong>Retención ISR</strong> <small class="text-muted">(10% del total)</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="aplica_retencion_itbis" name="aplica_retencion_itbis" value="1" {{ old('aplica_retencion_itbis') ? 'checked' : '' }}>
                            <label class="form-check-label" for="aplica_retencion_itbis">
                                <strong>Retención ITBIS</strong> <small class="text-muted">(100% del ITBIS)</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-body p-4">
                <label class="form-label small fw-semibold">Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="2" placeholder="Notas sobre la compra (opcional)">{{ old('observaciones') }}</textarea>
            </div>
        </div>

        <input type="hidden" name="total" id="total-hidden" value="0">
    </form>

    <div style="height: 80px;"></div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-end align-items-center">
        <a href="{{ route('compras.index') }}" class="btn-cancel me-2">Cancelar</a>
        <button type="submit" form="compraForm" class="btn-save">
            <i class="bi bi-check-lg me-2"></i>Guardar Compra
        </button>
    </div>
</div>

<datalist id="productList">
    @foreach ($productos as $producto)
        <option value="{{ $producto->nombre }}" data-id="{{ $producto->id }}" data-precio="{{ $producto->precio_compra }}" data-barcode="{{ $producto->codigo_barras }}"></option>
    @endforeach
</datalist>

<template id="fila-template">
    <tr>
        <td>
            <input type="text" class="form-control nombre" list="productList" placeholder="Nombre del producto" required>
            <input type="hidden" class="producto-id" value="">
            <small class="text-muted nuevo-producto-msg d-none text-warning">
                <i class="bi bi-info-circle"></i> Se crear&aacute; un nuevo producto. Asigna precio de venta y c&oacute;digo de barras.
            </small>
        </td>
        <td>
            <input type="text" class="form-control codigo-barras" placeholder="Escanear o escribir" autocomplete="off">
        </td>
        <td><input type="number" min="0.01" step="0.01" class="form-control cantidad" value="1" required></td>
        <td><input type="number" min="0" step="0.01" class="form-control precio" value="0.00" required></td>
        <td><input type="number" min="0" max="100" step="0.01" class="form-control itbis" value="18" required></td>
        <td class="subtotal fw-bold text-end">RD$ 0.00</td>
        <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btnEliminarFila" title="Eliminar fila">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

@php
    $oldProductos = old('productos', [['nombre' => '', 'codigo_barras' => '', 'cantidad' => 1, 'precio' => 0, 'itbis_porcentaje' => 18]]);
@endphp

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('detalle-body');
    const template = document.getElementById('fila-template');
    const btnAdd = document.getElementById('btnAgregarFila');
    const totalHidden = document.getElementById('total-hidden');
    const form = document.getElementById('compraForm');
    const oldProductos = @json($oldProductos);

    function formatRD(n) { return 'RD$ ' + (n || 0).toLocaleString('es-DO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    function calcular(row) {
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
            const r = calcular(row);
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

    document.getElementById('aplica_retencion_isr').addEventListener('change', recalcularTotal);
    document.getElementById('aplica_retencion_itbis').addEventListener('change', recalcularTotal);

    function attachEvents(row) {
        row.querySelector('.cantidad').addEventListener('input', recalcularTotal);
        row.querySelector('.precio').addEventListener('input', recalcularTotal);
        row.querySelector('.itbis').addEventListener('input', recalcularTotal);

        const nombre = row.querySelector('.nombre');
        const hidden = row.querySelector('.producto-id');
        const msg    = row.querySelector('.nuevo-producto-msg');
        const precio = row.querySelector('.precio');
        const barcode = row.querySelector('.codigo-barras');

        nombre.addEventListener('input', function () {
            const val = this.value.trim();
            const option = document.querySelector(`#productList option[value="${CSS.escape(val)}"]`);
            if (option) {
                hidden.value = option.dataset.id;
                msg.classList.add('d-none');
                if (!parseFloat(precio.value) && option.dataset.precio) {
                    precio.value = parseFloat(option.dataset.precio).toFixed(2);
                }
                if (option.dataset.barcode && !barcode.value) {
                    barcode.value = option.dataset.barcode;
                }
            } else {
                hidden.value = '';
                if (val.length > 2) {
                    msg.classList.remove('d-none');
                } else {
                    msg.classList.add('d-none');
                }
            }
            recalcularTotal();
        });

        row.querySelector('.btnEliminarFila').addEventListener('click', () => {
            if (tbody.children.length === 1) {
                nombre.value = ''; hidden.value = ''; precio.value = 0; cantidad.value = 1; itbis.value = 18;
                barcode.value = ''; msg.classList.add('d-none');
            } else {
                row.remove();
            }
            recalcularTotal();
        });
    }

    function agregarFila(data = {}) {
        const row = template.content.cloneNode(true).querySelector('tr');
        if (data.nombre) row.querySelector('.nombre').value = data.nombre;
        if (data.producto_id) row.querySelector('.producto-id').value = data.producto_id;
        if (data.codigo_barras) row.querySelector('.codigo-barras').value = data.codigo_barras;
        if (data.cantidad) row.querySelector('.cantidad').value = data.cantidad;
        if (data.precio !== undefined) row.querySelector('.precio').value = data.precio;
        if (data.itbis_porcentaje !== undefined) row.querySelector('.itbis').value = data.itbis_porcentaje;
        tbody.appendChild(row);
        attachEvents(row);
        return row;
    }

    btnAdd.addEventListener('click', () => {
        agregarFila();
        recalcularTotal();
    });

    form.addEventListener('submit', (e) => {
        form.querySelectorAll('input[type="hidden"][data-dynamic="producto"]').forEach(el => el.remove());

        const filasValidas = Array.from(tbody.querySelectorAll('tr')).filter(row => {
            const nombre = (row.querySelector('.nombre')?.value || '').trim();
            const cantidad = parseFloat(row.querySelector('.cantidad')?.value) || 0;
            const precio = parseFloat(row.querySelector('.precio')?.value) || 0;
            return nombre && cantidad > 0;
        });

        if (filasValidas.length === 0) {
            e.preventDefault();
            alert('Agrega al menos un producto con nombre y cantidad válida.');
            return;
        }

        filasValidas.forEach((row, idx) => {
            const campos = {
                'producto_id': row.querySelector('.producto-id').value,
                'nombre': row.querySelector('.nombre').value,
                'codigo_barras': row.querySelector('.codigo-barras').value,
                'cantidad': row.querySelector('.cantidad').value,
                'precio': row.querySelector('.precio').value,
                'itbis_porcentaje': row.querySelector('.itbis').value,
            };
            for (const [key, val] of Object.entries(campos)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `productos[${idx}][${key}]`;
                input.value = val || '';
                input.setAttribute('data-dynamic', 'producto');
                form.appendChild(input);
            }
        });
    });

    if (oldProductos.length > 0 && oldProductos[0].nombre) {
        oldProductos.forEach(p => agregarFila(p));
    } else {
        agregarFila();
    }
    recalcularTotal();
});
</script>
@endsection
