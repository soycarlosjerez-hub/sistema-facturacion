@extends('layouts.app')
@section('title', 'Nuevo Usuario - ' . $instance->nombre)
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
@endphp
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-person-plus text-success me-2"></i>Nuevo Usuario</h2>
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
                    <form method="POST" action="{{ route('owner.instances.users.store', $instance) }}">
                        @csrf

                        <div class="alert alert-info rounded-4 border-0 bg-info bg-opacity-10 small" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            Este usuario ser&aacute; asignado a <strong>{{ $instance->nombre }}</strong> con tipo de negocio <strong>{{ $instance->businessType?->nombre ?? '—' }}</strong>.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control rounded-pill @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Nombre completo">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control rounded-pill @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="usuario@ejemplo.com">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Contrase&ntilde;a <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control rounded-pill @error('password') is-invalid @enderror" required placeholder="M&iacute;nimo 6 caracteres">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Confirmar Contrase&ntilde;a <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control rounded-pill" required placeholder="Repetir contrase&ntilde;a">
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
                                      @endphp
                                    <div class="col-md-6">
                                        <label class="role-card d-block rounded-3 border p-3 {{ old('role') === $roleName ? 'border-'.$color.' bg-'.$color.' bg-opacity-10' : 'border-light' }}" style="cursor:pointer;" onclick="selectRole(this, '{{ $roleName }}')">
                                            <div class="form-check">
                                                <input type="radio" name="role" value="{{ $roleName }}" class="form-check-input role-input" id="role_{{ $roleName }}" {{ old('role') === $roleName ? 'checked' : '' }} required>
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
                                <i class="bi bi-check-lg me-2"></i>Crear Usuario
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
