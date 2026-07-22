@extends('layouts.app')

@section('title', 'Plantillas de Impresión')

@push('styles')
@include('partials.premium-ui')
<style>
    body.dark-mode .ui-header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Plantillas de Impresión</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-file-earmark-text me-1"></i>
                        <span>Personaliza el encabezado, pie y opciones de cada tipo de documento</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('impresoras.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Impresoras
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <form method="GET" class="d-inline">
                <select name="modulo" class="ui-select ui-select-sm rounded-pill" onchange="this.form.submit()">
                    <option value="">Todos los módulos</option>
                    @foreach($modulos as $k => $v)
                        <option value="{{ $k }}" {{ request('modulo')==$k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="row g-3">
        @forelse($plantillas as $p)
        <div class="col-md-6 col-lg-4">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold mb-1">{{ $p->nombre }}</h6>
                            <span class="ui-badge ui-badge-neutral rounded-pill me-1">{{ $modulos[$p->modulo] ?? $p->modulo }}</span>
                            <span class="ui-badge ui-badge-info rounded-pill">{{ strtoupper($p->tipo_formato) }}</span>
                        </div>
                        @if($p->activo)
                            <span class="ui-badge ui-badge-success rounded-pill">Activa</span>
                        @else
                            <span class="ui-badge ui-badge-neutral rounded-pill">Inactiva</span>
                        @endif
                    </div>

                    <hr class="my-2">

                    <form method="POST" action="{{ route('impresoras.plantilla-update', $p) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="ui-label small fw-semibold">Nombre</label>
                            <input name="nombre" class="ui-input ui-input-sm" value="{{ $p->nombre }}">
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
                            <label class="ui-label small fw-semibold">Encabezado personalizado</label>
                            <textarea name="encabezado_personalizado" class="ui-input ui-input-sm" rows="2">{{ $p->encabezado_personalizado }}</textarea>
                        </div>
                        <div class="mb-2">
                            <label class="ui-label small fw-semibold">Pie personalizado</label>
                            <textarea name="pie_personalizado" class="ui-input ui-input-sm" rows="2">{{ $p->pie_personalizado }}</textarea>
                        </div>

                        <button class="ui-btn ui-btn-solid ui-btn-sm rounded-pill w-100 mt-2">
                            <i class="bi bi-check-lg me-1"></i>Guardar cambios
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body text-center py-5 text-muted">
                    <i class="bi bi-file-earmark fs-1 d-block mb-2"></i>
                    No hay plantillas para este módulo
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection