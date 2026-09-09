<div class="ui-card-title">
    <i class="bi bi-wrench-adjustable"></i>Mantenimiento
</div>
<div class="ui-card-subtitle">Registra un mantenimiento preventivo o correctivo (opcional).</div>

<form action="<?php echo e(route('setup.step')); ?>" method="POST" class="row g-3" id="form-mantenimiento">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="step" value="mantenimiento">
    
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
        <label class="ui-label">Tipo</label>
        <select name="tipo" class="ui-select">
            <option value="preventivo">Preventivo</option>
            <option value="correctivo">Correctivo</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Descripción de Falla</label>
        <textarea name="descripcion_falla" class="ui-textarea" placeholder="Describe el problema o tarea"></textarea>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Programada Para</label>
        <input type="datetime-local" name="programada_para" class="ui-input">
    </div>

    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="<?php echo e(route('setup.wizard')); ?>" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-mantenimiento" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/setup/_step-mantenimiento.blade.php ENDPATH**/ ?>