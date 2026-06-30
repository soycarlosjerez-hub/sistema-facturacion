@extends('layouts.app')
@section('title', "Editar Vivienda")

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4" style="background: linear-gradient(135deg, #6366f1, #8b5cf6, #a855f7);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
            <div class="premium-avatar-circle"><i class="bi bi-pencil-square"></i></div>
            <div><h4 class="fw-bold mb-1 text-white">{{ $vivienda->nombre }}</h4><small class="text-white opacity-75">Editando vivienda</small></div>
        </div>
    </div>

    <form action="{{ route('alquileres.viviendas.update', $vivienda) }}" method="POST">
        @csrf @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="premium-card">
                    <div class="card-accent purple"></div>
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4"><h6 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Informaci�n General</h6></div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="form-floating-modern">
                                    <i class="bi bi-house form-icon"></i>
                                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $vivienda->nombre) }}" placeholder=" " required>
                                    <label class="form-label-float" for="nombre">Nombre de la vivienda *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="bi bi-tag form-icon"></i>
                                    <select name="tipo" id="tipo" class="form-control" required>
                                        <option value="apartamento" {{ old('tipo',$vivienda->tipo)=='apartamento'?'selected':'' }}>Apartamento</option>
                                        <option value="casa" {{ old('tipo',$vivienda->tipo)=='casa'?'selected':'' }}>Casa</option>
                                        <option value="local" {{ old('tipo',$vivienda->tipo)=='local'?'selected':'' }}>Local</option>
                                        <option value="habitacion" {{ old('tipo',$vivienda->tipo)=='habitacion'?'selected':'' }}>Habitaci�n</option>
                                        <option value="oficina" {{ old('tipo',$vivienda->tipo)=='oficina'?'selected':'' }}>Oficina</option>
                                        <option value="otro" {{ old('tipo',$vivienda->tipo)=='otro'?'selected':'' }}>Otro</option>
                                    </select>
                                    <label class="form-label-float" for="tipo">Tipo</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-geo-alt form-icon"></i>
                                    <input type="text" name="direccion" id="direccion" class="form-control" value="{{ old('direccion', $vivienda->direccion) }}" placeholder=" ">
                                    <label class="form-label-float" for="direccion">Direcci�n</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-card-text form-icon" style="top:14px;"></i>
                                    <textarea name="descripcion" id="descripcion" class="form-control" placeholder=" " rows="3">{{ old('descripcion', $vivienda->descripcion) }}</textarea>
                                    <label class="form-label-float" for="descripcion">Descripci�n</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="premium-card">
                    <div class="card-accent purple"></div>
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4"><h6 class="fw-bold mb-0"><i class="bi bi-sliders text-primary me-2"></i>Detalles</h6></div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="form-floating-modern">
                                    <i class="bi bi-door-open form-icon"></i>
                                    <input type="number" name="habitaciones" id="habitaciones" class="form-control" value="{{ old('habitaciones', $vivienda->habitaciones) }}" min="0" placeholder=" ">
                                    <label class="form-label-float" for="habitaciones">Habitaciones</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating-modern">
                                    <i class="bi bi-droplet form-icon"></i>
                                    <input type="number" name="banos" id="banos" class="form-control" value="{{ old('banos', $vivienda->banos) }}" min="0" placeholder=" ">
                                    <label class="form-label-float" for="banos">Ba�os</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-rulers form-icon"></i>
                                    <input type="number" step="0.01" name="area_m2" id="area_m2" class="form-control" value="{{ old('area_m2', $vivienda->area_m2) }}" placeholder=" ">
                                    <label class="form-label-float" for="area_m2">�rea (m�)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="premium-card">
                    <div class="card-accent purple"></div>
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4"><h6 class="fw-bold mb-0"><i class="bi bi-cash-coin text-primary me-2"></i>Valores</h6></div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-currency-dollar form-icon"></i>
                                    <input type="number" step="0.01" name="monto_alquiler" id="monto_alquiler" class="form-control" value="{{ old('monto_alquiler', $vivienda->monto_alquiler) }}" placeholder=" " required>
                                    <label class="form-label-float" for="monto_alquiler">Monto Alquiler *</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-shield-check form-icon"></i>
                                    <input type="number" step="0.01" name="monto_deposito" id="monto_deposito" class="form-control" value="{{ old('monto_deposito', $vivienda->monto_deposito) }}" placeholder=" ">
                                    <label class="form-label-float" for="monto_deposito">Dep�sito</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-info-circle form-icon"></i>
                                    <select name="estado" id="estado" class="form-control" required>
                                        <option value="disponible" {{ old('estado',$vivienda->estado)=='disponible'?'selected':'' }}>Disponible</option>
                                        <option value="alquilado" {{ old('estado',$vivienda->estado)=='alquilado'?'selected':'' }}>Alquilado</option>
                                        <option value="mantenimiento" {{ old('estado',$vivienda->estado)=='mantenimiento'?'selected':'' }}>Mantenimiento</option>
                                        <option value="inactivo" {{ old('estado',$vivienda->estado)=='inactivo'?'selected':'' }}>Inactivo</option>
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
            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" style="background:linear-gradient(135deg,#6366f1,#4f46e5);border:0;"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
            <a href="{{ route('alquileres.viviendas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
        </div>
    </form>
</div>
@endsection
