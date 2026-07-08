@extends('layouts.app')

@section('title', 'Nuevo Artista')

@section('content')
<div class="container-fluid px-4">
    <a href="{{ route('tattoo.artistas.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill mb-3">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-transparent pt-4 px-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-person-plus me-2"></i>Nuevo Artista / Tatuador</h4>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('tattoo.artistas.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_completo" class="form-control rounded-3" required value="{{ old('nombre_completo') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Usuario del Sistema</label>
                        <select name="user_id" class="form-select rounded-3">
                            <option value="">— No asociado —</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Tipo <span class="text-danger">*</span></label>
                        <select name="tipo" class="form-select rounded-3" required>
                            <option value="empleado" {{ old('tipo') === 'empleado' ? 'selected' : '' }}>Empleado</option>
                            <option value="externo" {{ old('tipo') === 'externo' ? 'selected' : '' }}>Externo</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Especialidad</label>
                        <input type="text" name="especialidad" class="form-control rounded-3" value="{{ old('especialidad') }}" placeholder="Ej: Realismo, Tradicional, Blackwork...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Experiencia (años)</label>
                        <input type="number" name="experiencia_anos" class="form-control rounded-3" min="0" max="99" value="{{ old('experiencia_anos', 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Comisión (%) <span class="text-danger">*</span></label>
                        <div class="input-group rounded-3">
                            <input type="number" name="comision_pct" class="form-control" min="0" max="100" step="0.01" required value="{{ old('comision_pct', 30) }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Foto (URL)</label>
                        <input type="text" name="foto_perfil" class="form-control rounded-3" value="{{ old('foto_perfil') }}" placeholder="https://...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Teléfono</label>
                        <input type="text" name="telefono" class="form-control rounded-3" value="{{ old('telefono') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control rounded-3" value="{{ old('whatsapp') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Instagram</label>
                        <div class="input-group rounded-3">
                            <span class="input-group-text">@</span>
                            <input type="text" name="instagram" class="form-control" value="{{ old('instagram') }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Biografía</label>
                        <textarea name="biografia" class="form-control rounded-3" rows="3" maxlength="1000">{{ old('biografia') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Notas</label>
                        <textarea name="notas" class="form-control rounded-3" rows="2" maxlength="500">{{ old('notas') }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="activo" class="form-check-input" id="activo" value="1" {{ old('activo', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Guardar Artista</button>
                    <a href="{{ route('tattoo.artistas.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
