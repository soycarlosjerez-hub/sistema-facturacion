@extends('layouts.app')

@section('title', 'Importar Productos')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-upload text-primary me-2"></i>Importar Productos</h2>
                    <p class="text-muted mb-0">Carga un archivo CSV/Excel con productos al inventario</p>
                </div>
                <a href="{{ route('productos.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
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

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0 text-dark">Subir archivo</h5>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('productos.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">Archivo CSV / Excel</label>
                            <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-file-earmark-spreadsheet"></i></span>
                                <input type="file" name="file" class="form-control border-start-0 ps-0 form-control-lg bg-white" accept=".csv,.txt,.xlsx,.xls" required>
                            </div>
                            <small class="text-muted">Formatos permitidos: CSV, XLSX. Tamaño máximo: 10 MB.</small>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg fw-bold">
                                <i class="bi bi-cloud-arrow-up me-2"></i>Importar Productos
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle text-info me-1"></i>Formato del archivo</h6>
                    <p class="text-muted small mb-2">El archivo debe contener las siguientes columnas en la primera fila (cabecera):</p>
                    <code class="d-block bg-light p-2 rounded small">nombre, codigo_barras, descripcion, precio, precio_compra, unidad_medida, itbis_porcentaje, stock, imagen, categoria</code>
                    <p class="text-muted small mt-3 mb-2"><strong>Nota:</strong> Los productos se identifican por <code>nombre</code>. Si un producto ya existe, se actualizará.</p>
                    <p class="text-muted small mb-0">Columna <code>categoria</code> (opcional): puede ser el ID numérico o el nombre exacto de la categoría.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
