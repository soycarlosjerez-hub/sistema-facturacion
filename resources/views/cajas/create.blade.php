@extends('layouts.app')

@section('title', 'Nueva Caja')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-cash-register text-primary me-2"></i>Nueva Caja</h2>
                    <p class="text-muted mb-0">Registra una nueva caja registradora en el sistema.</p>
                </div>
                <a href="{{ route('cajas.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i>Volver
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
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('cajas.store') }}" method="POST">
                @csrf
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header p-4 text-white" style="background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);">
                        <h5 class="fw-bold mb-0"><i class="bi bi-info-circle me-2"></i>Información de la Caja</h5>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-md-7">
                                <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-tag-fill text-primary"></i></span>
                                    <input type="text" name="nombre" class="form-control border-start-0 @error('nombre') is-invalid @enderror" required placeholder="Ej. Caja 1, Caja Express, Mostrador 2" value="{{ old('nombre') }}">
                                </div>
                                @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Código</label>
                                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-upc text-primary"></i></span>
                                    <input type="text" name="codigo" class="form-control border-start-0 @error('codigo') is-invalid @enderror" placeholder="C01, C02..." value="{{ old('codigo') }}">
                                </div>
                                <small class="text-muted">Identificador corto único.</small>
                                @error('codigo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Ubicación</label>
                                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-geo-alt-fill text-primary"></i></span>
                                    <input type="text" name="ubicacion" class="form-control border-start-0" placeholder="Ej. Mostrador principal, Segundo piso" value="{{ old('ubicacion') }}">
                                </div>
                            </div>

                            @if(isset($sucursales) && $sucursales->count())
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Sucursal</label>
                                <select name="sucursal_id" class="form-select form-select-lg shadow-sm rounded-3">
                                    <option value="">Sin asignar</option>
                                    @foreach($sucursales as $s)
                                        <option value="{{ $s->id }}" {{ old('sucursal_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="col-12">
                                <div class="p-3 rounded-3 d-flex align-items-start gap-3" style="background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2);">
                                    <div class="form-check form-switch fs-4 m-0">
                                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', true) ? 'checked' : '' }}>
                                    </div>
                                    <div>
                                        <label class="form-check-label fw-bold mb-0" for="activo">Caja activa</label>
                                        <small class="d-block text-muted">Las cajas inactivas no pueden abrir turnos. Puedes cambiar esto después.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('cajas.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                                <i class="bi bi-save me-1"></i>Guardar Caja
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
