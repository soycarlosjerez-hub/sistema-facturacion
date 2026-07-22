@extends('layouts.app')
@section('title', "Editar Contrato")

@push('styles')
@include('partials.premium-ui')
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
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;--delay:0s;">
    <div class="ui-header mb-4" style="background: linear-gradient(135deg, #f59e0b, #f97316);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-pencil-square"></i></div>
            <div><h4 class="ui-header-title">Contrato #{{ $contrato->id }}</h4><div class="ui-header-meta">Editando contrato de alquiler</div></div>
        </div>
    </div>

    <form action="{{ route('alquileres.contratos.update', $contrato) }}" method="POST">
        @csrf @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ui-card" style="--delay:.1s;">
                    <div class="ui-card-accent amber"></div>
                    <div class="ui-card-body p-4">
                        <div class="ui-card-title"><i class="bi bi-file-text"></i>Detalles del Contrato</div>
                        <div class="ui-card-subtitle"></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vivienda_id" class="ui-label">Vivienda <span class="text-danger">*</span></label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-house"></i></span>
                                        <select name="vivienda_id" id="vivienda_id" class="ui-select" required>
                                            @foreach($viviendas as $vivienda)
                                                <option value="{{ $vivienda->id }}" {{ old('vivienda_id', $contrato->vivienda_id)==$vivienda->id?'selected':'' }}>{{ $vivienda->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="inquilino_id" class="ui-label">Inquilino <span class="text-danger">*</span></label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-person"></i></span>
                                        <select name="inquilino_id" id="inquilino_id" class="ui-select" required>
                                            @foreach($inquilinos as $inquilino)
                                                <option value="{{ $inquilino->id }}" {{ old('inquilino_id', $contrato->inquilino_id)==$inquilino->id?'selected':'' }}>{{ $inquilino->nombre }}</option>\n                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fecha_inicio" class="ui-label">Fecha Inicio <span class="text-danger">*</span></label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-calendar"></i></span>
                                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="ui-input" value="{{ old('fecha_inicio', $contrato->fecha_inicio->format('Y-m-d')) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fecha_fin" class="ui-label">Fecha Fin</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-calendar-x"></i></span>
                                        <input type="date" name="fecha_fin" id="fecha_fin" class="ui-input" value="{{ old('fecha_fin', $contrato->fecha_fin?->format('Y-m-d')) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="estado" class="ui-label">Estado</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-info-circle"></i></span>
                                        <select name="estado" id="estado" class="ui-select" required>
                                            <option value="activo" {{ old('estado',$contrato->estado)=='activo'?'selected':'' }}>Activo</option>
                                            <option value="vencido" {{ old('estado',$contrato->estado)=='vencido'?'selected':'' }}>Vencido</option>
                                            <option value="cancelado" {{ old('estado',$contrato->estado)=='cancelado'?'selected':'' }}>Cancelado</option>
                                            <option value="finalizado" {{ old('estado',$contrato->estado)=='finalizado'?'selected':'' }}>Finalizado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="dia_pago" class="ui-label">Día de Pago</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-calendar-day"></i></span>
                                        <input type="number" name="dia_pago" id="dia_pago" class="ui-input" value="{{ old('dia_pago', $contrato->dia_pago) }}" min="1" max="31">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ui-card" style="--delay:.2s;">
                    <div class="ui-card-accent amber"></div>
                    <div class="ui-card-body p-4">
                        <div class="ui-card-title"><i class="bi bi-cash-coin"></i>Valores</div>
                        <div class="ui-card-subtitle"></div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="monto_alquiler" class="ui-label">Monto Alquiler <span class="text-danger">*</span></label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                        <input type="number" step="0.01" name="monto_alquiler" id="monto_alquiler" class="ui-input" value="{{ old('monto_alquiler', $contrato->monto_alquiler) }}" placeholder=" " required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="monto_deposito" class="ui-label">Depósito</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-shield-check"></i></span>
                                        <input type="number" step="0.01" name="monto_deposito" id="monto_deposito" class="ui-input" value="{{ old('monto_deposito', $contrato->monto_deposito) }}" placeholder=" ">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="deposito_pagado" id="deposito_pagado" class="form-check-input" value="1" {{ old('deposito_pagado',$contrato->deposito_pagado)?'checked':'' }}>
                                    <label class="form-check-label" for="deposito_pagado">Depósito pagado</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-5 fw-bold shadow-sm" style="background:linear-gradient(135deg,#f59e0b,#f97316);border:0;"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
            <a href="{{ route('alquileres.contratos.index') }}" class="ui-btn ui-btn-ghost rounded-pill px-4">Cancelar</a>
        </div>
    </form>
</div>
@endsection
