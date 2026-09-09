<?php $__env->startSection('title', 'Servicios de Domótica'); ?>

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
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-hdd-network"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Servicios de Domótica</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-gear me-1"></i>
                        Instalaciones y proyectos de automatización
                        <span class="divider">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        <span><?php echo e($services->total()); ?> registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('domotica.create')): ?>
                <a href="<?php echo e(route('domotica.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Servicio
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="total-card">
                <div class="value"><?php echo e($services->total()); ?></div>
                <div class="label">Total Servicios</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="total-card">
                <div class="value text-warning"><?php echo e($services->whereIn('estado', ['programado', 'pendiente'])->count()); ?></div>
                <div class="label">Pendientes</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="total-card">
                <div class="value text-info"><?php echo e($services->where('estado', 'en_curso')->count()); ?></div>
                <div class="label">En Curso</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="total-card">
                <div class="value text-success"><?php echo e($services->where('estado', 'completado')->count()); ?></div>
                <div class="label">Completados</div>
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
                        <input type="text" name="search" class="ui-input" placeholder="Buscar proyecto, título, cliente..." value="<?php echo e(request('search')); ?>">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="estado" class="ui-select">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" <?php echo e(request('estado') == 'pendiente' ? 'selected' : ''); ?>>Pendiente</option>
                        <option value="programado" <?php echo e(request('estado') == 'programado' ? 'selected' : ''); ?>>Programado</option>
                        <option value="en_curso" <?php echo e(request('estado') == 'en_curso' ? 'selected' : ''); ?>>En Curso</option>
                        <option value="completado" <?php echo e(request('estado') == 'completado' ? 'selected' : ''); ?>>Completado</option>
                        <option value="cancelado" <?php echo e(request('estado') == 'cancelado' ? 'selected' : ''); ?>>Cancelado</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <select name="tipo_servicio" class="ui-select">
                        <option value="">Todos los tipos</option>
                        <?php $__currentLoopData = $tiposServicio; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(request('tipo_servicio') == $key ? 'selected' : ''); ?>><?php echo e($val); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-3">
                    <select name="cliente_id" class="ui-select">
                        <option value="">Todos los clientes</option>
                        <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cliente->id); ?>" <?php echo e(request('cliente_id') == $cliente->id ? 'selected' : ''); ?>><?php echo e($cliente->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="ui-btn ui-btn-secondary w-100">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover" id="domotica-table">
                    <thead>
                        <tr>
                            <th>Nº Proyecto</th>
                            <th>Título</th>
                            <th>Cliente</th>
                            <th>Tipo Servicio</th>
                            <th>Técnico</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>F. Programada</th>
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
    $('#domotica-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?php echo e(route("domotica.ajax")); ?>?' + window.location.search.substring(1),
        columns: [
            { data: 'numero_proyecto', name: 'numero_proyecto' },
            { data: 'titulo', name: 'titulo' },
            { data: 'cliente', name: 'cliente' },
            { data: 'tipo_servicio', name: 'tipo_servicio' },
            { data: 'tecnico', name: 'tecnico' },
            { data: 'total', name: 'total' },
            {
                data: 'estado',
                name: 'estado',
                render: function(data, type, row) {
                    const colors = {
                        pendiente: 'warning',
                        programado: 'info',
                        en_curso: 'primary',
                        completado: 'success',
                        cancelado: 'danger'
                    };
                    const cls = colors[data] || 'secondary';
                    return '<span class="badge bg-' + cls + '-subtle text-' + cls + ' status-badge">' + (row.estado_label || data) + '</span>';
                }
            },
            { data: 'fecha_programada', name: 'fecha_programada' },
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/domotica/index.blade.php ENDPATH**/ ?>