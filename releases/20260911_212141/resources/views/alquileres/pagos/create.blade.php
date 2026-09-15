@extends('layouts.app')
@section('title', 'Registrar Pago')

@push('styles')
@include('partials.premium-ui')
<style>
    .form-floating-modern { position: relative; margin-bottom: 1rem; }
    .form-floating-modern .form-icon { position: absolute; top: 50%; left: 14px; transform: translateY(-50%); color: #94a3b8; z-index: 5; font-size: 1.1rem; pointer-events: none; }
    .form-floating-modern .form-control { padding-left: 42px; height: 50px; border-radius: 12px; border: 1.5px solid #e2e8f0; }
    .form-floating-modern .form-control:focus { border-color: #f43f5e; box-shadow: 0 0 0 3px rgba(244,63,94,.12); }
    .form-floating-modern .form-label-float { position: absolute; top: 50%; left: 42px; transform: translateY(-50%); color: #94a3b8; background: transparent; padding: 0 4px; }
    .form-floating-modern .form-control:focus + .form-label-float,
    .form-floating-modern .form-control:not(:placeholder-shown) + .form-label-float { top: -10px; left: 36px; font-size: .75rem; color: #f43f5e; background: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4" style="background: linear-gradient(135deg, #f43f5e, #ec4899);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
            <div class="premium-avatar-circle"><i class="bi bi-cash-stack"></i></div>
            <div><h4 class="fw-bold mb-1 text-white">Registrar Pago</h4><small class="text-white opacity-75">Registra un pago de alquiler</small></div>
        </div>
    </div>

    <form action="{{ route('alquileres.pagos.store') }}" method="POST" id="instanceForm">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="premium-card">
                    <div class="card-accent red"></div>
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4"><h6 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Datos del Pago</h6></div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-file-earmark-text form-icon"></i>
                                    <select name="contrato_id" id="contrato_id" class="form-control" required>
                                        <option value="">Seleccionar contrato...</option>
                                        @foreach($contratos as $contrato)
                                            <option value="{{ $contrato->id }}" {{ old('contrato_id')==$contrato->id?'selected':'' }}>
                                                {{ $contrato->vivienda->nombre }} - {{ $contrato->inquilino->nombre }} (RD$ {{ number_format($contrato->monto_alquiler, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label class="form-label-float" for="contrato_id">Contrato *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="bi bi-currency-dollar form-icon"></i>
                                    <input type="number" step="0.01" name="monto" id="monto" class="form-control" value="{{ old('monto') }}" placeholder=" " required>
                                    <label class="form-label-float" for="monto">Monto *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="bi bi-calendar form-icon"></i>
                                    <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" value="{{ old('fecha_pago', date('Y-m-d')) }}" required>
                                    <label class="form-label-float" for="fecha_pago">Fecha de Pago *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="bi bi-calendar-month form-icon"></i>
                                    <select name="mes_cobrado" id="mes_cobrado" class="form-control" required>
                                        @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $index => $mes)
                                            <option value="{{ $index+1 }}" {{ old('mes_cobrado',date('n'))==$index+1?'selected':'' }}>{{ $mes }}</option>
                                        @endforeach
                                    </select>
                                    <label class="form-label-float" for="mes_cobrado">Mes Cobrado *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="bi bi-calendar-year form-icon"></i>
                                    <input type="number" name="ano_cobrado" id="ano_cobrado" class="form-control" value="{{ old('ano_cobrado', date('Y')) }}" min="2020" max="2100" required>
                                    <label class="form-label-float" for="ano_cobrado">A�o *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="bi bi-credit-card form-icon"></i>
                                    <select name="metodo_pago" id="metodo_pago" class="form-control" required>
                                        <option value="efectivo" {{ old('metodo_pago')=='efectivo'?'selected':'' }}>Efectivo</option>
                                        <option value="tarjeta" {{ old('metodo_pago')=='tarjeta'?'selected':'' }}>Tarjeta</option>
                                        <option value="transferencia" {{ old('metodo_pago')=='transferencia'?'selected':'' }}>Transferencia</option>
                                        <option value="deposito" {{ old('metodo_pago')=='deposito'?'selected':'' }}>Dep�sito</option>
                                        <option value="otro" {{ old('metodo_pago')=='otro'?'selected':'' }}>Otro</option>
                                    </select>
                                    <label class="form-label-float" for="metodo_pago">M�todo de Pago *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="bi bi-receipt form-icon"></i>
                                    <input type="text" name="recibo_numero" id="recibo_numero" class="form-control" value="{{ old('recibo_numero') }}" placeholder=" ">
                                    <label class="form-label-float" for="recibo_numero">N�mero de Recibo</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-sticky form-icon" style="top:14px;"></i>
                                    <textarea name="notas" id="notas" class="form-control" placeholder=" " rows="2">{{ old('notas') }}</textarea>
                                    <label class="form-label-float" for="notas">Notas</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div style="height: 80px;"></div>
</div>
@endsection

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#f43f5e;"></i>
            <span class="fw-semibold d-none d-sm-inline">Registrando pago</span>
        </div>
        <div>
            <a href="{{ route('alquileres.pagos.index') }}" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="instanceForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Registrar Pago
            </button>
        </div>
    </div>
</div>
