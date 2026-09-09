<div class="ui-card-title">
    <i class="bi bi-grid-3x3-gap"></i>Mesas
</div>
<div class="ui-card-subtitle">Crea una mesa para el restaurante.</div>

<form action="<?php echo e(route('setup.step')); ?>" method="POST" class="row g-3" id="form-mesa">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="step" value="mesa">
    <?php
        $siguienteNumero = str_pad(
            \App\Models\Mesa::where('tenant_id', auth()->user()->business_instance_id)->count() + 1,
            2, '0', STR_PAD_LEFT
        );
    ?>
    <div class="col-md-3">
        <label class="ui-label">Número <span class="text-danger">*</span></label>
        <input type="text" name="numero" class="ui-input"
               placeholder="<?php echo e($siguienteNumero); ?>" value="<?php echo e($siguienteNumero); ?>" required maxlength="20">
    </div>
    <div class="col-md-9">
        <label class="ui-label">Nombre de la Mesa</label>
        <input type="text" name="nombre" class="ui-input" placeholder="Ej. Terraza, VIP">
    </div>
    <div class="col-md-3">
        <label class="ui-label">Capacidad</label>
        <input type="number" name="capacidad" class="ui-input" placeholder="4" min="1">
    </div>
    <div class="col-md-6">
        <label class="ui-label">Ubicación</label>
        <select name="ubicacion_id" class="ui-select" required>
            <?php $__currentLoopData = \App\Models\MesaUbicacion::where('tenant_id', auth()->user()->business_instance_id)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($u->id); ?>"><?php echo e($u->nombre); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="ui-label">Categoría</label>
        <select name="categoria_id" class="ui-select" required>
            <?php $__currentLoopData = \App\Models\MesaCategoria::where('tenant_id', auth()->user()->business_instance_id)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($c->id); ?>"><?php echo e($c->nombre); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="<?php echo e(route('setup.wizard')); ?>" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-mesa" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/setup/_step-mesa.blade.php ENDPATH**/ ?>