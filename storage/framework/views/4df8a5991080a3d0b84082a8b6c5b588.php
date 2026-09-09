<?php $__env->startSection('title', 'Categorías y Subcategorías'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-folder-symlink"></i></div>
                <div>
                    <h4 class="ui-header-title">Categorías y Subcategorías</h4>
                    <div class="ui-header-meta"><i class="bi bi-tags me-1"></i><span>Organiza productos por categorías</span></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('category-subcategories.create')): ?>
                <a href="<?php echo e(route('category-subcategories.create')); ?>" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Subcategoría
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="ui-card">
        <div class="ui-card-body">
            <table class="table ui-table nowrap" id="cat-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Categoría Padre</th>
                        <th>Tipo Negocio</th>
                        <th>Hijas</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-none" id="edit-url"><?php echo e(route('category-subcategories.edit', '__ID__')); ?></div>
<div class="d-none" id="del-url"><?php echo e(route('category-subcategories.destroy', '__ID__')); ?></div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
$(document).ready(function() {
    $('#cat-table').DataTable({
        ajax: { url: '<?php echo e(route("category-subcategories.index")); ?>?json=1', dataSrc: 'data' },
        columns: [
            { data: 'nombre', render: function(d) { return '<div class="fw-bold"><i class="bi bi-folder me-1" style="color:#8b5cf6;"></i>' + d + '</div>'; }},
            { data: 'parent_name', render: function(d) { return d || '<span class="text-muted">—</span>'; }},
            { data: 'business_type', render: function(d) { return d ? '<span class="badge bg-info">' + d + '</span>' : '-'; }},
            { data: 'children_count', render: function(d) { return d > 0 ? '<span class="badge bg-secondary">' + d + '</span>' : '-'; }},
            { data: 'activa', render: function(d) { return d ? '<span class="ui-badge ui-badge-success"><i class="bi bi-check-lg"></i> Sí</span>' : '<span class="ui-badge ui-badge-danger"><i class="bi bi-x-lg"></i> No</span>'; }},
            { data: null, orderable: false, render: function(row) {
                return '<a href="' + $('#edit-url').text().replace('__ID__', row.id) + '" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>' +
                       '<button class="ui-action ui-action-delete" onclick="UI.confirm.delete(\'<?php echo e(route("category-subcategories.destroy", "__ID__")); ?>'.replace('__ID__', 'ROW_ID') + '\', \'' + row.nombre + '\')"><i class="bi bi-trash"></i></button>';
            }}
        ],
        dom: '<"row"<"col-12"tr>>',
        pageLength: 50,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES' }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/category-subcategories/index.blade.php ENDPATH**/ ?>