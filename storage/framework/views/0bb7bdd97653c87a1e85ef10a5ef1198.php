<?php $__env->startSection('title', 'Importar Productos'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
/* Productos import-specific styles */
.drop-zone {
    border: 2px dashed #cbd5e1;
    border-radius: 1rem;
    padding: 3rem;
    text-align: center;
    cursor: pointer;
    transition: all .3s;
    background: #f8fafc;
}
.drop-zone:hover, .drop-zone.dragover {
    border-color: #4f46e5;
    background: rgba(99,102,241,.05);
}
.drop-zone.has-file {
    border-color: #22c55e;
    background: rgba(34,197,94,.05);
}
.mapping-row { transition: background .2s; }
.mapping-row:hover { background: #f8fafc; }
body.dark-mode .mapping-row:hover { background: rgba(255,255,255,.03); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5;">

    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-upload"></i>
                </div>
                <div>
                    <div class="ui-header-title">Importar Productos</div>
                    <div class="ui-header-meta">
                        <?php if(!isset($step) || $step !== 'map'): ?>
                            <i class="bi bi-file-earmark me-1"></i>Sube un archivo CSV o Excel con tus productos
                        <?php else: ?>
                            <i class="bi bi-diagram-3 me-1"></i>Asigna las columnas del archivo a los campos del producto
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('productos.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if(!isset($step) || $step !== 'map'): ?>
        
        <div class="ui-card mb-4" style="--delay:.1s;">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title">
                <i class="bi bi-cloud-arrow-up icon-blue"></i>
                Subir archivo
            </div>
            <div class="ui-card-subtitle">Arrastra o selecciona tu archivo CSV/Excel</div>
            <div class="card-body p-4">
                <form action="<?php echo e(route('productos.import.preview')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="drop-zone" id="dropZone" onclick="document.getElementById('fileInput').click()" role="button" tabindex="0" aria-label="Seleccionar archivo para importar" onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
                        <div id="dropContent">
                            <i class="bi bi-file-earmark-spreadsheet" style="font-size:3rem;color:#4f46e5;"></i>
                            <h6 class="fw-bold mt-3 mb-1">Arrastra el archivo aquí o haz clic para seleccionar</h6>
                            <p class="text-muted small mb-0">Formatos: CSV, XLSX • Máx. 10 MB</p>
                        </div>
                        <div id="dropFileInfo" class="d-none">
                            <i class="bi bi-check-circle-fill text-success" style="font-size:2rem;"></i>
                            <h6 class="fw-bold mt-2 mb-0" id="fileName"></h6>
                            <p class="text-muted small mb-0" id="fileSize"></p>
                        </div>
                        <input type="file" name="file" id="fileInput" class="d-none" accept=".csv,.txt,.xlsx,.xls" required>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-5 shadow fw-bold" id="uploadBtn" disabled>
                            <i class="bi bi-eye me-2"></i>Vista Previa y Mapear
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="ui-card mb-4" style="--delay:.15s;">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle text-info me-1"></i>Consejos</h6>
                <ul class="text-muted small mb-0">
                    <li>La primera fila debe contener los nombres de las columnas (cabecera).</li>
                    <li>Después de subir, podrás indicar qué columna corresponde a cada campo.</li>
                    <li>Las columnas no mapeadas se ignorarán.</li>
                    <li>El delimitador se detecta automáticamente (coma o punto y coma).</li>
                </ul>
            </div>
        </div>

        <div class="ui-card" style="--delay:.2s;">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title">
                <i class="bi bi-file-text" style="color:#10b981;"></i>
                Formato de ejemplo
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">La primera fila debe tener los nombres de las columnas. Ejemplo con <strong>coma (,)</strong>:</p>
                <div class="bg-dark rounded-3 p-3 mb-3" style="overflow-x:auto;">
                    <pre class="mb-0 text-light" style="font-size:.8rem;line-height:1.5;">nombre,codigo_barras,precio,stock,descripcion,precio_compra,unidad_medida,itbis_porcentaje,categoria
Laptop HP Probook,HP123,45000.00,10,Laptop HP Probook 15.6",38000.00,Unidad,18,Electrónica
Teclado Logitech,TEC456,1200.50,25,Teclado inalámbrico Logitech,800.00,Unidad,18,Electrónica
Silla Ergonómica,SILL789,8500.00,5,,6000.00,Unidad,18,Muebles</pre>
                </div>
                <p class="text-muted small mb-3">O con <strong>punto y coma (;)</strong>:</p>
                <div class="bg-dark rounded-3 p-3">
                    <pre class="mb-0 text-light" style="font-size:.8rem;line-height:1.5;">nombre;codigo_barras;precio;stock;descripcion;precio_compra;unidad_medida;itbis_porcentaje;categoria
Laptop HP Probook;HP123;45000.00;10;Laptop HP Probook 15.6&quot;;38000.00;Unidad;18;Electrónica
Teclado Logitech;TEC456;1200.50;25;Teclado inalámbrico Logitech;800.00;Unidad;18;Electrónica
Silla Ergonómica;SILL789;8500.00;5;;6000.00;Unidad;18;Muebles</pre>
                </div>
                <div class="mt-3 small text-muted">
                    <i class="bi bi-check-circle text-success me-1"></i>
                    <strong>Nota:</strong> Las columnas <code>nombre</code>, <code>precio</code> y <code>stock</code> son obligatorias. Las demás pueden omitirse si no están en tu archivo.
                </div>
            </div>
        </div>

        <?php $__env->startPush('scripts'); ?>
        <script>
            const fileInput = document.getElementById('fileInput');
            const dropZone = document.getElementById('dropZone');
            const dropContent = document.getElementById('dropContent');
            const dropFileInfo = document.getElementById('dropFileInfo');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            const uploadBtn = document.getElementById('uploadBtn');

            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) updateDropZone(this.files[0]);
            });
            dropZone.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('dragover'); });
            dropZone.addEventListener('dragleave', function() { this.classList.remove('dragover'); });
            dropZone.addEventListener('drop', function(e) {
                e.preventDefault(); this.classList.remove('dragover');
                if (e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    updateDropZone(e.dataTransfer.files[0]);
                }
            });
            function updateDropZone(file) {
                dropContent.classList.add('d-none');
                dropFileInfo.classList.remove('d-none');
                fileName.textContent = file.name;
                fileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
                dropZone.classList.add('has-file');
                uploadBtn.disabled = false;
            }
        </script>
        <?php $__env->stopPush(); ?>
    <?php else: ?>
        
        <form action="<?php echo e(route('productos.import.process')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="hash" value="<?php echo e($hash); ?>">
            <input type="hidden" name="delimiter" value="<?php echo e($delimiter); ?>">

            <div class="ui-card mb-4" style="--delay:.1s;">
                <div class="ui-card-accent"></div>
                <div class="d-flex justify-content-between align-items-center px-4 pt-4">
                    <div class="ui-card-title" style="padding:0;">
                        <i class="bi bi-diagram-3 icon-blue"></i>
                        Mapeo de Columnas
                    </div>
                    <span class="badge rounded-pill" style="background:rgba(99,102,241,.1);color:#4f46e5;font-weight:600;"><?php echo e(count($headers)); ?> columnas</span>
                </div>
                <div class="ui-card-subtitle">Asigna cada columna del archivo al campo correspondiente</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;">
                                    <th class="ps-4 py-3" width="220">Campo del Producto</th>
                                    <th class="py-3">Columna del Archivo</th>
                                    <th class="text-end pe-4 py-3" width="100">Obligatorio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $productFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="mapping-row">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-arrow-right-short text-muted"></i>
                                            <span class="fw-semibold small"><?php echo e($label); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <select name="mapping[<?php echo e($field); ?>]" class="ui-select ui-select-sm" style="max-width:300px;border-radius:.65rem;">
                                            <option value="">— No mapear —</option>
                                            <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($header); ?>" 
                                                    <?php echo e(strcasecmp($field, $header) === 0 || strcasecmp($label, $header) === 0 ? 'selected' : ''); ?>>
                                                    <?php echo e($header); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </td>
                                    <td class="text-end pe-4">
                                        <?php if(in_array($field, ['nombre', 'precio'])): ?>
                                            <span class="badge rounded-pill" style="background:rgba(239,68,68,.1);color:#dc2626;font-weight:600;">Sí</span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill" style="background:#f1f5f9;color:#64748b;font-weight:600;">No</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="ui-card mb-4" style="--delay:.15s;">
                <div class="ui-card-accent"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-info-circle text-info mt-1"></i>
                        <div class="small text-muted">
                            <strong>Columnas detectadas:</strong> 
                            <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge rounded-pill me-1" style="background:#f1f5f9;color:#475569;font-weight:600;"><?php echo e($h); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <br>
                            Los campos marcados como <strong>obligatorios</strong> deben ser mapeados.
                            Las filas sin nombre serán omitidas automáticamente.
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="<?php echo e(route('productos.import')); ?>" class="ui-btn ui-btn-primary rounded-pill px-4 fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Subir otro archivo
                </a>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-5 shadow fw-bold" id="importProcessBtn">
                    <i class="bi bi-cloud-upload me-2"></i>Importar Productos
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const importForm = document.querySelector('form[action*="import/process"]');
        if (importForm) {
            importForm.addEventListener('submit', function() {
                const btn = this.querySelector('#importProcessBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Importando...';
                }
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/productos/import.blade.php ENDPATH**/ ?>