@extends('layouts.app')
@section('title', 'Editar Usuario - ' . $instance->nombre)
@section('content')
@php
    $roleIcons = [
        'admin' => ['bi-shield-lock', 'danger'], 'gerente' => ['bi-briefcase', 'primary'],
        'vendedor' => ['bi-cart3', 'success'], 'almacen' => ['bi-box-seam', 'warning'],
        'contador' => ['bi-calculator', 'info'],
        'supervisor' => ['bi-eye', 'purple'], 'administrativo' => ['bi-folder2-open', 'teal'],
        'mesero' => ['bi-person', 'orange'], 'cocinero' => ['bi-fire', 'danger'],
        'delivery' => ['bi-truck', 'info'], 'bartender' => ['bi-cup-hot', 'purple'],
        'lavador' => ['bi-droplet', 'cyan'], 'recepcionista' => ['bi-headset', 'indigo'],
        'inspector' => ['bi-search', 'warning'],
        'cajero' => ['bi-cash-register', 'success'], 'reponedor' => ['bi-boxes', 'orange'],
        'despachador' => ['bi-truck', 'secondary'], 'vendedor-mayorista' => ['bi-people', 'primary'],
        'consultor' => ['bi-chat-dots', 'secondary'], 'facturador' => ['bi-file-earmark-text', 'pink'],
        'owner' => ['bi-shield-shaded', 'danger'], 'root' => ['bi-key-fill', 'dark'], 'admin-business' => ['bi-building-fill-lock', 'primary']
    ];
    $currentRole = $user->roles->first()?->name;
@endphp
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-pencil-square text-warning me-2"></i>Editar Usuario</h2>
            <p class="text-muted mb-0">{{ $instance->nombre }} &middot; {{ $instance->businessType?->nombre ?? 'Sin tipo' }}</p>
        </div>
        <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('owner.instances.users.update', [$instance, $user]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control rounded-pill @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required placeholder="Nombre completo">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control rounded-pill @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required placeholder="usuario@ejemplo.com">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Nueva Contrase&ntilde;a <small class="text-muted fw-normal">(dejar en blanco para no cambiar)</small></label>
                                <input type="password" name="password" class="form-control rounded-pill @error('password') is-invalid @enderror" placeholder="Nueva contraseña">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Confirmar Contrase&ntilde;a</label>
                                <input type="password" name="password_confirmation" class="form-control rounded-pill" placeholder="Confirmar contraseña">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Rol <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                @php $allRoles = \Spatie\Permission\Models\Role::orderBy('name')->get(); @endphp
                                 @foreach($allRoles as $rol)
                                      @php
                                          $roleName = $rol->name;
                                          $icon = $roleIcons[$roleName][0] ?? 'bi-person';
                                          $color = $roleIcons[$roleName][1] ?? 'primary';
                                          $isSelected = old('role', $currentRole) === $roleName;
                                      @endphp
                                    <div class="col-md-6">
                                        <label class="role-card d-block rounded-3 border p-3 {{ $isSelected ? 'border-'.$color.' bg-'.$color.' bg-opacity-10' : 'border-light' }}" style="cursor:pointer;" onclick="selectRole(this, '{{ $roleName }}')">
                                            <div class="form-check">
                                                <input type="radio" name="role" value="{{ $roleName }}" class="form-check-input role-input" id="role_{{ $roleName }}" {{ $isSelected ? 'checked' : '' }} required>
                                                <label class="form-check-label fw-bold" for="role_{{ $roleName }}">
                                                    <i class="bi {{ $icon }} text-{{ $color }} me-2"></i>{{ $rol->name }}
                                                </label>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('role') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                                <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectRole(el, role) {
    const colorMap = {gerente:'primary', admin:'danger', vendedor:'success', almacen:'warning', contador:'info', supervisor:'purple', administrativo:'teal', mesero:'orange', cocinero:'danger', delivery:'info', bartender:'purple', lavador:'cyan', recepcionista:'indigo', inspector:'warning', cajero:'success', reponedor:'orange', despachador:'secondary', ['vendedor-mayorista']:'primary', consultor:'secondary', facturador:'pink'};
    document.querySelectorAll('.role-card').forEach(c => {
        ['primary','danger','success','warning','info','purple','teal','orange','cyan','indigo','secondary','pink'].forEach(cl => {
            c.classList.remove('border-'+cl, 'bg-'+cl, 'bg-opacity-10');
        });
        c.classList.add('border-light');
    });
    const c = colorMap[role] || 'primary';
    el.classList.remove('border-light');
    el.classList.add('border-'+c, 'bg-'+c, 'bg-opacity-10');
    document.getElementById('role_' + role).checked = true;
}
</script>
@endpush
