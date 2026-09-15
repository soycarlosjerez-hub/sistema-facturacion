@extends('layouts.app')

@section('title', 'Editar Garantía')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Garantía</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-pencil-square me-1"></i>
                        Garantía #{{ $garantia->id }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('garantias.show', $garantia) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

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

    <form method="POST" action="{{ route('garantias.update', $garantia) }}">
        @csrf
        @method('PUT')

        <div class="ui-card mb-4" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-box-seam"></i> Equipo / Orden</div>
            <div class="ui-card-subtitle">Vincula la garantía a un equipo o a una orden de reparación entregada</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Equipo</label>
                        <select name="equipo_id" class="ui-select @error('equipo_id') is-invalid @enderror">
                            <option value="">Seleccionar equipo...</option>
                            @foreach($equipos as $equipo)
                                <option value="{{ $equipo->id }}" {{ old('equipo_id', $garantia->equipo_id) == $equipo->id ? 'selected' : '' }}>
                                    {{ $equipo->serial_imei }} - {{ $equipo->marca }} {{ $equipo->modelo }}
                                </option>
                            @endforeach
                        </select>
                        @error('equipo_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Orden de Reparación</label>
                        <select name="orden_reparacion_id" class="ui-select @error('orden_reparacion_id') is-invalid @enderror">
                            <option value="">Seleccionar orden...</option>
                            @foreach($ordenes as $orden)
                                <option value="{{ $orden->id }}" {{ old('orden_reparacion_id', $garantia->orden_reparacion_id) == $orden->id ? 'selected' : '' }}>
                                    {{ $orden->numero_orden }} - {{ $orden->cliente->nombre ?? 'Sin cliente' }}
                                </option>
                            @endforeach
                        </select>
                        @error('orden_reparacion_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4" style="--delay:.2s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-shield-check"></i> Detalles de la Garantía</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <label class="ui-label">Tipo <span class="text-danger">*</span></label>
                        <select name="tipo" class="ui-select @error('tipo') is-invalid @enderror" required>
                            <option value="">Seleccionar...</option>
                            <option value="reparacion" {{ old('tipo', $garantia->tipo) == 'reparacion' ? 'selected' : '' }}>Reparación</option>
                            <option value="pieza" {{ old('tipo', $garantia->tipo) == 'pieza' ? 'selected' : '' }}>Pieza</option>
                            <option value="servicio" {{ old('tipo', $garantia->tipo) == 'servicio' ? 'selected' : '' }}>Servicio</option>
                            <option value="extendida" {{ old('tipo', $garantia->tipo) == 'extendida' ? 'selected' : '' }}>Extendida</option>
                        </select>
                        @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Fecha de Inicio <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_inicio" class="ui-input @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio', $garantia->fecha_inicio ? $garantia->fecha_inicio->format('Y-m-d') : '') }}" required>
                        @error('fecha_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Fecha de Vencimiento <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_fin" class="ui-input @error('fecha_fin') is-invalid @enderror" value="{{ old('fecha_fin', $garantia->fecha_fin ? $garantia->fecha_fin->format('Y-m-d') : '') }}" required>
                        @error('fecha_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Cobertura (RD$) <span class="text-danger">*</span></label>
                        <input type="number" name="cobertura" class="ui-input @error('cobertura') is-invalid @enderror" step="0.01" min="0" value="{{ old('cobertura', $garantia->cobertura) }}" required>
                        @error('cobertura')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Términos y Condiciones</label>
                        <textarea name="terminos_condiciones" class="ui-input @error('terminos_condiciones') is-invalid @enderror" rows="4" placeholder="Describe la cobertura, exclusiones, condiciones...">{{ old('terminos_condiciones', $garantia->terminos_condiciones) }}</textarea>
                        @error('terminos_condiciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="ui-btn ui-btn-primary rounded-pill px-4">
                <i class="bi bi-check-lg me-1"></i> Actualizar Garantía
            </button>
            <a href="{{ route('garantias.show', $garantia) }}" class="ui-btn ui-btn-secondary rounded-pill px-4">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection