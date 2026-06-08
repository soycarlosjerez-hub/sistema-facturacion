@extends('layouts.app')

@section('title', 'Editar Certificado')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('certificados-digitales.index') }}" class="btn btn-light rounded-circle me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Editar Certificado</h3>
                    <p class="text-muted mb-0">{{ $cert->nombre }}</p>
                </div>
            </div>

            <form action="{{ route('certificados-digitales.update', $cert) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Nombre</label>
                                <input type="text" name="nombre" class="form-control rounded-3" value="{{ $cert->nombre }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Entidad Emisora</label>
                                <input type="text" name="emisor_cert" class="form-control rounded-3" value="{{ $cert->emisor_cert }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">RNC Emisor</label>
                                <input type="text" name="rnc_emisor" class="form-control rounded-3" value="{{ $cert->rnc_emisor }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">RNC Titular</label>
                                <input type="text" name="rnc_titular" class="form-control rounded-3" value="{{ $cert->rnc_titular }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Reemplazar Archivo (opcional)</label>
                                <input type="file" name="archivo" class="form-control rounded-3" accept=".p12,.pfx">
                                <small class="text-muted">Solo suba un nuevo archivo si desea reemplazar el actual</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Nueva Contraseña (opcional)</label>
                                <input type="password" name="password" class="form-control rounded-3" placeholder="Solo si la cambió">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Número de Serie</label>
                                <input type="text" name="serial_number" class="form-control rounded-3" value="{{ $cert->serial_number }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Fecha de Emisión</label>
                                <input type="date" name="fecha_emision" class="form-control rounded-3" value="{{ $cert->fecha_emision?->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Fecha de Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" class="form-control rounded-3" value="{{ $cert->fecha_vencimiento->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Notas</label>
                                <textarea name="notas" class="form-control rounded-3" rows="2">{{ $cert->notas }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ $cert->activo ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Activo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0 d-flex gap-2">
                        <a href="{{ route('certificados-digitales.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-save me-1"></i>Actualizar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
