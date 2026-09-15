@extends('layouts.app')

@section('title', 'Editar Gasto')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Gasto</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-pencil me-1"></i>
                        {{ $gasto->descripcion }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('gastos.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any() || session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <div class="d-flex">
                <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">No se pudo actualizar el gasto</h6>
                    <ul class="mb-0 ps-3">
                        @if(session('error'))<li>{{ session('error') }}</li>@endif
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form id="gastoForm" method="POST" action="{{ route('gastos.update', $gasto) }}">
        @csrf
        @method('PUT')

        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title">
                <i class="bi bi-info-circle"></i>
                Datos del Gasto
            </div>
            <div class="ui-card-subtitle">Actualiza la información del gasto</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="mb-0">
                            <label for="descripcion" class="ui-label">Descripción <span class="text-danger">*</span></label>
                            <input type="text" name="descripcion" id="descripcion" class="ui-input @error('descripcion') is-invalid @enderror"
                                   value="{{ old('descripcion', $gasto->descripcion) }}" required maxlength="500">
                            @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-0">
                            <label for="monto" class="ui-label">Monto (RD$) <span class="text-danger">*</span></label>
                            <div class="ui-input-group">
                                <span class="ui-input-group-text">RD$</span>
                                <input type="number" step="0.01" min="0.01" name="monto" id="monto"
                                       class="ui-input @error('monto') is-invalid @enderror"
                                       value="{{ old('monto', $gasto->monto) }}" required>
                            </div>
                            @error('monto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-lg-4">
                        <div class="mb-0">
                            <label for="categoria" class="ui-label">Categoría</label>
                            <select name="categoria" id="categoria" class="ui-select @error('categoria') is-invalid @enderror">
                                <option value="">Seleccionar categoría...</option>
                                @foreach($categorias as $key => $label)
                                    <option value="{{ $key }}" {{ (old('categoria', $gasto->categoria) === $key) ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('categoria') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-0">
                            <label for="planta_gasto_id" class="ui-label">Plantilla de Gasto</label>
                            <select name="planta_gasto_id" id="planta_gasto_id" class="ui-select @error('planta_gasto_id') is-invalid @enderror">
                                <option value="">Sin plantilla</option>
                                @foreach($plantillas as $planta)
                                    <option value="{{ $planta->id }}" {{ old('planta_gasto_id', $gasto->planta_gasto_id) == $planta->id ? 'selected' : '' }}>
                                        {{ $planta->codigo }} - {{ $planta->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                            @error('planta_gasto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-0">
                            <label for="metodo_pago" class="ui-label">Método de Pago</label>
                            <select name="metodo_pago" id="metodo_pago" class="ui-select @error('metodo_pago') is-invalid @enderror">
                                <option value="">Seleccionar...</option>
                                <option value="efectivo" {{ old('metodo_pago', $gasto->metodo_pago) === 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="tarjeta" {{ old('metodo_pago', $gasto->metodo_pago) === 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="transferencia" {{ old('metodo_pago', $gasto->metodo_pago) === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="cheque" {{ old('metodo_pago', $gasto->metodo_pago) === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="otro" {{ old('metodo_pago', $gasto->metodo_pago) === 'otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            @error('metodo_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-0">
                            <label for="proveedor_id" class="ui-label">Proveedor</label>
                            <select name="proveedor_id" id="proveedor_id" class="ui-select @error('proveedor_id') is-invalid @enderror">
                                <option value="">Seleccionar proveedor...</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $gasto->proveedor_id) == $proveedor->id ? 'selected' : '' }}>{{ $proveedor->nombre }}</option>
                                @endforeach
                            </select>
                            @error('proveedor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-0">
                            <label for="fecha_gasto" class="ui-label">Fecha del Gasto <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_gasto" id="fecha_gasto" class="ui-input @error('fecha_gasto') is-invalid @enderror"
                                   value="{{ old('fecha_gasto', $gasto->fecha_gasto->format('Y-m-d')) }}" required>
                            @error('fecha_gasto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-lg-6">
                        <div class="mb-0">
                            <label for="comprobante" class="ui-label">N° Comprobante</label>
                            <input type="text" name="comprobante" id="comprobante" class="ui-input @error('comprobante') is-invalid @enderror"
                                   value="{{ old('comprobante', $gasto->comprobante) }}" maxlength="100" placeholder="Factura o recibo #">
                            @error('comprobante') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-0">
                            <label for="notas" class="ui-label">Notas</label>
                            <textarea name="notas" id="notas" rows="2" class="ui-textarea @error('notas') is-invalid @enderror"
                                      maxlength="2000" placeholder="Información adicional...">{{ old('notas', $gasto->notas) }}</textarea>
                            @error('notas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('gastos.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
        <button type="submit" form="gastoForm" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>Actualizar Gasto
        </button>
    </div>
</div>
@endsection
