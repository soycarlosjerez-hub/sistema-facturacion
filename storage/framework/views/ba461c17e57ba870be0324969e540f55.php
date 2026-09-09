<?php $__env->startSection('title', 'Historial de Actividad'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .activity-icon {
        width: 40px; height: 40px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; flex-shrink: 0;
    }
    .activity-row {
        background: white;
        border-radius: 12px;
        padding: 12px 16px;
        border: 1px solid rgba(0,0,0,.06);
        transition: box-shadow .2s, transform .2s;
    }
    .activity-row:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,.08);
        transform: translateY(-1px);
    }
    .filter-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px; border-radius: 999px;
        font-size: 0.82rem; font-weight: 600;
        border: 1.5px solid #e2e8f0; color: #64748b;
        background: white; cursor: pointer;
        transition: all 0.2s; text-decoration: none;
    }
    .filter-chip:hover { border-color: #cbd5e1; background: #f8fafc; }
    .filter-chip.active {
        background: var(--accent-color, #8b5cf6);
        color: white; border-color: transparent;
    }
    .body.dark-mode .filter-chip {
        background: rgba(255,255,255,.06);
        border-color: rgba(255,255,255,.1);
        color: #94a3b8;
    }
    .body.dark-mode .filter-chip:hover {
        background: rgba(255,255,255,.1);
        color: #f1f5f9;
    }
    .body.dark-mode .filter-chip.active {
        background: var(--accent-color, #8b5cf6);
        color: white;
    }
    .time-stamp {
        font-size: 0.72rem; color: #94a3b8;
        white-space: nowrap;
    }
    .ip-badge {
        font-family: 'SF Mono', 'Consolas', monospace;
        font-size: 0.72rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php
    $actionConfig = [
        'login' => ['icon' => 'bi-box-arrow-in-right', 'color' => 'success', 'bg' => 'rgba(34,197,94,.1)', 'label' => 'Inicio Sesión'],
        'logout' => ['icon' => 'bi-box-arrow-right', 'color' => 'danger', 'bg' => 'rgba(239,68,68,.1)', 'label' => 'Cierre Sesión'],
        'page_view' => ['icon' => 'bi-eye', 'color' => 'primary', 'bg' => 'rgba(59,130,246,.1)', 'label' => 'Vista Página'],
    ];
?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    
    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Historial de Actividad</h2>
                    <p class="mb-0 opacity-75">Registro de actividad de usuarios en todas las instancias.</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('owner.online.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill">
                    <i class="bi bi-wifi me-2"></i>Usuarios Online
                </a>
                <a href="<?php echo e(route('owner.dashboard')); ?>" class="ui-btn ui-btn-primary rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Dashboard
                </a>
                <form method="POST" action="<?php echo e(route('owner.activity.history.clear')); ?>" id="clearHistoryForm" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="button" onclick="UI.confirm.submit('#clearHistoryForm', { title: '¿Limpiar historial?', text: 'Se eliminarán los registros con más de 30 días. Esta acción no se puede deshacer.', icon: 'warning', confirmText: 'Sí, limpiar' })" class="ui-btn ui-btn-danger rounded-pill">
                        <i class="bi bi-trash me-2"></i>Limpiar Historial
                    </button>
                </form>
            </div>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent" style="background:#22c55e"></div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="activity-icon" style="background:rgba(34,197,94,.1);color:#22c55e;">
                            <i class="bi bi-box-arrow-in-right"></i>
                        </div>
                        <div>
                            <small class="ui-stat-label d-block">Logins Hoy</small>
                            <h4 class="ui-stat-value mb-0 text-success"><?php echo e($todayStats['logins_today']); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#ef4444"></div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="activity-icon" style="background:rgba(239,68,68,.1);color:#ef4444;">
                            <i class="bi bi-box-arrow-right"></i>
                        </div>
                        <div>
                            <small class="ui-stat-label d-block">Logouts Hoy</small>
                            <h4 class="ui-stat-value mb-0 text-danger"><?php echo e($todayStats['logouts_today']); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#3b82f6"></div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="activity-icon" style="background:rgba(59,130,246,.1);color:#3b82f6;">
                            <i class="bi bi-eye"></i>
                        </div>
                        <div>
                            <small class="ui-stat-label d-block">Páginas Vistas</small>
                            <h4 class="ui-stat-value mb-0 text-primary"><?php echo e($todayStats['views_today']); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card" style="--delay:.25s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="activity-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6;">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <small class="ui-stat-label d-block">Usuarios Activos</small>
                            <h4 class="ui-stat-value mb-0" style="color:#8b5cf6;"><?php echo e($todayStats['unique_active']); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="ui-card mb-4" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-3">
            <form method="GET" class="d-flex align-items-center gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="<?php echo e(route('owner.activity.history')); ?>" class="filter-chip <?php echo e(!request('action') ? 'active' : ''); ?>">
                        <i class="bi bi-list-ul"></i> Todos
                    </a>
                    <?php $__currentLoopData = $actionConfig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $actionKey => $cfg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('owner.activity.history', ['action' => $actionKey] + request()->except('action'))); ?>" class="filter-chip <?php echo e(request('action') == $actionKey ? 'active' : ''); ?>" style="<?php echo e(request('action') == $actionKey ? 'background:' . ($actionKey === 'login' ? '#22c55e' : ($actionKey === 'logout' ? '#ef4444' : '#3b82f6')) : ''); ?>">
                            <i class="bi <?php echo e($cfg['icon']); ?>"></i> <?php echo e($cfg['label']); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="ui-input-group" style="max-width: 240px;">
                    <span class="ui-input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="ui-input border-0 bg-light" placeholder="Buscar usuario..." value="<?php echo e(request('search')); ?>">
                </div>
                <input type="date" name="start_date" class="form-control form-control-sm border-light bg-light" style="max-width:160px;" value="<?php echo e(request('start_date')); ?>" placeholder="Desde">
                <input type="date" name="end_date" class="form-control form-control-sm border-light bg-light" style="max-width:160px;" value="<?php echo e(request('end_date')); ?>" placeholder="Hasta">
                <?php if(request('search') || request('action') || request('start_date') || request('end_date')): ?>
                    <a href="<?php echo e(route('owner.activity.history')); ?>" class="btn btn-light btn-sm rounded-pill px-3">
                        <i class="bi bi-x-lg me-1"></i>Limpiar
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    
    <div class="ui-card" style="--delay:.35s">
        <div class="ui-card-accent"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: rgba(15,23,42,.03);">
                    <tr style="font-size: .7rem; text-transform: uppercase; letter-spacing: .05em;">
                        <th class="ps-4 py-3 text-muted fw-bold">Usuario</th>
                        <th class="py-3 text-muted fw-bold">Acción</th>
                        <th class="py-3 text-muted fw-bold">Instancia</th>
                        <th class="py-3 text-muted fw-bold">Sucursal</th>
                        <th class="py-3 text-muted fw-bold">IP / Hora</th>
                        <th class="text-end pe-4 py-3 text-muted fw-bold">Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_0 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <?php
                            $cfg = $actionConfig[$log->action] ?? ['icon' => 'bi-question-circle', 'color' => 'secondary', 'bg' => 'rgba(100,116,139,.1)', 'label' => $log->action];
                        ?>
                        <tr class="activity-row">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="activity-icon" style="background: <?php echo e($cfg['bg']); ?>; color: <?php echo e($cfg['color'] === 'success' ? '#22c55e' : ($cfg['color'] === 'danger' ? '#ef4444' : ($cfg['color'] === 'primary' ? '#3b82f6' : '#64748b'))); ?>;">
                                        <i class="bi <?php echo e($cfg['icon']); ?>"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark"><?php echo e($log->user->name); ?></div>
                                        <small class="text-muted" style="font-size: .7rem;"><?php echo e($log->user->email); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="badge rounded-pill px-3 py-2" style="background: <?php echo e($cfg['bg']); ?>; color: <?php echo e($cfg['color'] === 'success' ? '#22c55e' : ($cfg['color'] === 'danger' ? '#ef4444' : ($cfg['color'] === 'primary' ? '#3b82f6' : '#64748b'))); ?>;">
                                    <i class="bi <?php echo e($cfg['icon']); ?> me-1"></i><?php echo e($cfg['label']); ?>

                                </span>
                            </td>
                            <td class="py-3">
                                <small class="text-muted"><?php echo e($log->user->businessInstance?->nombre ?? 'N/A'); ?></small>
                            </td>
                            <td class="py-3">
                                <small class="text-muted"><?php echo e($log->user->sucursal?->nombre ?? 'N/A'); ?></small>
                            </td>
                            <td class="py-3">
                                <div class="ip-badge text-muted"><?php echo e($log->ip_address ?? '—'); ?></div>
                                <div class="time-stamp"><?php echo e($log->logged_at?->format('d M Y, H:i')); ?></div>
                                <small class="text-muted" style="font-size: .68rem;"><?php echo e($log->logged_at?->diffForHumans()); ?></small>
                            </td>
                            <td class="text-end pe-4 py-3">
                                <?php if($log->user_agent): ?>
                                    <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-toggle="collapse" data-bs-target="#ua-<?php echo e($log->id); ?>" title="User Agent">
                                        <i class="bi bi-terminal me-1"></i><small>UA</small>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if($log->user_agent): ?>
                        <tr>
                            <td colspan="6" class="ps-5 py-2">
                                <div class="collapse" id="ua-<?php echo e($log->id); ?>">
                                    <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <code style="font-size: .72rem; word-break: break-all;"><?php echo e($log->user_agent); ?></code>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="bi bi-clock-history display-4"></i>
                                </div>
                                <h5 class="fw-bold mb-2">No hay registros</h5>
                                <p class="text-muted mb-0">No se encontraron registros con los filtros aplicados.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($logs->hasPages()): ?>
            <div class="card-footer bg-transparent border-0 py-3">
                <?php echo e($logs->withQueryString()->links()); ?>

            </div>
        <?php endif; ?>
    </div>

</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/activity-history.blade.php ENDPATH**/ ?>