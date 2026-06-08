@extends('layouts.app')

@section('title', 'Plantillas de Impresión')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-file-earmark-text text-info me-2"></i>Plantillas de Impresión</h2>
            <p class="text-muted mb-0">Personaliza el encabezado, pie y opciones de cada tipo de documento</p>
        </div>
        <div class="d-flex gap-2">
            <form method="GET" class="d-inline">
                <select name="modulo" class="form-select border-0 bg-light" onchange="this.form.submit()">
                    <option value="">Todos los módulos</option>
                    @foreach($modulos as $k => $v)
                        <option value="{{ $k }}" {{ request('modulo')==$k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('impresoras.index') }}" class="btn btn-outline-primary rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Impresoras
            </a>
        </div>
    </div>

    <div class="row g-3">
        @forelse($plantillas as $p)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold mb-1">{{ $p->nombre }}</h6>
                            <span class="badge bg-light text-dark rounded-pill me-1">{{ $modulos[$p->modulo] ?? $p->modulo }}</span>
                            <span class="badge bg-info-subtle text-info rounded-pill">{{ strtoupper($p->tipo_formato) }}</span>
                        </div>
                        <span class="badge rounded-pill bg-{{ $p->activo ? 'success' : 'secondary' }}-subtle text-{{ $p->activo ? 'success' : 'secondary' }}">
                            {{ $p->activo ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>

                    <hr class="my-2">

                    <form method="POST" action="{{ route('impresoras.plantilla-update', $p) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Nombre</label>
                            <input name="nombre" class="form-control form-control-sm border-0 bg-light" value="{{ $p->nombre }}">
                        </div>

                        <div class="mb-2">
                            <div class="form-check form-switch">
                                <input type="hidden" name="incluir_logo" value="0">
                                <input type="checkbox" name="incluir_logo" class="form-check-input" value="1" id="logo{{ $p->id }}" {{ $p->incluir_logo ? 'checked' : '' }}>
                                <label class="form-check-label small" for="logo{{ $p->id }}">Incluir Logo</label>
                            </div>
                            <div class="form-check form-switch">
                                <input type="hidden" name="incluir_encabezado" value="0">
                                <input type="checkbox" name="incluir_encabezado" class="form-check-input" value="1" id="enc{{ $p->id }}" {{ $p->incluir_encabezado ? 'checked' : '' }}>
                                <label class="form-check-label small" for="enc{{ $p->id }}">Encabezado</label>
                            </div>
                            <div class="form-check form-switch">
                                <input type="hidden" name="incluir_pie" value="0">
                                <input type="checkbox" name="incluir_pie" class="form-check-input" value="1" id="pie{{ $p->id }}" {{ $p->incluir_pie ? 'checked' : '' }}>
                                <label class="form-check-label small" for="pie{{ $p->id }}">Pie de página</label>
                            </div>
                            <div class="form-check form-switch">
                                <input type="hidden" name="activo" value="0">
                                <input type="checkbox" name="activo" class="form-check-input" value="1" id="act{{ $p->id }}" {{ $p->activo ? 'checked' : '' }}>
                                <label class="form-check-label small" for="act{{ $p->id }}">Activa</label>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Encabezado personalizado</label>
                            <textarea name="encabezado_personalizado" class="form-control form-control-sm border-0 bg-light" rows="2">{{ $p->encabezado_personalizado }}</textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Pie personalizado</label>
                            <textarea name="pie_personalizado" class="form-control form-control-sm border-0 bg-light" rows="2">{{ $p->pie_personalizado }}</textarea>
                        </div>

                        <button class="btn btn-primary btn-sm rounded-pill w-100 mt-2">
                            <i class="bi bi-check-lg me-1"></i>Guardar cambios
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-file-earmark fs-1 d-block mb-2"></i>
                    No hay plantillas para este módulo
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
