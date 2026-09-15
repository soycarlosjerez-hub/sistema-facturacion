<div class="form-floating-modern">
    <i class="bi bi-person form-icon"></i>
    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $usuario->name ?? '') }}" placeholder=" " required maxlength="255">
    <label class="form-label-float" for="name">Nombre completo</label>
    @error('name')<div class="invalid-feedback d-block ms-5"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
</div>

<div class="form-floating-modern">
    <i class="bi bi-envelope form-icon"></i>
    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
           value="{{ old('email', $usuario->email ?? '') }}" placeholder=" " required maxlength="255">
    <label class="form-label-float" for="email">Correo electrónico</label>
    @error('email')<div class="invalid-feedback d-block ms-5"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="form-floating-modern">
            <i class="bi bi-lock form-icon"></i>
            <input type="password" name="password" id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder=" " {{ isset($usuario) ? '' : 'required' }} minlength="6">
            <label class="form-label-float" for="password">
                {{ isset($usuario) ? 'Nueva contraseña (opcional)' : 'Contraseña' }}
            </label>
            <div class="password-strength"><div class="password-strength-bar" id="strengthBar"></div></div>
            @error('password')<div class="invalid-feedback d-block ms-5"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-floating-modern">
            <i class="bi bi-shield-check form-icon"></i>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="form-control" placeholder=" " {{ isset($usuario) ? '' : 'required' }} minlength="6">
            <label class="form-label-float" for="password_confirmation">Confirmar contraseña</label>
        </div>
    </div>
</div>
@if(isset($usuario))
    <div class="text-muted small mb-3 ms-1"><i class="bi bi-info-circle me-1"></i>Deja los campos de contraseña vacíos para mantener la actual.</div>
@endif

@if(isset($sucursales) && $sucursales->count())
<div class="form-floating-modern">
    <i class="bi bi-building form-icon"></i>
    <select name="sucursal_id" id="sucursal_id" class="form-select @error('sucursal_id') is-invalid @enderror">
        <option value="">Sin sucursal asignada</option>
        @foreach($sucursales as $s)
            <option value="{{ $s->id }}" {{ old('sucursal_id', $usuario->sucursal_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
        @endforeach
    </select>
    <label class="form-label-float" for="sucursal_id">Sucursal</label>
    @error('sucursal_id')<div class="invalid-feedback d-block ms-5"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
</div>
@endif
