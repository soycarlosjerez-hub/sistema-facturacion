<div class="ui-card-title">
    <i class="bi bi-houses"></i>Servicio Domótico
</div>
<div class="ui-card-subtitle">Registra un proyecto de domótica para un cliente (opcional).</div>

<form action="<?php echo e(route('setup.step')); ?>" method="POST" class="row g-3" id="form-servicio-domotica">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="step" value="servicio-domotica">
    
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
        <label class="ui-label">Tipo de Servicio</label>
        <select name="tipo_servicio" class="ui-select">
            <option value="camaras_seguridad">Cámaras de Seguridad</option>
            <option value="alarmas">Alarmas</option>
            <option value="control_acceso">Control de Acceso</option>
            <option value="redes">Redes</option>
            <option value="automatizacion">Automatización</option>
            <option value="sonido">Sonido</option>
            <option value="iluminacion">Iluminación</option>
            <option value="otro">Otro</option>
        </select>
    </div>

    <div class="col-12">
        <label class="ui-label">Título del Proyecto</label>
        <input type="text" name="titulo" class="ui-input" placeholder="Ej. Cámaras para residencia" maxlength="255">
    </div>

    <div class="col-12">
        <label class="ui-label">Descripción</label>
        <textarea name="descripcion" class="ui-textarea" placeholder="Detalles del proyecto"></textarea>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Presupuesto</label>
        <div class="ui-input-group">
            <span class="ui-input-group-text">RD$</span>
            <input type="number" step="0.01" name="presupuesto" class="ui-input" placeholder="0.00">
        </div>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Fecha Programada</label>
        <input type="date" name="fecha_programada" class="ui-input">
    </div>

    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="<?php echo e(route('setup.wizard')); ?>" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-servicio-domotica" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/setup/_step-servicio-domotica.blade.php ENDPATH**/ ?>