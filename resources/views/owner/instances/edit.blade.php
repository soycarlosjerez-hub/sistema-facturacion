@extends('layouts.app')
@section('title', 'Editar Instancia')
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-pencil text-primary me-2"></i>Editar Instancia</h2>
            <p class="text-muted mb-0">{{ $instance->nombre }}</p>
        </div>
        <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('owner.instances.update', $instance) }}">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control rounded-pill @error('nombre') is-invalid @enderror" value="{{ old('nombre', $instance->nombre) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Slug</label>
                                <input type="text" class="form-control rounded-pill bg-light" value="{{ $instance->slug }}" disabled>
                                <small class="text-muted">El slug no se puede modificar.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">RNC</label>
                                <input type="text" name="rnc" class="form-control rounded-pill @error('rnc') is-invalid @enderror" value="{{ old('rnc', $instance->rnc) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Tipo de Negocio <span class="text-danger">*</span></label>
                                <select name="business_type_id" class="form-select rounded-pill @error('business_type_id') is-invalid @enderror" required>
                                    @foreach($businessTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('business_type_id', $instance->business_type_id) == $type->id ? 'selected' : '' }}>{{ $type->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Costo Mensual</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 rounded-start-pill">RD$</span>
                                    <input type="number" name="costo_mensual" class="form-control rounded-end-pill @error('costo_mensual') is-invalid @enderror" value="{{ old('costo_mensual', $instance->costo_mensual) }}" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Due&ntilde;o / Responsable</label>
                                <select name="owner_user_id" class="form-select rounded-pill @error('owner_user_id') is-invalid @enderror">
                                    <option value="">Seleccionar...</option>
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}" {{ old('owner_user_id', $instance->owner_user_id) == $owner->id ? 'selected' : '' }}>{{ $owner->name }} ({{ $owner->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Fecha de Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" class="form-control rounded-pill @error('fecha_vencimiento') is-invalid @enderror" value="{{ old('fecha_vencimiento', $instance->fecha_vencimiento?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">Email</label>
                                <input type="email" name="email" class="form-control rounded-pill @error('email') is-invalid @enderror" value="{{ old('email', $instance->email) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">Tel&eacute;fono</label>
                                <input type="text" name="telefono" class="form-control rounded-pill @error('telefono') is-invalid @enderror" value="{{ old('telefono', $instance->telefono) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">Direcci&oacute;n</label>
                                <textarea name="direccion" class="form-control rounded-4 @error('direccion') is-invalid @enderror" rows="2">{{ old('direccion', $instance->direccion) }}</textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" {{ old('activo', $instance->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold small" for="activo">Activa</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                                <i class="bi bi-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="row justify-content-center mt-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border-left: 4px solid #dc2626 !important;">
                <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                        <div>
                            <h5 class="fw-bold mb-0 text-white">Zona de Peligro</h5>
                            <small class="text-white text-opacity-75">Acciones destructivas que no se pueden deshacer</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                            <i class="bi bi-eraser fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Limpiar todos los datos de la instancia</h6>
                            <p class="text-muted small mb-0">
                                Esta acción eliminará <strong>todos los datos operacionales</strong> de <strong>{{ $instance->nombre }}</strong>:
                                productos, clientes, proveedores, ventas, compras, cotizaciones, conduces, devoluciones,
                                gastos, almacenes, cajas, mesas, reservaciones, datos de lavadero, listas de precio,
                                sucursales, secuencias NCF/ECF, y más.
                            </p>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 mb-3" style="background: rgba(239,68,68,0.05); border: 1px solid rgba(239,68,68,0.15);">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle text-danger mt-1"></i>
                            <div class="small">
                                <strong class="text-danger">Se conservarán:</strong> usuarios, roles de instancia, 
                                visibilidad de módulos, historial de pagos y configuración de la instancia.
                                <br>
                                <strong class="text-danger">No se puede deshacer.</strong> Asegúrate de haber hecho 
                                un backup antes de continuar.
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('owner.instances.clean', $instance) }}" 
                          onsubmit="return confirm('¿ESTÁS ABSOLUTAMENTE SEGURO? Esta acción eliminará TODOS los datos de {{ $instance->nombre }}. No se puede deshacer.')">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-danger">Escribe <strong>{{ $instance->nombre }}</strong> para confirmar:</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-key text-danger"></i></span>
                                <input type="text" name="confirm_name" class="form-control border-start-0" 
                                       placeholder="Escribe el nombre exacto de la instancia" 
                                       autocomplete="off" required
                                       oninput="document.getElementById('clean-btn').disabled = (this.value !== '{{ $instance->nombre }}')">
                            </div>
                        </div>
                        <button type="submit" id="clean-btn" class="btn btn-danger rounded-pill px-4 fw-bold" disabled>
                            <i class="bi bi-eraser me-2"></i>Limpiar Todos los Datos
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
