

<?php $__env->startSection('title', 'Cuentas Bancarias - Owner'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.cuentas-table { --bs-table-bg: transparent; --bs-table-hover-bg: rgba(5,150,105,.04); margin: 0; }
.cuentas-table thead th { background: rgba(241,245,249,.8); color: #64748b; font-size: .7rem; text-transform: uppercase; letter-spacing: .5px; font-weight: 700; padding: .85rem 1rem; border-bottom: 1px solid #e2e8f0; }
.cuentas-table tbody td { padding: .85rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: .9rem; }
.cuentas-table tbody tr:last-child td { border-bottom: none; }
.cuentas-table tbody tr { transition: background .15s; }
.cuentas-table tbody tr:hover { background: rgba(5,150,105,.03); }
.avatar-circle { width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.2rem; transition: transform 0.2s; }
tr:hover .avatar-circle { transform: scale(1.1); }
.status-badge { padding: 0.4em 0.8em; border-radius: 2rem; font-weight: 500; font-size: 0.75rem; letter-spacing: 0.5px; }
.instance-badge { font-size: 0.75rem; padding: 0.3em 0.7em; border-radius: 2rem; }
body.dark-mode .cuentas-table thead th { background: rgba(15,23,42,.5); color: #94a3b8; border-color: #1e293b; }
body.dark-mode .cuentas-table tbody td { border-bottom-color: #1e293b; color: #cbd5e1; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">


<div class="ui-header mb-4" style="--delay:.1s">
    <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
    <div class="ui-header-body">
        <div class="ui-header-left">
            <div class="ui-avatar-circle">
                <i class="bi bi-bank"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-1">Cuentas Bancarias</h2>
                <p class="mb-0 opacity-75">Gesti&oacute;n de cuentas bancarias de todas las instancias.</p>
            </div>
        </div>
    </div>
</div>


<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="ui-stat h-100" style="--delay:.1s">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="ui-avatar-circle bg-primary-subtle">
          <i class="bi bi-bank fs-4"></i>
        </div>
        <div>
          <small class="ui-stat-label d-block">Total Cuentas</small>
          <h3 class="ui-stat-value mb-0"><?php echo e($stats['total'] ?? 0); ?></h3>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="ui-stat h-100" style="--delay:.15s">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="ui-avatar-circle bg-success-subtle">
          <i class="bi bi-check-circle fs-4"></i>
        </div>
        <div>
          <small class="ui-stat-label d-block">Activas</small>
          <h3 class="ui-stat-value mb-0"><?php echo e($stats['activas'] ?? 0); ?></h3>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="ui-stat h-100" style="--delay:.2s">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="ui-avatar-circle bg-info-subtle">
          <i class="bi bi-building fs-4"></i>
        </div>
        <div>
          <small class="ui-stat-label d-block">Instancias con Cuentas</small>
          <h3 class="ui-stat-value mb-0"><?php echo e($stats['instancias_con_cuentas'] ?? 0); ?></h3>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="ui-card mb-4" style="--delay:.2s">
  <div class="ui-card-accent" style="background:#8b5cf6"></div>
  <div class="card-body p-4">
    <form id="filterForm" method="GET" action="<?php echo e(route('owner.cuentas-bancarias.index')); ?>">
      <div class="row g-3 align-items-center">
        <div class="col-md-4">
          <label for="search" class="ui-label small">Buscar</label>
          <input type="text" class="ui-input" id="search" name="search" placeholder="Nombre, banco, nro. cuenta..." value="<?php echo e(request('search')); ?>">
        </div>
        <div class="col-md-3">
          <label for="instance_id" class="ui-label small">Instancia</label>
          <select class="ui-select" id="instance_id" name="instance_id">
            <option value="">Todas las instancias</option>
            <?php $__currentLoopData = $instances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($inst->id); ?>" <?php echo e(old('instance_id', request('instance_id')) == $inst->id ? 'selected' : ''); ?>><?php echo e($inst->nombre); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="ui-label d-block pt-2">Incluir Inactivas</label>
          <div class="form-check form-switch mt-1">
            <input class="form-check-input" type="checkbox" id="inactive" name="inactive" <?php echo e(request('inactive') ? 'checked' : ''); ?>>
            <label class="form-check-label small text-muted" for="inactive">Mostrar inactivas</label>
          </div>
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
          <button type="submit" class="ui-btn ui-btn-ghost btn-sm">
            <i class="bi bi-search me-1"></i>Filtrar
          </button>
          <a href="<?php echo e(route('owner.cuentas-bancarias.index')); ?>" class="ui-btn ui-btn-danger btn-sm">
            <i class="bi bi-x-lg"></i>
          </a>
        </div>
      </div>
    </form>
  </div>
</div>


<div class="ui-card" style="--delay:.25s">
  <div class="ui-card-accent" style="background:#8b5cf6"></div>
  <div class="card-header bg-transparent border-0 pb-0 p-4">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="fw-bold mb-0"><i class="bi bi-bank2 me-2"></i>Lista de Cuentas Bancarias</h5>
      <span class="ui-badge ui-badge-neutral"><?php echo e($cuentas->total()); ?> registro(s)</span>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table cuentas-table align-middle mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Cuenta</th>
          <th>Banco</th>
          <th>Nro. Cuenta</th>
          <th>Moneda</th>
          <th>Saldo</th>
          <th>Instancia</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $cuentas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cuenta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td class="fw-medium text-muted"><?php echo e($loop->iteration + ($cuentas->currentPage() - 1) * $cuentas->perPage()); ?></td>
            <td>
              <div class="d-flex align-items-center">
                <div class="ui-avatar-circle bg-primary-subtle me-2">
                  <i class="bi bi-bank2"></i>
                </div>
                <div>
                  <div class="fw-semibold"><?php echo e($cuenta->nombre); ?></div>
                  <small class="text-muted"><?php echo e($cuenta->tipo_cuenta); ?></small>
                </div>
              </div>
            </td>
            <td><strong><?php echo e($cuenta->banco); ?></strong></td>
            <td><code><?php echo e($cuenta->numero_cuenta); ?></code></td>
            <td><span class="ui-badge ui-badge-neutral"><?php echo e($cuenta->moneda); ?></span></td>
            <td class="fw-bold"><?php echo e(number_format($cuenta->saldo, 2)); ?></td>
            <td>
              <span class="instance-badge bg-light text-dark rounded-pill px-3 py-1">
                <i class="bi bi-building me-1"></i>
                <?php echo e($cuenta->businessInstance->nombre ?? 'N/A'); ?>

              </span>
            </td>
            <td>
              <?php if($cuenta->activo): ?>
                <span class="ui-badge ui-badge-success rounded-pill">Activa</span>
              <?php else: ?>
                <span class="ui-badge ui-badge-neutral rounded-pill">Inactiva</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="d-flex gap-1">
                <a href="<?php echo e(route('cuentas-bancarias.show', $cuenta->id)); ?>" class="ui-action ui-action-view" title="Ver"><i class="bi bi-eye"></i></a>
                <a href="<?php echo e(route('cuentas-bancarias.edit', $cuenta->id)); ?>" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>
                <form action="<?php echo e(route('cuentas-bancarias.destroy', $cuenta->id)); ?>" method="POST" class="d-inline" onsubmit="return UI.confirm.delete('&iquest;Seguro que desea eliminar esta cuenta?')">
                  <input type="hidden" name="_method" value="DELETE">
                  <button type="submit" class="ui-action ui-action-delete" title="Eliminar"><i class="bi bi-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
          <tr>
            <td colspan="9" class="text-center py-5">
              <div class="text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                <p class="mb-0">No se encontraron cuentas bancarias.</p>
                <small>Intenta ajustar los filtros de b&uacute;squeda.</small>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer bg-transparent border-0 py-3 px-4">
    <?php echo e($cuentas->links()); ?>

  </div>
</div>

</div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  // Live Search
  document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const form = document.getElementById('filterForm');
    let debounceTimer;
    searchInput.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => { form.submit(); }, 500);
    });
  });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/cuentas-bancarias/index.blade.php ENDPATH**/ ?>