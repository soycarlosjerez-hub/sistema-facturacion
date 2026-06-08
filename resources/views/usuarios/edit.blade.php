@extends('layouts.app')

@section('title', 'Editar Usuario: ' . $usuario->name)

@section('content')
@php
    $rolConfig = [
        'admin'    => ['color' => '#ef4444', 'gradient' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', 'icon' => 'bi-shield-lock-fill',  'label' => 'Admin',    'desc' => 'Acceso total al sistema.'],
        'gerente'  => ['color' => '#f59e0b', 'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'icon' => 'bi-person-badge-fill', 'label' => 'Gerente',  'desc' => 'Gestión operativa, sin admin.'],
        'vendedor' => ['color' => '#38bdf8', 'gradient' => 'linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%)', 'icon' => 'bi-cart-check-fill',  'label' => 'Vendedor', 'desc' => 'POS, ventas y caja.'],
        'almacen'  => ['color' => '#22c55e', 'gradient' => 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)', 'icon' => 'bi-box-seam-fill',     'label' => 'Almacén',  'desc' => 'Productos, compras, stock.'],
        'contador' => ['color' => '#6366f1', 'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', 'icon' => 'bi-calculator-fill',   'label' => 'Contador', 'desc' => 'Reportes y consulta fiscal.'],
    ];
    $rolActual = $usuario->roles->pluck('name')->first() ?? old('role', 'vendedor');
    $cfgActual = $rolConfig[$rolActual] ?? null;
@endphp

@include('usuarios._styles')

<div class="container-fluid px-4">
    <!-- Header gradiente (warning para edición) -->
    <div class="page-header-gradient d-flex justify-content-between align-items-center flex-wrap gap-3" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); box-shadow: 0 10px 30px rgba(245,158,11,0.25);">
        <div class="d-flex align-items-center gap-3" style="position: relative; z-index: 2;">
            <div class="user-avatar" style="background: rgba(255,255,255,0.25); backdrop-filter: blur(10px);">
                {{ strtoupper(substr($usuario->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $usuario->name)[1] ?? '', 0, 1)) }}
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-pencil-square me-1"></i>EDITANDO
                    </span>
                </div>
                <h2 class="fw-bold mb-0">{{ $usuario->name }}</h2>
                <p class="mb-0 opacity-75 small">{{ $usuario->email }}</p>
            </div>
        </div>
        <a href="{{ route('usuarios.index') }}" class="btn btn-light rounded-pill px-4 fw-bold" style="position: relative; z-index: 2;">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>

    <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="row g-4">
            <!-- Columna izquierda: datos del usuario -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-person-vcard text-primary me-2"></i>Información del Usuario</h5>
                    </div>
                    <div class="card-body p-4">
                        @include('usuarios._form_fields')
                    </div>
                </div>

                @if($usuario->id === auth()->id())
                    <div class="alert rounded-4 border-0 shadow-sm mt-3 d-flex align-items-center" style="background: rgba(56,189,248,0.1); border-left: 4px solid #38bdf8 !important;">
                        <i class="bi bi-info-circle-fill text-info fs-4 me-3"></i>
                        <div class="small">Estás editando tu propio usuario. No puedes cambiar tu propio rol a uno inferior.</div>
                    </div>
                @endif
            </div>

            <!-- Columna derecha: selector de rol -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-0"><i class="bi bi-shield-fill-check text-primary me-2"></i>Rol Asignado</h5>
                            <small class="text-muted">Cambiar el rol actualizará los permisos</small>
                        </div>
                        @if($cfgActual)
                            <span class="role-badge bg-opacity-10" style="background: {{ $cfgActual['gradient'] }}; color: white; padding: 6px 12px; border-radius: 999px; font-size: 0.7rem; font-weight: 700;">
                                <i class="bi {{ $cfgActual['icon'] }}"></i> {{ $cfgActual['label'] }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <div class="role-picker" id="rolePicker">
                            @foreach($roles as $rol)
                                @php $cfg = $rolConfig[$rol->name] ?? null; @endphp
                                <label class="role-card {{ $rolActual == $rol->name ? 'active' : '' }}"
                                       style="--role-color: {{ $cfg['color'] }}; --role-gradient: {{ $cfg['gradient'] }};">
                                    <input type="radio" name="role" value="{{ $rol->name }}"
                                           {{ $rolActual == $rol->name ? 'checked' : '' }} required>
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

                <!-- Preview de permisos -->
                <div class="card border-0 shadow-sm rounded-4 mt-3">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h6 class="fw-bold mb-0"><i class="bi bi-key text-primary me-2"></i>Permisos del rol</h6>
                        <small class="text-muted" id="permHint">Estos son los accesos que tendrá el usuario</small>
                    </div>
                    <div class="card-body p-4">
                        <div class="permission-preview">
                            @foreach($roles as $rol)
                                @php
                                    $grouped = $rol->permissions->groupBy(function($p) { return explode('.', $p->name)[0]; });
                                @endphp
                                <div class="perm-block" data-role="{{ $rol->name }}" style="display: {{ $rolActual == $rol->name ? 'block' : 'none' }};">
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

        <!-- Botones de acción -->
        <div class="d-flex justify-content-between align-items-center mt-4 mb-4 flex-wrap gap-2">
            <a href="{{ route('usuarios.show', $usuario->id) }}" class="btn btn-light rounded-pill px-4">
                <i class="bi bi-eye me-1"></i>Ver Perfil
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('usuarios.index') }}" class="btn btn-light rounded-pill px-4">
                    <i class="bi bi-x-lg me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-check-lg me-1"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    const rolActual = @json($rolActual);
    document.querySelectorAll('.role-card input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.role-card').forEach(c => c.classList.remove('active'));
            radio.closest('.role-card').classList.add('active');
            document.querySelectorAll('.perm-block').forEach(b => b.style.display = 'none');
            const target = document.querySelector(`.perm-block[data-role="${radio.value}"]`);
            if (target) target.style.display = 'block';
            const hint = document.getElementById('permHint');
            if (hint) hint.innerHTML = radio.value !== rolActual
                ? '<i class="bi bi-exclamation-triangle text-warning"></i> Se cambiará del rol actual a <strong>' + radio.value + '</strong>'
                : 'Estos son los accesos que tendrá el usuario';
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
