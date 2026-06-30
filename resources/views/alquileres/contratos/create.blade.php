@extends('layouts.app')
@section('title', 'Nuevo Contrato')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
    .form-floating-modern { position: relative; margin-bottom: 1rem; }
    .form-floating-modern .form-icon { position: absolute; top: 50%; left: 14px; transform: translateY(-50%); color: #94a3b8; z-index: 5; font-size: 1.1rem; pointer-events: none; }
    .form-floating-modern .form-control { padding-left: 42px; height: 50px; border-radius: 12px; border: 1.5px solid #e2e8f0; }
    .form-floating-modern .form-control:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.12); }
    .form-floating-modern .form-label-float { position: absolute; top: 50%; left: 42px; transform: translateY(-50%); color: #94a3b8; background: transparent; padding: 0 4px; }
    .form-floating-modern .form-control:focus + .form-label-float,
    .form-floating-modern .form-control:not(:placeholder-shown) + .form-label-float { top: -10px; left: 36px; font-size: .75rem; color: #f59e0b; background: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4" style="background: linear-gradient(135deg, #f59e0b, #f97316);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
            <div class="premium-avatar-circle"><i class="bi bi-file-earmark-plus"></i></div>
            <div><h4 class="fw-bold mb-1 text-white">Nuevo Contrato</h4><small class="text-white opacity-75">Crea un nuevo contrato de alquiler</small></div>
        </div>
    </div>

    <form action="{{ route('alquileres.contratos.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="premium-card">
                    <div class="card-accent amber"></div>
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4"><h6 class="fw-bold mb-0"><i class="bi bi-file-text text-primary me-2"></i>Detalles del Contrato</h6></div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="bi bi-house form-icon"></i>
                                    <select name="vivienda_id" id="vivienda_id" class="form-control" required>
                                        <option value="">Seleccionar vivienda...</option>
                                        @foreach($viviendas as $vivienda)
                                            <option value="{{ $vivienda->id }}" {{ old('vivienda_id')==$vivienda->id?'selected':'' }}>{{ $vivienda->nombre }} ({{ $vivienda->tipo }})</option>
                                        @endforeach
                                    </select>
                                    <label class="form-label-float" for="vivienda_id">Vivienda *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="bi bi-person form-icon"></i>
                                    <select name="inquilino_id" id="inquilino_id" class="form-control" required>
                                        <option value="">Seleccionar inquilino...</option>
                                        @foreach($inquilinos as $inquilino)
                                            <option value="{{ $inquilino->id }}" {{ old('inquilino_id')==$inquilino->id?'selected':'' }}>{{ $inquilino->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <label class="form-label-float" for="inquilino_id">Inquilino *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="bi bi-calendar form-icon"></i>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ old('fecha_inicio', date('Y-m-d')) }}" required>
                                    <label class="form-label-float" for="fecha_inicio">Fecha Inicio *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="bi bi-calendar-x form-icon"></i>
                                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ old('fecha_fin') }}">
                                    <label class="form-label-float" for="fecha_fin">Fecha Fin (opcional)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="bi bi-calendar-day form-icon"></i>
                                    <input type="number" name="dia_pago" id="dia_pago" class="form-control" value="{{ old('dia_pago', 1) }}" min="1" max="31">
                                    <label class="form-label-float" for="dia_pago">D�a de Pago</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="premium-card">
                    <div class="card-accent amber"></div>
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4"><h6 class="fw-bold mb-0"><i class="bi bi-cash-coin text-primary me-2"></i>Valores</h6></div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-currency-dollar form-icon"></i>
                                    <input type="number" step="0.01" name="monto_alquiler" id="monto_alquiler" class="form-control" value="{{ old('monto_alquiler') }}" placeholder=" " required>
                                    <label class="form-label-float" for="monto_alquiler">Monto Alquiler *</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-shield-check form-icon"></i>
                                    <input type="number" step="0.01" name="monto_deposito" id="monto_deposito" class="form-control" value="{{ old('monto_deposito', 0) }}" placeholder=" ">
                                    <label class="form-label-float" for="monto_deposito">Dep�sito</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="deposito_pagado" id="deposito_pagado" class="form-check-input" value="1" {{ old('deposito_pagado')?'checked':'' }}>
                                    <label class="form-check-label" for="deposito_pagado">Dep�sito ya pagado</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" style="background:linear-gradient(135deg,#f59e0b,#f97316);border:0;"><i class="bi bi-save me-1"></i>Crear Contrato</button>
            <a href="{{ route('alquileres.contratos.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
        </div>
    </form>
</div>
@endsection
