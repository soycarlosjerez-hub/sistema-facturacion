<?php
    $empresa = \App\Models\SystemSetting::allCached();
    $nombreEmpresa = \App\Models\SystemSetting::nombreEmpresaActual();
?>
<?php if($pdfLogoUrl): ?>
<div style="margin-bottom: 4px;">
    <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 70px; max-height: 55px; object-fit: contain;" alt="Logo">
</div>
<?php endif; ?>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/partials/_pdf_logo.blade.php ENDPATH**/ ?>