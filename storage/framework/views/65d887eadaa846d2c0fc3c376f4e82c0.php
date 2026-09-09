<?php $__env->startSection('title', 'Redes de Configuración'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.datatable-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
:root {
    --dt-accent: #06b6d4;
    --dt-accent-gradient: linear-gradient(135deg, #06b6d4, #0891b2);
    --dt-accent-rgb: 6,182,212;
}
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
                    <h4 class="ui-header-title">Redes de Configuración</h4>
                    <div class="ui-header-meta">Administra infraestructura de red para clientes empresariales</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('redes-config.create')): ?>
                <a href="<?php echo e(route('redes-config.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Red
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
            <form method="GET" action="<?php echo e(route('redes-config.index')); ?>" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar red/SSID..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-3">
                    <select name="cliente_id" class="form-select">
                        <option value="">Todos los clientes</option>
                        <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cliente->id); ?>" <?php echo e(request('cliente_id') == $cliente->id ? 'selected' : ''); ?>>
                            <?php echo e($cliente->nombre); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="vlan_id" class="form-select">
                        <option value="">Todas las VLANs</option>
                        <?php $__currentLoopData = $redes->pluck('vlan_id')->filter()->unique()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vlan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($vlan); ?>" <?php echo e(request('vlan_id') == $vlan ? 'selected' : ''); ?>>
                            VLAN <?php echo e($vlan); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <table class="table table-hover dt-table" id="redesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre de Red</th>
                            <th>SSID WiFi</th>
                            <th>VLAN</th>
                            <th>Cliente</th>
                            <th>DHCP</th>
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
    var table = $('#redesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?php echo e(route("redes-config.ajax")); ?>',
            type: 'GET',
            data: function(d) {
                d.cliente_id = $('select[name="cliente_id"]').val();
                d.vlan_id = $('select[name="vlan_id"]').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, width: '50px' },
            { 
                data: 'nombre_red', 
                name: 'nombre_red',
                render: function(data) {
                    return '<strong>' + data + '</strong>';
                }
            },
            { data: 'ssid_wifi', name: 'ssid_wifi' },
            { 
                data: 'vlan_id', 
                name: 'vlan_id',
                render: function(data) {
                    if (data) return '<span class="badge bg-info">VLAN ' + data + '</span>';
                    return '<span class="text-muted">-</span>';
                }
            },
            { data: 'cliente', name: 'cliente' },
            { 
                data: 'dhcp_activado', 
                name: 'dhcp_activado',
                render: function(data) {
                    return data ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>';
                }
            },
            { 
                data: 'activo_label', 
                name: 'activo',
                render: function(data, type, row) {
                    var cls = row.activo ? 'success' : 'secondary';
                    return '<span class="badge bg-' + cls + '">' + data + '</span>';
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/redes-config/index.blade.php ENDPATH**/ ?>