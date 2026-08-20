@extends('layouts.app')

@section('title', 'Nuevo Servicio de Domótica')

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
                    <h4 class="ui-header-title">Nuevo Servicio de Domótica</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registrar una nueva instalación o proyecto de automatización
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('domotica.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
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

    <form method="POST" action="{{ route('domotica.store') }}">
        @csrf

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
                                <option value="{{ $cliente->id }}" {{ old('cliente_id', $clientePreselect) == $cliente->id ? 'selected' : '' }}>
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
                            <option value="camaras_seguridad" {{ old('tipo_servicio') == 'camaras_seguridad' ? 'selected' : '' }}>Cámaras de Seguridad</option>
                            <option value="alarmas" {{ old('tipo_servicio') == 'alarmas' ? 'selected' : '' }}>Alarmas</option>
                            <option value="control_acceso" {{ old('tipo_servicio') == 'control_acceso' ? 'selected' : '' }}>Control de Acceso</option>
                            <option value="redes" {{ old('tipo_servicio') == 'redes' ? 'selected' : '' }}>Redes</option>
                            <option value="automatizacion" {{ old('tipo_servicio') == 'automatizacion' ? 'selected' : '' }}>Automatización</option>
                            <option value="sonido" {{ old('tipo_servicio') == 'sonido' ? 'selected' : '' }}>Sonido</option>
                            <option value="iluminacion" {{ old('tipo_servicio') == 'iluminacion' ? 'selected' : '' }}>Iluminación</option>
                            <option value="otro" {{ old('tipo_servicio') == 'otro' ? 'selected' : '' }}>Otro</option>
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
                        <input type="text" name="titulo" class="ui-input @error('titulo') is-invalid @enderror" value="{{ old('titulo') }}" required placeholder="Ej: Instalación de cámaras en oficina">
                        @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Técnico Asignado</label>
                        <select name="tecnico_id" class="ui-select @error('tecnico_id') is-invalid @enderror">
                            <option value="">Sin asignar</option>
                            @foreach($tecnicos as $tech)
                                <option value="{{ $tech->id }}" {{ old('tecnico_id') == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tecnico_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Descripción</label>
                        <textarea name="descripcion" class="ui-input @error('descripcion') is-invalid @enderror" rows="3" placeholder="Describe el trabajo a realizar...">{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Dirección de Instalación</label>
                        <input type="text" name="direccion_instalacion" class="ui-input @error('direccion_instalacion') is-invalid @enderror" value="{{ old('direccion_instalacion') }}" placeholder="Dirección donde se instalará">
                        @error('direccion_instalacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Fecha Programada</label>
                        <input type="date" name="fecha_programada" class="ui-input @error('fecha_programada') is-invalid @enderror" value="{{ old('fecha_programada') }}">
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
                            <input type="number" name="presupuesto" class="ui-input" step="0.01" min="0" value="{{ old('presupuesto', '0') }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Descuento</label>
                        <div class="input-group">
                            <span class="input-group-text">RD$</span>
                            <input type="number" name="descuento" class="ui-input" step="0.01" min="0" value="{{ old('descuento', '0') }}">
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
                        <textarea name="notas" class="ui-input" rows="3" placeholder="Observaciones adicionales...">{{ old('notas') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="ui-btn ui-btn-primary rounded-pill px-4">
                <i class="bi bi-check-lg me-1"></i> Crear Servicio
            </button>
            <a href="{{ route('domotica.index') }}" class="ui-btn ui-btn-secondary rounded-pill px-4">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection