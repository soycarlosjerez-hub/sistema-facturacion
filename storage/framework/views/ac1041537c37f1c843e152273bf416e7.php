<?php $__env->startSection('title', 'Nuevo Contrato de Mantenimiento'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; }
body.dark-mode .ui-page { --accent: #22d3ee; --accent-rgb: 34,211,238; --accent-hover: #06b6d4; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-file-earmark-plus"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nuevo Contrato de Mantenimiento</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-wind me-1"></i>Climatización
                        <span class="mx-2">·</span>
                        <a href="<?php echo e(route('climatizacion.contratos.index')); ?>" class="text-white-50 text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <form action="<?php echo e(route('climatizacion.contratos.store')); ?>" method="POST" id="contratoForm">
        <?php echo csrf_field(); ?>

        
        <div class="ui-card" style="--delay:.1s">
            <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
            <div class="ui-card-body">
                <h5 class="ui-card-title" style="padding:0;margin-bottom:1.25rem;">
                    <i class="bi bi-info-circle"></i> Datos Generales
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label" for="cliente_id">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" id="cliente_id" class="ui-select <?php $__errorArgs = ['cliente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccionar cliente...</option>
                            <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cliente->id); ?>" <?php echo e(old('cliente_id') == $cliente->id ? 'selected' : ''); ?>>
                                    <?php echo e($cliente->nombre); ?> <?php echo e($cliente->identificacion ? '- '.$cliente->identificacion : ''); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['cliente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-3">
                        <label class="ui-label" for="tipo_periodicidad">Periodicidad <span class="text-danger">*</span></label>
                        <select name="tipo_periodicidad" id="tipo_periodicidad" class="ui-select <?php $__errorArgs = ['tipo_periodicidad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccionar...</option>
                            <?php $__currentLoopData = \App\Models\ContratoMantenimiento::PERIODICIDADES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php echo e(old('tipo_periodicidad') == $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['tipo_periodicidad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-3">
                        <label class="ui-label" for="valor_mensual">Valor Mensual (RD$) <span class="text-danger">*</span></label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">RD$</span>
                            <input type="number" step="0.01" min="0" name="valor_mensual" id="valor_mensual"
                                   class="ui-input <?php $__errorArgs = ['valor_mensual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('valor_mensual')); ?>" placeholder="0.00" required>
                        </div>
                        <?php $__errorArgs = ['valor_mensual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="ui-card" style="--delay:.15s">
            <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
            <div class="ui-card-body">
                <h5 class="ui-card-title" style="padding:0;margin-bottom:1.25rem;">
                    <i class="bi bi-calendar-range"></i> Vigencia
                </h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="ui-label" for="vigencia_desde">Vigencia Desde <span class="text-danger">*</span></label>
                        <input type="date" name="vigencia_desde" id="vigencia_desde"
                               class="ui-input <?php $__errorArgs = ['vigencia_desde'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('vigencia_desde')); ?>" required>
                        <?php $__errorArgs = ['vigencia_desde'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="vigencia_hasta">Vigencia Hasta <span class="text-danger">*</span></label>
                        <input type="date" name="vigencia_hasta" id="vigencia_hasta"
                               class="ui-input <?php $__errorArgs = ['vigencia_hasta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('vigencia_hasta')); ?>" required>
                        <?php $__errorArgs = ['vigencia_hasta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="ui-card" style="--delay:.2s">
            <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
            <div class="ui-card-body">
                <h5 class="ui-card-title" style="padding:0;margin-bottom:1.25rem;">
                    <i class="bi bi-shield-check"></i> Cobertura y Visitas
                </h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="ui-label" for="deducible">Deducible (RD$)</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">RD$</span>
                            <input type="number" step="0.01" min="0" name="deducible" id="deducible"
                                   class="ui-input <?php $__errorArgs = ['deducible'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('deducible', 0)); ?>" placeholder="0.00">
                        </div>
                        <?php $__errorArgs = ['deducible'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="cobertura_maxima">Cobertura Máxima (RD$)</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">RD$</span>
                            <input type="number" step="0.01" min="0" name="cobertura_maxima" id="cobertura_maxima"
                                   class="ui-input <?php $__errorArgs = ['cobertura_maxima'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('cobertura_maxima', 0)); ?>" placeholder="0.00">
                        </div>
                        <?php $__errorArgs = ['cobertura_maxima'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-4 d-flex align-items-end pb-2">
                        <div class="form-check">
                            <input type="hidden" name="incluye_visitas" value="0">
                            <input type="checkbox" name="incluye_visitas" id="incluye_visitas" class="form-check-input"
                                   value="1" <?php echo e(old('incluye_visitas') ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold" for="incluye_visitas">
                                <i class="bi bi-tools me-1"></i> Incluye Visitas
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4" id="visitasAnualesGroup" style="<?php echo e(old('incluye_visitas') ? '' : 'display:none;'); ?>">
                        <label class="ui-label" for="num_visitas_anuales">Visitas Anuales</label>
                        <input type="number" min="0" name="num_visitas_anuales" id="num_visitas_anuales"
                               class="ui-input <?php $__errorArgs = ['num_visitas_anuales'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('num_visitas_anuales', 0)); ?>" placeholder="0">
                        <?php $__errorArgs = ['num_visitas_anuales'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="ui-card" style="--delay:.25s">
            <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
            <div class="ui-card-body">
                <h5 class="ui-card-title" style="padding:0;margin-bottom:1.25rem;">
                    <i class="bi bi-cpu"></i> Equipos Cubiertos
                </h5>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="ui-label" for="equipos_cubiertos">Descripción de los equipos cubiertos</label>
                        <textarea name="equipos_cubiertos" id="equipos_cubiertos"
                                  class="ui-textarea <?php $__errorArgs = ['equipos_cubiertos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  rows="4" placeholder="Detalle aquí los equipos cubiertos por este contrato..."><?php echo e(old('equipos_cubiertos')); ?></textarea>
                        <?php $__errorArgs = ['equipos_cubiertos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text">Describa los equipos, marcas, modelos incluidos en la cobertura.</div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="<?php echo e(route('climatizacion.contratos.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill">
                    <i class="bi bi-check-lg"></i> Crear Contrato
                </button>
            </div>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const incluyeVisitas = document.getElementById('incluye_visitas');
    const visitasGroup = document.getElementById('visitasAnualesGroup');
    if (incluyeVisitas && visitasGroup) {
        incluyeVisitas.addEventListener('change', function() {
            visitasGroup.style.display = this.checked ? '' : 'none';
            if (!this.checked) {
                document.getElementById('num_visitas_anuales').value = 0;
            }
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/climatizacion/contratos/create.blade.php ENDPATH**/ ?>