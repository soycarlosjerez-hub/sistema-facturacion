<?php $__env->startSection('title', 'Garantías'); ?>

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
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Garantías</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-shield-shaded me-1"></i>
                        Gestión de garantías de equipos y servicios
                        <span class="divider">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        <span><?php echo e($garantias->total()); ?> registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('garantias.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Garantía
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="total-card">
                <div class="value"><?php echo e($garantias->total()); ?></div>
                <div class="label">Total Garantías</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="total-card">
                <div class="value text-success"><?php echo e($garantias->where('estado', 'vigente')->count()); ?></div>
                <div class="label">Vigentes</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="total-card">
                <div class="value text-warning"><?php echo e($garantias->where('estado', 'reclamada')->count()); ?></div>
                <div class="label">En Reclamo</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="total-card">
                <div class="value text-danger"><?php echo e($garantias->where('estado', 'expirada')->count()); ?></div>
                <div class="label">Expiradas</div>
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
                        <input type="text" name="search" class="ui-input" placeholder="Buscar serial, modelo, cobertura, orden..." value="<?php echo e(request('search')); ?>">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="tipo" class="ui-select">
                        <option value="">Todos los tipos</option>
                        <?php $__currentLoopData = $tiposGarantia; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(request('tipo') == $key ? 'selected' : ''); ?>><?php echo e($val); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <select name="estado" class="ui-select">
                        <option value="">Todos los estados</option>
                        <option value="vigente" <?php echo e(request('estado') == 'vigente' ? 'selected' : ''); ?>>Vigente</option>
                        <option value="expirada" <?php echo e(request('estado') == 'expirada' ? 'selected' : ''); ?>>Expirada</option>
                        <option value="reclamada" <?php echo e(request('estado') == 'reclamada' ? 'selected' : ''); ?>>En Reclamo</option>
                        <option value="cancelada" <?php echo e(request('estado') == 'cancelada' ? 'selected' : ''); ?>>Cancelada</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <select name="vigencia" class="ui-select">
                        <option value="">Todas</option>
                        <option value="vigentes" <?php echo e(request('vigencia') == 'vigentes' ? 'selected' : ''); ?>>Vigentes</option>
                        <option value="por_vencer" <?php echo e(request('vigencia') == 'por_vencer' ? 'selected' : ''); ?>>Por vencer (30 días)</option>
                        <option value="expiradas" <?php echo e(request('vigencia') == 'expiradas' ? 'selected' : ''); ?>>Expiradas</option>
                    </select>
                </div>
                <div class="col-lg-1">
                    <button type="submit" class="ui-btn ui-btn-secondary w-100">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover" id="garantias-table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Equipo</th>
                            <th>Modelo</th>
                            <th>Cobertura</th>
                            <th>Inicio</th>
                            <th>Vence</th>
                            <th>Días</th>
                            <th>Estado</th>
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
    $('#garantias-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?php echo e(route("garantias.ajax")); ?>?' + window.location.search.substring(1),
        columns: [
            { data: 'tipo', name: 'tipo' },
            { data: 'equipo_serial', name: 'equipo_serial' },
            { data: 'equipo_modelo', name: 'equipo_modelo' },
            { data: 'cobertura', name: 'cobertura' },
            { data: 'fecha_inicio', name: 'fecha_inicio' },
            { data: 'fecha_fin', name: 'fecha_fin' },
            {
                data: 'dias_restantes',
                name: 'dias_restantes',
                render: function(data, type, row) {
                    if (row.estado !== 'vigente') return '<span class="text-muted">-</span>';
                    const color = data <= 7 ? 'danger' : (data <= 30 ? 'warning' : 'success');
                    return '<span class="text-' + color + ' fw-semibold">' + data + ' d</span>';
                }
            },
            {
                data: 'estado',
                name: 'estado',
                render: function(data, type, row) {
                    return '<span class="badge bg-' + row.badge_color + '-subtle text-' + row.badge_color + ' status-badge">' + (row.estado_label || data) + '</span>';
                }
            },
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/garantias/index.blade.php ENDPATH**/ ?>