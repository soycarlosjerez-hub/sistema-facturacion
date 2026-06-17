@extends('layouts.app')

@section('title', 'Crear Usuario')

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


<div class="page-header-gradient d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4"
    style="background: linear-gradient(135deg, #38bdf8 0%, #6366f1 100%);">
        <div style="position: relative; z-index: 2;">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                    <i class="bi bi-person-plus-fill me-1"></i>NUEVO USUARIO
                </span>
            </div>
            <h2 class="fw-bold mb-1">Crear Usuario</h2>
            <p class="mb-0 opacity-75">Agrega un nuevo miembro al sistema y asigna su nivel de acceso</p>
        </div>
        <a href="{{ route('usuarios.index') }}" class="btn btn-light rounded-pill px-4 fw-bold" style="position: relative; z-index: 2;">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
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

    <form action="{{ route('usuarios.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Columna izquierda: datos del usuario -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-person-vcard text-primary me-2"></i>Información del Usuario</h5>
                    </div>
                    <div class="card-body p-4">
                        @include('usuarios._form_fields')
                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-light rounded-pill px-4">
                                <i class="bi bi-x-lg me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                                <i class="bi bi-check-lg me-1"></i>Crear Usuario
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: selector de rol -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-shield-fill-check text-primary me-2"></i>Asignar Rol</h5>
                        <small class="text-muted">Selecciona el nivel de acceso del usuario</small>
                    </div>
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

                <!-- Preview de permisos del rol seleccionado -->
                <div class="card border-0 shadow-sm rounded-4 mt-3" id="permPreviewCard">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h6 class="fw-bold mb-0"><i class="bi bi-key text-primary me-2"></i>Vista previa de permisos</h6>
                        <small class="text-muted">Estos son los accesos que tendrá el usuario</small>
                    </div>
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

    // Password strength meter
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
