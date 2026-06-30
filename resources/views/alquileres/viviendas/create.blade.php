@extends('layouts.app')
@section('title', 'Nueva Vivienda')

@push('styles')
@include('partials.premium-ui')
<style>
    .form-floating-modern { position: relative; margin-bottom: 1rem; }
    .form-floating-modern .form-icon { position: absolute; top: 50%; left: 14px; transform: translateY(-50%); color: #94a3b8; z-index: 5; font-size: 1.1rem; pointer-events: none; }
    .form-floating-modern .form-control { padding-left: 42px; height: 50px; border-radius: 12px; border: 1.5px solid #e2e8f0; transition: all .2s; }
    .form-floating-modern .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
    .form-floating-modern .form-label-float { position: absolute; top: 50%; left: 42px; transform: translateY(-50%); color: #94a3b8; transition: all .2s; pointer-events: none; background: transparent; padding: 0 4px; }
    .form-floating-modern .form-control:focus + .form-label-float,
    .form-floating-modern .form-control:not(:placeholder-shown) + .form-label-float { top: -10px; left: 36px; font-size: .75rem; color: #6366f1; background: #fff; }
    textarea.form-control { height: auto !important; padding-top: 14px !important; }
    textarea.form-control + .form-label-float { top: 14px !important; }
    textarea.form-control:focus + .form-label-float,
    textarea.form-control:not(:placeholder-shown) + .form-label-float { top: -10px !important; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4" style="background: linear-gradient(135deg, #6366f1, #8b5cf6, #a855f7);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
            <div class="premium-avatar-circle"><i class="bi bi-house-add"></i></div>
            <div><h4 class="fw-bold mb-1 text-white">Nueva Vivienda</h4><small class="text-white opacity-75">Registra una nueva propiedad para alquiler</small></div>
        </div>
    </div>

    <form action="{{ route('alquileres.viviendas.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="premium-card">
                    <div class="card-accent purple"></div>
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h6 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Información General</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="form-floating-modern">
                                    <i class="bi bi-house form-icon"></i>
                                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder=" " required>
                                    <label class="form-label-float" for="nombre">Nombre de la vivienda *</label>
                                    @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="bi bi-tag form-icon"></i>
                                    <select name="tipo" id="tipo" class="form-control @error('tipo') is-invalid @enderror" required>
                                        <option value="apartamento" {{ old('tipo')=='apartamento'?'selected':'' }}>Apartamento</option>
                                        <option value="casa" {{ old('tipo')=='casa'?'selected':'' }}>Casa</option>
                                        <option value="local" {{ old('tipo')=='local'?'selected':'' }}>Local</option>
                                        <option value="habitacion" {{ old('tipo')=='habitacion'?'selected':'' }}>Habitación</option>
                                        <option value="oficina" {{ old('tipo')=='oficina'?'selected':'' }}>Oficina</option>
                                        <option value="otro" {{ old('tipo')=='otro'?'selected':'' }}>Otro</option>
                                    </select>
                                    <label class="form-label-float" for="tipo">Tipo</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-geo-alt form-icon"></i>
                                    <input type="text" name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion') }}" placeholder=" ">
                                    <label class="form-label-float" for="direccion">Dirección</label>
                                    @error('direccion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-card-text form-icon" style="top:14px;"></i>
                                    <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" placeholder=" " rows="3">{{ old('descripcion') }}</textarea>
                                    <label class="form-label-float" for="descripcion">Descripción</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="premium-card">
                    <div class="card-accent purple"></div>
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h6 class="fw-bold mb-0"><i class="bi bi-sliders text-primary me-2"></i>Detalles</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="form-floating-modern">
                                    <i class="bi bi-door-open form-icon"></i>
                                    <input type="number" name="habitaciones" id="habitaciones" class="form-control" value="{{ old('habitaciones', 0) }}" min="0" placeholder=" ">
                                    <label class="form-label-float" for="habitaciones">Habitaciones</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating-modern">
                                    <i class="bi bi-droplet form-icon"></i>
                                    <input type="number" name="banos" id="banos" class="form-control" value="{{ old('banos', 0) }}" min="0" placeholder=" ">
                                    <label class="form-label-float" for="banos">Baños</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-rulers form-icon"></i>
                                    <input type="number" step="0.01" name="area_m2" id="area_m2" class="form-control" value="{{ old('area_m2') }}" placeholder=" ">
                                    <label class="form-label-float" for="area_m2">Área (m²)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="premium-card">
                    <div class="card-accent purple"></div>
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h6 class="fw-bold mb-0"><i class="bi bi-cash-coin text-primary me-2"></i>Valores</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-currency-dollar form-icon"></i>
                                    <input type="number" step="0.01" name="monto_alquiler" id="monto_alquiler" class="form-control @error('monto_alquiler') is-invalid @enderror" value="{{ old('monto_alquiler') }}" placeholder=" " required>
                                    <label class="form-label-float" for="monto_alquiler">Monto Alquiler *</label>
                                    @error('monto_alquiler')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-shield-check form-icon"></i>
                                    <input type="number" step="0.01" name="monto_deposito" id="monto_deposito" class="form-control" value="{{ old('monto_deposito', 0) }}" placeholder=" ">
                                    <label class="form-label-float" for="monto_deposito">Depósito</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-info-circle form-icon"></i>
                                    <select name="estado" id="estado" class="form-control" required>
                                        <option value="disponible" {{ old('estado','disponible')=='disponible'?'selected':'' }}>Disponible</option>
                                        <option value="alquilado" {{ old('estado')=='alquilado'?'selected':'' }}>Alquilado</option>
                                        <option value="mantenimiento" {{ old('estado')=='mantenimiento'?'selected':'' }}>Mantenimiento</option>
                                        <option value="inactivo" {{ old('estado')=='inactivo'?'selected':'' }}>Inactivo</option>
                                    </select>
                                    <label class="form-label-float" for="estado">Estado</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" style="background:linear-gradient(135deg,#6366f1,#4f46e5);border:0;"><i class="bi bi-save me-1"></i>Guardar Vivienda</button>
            <a href="{{ route('alquileres.viviendas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
        </div>
    </form>
</div>
@endsection
