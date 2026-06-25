@extends('layouts.app')
@section('title', 'Editar Secuencia e-CF')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .premium-header { background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e); }
body.dark-mode .premium-sticky-bar { border-top-color: #f59e0b; }
body.dark-mode .premium-sticky-bar .btn-save { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,.3); }
body.dark-mode .premium-sticky-bar .btn-save:hover { box-shadow: 0 6px 20px rgba(245,158,11,.45); }
body.dark-mode .premium-card .form-control:focus,
body.dark-mode .premium-card .form-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.15); }
body.dark-mode .premium-card .btn-primary { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,.3); }
body.dark-mode .premium-card .btn-primary:hover { box-shadow: 0 6px 20px rgba(245,158,11,.45); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header d-flex justify-content-between align-items-center mb-4" style="background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e);">
        <div class="d-flex align-items-center gap-3">
            <div class="premium-avatar-circle">
                <i class="bi bi-list-ol"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1">Editar Secuencia e-CF</h3>
                <p class="mb-0 opacity-75">{{ $secuencia->tipo_ecf }} - {{ $secuencia->nombre }}</p>
            </div>
        </div>
        <a href="{{ route('secuencias-ecf.index') }}" class="btn btn-light rounded-pill text-dark fw-semibold btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
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

    <div class="premium-card mb-5">
        <div class="card-accent amber"></div>
        <form id="secuenciaEcfForm" action="{{ route('secuencias-ecf.update', $secuencia) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color: #f59e0b;">
                        <i class="bi bi-info-circle me-2"></i>Información de la Secuencia
                    </h6>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Tipo de e-CF</label>
                        <select name="tipo_ecf" class="form-select rounded-3" required>
                            @foreach($tipos as $key => $nombre)
                                <option value="{{ $key }}" {{ $secuencia->tipo_ecf === $key ? 'selected' : '' }}>
                                    {{ $key }} - {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nombre Descriptivo</label>
                        <input type="text" name="nombre" class="form-control rounded-3" value="{{ $secuencia->nombre }}" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold small">Descripción</label>
                        <input type="text" name="descripcion" class="form-control rounded-3" value="{{ $secuencia->descripcion }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Desde</label>
                        <input type="number" name="desde" class="form-control rounded-3" min="1" value="{{ $secuencia->desde }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Hasta</label>
                        <input type="number" name="hasta" class="form-control rounded-3" min="1" value="{{ $secuencia->hasta }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Actual</label>
                        <input type="number" name="actual" class="form-control rounded-3" min="0" value="{{ $secuencia->actual }}" required>
                        <small class="text-muted">Cuidado: modificar esto puede generar saltos o duplicados.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="form-control rounded-3" value="{{ $secuencia->fecha_vencimiento->format('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Estado</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ $secuencia->activo ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">Activa</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="stickySaveBar" class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Editando: {{ $secuencia->tipo_ecf }}</span>
        <div class="d-flex gap-2">
            <a href="{{ route('secuencias-ecf.index') }}" class="btn-cancel rounded-pill px-4">Cancelar</a>
            <button type="submit" form="secuenciaEcfForm" class="btn-save rounded-pill px-4 shadow-sm">
                <i class="bi bi-save me-1"></i> Actualizar Secuencia
            </button>
        </div>
    </div>
</div>
@endsection
