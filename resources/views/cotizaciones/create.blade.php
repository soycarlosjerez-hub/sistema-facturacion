@extends('layouts.app')

@section('title', 'Nueva Cotización')

@section('content')
<div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">
                    <i class="bi bi-file-earmark-plus text-primary me-2"></i>
                    Nueva Cotización
                </h2>
                <p class="text-muted mb-0">Crear presupuesto para cliente</p>
            </div>
            <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-x-lg me-1"></i> Cancelar
            </a>
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

        <form id="cotizacion-form" action="{{ route('cotizaciones.store') }}" method="POST" autocomplete="off">
            @csrf
        <div class="row g-3">
            <!-- Columna izquierda: información y productos -->
            <div class="col-lg-8">
                <!-- Información general -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            Información General
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Número</label>
                                <input type="text" class="form-control" value="{{ $numero }}" disabled>
                                <small class="text-muted">Se genera automáticamente</small>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold">Cliente</label>
                                <select name="cliente_id" class="form-select" id="cliente_id">
                                    <option value="">-- Consumidor Final --</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">Fecha</label>
                                <input type="date" name="fecha" class="form-control" value="{{ old('fecha', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">Válida hasta</label>
                                <input type="date" name="fecha_validez" class="form-control" value="{{ old('fecha_validez', date('Y-m-d', strtotime('+15 days'))) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Búsqueda y agregado de productos -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-box-seam text-primary me-2"></i>
                                Productos
                            </h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary" id="items-count">0 items</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Buscador -->
                        <div class="position-relative mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="buscar-producto" class="form-control border-start-0" placeholder="Buscar por nombre o código...">
                            </div>
                            <div id="resultados-busqueda" class="position-absolute w-100 bg-white border rounded shadow-lg" style="z-index: 1000; max-height: 300px; overflow-y: auto; display: none;"></div>
                        </div>

                        <!-- Tabla de items -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th style="width: 100px;">Cantidad</th>
                                        <th style="width: 120px;">Precio</th>
                                        <th style="width: 100px;">ITBIS %</th>
                                        <th style="width: 100px;">Desc.</th>
                                        <th class="text-end" style="width: 120px;">Total</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="items-tbody">
                                    <tr id="empty-row">
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-cart-x fs-2 opacity-50"></i>
                                            <p class="mb-0">No hay productos agregados</p>
                                            <small>Busca y agrega productos arriba</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Notas y condiciones -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">
                                    <i class="bi bi-sticky me-1"></i> Notas
                                </label>
                                <textarea name="notas" class="form-control" rows="3" placeholder="Notas internas...">{{ old('notas') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">
                                    <i class="bi bi-file-text me-1"></i> Términos y Condiciones
                                </label>
                                <textarea name="condiciones" class="form-control" rows="3" placeholder="Ej: Validez 15 días, pago 50% anticipado...">{{ old('condiciones', 'Precios incluyen ITBIS. Validez de la oferta: 15 días.') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: resumen -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-calculator me-2"></i>
                            Resumen
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-semibold" id="subtotal-display">RD$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">ITBIS:</span>
                            <span class="fw-semibold" id="itbis-display">RD$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 align-items-center">
                            <span class="text-muted">Descuento:</span>
                            <div class="input-group input-group-sm" style="width: 130px;">
                                <span class="input-group-text bg-white">RD$</span>
                                <input type="number" name="descuento" id="descuento" class="form-control text-end" 
                                       value="{{ old('descuento', 0) }}" min="0" step="0.01">
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5">Total:</span>
                            <span class="fw-bold fs-4 text-primary" id="total-display">RD$0.00</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">
                            <i class="bi bi-save me-1"></i> Guardar Cotización
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let items = [];
let itemIndex = 0;

const buscarInput = document.getElementById('buscar-producto');
const resultadosDiv = document.getElementById('resultados-busqueda');

let searchTimeout = null;
buscarInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const q = this.value.trim();
    
    if (q.length < 2) {
        resultadosDiv.style.display = 'none';
        return;
    }
    
    searchTimeout = setTimeout(() => {
        fetch(`{{ route('cotizaciones.buscarProducto') }}?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) {
                    resultadosDiv.innerHTML = '<div class="p-3 text-muted text-center">No se encontraron productos</div>';
                } else {
                    resultadosDiv.innerHTML = data.map(p => `
                        <div class="p-2 border-bottom resultado-item" style="cursor: pointer;" 
                             onclick="agregarProducto(${JSON.stringify(p).replace(/"/g, '&quot;')})">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">${p.nombre}</div>
                                    <small class="text-muted">${p.codigo} · Stock: ${p.stock}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">RD$${parseFloat(p.precio).toFixed(2)}</div>
                                    <small class="text-muted">ITBIS: ${p.itbis_porcentaje}%</small>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }
                resultadosDiv.style.display = 'block';
            });
    }, 300);
});

