<?php $__env->startSection('title', 'Editar Cotización ' . $cotizacion->numero); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
body.dark-mode .ui-sticky-bar {
    border-top-color: #8b5cf6 !important;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div>
                    <h3 class="ui-header-title">Editar Cotización</h3>
                    <div class="ui-header-meta"><?php echo e($cotizacion->numero); ?></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('cotizaciones.show', $cotizacion)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-x-lg me-1"></i> Cancelar
                </a>
            </div>
        </div>
    </div>

    <form id="cotizacion-form" action="<?php echo e(route('cotizaciones.update', $cotizacion)); ?>" method="POST" autocomplete="off">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="row g-3">
            <!-- Columna izquierda -->
            <div class="col-lg-8">
                <div class="ui-card mb-3" style="--delay:.1s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-info-circle"></i>
                        Información General
                    </div>
                    <div class="ui-card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="ui-label small fw-semibold">Número</label>
                                <input type="text" class="ui-input" value="<?php echo e($cotizacion->numero); ?>" disabled>
                            </div>
                            <div class="col-md-5">
                                <label class="ui-label small fw-semibold">Cliente</label>
                                <select name="cliente_id" class="ui-select">
                                    <option value="">-- Consumidor Final --</option>
                                    <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($cliente->id); ?>" <?php echo e(old('cliente_id', $cotizacion->cliente_id) == $cliente->id ? 'selected' : ''); ?>>
                                            <?php echo e($cliente->nombre); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="ui-label small fw-semibold">Fecha</label>
                                <input type="date" name="fecha" class="ui-input" value="<?php echo e(old('fecha', $cotizacion->fecha->format('Y-m-d'))); ?>" required>
                            </div>
                            <div class="col-md-2">
                                <label class="ui-label small fw-semibold">Válida hasta</label>
                                <input type="date" name="fecha_validez" class="ui-input" value="<?php echo e(old('fecha_validez', $cotizacion->fecha_validez->format('Y-m-d'))); ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ui-card mb-3" style="--delay:.15s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-box-seam"></i>
                        Productos
                        <span class="badge bg-primary bg-opacity-10 text-primary ms-auto" id="items-count">0 items</span>
                    </div>
                    <div class="ui-card-body">
                        <div class="position-relative mb-3">
                            <div class="ui-input-group">
                                <span class="ui-input-group-text bg-white border-end-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="buscar-producto" class="ui-input border-start-0" placeholder="Buscar productos...">
                            </div>
                            <div id="resultados-busqueda" class="position-absolute w-100 bg-white border rounded shadow-lg" style="z-index: 1000; max-height: 300px; overflow-y: auto; display: none;"></div>
                        </div>

                        <div class="table-responsive">
                            <table class="ui-table table-hover align-middle">
                                <thead>
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
                                <tbody id="items-tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="ui-card" style="--delay:.2s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="ui-label small fw-semibold"><i class="bi bi-sticky me-1"></i> Notas</label>
                                <textarea name="notas" class="ui-input" rows="3"><?php echo e(old('notas', $cotizacion->notas)); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label small fw-semibold"><i class="bi bi-file-text me-1"></i> Términos y Condiciones</label>
                                <textarea name="condiciones" class="ui-input" rows="3"><?php echo e(old('condiciones', $cotizacion->condiciones)); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: resumen -->
            <div class="col-lg-4">
                <div class="ui-card sticky-top" style="top: 20px; --delay:.1s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-calculator"></i>
                        Resumen
                    </div>
                    <div class="ui-card-body">
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
                            <div class="ui-input-group input-group-sm" style="width: 130px;">
                                <span class="ui-input-group-text bg-white">RD$</span>
                                <input type="number" name="descuento" id="descuento" class="ui-input text-end" 
                                       value="<?php echo e(old('descuento', $cotizacion->descuento)); ?>" min="0" step="0.01">
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5">Total:</span>
                            <span class="fw-bold fs-4 text-primary" id="total-display">RD$0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Sticky Save Bar -->
    <div class="ui-sticky-bar">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small d-none d-md-inline">Editando: <?php echo e($cotizacion->numero); ?></span>
            <button type="submit" form="cotizacion-form" class="ui-btn ui-btn-solid rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-save me-1"></i> Actualizar Cotización
            </button>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let items = [];
let itemIndex = 0;

// Cargar items existentes
<?php $__currentLoopData = $cotizacion->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
items.push({
    _key: itemIndex++,
    producto_id: <?php echo e($item->producto_id ?? 'null'); ?>,
    codigo: '<?php echo e($item->codigo ?? ''); ?>',
    nombre: '<?php echo e(addslashes($item->nombre)); ?>',
    unidad: '<?php echo e($item->unidad); ?>',
    cantidad: <?php echo e($item->cantidad); ?>,
    precio_unitario: <?php echo e($item->precio_unitario); ?>,
    descuento: <?php echo e($item->descuento); ?>,
    itbis_porcentaje: <?php echo e($item->itbis_porcentaje); ?>

});
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

const buscarInput = document.getElementById('buscar-producto');
const resultadosDiv = document.getElementById('resultados-busqueda');

let searchTimeout = null;
buscarInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const q = this.value.trim();
    if (q.length < 2) { resultadosDiv.style.display = 'none'; return; }
    searchTimeout = setTimeout(() => {
        fetch(`<?php echo e(route('cotizaciones.buscarProducto')); ?>?q=${encodeURIComponent(q)}`)
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
    if (existente) { existente.cantidad += 1; existente._mod = Date.now(); }
    else {
        items.push({
            _key: itemIndex++, producto_id: p.id, codigo: p.codigo, nombre: p.nombre,
            unidad: p.unidad, cantidad: 1, precio_unitario: parseFloat(p.precio),
            descuento: 0, itbis_porcentaje: parseFloat(p.itbis_porcentaje) || 18, _mod: Date.now()
        });
    }
    buscarInput.value = '';
    resultadosDiv.style.display = 'none';
    renderItems();
}

function eliminarItem(key) { items = items.filter(i => i._key !== key); renderItems(); }
function cambiarCantidad(key, delta) {
    const item = items.find(i => i._key === key);
    if (item) { item.cantidad = Math.max(0.01, item.cantidad + delta); item._mod = Date.now(); renderItems(); }
}
function actualizarItem(key, field, value) {
    const item = items.find(i => i._key === key);
    if (item) { item[field] = parseFloat(value) || 0; item._mod = Date.now(); renderItems(); }
}

function renderItems() {
    const tbody = document.getElementById('items-tbody');
    const count = document.getElementById('items-count');
    
    if (items.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-cart-x fs-2 opacity-50"></i><p class="mb-0">No hay productos</p></td></tr>`;
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
                    <div class="ui-input-group input-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="cambiarCantidad(${item._key}, -1)">-</button>
                        <input type="number" name="items[${item._key}][cantidad]" class="ui-input text-center" 
                               value="${item.cantidad}" min="0.01" step="0.01" onchange="actualizarItem(${item._key}, 'cantidad', this.value)">
                        <button type="button" class="btn btn-outline-secondary" onclick="cambiarCantidad(${item._key}, 1)">+</button>
                    </div>
                </td>
                <td><input type="number" name="items[${item._key}][precio_unitario]" class="ui-input form-control-sm" value="${item.precio_unitario}" min="0" step="0.01" onchange="actualizarItem(${item._key}, 'precio_unitario', this.value)"></td>
                <td><input type="number" name="items[${item._key}][itbis_porcentaje]" class="ui-input form-control-sm" value="${item.itbis_porcentaje}" min="0" max="100" step="0.01" onchange="actualizarItem(${item._key}, 'itbis_porcentaje', this.value)"></td>
                <td><input type="number" name="items[${item._key}][descuento]" class="ui-input form-control-sm" value="${item.descuento}" min="0" step="0.01" onchange="actualizarItem(${item._key}, 'descuento', this.value)"></td>
                <td class="text-end fw-bold">RD$${total.toFixed(2)}</td>
                <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarItem(${item._key})"><i class="bi bi-trash"></i></button></td>
            </tr>
        `;
    }).join('');
    
    count.textContent = `${items.length} ${items.length === 1 ? 'item' : 'items'}`;
    calcularTotales();
}

function calcularTotales() {
    let subtotal = 0, itbisTotal = 0;
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

// Render inicial
renderItems();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/cotizaciones/edit.blade.php ENDPATH**/ ?>