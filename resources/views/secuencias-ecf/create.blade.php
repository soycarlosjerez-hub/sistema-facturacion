@extends('layouts.app')

@section('title', 'Nueva Secuencia e-CF')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-123 text-primary me-2"></i>Nueva Secuencia e-CF</h2>
                    <p class="text-muted mb-0">Configure una nueva numeración autorizada por DGII</p>
                </div>
                <a href="{{ route('secuencias-ecf.index') }}" class="btn btn-light rounded-pill"><i class="bi bi-arrow-left me-1"></i> Volver</a>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <form action="{{ route('secuencias-ecf.store') }}" method="POST">
                    @csrf
                    <div class="card-header bg-light border-bottom border-light p-4">
                        <h5 class="mb-0 fw-semibold"><i class="bi bi-123 me-2"></i>Información de la Secuencia</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info border-0 rounded-3 small mb-4">
                            <i class="bi bi-info-circle me-1"></i>
                            Solicite su rango de numeración en el portal de <strong>DGII</strong> → e-CF → Autorización de Secuencia.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Tipo de e-CF</label>
                                <select name="tipo_ecf" class="form-select rounded-3" required>
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
                                <label class="form-label fw-bold small">Nombre Descriptivo</label>
                                <input type="text" name="nombre" class="form-control rounded-3" placeholder="Ej: Facturas Crédito Fiscal 2026" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Descripción (opcional)</label>
                                <input type="text" name="descripcion" class="form-control rounded-3" placeholder="Notas internas sobre esta secuencia">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Desde</label>
                                <input type="number" name="desde" class="form-control rounded-3" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Hasta</label>
                                <input type="number" name="hasta" class="form-control rounded-3" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Actual (Último usado)</label>
                                <input type="number" name="actual" class="form-control rounded-3" min="0" value="0" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Fecha de Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" class="form-control rounded-3" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Estado</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" checked>
                                    <label class="form-check-label" for="activo">Activa</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <a href="{{ route('secuencias-ecf.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-save me-1"></i>Guardar Secuencia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
