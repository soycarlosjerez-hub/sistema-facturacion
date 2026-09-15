@extends('layouts.app')
@section('title', 'Nueva Configuración de Red')

@push('styles')
@include('partials.premium-ui')
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
                    <div class="ui-header-title">Nueva Configuración de Red</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registra una nueva configuración de red empresarial
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('redes-config.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <form id="redConfigForm" action="{{ route('redes-config.store') }}" method="POST">
                    @csrf

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #0891b2;">
                            <i class="bi bi-person-vcard me-2"></i>Cliente y Nombre
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="cliente_id" class="ui-label">Cliente</label>
                                <select name="cliente_id" id="cliente_id" class="ui-select @error('cliente_id') is-invalid @enderror">
                                    <option value="">-- Seleccionar cliente --</option>
                                    @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nombre }} - {{ $cliente->rnc_cedula }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="nombre_red" class="ui-label">Nombre de la Red <span class="text-danger">*</span></label>
                                <input type="text" name="nombre_red" id="nombre_red" class="ui-input @error('nombre_red') is-invalid @enderror" value="{{ old('nombre_red') }}" required>
                                <small class="text-muted">Nombre descriptivo de la red (ej: Red Oficinas Principales)</small>
                                @error('nombre_red')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #7c3aed;">
                            <i class="bi bi-wifi me-2"></i>Configuración WiFi
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="ssid_wifi" class="ui-label">SSID WiFi</label>
                                <input type="text" name="ssid_wifi" id="ssid_wifi" class="ui-input @error('ssid_wifi') is-invalid @enderror" value="{{ old('ssid_wifi') }}" placeholder="Ej: Empresa-WiFi">
                            </div>
                            <div class="col-md-6">
                                <label for="canal_wifi" class="ui-label">Canal WiFi</label>
                                <input type="text" name="canal_wifi" id="canal_wifi" class="ui-input @error('canal_wifi') is-invalid @enderror" value="{{ old('canal_wifi') }}" placeholder="Ej: 6, 11, Auto">
                            </div>
                        </div>
                    </div>

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #059669;">
                            <i class="bi bi-ethernet me-2"></i>Configuración de Red
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="vlan_id" class="ui-label">VLAN ID</label>
                                <input type="number" name="vlan_id" id="vlan_id" class="ui-input @error('vlan_id') is-invalid @enderror" value="{{ old('vlan_id') }}" min="1" max="4094" placeholder="Ej: 10">
                            </div>
                            <div class="col-md-6">
                                <label for="direccion_red" class="ui-label">Dirección de Red</label>
                                <input type="text" name="direccion_red" id="direccion_red" class="ui-input @error('direccion_red') is-invalid @enderror" value="{{ old('direccion_red') }}" placeholder="Ej: 192.168.1.0/24">
                            </div>
                        </div>
                    </div>

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #ca8a04;">
                            <i class="bi bi-server me-2"></i>DHCP
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="dhcp_activado" class="ui-label">DHCP</label>
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(6,182,212,.05);">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="dhcp_activado" id="dhcp_activado" {{ old('dhcp_activado') ? 'checked' : '' }} role="switch" style="width:3em;height:1.5em;">
                                        <label class="form-check-label fw-semibold ms-2" for="dhcp_activado">DHCP Activado</label>
                                    </div>
                                    <small class="text-muted">Si está desactivado, asigna IPs estáticas.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="dhcp_rango" class="ui-label">Rango DHCP</label>
                                <input type="text" name="dhcp_rango" id="dhcp_rango" class="ui-input @error('dhcp_rango') is-invalid @enderror" value="{{ old('dhcp_rango') }}" placeholder="Ej: 192.168.1.100-200">
                            </div>
                        </div>
                    </div>

                    <div class="ui-card-body">
                        <h6 class="fw-bold mb-3" style="color: #64748b;">
                            <i class="bi bi-journal-text me-2"></i>Notas y Estado
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="cobertura" class="ui-label">Cobertura</label>
                                <textarea name="cobertura" id="cobertura" class="ui-textarea @error('cobertura') is-invalid @enderror" rows="2" placeholder="Descripción del área de cobertura">{{ old('cobertura') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="notas" class="ui-label">Notas</label>
                                <textarea name="notas" id="notas" class="ui-textarea @error('notas') is-invalid @enderror" rows="3" placeholder="Notas adicionales">{{ old('notas') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="activo" class="ui-label">Estado</label>
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(6,182,212,.05);">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="activo" id="activo" {{ old('activo', true) ? 'checked' : '' }} role="switch" style="width:3em;height:1.5em;">
                                        <label class="form-check-label fw-semibold ms-2" for="activo">Configuración Activa</label>
                                    </div>
                                    <small class="text-muted">Si está inactiva no se aplicará.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#06b6d4;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva configuración</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('redes-config.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="redConfigForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Guardar Configuración
            </button>
        </div>
    </div>
</div>
@endsection
