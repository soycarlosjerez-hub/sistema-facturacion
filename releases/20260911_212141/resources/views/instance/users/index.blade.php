@extends('layouts.app')

@section('title', 'Usuarios de Instancia')

@push('styles')
@include('partials.premium-ui')
<style>
    .premium-header-blue {
        background: linear-gradient(135deg, #3b82f6, #2563eb, #3b82f6, #1d4ed8);
        background-size: 300% 300%;
        animation: premiumGradientShift 6s ease infinite;
        border-radius: 1.2rem;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(59,130,246,.25);
    }
    .premium-header-blue::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background:
            radial-gradient(circle at 30% 40%, rgba(255,255,255,.1) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.07) 0%, transparent 50%);
        pointer-events: none;
    }
    .premium-header-blue .bubble {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
        pointer-events: none;
    }
    .premium-header-blue .bubble:nth-child(1) { width: 80px; height: 80px; top: -20px; right: 10%; animation: premiumFloat 4s ease-in-out infinite; }
    .premium-header-blue .bubble:nth-child(2) { width: 50px; height: 50px; bottom: 10px; right: 28%; animation: premiumFloat 5s ease-in-out infinite 1s; }
    .premium-header-blue .bubble:nth-child(3) { width: 100px; height: 100px; bottom: -30px; right: 5%; animation: premiumFloat 6s ease-in-out infinite .5s; }

    .role-badge {
        display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px;
        border-radius: 999px; font-size: 0.75rem; font-weight: 600;
    }
    .role-badge-sin-rol { background: rgba(15,23,42,0.08); color: #64748b; }
    body.dark-mode .role-badge-sin-rol { background: rgba(255,255,255,0.08); color: #94a3b8; }

    .user-avatar-sm {
        width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem; font-weight: 700; color: white; flex-shrink: 0;
    }

    .ui-stat { background: rgba(255,255,255,.85); border-radius: 12px; padding: 1rem 1.25rem; border: 1px solid rgba(0,0,0,.06); transition: all .2s; }
    body.dark-mode .ui-stat { background: rgba(30,41,59,.9); border-color: rgba(255,255,255,.08); }
    .ui-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
    .stat-label { font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
    body.dark-mode .stat-label { color: #94a3b8; }
    .stat-value { font-size: 1.5rem; font-weight: 800; color: #1e293b; }
    body.dark-mode .stat-value { color: #f1f5f9; }

    .instance-chip {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px;
        border-radius: 999px; font-size: 0.8rem; font-weight: 600; background: rgba(59,130,246,.1);
        color: #2563eb; border: 1px solid rgba(59,130,246,.2);
    }
    body.dark-mode .instance-chip { background: rgba(59,130,246,.15); color: #60a5fa; border-color: rgba(59,130,246,.3); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

    <div class="premium-header-blue mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative; z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="ui-avatar-circle" style="background: rgba(255,255,255,.2); backdrop-filter: blur(8px); border: 2px solid rgba(255,255,255,.35);">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-building me-1"></i>{{ $instance->nombre ?? 'Mi Instancia' }}
                    </span>
                    <h4 class="fw-bold mb-1 text-white">Usuarios de Instancia</h4>
                    <small class="text-white opacity-75">Gestiona los usuarios de tu negocio y asigna roles</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('instance.users.create', $instance->id) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-person-plus me-1"></i>Nuevo Usuario
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ui-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="stat-label">Total Usuarios</div>
                        <div class="stat-value">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-person-x"></i>
                    </div>
                    <div>
                        <div class="stat-label">Sin Rol Asignado</div>
                        <div class="stat-value">{{ $stats['sin_rol'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-success bg-opacity-10 text-success">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <div>
                        <div class="stat-label">Con Rol</div>
                        <div class="stat-value">{{ $stats['total'] - $stats['sin_rol'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="card-header bg-transparent border-0 pb-0 pt-4 px-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-people-fill me-2"></i>Lista de Usuarios</h5>
                        <form method="GET" action="{{ route('instance.users.index', $instance->id) }}" class="d-flex gap-2 flex-wrap">
                            <div class="input-group" style="min-width: 220px;">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar por nombre o email..." value="{{ request('buscar') }}" style="background: #f8fafc;">
                            </div>
                            <select name="instance_role" class="form-select" style="min-width: 180px; background: #f8fafc;" onchange="this.form.submit()">
                                <option value="">Todos los roles</option>
                                @foreach($instanceRoles as $role)
                                    <option value="{{ $role->id }}" {{ request('instance_role') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @if(request('buscar') || request('instance_role'))
                                <a href="{{ route('instance.users.index', $instance->id) }}" class="btn btn-light border"><i class="bi bi-x-lg"></i></a>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($usuarios->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4 py-3" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 600;">Usuario</th>
                                        <th class="py-3" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 600;">Email</th>
                                        <th class="py-3" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 600;">Rol de Instancia</th>
                                        <th class="py-3" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 600;">Sucursal</th>
                                        <th class="py-3" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 600;">Estado</th>
                                        <th class="text-end pe-4 py-3" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 600;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usuarios as $usuario)
                                        <tr class="user-row">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="user-avatar-sm" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                                                        {{ strtoupper(substr($usuario->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $usuario->name }}</div>
                                                        <small class="text-muted">Creado {{ $usuario->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $usuario->email }}</small>
                                            </td>
                                            <td>
                                                @if($usuario->instanceRole)
                                                    <span class="role-badge" style="background: rgba(59,130,246,.1); color: #2563eb;">
                                                        <i class="bi bi-shield-check"></i> {{ $usuario->instanceRole->name }}
                                                    </span>
                                                @else
                                                    <span class="role-badge role-badge-sin-rol">
                                                        <i class="bi bi-x-circle"></i> Sin rol
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($usuario->sucursal)
                                                    <small>{{ $usuario->sucursal->nombre }}</small>
                                                @else
                                                    <small class="text-muted">—</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($usuario->isOnline())
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">
                                                        <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Online
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2">
                                                        Offline
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    <a href="{{ route('instance.users.show', [$instance->id, $usuario->id]) }}" class="ui-action ui-action-view" title="Ver perfil">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('instance.users.edit', [$instance->id, $usuario->id]) }}" class="ui-action ui-action-edit" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    @if($usuario->role !== 'admin-business' || \App\Models\User::where('business_instance_id', $instance->id)->where('role', 'admin-business')->count() > 1)
                                                        <form action="{{ route('instance.users.destroy', [$instance->id, $usuario->id]) }}" method="POST" class="d-inline" onsubmit="return UI.confirm.delete('¿Eliminar al usuario &quot;{{ $usuario->name }}&quot;?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="ui-action ui-action-delete" title="Eliminar">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-top">
                            {{ $usuarios->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-people display-4 text-muted d-block mb-3"></i>
                            <h5 class="fw-bold">No hay usuarios aún</h5>
                            <p class="text-muted mb-4">Crea el primer usuario para tu instancia y asígnale un rol de acceso.</p>
                            <a href="{{ route('instance.users.create', $instance->id) }}" class="ui-btn ui-btn-solid rounded-pill px-4">
                                <i class="bi bi-person-plus me-1"></i>Crear Usuario
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
