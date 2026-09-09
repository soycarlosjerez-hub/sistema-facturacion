<?php $__env->startSection('title', 'Listas de Precios'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .price-card {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.4);
        border-radius: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        position: relative;
        overflow: hidden;
    }
    .price-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        border-color: rgba(139,92,246,0.3);
    }
    .price-card::before {
        content: '';
        position: absolute; top: 0; left: 0;
        width: 4px; height: 100%;
        background: linear-gradient(to bottom, #8b5cf6, #a855f7);
        border-top-left-radius: 1.25rem; border-bottom-left-radius: 1.25rem;
        opacity: 0; transition: opacity 0.3s ease;
    }
    .price-card:hover::before { opacity: 1; }
    .price-card.status-expired::before {
        background: linear-gradient(to bottom, #dc3545, #e74c5a);
    }
    .price-card.status-warning::before {
        background: linear-gradient(to bottom, #ffc107, #ffca2c);
    }
    .price-card.status-notstarted::before {
        background: linear-gradient(to bottom, #0dcaf0, #3dd9f1);
    }
    .icon-wrapper {
        width: 48px; height: 48px;
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, rgba(139,92,246,0.1) 0%, rgba(168,85,247,0.1) 100%);
        border-radius: 0.75rem;
        color: #8b5cf6;
        font-size: 1.5rem;
    }
    .status-badge {
        padding: 0.4em 0.8em;
        border-radius: 2rem;
        font-weight: 500;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .btn-icon-hover {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%;
        transition: background-color 0.2s;
    }
    .btn-icon-hover:hover { background-color: rgba(0,0,0,0.05); }
    .alert-banner {
        border-radius: 1rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        animation: slideDown 0.4s ease-out;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .alert-banner-warning {
        background: linear-gradient(135deg, rgba(255,193,7,0.12), rgba(255,193,7,0.05));
        border-color: rgba(255,193,7,0.4) !important;
    }
    .alert-banner-danger {
        background: linear-gradient(135deg, rgba(220,53,69,0.12), rgba(220,53,69,0.05));
        border-color: rgba(220,53,69,0.4) !important;
    }

    body.dark-mode .price-card {
        background: rgba(15,23,42,.8);
        border-color: rgba(255,255,255,.08);
    }
    body.dark-mode .icon-wrapper {
        background: linear-gradient(135deg, rgba(139,92,246,0.15) 0%, rgba(168,85,247,0.15) 100%) !important;
    }
    body.dark-mode .status-badge.bg-light { background: rgba(30,41,59,.8) !important; }
    body.dark-mode .btn-icon-hover:hover { background-color: rgba(255,255,255,.1); }
    body.dark-mode .alert-banner-warning {
        background: linear-gradient(135deg, rgba(255,193,7,0.08), rgba(255,193,7,0.03));
        border-color: rgba(255,193,7,0.2) !important;
    }
    body.dark-mode .alert-banner-danger {
        background: linear-gradient(135deg, rgba(220,53,69,0.08), rgba(220,53,69,0.03));
        border-color: rgba(220,53,69,0.2) !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">

    <?php
        $porExpirar = $listas->filter(fn($l) => isset($l->status) && $l->status['class'] === 'warning');
        $expiradas = $listas->filter(fn($l) => isset($l->status) && $l->status['class'] === 'danger');
        $vigentes = $listas->count() - $porExpirar->count() - $expiradas->count();
    ?>

    <?php if($porExpirar->isNotEmpty()): ?>
    <div class="alert-banner alert-banner-warning alert alert-warning d-flex align-items-center gap-3 mb-4 p-3">
        <div class="flex-shrink-0">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
        </div>
        <div class="flex-grow-1">
            <strong>¡Atención!</strong> Hay <strong><?php echo e($porExpirar->count()); ?></strong> <?php echo e(Str::plural('lista de precios', $porExpirar->count())); ?> por expirar en los próximos 7 días:
            <span class="fw-semibold"><?php echo e($porExpirar->pluck('nombre')->join(', ')); ?></span>.
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if($expiradas->isNotEmpty()): ?>
    <div class="alert-banner alert-banner-danger alert alert-danger d-flex align-items-center gap-3 mb-4 p-3">
        <div class="flex-shrink-0">
            <i class="bi bi-x-octagon-fill fs-4"></i>
        </div>
        <div class="flex-grow-1">
            <strong>Lista(s) expirada(s):</strong> <?php echo e($expiradas->count()); ?> <?php echo e(Str::plural('lista', $expiradas->count())); ?> ha expirado<?php echo e($expiradas->count() > 1 ? 'n' : ''); ?>:
            <span class="fw-semibold"><?php echo e($expiradas->pluck('nombre')->join(', ')); ?></span>.
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tag"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Listas de Precios</h4>
                    <div class="ui-header-meta">
                        Gestiona diferentes tarifas para tus canales o clientes especiales
                        <span class="mx-2">·</span>
                        <?php echo e($listas->count()); ?> lista(s)
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('listas-precio.create')): ?>
                <a href="<?php echo e(route('listas-precio.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-2"></i> Nueva Lista
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="ui-stat" style="--delay:.05s">
                <div class="ui-card-accent"></div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(139,92,246,0.1);color:#8b5cf6;font-size:1.4rem;">
                            <i class="bi bi-tags"></i>
                        </div>
                        <div>
                            <div class="ui-stat-value" style="color:#8b5cf6;"><?php echo e($listas->count()); ?></div>
                            <div class="ui-stat-label">Total Listas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(25,135,84,0.1);color:#198754;font-size:1.4rem;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <div class="ui-stat-value text-success"><?php echo e($vigentes); ?></div>
                            <div class="ui-stat-label">Vigentes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(255,193,7,0.1);color:#ffc107;font-size:1.4rem;">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div>
                            <div class="ui-stat-value text-warning"><?php echo e($porExpirar->count()); ?></div>
                            <div class="ui-stat-label">Por Expirar</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(220,53,69,0.1);color:#dc3545;font-size:1.4rem;">
                            <i class="bi bi-x-octagon"></i>
                        </div>
                        <div>
                            <div class="ui-stat-value text-danger"><?php echo e($expiradas->count()); ?></div>
                            <div class="ui-stat-label">Expiradas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php $__empty_0 = true; $__currentLoopData = $listas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lista): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
        <?php
            $statusClass = $lista->status['class'] ?? 'success';
            $statusLabel = $lista->status['label'] ?? 'Vigente';
            $statusIcon = $lista->status['icon'] ?? 'bi-check-circle';
            $cardStatusClass = match($statusClass) {
                'danger' => 'status-expired',
                'warning' => 'status-warning',
                'info' => 'status-notstarted',
                default => '',
            };
        ?>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="price-card <?php echo e($cardStatusClass); ?> h-100 d-flex flex-column">
                <div class="p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-wrapper">
                            <i class="bi bi-tag-fill"></i>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <a href="<?php echo e(route('listas-precio.show', $lista)); ?>" class="btn btn-sm btn-outline-primary rounded-circle" style="width:32px;height:32px;border-color:rgba(139,92,246,0.2);color:#8b5cf6;" title="Ver detalles">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('listas-precio.edit')): ?>
                            <a href="<?php echo e(route('listas-precio.edit', $lista)); ?>" class="ui-action ui-action-edit" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('listas-precio.delete')): ?>
                            <button type="button" class="ui-action ui-action-delete" 
                                    onclick="UI.confirm.delete('<?php echo e(route('listas-precio.destroy', $lista)); ?>', '<?php echo e(addslashes($lista->nombre)); ?>')"
                                    title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                            <?php endif; ?>
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-sm btn-outline-secondary rounded-circle" style="width:32px;height:32px;" data-bs-toggle="dropdown" title="Más acciones">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('listas-precio.edit')): ?>
                                    <li><a class="dropdown-item py-2" href="<?php echo e(route('listas-precio.impacto', $lista)); ?>"><i class="bi bi-graph-up text-warning me-2"></i>Impacto de precios</a></li>
                                    <li><a class="dropdown-item py-2" href="<?php echo e(route('listas-precio.logs', $lista)); ?>"><i class="bi bi-clock-history text-secondary me-2"></i>Historial de cambios</a></li>
                                    <li>
                                        <form action="<?php echo e(route('listas-precio.duplicar', $lista)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button class="dropdown-item py-2"><i class="bi bi-copy text-info me-2"></i>Duplicar</button>
                                        </form>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="mb-auto">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h4 class="fw-bold text-dark mb-0"><?php echo e($lista->nombre); ?></h4>
                            <span class="status-badge bg-<?php echo e($statusClass); ?> bg-opacity-10 text-<?php echo e($statusClass); ?>">
                                <i class="bi <?php echo e($statusIcon); ?> me-1"></i><?php echo e($statusLabel); ?>

                            </span>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 fw-medium tracking-wider text-uppercase small">
                                <i class="bi bi-upc-scan me-1"></i> <?php echo e($lista->codigo); ?>

                            </span>
                        </div>
                        <?php if($lista->descripcion): ?>
                            <p class="text-muted small lh-sm"><?php echo e(Str::limit($lista->descripcion, 80)); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex justify-content-between align-items-center small text-muted mb-3">
                            <span class="d-flex align-items-center gap-2 bg-light px-3 py-1 rounded-pill">
                                <i class="bi bi-box-seam" style="color: #8b5cf6;"></i>
                                <span class="fw-bold text-dark"><?php echo e($lista->items_count); ?></span> prod.
                            </span>
                            <?php if($lista->vigencia_desde): ?>
                                <span class="d-flex align-items-center gap-1" title="Vigencia">
                                    <i class="bi bi-calendar3"></i>
                                    <?php echo e($lista->vigencia_desde->format('d/m/Y')); ?>

                                    <?php if($lista->vigencia_hasta): ?>
                                        - <?php echo e($lista->vigencia_hasta->format('d/m/Y')); ?>

                                    <?php endif; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <a href="<?php echo e(route('listas-precio.show', $lista)); ?>" class="btn btn-primary bg-opacity-10 text-primary border-0 w-100 rounded-pill fw-bold" style="transition: all 0.2s;color:#8b5cf6 !important;">
                            Gestionar Precios <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
        <div class="col-12">
            <div class="ui-empty-state">
                <i class="bi bi-tag"></i>
                <h4>No hay listas de precios</h4>
                <p>Las listas de precios te permiten tener tarifas especiales para clientes mayoristas, promociones temporales o diferentes sucursales.</p>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('listas-precio.create')): ?>
                <a href="<?php echo e(route('listas-precio.create')); ?>" class="ui-btn ui-btn-solid rounded-pill px-5">
                    <i class="bi bi-plus-lg me-2"></i> Crear Primera Lista
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function confirmDelete(url, nombre) {
    Swal.fire({
        title: '¿Eliminar lista?',
        text: `Se eliminará: "${nombre}"`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = '<?php echo csrf_field(); ?> <?php echo method_field("DELETE"); ?>';
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/listas-precio/index.blade.php ENDPATH**/ ?>