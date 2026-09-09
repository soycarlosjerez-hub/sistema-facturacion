<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Responder Encuesta — <?php echo e($encuesta->titulo); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; }
        .survey-header { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; padding: 2rem; border-radius: 1rem; margin-bottom: 2rem; }
        .pregunta-card { background: #fff; border-radius: .75rem; padding: 1.5rem; margin-bottom: 1rem; border: 1px solid #e2e8f0; }
        .pregunta-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08); }
        .star-rating { display: flex; gap: .25rem; }
        .star-rating input { display: none; }
        .star-rating label { font-size: 1.5rem; color: #cbd5e1; cursor: pointer; transition: color .2s; }
        .star-rating label:hover, .star-rating label:hover ~ label,
        .star-rating input:checked ~ label { color: #f59e0b; }
        .btn-submit { background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; color: #fff; padding: .75rem 2rem; border-radius: 999px; font-weight: 600; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(99,102,241,.4); color: #fff; }
    </style>
</head>
<body>
    <div class="container py-5" style="max-width:700px;">
        <div class="survey-header text-center">
            <h3 class="fw-bold mb-1"><?php echo e($encuesta->titulo); ?></h3>
            <?php if($encuesta->descripcion): ?>
            <p class="mb-0 opacity-75"><?php echo e($encuesta->descripcion); ?></p>
            <?php endif; ?>
            <?php if($encuesta->instrucciones): ?>
            <div class="mt-3 p-3 rounded" style="background:rgba(255,255,255,.15);">
                <small><?php echo e($encuesta->instrucciones); ?></small>
            </div>
            <?php endif; ?>
        </div>

        <form action="<?php echo e(route('sgc.satisfaccion.responder', $encuesta)); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <?php if(session('success')): ?>
            <div class="alert alert-success rounded-4 mb-4">
                <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

            </div>
            <?php endif; ?>

            <?php $__currentLoopData = $encuesta->preguntas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $pregunta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="pregunta-card">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <h6 class="fw-bold mb-0"><?php echo e($idx + 1); ?>. <?php echo e($pregunta->texto ?? $pregunta->enunciado ?? ''); ?></h6>
                    <?php if($pregunta->obligatoria): ?>
                    <span class="badge bg-danger bg-opacity-10 text-danger" style="font-size:.6rem;">*</span>
                    <?php endif; ?>
                </div>

                <?php if(($pregunta->tipo ?? '') === 'escala_5' || ($pregunta->tipo ?? '') === 'escala_10'): ?>
                    <div class="d-flex gap-1 flex-wrap">
                        <?php $max = ($pregunta->tipo === 'escala_10') ? 10 : 5; ?>
                        <?php for($i = 1; $i <= $max; $i++): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="respuestas[<?php echo e($pregunta->id); ?>]" value="<?php echo e($i); ?>" id="p<?php echo e($pregunta->id); ?>_<?php echo e($i); ?>" <?php echo e($pregunta->obligatoria ? 'required' : ''); ?>>
                            <label class="form-check-label" for="p<?php echo e($pregunta->id); ?>_<?php echo e($i); ?>"><?php echo e($i); ?></label>
                        </div>
                        <?php endfor; ?>
                    </div>
                <?php elseif(($pregunta->tipo ?? '') === 'si_no'): ?>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="respuestas[<?php echo e($pregunta->id); ?>]" value="si" id="p<?php echo e($pregunta->id); ?>_si" <?php echo e($pregunta->obligatoria ? 'required' : ''); ?>>
                            <label class="form-check-label" for="p<?php echo e($pregunta->id); ?>_si">Sí</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="respuestas[<?php echo e($pregunta->id); ?>]" value="no" id="p<?php echo e($pregunta->id); ?>_no">
                            <label class="form-check-label" for="p<?php echo e($pregunta->id); ?>_no">No</label>
                        </div>
                    </div>
                <?php elseif(($pregunta->tipo ?? '') === 'seleccion'): ?>
                    <select name="respuestas[<?php echo e($pregunta->id); ?>]" class="form-select" <?php echo e($pregunta->obligatoria ? 'required' : ''); ?>>
                        <option value="">Seleccionar...</option>
                        <?php $__currentLoopData = $pregunta->opciones ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opcion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($opcion); ?>"><?php echo e($opcion); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php else: ?>
                    <textarea name="respuestas[<?php echo e($pregunta->id); ?>]" class="form-control" rows="3" placeholder="Escribe tu respuesta..." <?php echo e($pregunta->obligatoria ? 'required' : ''); ?>></textarea>
                <?php endif; ?>

                <?php $__errorArgs = ['respuestas.' . $pregunta->id];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-submit">
                    <i class="bi bi-send me-2"></i>Enviar Respuestas
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted">Encuesta de Satisfacción — <?php echo e(config('app.name', 'SGC')); ?></small>
        </div>
    </div>
</body>
</html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/satisfaccion/encuestas/responder.blade.php ENDPATH**/ ?>