document.addEventListener('click', function(e) {
    if (!buscarInput.contains(e.target) && !resultadosDiv.contains(e.target)) {
        resultadosDiv.style.display = 'none';
    }
});

function agregarProducto(p) {
    const existente = items.find(i => i.producto_id === p.id);
    if (existente) {
        existente.cantidad += 1;
        existente._mod = Date.now();
    } else {
        items.push({
            _key: itemIndex++,
            producto_id: p.id,
            codigo: p.codigo,
            nombre: p.nombre,
            unidad: p.unidad,
            cantidad: 1,
            precio_unitario: parseFloat(p.precio),
            descuento: 0,
            itbis_porcentaje: parseFloat(p.itbis_porcentaje) || 18,
            _mod: Date.now()
        });
    }
    buscarInput.value = '';
    resultadosDiv.style.display = 'none';
    renderItems();
}

function eliminarItem(key) {
    items = items.filter(i => i._key !== key);
    renderItems();
}

function cambiarCantidad(key, delta) {
    const item = items.find(i => i._key === key);
    if (item) {
        item.cantidad = Math.max(0.01, item.cantidad + delta);
        item._mod = Date.now();
        renderItems();
    }
}

function actualizarItem(key, field, value) {
    const item = items.find(i => i._key === key);
    if (item) {
        item[field] = parseFloat(value) || 0;
        item._mod = Date.now();
        renderItems();
    }
}

function renderItems() {
    const tbody = document.getElementById('items-tbody');
    const count = document.getElementById('items-count');
    
    if (items.length === 0) {
        tbody.innerHTML = `
            <tr id="empty-row">
                <td colspan="7" class="text-center py-4 text-muted">
                    <i class="bi bi-cart-x fs-2 opacity-50"></i>
                    <p class="mb-0">No hay productos agregados</p>
                    <small>Busca y agrega productos arriba</small>
                </td>
            </tr>
        `;
        count.textContent = '0 items';
        calcularTotales();
        return;
    }
    
    tbody.innerHTML = items.map(item => {
        const subtotal = item.cantidad * item.precio_unitario;
        const itbis = subtotal * (item.itbis_porcentaje / 100);
        const total = subtotal - item.descuento + itbis;
        
        return `
            <tr>
                <td>
                    <div class="fw-semibold">${item.nombre}</div>
                    <small class="text-muted">${item.codigo || ''} · ${item.unidad || 'Unidad'}</small>
                    <input type="hidden" name="items[${item._key}][producto_id]" value="${item.producto_id}">
                    <input type="hidden" name="items[${item._key}][nombre]" value="${item.nombre}">
                    <input type="hidden" name="items[${item._key}][unidad]" value="${item.unidad}">
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="cambiarCantidad(${item._key}, -1)">-</button>
                        <input type="number" name="items[${item._key}][cantidad]" class="form-control text-center" 
                               value="${item.cantidad}" min="0.01" step="0.01" onchange="actualizarItem(${item._key}, 'cantidad', this.value)">
                        <button type="button" class="btn btn-outline-secondary" onclick="cambiarCantidad(${item._key}, 1)">+</button>
                    </div>
                </td>
                <td>
                    <input type="number" name="items[${item._key}][precio_unitario]" class="form-control form-control-sm" 
                           value="${item.precio_unitario}" min="0" step="0.01" onchange="actualizarItem(${item._key}, 'precio_unitario', this.value)">
                </td>
                <td>
                    <input type="number" name="items[${item._key}][itbis_porcentaje]" class="form-control form-control-sm" 
                           value="${item.itbis_porcentaje}" min="0" max="100" step="0.01" onchange="actualizarItem(${item._key}, 'itbis_porcentaje', this.value)">
                </td>
                <td>
                    <input type="number" name="items[${item._key}][descuento]" class="form-control form-control-sm" 
                           value="${item.descuento}" min="0" step="0.01" onchange="actualizarItem(${item._key}, 'descuento', this.value)">
                </td>
                <td class="text-end fw-bold">RD$${total.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarItem(${item._key})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
    
    count.textContent = `${items.length} ${items.length === 1 ? 'item' : 'items'}`;
    calcularTotales();
}

function calcularTotales() {
    let subtotal = 0;
    let itbisTotal = 0;
    
    items.forEach(item => {
        const sub = item.cantidad * item.precio_unitario;
        const itb = sub * (item.itbis_porcentaje / 100);
        subtotal += sub - item.descuento;
        itbisTotal += itb;
    });
    
    const descuento = parseFloat(document.getElementById('descuento').value) || 0;
    const total = subtotal + itbisTotal - descuento;
    
    document.getElementById('subtotal-display').textContent = 'RD$' + subtotal.toFixed(2);
    document.getElementById('itbis-display').textContent = 'RD$' + itbisTotal.toFixed(2);
    document.getElementById('total-display').textContent = 'RD$' + total.toFixed(2);
}

document.getElementById('descuento').addEventListener('input', calcularTotales);
</script>
@endpush
@endsection
