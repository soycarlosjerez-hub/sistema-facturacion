@extends('layouts.app')

@section('title', 'Nueva Devolución')

@push('styles')
@include('partials.premium-ui')
<style>
.devoluciones-create-table {
    --bs-table-bg: transparent;
    margin: 0;
}
.devoluciones-create-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
body.dark-mode .devoluciones-create-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .devoluciones-create-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#ef4444;--accent-rgb:239,68,68;--accent-hover:#dc2626;">

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

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-arrow-return-left"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Devolución</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-arrow-return-left me-1"></i>
                        <span>Registra la devolución de productos de una venta</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('devoluciones.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('devoluciones.store') }}" method="POST" id="formDevolucion">
        @csrf

        <div class="ui-card mb-4" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title">
                <i class="bi bi-arrow-return-left"></i>
                Datos de la Devolución
            </div>
            <div class="ui-card-subtitle">Información general de la devolución</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="ui-label">Buscar Venta</label>
                        <div class="ui-input-group">
                            <input type="text" id="buscarVenta" class="ui-input" placeholder="# de venta o nombre cliente" autocomplete="off">
                            <input type="hidden" name="venta_id" id="venta_id" value="{{ $venta?->id }}">
                            <button type="button" class="ui-btn ui-btn-ghost ui-btn-sm" id="btnLimpiarVenta"><i class="bi bi-x-lg"></i></button>
                        </div>
                        <div id="resultadosVenta" class="list-group mt-1" style="position:absolute;z-index:10;max-height:200px;overflow-y:auto;display:none;"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" class="ui-select" required>
                            <option value="">Seleccionar cliente</option>
                            @foreach($clientes as $c)
                                <option value="{{ $c->id }}" {{ $venta && $venta->cliente_id == $c->id ? 'selected' : '' }}>{{ $c->nombre }} ({{ $c->rnc_cedula ?? '—' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="ui-label">Fecha</label>
                        <input type="date" name="fecha" class="ui-input" value="{{ old('fecha', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="ui-label">Tipo</label>
                        <select name="tipo" class="ui-select" required>
                            <option value="parcial">Parcial</option>
                            <option value="total">Total</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <label class="ui-label">Motivo <span class="text-danger">*</span></label>
                        <textarea name="motivo" class="ui-input" rows="2" required minlength="5">{{ old('motivo') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4" style="--delay:.2s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title">
                <i class="bi bi-box-seam"></i>
                Productos a Devolver
            </div>
            <div class="ui-card-subtitle">Detalle de los productos incluidos en la devolución</div>
            <div class="ui-card-body p-0">
                <div class="d-flex justify-content-end px-4 pt-3">
                    <button type="button" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill px-3" id="btnAgregarFila">
                        <i class="bi bi-plus-lg me-1"></i>Agregar producto
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table devoluciones-create-table align-middle mb-0" id="tablaItems">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th style="width:100px;">Cantidad</th>
                                <th style="width:130px;">Precio Unit.</th>
                                <th style="width:80px;">ITBIS %</th>
                                <th style="width:130px;">Subtotal</th>
                                <th style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="items-body"></tbody>
                        <tfoot class="bg-light bg-opacity-50">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                                <td class="fw-bold text-end" id="subtotal-display">RD$ 0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">ITBIS:</td>
                                <td class="fw-bold text-end" id="itbis-display">RD$ 0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-bold fs-5">TOTAL:</td>
                                <td class="fw-bold text-end fs-5 text-primary" id="total-display">RD$ 0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </form>

    <div class="ui-sticky-bar">
        <div class="ui-sticky-bar-inner">
            <a href="{{ route('devoluciones.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">Cancelar</a>
            <button type="submit" form="formDevolucion" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                <i class="bi bi-save me-2"></i>Registrar Devolución
            </button>
        </div>
    </div>
</div>

<datalist id="productList">
    @foreach(\App\Models\Producto::orderBy('nombre')->get() as $p)
        <option value="{{ $p->nombre }}" data-id="{{ $p->id }}" data-precio="{{ $p->precio }}" data-itbis="{{ $p->itbis_porcentaje ?? 18 }}"></option>
    @endforeach
</datalist>

<template id="fila-template">
    <tr>
        <td>
            <input type="text" class="ui-input nombre" list="productList" placeholder="Nombre del producto" required>
            <input type="hidden" class="producto-id" name="items[0][producto_id]" value="">
        </td>
        <td><input type="number" min="0.01" step="0.01" class="ui-input cantidad" value="1" required></td>
        <td><input type="number" min="0" step="0.01" class="ui-input precio" value="0.00" required></td>
        <td><input type="number" min="0" max="100" step="0.01" class="ui-input itbis" value="18"></td>
        <td class="subtotal fw-bold text-end">RD$ 0.00</td>
        <td class="text-center">
            <button type="button" class="ui-action ui-action-delete btnEliminarFila"><i class="bi bi-trash"></i></button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('items-body');
    const template = document.getElementById('fila-template');
    const btnAdd = document.getElementById('btnAgregarFila');

    function formatRD(n) { return 'RD$ ' + (n || 0).toLocaleString('es-DO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    function recalcular() {
        let subtotal = 0, itbis = 0, total = 0;
        tbody.querySelectorAll('tr').forEach(row => {
            const cant = parseFloat(row.querySelector('.cantidad').value) || 0;
            const precio = parseFloat(row.querySelector('.precio').value) || 0;
            const itbisP = parseFloat(row.querySelector('.itbis').value) || 0;
            const base = cant * precio;
            const imp = base * itbisP / 100;
            const tot = base + imp;
            row.querySelector('.subtotal').textContent = formatRD(tot);
            subtotal += base;
            itbis += imp;
            total += tot;
        });
        document.getElementById('subtotal-display').textContent = formatRD(subtotal);
        document.getElementById('itbis-display').textContent = formatRD(itbis);
        document.getElementById('total-display').textContent = formatRD(total);
    }

    function renumer() {
        tbody.querySelectorAll('tr').forEach((row, i) => {
            row.querySelector('.producto-id').name = `items[${i}][producto_id]`;
            row.querySelector('.cantidad').name = `items[${i}][cantidad]`;
            row.querySelector('.precio').name = `items[${i}][precio_unitario]`;
            row.querySelector('.itbis').name = `items[${i}][itbis_porcentaje]`;
        });
    }

    function attachEvents(row) {
        row.querySelector('.cantidad').addEventListener('input', recalcular);
        row.querySelector('.precio').addEventListener('input', recalcular);
        row.querySelector('.itbis').addEventListener('input', recalcular);

        const nombre = row.querySelector('.nombre');
        const hidden = row.querySelector('.producto-id');
        const precio = row.querySelector('.precio');
        const itbis = row.querySelector('.itbis');
        nombre.addEventListener('input', () => {
            const val = nombre.value.trim();
            const opt = document.querySelector(`#productList option[value="${CSS.escape(val)}"]`);
            if (opt) {
                hidden.value = opt.dataset.id;
                if (!parseFloat(precio.value) && opt.dataset.precio) precio.value = parseFloat(opt.dataset.precio).toFixed(2);
                if (opt.dataset.itbis) itbis.value = opt.dataset.itbis;
            } else {
                hidden.value = '';
            }
            recalcular();
        });

        row.querySelector('.btnEliminarFila').addEventListener('click', () => {
            if (tbody.children.length <= 1) {
                row.querySelector('.nombre').value = '';
                hidden.value = '';
                precio.value = '0';
                row.querySelector('.cantidad').value = '1';
                itbis.value = '18';
            } else {
                row.remove();
                renumer();
            }
            recalcular();
        });
    }

    btnAdd.addEventListener('click', () => {
        const row = template.content.cloneNode(true).querySelector('tr');
        tbody.appendChild(row);
        renumer();
        attachEvents(row);
        recalcular();
    });

    agregarFila: {
        const row = template.content.cloneNode(true).querySelector('tr');
        tbody.appendChild(row);
        renumer();
        attachEvents(row);
        recalcular();
    }

    const form = document.getElementById('formDevolucion');
    form.addEventListener('submit', (e) => {
        const filas = tbody.querySelectorAll('tr');
        if (!filas.length || !filas[0].querySelector('.nombre').value.trim()) {
            e.preventDefault();
            alert('Agrega al menos un producto a devolver.');
        }
    });

    const inputVenta = document.getElementById('buscarVenta');
    const resultados = document.getElementById('resultadosVenta');
    let timer = null;
    inputVenta.addEventListener('input', () => {
        clearTimeout(timer);
        const q = inputVenta.value.trim();
        if (q.length < 1) { resultados.style.display = 'none'; return; }
        timer = setTimeout(() => {
            fetch('{{ route('devoluciones.buscar-venta') }}?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(data => {
                    if (!data.length) { resultados.style.display = 'none'; return; }
                    resultados.innerHTML = data.map(v =>
                        `<a href="#" class="list-group-item list-group-item-action py-2 small" data-venta='${JSON.stringify(v)}'>${v.label} - RD$ ${v.total.toFixed(2)}</a>`
                    ).join('');
                    resultados.style.display = 'block';
                });
        }, 300);
    });

    resultados.addEventListener('click', (e) => {
        const a = e.target.closest('a');
        if (!a) return;
        e.preventDefault();
        const venta = JSON.parse(a.dataset.venta);
        document.getElementById('venta_id').value = venta.id;
        inputVenta.value = venta.label;
        resultados.style.display = 'none';
        if (venta.detalles) {
            tbody.innerHTML = '';
            venta.detalles.forEach((item, i) => {
                const row = template.content.cloneNode(true).querySelector('tr');
                row.querySelector('.nombre').value = item.producto_nombre;
                row.querySelector('.producto-id').value = item.producto_id;
                row.querySelector('.cantidad').value = item.cantidad;
                row.querySelector('.precio').value = item.precio;
                row.querySelector('.itbis').value = item.itbis;
                tbody.appendChild(row);
                attachEvents(row);
            });
            renumer();
            recalcular();
        }
    });

    document.getElementById('btnLimpiarVenta').addEventListener('click', () => {
        document.getElementById('venta_id').value = '';
        inputVenta.value = '';
        resultados.style.display = 'none';
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.col-md-4') && !e.target.closest('#resultadosVenta')) {
            resultados.style.display = 'none';
        }
    });
});
</script>
@endpush
@endsection
