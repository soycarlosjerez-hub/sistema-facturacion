<?php $__env->startSection('title', 'Kardex de Inventario'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .premium-header {
        background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(100, 116, 139, 0.4);
        position: relative;
        overflow: hidden;
    }
    .premium-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .filter-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    .avatar-circle {
        width: 44px; height: 44px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 1.2rem;
        transition: transform 0.2s;
    }
    tr:hover .avatar-circle { transform: scale(1.1); }
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
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 py-3">

    <div class="premium-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1 d-flex align-items-center">
                <i class="bi bi-journal-text me-3 fs-1 opacity-75"></i> Kardex de Inventario
            </h2>
            <p class="mb-0 opacity-75 fs-5">Rastrea y audita cada movimiento de tus productos en el almacén</p>
        </div>
    </div>

    <div class="filter-card p-4 mb-4">
        <form method="GET" id="search-form" class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label class="form-label text-muted small fw-bold text-uppercase tracking-wider">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" id="busqueda-kardex" class="form-control border-start-0 ps-0" placeholder="Concepto o nota..." value="<?php echo e(request('buscar')); ?>" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-3">
                <label class="form-label text-muted small fw-bold text-uppercase tracking-wider">Producto</label>
                <select name="producto_id" class="form-select">
                    <option value="">Todos los productos</option>
                    <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>" <?php echo e(request('producto_id') == $p->id ? 'selected' : ''); ?>><?php echo e($p->nombre); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-lg-3">
                <label class="form-label text-muted small fw-bold text-uppercase tracking-wider">Almacén</label>
                <select name="almacen_id" class="form-select">
                    <option value="">Todos los almacenes</option>
                    <?php $__currentLoopData = $almacenes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($a->id); ?>" <?php echo e(request('almacen_id') == $a->id ? 'selected' : ''); ?>><?php echo e($a->nombre); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill flex-grow-1"><i class="bi bi-funnel me-2"></i>Filtrar</button>
                <a href="<?php echo e(route('kardex.index')); ?>" class="btn btn-outline-secondary rounded-circle" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('kardex.export')): ?>
        <div class="d-flex gap-2 mt-3 pt-3 border-top">
            <a href="<?php echo e(route('kardex.exportar', request()->all())); ?>" class="btn btn-light rounded-pill shadow-sm fw-medium">
                <i class="bi bi-file-excel me-1"></i> Excel
            </a>
            <a href="<?php echo e(route('kardex.pdf', request()->all())); ?>" class="btn btn-light rounded-pill shadow-sm fw-medium">
                <i class="bi bi-file-pdf me-1"></i> PDF
            </a>
        </div>
        <?php endif; ?>
    </div>

    <div id="kardex-container" class="table-responsive" style="min-height:400px;">
        <table class="table table-hover align-middle mb-0 w-100">
            <thead class="text-muted small text-uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">
                <tr>
                    <th class="ps-4 pb-3">Fecha y Hora</th>
                    <th class="pb-3">Producto</th>
                    <th class="text-center pb-3">Almacén</th>
                    <th class="text-center pb-3">Tipo</th>
                    <th class="text-center pb-3">Cantidad</th>
                    <th class="pb-3">Concepto / Nota</th>
                    <th class="text-end pe-4 pb-3">Usuario</th>
                </tr>
            </thead>
            <tbody id="kardex-tbody">
                <?php $__empty_0 = true; $__currentLoopData = $movimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="text-primary bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:35px;height:35px;">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark fs-6"><?php echo e($m->created_at->format('d/m/Y')); ?></div>
                                <div class="text-muted small"><?php echo e($m->created_at->format('h:i A')); ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="max-width:200px;">
                        <div class="fw-bold text-dark fs-6 text-truncate" title="<?php echo e(optional($m->producto)->nombre ?? "Producto Eliminado"); ?>"><?php echo e(optional($m->producto)->nombre ?? "Producto Eliminado"); ?></div>
                        <div class="text-muted small"><i class="bi bi-upc-scan me-1"></i> ID: <?php echo e($m->producto_id ?? $m->producto->id); ?></div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-secondary border rounded-pill">
                            <i class="bi bi-building me-1"></i> <?php echo e(optional($m->almacen)->nombre ?? "Almacén Eliminado"); ?>

                        </span>
                    </td>
                    <td class="text-center">
                        <?php if(strtolower($m->tipo) == 'entrada'): ?>
                            <span class="status-badge bg-success bg-opacity-10 text-success">
                                <i class="bi bi-arrow-down-left me-1"></i> Entrada
                            </span>
                        <?php else: ?>
                            <span class="status-badge bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-arrow-up-right me-1"></i> Salida
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="fw-bold fs-5 <?php echo e(strtolower($m->tipo) == 'entrada' ? 'text-success' : 'text-danger'); ?>">
                            <?php echo e(strtolower($m->tipo) == 'entrada' ? '+' : '-'); ?> <?php echo e($m->cantidad); ?>

                        </div>
                    </td>
                    <td style="max-width:250px;">
                        <div class="text-muted small text-truncate" title="<?php echo e($m->nota ?? $m->motivo ?? 'Movimiento de inventario'); ?>">
                            <i class="bi bi-journal-text me-1"></i>
                            <?php echo e($m->nota ?? $m->motivo ?? 'Movimiento de inventario'); ?>

                        </div>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <div class="text-end">
                                <div class="fw-medium text-dark"><?php echo e($m->user->name ?? 'Sistema'); ?></div>
                            </div>
                            <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center p-5">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-4" style="width:100px;height:100px;">
                                <i class="bi bi-boxes text-muted opacity-50" style="font-size:3rem;"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">No hay movimientos registrados</h4>
                            <p class="text-muted mb-4 text-center" style="max-width:400px;">Aún no se han registrado entradas o salidas de inventario, o los filtros actuales no arrojaron resultados.</p>
                            <a href="<?php echo e(route('kardex.index')); ?>" class="btn btn-light rounded-pill px-4 py-2 shadow-sm">
                                <i class="bi bi-arrow-counterclockwise me-2"></i> Limpiar Filtros
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($movimientos->hasPages()): ?>
    <div class="mt-4 d-flex justify-content-center" id="pagination-container">
        <?php echo e($movimientos->withQueryString()->links()); ?>

    </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filters = document.querySelectorAll('select, input[name="buscar"]');
        const tableBody = document.getElementById('kardex-tbody');
        const pagination = document.getElementById('pagination-container');
        let timeout = null;

        filters.forEach(filter => {
            const eventType = filter.tagName === 'SELECT' ? 'change' : 'input';

            filter.addEventListener(eventType, function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    const formData = new FormData(document.getElementById('search-form'));
                    const params = new URLSearchParams(formData).toString();
                    const url = `<?php echo e(route('kardex.index')); ?>?${params}`;

                    if (tableBody) tableBody.style.opacity = '0.5';

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTbody = doc.getElementById('kardex-tbody');
                        const newPag = doc.getElementById('pagination-container');

                        if (newTbody && tableBody) {
                            tableBody.innerHTML = newTbody.innerHTML;
                            tableBody.style.opacity = '1';
                        }
                        if (newPag && pagination) {
                            pagination.innerHTML = newPag.innerHTML;
                        } else if (pagination) {
                            pagination.innerHTML = '';
                        }
                    })
                    .catch(() => {
                        if (tableBody) tableBody.style.opacity = '1';
                    });
                }, 400);
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/kardex/index.blade.php ENDPATH**/ ?>