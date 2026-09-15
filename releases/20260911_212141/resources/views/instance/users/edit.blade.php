@extends('layouts.app')

@section('title', 'Editar Usuario: ' . $user->name)

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
    .premium-header-amber .bubble:nth-child(1) { width: 80px; height: 80px; top: -20px; right: 10%; animation: premiumFloat 4s ease-in-out infinite; }
    .premium-header-amber .bubble:nth-child(2) { width: 50px; height: 50px; bottom: 10px; right: 28%; animation: premiumFloat 5s ease-in-out infinite 1s; }
    .premium-header-amber .bubble:nth-child(3) { width: 100px; height: 100px; bottom: -30px; right: 5%; animation: premiumFloat 6s ease-in-out infinite .5s; }

    .user-avatar {
        width: 72px; height: 72px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        color: white; font-size: 2rem; font-weight: 800; flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(0,0,0,.15);
    }

    .role-card {
        display: flex; align-items: center; gap: 12px; padding: 12px 16px;
        border-radius: 12px; border: 2px solid rgba(0,0,0,.06); background: rgba(255,255,255,.8);
        cursor: pointer; transition: all .2s; margin-bottom: 8px;
    }
    .role-card:hover { border-color: rgba(245,158,11,.3); background: rgba(245,158,11,.04); transform: translateX(4px); }
    .role-card.active { border-color: #f59e0b; background: rgba(245,158,11,.08); box-shadow: 0 4px 12px rgba(245,158,11,.15); }
    body.dark-mode .role-card { border-color: rgba(255,255,255,.08); background: rgba(30,41,59,.9); }
    body.dark-mode .role-card:hover { border-color: rgba(245,158,11,.4); background: rgba(245,158,11,.08); }
    body.dark-mode .role-card.active { border-color: #f59e0b; background: rgba(245,158,11,.15); }

    .role-icon-sm {
        width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; color: white; flex-shrink: 0;
    }
    .role-name { font-weight: 700; font-size: 0.9rem; color: #1e293b; }
    .role-desc { font-size: 0.8rem; color: #64748b; margin: 0; }
    body.dark-mode .role-name { color: #f1f5f9; }
    body.dark-mode .role-desc { color: #94a3b8; }

    .ui-card { background: rgba(255,255,255,.9); border: 1px solid rgba(0,0,0,.06); border-radius: 16px; backdrop-filter: blur(12px); }
    body.dark-mode .ui-card { background: rgba(30,41,59,.9); border-color: rgba(255,255,255,.08); }

    .ui-label { font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 6px; display: block; }
    body.dark-mode .ui-label { color: #d1d5db; }
    .ui-input, .ui-select {
        width: 100%; padding: 10px 14px; border: 1.5px solid rgba(0,0,0,.1); border-radius: 10px;
        font-size: 0.9rem; background: rgba(255,255,255,.9); transition: all .2s;
    }
    .ui-input:focus, .ui-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.15); outline: none; }
    body.dark-mode .ui-input, body.dark-mode .ui-select { background: rgba(15,23,42,.9); border-color: rgba(255,255,255,.15); color: #f1f5f9; }
    body.dark-mode .ui-input:focus, body.dark-mode .ui-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.2); }

    .btn-save { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 700; transition: all .2s; }
    .btn-save:hover { background: linear-gradient(135deg, #d97706, #b45309); color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(245,158,11,.3); }
    .btn-cancel { background: rgba(255,255,255,.15); color: #64748b; border: 1.5px solid rgba(0,0,0,.08); padding: 10px 20px; border-radius: 10px; font-weight: 600; transition: all .2s; text-decoration: none; }
    .btn-cancel:hover { background: rgba(255,255,255,.2); color: #1e293b; text-decoration: none; }
    body.dark-mode .btn-cancel { background: rgba(255,255,255,.08); color: #94a3b8; border-color: rgba(255,255,255,.12); }
    body.dark-mode .btn-cancel:hover { background: rgba(255,255,255,.12); color: #f1f5f9; }

    .premium-sticky-bar {
        position: fixed; bottom: 0; left: 0; right: 0; z-index: 1000;
        background: rgba(255,255,255,.95); backdrop-filter: blur(12px);
        border-top: 1px solid rgba(0,0,0,.08); padding: 12px 24px;
    }
    body.dark-mode .premium-sticky-bar { background: rgba(15,23,42,.95); border-color: rgba(255,255,255,.1); }

    .strength-bar-container { height: 6px; border-radius: 999px; background: rgba(0,0,0,.06); overflow: hidden; margin-top: 6px; }
    .strength-bar { height: 100%; border-radius: 999px; transition: all .3s; width: 0%; }
    body.dark-mode .strength-bar-container { background: rgba(255,255,255,.1); }

    .role-picker { max-height: 400px; overflow-y: auto; padding-right: 4px; }
    .role-picker::-webkit-scrollbar { width: 4px; }
    .role-picker::-webkit-scrollbar-thumb { background: rgba(0,0,0,.15); border-radius: 4px; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">

    <div class="premium-header-amber mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative; z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="user-avatar" style="background: rgba(255,255,255,.2); backdrop-filter: blur(8px); border: 2px solid rgba(255,255,255,.35);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-pencil me-1"></i>EDITANDO
                    </span>
                    <h4 class="fw-bold mb-1 text-white">{{ $user->name }}</h4>
                    <small class="text-white opacity-75">{{ $user->email }}</small>
                </div>
            </div>
            <a href="{{ route('instance.users.index', $user->business_instance_id) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="editUserForm" action="{{ route('instance.users.update', [$user->business_instance_id, $user->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="ui-card h-100">
                    <div class="card-body p-4">
                        <div class="premium-card-title mb-1"><i class="bi bi-person-vcard me-2"></i>Información del Usuario</div>
                        <div class="premium-card-subtitle mb-4">Edita los datos del usuario</div>

                        <div class="mb-3">
                            <label class="ui-label">Nombre Completo</label>
                            <input type="text" name="name" class="ui-input" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="ui-label">Correo Electrónico</label>
                            <input type="email" name="email" class="ui-input" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="ui-label">Nueva Contraseña <small class="text-muted">(Opcional)</small></label>
                                <input type="password" name="password" class="ui-input" id="password" placeholder="Deja vacío para no cambiar">
                                <div class="strength-bar-container">
                                    <div class="strength-bar" id="strengthBar"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label">Confirmar Nueva Contraseña</label>
                                <input type="password" name="password_confirmation" class="ui-input" id="password_confirmation" placeholder="Repite la contraseña">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="ui-label">Rol de Instancia <small class="text-muted">(Opcional)</small></label>
                            <p class="text-muted small mb-3">Selecciona el nivel de acceso del usuario en tu negocio</p>

                            <div class="role-picker" id="rolePicker">
                                @if($instanceRoles->count() > 0)
                                    @foreach($instanceRoles as $rol)
                                        <label class="role-card {{ old('instance_role_id', $user->instance_role_id) == $rol->id ? 'active' : '' }}" data-role-id="{{ $rol->id }}">
                                            <input type="radio" name="instance_role_id" value="{{ $rol->id }}" class="d-none" {{ old('instance_role_id', $user->instance_role_id) == $rol->id ? 'checked' : '' }}>
                                            <div class="role-icon-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                                <i class="bi bi-shield-check"></i>
                                            </div>
                                            <div>
                                                <div class="role-name">{{ ucfirst($rol->name) }}</div>
                                                @if($rol->users->count() > 0)
                                                    <div class="role-desc">{{ $rol->users->count() }} usuario(s) con este rol</div>
                                                @else
                                                    <div class="role-desc">Rol sin usuarios asignados</div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <i class="bi bi-shield-lock display-4 text-muted d-block mb-3"></i>
                                        <p class="text-muted mb-0">No hay roles de instancia disponibles.</p>
                                        <small class="text-muted">Contacta al administrador del sistema para que configure roles.</small>
                                    </div>
                                @endif
                            </div>
                            @error('instance_role_id')<div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="ui-card">
                    <div class="card-body p-4">
                        <div class="premium-card-title mb-1"><i class="bi bi-info-circle me-2"></i>Resumen</div>
                        <div class="premium-card-subtitle mb-3">Información del usuario</div>

                        <div class="text-center mb-4">
                            <div class="user-avatar mx-auto mb-3" style="background: linear-gradient(135deg, #f59e0b, #d97706); width: 64px; height: 64px; font-size: 1.5rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="fw-bold">{{ $user->name }}</div>
                            <div class="text-muted small">{{ $user->email }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="ui-label">Instancia</label>
                            <div class="instance-chip">
                                <i class="bi bi-building"></i> {{ $instance->nombre }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="ui-label">Rol Actual</label>
                            <div id="summaryRole" class="badge rounded-pill px-3 py-2" style="background: rgba(245,158,11,.1); color: #d97706;">
                                @if($user->instanceRole)
                                    <i class="bi bi-shield-check me-1"></i> {{ ucfirst($user->instanceRole->name) }}
                                @else
                                    <i class="bi bi-person-x me-1"></i> Sin asignar
                                @endif
                            </div>
                        </div>

                        <div class="alert alert-info rounded-3 border-0 small mb-0">
                            <i class="bi bi-shield-check me-2"></i>
                            Si cambias la contraseña, el usuario recibirá un correo para establecerla.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="premium-sticky-bar">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-warning"></i>
                <span class="fw-semibold d-none d-sm-inline">Editar Usuario</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('instance.users.index', $user->business_instance_id) }}" class="btn-cancel">Cancelar</a>
                <button type="submit" form="editUserForm" class="btn-save"><i class="bi bi-save me-2"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleCards = document.querySelectorAll('.role-card');
        const roleInput = document.querySelector('input[name="instance_role_id"]');
        const summaryRole = document.getElementById('summaryRole');
        const nameInput = document.querySelector('input[name="name"]');
        const emailInput = document.querySelector('input[name="email"]');

        roleCards.forEach(card => {
            card.addEventListener('click', function() {
                roleCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const radio = this.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;

                const roleName = this.querySelector('.role-name').textContent;
                summaryRole.style.background = 'rgba(245,158,11,.1)';
                summaryRole.style.color = '#d97706';
                summaryRole.innerHTML = '<i class="bi bi-shield-check me-1"></i> ' + roleName;
            });
        });

        const passInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        if (passInput && strengthBar) {
            passInput.addEventListener('input', function() {
                const v = this.value;
                let score = 0;
                if (v.length >= 12) score += 30;
                if (v.length >= 16) score += 20;
                if (/[A-Z]/.test(v)) score += 20;
                if (/[0-9]/.test(v)) score += 20;
                if (/[^A-Za-z0-9]/.test(v)) score += 10;
                strengthBar.style.width = score + '%';
                strengthBar.style.background = score < 30 ? '#ef4444' : score < 60 ? '#f59e0b' : '#22c55e';
            });
        }

        if (nameInput) {
            nameInput.addEventListener('input', function() {
                const firstLetter = this.value.charAt(0).toUpperCase();
                const avatar = document.querySelector('.text-center .user-avatar');
                if (avatar) avatar.textContent = firstLetter;
            });
        }
    });
</script>
@endsection
