@extends('layouts.app')
@section('title', 'Importar Categorías')

@push('styles')
@include('partials.premium-ui')
<style>
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
    border-color: #8b5cf6;
    background: rgba(139,92,246,.05);
}
.drop-zone.has-file {
    border-color: #22c55e;
    background: rgba(34,197,94,.05);
}
body.dark-mode .drop-zone { background: rgba(15,23,42,.3); }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#ec4899;--accent-rgb:236,72,153;--accent-hover:#db2777;">

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
                    <div class="ui-header-title">Importar Categorías</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-file-earmark me-1"></i>Sube un archivo CSV o Excel con tus categorías
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('categorias.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
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

    <div class="ui-card mb-4" style="--delay:.1s;">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title">
            <i class="bi bi-cloud-arrow-up icon-purple"></i>
            Subir archivo
        </div>
        <div class="ui-card-subtitle">Arrastra o selecciona tu archivo CSV o Excel</div>
        <div class="card-body p-4">
            <form action="{{ route('categorias.importar.procesar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="drop-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
                    <div id="dropContent">
                        <i class="bi bi-file-earmark-spreadsheet" style="font-size:3rem;color:#8b5cf6;"></i>
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
                        <i class="bi bi-cloud-upload me-2"></i>Importar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.2s;">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title">
            <i class="bi bi-file-text" style="color:#10b981;"></i>
            Formato esperado
        </div>
        <div class="card-body p-4">
            <p class="text-muted small mb-3">La primera fila debe contener los nombres de las columnas. Ejemplo:</p>
            <div class="bg-dark rounded-3 p-3 mb-3" style="overflow-x:auto;">
                <pre class="mb-0 text-light" style="font-size:.8rem;line-height:1.5;">nombre,descripcion,activa
Electrónica,Productos electrónicos y accesorios,Sí
Ropa,Prendas de vestir,Si
Hogar,Artículos para el hogar,No</pre>
            </div>
            <div class="mt-3 small text-muted">
                <i class="bi bi-check-circle text-success me-1"></i>
                <strong>Notas:</strong>
                <ul class="mb-0 mt-1">
                    <li>La columna <code>nombre</code> es obligatoria.</li>
                    <li><code>descripcion</code> y <code>activa</code> son opcionales.</li>
                    <li>Si una categoría ya existe (mismo nombre), se actualizarán sus datos.</li>
                </ul>
            </div>
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
@endsection
