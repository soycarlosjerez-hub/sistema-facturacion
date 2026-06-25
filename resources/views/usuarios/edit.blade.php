@extends('layouts.app')

@section('title', 'Editar Usuario: ' . $usuario->name)

@push('styles')
@include('partials.premium-ui')
<style>
    .premium-header-amber {
        background: linear-gradient(135deg, #f59e0b, #f97316, #f59e0b, #d97706);
        background-size: 300% 300%;
        animation: premiumGradientShift 6s ease infinite;
        border-radius: 1.2rem;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(245,158,11,.25);
    }
    .premium-header-amber::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background:
            radial-gradient(circle at 30% 40%, rgba(255,255,255,.1) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.07) 0%, transparent 50%);
        pointer-events: none;
    }
    .premium-header-amber .bubble {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
        pointer-events: none;
    }
    .premium-header-amber .bubble:nth-child(1) {
        width: 80px; height: 80px; top: -20px; right: 10%;
        animation: premiumFloat 4s ease-in-out infinite;
    }
    .premium-header-amber .bubble:nth-child(2) {
        width: 50px; height: 50px; bottom: 10px; right: 28%;
        animation: premiumFloat 5s ease-in-out infinite 1s;
    }
    .premium-header-amber .bubble:nth-child(3) {
        width: 100px; height: 100px; bottom: -30px; right: 5%;
        animation: premiumFloat 6s ease-in-out infinite .5s;
    }
    .user-avatar {
        width: 72px; height: 72px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 2rem; font-weight: 800; flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    #editUserForm+.row, .row.g-4.align-items-start { align-items: stretch !important; }
    .row.g-4.align-items-start>.col-lg-8, .row.g-4.align-items-start>.col-lg-4 { display: flex; flex-direction: column; }
    .row.g-4.align-items-start .card { height: 100%; }
</style>
@endpush

@section('content')

@php
    $rolActual = $usuario->roles->pluck('name')->first() ?? old('role','vendedor');
    $nombres = explode(' ',trim($usuario->name));
    $defaultConfig = [
        'color' => '#64748b',
        'gradient' => 'linear-gradient(135deg,#64748b,#475569)',
        'icon' => 'bi-person',
        'label' => 'Rol',
        'desc' => 'Rol personalizado.'
    ];
@endphp

@include('usuarios._rol_config')
@include('usuarios._styles')

<div class="premium-page">

    <div class="premium-header-amber mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative; z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="user-avatar" style="background:rgba(255,255,255,.2); backdrop-filter:blur(8px); border:2px solid rgba(255,255,255,.35);">
                    {{ strtoupper(substr($nombres[0],0,1)) }}{{ strtoupper(substr($nombres[1] ?? '',0,1)) }}
                </div>
                <div>
                    <span class="badge bg-white text-dark rounded-pill">
                        <i class="bi bi-pencil"></i> EDITANDO
                    </span>
                    <h2 class="fw-bold mb-0">{{ $usuario->name }}</h2>
                    <p class="mb-0 opacity-75">{{ $usuario->email }}</p>
                </div>
            </div>
            <a href="{{ route('usuarios.index') }}" class="btn btn-light rounded-pill fw-bold">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
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

    <form id="editUserForm" action="{{ route('usuarios.update',$usuario->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="business_type_id" value="{{ $usuario->business_type_id ?? '' }}">

        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <div class="premium-card h-100">
                    <div class="card-accent amber"></div>
                    <div class="premium-card-title"><i class="bi bi-person-vcard icon-amber"></i> Información Usuario</div>
                    <div class="card-body p-4">
                        @include('usuarios._form_fields')
                        <div class="role-picker mt-4">
                            @foreach($roles as $rol)
                                @php
                                    $cfg = $rolConfig[$rol->name] ?? $defaultConfig;
                                    $cfg['label'] = $cfg['label'] ?? ucfirst(str_replace(['-', '_'], ' ', $rol->name));
                                    $cfg['desc'] = $cfg['desc'] ?? 'Rol personalizado.';
                                    $cfg['color'] = $cfg['color'] ?? '#64748b';
                                    $cfg['gradient'] = $cfg['gradient'] ?? 'linear-gradient(135deg,#64748b,#475569)';
                                    $cfg['icon'] = $cfg['icon'] ?? 'bi-person';
                                @endphp
                                <label class="role-card" style="--role-color:{{ $cfg['color'] }};--role-gradient:{{ $cfg['gradient'] }};">
                                    <input type="radio" name="role" value="{{ $rol->name }}" class="d-none" {{ $rolActual==$rol->name?'checked':'' }} required>
                                    <div class="role-icon" style="background:{{ $cfg['gradient'] }}"><i class="bi {{ $cfg['icon'] }}"></i></div>
                                    <div class="role-name">{{ $cfg['label'] }}</div>
                                    <div class="role-desc">{{ $cfg['desc'] }}</div>
                                    <div class="role-perms"><i class="bi bi-key"></i> {{ $rol->permissions->count() }} permisos</div>
                                </label>
                            @endforeach
                        </div>
                        @error('role')<div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                        <div id="businessTypeSection" class="mt-4" style="display:none;">
                            <label class="form-label fw-bold">Tipo de Negocio</label>
                            <select name="business_type_id" id="business_type_id" class="form-select">
                                <option value="">Seleccione</option>
                                @foreach($businessTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('business_type_id',$usuario->business_type_id)==$type->id?'selected':'' }}>{{ $type->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-info mt-3" id="businessTypeInfo" style="display:none">
                            <i class="bi bi-info-circle"></i> Administrador business solo puede gestionar este negocio.
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="premium-card sticky-top" style="top: 100px; z-index: 100;">
                    <div class="card-accent amber"></div>
                    <div class="card-body text-center">
                        <div class="user-avatar mx-auto mb-3" style="background:{{ $cfg['gradient'] ?? 'linear-gradient(135deg,#64748b,#475569)' }}; width:72px; height:72px; border-radius:50; font-size:2rem;">
                            {{ strtoupper(substr($usuario->name,0,1)) }}
                        </div>
                        <h5 class="fw-bold">{{ $usuario->name }}</h5>
                        <p class="text-muted">{{ $usuario->email }}</p>
                        @if($usuario->roles->count())
                            @php
                                $cfg = $rolConfig[$rolActual] ?? $defaultConfig;
                            @endphp
                            <span class="badge rounded-pill" style="background:{{ $cfg['color'] }}">{{ $cfg['label'] }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="premium-sticky-bar">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2" id="saveBarLeft">
                <i class="bi bi-info-circle text-primary"></i>
                <span class="fw-semibold d-none d-sm-inline">Editar Usuario</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('usuarios.index') }}" class="btn btn-cancel">Cancelar</a>
                <button type="submit" form="editUserForm" class="btn btn-save"><i class="bi bi-save me-2"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleCards = document.querySelectorAll('.role-card');
        const businessTypeSection = document.getElementById('businessTypeSection');
        const businessTypeInfo = document.getElementById('businessTypeInfo');
        const businessTypeSelect = document.getElementById('business_type_id');
        const businessTypeHidden = document.querySelector('input[name="business_type_id"][type="hidden"]');
        const roleInputs = document.querySelectorAll('input[name="role"]');

        roleInputs.forEach(input => {
            if (input.checked) {
                input.closest('.role-card').classList.add('active');
            }
        });

        roleCards.forEach(card => {
            card.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    roleCards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    const selectedRole = radio.value;
                    if (selectedRole === 'admin-business') {
                        businessTypeSection.style.display = 'block';
                        businessTypeInfo.style.display = 'block';
                        businessTypeSelect.required = true;
                        if (businessTypeHidden) businessTypeHidden.disabled = true;
                    } else {
                        businessTypeSection.style.display = 'none';
                        businessTypeInfo.style.display = 'none';
                        businessTypeSelect.required = false;
                        businessTypeSelect.value = '';
                        if (businessTypeHidden) businessTypeHidden.disabled = false;
                    }
                }
            });
        });

        roleInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.checked) {
                    roleCards.forEach(c => c.classList.remove('active'));
                    this.closest('.role-card').classList.add('active');
                    const selectedRole = this.value;
                    if (selectedRole === 'admin-business') {
                        businessTypeSection.style.display = 'block';
                        businessTypeInfo.style.display = 'block';
                        businessTypeSelect.required = true;
                        if (businessTypeHidden) businessTypeHidden.disabled = true;
                    } else {
                        businessTypeSection.style.display = 'none';
                        businessTypeInfo.style.display = 'none';
                        businessTypeSelect.required = false;
                        businessTypeSelect.value = '';
                        if (businessTypeHidden) businessTypeHidden.disabled = false;
                    }
                }
            });
        });

        if (businessTypeSelect) {
            businessTypeSelect.addEventListener('change', function() {
                if (businessTypeHidden) {
                    businessTypeHidden.value = this.value;
                }
            });
        }

        const initialRole = document.querySelector('input[name="role"]:checked');
        if (initialRole && initialRole.value === 'admin-business') {
            businessTypeSection.style.display = 'block';
            businessTypeInfo.style.display = 'block';
            businessTypeSelect.required = true;
            if (businessTypeHidden) businessTypeHidden.disabled = true;
        } else if (businessTypeHidden) {
            businessTypeHidden.disabled = false;
        }
    });
</script>
@endsection