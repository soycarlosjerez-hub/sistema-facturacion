@extends('layouts.app')
@section('title', 'Ver Red de Configuración')

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
                    <h4 class="ui-header-title">{{ $redConfig->nombre_red }}</h4>
                    <div class="ui-header-meta">Detalles de la configuración de red</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('redes-config.edit')
                <a href="{{ route('redes-config.edit', $redConfig) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                @endcan
                <a href="{{ route('redes-config.index') }}" class="ui-btn ui-btn-sm rounded-pill" style="background:rgba(255,255,255,.15);">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Información de Red</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Nombre de la Red</small>
                        <strong>{{ $redConfig->nombre_red }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Cliente</small>
                        <strong>{{ $redConfig->cliente->nombre ?? '-' }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">SSID WiFi</small>
                        <span class="badge bg-info">{{ $redConfig->ssid_wifi ?? 'N/A' }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Canal WiFi</small>
                        <strong>{{ $redConfig->canal_wifi ?? '-' }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">VLAN ID</small>
                        @if($redConfig->vlan_id)
                        <span class="badge bg-success fs-6">VLAN {{ $redConfig->vlan_id }}</span>
                        @else
                        <span class="text-muted">Sin VLAN</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Dirección de Red</small>
                        <code>{{ $redConfig->direccion_red ?? '-' }}</code>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Configuración DHCP</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">DHCP</small>
                        <span class="badge {{ $redConfig->dhcp_activado ? 'bg-success' : 'bg-secondary' }} fs-6">
                            {{ $redConfig->dhcp_activado ? 'Activado' : 'Desactivado' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Rango DHCP</small>
                        <code>{{ $redConfig->dhcp_rango ?? 'N/A' }}</code>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Cobertura</small>
                        <p class="text-muted">{{ $redConfig->cobertura ?? '-' }}</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Notas</small>
                        <p class="text-muted">{{ $redConfig->notas ?? 'Sin notas' }}</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Estado</small>
                        <span class="badge {{ $redConfig->activo ? 'bg-success' : 'bg-secondary' }} fs-6">
                            {{ $redConfig->activo_label }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
