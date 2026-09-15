@extends('layouts.app')
@section('title', 'Nueva Cita')

@push('styles')
@include('partials.premium-ui')
<style>
    .premium-header {
        background: linear-gradient(135deg, #8b5cf6, #a855f7, #7c3aed, #8b5cf6);
        background-size: 300% 300%;
        box-shadow: 0 8px 32px rgba(139,92,246,.25);
    }
    .premium-header::before {
        background:
            radial-gradient(circle at 30% 40%, rgba(255,255,255,.12) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.08) 0%, transparent 50%);
    }
    .service-card { border:2px solid #e5e7eb; border-radius:12px; padding:1rem; cursor:pointer; transition: all 0.2s; }
    .service-card:hover { border-color:#a855f7; background:#faf5ff; }
    .service-card.selected { border-color:#a855f7; background:#f3e8ff; box-shadow:0 0 0 3px rgba(168,85,247,0.15); }
    .premium-card .form-check-input:checked {
        background-color: #8b5cf6;
        border-color: #8b5cf6;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-calendar-plus"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Nueva Cita</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-plus-circle me-1"></i>Agenda una nueva cita para tatuaje
                    </small>
                </div>
            </div>
            <a href="{{ route('tattoo.citas.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

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

    <div class="premium-card" style="animation-delay:.1s;">
        <div class="card-accent purple"></div>
        <h5 class="premium-card-title"><i class="bi bi-calendar-plus icon-purple"></i> Información de la Cita</h5>
        <div class="card-body">
            <form id="citaForm" action="{{ route('tattoo.citas.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <h5 class="fw-bold"><i class="bi bi-person me-1"></i> Cliente</h5>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" class="form-select rounded-3" required>
                            <option value="">— Seleccionar cliente —</option>
                            @foreach($clientes as $cl)
                                <option value="{{ $cl->id }}" {{ old('cliente_id') == $cl->id ? 'selected' : '' }}>{{ $cl->nombre }} @if($cl->telefono) ({{ $cl->telefono }}) @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Diseño Base</label>
                        <select name="diseno_id" class="form-select rounded-3">
                            <option value="">— Sin diseño —</option>
                            @foreach($disenos as $d)
                                <option value="{{ $d->id }}" {{ old('diseno_id') == $d->id ? 'selected' : '' }}>{{ $d->titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Lugar del tatuaje</label>
                        <select name="lugar_tatuaje" class="form-select rounded-3">
                            <option value="">— Seleccionar —</option>
                            @foreach($lugares as $l)
                                <option value="{{ $l }}" {{ old('lugar_tatuaje') == $l ? 'selected' : '' }}>{{ ucfirst($l) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row g-3">
                    <h5 class="fw-bold"><i class="bi bi-brush me-1"></i> Servicio & Precio</h5>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Total Servicio <span class="text-danger">*</span></label>
                        <div class="input-group rounded-3">
                            <span class="input-group-text">RD$</span>
                            <input type="number" name="total_servicio" id="totalServicio" class="form-control" min="1" step="0.01" required value="{{ old('total_servicio') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">% Depósito</label>
                        <div class="input-group rounded-3">
                            <input type="number" name="deposito_pct" id="depositoPct" class="form-control" min="0" max="100" step="1" value="{{ old('deposito_pct', 30) }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Descuento</label>
                        <div class="input-group rounded-3">
                            <span class="input-group-text">RD$</span>
                            <input type="number" name="descuento_aplicado" class="form-control" min="0" step="0.01" value="{{ old('descuento_aplicado', 0) }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Tamaño aprox.</label>
                        <input type="text" name="tamanio_approx" class="form-control rounded-3" value="{{ old('tamanio_approx') }}" placeholder="Ej: 10cm">
                    </div>
                </div>
                <hr class="my-4">
                <div class="row g-3">
                    <h5 class="fw-bold"><i class="bi bi-person-badge me-1"></i> Artista & Agenda</h5>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Artista</label>
                        <select name="artista_id" class="form-select rounded-3">
                            <option value="">— Sin asignar —</option>
                            @foreach($artistas as $a)
                                <option value="{{ $a->id }}" {{ old('artista_id') == $a->id ? 'selected' : '' }}>{{ $a->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Fecha y Hora <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="fecha_hora_inicio" class="form-control rounded-3" required
                               value="{{ old('fecha_hora_inicio', now()->addDay()->setHour(10)->setMinute(0)->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Duración (min) <span class="text-danger">*</span></label>
                        <input type="number" name="duracion_min" class="form-control rounded-3" min="15" max="600" step="15" value="{{ old('duracion_min', 60) }}" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="revision_previa" class="form-check-input" id="revision_previa" value="1" {{ old('revision_previa') ? 'checked' : '' }}>
                            <label class="form-check-label" for="revision_previa">Revisión previa</label>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row g-3">
                    <h5 class="fw-bold"><i class="bi bi-chat me-1"></i> Notas</h5>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Notas para el cliente</label>
                        <textarea name="notas_cliente" class="form-control rounded-3" rows="2" maxlength="1000">{{ old('notas_cliente') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Notas internas</label>
                        <textarea name="notas_internas" class="form-control rounded-3" rows="2" maxlength="1000">{{ old('notas_internas') }}</textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div style="height: 80px;"></div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#8b5cf6;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva cita</span>
        </div>
        <div>
            <a href="{{ route('tattoo.citas.index') }}" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="citaForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Guardar Cita
            </button>
        </div>
    </div>
</div>
@endsection
