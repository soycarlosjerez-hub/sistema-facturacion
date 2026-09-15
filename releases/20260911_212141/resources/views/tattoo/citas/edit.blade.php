@extends('layouts.app')

@section('title', 'Editar Cita')

@section('content')
<div class="container-fluid px-4">
    <a href="{{ route('tattoo.citas.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill mb-3">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-transparent pt-4 px-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-pencil me-2"></i>Editar Cita #{{ $cita->id }}</h4>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('tattoo.citas.update', $cita) }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <h5 class="fw-bold"><i class="bi bi-person me-1"></i> Cliente</h5>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" class="form-select rounded-3" required>
                            <option value="">— Seleccionar cliente —</option>
                            @foreach($clientes as $cl)
                                <option value="{{ $cl->id }}" {{ old('cliente_id', $cita->cliente_id) == $cl->id ? 'selected' : '' }}>{{ $cl->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Diseño Base</label>
                        <select name="diseno_id" class="form-select rounded-3">
                            <option value="">— Sin diseño —</option>
                            @foreach($disenos as $d)
                                <option value="{{ $d->id }}" {{ old('diseno_id', $cita->diseno_id) == $d->id ? 'selected' : '' }}>{{ $d->titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Lugar del tatuaje</label>
                        <select name="lugar_tatuaje" class="form-select rounded-3">
                            <option value="">— Seleccionar —</option>
                            @foreach($lugares as $l)
                                <option value="{{ $l }}" {{ old('lugar_tatuaje', $cita->lugar_tatuaje) == $l ? 'selected' : '' }}>{{ ucfirst($l) }}</option>
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
                            <input type="number" name="total_servicio" class="form-control" min="1" step="0.01" required value="{{ old('total_servicio', $cita->total_servicio) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">% Depósito</label>
                        <div class="input-group rounded-3">
                            <input type="number" name="deposito_pct" class="form-control" min="0" max="100" step="1" value="{{ old('deposito_pct', $cita->deposito_pct) }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Descuento</label>
                        <div class="input-group rounded-3">
                            <span class="input-group-text">RD$</span>
                            <input type="number" name="descuento_aplicado" class="form-control" min="0" step="0.01" value="{{ old('descuento_aplicado', $cita->descuento_aplicado) }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Tamaño aprox.</label>
                        <input type="text" name="tamanio_approx" class="form-control rounded-3" value="{{ old('tamanio_approx', $cita->tamanio_approx) }}">
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
                                <option value="{{ $a->id }}" {{ old('artista_id', $cita->artista_id) == $a->id ? 'selected' : '' }}>{{ $a->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Fecha y Hora <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="fecha_hora_inicio" class="form-control rounded-3" required
                               value="{{ old('fecha_hora_inicio', $cita->fecha_hora_inicio?->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Duración (min) <span class="text-danger">*</span></label>
                        <input type="number" name="duracion_min" class="form-control rounded-3" min="15" max="600" step="15" value="{{ old('duracion_min', $cita->duracion_min) }}" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="revision_previa" class="form-check-input" value="1" {{ old('revision_previa', $cita->revision_previa) ? 'checked' : '' }}>
                            <label class="form-check-label">Revisión previa</label>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row g-3">
                    <h5 class="fw-bold"><i class="bi bi-chat me-1"></i> Notas</h5>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Notas para el cliente</label>
                        <textarea name="notas_cliente" class="form-control rounded-3" rows="2" maxlength="1000">{{ old('notas_cliente', $cita->notas_cliente) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Notas internas</label>
                        <textarea name="notas_internas" class="form-control rounded-3" rows="2" maxlength="1000">{{ old('notas_internas', $cita->notas_internas) }}</textarea>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Actualizar Cita</button>
                    <a href="{{ route('tattoo.citas.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
