<div class="ui-card-title">
    <i class="bi bi-tools"></i>Orden de Reparación
</div>
<div class="ui-card-subtitle">Crea una orden técnica para reparación de equipos (opcional).</div>

<form action="<?php echo e(route('setup.step')); ?>" method="POST" class="row g-3" id="form-orden-tecnica">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="step" value="orden-tecnica">
    
    <div class="col-md-6">
        <label class="ui-label">Cliente</label>
        <select name="cliente_id" class="ui-select">
            <option value="">-- Seleccionar --</option>
            <?php $__currentLoopData = \App\Models\Cliente::where('tenant_id', auth()->user()->business_instance_id)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($c->id); ?>"><?php echo e($c->nombre); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    
    <div class="col-md-6">
        <label class="ui-label">Equipo</label>
        <select name="equipo_id" class="ui-select">
            <option value="">-- Seleccionar --</option>
            <?php $__currentLoopData = \App\Models\Equipo::where('tenant_id', auth()->user()->business_instance_id)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($e->id); ?>"><?php echo e($e->marca); ?> <?php echo e($e->modelo); ?> (<?php echo e($e->serial_imei); ?>)</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Tipo de Servicio</label>
        <select name="tipo_servicio" class="ui-select">
            <option value="hardware">Hardware</option>
            <option value="software">Software</option>
            <option value="desbloqueo">Desbloqueo</option>
            <option value="recuperacion_datos">Recuperación de Datos</option>
            <option value="mantenimiento">Mantenimiento</option>
            <option value="otro">Otro</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Técnico Asignado</label>
        <select name="tecnico_id" class="ui-select">
            <option value="">-- Seleccionar --</option>
            <?php $__currentLoopData = \App\Models\Tecnico::where('tenant_id', auth()->user()->business_instance_id)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($t->id); ?>"><?php echo e($t->nombre); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="col-12">
        <label class="ui-label">Problema Reportado</label>
        <textarea name="problema_reportado" class="ui-textarea" placeholder="Describe el problema"></textarea>
    </div>

    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="<?php echo e(route('setup.wizard')); ?>" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-orden-tecnica" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/setup/_step-orden-tecnica.blade.php ENDPATH**/ ?>