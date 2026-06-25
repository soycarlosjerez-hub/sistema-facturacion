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
<div class="container-fluid py-4 premium-page">

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

    <div class="premium-header mb-4" style="background:linear-gradient(135deg,#ef4444,#f97316,#ef4444);box-shadow:0 8px 32px rgba(239,68,68,.25);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle" style="background:rgba(255,255,255,.2);border-color:rgba(255,255,255,.35);">
                    <i class="bi bi-arrow-return-left"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Nueva Devolución</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-arrow-return-left me-1"></i>
                        Registra la devolución de productos de una venta
                    </small>
                </div>
            </div>
            <div>
                <a href="{{ route('devoluciones.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('devoluciones.store') }}" method="POST" id="formDevolucion">
        @csrf

        <div class="premium-card mb-4" style="animation-delay:.1s;">
            <div class="card-accent red"></div>
            <div class="premium-card-title">
                <i class="bi bi-arrow-return-left icon-red"></i>
                Datos de la Devolución
            </div>
            <div class="premium-card-subtitle">Información general de la devolución</div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Buscar Venta</label>
                        <div class="input-group">
                            <input type="text" id="buscarVenta" class="form-control" placeholder="# de venta o nombre cliente" autocomplete="off">
                            <input type="hidden" name="venta_id" id="venta_id" value="{{ $venta?->id }}">
                            <button type="button" class="btn btn-outline-secondary" id="btnLimpiarVenta"><i class="bi bi-x-lg"></i></button>
                        </div>
                        <div id="resultadosVenta" class="list-group mt-1" style="position:absolute;z-index:10;max-height:200px;overflow-y:auto;display:none;"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" class="form-select" required>
                            <option value="">Seleccionar cliente</option>
                            @foreach($clientes as $c)
                                <option value="{{ $c->id }}" {{ $venta && $venta->cliente_id == $c->id ? 'selected' : '' }}>{{ $c->nombre }} ({{ $c->rnc_cedula ?? '—' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control" value="{{ old('fecha', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select" required>
                            <option value="parcial">Parcial</option>
                            <option value="total">Total</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <label class="form-label">Motivo <span class="text-danger">*</span></label>
                        <textarea name="motivo" class="form-control" rows="2" required minlength="5">{{ old('motivo') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="premium-card mb-4" style="animation-delay:.2s;">
            <div class="card-accent red"></div>
            <div class="premium-card-title">
                <i class="bi bi-box-seam icon-red"></i>
                Productos a Devolver
            </div>
            <div class="premium-card-subtitle">Detalle de los productos incluidos en la devolución</div>
            <div class="card-body p-0">
                <div class="d-flex justify-content-end px-4 pt-3">
                    <button type="button" class="btn btn-primary rounded-pill px-3" id="btnAgregarFila">
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

    <div class="premium-sticky-bar d-flex justify-content-end align-items-center gap-3">
        <span class="text-muted small d-none d-md-inline"><i class="bi bi-info-circle me-1"></i>Registrando nueva devolución</span>
        <a href="{{ route('devoluciones.index') }}" class="btn btn-cancel rounded-pill px-4">Cancelar</a>
        <button type="submit" form="formDevolucion" class="btn btn-save rounded-pill px-5 fw-bold">
            <i class="bi bi-save me-2"></i>Registrar Devolución
        </button>
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
            <input type="text" class="form-control nombre" list="productList" placeholder="Nombre del producto" required>
            <input type="hidden" class="producto-id" name="items[0][producto_id]" value="">
        </td>
        <td><input type="number" min="0.01" step="0.01" class="form-control cantidad" value="1" required></td>
        <td><input type="number" min="0" step="0.01" class="form-control precio" value="0.00" required></td>
        <td><input type="number" min="0" max="100" step="0.01" class="form-control itbis" value="18"></td>
        <td class="subtotal fw-bold text-end">RD$ 0.00</td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btnEliminarFila"><i class="bi bi-trash"></i></button>
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
