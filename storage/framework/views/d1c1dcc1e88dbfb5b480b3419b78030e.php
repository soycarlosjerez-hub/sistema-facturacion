<?php $__env->startSection('title', 'Editar Cliente'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.form-section-title {
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 1rem;
    padding-bottom: .5rem;
    border-bottom: 1px solid #e2e8f0;
}
body.dark-mode .form-section-title { color: #94a3b8; border-bottom-color: #1e293b; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Cliente</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-person me-1"></i>
                        <?php echo e($cliente->nombre); ?>

                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('clientes.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <?php if(session('error')): ?>
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form id="clienteForm" method="POST" action="<?php echo e(route('clientes.update', $cliente)); ?>">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-person-vcard"></i> Información General</div>
            <div class="ui-card-subtitle">Datos principales del cliente</div>
            <div class="ui-card-body">
                <div class="form-section-title">Identificación</div>
                <div class="row g-3">
                    <div class="col-lg-4">
                        <label class="ui-label">Tipo de Persona <span class="text-danger">*</span></label>
                        <select name="tipo_persona" class="ui-select <?php $__errorArgs = ['tipo_persona'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="fisica" <?php echo e(old('tipo_persona', $cliente->tipo_persona) === 'fisica' ? 'selected' : ''); ?>>Física</option>
                            <option value="juridica" <?php echo e(old('tipo_persona', $cliente->tipo_persona) === 'juridica' ? 'selected' : ''); ?>>Jurídica</option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">RNC / Cédula</label>
                        <input type="text" name="rnc" class="ui-input <?php $__errorArgs = ['rnc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('rnc', $cliente->rnc)); ?>" placeholder="000-0000000-0" maxlength="20">
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Nombre / Razón Social <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="ui-input <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nombre', $cliente->nombre)); ?>" required maxlength="255" placeholder="Nombre completo">
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-lg-4">
                        <label class="ui-label">Teléfono</label>
                        <input type="text" name="telefono" class="ui-input <?php $__errorArgs = ['telefono'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('telefono', $cliente->telefono)); ?>" placeholder="809-555-0100">
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Email</label>
                        <input type="email" name="email" class="ui-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('email', $cliente->email)); ?>" placeholder="cliente@ejemplo.com">
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Persona de Contacto</label>
                        <input type="text" name="persona_contacto" class="ui-input <?php $__errorArgs = ['persona_contacto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('persona_contacto', $cliente->persona_contacto)); ?>" placeholder="Nombre de contacto">
                    </div>
                </div>

                <div class="form-section-title mt-4">Ubicación</div>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Dirección</label>
                        <textarea name="direccion" rows="2" class="ui-textarea <?php $__errorArgs = ['direccion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Calle, número, sector..."><?php echo e(old('direccion', $cliente->direccion)); ?></textarea>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Ciudad</label>
                        <input type="text" name="ciudad" class="ui-input <?php $__errorArgs = ['ciudad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('ciudad', $cliente->ciudad)); ?>" placeholder="Santo Domingo">
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Código Postal</label>
                        <input type="text" name="codigo_postal" class="ui-input <?php $__errorArgs = ['codigo_postal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('codigo_postal', $cliente->codigo_postal)); ?>" placeholder="10101">
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card" style="--delay:.15s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-gear"></i> Configuración Comercial</div>
            <div class="ui-card-subtitle">Clasificación y límites de crédito</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-3">
                        <label class="ui-label">Tipo de Cliente <span class="text-danger">*</span></label>
                        <select name="tipo_cliente" class="ui-select <?php $__errorArgs = ['tipo_cliente'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="consumo" <?php echo e(old('tipo_cliente', $cliente->tipo_cliente) === 'consumo' ? 'selected' : ''); ?>>Consumo</option>
                            <option value="credito_fiscal" <?php echo e(old('tipo_cliente', $cliente->tipo_cliente) === 'credito_fiscal' ? 'selected' : ''); ?>>Crédito Fiscal</option>
                            <option value="especial" <?php echo e(old('tipo_cliente', $cliente->tipo_cliente) === 'especial' ? 'selected' : ''); ?>>Especial</option>
                            <option value="gubernamental" <?php echo e(old('tipo_cliente', $cliente->tipo_cliente) === 'gubernamental' ? 'selected' : ''); ?>>Gubernamental</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Segmento</label>
                        <select name="segmento" class="ui-select <?php $__errorArgs = ['segmento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="micro" <?php echo e(old('segmento', $cliente->segmento) === 'micro' ? 'selected' : ''); ?>>Micro</option>
                            <option value="pequeno" <?php echo e(old('segmento', $cliente->segmento) === 'pequeno' ? 'selected' : ''); ?>>Pequeño</option>
                            <option value="mediano" <?php echo e(old('segmento', $cliente->segmento) === 'mediano' ? 'selected' : ''); ?>>Mediano</option>
                            <option value="grande" <?php echo e(old('segmento', $cliente->segmento) === 'grande' ? 'selected' : ''); ?>>Grande</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Límite de Crédito</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">RD$</span>
                            <input type="number" step="0.01" min="0" name="limite_credito" class="ui-input <?php $__errorArgs = ['limite_credito'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('limite_credito', $cliente->limite_credito)); ?>">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Días de Crédito</label>
                        <input type="number" min="0" max="365" name="dias_credito" class="ui-input <?php $__errorArgs = ['dias_credito'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('dias_credito', $cliente->dias_credito)); ?>" placeholder="30">
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Notas</label>
                        <textarea name="notas" rows="2" class="ui-textarea <?php $__errorArgs = ['notas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Información adicional..."><?php echo e(old('notas', $cliente->notas)); ?></textarea>
                    </div>
                    <div class="col-lg-3 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" id="check-activo" <?php echo e(old('activo', $cliente->activo) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="check-activo">Cliente activo</label>
                        </div>
                    </div>
                    <div class="col-lg-3 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="acceso_api" class="form-check-input" value="1" id="check-api" <?php echo e(old('acceso_api', $cliente->acceso_api) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="check-api">Acceso API</label>
                        </div>
                    </div>
                </div>

                <div id="api-password-section" class="mt-4" style="display: none;">
                    <div class="form-section-title">Cambiar Contraseña API</div>
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <label class="ui-label">Nueva Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="input-password" class="ui-input <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Mínimo 12 caracteres" minlength="12">
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Dejar vacío para mantener la actual (solo obligatorio si se activa Acceso API sin contraseña previa)</small>
                        </div>
                        <div class="col-lg-6">
                            <label class="ui-label">Confirmar Nueva Contraseña</label>
                            <input type="password" name="password_confirmation" id="input-password-confirm" class="ui-input" placeholder="Repite la contraseña" minlength="12">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="ui-sticky-bar">
        <div class="ui-sticky-bar-inner">
            <a href="<?php echo e(route('clientes.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="clienteForm" class="ui-btn ui-btn-solid rounded-pill px-5">
                <i class="bi bi-check-lg me-2"></i>Actualizar Cliente
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiCheckbox = document.getElementById('check-api');
    const passwordSection = document.getElementById('api-password-section');
    const passwordInput = document.getElementById('input-password');
    const passwordConfirmInput = document.getElementById('input-password-confirm');

    function togglePasswordSection() {
        if (apiCheckbox.checked) {
            passwordSection.style.display = 'block';
            if (!<?php echo e($cliente->password ? 'true' : 'false'); ?>) {
                passwordInput.required = true;
                passwordConfirmInput.required = true;
            } else {
                passwordInput.required = false;
                passwordConfirmInput.required = false;
            }
        } else {
            passwordSection.style.display = 'none';
            passwordInput.value = '';
            passwordConfirmInput.value = '';
            passwordInput.required = false;
            passwordConfirmInput.required = false;
        }
    }

    apiCheckbox.addEventListener('change', function() {
        if (this.checked && !<?php echo e($cliente->password ? 'true' : 'false'); ?>) {
            passwordInput.required = true;
            passwordConfirmInput.required = true;
        }
    });

    togglePasswordSection();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/clientes/edit.blade.php ENDPATH**/ ?>