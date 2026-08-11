@extends('layouts.app')
@section('title', 'Editar Exhibición')
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Editar Exhibición</h2>
                    <p class="text-white text-opacity-75 mb-0">{{ $exhibicion->nombre }}</p>
                </div>
            </div>
            <a href="{{ route('arte.exhibiciones.index') }}" class="btn btn-light rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-accent purple"></div>
        <form method="POST" action="{{ route('arte.exhibiciones.update', $exhibicion) }}">
            @csrf @method('PUT')
            @php $selected = $exhibicion->obras->pluck('id')->toArray(); @endphp
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label small fw-bold">Nombre *</label>
                    <input type="text" name="nombre" class="form-control rounded-3 @error('nombre') is-invalid @enderror" value="{{ old('nombre', $exhibicion->nombre) }}" required>
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Ubicación</label>
                    <input type="text" name="ubicacion" class="form-control rounded-3" value="{{ old('ubicacion', $exhibicion->ubicacion) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Fecha inicio *</label>
                    <input type="date" name="fecha_inicio" class="form-control rounded-3 @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio', optional($exhibicion->fecha_inicio)->format('Y-m-d')) }}" required>
                    @error('fecha_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Fecha fin</label>
                    <input type="date" name="fecha_fin" class="form-control rounded-3" value="{{ old('fecha_fin', optional($exhibicion->fecha_fin)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label small fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control rounded-3" rows="3">{{ old('descripcion', $exhibicion->descripcion) }}</textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label small fw-bold">Obras a exhibir</label>
                    <div class="border rounded-3 p-3" style="max-height:260px;overflow-y:auto;">
                        @foreach($obrasDisponibles as $obra)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="obra_ids[]" value="{{ $obra->id }}" id="obra{{ $obra->id }}" {{ in_array($obra->id, $selected) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="obra{{ $obra->id }}">
                                {{ $obra->titulo }} — <span class="text-muted">{{ $obra->artista?->nombre ?? 'Sin artista' }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-check">
                        <input type="checkbox" name="activa" class="form-check-input" value="1" id="activa" {{ old('activa', $exhibicion->activa) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="activa">Exhibición activa</label>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2 justify-content-end">
                <a href="{{ route('arte.exhibiciones.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-check-lg me-1"></i>Actualizar Exhibición</button>
            </div>
        </form>
    </div>
</div>
@endsection
