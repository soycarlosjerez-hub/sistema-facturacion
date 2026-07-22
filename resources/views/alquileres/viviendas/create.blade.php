@extends('layouts.app')
@section('title', 'Nueva Vivienda')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">

    <div class="ui-header mb-4" style="--delay:0s; background: linear-gradient(135deg, #6366f1, #8b5cf6, #a855f7);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-house-add"></i></div>
                <div><h4 class="ui-header-title">Nueva Vivienda</h4><div class="ui-header-meta">Registra una nueva propiedad para alquiler</div></div>
            </div>
        </div>
    </div>

    <form action="{{ route('alquileres.viviendas.store') }}" method="POST" id="instanceForm">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ui-card" style="--delay:.1s;">
                    <div class="ui-card-accent purple"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-info-circle"></i>
                        Informaci&oacute;n General
                    </div>
                    <div class="ui-card-subtitle">Datos b&aacute;sicos de la propiedad</div>
                    <div class="ui-card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nombre" class="ui-label">Nombre de la vivienda <span class="text-danger">*</span></label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-house"></i></span>
                                        <input type="text" name="nombre" id="nombre" class="ui-input @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej: Apartamento 101" required>
                                    </div>
                                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tipo" class="ui-label">Tipo</label>
                                    <select name="tipo" id="tipo" class="ui-select @error('tipo') is-invalid @enderror" required>
                                        <option value="apartamento" {{ old('tipo')=='apartamento'?'selected':'' }}>Apartamento</option>
                                        <option value="casa" {{ old('tipo')=='casa'?'selected':'' }}>Casa</option>
                                        <option value="local" {{ old('tipo')=='local'?'selected':'' }}>Local</option>
                                        <option value="habitacion" {{ old('tipo')=='habitacion'?'selected':'' }}>Habitaci&oacute;n</option>
                                        <option value="oficina" {{ old('tipo')=='oficina'?'selected':'' }}>Oficina</option>
                                        <option value="otro" {{ old('tipo')=='otro'?'selected':'' }}>Otro</option>
                                    </select>
                                    @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="direccion" class="ui-label">Direcci&oacute;n</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <input type="text" name="direccion" id="direccion" class="ui-input @error('direccion') is-invalid @enderror" value="{{ old('direccion') }}" placeholder=" ">
                                    </div>
                                    @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="descripcion" class="ui-label">Descripci&oacute;n</label>
                                    <textarea name="descripcion" id="descripcion" class="ui-textarea @error('descripcion') is-invalid @enderror" placeholder=" " rows="3">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ui-card" style="--delay:.2s;">
                    <div class="ui-card-accent purple"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-sliders"></i>
                        Detalles
                    </div>
                    <div class="ui-card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="habitaciones" class="ui-label">Habitaciones</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-door-open"></i></span>
                                        <input type="number" name="habitaciones" id="habitaciones" class="ui-input" value="{{ old('habitaciones', 0) }}" min="0" placeholder=" ">
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="banos" class="ui-label">Ba&ntilde;os</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-droplet"></i></span>
                                        <input type="number" name="banos" id="banos" class="ui-input" value="{{ old('banos', 0) }}" min="0" placeholder=" ">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="area_m2" class="ui-label">&Aacute;rea (m&sup2;)</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-rulers"></i></span>
                                        <input type="number" step="0.01" name="area_m2" id="area_m2" class="ui-input" value="{{ old('area_m2') }}" placeholder=" ">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ui-card" style="--delay:.3s;">
                    <div class="ui-card-accent purple"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-cash-coin"></i>
                        Valores
                    </div>
                    <div class="ui-card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="monto_alquiler" class="ui-label">Monto Alquiler <span class="text-danger">*</span></label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                        <input type="number" step="0.01" name="monto_alquiler" id="monto_alquiler" class="ui-input @error('monto_alquiler') is-invalid @enderror" value="{{ old('monto_alquiler') }}" placeholder=" " required>
                                    </div>
                                    @error('monto_alquiler')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="monto_deposito" class="ui-label">Dep&oacute;sito</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-shield-check"></i></span>
                                        <input type="number" step="0.01" name="monto_deposito" id="monto_deposito" class="ui-input" value="{{ old('monto_deposito', 0) }}" placeholder=" ">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="estado" class="ui-label">Estado</label>
                                    <select name="estado" id="estado" class="ui-select" required>
                                        <option value="disponible" {{ old('estado','disponible')=='disponible'?'selected':'' }}>Disponible</option>
                                        <option value="alquilado" {{ old('estado')=='alquilado'?'selected':'' }}>Alquilado</option>
                                        <option value="mantenimiento" {{ old('estado')=='mantenimiento'?'selected':'' }}>Mantenimiento</option>
                                        <option value="inactivo" {{ old('estado')=='inactivo'?'selected':'' }}>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
    <div style="height: 80px;"></div>
</div>
@endsection

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('alquileres.viviendas.index') }}" class="ui-btn ui-btn-ghost rounded-pill px-4 me-2">Cancelar</a>
        <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>Guardar Vivienda
        </button>
    </div>
</div>