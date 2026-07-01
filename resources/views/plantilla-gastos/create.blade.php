@extends('layouts.app')

@section('title', 'Nueva Plantilla de Gasto')

@push('styles')
@include('partials.premium-ui')
<style>
.form-section-title {
    font-size: .8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #64748b;
    margin-bottom: .75rem;
    padding-bottom: .5rem;
    border-bottom: 1px solid #e2e8f0;
}
body.dark-mode .form-section-title {
    color: #94a3b8;
    border-color: #1e293b;
}
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
                    <i class="bi bi-file-earmark-richtext"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Nueva Plantilla de Gasto</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-plus-circle me-1"></i>
                        Define datos reutilizables para gastos recurrentes
                    </small>
                </div>
            </div>
        </div>
    </div>

    <form id="plantillaForm" action="{{ route('plantilla-gastos.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="premium-card h-100">
                    <div class="card-accent green"></div>
                    <div class="card-body p-4">
                        <div class="form-section-title">Información Principal</div>

                        <div class="row g-3">
                            <div class="col-lg-8">
                                <div class="mb-0">
                                    <label for="nombre" class="form-label">Nombre de la Plantilla <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                           value="{{ old('nombre') }}" placeholder="Ej: Luz eléctrica mensual" required>
                                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-0">
                                    <label for="activo" class="form-label">Estado</label>
                                    <select name="activo" id="activo" class="form-select @error('activo') is-invalid @enderror">
                                        <option value="1" {{ old('activo', 1) == 1 ? 'selected' : '' }}>Activa</option>
                                        <option value="0" {{ old('activo') == 0 ? 'selected' : '' }}>Inactiva</option>
                                    </select>
                                    @error('activo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="mb-0">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea name="descripcion" id="descripcion" rows="2" class="form-control @error('descripcion') is-invalid @enderror"
                                          maxlength="500" placeholder="Detalle breve de esta plantilla...">{{ old('descripcion') }}</textarea>
                                @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="form-section-title">Detalles del Gasto</div>

                        <div class="row g-3">
                            <div class="col-lg-4">
                                <div class="mb-0">
                                    <label for="categoria" class="form-label">Categoría</label>
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
                                <div class="mb-0">
                                    <label for="metodo_pago" class="form-label">Método de Pago</label>
                                    <select name="metodo_pago" id="metodo_pago" class="form-select @error('metodo_pago') is-invalid @enderror">
                                        <option value="">Seleccionar...</option>
                                        @foreach($metodosPago as $key => $label)
                                            <option value="{{ $key }}" {{ old('metodo_pago') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('metodo_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-0">
                                    <label for="comprobante" class="form-label">N° Comprobante</label>
                                    <input type="text" name="comprobante" id="comprobante" class="form-control @error('comprobante') is-invalid @enderror"
                                           value="{{ old('comprobante') }}" maxlength="100" placeholder="Ej: FAC-0001">
                                    @error('comprobante') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="mb-0">
                                <label for="notas" class="form-label">Notas</label>
                                <textarea name="notas" id="notas" rows="2" class="form-control @error('notas') is-invalid @enderror"
                                          maxlength="2000" placeholder="Observaciones adicionales...">{{ old('notas') }}</textarea>
                                @error('notas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="premium-card h-100">
                    <div class="card-accent green"></div>
                    <div class="card-body p-4">
                        <div class="form-section-title">Ayuda</div>
                        <div style="font-size:.875rem;color:#64748b;line-height:1.7;">
                            <p class="mb-2"><strong class="text-dark">¿Para qué sirve?</strong></p>
                            <p class="mb-3">Las plantillas te permiten guardar datos comunes de gastos recurrentes (luz, agua, alquiler, etc.) para registrarlos rápidamente sin volver a escribir toda la información.</p>
                            
                            <p class="mb-2"><strong class="text-dark">Uso recomendado:</strong></p>
                            <ul class="mb-0 ps-3" style="font-size:.875rem;color:#64748b;">
                                <li>Crea una plantilla por tipo de gasto fijo</li>
                                <li>Incluye el número de comprobante habitual</li>
                                <li>Usa notas para detalles específicos</li>
                            </ul>
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
        <a href="{{ route('plantilla-gastos.index') }}" class="btn-cancel me-2">Cancelar</a>
        <button type="submit" form="plantillaForm" class="btn-save">
            <i class="bi bi-check-lg me-2"></i>Guardar Plantilla
        </button>
    </div>
</div>
@endsection
