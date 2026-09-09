<div class="ui-card-title">
    <i class="bi bi-cash-stack"></i>Caja
</div>
<div class="ui-card-subtitle">Crea la caja registradora para el punto de venta.</div>

<form action="<?php echo e(route('setup.step')); ?>" method="POST" class="row g-3">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="step" value="caja">
    <div class="col-md-6">
        <label class="ui-label">Nombre de la Caja</label>
        <input type="text" name="nombre" class="ui-input" placeholder="Caja Principal" required>
    </div>
    <div class="col-md-6">
        <label class="ui-label">Sucursal</label>
        <select name="sucursal_id" class="ui-select" required>
            <?php $__currentLoopData = \App\Models\Sucursal::where('tenant_id', auth()->user()->business_instance_id)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($s->id); ?>"><?php echo e($s->nombre); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="<?php echo e(route('setup.wizard')); ?>" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/setup/_step-caja.blade.php ENDPATH**/ ?>