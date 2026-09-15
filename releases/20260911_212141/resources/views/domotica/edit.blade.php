@extends('layouts.app')

@section('title', 'Editar Servicio de Domótica')

@push('styles')
@include('partials.premium-ui')
<style>
.form-section-title {
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 1rem;
    padding-bottom: .5rem;
    border-bottom: 1px solid #e2e8f0;
}
body.dark-mode .form-section-title { color: #94a3b8; border-bottom-color: #1e293b; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-hdd-network"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Servicio</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-pencil-square me-1"></i>
                        {{ $servicio->numero_proyecto }} · {{ $servicio->titulo }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('domotica.show', $servicio) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
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

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert rounded-4 shadow-sm border-0 mb-4" style="background:rgba(6,182,212,.05);border-left:4px solid #06b6d4 !important;">
        <div class="d-flex align-items-center">
            <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;color:#06b6d4;background:rgba(6,182,212,.1);">
                <i class="bi bi-info-circle fs-5"></i>
            </div>
            <div>
                <span class="text-muted">Editando el servicio:</span>
                <strong class="d-block" style="font-size:1.1rem;color:#1e293b;">{{ $servicio->numero_proyecto }} · {{ $servicio->titulo }}</strong>
            </div>
        </div>
    </div>

    <form id="domoticaForm" method="POST" action="{{ route('domotica.update', $servicio) }}">
        @csrf
        @method('PUT')

        <div class="ui-card mb-4" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-person-vcard"></i> Información del Cliente</div>
            <div class="ui-card-subtitle">Selecciona el cliente del servicio</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" class="ui-select @error('cliente_id') is-invalid @enderror" required>
                            <option value="">Seleccionar cliente...</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id', $servicio->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre }} - {{ $cliente->rnc_cedula }}
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Tipo de Servicio <span class="text-danger">*</span></label>
                        <select name="tipo_servicio" class="ui-select @error('tipo_servicio') is-invalid @enderror" required>
                            <option value="">Seleccionar...</option>
                            <option value="camaras_seguridad" {{ old('tipo_servicio', $servicio->tipo_servicio) == 'camaras_seguridad' ? 'selected' : '' }}>Cámaras de Seguridad</option>
                            <option value="alarmas" {{ old('tipo_servicio', $servicio->tipo_servicio) == 'alarmas' ? 'selected' : '' }}>Alarmas</option>
                            <option value="control_acceso" {{ old('tipo_servicio', $servicio->tipo_servicio) == 'control_acceso' ? 'selected' : '' }}>Control de Acceso</option>
                            <option value="redes" {{ old('tipo_servicio', $servicio->tipo_servicio) == 'redes' ? 'selected' : '' }}>Redes</option>
                            <option value="automatizacion" {{ old('tipo_servicio', $servicio->tipo_servicio) == 'automatizacion' ? 'selected' : '' }}>Automatización</option>
                            <option value="sonido" {{ old('tipo_servicio', $servicio->tipo_servicio) == 'sonido' ? 'selected' : '' }}>Sonido</option>
                            <option value="iluminacion" {{ old('tipo_servicio', $servicio->tipo_servicio) == 'iluminacion' ? 'selected' : '' }}>Iluminación</option>
                            <option value="otro" {{ old('tipo_servicio', $servicio->tipo_servicio) == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('tipo_servicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4" style="--delay:.2s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-clipboard-check"></i> Detalles del Servicio</div>
            <div class="ui-card-subtitle">Describe el proyecto y la instalación</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="ui-input @error('titulo') is-invalid @enderror" value="{{ old('titulo', $servicio->titulo) }}" required>
                        @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Técnico Asignado</label>
                        <select name="tecnico_id" class="ui-select @error('tecnico_id') is-invalid @enderror">
                            <option value="">Sin asignar</option>
                            @foreach($tecnicos as $tech)
                                <option value="{{ $tech->id }}" {{ old('tecnico_id', $servicio->equipo_asignado_id) == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tecnico_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Descripción</label>
                        <textarea name="descripcion" class="ui-input @error('descripcion') is-invalid @enderror" rows="3">{{ old('descripcion', $servicio->descripcion) }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Dirección de Instalación</label>
                        <input type="text" name="direccion_instalacion" class="ui-input @error('direccion_instalacion') is-invalid @enderror" value="{{ old('direccion_instalacion', $servicio->direccion_instalacion) }}">
                        @error('direccion_instalacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Fecha Programada</label>
                        <input type="date" name="fecha_programada" class="ui-input @error('fecha_programada') is-invalid @enderror" value="{{ old('fecha_programada', $servicio->fecha_programada ? $servicio->fecha_programada->format('Y-m-d') : '') }}">
                        @error('fecha_programada')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4" style="--delay:.3s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-currency-dollar"></i> Presupuesto</div>
            <div class="ui-card-subtitle">Montos del servicio</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Presupuesto</label>
                        <div class="input-group">
                            <span class="input-group-text">RD$</span>
                            <input type="number" name="presupuesto" class="ui-input" step="0.01" min="0" value="{{ old('presupuesto', $servicio->presupuesto) }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Descuento</label>
                        <div class="input-group">
                            <span class="input-group-text">RD$</span>
                            <input type="number" name="descuento" class="ui-input" step="0.01" min="0" value="{{ old('descuento', $servicio->descuento) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4" style="--delay:.4s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-journal-text"></i> Notas</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="ui-label">Notas Adicionales</label>
                        <textarea name="notas" class="ui-input" rows="3">{{ old('notas', $servicio->notas) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#06b6d4;"></i>
            <span class="fw-semibold d-none d-sm-inline">Editando servicio: {{ $servicio->numero_proyecto }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('domotica.show', $servicio) }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="domoticaForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>
@endsection