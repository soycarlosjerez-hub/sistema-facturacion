@extends('layouts.app')
@section('title', 'Importar Proveedores')

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
    border-color: #3b82f6;
    background: rgba(59,130,246,.05);
}
.drop-zone.has-file {
    border-color: #22c55e;
    background: rgba(34,197,94,.05);
}
body.dark-mode .drop-zone { background: rgba(15,23,42,.3); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4" style="background:linear-gradient(135deg,#3b82f6,#6366f1,#8b5cf6,#3b82f6);box-shadow:0 8px 32px rgba(59,130,246,.25);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle" style="background:rgba(255,255,255,.2);border-color:rgba(255,255,255,.35);">
                    <i class="bi bi-upload"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Importar Proveedores</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-file-earmark me-1"></i>Sube un archivo CSV o Excel con tus proveedores
                    </small>
                </div>
            </div>
            <a href="{{ route('proveedores.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
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

    <div class="premium-card mb-4" style="animation-delay:.1s;">
        <div class="card-accent blue"></div>
        <div class="premium-card-title">
            <i class="bi bi-cloud-arrow-up" style="color:#3b82f6;"></i>
            Subir archivo
        </div>
        <div class="premium-card-subtitle">Arrastra o selecciona tu archivo CSV o Excel</div>
        <div class="card-body p-4">
            <form action="{{ route('proveedores.importar.procesar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="drop-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
                    <div id="dropContent">
                        <i class="bi bi-file-earmark-spreadsheet" style="font-size:3rem;color:#3b82f6;"></i>
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

    <div class="premium-card" style="animation-delay:.2s;">
        <div class="card-accent blue"></div>
        <div class="premium-card-title">
            <i class="bi bi-file-text" style="color:#10b981;"></i>
            Formato esperado
        </div>
        <div class="card-body p-4">
            <p class="text-muted small mb-3">La primera fila debe contener los nombres de las columnas. Ejemplo:</p>
            <div class="bg-dark rounded-3 p-3 mb-3" style="overflow-x:auto;">
                <pre class="mb-0 text-light" style="font-size:.8rem;line-height:1.5;">nombre,email,telefono,direccion,rnc,tipo_persona,activo
Distribuidora Corripio,info@corripio.com,8095550101,Ave. 27 de Febrero 101-123456-7,juridica,Sí
Tech Solutions RD,ventas@techsolutions.do,8095550202,Calle Las Palmas 45-987654-3,fisica,Sí
</pre>
            </div>
            <div class="mt-3 small text-muted">
                <i class="bi bi-check-circle text-success me-1"></i>
                <strong>Notas:</strong>
                <ul class="mb-0 mt-1">
                    <li>La columna <code>nombre</code> es obligatoria.</li>
                    <li>Las demás columnas son opcionales.</li>
                    <li>Si un proveedor ya existe (mismo nombre), se actualizarán sus datos.</li>
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
