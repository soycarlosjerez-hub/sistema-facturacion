@extends('layouts.app')
@section('title', 'Nueva Secuencia e-CF')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="container-fluid px-4 ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>    <div class="bubble"></div>    <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-list-ol"></i></div>
                <div>
                    <h4 class="ui-header-title">Nueva Secuencia e-CF</h4>
                    <div class="ui-header-meta"><i class="bi bi-plus-circle me-1"></i> <span>Configure una nueva numeración autorizada por DGII</span></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('secuencias-ecf.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill"><i class="bi bi-arrow-left me-1"></i> Volver</a>
            </div>
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

    <div class="ui-card mb-5" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <form id="secuenciaEcfForm" action="{{ route('secuencias-ecf.store') }}" method="POST">
            @csrf
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color: #f59e0b;">
                        <i class="bi bi-info-circle me-2"></i>Información de la Secuencia
                    </h6>
                </div>

                <div class="alert alert-info border-0 rounded-3 small mb-4">
                    <i class="bi bi-info-circle me-1"></i>
                    Solicite su rango de numeración en el portal de <strong>DGII</strong> → e-CF → Autorización de Secuencia.
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label">Tipo de e-CF</label>
                        <select name="tipo_ecf" class="ui-select rounded-3" required>
                            <option value="">Seleccione...</option>
                            @foreach($tipos as $key => $nombre)
                                @if(!in_array($key, $usadas))
                                    <option value="{{ $key }}">{{ $key }} - {{ $nombre }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('tipo_ecf')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="ui-label">Nombre Descriptivo</label>
                        <input type="text" name="nombre" class="ui-input rounded-3" placeholder="Ej: Facturas Crédito Fiscal 2026" required>
                    </div>

                    <div class="col-md-12">
                        <label class="ui-label">Descripción (opcional)</label>
                        <input type="text" name="descripcion" class="ui-input rounded-3" placeholder="Notas internas sobre esta secuencia">
                    </div>

                    <div class="col-md-4">
                        <label class="ui-label">Desde</label>
                        <input type="number" name="desde" class="ui-input rounded-3" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Hasta</label>
                        <input type="number" name="hasta" class="ui-input rounded-3" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Actual (Último usado)</label>
                        <input type="number" name="actual" class="ui-input rounded-3" min="0" value="0" required>
                    </div>

                    <div class="col-md-6">
                        <label class="ui-label">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="ui-input rounded-3" required>
                    </div>

                    <div class="col-md-6">
                        <label class="ui-label">Estado</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" checked>
                            <label class="form-check-label" for="activo">Activa</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div style="height: 80px;"></div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#f59e0b;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva secuencia e-CF</span>
        </div>
        <div>
            <a href="{{ route('secuencias-ecf.index') }}" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="secuenciaEcfForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Guardar Secuencia
            </button>
        </div>
    </div>
</div>
@endsection
