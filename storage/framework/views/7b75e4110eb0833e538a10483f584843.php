<?php $__env->startSection('title', 'Órdenes de Reparación'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.status-badge {
    padding: 0.4em 0.8em;
    border-radius: 2rem;
    font-weight: 500;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}
.total-card {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
}
.total-card .value { font-size: 1.5rem; font-weight: 700; }
.total-card .label { font-size: 0.75rem; opacity: 0.7; }
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
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Órdenes de Reparación</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-wrench-adjustable me-1"></i>
                        Gestiona las órdenes de reparación técnica
                        <span class="divider">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        <span><?php echo e($ordenes->total()); ?> registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tecnicas.create')): ?>
                <a href="<?php echo e(route('tecnicas.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Orden
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="total-card">
                <div class="value"><?php echo e($ordenes->total()); ?></div>
                <div class="label">Total Órdenes</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="total-card">
                <div class="value text-warning"><?php echo e($ordenes->where('estado', 'en_reparacion')->count()); ?></div>
                <div class="label">En Reparación</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="total-card">
                <div class="value text-info"><?php echo e($ordenes->where('estado', 'listo_para_entrega')->count() + $ordenes->where('estado', 'terminado')->count()); ?></div>
                <div class="label">Listas</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="total-card">
                <div class="value text-success"><?php echo e($ordenes->where('estado', 'entregado')->count()); ?></div>
                <div class="label">Entregadas</div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" id="search-form" class="row g-2 mb-3">
                <div class="col-lg-3">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="ui-input" placeholder="Buscar orden, cliente, RNC..." value="<?php echo e(request('search')); ?>">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="estado" class="ui-select">
                        <option value="">Todos los estados</option>
                        <?php $__currentLoopData = $estados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(request('estado') == $key ? 'selected' : ''); ?>><?php echo e($val); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <select name="tecnico_id" class="ui-select">
                        <option value="">Todos los técnicos</option>
                        <?php $__currentLoopData = $tecnicos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tech->id); ?>" <?php echo e(request('tecnico_id') == $tech->id ? 'selected' : ''); ?>><?php echo e($tech->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="desde" class="ui-input" value="<?php echo e(request('desde')); ?>">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="hasta" class="ui-input" value="<?php echo e(request('hasta')); ?>">
                </div>
                <div class="col-lg-1">
                    <button type="submit" class="ui-btn ui-btn-secondary w-100">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover" id="ordenes-table">
                    <thead>
                        <tr>
                            <th>Nº Orden</th>
                            <th>Cliente</th>
                            <th>Equipo</th>
                            <th>Tipo Servicio</th>
                            <th>Técnico</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>F. Recibo</th>
                            <th>F. Entrega Est.</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    $('#ordenes-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?php echo e(route("tecnicas.index")); ?>?' + window.location.search.substring(1),
        columns: [
            { data: 'numero_orden', name: 'numero_orden' },
            { data: 'cliente', name: 'cliente' },
            { data: 'equipo', name: 'equipo' },
            { data: 'tipo_servicio', name: 'tipo_servicio' },
            { data: 'tecnico', name: 'tecnico' },
            { data: 'total', name: 'total' },
            { data: 'estado_label', name: 'estado' },
            { data: 'fecha_recibo', name: 'fecha_recibo' },
            { data: 'fecha_entrega_estimada', name: 'fecha_entrega_estimada' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
        ],
        pageLength: 10,
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es_ES.json'
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/tecnicas/index.blade.php ENDPATH**/ ?>