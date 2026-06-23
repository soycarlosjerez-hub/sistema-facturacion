@extends('layouts.app')

@section('title', 'Nueva Lista de Precios')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-tag text-primary me-2"></i>Nueva Lista de Precios</h2>
                    <p class="text-muted mb-0">Define una nueva lista con precios especiales.</p>
                </div>
                <a href="{{ route('listas-precio.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
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

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom border-light p-4"><h5 class="fw-bold mb-0 text-dark"><i class="bi bi-tag me-2 text-primary"></i>Información de la Lista</h5></div>
                <div class="card-body p-4">
                    <form action="{{ route('listas-precio.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
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
                <div class="card-footer bg-light border-top border-light p-4 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('listas-precio.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i>Guardar
                        </button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
