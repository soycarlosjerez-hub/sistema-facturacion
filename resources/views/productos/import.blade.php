@extends('layouts.app')
@section('title', 'Importar Productos')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
        position: relative;
        overflow: hidden;
    }
    .premium-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .drop-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 1rem;
        padding: 3rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: #f8fafc;
    }
    .drop-zone:hover, .drop-zone.dragover {
        border-color: #6366f1;
        background: #eef2ff;
    }
    .drop-zone.has-file {
        border-color: #22c55e;
        background: #f0fdf4;
    }
    .mapping-row {
        transition: background 0.2s;
    }
    .mapping-row:hover {
        background: #f8fafc;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="premium-header">
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-upload fs-2 text-white"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Importar Productos</h2>
                    <p class="text-white text-opacity-75 mb-0">
                        @if(!isset($step) || $step !== 'map')
                            Sube un archivo CSV o Excel con tus productos
                        @else
                            Asigna las columnas del archivo a los campos del producto
                        @endif
                    </p>
                </div>
            </div>
            <a href="{{ route('productos.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
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
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom p-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-cloud-arrow-up text-indigo me-2"></i>Subir archivo</h5>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('productos.import.preview') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="drop-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
                                <div id="dropContent">
                                    <i class="bi bi-file-earmark-spreadsheet text-indigo" style="font-size: 3rem;"></i>
                                    <h6 class="fw-bold mt-3 mb-1">Arrastra el archivo aquí o haz clic para seleccionar</h6>
                                    <p class="text-muted small mb-0">Formatos: CSV, XLSX &bull; Máx. 10 MB</p>
                                </div>
                                <div id="dropFileInfo" class="d-none">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                                    <h6 class="fw-bold mt-2 mb-0" id="fileName"></h6>
                                    <p class="text-muted small mb-0" id="fileSize"></p>
                                </div>
                                <input type="file" name="file" id="fileInput" class="d-none" accept=".csv,.txt,.xlsx,.xls" required>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg fw-bold" id="uploadBtn" disabled>
                                        <i class="bi bi-eye me-2"></i>Vista Previa y Mapear
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mt-4">
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

                <div class="card border-0 shadow-sm rounded-4 mt-4">
                    <div class="card-header bg-white border-bottom p-4">
                        <h6 class="fw-bold mb-0"><i class="bi bi-file-text text-success me-1"></i>Formato de ejemplo</h6>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">La primera fila debe tener los nombres de las columnas. Ejemplo con <strong>coma (,)</strong>:</p>
                        <div class="bg-dark rounded-3 p-3 mb-3" style="overflow-x: auto;">
                            <pre class="mb-0 text-light" style="font-size: 0.8rem; line-height: 1.5;">nombre,codigo_barras,precio,stock,descripcion,precio_compra,unidad_medida,itbis_porcentaje,categoria
Laptop HP Probook,HP123,45000.00,10,Laptop HP Probook 15.6\",38000.00,Unidad,18,Electrónica
Teclado Logitech,TEC456,1200.50,25,Teclado inalámbrico Logitech,800.00,Unidad,18,Electrónica
Silla Ergonómica,SILL789,8500.00,5,,6000.00,Unidad,18,Muebles</pre>
                        </div>
                        <p class="text-muted small mb-3">O con <strong>punto y coma (;)</strong>:</p>
                        <div class="bg-dark rounded-3 p-3">
                            <pre class="mb-0 text-light" style="font-size: 0.8rem; line-height: 1.5;">nombre;codigo_barras;precio;stock;descripcion;precio_compra;unidad_medida;itbis_porcentaje;categoria
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

            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            dropZone.addEventListener('dragleave', function() {
                this.classList.remove('dragover');
            });

            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
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

                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-header bg-white border-bottom p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0"><i class="bi bi-diagram-3 text-indigo me-2"></i>Mapeo de Columnas</h5>
                                <span class="badge bg-light text-dark rounded-pill px-3">{{ count($headers) }} columnas detectadas</span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="small text-uppercase text-muted">
                                        <th class="ps-4" width="220">Campo del Producto</th>
                                        <th>Columna del Archivo</th>
                                        <th class="text-end pe-4" width="100">Obligatorio</th>
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
                                            <select name="mapping[{{ $field }}]" class="form-select form-select-sm rounded-pill" style="max-width: 300px;">
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
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">Sí</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 mt-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start gap-3">
                                <i class="bi bi-info-circle text-info mt-1"></i>
                                <div class="small text-muted">
                                    <strong>Columnas detectadas:</strong> 
                                    @foreach($headers as $h)
                                        <span class="badge bg-light text-dark me-1">{{ $h }}</span>
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
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow fw-bold">
                            <i class="bi bi-cloud-upload me-2"></i>Importar Productos
                        </button>
                    </div>
                </form>

    @endif
</div>
@endsection
