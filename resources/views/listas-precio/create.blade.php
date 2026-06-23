@extends('layouts.app')
@section('title', 'Nueva Lista de Precios')

@push('styles')
<style>
.premium-header {
    background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
    border-radius: 1rem;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px -5px rgba(14,165,233, 0.4);
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
.sticky-save-bar {
    position: fixed;
    bottom: 0;
    left: var(--sidebar-width, 280px);
    right: 0;
    background: #fff;
    border-top: 2px solid #0ea5e9;
    padding: 0.75rem 1.5rem;
    z-index: 1050;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
}
.sticky-save-bar .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
body.dark-mode .sticky-save-bar {
    background: #0f172a;
    border-top-color: #38bdf8;
}
@media (max-width: 991.98px) {
    .sticky-save-bar { left: 0; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="premium-header">
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-tags fs-2 text-white"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Nueva Lista de Precios</h2>
                    <p class="text-white text-opacity-75 mb-0">Define una nueva lista con precios especiales.</p>
                </div>
            </div>
            <a href="{{ route('listas-precio.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px);">
        <form id="listaPrecioForm" action="{{ route('listas-precio.store') }}" method="POST">
            @csrf
            <div class="card-body p-4 p-md-5">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">C&oacute;digo <span class="text-danger">*</span></label>
                        <input type="text" name="codigo" class="form-control form-control-lg" value="{{ old('codigo') }}" required maxlength="20" placeholder="MAYORISTA">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control form-control-lg" value="{{ old('nombre') }}" required maxlength="255" placeholder="Precio Mayorista">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Descripci&oacute;n</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Opcional: descripci&oacute;n de la lista">{{ old('descripcion') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Vigencia desde</label>
                        <input type="date" name="vigencia_desde" class="form-control" value="{{ old('vigencia_desde') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Vigencia hasta</label>
                        <input type="date" name="vigencia_hasta" class="form-control" value="{{ old('vigencia_hasta') }}">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="activa" name="activa" value="1" {{ old('activa', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="activa">Activa</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="stickySaveBar" class="sticky-save-bar">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle text-primary"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva lista de precios</span>
        </div>
        <button type="submit" form="listaPrecioForm" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="background: linear-gradient(135deg, #0ea5e9, #2563eb); border: none;">
            <i class="bi bi-save me-1"></i>Guardar Lista
        </button>
    </div>
</div>
@endsection
