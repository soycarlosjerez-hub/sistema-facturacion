<?php $__env->startSection('title', 'Panel Ejecutivo - Dueño del Sistema'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    
    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Panel Ejecutivo</h2>
                    <p class="mb-0 opacity-75">Resumen general de Erpipos ERP · <?php echo e(now()->translatedFormat('l, j \d\e F \d\e Y')); ?></p>
                </div>
            </div>
            <div class="ui-header-actions">
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?php echo e(route('owner.instances.create')); ?>" class="ui-btn ui-btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Nueva Instancia
                    </a>
                    <a href="<?php echo e(route('owner.plans.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-primary btn-sm">
                        <i class="bi bi-card-checklist me-1"></i>Planes
                    </a>
                    <a href="<?php echo e(route('owner.business-types.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-primary btn-sm">
                        <i class="bi bi-tags me-1"></i>Tipos
                    </a>
                    <a href="<?php echo e(route('owner.modules.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-primary btn-sm">
                        <i class="bi bi-grid me-1"></i>Módulos
                    </a>
                    <a href="<?php echo e(route('owner.activity.history')); ?>" class="ui-btn ui-btn-ghost ui-btn-primary btn-sm">
                        <i class="bi bi-clock-history me-1"></i>Audit Log
                    </a>
                    <a href="<?php echo e(route('owner.bootstrap.status')); ?>" class="ui-btn ui-btn-ghost ui-btn-primary btn-sm">
                        <i class="bi bi-shield-lock me-1"></i>Owner Bootstrap
                    </a>
                </div>
            </div>
        </div>
    </div>

    
    <?php echo $__env->make('owner.partials._alert_banner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="row g-3 mb-4">
        <?php echo $__env->make('owner.partials._kpi_mrr', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('owner.partials._kpi_arr', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('owner.partials._kpi_growth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('owner.partials._kpi_collection', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('owner.partials._kpi_arpu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('owner.partials._kpi_active', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    
    <div class="row g-3 mb-4">
        <?php echo $__env->make('owner.partials._kpi_new', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('owner.partials._kpi_active_users', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('owner.partials._kpi_churn', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('owner.partials._kpi_errors', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('owner.partials._kpi_overdue', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('owner.partials._kpi_pending', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <?php echo $__env->make('owner.partials._chart_mrr_trend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div class="col-lg-4">
            <?php echo $__env->make('owner.partials._chart_revenue_trend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div class="col-lg-3">
            <?php echo $__env->make('owner.partials._chart_errors', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <?php echo $__env->make('owner.partials._chart_instance_health', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div class="col-lg-7">
            <?php echo $__env->make('owner.partials._chart_plan_distribution', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <?php echo $__env->make('owner.partials._activity_feed', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div class="col-lg-4">
            <?php echo $__env->make('owner.partials._upcoming_renewals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div class="col-lg-4">
            <?php echo $__env->make('owner.partials._action_items', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    
    <?php echo $__env->make('owner.partials._overdue_table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('owner.partials._recent_instances_table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</div>
</div>


<script>
window.ownerDashboard = {
    mrrLabels: <?php echo json_encode($mrrChartLabels); ?>,
    mrrData: <?php echo json_encode($mrrChartData); ?>,
    revenueLabels: <?php echo json_encode($revenueChartLabels); ?>,
    revenueData: <?php echo json_encode($revenueChartData); ?>,
    errorLabels: <?php echo json_encode($errorLabels); ?>,
    errorData: <?php echo json_encode($errorChartData); ?>,
    planDistribution: <?php echo json_encode($planDistribution); ?>,
    healthData: {
        active: <?php echo e($activas); ?>,
        trial: <?php echo e($enPrueba); ?>,
        blocked: <?php echo e($bloqueadas); ?>,
        churnRisk: <?php echo e($churnRiskCount); ?>,
        pending: <?php echo e($pendingApprovals); ?>,
        archived: <?php echo e($archivadas); ?>,
    }
};
</script>

<?php $__env->startPush('scripts'); ?>
<?php echo app('Illuminate\Foundation\Vite')(['resources/js/dashboard.js']); ?>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/dashboard.blade.php ENDPATH**/ ?>