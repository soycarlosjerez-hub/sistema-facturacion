<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <title><?php echo e(config('app.name', 'Laravel')); ?></title>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/scss/app.scss', 'resources/js/app.js']); ?>
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <?php echo e($slot); ?>

        </div>
    </body>
</html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/layouts/guest.blade.php ENDPATH**/ ?>