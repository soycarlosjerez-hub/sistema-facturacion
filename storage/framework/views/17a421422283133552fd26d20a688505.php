<?php $__env->startSection('title', 'Configuración de Garantías'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.datatable-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
:root {
    --dt-accent: #22c55e;
    --dt-accent-gradient: linear-gradient(135deg, #22c55e, #16a34a);
    --dt-accent-rgb: 34,197,94;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#22c55e;--accent-rgb:34,197,94;--accent-hover:#16a34a;">
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
                    <h4 class="ui-header-title">Configuración de Garantías</h4>
                    <div class="ui-header-meta">Define los tipos y duraciones de garantía por producto</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('garantias-config.create')): ?>
                <a href="<?php echo e(route('garantias-config.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Configuración
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
            <form method="GET" action="<?php echo e(route('garantias-config.index')); ?>" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar configuración..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-3">
                    <select name="tipo_garantia" class="form-select">
                        <option value="">Todos los tipos</option>
                        <option value="fabrica" <?php echo e(request('tipo_garantia') == 'fabrica' ? 'selected' : ''); ?>>Garantía de Fábrica</option>
                        <option value="extendida" <?php echo e(request('tipo_garantia') == 'extendida' ? 'selected' : ''); ?>>Garantía Extendida</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="activo" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="1" <?php echo e(request('activo') == '1' ? 'selected' : ''); ?>>Activas</option>
                        <option value="0" <?php echo e(request('activo') == '0' ? 'selected' : ''); ?>>Inactivas</option>
                    </select>
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
                <table class="table table-hover dt-table" id="garantiasTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo Producto</th>
                            <th>Días</th>
                            <th>Tipo</th>
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
    var table = $('#garantiasTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?php echo e(route("garantias-config.ajax")); ?>',
            type: 'GET',
            data: function(d) {
                d.tipo_garantia = $('select[name="tipo_garantia"]').val();
                d.activo = $('select[name="activo"]').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, width: '50px' },
            { 
                data: 'nombre', 
                name: 'nombre',
                render: function(data) {
                    return '<strong>' + data + '</strong>';
                }
            },
            { 
                data: 'tipo_producto', 
                name: 'tipo_producto',
                render: function(data) {
                    return data || 'General';
                }
            },
            { 
                data: 'dias_garantia', 
                name: 'dias_garantia',
                render: function(data) {
                    return '<span class="badge bg-info">' + data + '</span>';
                }
            },
            { 
                data: 'tipo_garantia', 
                name: 'tipo_garantia',
                render: function(data, type, row) {
                    var cls = row.tipo_garantia == 'fabrica' ? 'primary' : 'warning text-dark';
                    return '<span class="badge ' + cls + '">' + data + '</span>';
                }
            },
            { 
                data: 'activo', 
                name: 'activo',
                render: function(data) {
                    var cls = data ? 'success' : 'secondary';
                    var label = data ? 'Activa' : 'Inactiva';
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/garantias-config/index.blade.php ENDPATH**/ ?>