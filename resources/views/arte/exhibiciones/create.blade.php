@extends('layouts.app')
@section('title', 'Nueva Exhibición')
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
                    <i class="bi bi-plus-circle"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Nueva Exhibición</h2>
                    <p class="text-white text-opacity-75 mb-0">Crea una nueva exhibición y asigna obras</p>
                </div>
            </div>
            <a href="{{ route('arte.exhibiciones.index') }}" class="btn btn-light rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-accent purple"></div>
        <form method="POST" action="{{ route('arte.exhibiciones.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label small fw-bold">Nombre *</label>
                    <input type="text" name="nombre" class="form-control rounded-3 @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Ubicación</label>
                    <input type="text" name="ubicacion" class="form-control rounded-3" value="{{ old('ubicacion') }}" placeholder="Sala principal, ala oeste...">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Fecha inicio *</label>
                    <input type="date" name="fecha_inicio" class="form-control rounded-3 @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio') }}" required>
                    @error('fecha_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Fecha fin</label>
                    <input type="date" name="fecha_fin" class="form-control rounded-3" value="{{ old('fecha_fin') }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label small fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control rounded-3" rows="3">{{ old('descripcion') }}</textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label small fw-bold">Obras a exhibir</label>
                    <div class="border rounded-3 p-3" style="max-height:260px;overflow-y:auto;">
                        @forelse($obrasDisponibles as $obra)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="obra_ids[]" value="{{ $obra->id }}" id="obra{{ $obra->id }}">
                            <label class="form-check-label small" for="obra{{ $obra->id }}">
                                {{ $obra->titulo }} — <span class="text-muted">{{ $obra->artista?->nombre ?? 'Sin artista' }}</span>
                            </label>
                        </div>
                        @empty
                        <div class="text-muted small py-2"><i class="bi bi-info-circle me-1"></i>No hay obras disponibles para exhibir.</div>
                        @endforelse
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-check">
                        <input type="checkbox" name="activa" class="form-check-input" value="1" id="activa" {{ old('activa', true) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="activa">Exhibición activa</label>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2 justify-content-end">
                <a href="{{ route('arte.exhibiciones.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-check-lg me-1"></i>Guardar Exhibición</button>
            </div>
        </form>
    </div>
</div>
@endsection
