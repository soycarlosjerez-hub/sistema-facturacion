@extends('layouts.app')

@section('title', 'Editar Repartidor')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#0ea5e9;--accent-rgb:14,165,233;--accent-hover:#0284c7;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-person-gear"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Repartidor</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-pencil me-1"></i>
                        <span>{{ $driver->nombre }} {{ $driver->apellido }}</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('delivery-drivers.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="ui-card mb-5" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <form id="driverForm" method="POST" action="{{ route('delivery-drivers.update', $driver) }}">
            @csrf @method('PUT')
            <div class="ui-card-body">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color:#0ea5e9;">
                        <i class="bi bi-person me-2"></i>Información Personal
                    </h6>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nombre" class="ui-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre" class="ui-input @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $driver->nombre) }}" required maxlength="100" placeholder="Ej: Juan">
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="apellido" class="ui-label">Apellido <span class="text-danger">*</span></label>
                        <input type="text" name="apellido" id="apellido" class="ui-input @error('apellido') is-invalid @enderror"
                               value="{{ old('apellido', $driver->apellido) }}" required maxlength="100" placeholder="Ej: Pérez">
                        @error('apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label for="cedula" class="ui-label">Cédula <span class="text-danger">*</span></label>
                        <input type="text" name="cedula" id="cedula" class="ui-input @error('cedula') is-invalid @enderror"
                               value="{{ old('cedula', $driver->cedula) }}" required maxlength="20" placeholder="Ej: 001-0000000-0">
                        @error('cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="licencia_conducir" class="ui-label">Licencia de Conducir</label>
                        <input type="text" name="licencia_conducir" id="licencia_conducir" class="ui-input @error('licencia_conducir') is-invalid @enderror"
                               value="{{ old('licencia_conducir', $driver->licencia_conducir) }}" maxlength="30" placeholder="Ej: A-II-12345">
                        @error('licencia_conducir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-4 pb-3 border-bottom mt-4">
                    <h6 class="fw-bold mb-0" style="color:#0ea5e9;">
                        <i class="bi bi-telephone me-2"></i>Contacto
                    </h6>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="telefono" class="ui-label">Teléfono <span class="text-danger">*</span></label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="tel" name="telefono" id="telefono" class="ui-input @error('telefono') is-invalid @enderror"
                                   value="{{ old('telefono', $driver->telefono) }}" required maxlength="20" placeholder="Ej: 809-000-0000">
                            @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="whatsapp" class="ui-label">WhatsApp</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text" style="color:#25D366;"><i class="bi bi-whatsapp"></i></span>
                            <input type="tel" name="whatsapp" id="whatsapp" class="ui-input @error('whatsapp') is-invalid @enderror"
                                   value="{{ old('whatsapp', $driver->whatsapp) }}" maxlength="20" placeholder="Ej: 809-000-0000">
                            @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-4 pb-3 border-bottom mt-4">
                    <h6 class="fw-bold mb-0" style="color:#0ea5e9;">
                        <i class="bi bi-journal-text me-2"></i>Observaciones
                    </h6>
                </div>

                <div class="mb-3">
                    <label for="notas" class="ui-label">Notas Internas</label>
                    <textarea name="notas" id="notas" class="ui-textarea @error('notas') is-invalid @enderror"
                              rows="3" maxlength="500" placeholder="Notas internas sobre el repartidor...">{{ old('notas', $driver->notas) }}</textarea>
                    @error('notas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-check form-switch mt-2">
                    <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1" {{ old('activo', $driver->activo) ? 'checked' : '' }}>
                    <label for="activo" class="form-check-label small fw-semibold">Repartidor Activo</label>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('delivery-drivers.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">Cancelar</a>
        <button type="submit" form="driverForm" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
            <i class="bi bi-save me-2"></i>Guardar Cambios
        </button>
    </div>
</div>
@endsection
