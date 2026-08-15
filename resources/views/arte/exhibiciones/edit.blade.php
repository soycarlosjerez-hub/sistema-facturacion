@extends('layouts.app')
@section('title', 'Editar Exhibición')
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-pencil-square me-1"></i>EDITANDO
                    </span>
                    <h2 class="fw-bold mb-0 text-white">Editar Exhibición</h2>
                    <p class="mb-0 opacity-75">{{ $exhibicion->nombre }}</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('arte.exhibiciones.index') }}" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body">
            <form method="POST" action="{{ route('arte.exhibiciones.update', $exhibicion) }}" id="exhibicionForm">
                @csrf @method('PUT')
                @php $selected = $exhibicion->obras->pluck('id')->toArray(); @endphp
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="ui-label" for="nombre">Nombre *</label>
                        <input type="text" name="nombre" id="nombre" class="ui-input @error('nombre') is-invalid @enderror" value="{{ old('nombre', $exhibicion->nombre) }}" required>
                        @error('nombre') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="ubicacion">Ubicación</label>
                        <input type="text" name="ubicacion" id="ubicacion" class="ui-input" value="{{ old('ubicacion', $exhibicion->ubicacion) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label" for="fecha_inicio">Fecha inicio *</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="ui-input @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio', optional($exhibicion->fecha_inicio)->format('Y-m-d')) }}" required>
                        @error('fecha_inicio') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label" for="fecha_fin">Fecha fin</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" class="ui-input" value="{{ old('fecha_fin', optional($exhibicion->fecha_fin)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-12">
                        <label class="ui-label" for="descripcion">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="ui-textarea" rows="3">{{ old('descripcion', $exhibicion->descripcion) }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="ui-label">Obras a exhibir</label>
                        <div class="border rounded-4 p-3" style="max-height:260px;overflow-y:auto;">
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
                            <label class="form-check-label small fw-semibold" for="activa">Exhibición activa</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:var(--accent,#8b5cf6)"></i>
            <span class="fw-semibold d-none d-sm-inline">Editando Exhibición: {{ $exhibicion->nombre }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('arte.exhibiciones.index') }}" class="ui-btn ui-btn-ghost btn-sm">
                <i class="bi bi-x-lg me-1"></i>Cancelar
            </a>
            <button type="submit" form="exhibicionForm" class="ui-btn ui-btn-solid rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-save me-1"></i>Actualizar Exhibición
            </button>
        </div>
    </div>
</div>
</div>
@endsection