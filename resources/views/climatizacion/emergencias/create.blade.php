@extends('layouts.app')

@section('title', 'Nueva Orden de Emergencia')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .ui-page { --accent:#ef4444; --accent-rgb:239,68,68; --accent-hover:#dc2626; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#ef4444;--accent-rgb:239,68,68;--accent-hover:#dc2626;">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="ui-header" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Orden de Emergencia</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>Registrar una nueva emergencia de climatización
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('climatizacion.ordenes-emergencia.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================================
         FORMULARIO
         ============================================================ --}}
    <form action="{{ route('climatizacion.ordenes-emergencia.store') }}" method="POST" id="form-emergencia">
        @csrf

        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title">
                <i class="bi bi-info-circle"></i> Información de la Emergencia
            </div>
            <div class="ui-card-subtitle">Datos generales del reporte de emergencia</div>
            <div class="ui-card-body">
                <div class="row g-3">

                    {{-- Cliente --}}
                    <div class="col-md-6">
                        <label class="ui-label">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" class="ui-select @error('cliente_id') is-invalid @enderror" required>
                            <option value="">Seleccionar cliente...</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre }} {{ $cliente->identificacion ? '('. $cliente->identificacion .')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Prioridad --}}
                    <div class="col-md-3">
                        <label class="ui-label">Prioridad <span class="text-danger">*</span></label>
                        <select name="prioridad" class="ui-select @error('prioridad') is-invalid @enderror" required>
                            <option value="">Seleccionar...</option>
                            @foreach (\App\Models\OrdenEmergencia::PRIORIDADES as $key => $label)
                                <option value="{{ $key }}" {{ old('prioridad') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('prioridad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Tipo Falla --}}
                    <div class="col-md-3">
                        <label class="ui-label">Tipo de Falla <span class="text-danger">*</span></label>
                        <select name="tipo_falla" class="ui-select @error('tipo_falla') is-invalid @enderror" required>
                            <option value="">Seleccionar...</option>
                            @foreach (\App\Models\OrdenEmergencia::TIPOS_FALLA as $key => $label)
                                <option value="{{ $key }}" {{ old('tipo_falla') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tipo_falla') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Dirección --}}
                    <div class="col-md-8">
                        <label class="ui-label">Dirección <span class="text-danger">*</span></label>
                        <input type="text" name="direccion" class="ui-input @error('direccion') is-invalid @enderror"
                               value="{{ old('direccion') }}" placeholder="Dirección del servicio" required>
                        @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Teléfono --}}
                    <div class="col-md-4">
                        <label class="ui-label">Teléfono de Contacto</label>
                        <input type="text" name="contacto_telefono" class="ui-input @error('contacto_telefono') is-invalid @enderror"
                               value="{{ old('contacto_telefono') }}" placeholder="809-000-0000">
                        @error('contacto_telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Descripción --}}
                    <div class="col-12">
                        <label class="ui-label">Descripción <span class="text-danger">*</span></label>
                        <textarea name="descripcion" rows="4" class="ui-textarea @error('descripcion') is-invalid @enderror"
                                  placeholder="Describa el problema reportado..." required>{{ old('descripcion') }}</textarea>
                        @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Técnico (opcional en creación) --}}
                    <div class="col-md-4">
                        <label class="ui-label">Técnico Asignado (opcional)</label>
                        <select name="tecnico_id" class="ui-select @error('tecnico_id') is-invalid @enderror">
                            <option value="">Sin asignar...</option>
                            @foreach ($tecnicos ?? [] as $tecnico)
                                <option value="{{ $tecnico->id }}" {{ old('tecnico_id') == $tecnico->id ? 'selected' : '' }}>
                                    {{ $tecnico->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('tecnico_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted">Puede asignarse después desde la ficha</small>
                    </div>

                    {{-- Costo estimado --}}
                    <div class="col-md-4">
                        <label class="ui-label">Costo Estimado</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">RD$</span>
                            <input type="number" step="0.01" min="0" name="costo_estimado"
                                   class="ui-input @error('costo_estimado') is-invalid @enderror"
                                   value="{{ old('costo_estimado') }}" placeholder="0.00">
                        </div>
                        @error('costo_estimado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- ============================================================
             STICKY BAR
             ============================================================ --}}
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('climatizacion.ordenes-emergencia.index') }}" class="ui-btn ui-btn-ghost rounded-pill">
                    Cancelar
                </a>
                <button type="submit" form="form-emergencia" class="ui-btn ui-btn-solid rounded-pill px-5">
                    <i class="bi bi-check-lg me-2"></i> Guardar
                </button>
            </div>
        </div>

    </form>

    {{-- Spacer para el sticky bar --}}
    <div style="height:80px;"></div>

</div>
@endsection