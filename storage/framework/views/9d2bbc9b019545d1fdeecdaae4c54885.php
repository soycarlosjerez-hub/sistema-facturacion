<?php $__env->startSection('title', 'Marcas Tecnológicas'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.datatable-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
:root {
    --dt-accent: #3b82f6;
    --dt-accent-gradient: linear-gradient(135deg, #3b82f6, #2563eb);
    --dt-accent-rgb: 59,130,246;
}
.marca-logo {
    width: 40px;
    height: 40px;
    border-radius: 0.5rem;
    object-fit: cover;
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
                    <i class="bi bi-tag"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Marcas Tecnológicas</h4>
                    <div class="ui-header-meta">Administra las marcas de productos tecnológicos</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('marca-tecnologicas.create')): ?>
                <a href="<?php echo e(route('marcas-tecnologicas.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Marca
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('marcas-tecnologicas.index')); ?>" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar marca..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-3">
                    <select name="activo" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="1" <?php echo e(request('activo') == '1' ? 'selected' : ''); ?>>Activas</option>
                        <option value="0" <?php echo e(request('activo') == '0' ? 'selected' : ''); ?>>Inactivas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="pais" class="form-control" placeholder="País..." value="<?php echo e(request('pais')); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover dt-table" id="marcasTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Website</th>
                            <th>País</th>
                            <th>Productos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="mt-3 dt-table-footer"></div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    var table = $('#marcasTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?php echo e(route("marcas-tecnologicas.ajax")); ?>',
            type: 'GET',
            data: function(d) {
                d.activo = $('select[name="activo"]').val();
                d.pais = $('input[name="pais"]').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, width: '50px' },
            { data: 'nombre', name: 'nombre' },
            { data: 'website', name: 'website', orderable: false },
            { data: 'pais', name: 'pais' },
            { 
                data: 'productos_count', 
                name: 'productos_count',
                orderable: false,
                render: function(data) {
                    return '<span class="badge bg-info">' + (data || 0) + '</span>';
                }
            },
            { 
                data: 'activo', 
                name: 'activo',
                render: function(data) {
                    var cls = data ? 'success' : 'secondary';
                    var label = data ? 'Activo' : 'Inactivo';
                    return '<span class="badge bg-' + cls + '">' + label + '</span>';
                }
            },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, width: '140px' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true
    });

    // Filter on form submit
    $('form').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/marcas-tecnologicas/index.blade.php ENDPATH**/ ?>