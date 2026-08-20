@extends('layouts.app')
@section('title', 'Editar Red de Configuración')

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
                    <h4 class="ui-header-title">Editar Red: {{ $redConfig->nombre_red }}</h4>
                    <div class="ui-header-meta">Actualiza la configuración de red empresarial</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('redes-config.update', $redConfig) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="cliente_id" class="form-label fw-bold">Cliente</label>
                            <select name="cliente_id" id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror">
                                <option value="">-- Seleccionar cliente --</option>
                                @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id', $redConfig->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre }} - {{ $cliente->rnc_cedula }}
                                </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nombre_red" class="form-label fw-bold">Nombre de la Red *</label>
                            <input type="text" name="nombre_red" id="nombre_red" class="form-control @error('nombre_red') is-invalid @enderror" value="{{ old('nombre_red', $redConfig->nombre_red) }}" required>
                            @error('nombre_red')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="ssid_wifi" class="form-label fw-bold">SSID WiFi</label>
                                <input type="text" name="ssid_wifi" id="ssid_wifi" class="form-control @error('ssid_wifi') is-invalid @enderror" value="{{ old('ssid_wifi', $redConfig->ssid_wifi) }}" placeholder="Ej: Empresa-WiFi">
                            </div>
                            <div class="col-md-6">
                                <label for="canal_wifi" class="form-label fw-bold">Canal WiFi</label>
                                <input type="text" name="canal_wifi" id="canal_wifi" class="form-control @error('canal_wifi') is-invalid @enderror" value="{{ old('canal_wifi', $redConfig->canal_wifi) }}" placeholder="Ej: 6, 11, Auto">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="vlan_id" class="form-label fw-bold">VLAN ID</label>
                                <input type="number" name="vlan_id" id="vlan_id" class="form-control @error('vlan_id') is-invalid @enderror" value="{{ old('vlan_id', $redConfig->vlan_id) }}" min="1" max="4094" placeholder="Ej: 10">
                            </div>
                            <div class="col-md-6">
                                <label for="direccion_red" class="form-label fw-bold">Dirección de Red</label>
                                <input type="text" name="direccion_red" id="direccion_red" class="form-control @error('direccion_red') is-invalid @enderror" value="{{ old('direccion_red', $redConfig->direccion_red) }}" placeholder="Ej: 192.168.1.0/24">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="dhcp_activado" class="form-label fw-bold">DHCP</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="dhcp_activado" id="dhcp_activado" {{ old('dhcp_activado', $redConfig->dhcp_activado) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="dhcp_activado">DHCP Activado</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="dhcp_rango" class="form-label fw-bold">Rango DHCP</label>
                                <input type="text" name="dhcp_rango" id="dhcp_rango" class="form-control @error('dhcp_rango') is-invalid @enderror" value="{{ old('dhcp_rango', $redConfig->dhcp_rango) }}" placeholder="Ej: 192.168.1.100-200">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="cobertura" class="form-label fw-bold">Cobertura</label>
                            <textarea name="cobertura" id="cobertura" class="form-control @error('cobertura') is-invalid @enderror" rows="2">{{ old('cobertura', $redConfig->cobertura) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="notas" class="form-label fw-bold">Notas</label>
                            <textarea name="notas" id="notas" class="form-control @error('notas') is-invalid @enderror" rows="3">{{ old('notas', $redConfig->notas) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activo" id="activo" {{ old('activo', $redConfig->activo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">Configuración Activa</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Actualizar Configuración
                            </button>
                            <a href="{{ route('redes-config.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
