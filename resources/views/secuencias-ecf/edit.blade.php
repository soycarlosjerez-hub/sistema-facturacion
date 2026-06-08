@extends('layouts.app')

@section('title', 'Editar Secuencia e-CF')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('secuencias-ecf.index') }}" class="btn btn-light rounded-circle me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Editar Secuencia {{ $secuencia->tipo_ecf }}</h3>
                    <p class="text-muted mb-0">{{ $secuencia->nombre }}</p>
                </div>
            </div>

            <form action="{{ route('secuencias-ecf.update', $secuencia) }}" method="POST">
                @csrf @method('PUT')
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
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
                    <div class="card-footer bg-transparent border-0 p-4 pt-0 d-flex gap-2">
                        <a href="{{ route('secuencias-ecf.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
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
