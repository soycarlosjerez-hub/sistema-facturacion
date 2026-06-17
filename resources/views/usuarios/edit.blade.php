@extends('layouts.app')

@section('title', 'Editar Usuario: ' . $usuario->name)

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


<div class="page-header-gradient d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4"
    style="background:linear-gradient(135deg,#f59e0b,#ea580c);">


    <div class="d-flex align-items-center gap-3">


        <div class="user-avatar">
            {{ strtoupper(substr($nombres[0],0,1)) }}
            {{ strtoupper(substr($nombres[1] ?? '',0,1)) }}
        </div>


        <div>
            <span class="badge bg-white text-dark rounded-pill">
                <i class="bi bi-pencil"></i>
                EDITANDO
            </span>
            <h2 class="fw-bold mb-0">
                {{ $usuario->name }}
            </h2>
            <p class="mb-0">
                {{ $usuario->email }}
            </p>
        </div>
    </div>
    <a href="{{ route('usuarios.index') }}" class="btn btn-light rounded-pill">Volver</a>
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
        {{-- FORMULARIO --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person-vcard"></i> Información Usuario</h5>
                </div>
                <div class="card-body">
                    @include('usuarios._form_fields')
                    {{-- ROLES --}}
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
                    {{-- BUSINESS TYPE --}}
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
            <div class="sticky-save-bar">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2" id="saveBarLeft">
                        <i class="bi bi-info-circle text-primary"></i>
                        <span class="fw-semibold d-none d-sm-inline">Editar Usuario</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" form="editUserForm" class="btn btn-white w-100 rounded-pill fw-bold py-2 shadow"><i class="bi bi-save me-2"></i> Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- LATERAL DERECHO --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px; z-index: 100;">
                <div class="card-body text-center">
                    <div class="user-avatar mx-auto mb-3">{{ strtoupper(substr($usuario->name,0,1)) }}</div>
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

<style>
    .sticky-save-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #fff;
        border-top: 2px solid var(--bs-primary, #0d6efd);
        padding: 0.75rem 1.5rem;
        z-index: 1050;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
        max-width: 100%;
    }

    body.dark-mode .sticky-save-bar {
        background: #0f172a;
        border-top-color: #38bdf8;
    }

    @media (min-width: 992px) {
        .sticky-save-bar {
            left: auto;
            right: 1.5rem;
            max-width: calc(100% - 280px - 3rem);
            margin-left: 280px;
        }
    }

    @media (max-width: 991.98px) {
        .sticky-save-bar {
            left: 0;
            right: 0;
            max-width: 100%;
            margin-left: 0;
        }
    }

    /* Alinear formulario y sidebar */
    #editUserForm+.row,
    .row.g-4.align-items-start {
        align-items: stretch !important;
    }

    .row.g-4.align-items-start>.col-lg-8,
    .row.g-4.align-items-start>.col-lg-4 {
        display: flex;
        flex-direction: column;
    }

    .row.g-4.align-items-start .card {
        height: 100%;
    }
</style>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleCards = document.querySelectorAll('.role-card');
        const businessTypeSection = document.getElementById('businessTypeSection');
        const businessTypeInfo = document.getElementById('businessTypeInfo');
        const businessTypeSelect = document.getElementById('business_type_id');
        const businessTypeHidden = document.querySelector('input[name="business_type_id"][type="hidden"]');
        const roleInputs = document.querySelectorAll('input[name="role"]');

        // Set initial active state based on checked radio
        roleInputs.forEach(input => {
            if (input.checked) {
                input.closest('.role-card').classList.add('active');
            }
        });

        // Handle role card clicks
        roleCards.forEach(card => {
            card.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;

                    // Update visual state
                    roleCards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');

                    // Show/hide business type section
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

        // Also handle radio change directly (for accessibility)
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

        // Update hidden input when select changes
        if (businessTypeSelect) {
            businessTypeSelect.addEventListener('change', function() {
                if (businessTypeHidden) {
                    businessTypeHidden.value = this.value;
                }
            });
        }

        // Initialize business type visibility on load
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