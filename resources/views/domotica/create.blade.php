@extends('layouts.app')

@section('title', 'Nuevo Servicio de Domótica')

@push('styles')
@include('partials.premium-ui')
<style>
.domotica-create-section {
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 1.5rem;
    margin-bottom: 1.5rem;
}
body.dark-mode .domotica-create-section { border-bottom-color: #1e293b; }
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
                    <div class="ui-header-title">Nuevo Servicio de Domótica</div>
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

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <form id="domoticaForm" method="POST" action="{{ route('domotica.store') }}">
            @csrf

            {{-- Información del Cliente --}}
            <div class="ui-card-body pb-4 mb-4 border-bottom">
                <h6 class="fw-bold mb-3" style="color: #0891b2;">
                    <i class="bi bi-person-vcard me-2"></i>Información del Cliente
                </h6>
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

            {{-- Detalles del Servicio --}}
            <div class="ui-card-body pb-4 mb-4 border-bottom">
                <h6 class="fw-bold mb-3" style="color: #7c3aed;">
                    <i class="bi bi-clipboard-check me-2"></i>Detalles del Servicio
                </h6>
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

            {{-- Presupuesto --}}
            <div class="ui-card-body pb-4 mb-4 border-bottom">
                <h6 class="fw-bold mb-3" style="color: #059669;">
                    <i class="bi bi-currency-dollar me-2"></i>Presupuesto
                </h6>
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

            {{-- Notas --}}
            <div class="ui-card-body">
                <h6 class="fw-bold mb-3" style="color: #ca8a04;">
                    <i class="bi bi-journal-text me-2"></i>Notas
                </h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="ui-label">Notas Adicionales</label>
                        <textarea name="notas" class="ui-input" rows="3" placeholder="Observaciones adicionales...">{{ old('notas') }}</textarea>
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
            <span class="fw-semibold d-none d-sm-inline">Creando nuevo servicio de domótica</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('domotica.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="domoticaForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Guardar Servicio
            </button>
        </div>
    </div>
</div>
@endsection
