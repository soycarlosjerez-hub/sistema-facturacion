@extends('layouts.app')
@section('title', 'Editar Instancia')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">
    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Editar Instancia</h3>
                    <p class="mb-0 opacity-75">{{ $instance->nombre }}</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#3b82f6"></div>
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-info-circle me-2"></i>Informaci&oacute;n de la Instancia</h5>
            <form method="POST" action="{{ route('owner.instances.update', $instance) }}" id="instanceForm">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="ui-input rounded-pill @error('nombre') is-invalid @enderror" value="{{ old('nombre', $instance->nombre) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Slug</label>
                        <input type="text" class="ui-input rounded-pill bg-light" value="{{ $instance->slug }}" disabled>
                        <small class="text-muted">El slug no se puede modificar.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">RNC</label>
                        <input type="text" name="rnc" class="ui-input rounded-pill @error('rnc') is-invalid @enderror" value="{{ old('rnc', $instance->rnc) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Tipo de Negocio <span class="text-danger">*</span></label>
                        <select name="business_type_id" class="ui-select rounded-pill @error('business_type_id') is-invalid @enderror" required>
                            @foreach($businessTypes as $type)
                                <option value="{{ $type->id }}" {{ old('business_type_id', $instance->business_type_id) == $type->id ? 'selected' : '' }}>{{ $type->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Costo Mensual</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text bg-light border-0 rounded-start-pill">RD$</span>
                            <input type="number" name="costo_mensual" class="ui-input rounded-end-pill @error('costo_mensual') is-invalid @enderror" value="{{ old('costo_mensual', $instance->costo_mensual) }}" step="0.01" min="0" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Due&ntilde;o / Responsable</label>
                        <select name="owner_user_id" class="ui-select rounded-pill @error('owner_user_id') is-invalid @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}" {{ old('owner_user_id', $instance->owner_user_id) == $owner->id ? 'selected' : '' }}>{{ $owner->name }} ({{ $owner->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="ui-input rounded-pill @error('fecha_vencimiento') is-invalid @enderror" value="{{ old('fecha_vencimiento', $instance->fecha_vencimiento?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-12">
                        <label class="ui-label fw-bold">Email</label>
                        <input type="email" name="email" class="ui-input rounded-pill @error('email') is-invalid @enderror" value="{{ old('email', $instance->email) }}">
                    </div>
                    <div class="col-12">
                        <label class="ui-label fw-bold">Tel&eacute;fono</label>
                        <input type="text" name="telefono" class="ui-input rounded-pill @error('telefono') is-invalid @enderror" value="{{ old('telefono', $instance->telefono) }}">
                    </div>
                    <div class="col-12">
                        <label class="ui-label fw-bold">Direcci&oacute;n</label>
                        <textarea name="direccion" class="ui-input rounded-4 @error('direccion') is-invalid @enderror" rows="2">{{ old('direccion', $instance->direccion) }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" {{ old('activo', $instance->activo) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small" for="activo">Activa</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card mt-5" style="--delay:.2s;border-left: 4px solid #dc2626 !important;">
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
                        Esta acci&oacute;n eliminar&aacute; <strong>todos los datos operacionales</strong> de
                        <strong>{{ $instance->nombre }}</strong> y reiniciar&aacute; el wizard de configuraci&oacute;n.
                    </p>
                    <div class="mt-2">
                        <small class="text-muted d-block"><i class="bi bi-x-circle-fill text-danger me-1"></i><strong>Se eliminar&aacute;n:</strong></small>
                        <div class="row row-cols-2 row-cols-md-3 g-1 mt-1">
                            @foreach([
                                'Ventas y pagos','Detalles de ventas','Compras y detalles',
                                'Cotizaciones','Conduces','Devoluciones',
                                'Gastos','Almacenes y movimientos','Cajas y sesiones',
                                'Productos','Categorías','Clientes',
                                'Proveedores','Sucursales','NCF / ECF / Secuencias',
                                'Mesas y reservaciones','Lavadero (citas/servicios)','Listas de precio',
                                'Parámetros del sistema','Logs de errores','Datos restaurante',
                            ] as $item)
                            <div class="col">
                                <span class="ui-badge ui-badge-danger fw-normal px-2 py-1 rounded-pill w-100 text-start">
                                    <i class="bi bi-dash-circle me-1 opacity-75"></i>{{ $item }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-3 rounded-3 mb-3" style="background: rgba(239,68,68,0.05); border: 1px solid rgba(239,68,68,0.15);">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-info-circle text-danger mt-1"></i>
                    <div class="small">
                        <strong class="text-danger">Se conservar&aacute;n:</strong> usuarios de la instancia, roles y permisos,
                        m&oacute;dulos habilitados, historial de pagos y configuraci&oacute;n general de la instancia.
                        <br class="mb-1">
                        <strong class="text-warning">⚠ El wizard de configuraci&oacute;n inicial se reiniciar&aacute;</strong> &mdash; el usuario
                        deber&aacute; completarlo nuevamente al ingresar.
                        <br class="mb-1">
                        <strong class="text-danger">No se puede deshacer.</strong> Realiza un backup antes de continuar.
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('owner.instances.clean', $instance) }}" 
                  onsubmit="return UI.confirm.delete('¿ESTÁS ABSOLUTAMENTE SEGURO? Esta acci&oacute;n eliminar&aacute; TODOS los datos de {{ $instance->nombre }}. No se puede deshacer.')">
                @csrf
                <div class="mb-3">
                    <label class="ui-label fw-bold text-danger">Escribe <strong>{{ $instance->nombre }}</strong> para confirmar:</label>
                    <div class="ui-input-group">
                        <span class="ui-input-group-text bg-white border-end-0"><i class="bi bi-key text-danger"></i></span>
                        <input type="text" name="confirm_name" class="ui-input border-start-0" 
                               placeholder="Escribe el nombre exacto de la instancia" 
                               autocomplete="off" required
                               oninput="document.getElementById('clean-btn').disabled = (this.value !== '{{ $instance->nombre }}')">
                    </div>
                </div>
                <button type="submit" id="clean-btn" class="ui-btn ui-btn-danger rounded-pill px-4 fw-bold" disabled>
                    <i class="bi bi-eraser me-2"></i>Limpiar Todos los Datos
                </button>
            </form>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Editando: {{ $instance->nombre }}</span>
        <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-pill px-5 fw-bold shadow-sm" style="background:#8b5cf6;border-color:#8b5cf6;color:#fff;">
            <i class="bi bi-save me-2"></i>Guardar Cambios
        </button>
    </div>
</div>
</div>
@endsection
