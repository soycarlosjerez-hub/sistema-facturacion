<script>
window.dashboardData = {
    chartLabels: <?php echo json_encode($chartData['labels'], 15, 512) ?>,
    chartData: <?php echo json_encode($chartData['data'], 15, 512) ?>,
    hourlyLabels: <?php echo json_encode($hourlyData['labels'], 15, 512) ?>,
    hourlyData: <?php echo json_encode($hourlyData['data'], 15, 512) ?>,
    paymentLabels: <?php echo json_encode($paymentMethod['labels'], 15, 512) ?>,
    paymentData: <?php echo json_encode($paymentMethod['data'], 15, 512) ?>,
    paymentColors: <?php echo json_encode($paymentMethod['colors'], 15, 512) ?>,
};
</script>

<?php $__env->startPush('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/dashboard.js']); ?>
<?php $__env->stopPush(); ?>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/dashboard/_scripts.blade.php ENDPATH**/ ?>