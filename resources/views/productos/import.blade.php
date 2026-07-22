@extends('layouts.app')
@section('title', 'Importar Productos')

@push('styles')
@include('partials.premium-ui')
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
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-upload"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Importar Productos</h4>
                    <small class="text-white opacity-75">
                        @if(!isset($step) || $step !== 'map')
                            <i class="bi bi-file-earmark me-1"></i>Sube un archivo CSV o Excel con tus productos
                        @else
                            <i class="bi bi-diagram-3 me-1"></i>Asigna las columnas del archivo a los campos del producto
                        @endif
                    </small>
                </div>
            </div>
            <a href="{{ route('productos.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!isset($step) || $step !== 'map')
        {{-- STEP 1: Upload --}}
        <div class="premium-card mb-4" style="animation-delay:.1s;">
            <div class="card-accent blue"></div>
            <div class="premium-card-title">
                <i class="bi bi-cloud-arrow-up icon-blue"></i>
                Subir archivo
            </div>
            <div class="premium-card-subtitle">Arrastra o selecciona tu archivo CSV/Excel</div>
            <div class="card-body p-4">
                <form action="{{ route('productos.import.preview') }}" method="POST" enctype="multipart/form-data">
                    @csrf
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
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow fw-bold" id="uploadBtn" disabled>
                            <i class="bi bi-eye me-2"></i>Vista Previa y Mapear
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="premium-card mb-4" style="animation-delay:.15s;">
            <div class="card-accent blue"></div>
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

        <div class="premium-card" style="animation-delay:.2s;">
            <div class="card-accent blue"></div>
            <div class="premium-card-title">
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

        @push('scripts')
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
        @endpush
    @else
        {{-- STEP 2: Column Mapping --}}
        <form action="{{ route('productos.import.process') }}" method="POST">
            @csrf
            <input type="hidden" name="hash" value="{{ $hash }}">
            <input type="hidden" name="delimiter" value="{{ $delimiter }}">

            <div class="premium-card mb-4" style="animation-delay:.1s;">
                <div class="card-accent blue"></div>
                <div class="d-flex justify-content-between align-items-center px-4 pt-4">
                    <div class="premium-card-title" style="padding:0;">
                        <i class="bi bi-diagram-3 icon-blue"></i>
                        Mapeo de Columnas
                    </div>
                    <span class="badge rounded-pill" style="background:rgba(99,102,241,.1);color:#4f46e5;font-weight:600;">{{ count($headers) }} columnas</span>
                </div>
                <div class="premium-card-subtitle">Asigna cada columna del archivo al campo correspondiente</div>
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
                                @foreach($productFields as $field => $label)
                                <tr class="mapping-row">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-arrow-right-short text-muted"></i>
                                            <span class="fw-semibold small">{{ $label }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <select name="mapping[{{ $field }}]" class="form-select form-select-sm" style="max-width:300px;border-radius:.65rem;">
                                            <option value="">— No mapear —</option>
                                            @foreach($headers as $header)
                                                <option value="{{ $header }}" 
                                                    {{ strcasecmp($field, $header) === 0 || strcasecmp($label, $header) === 0 ? 'selected' : '' }}>
                                                    {{ $header }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-end pe-4">
                                        @if(in_array($field, ['nombre', 'precio']))
                                            <span class="badge rounded-pill" style="background:rgba(239,68,68,.1);color:#dc2626;font-weight:600;">Sí</span>
                                        @else
                                            <span class="badge rounded-pill" style="background:#f1f5f9;color:#64748b;font-weight:600;">No</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="premium-card mb-4" style="animation-delay:.15s;">
                <div class="card-accent blue"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-info-circle text-info mt-1"></i>
                        <div class="small text-muted">
                            <strong>Columnas detectadas:</strong> 
                            @foreach($headers as $h)
                                <span class="badge rounded-pill me-1" style="background:#f1f5f9;color:#475569;font-weight:600;">{{ $h }}</span>
                            @endforeach
                            <br>
                            Los campos marcados como <strong>obligatorios</strong> deben ser mapeados.
                            Las filas sin nombre serán omitidas automáticamente.
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('productos.import') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Subir otro archivo
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow fw-bold" id="importProcessBtn">
                    <i class="bi bi-cloud-upload me-2"></i>Importar Productos
                </button>
            </div>
        </form>
    @endif
</div>

@push('scripts')
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
@endpush
@endsection
