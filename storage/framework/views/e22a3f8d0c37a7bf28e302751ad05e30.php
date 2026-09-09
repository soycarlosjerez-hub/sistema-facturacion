<?php $__env->startSection('title', 'Editar Pago'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .form-floating-modern { position: relative; margin-bottom: 1rem; }
    .form-floating-modern .form-icon { position: absolute; top: 50%; left: 14px; transform: translateY(-50%); color: #94a3b8; z-index: 5; font-size: 1.1rem; pointer-events: none; }
    .form-floating-modern .form-control { padding-left: 42px; height: 50px; border-radius: 12px; border: 1.5px solid #e2e8f0; }
    .form-floating-modern .form-control:focus { border-color: #f43f5e; box-shadow: 0 0 0 3px rgba(244,63,94,.12); }
    .form-floating-modern .form-label-float { position: absolute; top: 50%; left: 42px; transform: translateY(-50%); color: #94a3b8; background: transparent; padding: 0 4px; }
    .form-floating-modern .form-control:focus + .form-label-float,
    .form-floating-modern .form-control:not(:placeholder-shown) + .form-label-float { top: -10px; left: 36px; font-size: .75rem; color: #f43f5e; background: #fff; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#f43f5e;--accent-rgb:244,63,94;--accent-hover:#e11d48;--delay:0s;">
    <div class="ui-header mb-4" style="background: linear-gradient(135deg, #f43f5e, #ec4899);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-pencil-square"></i></div>
                <div>
                    <h4 class="ui-header-title">Editar Pago</h4>
                    <div class="ui-header-meta">Modifica los datos del pago</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('alquileres.pagos.index')); ?>" class="ui-btn ui-btn-primary rounded-pill">
                    <i class="bi bi-eye me-1"></i> Ver Todos
                </a>
            </div>
        </div>
    </div>

    <form action="<?php echo e(route('alquileres.pagos.update', $pago)); ?>" method="POST">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ui-card" style="--delay:.1s;">
                    <div class="ui-card-accent red"></div>
                    <div class="ui-card-body p-4">
                        <div class="ui-card-title"><i class="bi bi-info-circle"></i>Datos del Pago</div>
                        <div class="ui-card-subtitle"></div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="contrato_id" class="ui-label">Contrato <span class="text-danger">*</span></label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-file-earmark-text"></i></span>
                                        <select name="contrato_id" id="contrato_id" class="ui-select" required>
                                            <?php $__currentLoopData = $contratos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrato): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($contrato->id); ?>" <?php echo e(old('contrato_id',$pago->contrato_id)==$contrato->id?'selected':''); ?>>
                                                    <?php echo e($contrato->vivienda->nombre); ?> - <?php echo e($contrato->inquilino->nombre); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="monto" class="ui-label">Monto <span class="text-danger">*</span></label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                        <input type="number" step="0.01" name="monto" id="monto" class="ui-input" value="<?php echo e(old('monto', $pago->monto)); ?>" placeholder=" " required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_pago" class="ui-label">Fecha de Pago <span class="text-danger">*</span></label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-calendar"></i></span>
                                        <input type="date" name="fecha_pago" id="fecha_pago" class="ui-input" value="<?php echo e(old('fecha_pago', $pago->fecha_pago->format('Y-m-d'))); ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="mes_cobrado" class="ui-label">Mes Cobrado <span class="text-danger">*</span></label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-calendar-month"></i></span>
                                        <select name="mes_cobrado" id="mes_cobrado" class="ui-select" required>
                                            <?php $__currentLoopData = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $mes): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($index+1); ?>" <?php echo e(old('mes_cobrado',$pago->mes_cobrado)==$index+1?'selected':''); ?>><?php echo e($mes); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ano_cobrado" class="ui-label">Año <span class="text-danger">*</span></label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-calendar-year"></i></span>
                                        <input type="number" name="ano_cobrado" id="ano_cobrado" class="ui-input" value="<?php echo e(old('ano_cobrado', $pago->ano_cobrado)); ?>" min="2020" max="2100" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="metodo_pago" class="ui-label">Método de Pago <span class="text-danger">*</span></label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-credit-card"></i></span>
                                        <select name="metodo_pago" id="metodo_pago" class="ui-select" required>
                                            <option value="efectivo" <?php echo e(old('metodo_pago',$pago->metodo_pago)=='efectivo'?'selected':''); ?>>Efectivo</option>
                                            <option value="tarjeta" <?php echo e(old('metodo_pago',$pago->metodo_pago)=='tarjeta'?'selected':''); ?>>Tarjeta</option>
                                            <option value="transferencia" <?php echo e(old('metodo_pago',$pago->metodo_pago)=='transferencia'?'selected':''); ?>>Transferencia</option>
                                            <option value="deposito" <?php echo e(old('metodo_pago',$pago->metodo_pago)=='deposito'?'selected':''); ?>>Depósito</option>
                                            <option value="otro" <?php echo e(old('metodo_pago',$pago->metodo_pago)=='otro'?'selected':''); ?>>Otro</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="recibo_numero" class="ui-label">Número de Recibo</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-receipt"></i></span>
                                        <input type="text" name="recibo_numero" id="recibo_numero" class="ui-input" value="<?php echo e(old('recibo_numero', $pago->recibo_numero)); ?>" placeholder=" ">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notas" class="ui-label">Notas</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-sticky" style="top:14px;"></i></span>
                                        <textarea name="notas" id="notas" class="ui-textarea" placeholder=" " rows="2"><?php echo e(old('notas', $pago->notas)); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-5 fw-bold shadow-sm" style="background:linear-gradient(135deg,#f43f5e,#e11d48);border:0;"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
            <a href="<?php echo e(route('alquileres.pagos.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill px-4">Cancelar</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/alquileres/pagos/edit.blade.php ENDPATH**/ ?>