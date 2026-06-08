@extends('layouts.app')

@section('title', 'Registrar Gasto')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            @if (session('error'))
                <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="bi bi-wallet2 text-warning me-2"></i>
                        Nuevo Gasto
                    </h2>
                    <p class="text-muted mb-0">Añade un nuevo gasto operativo</p>
                </div>
                <a href="{{ route('gastos.index') }}" class="btn btn-light rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <form method="POST" action="{{ route('gastos.store') }}">
                    @csrf
                    <div class="card-header bg-light border-bottom border-light p-4">
                        <h5 class="mb-0 fw-semibold"><i class="bi bi-wallet2 me-2"></i>Detalles del Gasto</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label small fw-semibold">Descripción <span class="text-danger">*</span></label>
                                    <input type="text" name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" 
                                           value="{{ old('descripcion') }}" required maxlength="500" placeholder="Ej: Pago de electricidad">
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
                                               value="{{ old('monto') }}" required>
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
                                            <option value="{{ $key }}" {{ old('categoria') === $key ? 'selected' : '' }}>{{ $label }}</option>
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
                                        <option value="efectivo" {{ old('metodo_pago') === 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                        <option value="tarjeta" {{ old('metodo_pago') === 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                        <option value="transferencia" {{ old('metodo_pago') === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                        <option value="cheque" {{ old('metodo_pago') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="otro" {{ old('metodo_pago') === 'otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('metodo_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="fecha_gasto" class="form-label small fw-semibold">Fecha del Gasto <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_gasto" id="fecha_gasto" class="form-control @error('fecha_gasto') is-invalid @enderror" 
                                           value="{{ old('fecha_gasto', date('Y-m-d')) }}" required>
                                    @error('fecha_gasto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="comprobante" class="form-label small fw-semibold">N° Comprobante</label>
                                    <input type="text" name="comprobante" id="comprobante" class="form-control @error('comprobante') is-invalid @enderror" 
                                           value="{{ old('comprobante') }}" maxlength="100" placeholder="Factura o recibo #">
                                    @error('comprobante') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="notas" class="form-label small fw-semibold">Notas</label>
                                    <textarea name="notas" id="notas" rows="2" class="form-control @error('notas') is-invalid @enderror" 
                                              maxlength="2000" placeholder="Información adicional...">{{ old('notas') }}</textarea>
                                    @error('notas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <a href="{{ route('gastos.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-check-lg me-1"></i> Guardar Gasto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
