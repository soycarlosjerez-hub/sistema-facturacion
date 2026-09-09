<?php $__env->startSection('title', 'Perfil: ' . $user->name); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .premium-header-green {
        background: linear-gradient(135deg, #22c55e, #16a34a, #22c55e, #15803d);
        background-size: 300% 300%;
        animation: premiumGradientShift 6s ease infinite;
        border-radius: 1.2rem;
        padding: 2.5rem 2.5rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(34,197,94,.25);
    }
    .premium-header-green::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background:
            radial-gradient(circle at 30% 40%, rgba(255,255,255,.1) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.07) 0%, transparent 50%);
        pointer-events: none;
    }
    .premium-header-green .bubble {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
        pointer-events: none;
    }
    .premium-header-green .bubble:nth-child(1) { width: 80px; height: 80px; top: -20px; right: 10%; animation: premiumFloat 4s ease-in-out infinite; }
    .premium-header-green .bubble:nth-child(2) { width: 50px; height: 50px; bottom: 10px; right: 28%; animation: premiumFloat 5s ease-in-out infinite 1s; }
    .premium-header-green .bubble:nth-child(3) { width: 100px; height: 100px; bottom: -30px; right: 5%; animation: premiumFloat 6s ease-in-out infinite .5s; }

    .user-avatar-lg {
        width: 96px; height: 96px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        color: white; font-size: 2.5rem; font-weight: 800; flex-shrink: 0;
        box-shadow: 0 8px 24px rgba(0,0,0,.15);
    }

    .info-card { background: rgba(255,255,255,.9); border: 1px solid rgba(0,0,0,.06); border-radius: 16px; backdrop-filter: blur(12px); }
    body.dark-mode .info-card { background: rgba(30,41,59,.9); border-color: rgba(255,255,255,.08); }

    .info-label { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
    body.dark-mode .info-label { color: #94a3b8; }
    .info-value { font-size: 1rem; font-weight: 600; color: #1e293b; }
    body.dark-mode .info-value { color: #f1f5f9; }

    .instance-chip {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px;
        border-radius: 999px; font-size: 0.8rem; font-weight: 600; background: rgba(34,197,94,.1);
        color: #16a34a; border: 1px solid rgba(34,197,94,.2);
    }
    body.dark-mode .instance-chip { background: rgba(34,197,94,.15); color: #4ade80; border-color: rgba(34,197,94,.3); }

    .online-dot {
        width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 4px;
    }
    .online-dot.online { background: #22c55e; box-shadow: 0 0 8px rgba(34,197,94,.5); }
    .online-dot.offline { background: #94a3b8; }

    .premium-sticky-bar {
        position: fixed; bottom: 0; left: 0; right: 0; z-index: 1000;
        background: rgba(255,255,255,.95); backdrop-filter: blur(12px);
        border-top: 1px solid rgba(0,0,0,.08); padding: 12px 24px;
    }
    body.dark-mode .premium-sticky-bar { background: rgba(15,23,42,.95); border-color: rgba(255,255,255,.1); }

    .btn-save { background: linear-gradient(135deg, #22c55e, #16a34a); color: white; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 700; transition: all .2s; }
    .btn-save:hover { background: linear-gradient(135deg, #16a34a, #15803d); color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(34,197,94,.3); }
    .btn-cancel { background: rgba(255,255,255,.15); color: #64748b; border: 1.5px solid rgba(0,0,0,.08); padding: 10px 20px; border-radius: 10px; font-weight: 600; transition: all .2s; text-decoration: none; }
    .btn-cancel:hover { background: rgba(255,255,255,.2); color: #1e293b; text-decoration: none; }
    body.dark-mode .btn-cancel { background: rgba(255,255,255,.08); color: #94a3b8; border-color: rgba(255,255,255,.12); }
    body.dark-mode .btn-cancel:hover { background: rgba(255,255,255,.12); color: #f1f5f9; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#22c55e;--accent-rgb:34,197,94;--accent-hover:#16a34a;">

    <div class="premium-header-green mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative; z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="user-avatar-lg" style="background: rgba(255,255,255,.2); backdrop-filter: blur(8px); border: 2px solid rgba(255,255,255,.35);">
                    <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-building me-1"></i><?php echo e($instance->nombre ?? 'Mi Instancia'); ?>

                    </span>
                    <h4 class="fw-bold mb-1 text-white">Perfil del Usuario</h4>
                    <small class="text-white opacity-75"><?php echo e($user->email); ?></small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('instance.users.edit', [$instance->id, $user->id])); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
                <a href="<?php echo e(route('instance.users.index', $instance->id)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="info-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-person-fill me-2"></i>Información Personal</h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-label">Nombre Completo</div>
                            <div class="info-value"><?php echo e($user->name); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Correo Electrónico</div>
                            <div class="info-value"><?php echo e($user->email); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Rol de Instancia</div>
                            <div class="info-value">
                                <?php if($user->instanceRole): ?>
                                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(34,197,94,.1); color: #16a34a;">
                                        <i class="bi bi-shield-check me-1"></i><?php echo e(ucfirst($user->instanceRole->name)); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                        <i class="bi bi-person-x me-1"></i>Sin asignar
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Sucursal</div>
                            <div class="info-value">
                                <?php if($user->sucursal): ?>
                                    <i class="bi bi-building me-1"></i><?php echo e($user->sucursal->nombre); ?>

                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Estado</div>
                            <div class="info-value">
                                <?php if($user->isOnline()): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                        <i class="online-dot online"></i>Online
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">
                                        <i class="online-dot offline"></i>Offline
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Última Conexión</div>
                            <div class="info-value">
                                <?php if($user->last_seen_at): ?>
                                    <?php echo e($user->last_seen_at->diffForHumans()); ?>

                                <?php else: ?>
                                    <span class="text-muted">Sin registros</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="info-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Detalles</h5>

                    <div class="mb-3">
                        <div class="info-label">Fecha de Creación</div>
                        <div class="info-value small"><?php echo e($user->created_at->format('d M Y')); ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Última Actualización</div>
                        <div class="info-value small"><?php echo e($user->updated_at->format('d M Y')); ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Tipo de Usuario</div>
                        <div class="info-value small">
                            <?php if($user->role === 'admin-business'): ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                                    <i class="bi bi-shield-lock me-1"></i>Administrador de Instancia
                                </span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark rounded-pill px-3 py-2"><?php echo e(ucfirst($user->role)); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Instancia</div>
                        <div class="info-value small">
                            <span class="instance-chip">
                                <i class="bi bi-building"></i> <?php echo e($instance->nombre); ?>

                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-sticky-bar">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-fill text-success"></i>
                <span class="fw-semibold d-none d-sm-inline"><?php echo e($user->name); ?></span>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('instance.users.index', $instance->id)); ?>" class="btn-cancel">Cerrar</a>
                <a href="<?php echo e(route('instance.users.edit', [$instance->id, $user->id])); ?>" class="btn-save"><i class="bi bi-pencil me-2"></i>Editar Usuario</a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/instance/users/show.blade.php ENDPATH**/ ?>