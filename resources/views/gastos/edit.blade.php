@extends('layouts.app')

@section('title', 'Editar Gasto')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-pencil-square text-warning me-2"></i>
                Editar Gasto
            </h2>
            <p class="text-muted mb-0">{{ $gasto->descripcion }}</p>
        </div>
        <a href="{{ route('gastos.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('gastos.update', $gasto) }}">
                @csrf
                @method('PUT')

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

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-warning rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Actualizar Gasto
                    </button>
                    <a href="{{ route('gastos.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
