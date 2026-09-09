<?php $__env->startSection('title', 'Cuentas por Cobrar'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.cuenta-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 1.05rem;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,.12);
    transition: transform .2s ease;
}
.ui-card:hover .cuenta-avatar { transform: scale(1.08); }
.cuenta-cliente-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: .75rem;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}
.cuenta-total-pill {
    background: rgba(255,255,255,.2);
    backdrop-filter: blur(8px);
    border: 1.5px solid rgba(255,255,255,.28);
    border-radius: 9999px;
    padding: .45rem 1.1rem;
}
body.dark-mode .cuenta-cliente-head { border-bottom-color: #1e293b; }
body.dark-mode .cuenta-avatar { color: #f1f5f9; }
body.dark-mode .cuenta-total-pill { background: rgba(255,255,255,.06); border-color: rgba(255,255,255,.12); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $totalPorCobrar = $clientes->sum('balance_pendiente');
    $clientesConDeuda = $clientes->total();
    $ventasPendientes = $clientes->sum(fn($c) => $c->ventas->count());
    $montoPendienteVentas = $clientes->sum(fn($c) => $c->ventas->sum(fn($v) => $v->total - $v->montoPagado()));
?>
<div class="ui-page" style="--accent:#dc2626;--accent-rgb:220,38,38;--accent-hover:#b91c1c;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Cuentas por Cobrar</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Auditando deudas, fiados y cuentas abiertas
                        <span class="divider">·</span>
                        <i class="bi bi-people me-1"></i>
                        <?php echo e($clientesConDeuda); ?> cliente(s) con deuda
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <div class="cuenta-total-pill">
                    <span class="small fw-bold text-uppercase me-2 opacity-75">Total por Cobrar:</span>
                    <span class="fs-6 fw-bold">RD$<?php echo e(number_format($totalPorCobrar, 2)); ?></span>
                </div>
                <a href="<?php echo e(route('clientes.creditos.resumen')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-credit-card me-1"></i> Créditos
                </a>
                <a href="<?php echo e(route('clientes.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-people me-1"></i> Clientes
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-cash-stack me-1"></i>Total por Cobrar</div>
                    <div class="ui-stat-value" style="color:#dc2626;">RD$<?php echo e(number_format($totalPorCobrar, 2)); ?></div>
                    <div class="ui-stat-sub">Saldo pendiente de clientes</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-people me-1"></i>Clientes con Deuda</div>
                    <div class="ui-stat-value" style="color:#d97706;"><?php echo e($clientesConDeuda); ?></div>
                    <div class="ui-stat-sub">Con balance pendiente mayor a cero</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-receipt me-1"></i>Ventas Pendientes</div>
                    <div class="ui-stat-value" style="color:#2563eb;"><?php echo e($ventasPendientes); ?></div>
                    <div class="ui-stat-sub">Fiados y cuentas abiertas</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.25s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-hourglass-split me-1"></i>Monto Pendiente</div>
                    <div class="ui-stat-value" style="color:#16a34a;">RD$<?php echo e(number_format($montoPendienteVentas, 2)); ?></div>
                    <div class="ui-stat-sub">Suma de saldos por venta</div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-lg-10">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="ui-input" placeholder="Buscar cliente con deuda..." value="<?php echo e(request('buscar')); ?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="ui-btn ui-btn-solid w-100"><i class="bi bi-funnel me-1"></i>Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <?php $__empty_0 = true; $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
    <?php
        $nombre = $c->nombre ?? 'C';
        $colors = ['#dc2626', '#f97316', '#d97706', '#2563eb', '#7c3aed', '#0891b2'];
        $color = $colors[crc32($nombre) % count($colors)];
        $inicial = strtoupper(substr($nombre, 0, 1));
    ?>
    <div class="ui-card mb-3" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="cuenta-cliente-head">
                <div class="d-flex align-items-center">
                    <div class="cuenta-avatar me-3" style="background:<?php echo e($color); ?>;">
                        <?php echo e($inicial); ?>

                    </div>
                    <div>
                        <h6 class="fw-bold mb-0"><?php echo e($c->nombre); ?></h6>
                        <small class="text-muted"><i class="bi bi-telephone me-1"></i><?php echo e($c->telefono ?? 'Sin teléfono'); ?></small>
                    </div>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block small fw-bold text-uppercase">Balance Pendiente</small>
                    <span class="fs-5 fw-bold text-danger">RD$<?php echo e(number_format($c->balance_pendiente, 2)); ?></span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="ui-table table-sm">
                    <thead>
                        <tr>
                            <th class="ps-4">Venta #</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="text-end">Monto Total</th>
                            <th class="text-end">Pagado</th>
                            <th class="text-end">Pendiente</th>
                            <th class="text-center pe-4" width="150">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $c->ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $pagado = $v->montoPagado(); $pendiente = $v->total - $pagado; $vencida = $pendiente > 0 && $v->created_at->lt(now()->subDays(30)); ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary">#<?php echo e(str_pad($v->id, 5, '0', STR_PAD_LEFT)); ?></td>
                            <td class="small"><?php echo e($v->created_at->format('d/m/Y h:i A')); ?></td>
                            <td>
                                <?php if($pendiente <= 0): ?>
                                    <span class="ui-badge ui-badge-success"><i class="bi bi-check-circle me-1"></i>PAGADA</span>
                                <?php elseif($v->estado == 'pendiente' && $vencida): ?>
                                    <span class="ui-badge ui-badge-danger"><i class="bi bi-clock-history me-1"></i>VENCIDA</span>
                                <?php elseif($v->estado == 'pendiente'): ?>
                                    <span class="ui-badge ui-badge-warning"><i class="bi bi-clock me-1"></i>FIAO</span>
                                <?php else: ?>
                                    <span class="ui-badge ui-badge-info"><i class="bi bi-credit-card-2-front me-1"></i>CTA. ABIERTA</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold small">RD$<?php echo e(number_format($v->total, 2)); ?></td>
                            <td class="text-end text-success small">RD$<?php echo e(number_format($pagado, 2)); ?></td>
                            <td class="text-end text-danger fw-bold small">RD$<?php echo e(number_format($pendiente, 2)); ?></td>
                            <td class="text-center pe-4">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cobros.create')): ?>
                                <a href="<?php echo e(route('pagos.realizar', $v->id)); ?>" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                                    <i class="bi bi-cash me-1"></i> Cobrar
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <div class="ui-empty-state">
                <i class="bi bi-cash-coin"></i>
                <p>Sin cuentas por cobrar</p>
                <small class="text-muted">No hay clientes con balance pendiente en este momento.</small>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if($clientes->hasPages()): ?>
    <div class="d-flex justify-content-center mt-3">
        <?php echo e($clientes->withQueryString()->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/clientes/cuentas.blade.php ENDPATH**/ ?>