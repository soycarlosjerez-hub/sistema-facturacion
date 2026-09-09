<?php $__env->startSection('title', 'Editar ' . $instalacion->numero); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; }
body.dark-mode .ui-card-title { color: #f1f5f9; }
body.dark-mode .ui-card-subtitle { color: #94a3b8; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    <div class="ui-header" style="--delay:0s;">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h1 class="ui-header-title">Editar: <?php echo e($instalacion->numero); ?></h1>
                    <div class="ui-header-meta">
                        <span>Modificar instalación de climatización</span>
                        <span class="divider">·</span>
                        <a href="<?php echo e(route('climatizacion.instalaciones.show', $instalacion)); ?>" style="color:rgba(255,255,255,.8);text-decoration:none;">
                            <i class="bi bi-eye"></i> Ver detalle
                        </a>
                        <span class="divider">·</span>
                        <a href="<?php echo e(route('climatizacion.instalaciones.index')); ?>" style="color:rgba(255,255,255,.8);text-decoration:none;">
                            <i class="bi bi-arrow-left"></i> Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s;max-width:960px;margin:0 auto;">
        <div style="height:4px;background:linear-gradient(90deg, #06b6d4, rgba(255,255,255,.3));"></div>
        <div style="padding:1.25rem 1.75rem 0;">
            <div class="ui-card-title" style="padding:0;margin-bottom:.15rem;">
                <i class="bi bi-pencil-square"></i> Editar Instalación #<?php echo e($instalacion->numero); ?>

            </div>
            <div class="ui-card-subtitle" style="padding:0;">Modifica los campos necesarios y guarda los cambios</div>
        </div>

        <div class="ui-card-body">
            <form action="<?php echo e(route('climatizacion.instalaciones.update', $instalacion)); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="ui-label">Cliente</label>
                        <select name="cliente_id" class="ui-select <?php $__errorArgs = ['cliente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Seleccionar cliente</option>
                            <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cliente->id); ?>" <?php echo e(old('cliente_id', $instalacion->cliente_id) == $cliente->id ? 'selected' : ''); ?>><?php echo e($cliente->nombre); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['cliente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-4">
                        <label class="ui-label">Tipo de Inmueble <span class="text-danger">*</span></label>
                        <select name="tipo_inmueble" class="ui-select <?php $__errorArgs = ['tipo_inmueble'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccionar</option>
                            <?php $__currentLoopData = \App\Models\Instalacion::TIPOS_INMUEBLE; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(old('tipo_inmueble', $instalacion->tipo_inmueble) === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['tipo_inmueble'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-4">
                        <label class="ui-label">Estado <span class="text-danger">*</span></label>
                        <select name="estado" class="ui-select <?php $__errorArgs = ['estado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <?php $__currentLoopData = \App\Models\Instalacion::ESTADOS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(old('estado', $instalacion->estado) === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['estado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-6">
                        <label class="ui-label">Programada Para</label>
                        <input type="datetime-local" name="programada_para" class="ui-input <?php $__errorArgs = ['programada_para'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('programada_para', $instalacion->programada_para ? $instalacion->programada_para->format('Y-m-d\TH:i') : '')); ?>">
                        <?php $__errorArgs = ['programada_para'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-6">
                        <label class="ui-label">Completada En</label>
                        <input type="datetime-local" name="completada_en" class="ui-input <?php $__errorArgs = ['completada_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('completada_en', $instalacion->completada_en ? $instalacion->completada_en->format('Y-m-d\TH:i') : '')); ?>">
                        <?php $__errorArgs = ['completada_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-12">
                        <label class="ui-label">Dirección de Instalación</label>
                        <input type="text" name="direccion_instalacion" class="ui-input <?php $__errorArgs = ['direccion_instalacion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('direccion_instalacion', $instalacion->direccion_instalacion)); ?>" placeholder="Dirección donde se realizará la instalación" maxlength="300">
                        <?php $__errorArgs = ['direccion_instalacion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-12">
                        <label class="ui-label">Nota Interna</label>
                        <textarea name="nota_interna" class="ui-textarea <?php $__errorArgs = ['nota_interna'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3" placeholder="Instrucciones u observaciones internas..." maxlength="2000"><?php echo e(old('nota_interna', $instalacion->nota_interna)); ?></textarea>
                        <?php $__errorArgs = ['nota_interna'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <!-- Productos -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0" style="font-size:.95rem;font-weight:600;"><i class="bi bi-box-seam me-2" style="color:#06b6d4;"></i>Productos de la Instalación</h5>
                        <button type="button" class="ui-btn ui-btn-primary ui-btn-sm" id="addProductRow">
                            <i class="bi bi-plus-circle me-1"></i>Agregar Producto
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="productosTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:45%;">Producto</th>
                                    <th style="width:15%;">Cantidad</th>
                                    <th style="width:20%;">Precio Unitario</th>
                                    <th style="width:15%;">Subtotal</th>
                                    <th style="width:5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="productosContainer">
                                <?php if(old('productos')): ?>
                                    <?php $__currentLoopData = old('productos'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="product-row">
                                        <td>
                                            <select name="productos[<?php echo e($index); ?>][producto_id]" class="form-select form-select-sm product-select">
                                                <option value="">Seleccionar</option>
                                                <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($p->id); ?>" <?php echo e($prod['producto_id'] == $p->id ? 'selected' : ''); ?> data-precio="<?php echo e($p->precio_venta ?? 0); ?>">
                                                        <?php echo e($p->nombre); ?> <?php if($p->codigo): ?>- <?php echo e($p->codigo); ?><?php endif; ?>
                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="productos[<?php echo e($index); ?>][cantidad]" class="form-control form-control-sm product-cantidad" value="<?php echo e($prod['cantidad'] ?? 1); ?>" min="1" step="1">
                                        </td>
                                        <td>
                                            <input type="number" name="productos[<?php echo e($index); ?>][precio_unitario]" class="form-control form-control-sm product-precio" value="<?php echo e($prod['precio_unitario'] ?? 0); ?>" min="0" step="0.01">
                                        </td>
                                        <td class="product-subtotal text-end fw-medium">$0.00</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-product-row"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <?php $__currentLoopData = $instalacion->productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="product-row">
                                        <td>
                                            <select name="productos[<?php echo e($loop->index); ?>][producto_id]" class="form-select form-select-sm product-select">
                                                <option value="">Seleccionar</option>
                                                <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($p->id); ?>" <?php echo e($producto->id == $p->id ? 'selected' : ''); ?> data-precio="<?php echo e($p->precio_venta ?? 0); ?>">
                                                        <?php echo e($p->nombre); ?> <?php if($p->codigo): ?>- <?php echo e($p->codigo); ?><?php endif; ?>
                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="productos[<?php echo e($loop->index); ?>][cantidad]" class="form-control form-control-sm product-cantidad" value="<?php echo e($producto->pivot->cantidad); ?>" min="1" step="1">
                                        </td>
                                        <td>
                                            <input type="number" name="productos[<?php echo e($loop->index); ?>][precio_unitario]" class="form-control form-control-sm product-precio" value="<?php echo e($producto->pivot->precio_unitario); ?>" min="0" step="0.01">
                                        </td>
                                        <td class="product-subtotal text-end fw-medium">$0.00</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-product-row"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-end" id="productosTotal">$0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php $__errorArgs = ['productos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php $__errorArgs = ['productos.*.producto_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small">Debe seleccionar un producto válido.</div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="ui-sticky-bar" style="position:sticky;bottom:0;left:0;right:0;background:rgba(255,255,255,.85);backdrop-filter:blur(20px);border-top:2px solid #06b6d4;padding:.7rem 1.5rem;z-index:1050;box-shadow:0 -4px 20px rgba(0,0,0,.08);margin:0 -1.75rem -1.5rem;border-radius:0 0 var(--radius-2xl) var(--radius-2xl);">
                    <div class="ui-sticky-bar-inner">
                        <a href="<?php echo e(route('climatizacion.instalaciones.show', $instalacion)); ?>" class="ui-btn ui-btn-ghost"><i class="bi bi-x-lg"></i> Cancelar</a>
                        <button type="submit" class="ui-btn ui-btn-solid"><i class="bi bi-check-lg"></i> Actualizar</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('productosContainer');
    const addBtn = document.getElementById('addProductRow');
    const productosOptions = `<?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($p->id); ?>" data-precio="<?php echo e($p->precio_venta ?? 0); ?>"><?php echo e($p->nombre); ?> <?php if($p->codigo): ?>- <?php echo e($p->codigo); ?><?php endif; ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>`;

    let rowIndex = container.querySelectorAll('.product-row').length;

    function updateSubtotal(row) {
        const cantidad = parseFloat(row.querySelector('.product-cantidad').value) || 0;
        const precio = parseFloat(row.querySelector('.product-precio').value) || 0;
        row.querySelector('.product-subtotal').textContent = '$' + (cantidad * precio).toFixed(2);
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        container.querySelectorAll('.product-row').forEach(function (row) {
            total += (parseFloat(row.querySelector('.product-cantidad').value) || 0) * (parseFloat(row.querySelector('.product-precio').value) || 0);
        });
        document.getElementById('productosTotal').textContent = '$' + total.toFixed(2);
    }

    function addRow(data) {
        const tr = document.createElement('tr');
        tr.className = 'product-row';
        const i = rowIndex++;
        tr.innerHTML = `
            <td>
                <select name="productos[${i}][producto_id]" class="form-select form-select-sm product-select">
                    <option value="">Seleccionar</option>
                    ${productosOptions}
                </select>
            </td>
            <td>
                <input type="number" name="productos[${i}][cantidad]" class="form-control form-control-sm product-cantidad" value="${data ? data.cantidad : 1}" min="1" step="1">
            </td>
            <td>
                <input type="number" name="productos[${i}][precio_unitario]" class="form-control form-control-sm product-precio" value="${data ? data.precio_unitario : 0}" min="0" step="0.01">
            </td>
            <td class="product-subtotal text-end fw-medium">$0.00</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-product-row"><i class="bi bi-trash"></i></button>
            </td>
        `;
        container.appendChild(tr);

        if (data && data.producto_id) {
            const select = tr.querySelector('.product-select');
            select.value = data.producto_id;
        }

        tr.querySelector('.product-cantidad').addEventListener('input', function () { updateSubtotal(tr); });
        tr.querySelector('.product-precio').addEventListener('input', function () { updateSubtotal(tr); });
        tr.querySelector('.product-select').addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.dataset.precio) {
                const precioInput = tr.querySelector('.product-precio');
                if (!precioInput.value || parseFloat(precioInput.value) === 0) {
                    precioInput.value = parseFloat(opt.dataset.precio).toFixed(2);
                }
            }
            updateSubtotal(tr);
        });
        tr.querySelector('.remove-product-row').addEventListener('click', function () { tr.remove(); updateTotal(); });

        const select = tr.querySelector('.product-select');
        if (select.value) select.dispatchEvent(new Event('change'));
        updateSubtotal(tr);
    }

    addBtn.addEventListener('click', function () { addRow(); });

    container.querySelectorAll('.product-row').forEach(function (row) {
        const cantidad = row.querySelector('.product-cantidad');
        const precio = row.querySelector('.product-precio');
        const select = row.querySelector('.product-select');
        cantidad.addEventListener('input', function () { updateSubtotal(row); });
        precio.addEventListener('input', function () { updateSubtotal(row); });
        select.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.dataset.precio) {
                const precioInput = row.querySelector('.product-precio');
                if (!precioInput.value || parseFloat(precioInput.value) === 0) {
                    precioInput.value = parseFloat(opt.dataset.precio).toFixed(2);
                }
            }
            updateSubtotal(row);
        });
        const btn = row.querySelector('.remove-product-row');
        if (btn) btn.addEventListener('click', function () { row.remove(); updateTotal(); });
        updateSubtotal(row);
    });

    container.querySelectorAll('.product-row .product-select').forEach(function (sel) {
        if (sel.value) sel.dispatchEvent(new Event('change'));
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/climatizacion/instalaciones/edit.blade.php ENDPATH**/ ?>