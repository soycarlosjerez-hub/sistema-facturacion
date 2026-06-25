@extends('layouts.app')

@section('title', 'Editar Gasto')

@push('styles')
@include('partials.premium-ui')
<style>
/* Gastos form-specific styles */
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Editar Gasto</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-pencil me-1"></i>
                        {{ $gasto->descripcion }}
                    </small>
                </div>
            </div>
            <a href="{{ route('gastos.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
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

        <div class="premium-card" style="animation-delay:.1s;">
            <div class="card-accent green"></div>
            <div class="premium-card-title">
                <i class="bi bi-info-circle icon-green"></i>
                Datos del Gasto
            </div>
            <div class="premium-card-subtitle">Actualiza la información del gasto</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="mb-0">
                            <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                                   value="{{ old('descripcion', $gasto->descripcion) }}" required maxlength="500">
                            @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-0">
                            <label for="monto" class="form-label">Monto (RD$) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">RD$</span>
                                <input type="number" step="0.01" min="0.01" name="monto" id="monto"
                                       class="form-control @error('monto') is-invalid @enderror"
                                       value="{{ old('monto', $gasto->monto) }}" required>
                            </div>
                            @error('monto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-lg-4">
                        <div class="mb-0">
                            <label for="categoria" class="form-label">Categoría</label>
                            <select name="categoria" id="categoria" class="form-select @error('categoria') is-invalid @enderror">
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
                            <label for="metodo_pago" class="form-label">Método de Pago</label>
                            <select name="metodo_pago" id="metodo_pago" class="form-select @error('metodo_pago') is-invalid @enderror">
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
                            <label for="fecha_gasto" class="form-label">Fecha del Gasto <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_gasto" id="fecha_gasto" class="form-control @error('fecha_gasto') is-invalid @enderror"
                                   value="{{ old('fecha_gasto', $gasto->fecha_gasto->format('Y-m-d')) }}" required>
                            @error('fecha_gasto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-lg-6">
                        <div class="mb-0">
                            <label for="comprobante" class="form-label">N° Comprobante</label>
                            <input type="text" name="comprobante" id="comprobante" class="form-control @error('comprobante') is-invalid @enderror"
                                   value="{{ old('comprobante', $gasto->comprobante) }}" maxlength="100" placeholder="Factura o recibo #">
                            @error('comprobante') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-0">
                            <label for="notas" class="form-label">Notas</label>
                            <textarea name="notas" id="notas" rows="2" class="form-control @error('notas') is-invalid @enderror"
                                      maxlength="2000" placeholder="Información adicional...">{{ old('notas', $gasto->notas) }}</textarea>
                            @error('notas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div style="height: 80px;"></div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-end align-items-center">
        <a href="{{ route('gastos.index') }}" class="btn-cancel me-2">Cancelar</a>
        <button type="submit" form="gastoForm" class="btn-save">
            <i class="bi bi-check-lg me-2"></i>Actualizar Gasto
        </button>
    </div>
</div>
@endsection
