@extends('layouts.app')
@section('title', 'Nueva Instancia')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="premium-page">
<div class="container-fluid px-4">
    <div class="premium-header" style="margin-bottom: 2rem;">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Nueva Instancia</h3>
                    <p class="mb-0 opacity-75">Crear una nueva instancia de negocio multi-tenant</p>
                </div>
            </div>
            <a href="{{ route('owner.instances.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-dark">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="premium-card" style="background: rgba(255,255,255,0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);">
        <div class="card-accent blue"></div>
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-info-circle me-2"></i>Información de la Instancia</h5>
            <form method="POST" action="{{ route('owner.instances.store') }}" id="instanceForm">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nombre de la Instancia <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control rounded-pill @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej: Restaurante La Esquina" id="nombreInput">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Slug <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="slug" class="form-control rounded-pill @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required placeholder="restaurante-la-esquina" id="slugInput">
                        </div>
                        <small class="text-muted">Identificador &uacute;nico para la instancia.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">RNC</label>
                        <input type="text" name="rnc" class="form-control rounded-pill @error('rnc') is-invalid @enderror" value="{{ old('rnc') }}" placeholder="RNC">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Tipo de Negocio <span class="text-danger">*</span></label>
                        <select name="business_type_id" class="form-select rounded-pill @error('business_type_id') is-invalid @enderror" required>
                            <option value="">Seleccionar...</option>
                            @foreach($businessTypes as $type)
                                <option value="{{ $type->id }}" {{ old('business_type_id') == $type->id ? 'selected' : '' }}>{{ $type->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Costo Mensual</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 rounded-start-pill">RD$</span>
                            <input type="number" name="costo_mensual" class="form-control rounded-end-pill @error('costo_mensual') is-invalid @enderror" value="{{ old('costo_mensual') }}" step="0.01" min="0" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Due&ntilde;o / Responsable</label>
                        <select name="owner_user_id" class="form-select rounded-pill @error('owner_user_id') is-invalid @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}" {{ old('owner_user_id') == $owner->id ? 'selected' : '' }}>{{ $owner->name }} ({{ $owner->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="form-control rounded-pill @error('fecha_vencimiento') is-invalid @enderror" value="{{ old('fecha_vencimiento') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small">Email</label>
                        <input type="email" name="email" class="form-control rounded-pill @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Email de contacto">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small">Tel&eacute;fono</label>
                        <input type="text" name="telefono" class="form-control rounded-pill @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}" placeholder="Tel&eacute;fono">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small">Direcci&oacute;n</label>
                        <textarea name="direccion" class="form-control rounded-4 @error('direccion') is-invalid @enderror" rows="2" placeholder="Direcci&oacute;n">{{ old('direccion') }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" {{ old('activo', '1') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small" for="activo">Activa</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="form-check form-switch mb-0">
                        <input type="checkbox" name="crear_usuario" class="form-check-input" value="1" id="crearUsuario" {{ old('crear_usuario') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="crearUsuario">
                            <i class="bi bi-person-plus text-primary me-1"></i>Crear usuario administrador para esta instancia
                        </label>
                    </div>
                </div>
                <div id="usuarioFields" class="row g-3 p-3 bg-light rounded-4 mb-3 {{ old('crear_usuario') ? '' : 'd-none' }}">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="user_name" class="form-control rounded-pill @error('user_name') is-invalid @enderror" value="{{ old('user_name') }}" placeholder="Nombre del administrador">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Email <span class="text-danger">*</span></label>
                        <input type="email" name="user_email" class="form-control rounded-pill @error('user_email') is-invalid @enderror" value="{{ old('user_email') }}" placeholder="admin@ejemplo.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Contrase&ntilde;a <span class="text-danger">*</span></label>
                        <input type="password" name="user_password" class="form-control rounded-pill @error('user_password') is-invalid @enderror" placeholder="Contrase&ntilde;a">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Confirmar Contrase&ntilde;a <span class="text-danger">*</span></label>
                        <input type="password" name="user_password_confirmation" class="form-control rounded-pill" placeholder="Confirmar contrase&ntilde;a">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Rol <span class="text-danger">*</span></label>
                        <select name="user_role" class="form-select rounded-pill @error('user_role') is-invalid @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach(['gerente', 'admin', 'vendedor', 'almacen', 'contador'] as $role)
                                <option value="{{ $role }}" {{ old('user_role') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">El usuario ser&aacute; asignado a esta instancia con este rol.</small>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Creando nueva instancia</span>
        <button type="submit" form="instanceForm" class="btn btn-save rounded-pill px-5 fw-bold shadow-sm">
            <i class="bi bi-save me-2"></i>Crear Instancia
        </button>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('nombreInput')?.addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
    document.getElementById('slugInput').value = slug;
});

document.getElementById('crearUsuario')?.addEventListener('change', function() {
    document.getElementById('usuarioFields').classList.toggle('d-none', !this.checked);
});
</script>
@endpush
