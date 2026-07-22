@extends('layouts.app')

@section('title', 'Crear Usuario')

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
</style>
@endpush

@section('content')
@php
    $rolSeleccionado = old('role', 'vendedor');
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

<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb">

    <div class="premium-header-amber mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative; z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="ui-avatar-circle">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-person-plus-fill me-1"></i>NUEVO USUARIO
                    </span>
                    <h4 class="fw-bold mb-1 text-white">Crear Usuario</h4>
                    <small class="text-white opacity-75">Agrega un nuevo miembro al sistema y asigna su nivel de acceso</small>
                </div>
            </div>
            <a href="{{ route('usuarios.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
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

    <form action="{{ route('usuarios.store') }}" method="POST" id="userForm">
        @csrf
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="ui-card h-100" style="--delay:.1s">
                    <div class="ui-card-accent"></div>
                    <div class="premium-card-title"><i class="bi bi-person-vcard icon-amber"></i> Información del Usuario</div>
                    <div class="card-body p-4">
                        @include('usuarios._form_fields')
                    </div>

                </div>
            </div>

            <div class="col-lg-5">
                <div class="ui-card h-100" style="--delay:.2s">
                    <div class="ui-card-accent"></div>
                    <div class="premium-card-title"><i class="bi bi-shield-fill-check icon-amber"></i> Asignar Rol</div>
                    <div class="premium-card-subtitle">Selecciona el nivel de acceso del usuario</div>
                    <div class="card-body p-4">
                        <div class="role-picker" id="rolePicker">
                            @foreach($roles as $rol)
                                @php
                                    $cfg = $rolConfig[$rol->name] ?? $defaultConfig;
                                    $cfg['label'] = $cfg['label'] ?? ucfirst(str_replace(['-', '_'], ' ', $rol->name));
                                    $cfg['desc'] = $cfg['desc'] ?? 'Rol personalizado.';
                                    $cfg['color'] = $cfg['color'] ?? '#64748b';
                                    $cfg['gradient'] = $cfg['gradient'] ?? 'linear-gradient(135deg,#64748b,#475569)';
                                    $cfg['icon'] = $cfg['icon'] ?? 'bi-person';
                                @endphp
                                <label class="role-card {{ $rolSeleccionado == $rol->name ? 'active' : '' }}"
                                       style="--role-color: {{ $cfg['color'] }}; --role-gradient: {{ $cfg['gradient'] }};">
                                    <input type="radio" name="role" value="{{ $rol->name }}"
                                           {{ $rolSeleccionado == $rol->name ? 'checked' : '' }} required>
                                    <div class="role-icon" style="background: {{ $cfg['gradient'] }};">
                                        <i class="bi {{ $cfg['icon'] }}"></i>
                                    </div>
                                    <div class="role-name">{{ $cfg['label'] }}</div>
                                    <div class="role-desc">{{ $cfg['desc'] }}</div>
                                    <div class="role-perms"><i class="bi bi-key"></i> {{ $rol->permissions->count() }} permisos</div>
                                </label>
                            @endforeach
                        </div>
                        @error('role')<div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="ui-card mt-3" id="permPreviewCard" style="--delay:.3s">
                    <div class="ui-card-accent"></div>
                    <div class="premium-card-title"><i class="bi bi-key icon-amber"></i> Vista previa de permisos</div>
                    <div class="premium-card-subtitle">Estos son los accesos que tendrá el usuario</div>
                    <div class="card-body p-4">
                        <div class="permission-preview" id="permPreview">
                            @foreach($roles as $rol)
                                @php
                                    $grouped = $rol->permissions->groupBy(function($p) { return explode('.', $p->name)[0]; });
                                @endphp
                                <div class="perm-block" data-role="{{ $rol->name }}" style="display: {{ $rolSeleccionado == $rol->name ? 'block' : 'none' }};">
                                    @forelse($grouped as $modulo => $perms)
                                        <div class="perm-group">
                                            <div class="perm-group-title">{{ ucfirst($modulo) }}</div>
                                            <div>
                                                @foreach($perms->take(8) as $p)
                                                    <span class="perm-tag"><i class="bi bi-check2"></i> {{ str_replace($modulo.'.', '', $p->name) }}</span>
                                                @endforeach
                                                @if($perms->count() > 8)
                                                    <span class="perm-tag" style="background: rgba(15,23,42,0.06); color: #64748b;">+{{ $perms->count() - 8 }} más</span>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-muted small">Este rol no tiene permisos asignados.</div>
                                    @endforelse
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="premium-sticky-bar">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-info-circle" style="color:#f59e0b;"></i>
                <span class="fw-semibold d-none d-sm-inline">Crear Usuario</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('usuarios.index') }}" class="btn-cancel">Cancelar</a>
                <button type="submit" form="userForm" class="btn-save"><i class="bi bi-save me-2"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.role-card input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.role-card').forEach(c => c.classList.remove('active'));
            radio.closest('.role-card').classList.add('active');
            document.querySelectorAll('.perm-block').forEach(b => b.style.display = 'none');
            const target = document.querySelector(`.perm-block[data-role="${radio.value}"]`);
            if (target) target.style.display = 'block';
        });
    });

    const passInput = document.getElementById('password');
    const strengthBar = document.getElementById('strengthBar');
    if (passInput && strengthBar) {
        passInput.addEventListener('input', () => {
            const v = passInput.value;
            let score = 0;
            if (v.length >= 6) score += 25;
            if (v.length >= 10) score += 15;
            if (/[A-Z]/.test(v)) score += 20;
            if (/[0-9]/.test(v)) score += 20;
            if (/[^A-Za-z0-9]/.test(v)) score += 20;
            strengthBar.style.width = score + '%';
            strengthBar.style.background = score < 40 ? '#ef4444' : score < 70 ? '#f59e0b' : '#22c55e';
        });
    }
</script>
@endsection