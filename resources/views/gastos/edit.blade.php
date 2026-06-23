@extends('layouts.app')

@section('title', 'Editar Gasto')

@push('styles')
<style>
.premium-header {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    border-radius: 1rem;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.4);
    position: relative;
    overflow: hidden;
}
.premium-header::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
}
.sticky-save-bar {
    position: fixed;
    bottom: 0;
    left: var(--sidebar-width, 280px);
    right: 0;
    background: #fff;
    border-top: 2px solid #ef4444;
    padding: 0.75rem 1.5rem;
    z-index: 1050;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
}
.sticky-save-bar .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
body.dark-mode .sticky-save-bar {
    background: #0f172a;
    border-top-color: #f87171;
}
@media (max-width: 991.98px) {
    .sticky-save-bar { left: 0; }
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="premium-header">
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 1;">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-pencil-square me-2"></i>Editar Gasto</h3>
                <p class="mb-0 opacity-75">{{ $gasto->descripcion }}</p>
            </div>
            <a href="{{ route('gastos.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px);">
        <div class="card-header bg-white border-bottom border-light p-4">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle text-danger me-2"></i>Detalles del Gasto</h5>
        </div>

        <form id="gastoForm" method="POST" action="{{ route('gastos.update', $gasto) }}">
            @csrf
            @method('PUT')

            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4 p-3 rounded-3" style="background: rgba(239, 68, 68, 0.08); border-left: 4px solid #ef4444;">
                    <i class="bi bi-info-circle text-danger me-2"></i>
                    <span class="text-muted small">Editando gasto</span>
                </div>

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label for="descripcion" class="form-label small fw-semibold">Descripción <span class="text-danger">*</span></label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" 
                                   value="{{ old('descripcion', $gasto->descripcion) }}" required maxlength="500">
                            @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="monto" class="form-label small fw-semibold">Monto (RD$) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">RD$</span>
                                <input type="number" step="0.01" min="0.01" name="monto" id="monto" 
                                       class="form-control @error('monto') is-invalid @enderror" 
                                       value="{{ old('monto', $gasto->monto) }}" required>
                                @error('monto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="categoria" class="form-label small fw-semibold">Categoría</label>
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
                        <div class="mb-3">
                            <label for="metodo_pago" class="form-label small fw-semibold">Método de Pago</label>
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
                        <div class="mb-3">
                            <label for="fecha_gasto" class="form-label small fw-semibold">Fecha del Gasto <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_gasto" id="fecha_gasto" class="form-control @error('fecha_gasto') is-invalid @enderror" 
                                   value="{{ old('fecha_gasto', $gasto->fecha_gasto->format('Y-m-d')) }}" required>
                            @error('fecha_gasto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="comprobante" class="form-label small fw-semibold">N° Comprobante</label>
                            <input type="text" name="comprobante" id="comprobante" class="form-control @error('comprobante') is-invalid @enderror" 
                                   value="{{ old('comprobante', $gasto->comprobante) }}" maxlength="100" placeholder="Factura o recibo #">
                            @error('comprobante') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="notas" class="form-label small fw-semibold">Notas</label>
                            <textarea name="notas" id="notas" rows="2" class="form-control @error('notas') is-invalid @enderror" 
                                      maxlength="2000" placeholder="Información adicional...">{{ old('notas', $gasto->notas) }}</textarea>
                            @error('notas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div style="height: 80px;"></div>
</div>

<div class="sticky-save-bar">
    <div class="d-flex justify-content-end align-items-center">
        <a href="{{ route('gastos.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancelar</a>
        <button type="submit" form="gastoForm" class="btn btn-primary rounded-pill px-5 shadow fw-bold" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none;">
            <i class="bi bi-check-lg me-2"></i>Actualizar Gasto
        </button>
    </div>
</div>
@endsection
