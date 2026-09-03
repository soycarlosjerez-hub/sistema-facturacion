<?php $__env->startSection('title', 'Historial de Ventas'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.ventas-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(59,130,246,.04);
    margin: 0;
}
.ventas-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.ventas-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.ventas-table tbody tr:last-child td { border-bottom: none; }
.ventas-table tbody tr { transition: background .15s; }
.ventas-table tbody tr:hover { background: rgba(59,130,246,.03); }
.status-badge {
    padding: 0.4em 0.8em;
    border-radius: 2rem;
    font-weight: 500;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}
body.dark-mode .ventas-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .ventas-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Gestión de Ventas</h4>
                    <div class="ui-header-meta">
                        Administración de ventas, facturación y cuentas por cobrar
                        <span class="mx-2">·</span>
                        <?php echo e($ventas->total()); ?> registro(s)
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ventas.create')): ?>
                <a href="<?php echo e(route('ventas.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Venta
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-center" id="filtros-form">
                <div class="col-lg-4">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="cliente" id="busqueda-cliente" class="ui-input" placeholder="Buscar por cliente o NCF..." value="<?php echo e(request('cliente')); ?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="desde" class="ui-input" value="<?php echo e(request('desde')); ?>" placeholder="Desde">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="hasta" class="ui-input" value="<?php echo e(request('hasta')); ?>" placeholder="Hasta">
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="<?php echo e(route('ventas.index')); ?>" class="ui-btn ui-btn-ghost"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-lg-2 text-end"></div>
            </form>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ventas.export')): ?>
            <div class="d-flex gap-2 mt-3 pt-3 border-top">
                <a href="<?php echo e(route('ventas.csv', request()->all())); ?>" class="ui-btn ui-btn-primary rounded-pill shadow-sm fw-medium">
                    <i class="bi bi-filetype-csv me-1"></i> CSV
                </a>
                <a href="<?php echo e(route('ventas.exportar', request()->all())); ?>" class="ui-btn ui-btn-primary rounded-pill shadow-sm fw-medium">
                    <i class="bi bi-file-excel me-1"></i> Excel
                </a>
                <a href="<?php echo e(route('ventas.pdf', request()->all())); ?>" class="ui-btn ui-btn-primary rounded-pill shadow-sm fw-medium">
                    <i class="bi bi-file-pdf me-1"></i> PDF
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table ventas-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Comprobante</th>
                            <th>Cliente</th>
                            <th>Fecha &amp; Hora</th>
                            <th>Tipo de Venta</th>
                            <th class="text-end">Montos</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="ventas-tbody">
                        <?php $__empty_1 = true; $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $esFiado = in_array($v->tipoVenta?->nombre, ['Fiado', 'Crédito']);
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark fs-6">#<?php echo e(str_pad($v->id, 5, '0', STR_PAD_LEFT)); ?></span>
                                        <?php if($v->ncf): ?>
                                            <span class="badge bg-light text-muted mt-1 border rounded-pill text-truncate" style="max-width:130px;">
                                                <i class="bi bi-receipt-cutoff me-1"></i><?php echo e($v->ncf); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small mt-1">&mdash;</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        $nombreCliente = $v->cliente->nombre ?? 'Consumidor Final';
                                        $firstLetter = strtoupper(substr($nombreCliente, 0, 1));
                                        $colors = ['#f87171', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa', '#f472b6'];
                                        $color = $v->cliente ? $colors[crc32($nombreCliente) % count($colors)] : '#9ca3af';
                                    ?>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle text-white me-3 shadow-sm" style="background-color: <?php echo e($color); ?>; width:40px;height:40px;font-size:1.1rem;">
                                            <?php echo e($firstLetter); ?>

                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6"><?php echo e($nombreCliente); ?></div>
                                            <div class="text-muted small"><i class="bi bi-person-badge me-1"></i>Cajero: <?php echo e($v->usuario->name ?? 'Sistema'); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><?php echo e($v->created_at->format('d/m/Y')); ?></div>
                                    <div class="text-muted small"><i class="bi bi-clock me-1"></i><?php echo e($v->created_at->format('h:i A')); ?></div>
                                </td>
                                <td>
                                    <span class="ui-badge-info">
                                        <?php if($esFiado): ?>
                                            <i class="bi bi-credit-card me-1"></i>
                                        <?php else: ?>
                                            <i class="bi bi-cash me-1"></i>
                                        <?php endif; ?>
                                        <?php echo e($v->tipoVenta->nombre ?? 'N/A'); ?>

                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="text-muted small mb-1">Sub: RD$ <?php echo e(number_format($v->total - $v->impuestos, 2)); ?></div>
                                    <div class="fw-bold text-primary fs-5">RD$ <?php echo e(number_format($v->total, 2)); ?></div>
                                </td>
                                <td class="text-center">
                                    <?php if($v->estado == 'completada'): ?>
                                        <span class="status-badge bg-success bg-opacity-10 text-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>Pagada
                                        </span>
                                    <?php elseif($v->estado == 'cuenta_abierta'): ?>
                                        <span class="status-badge bg-primary bg-opacity-10 text-primary">
                                            <i class="bi bi-door-open-fill me-1"></i>Cta. Abierta
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge bg-warning bg-opacity-10 text-warning">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i>Por Pagar
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="<?php echo e(route('ventas.show', $v->id)); ?>" class="ui-action ui-action-edit" title="Ver Detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('pagos.realizar', $v->id)); ?>" class="ui-action ui-action-edit" title="Cobrar / Abono">
                                            <i class="bi bi-cash-coin"></i>
                                        </a>
                                        <a href="<?php echo e(route('venta.pdf', $v->id)); ?>" class="ui-action ui-action-edit" title="Reimprimir">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        <form action="<?php echo e(route('ventas.destroy', $v->id)); ?>" method="POST" class="d-inline form-anular" data-venta-id="<?php echo e($v->id); ?>" data-venta-label="#<?php echo e(str_pad($v->id, 5, '0', STR_PAD_LEFT)); ?>">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <input type="hidden" name="motivo" class="motivo-input">
                                            <input type="hidden" name="confirmar" value="1">
                                            <button type="button" class="ui-action ui-action-delete btn-trigger-anular" title="Anular">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-receipt fs-1" style="color:#cbd5e1;"></i>
                                    <p class="mt-2 mb-0 fw-semibold">No hay ventas registradas</p>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ventas.create')): ?>
                                    <a href="<?php echo e(route('ventas.create')); ?>" class="btn btn-primary rounded-pill mt-2">Registrar primera venta</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if($ventas->hasPages()): ?>
    <div class="d-flex justify-content-center mt-3">
        <?php echo e($ventas->withQueryString()->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('busqueda-cliente');
    const tableBody = document.getElementById('ventas-tbody');
    let timeout = null;

    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            const query = this.value;
            const url = new URL(window.location.href);
            url.searchParams.set('cliente', query);

            if (tableBody) tableBody.style.opacity = '0.5';

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTbody = doc.getElementById('ventas-tbody');

                if (newTbody && tableBody) {
                    tableBody.innerHTML = newTbody.innerHTML;
                    tableBody.style.opacity = '1';
                }
            })
            .catch(() => {
                if (tableBody) tableBody.style.opacity = '1';
            });
        }, 400);
    });

    // Anular venta con motivo
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-trigger-anular');
        if (!btn) return;

        const form = btn.closest('.form-anular');
        if (!form) return;

        const ventaLabel = form.dataset.ventaLabel || '';
        const motivoInput = form.querySelector('.motivo-input');

        Swal.fire({
            title: 'Anular Venta',
            html: `
                <p class="text-muted mb-3">¿Anular la venta <strong>${ventaLabel}</strong>?</p>
                <textarea id="swal-motivo" class="form-control" rows="3" placeholder="Motivo de la anulación (mín. 5 caracteres)" required></textarea>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ef4444',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const motivo = document.getElementById('swal-motivo').value.trim();
                if (!motivo || motivo.length < 5) {
                    Swal.showValidationMessage('El motivo debe tener al menos 5 caracteres');
                    return false;
                }
                return motivo;
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(function(result) {
            if (result.isConfirmed) {
                motivoInput.value = result.value;
                form.submit();
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/ventas/index.blade.php ENDPATH**/ ?>