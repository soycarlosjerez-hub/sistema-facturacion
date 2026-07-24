@extends('layouts.app')

@section('title', 'Editar ' . $orden->codigo)

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .ui-page { --accent:#ef4444; --accent-rgb:239,68,68; --accent-hover:#dc2626; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#ef4444;--accent-rgb:239,68,68;--accent-hover:#dc2626;">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="ui-header" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar: {{ $orden->codigo }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-pencil me-1"></i>Modificar orden de emergencia
                        <span class="divider">·</span>
                        <span class="ui-badge {{ match($orden->estado) {
                            'reportada' => 'ui-badge-danger',
                            'asignada'  => 'ui-badge-warning',
                            'en_camino' => 'ui-badge-primary',
                            'en_lugar'  => 'ui-badge-info',
                            'resuelta'  => 'ui-badge-success',
                            'cerrada'   => 'ui-badge-neutral',
                            default     => 'ui-badge-neutral',
                        } }}" style="font-size:.72rem;">
                            {{ \App\Models\OrdenEmergencia::ESTADOS[$orden->estado] ?? $orden->estado }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('climatizacion.ordenes-emergencia.show', $orden) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-eye me-1"></i> Ver
                </a>
                <a href="{{ route('climatizacion.ordenes-emergencia.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================================
         FORMULARIO
         ============================================================ --}}
    <form action="{{ route('climatizacion.ordenes-emergencia.update', $orden) }}" method="POST" id="form-emergencia">
        @csrf @method('PUT')

        <div class="row g-4">

            {{-- Columna izquierda: datos principales --}}
            <div class="col-lg-7">

                <div class="ui-card" style="--delay:.1s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-info-circle"></i> Información de la Emergencia
                    </div>
                    <div class="ui-card-subtitle">Datos generales del reporte</div>
                    <div class="ui-card-body">
                        <div class="row g-3">

                            {{-- Código (readonly) --}}
                            <div class="col-md-6">
                                <label class="ui-label">Código</label>
                                <input type="text" class="ui-input" value="{{ $orden->codigo }}" disabled readonly
                                       style="background:#f8fafc;opacity:.8;">
                            </div>

                            {{-- Estado --}}
                            <div class="col-md-6">
                                <label class="ui-label">Estado <span class="text-danger">*</span></label>
                                <select name="estado" class="ui-select @error('estado') is-invalid @enderror" required>
                                    @foreach (\App\Models\OrdenEmergencia::ESTADOS as $key => $label)
                                        <option value="{{ $key }}" {{ old('estado', $orden->estado) === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Cliente --}}
                            <div class="col-md-6">
                                <label class="ui-label">Cliente <span class="text-danger">*</span></label>
                                <select name="cliente_id" class="ui-select @error('cliente_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach ($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('cliente_id', $orden->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nombre }} {{ $cliente->identificacion ? '('. $cliente->identificacion .')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cliente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Prioridad --}}
                            <div class="col-md-3">
                                <label class="ui-label">Prioridad <span class="text-danger">*</span></label>
                                <select name="prioridad" class="ui-select @error('prioridad') is-invalid @enderror" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach (\App\Models\OrdenEmergencia::PRIORIDADES as $key => $label)
                                        <option value="{{ $key }}" {{ old('prioridad', $orden->prioridad) === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('prioridad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Tipo Falla --}}
                            <div class="col-md-3">
                                <label class="ui-label">Tipo de Falla <span class="text-danger">*</span></label>
                                <select name="tipo_falla" class="ui-select @error('tipo_falla') is-invalid @enderror" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach (\App\Models\OrdenEmergencia::TIPOS_FALLA as $key => $label)
                                        <option value="{{ $key }}" {{ old('tipo_falla', $orden->tipo_falla) === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_falla') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Dirección --}}
                            <div class="col-md-8">
                                <label class="ui-label">Dirección <span class="text-danger">*</span></label>
                                <input type="text" name="direccion" class="ui-input @error('direccion') is-invalid @enderror"
                                       value="{{ old('direccion', $orden->direccion) }}" placeholder="Dirección del servicio" required>
                                @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Teléfono --}}
                            <div class="col-md-4">
                                <label class="ui-label">Teléfono de Contacto</label>
                                <input type="text" name="contacto_telefono" class="ui-input @error('contacto_telefono') is-invalid @enderror"
                                       value="{{ old('contacto_telefono', $orden->contacto_telefono) }}" placeholder="809-000-0000">
                                @error('contacto_telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Descripción --}}
                            <div class="col-12">
                                <label class="ui-label">Descripción <span class="text-danger">*</span></label>
                                <textarea name="descripcion" rows="4" class="ui-textarea @error('descripcion') is-invalid @enderror"
                                          placeholder="Describa el problema reportado..." required>{{ old('descripcion', $orden->descripcion) }}</textarea>
                                @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna derecha: asignación y costos --}}
            <div class="col-lg-5">

                {{-- Técnico --}}
                <div class="ui-card" style="--delay:.15s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-person-badge"></i> Asignación
                    </div>
                    <div class="ui-card-body">
                        <label class="ui-label">Técnico Asignado</label>
                        <select name="tecnico_id" class="ui-select @error('tecnico_id') is-invalid @enderror">
                            <option value="">Sin asignar...</option>
                            @foreach ($tecnicos ?? [] as $tecnico)
                                <option value="{{ $tecnico->id }}" {{ old('tecnico_id', $orden->tecnico_id) == $tecnico->id ? 'selected' : '' }}>
                                    {{ $tecnico->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('tecnico_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Al cambiar el técnico, la orden se actualizará automáticamente.
                        </small>
                    </div>
                </div>

                {{-- Costos --}}
                <div class="ui-card" style="--delay:.2s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-currency-dollar"></i> Costos
                    </div>
                    <div class="ui-card-body">
                        <div class="mb-3">
                            <label class="ui-label">Costo Estimado</label>
                            <div class="ui-input-group">
                                <span class="ui-input-group-text">RD$</span>
                                <input type="number" step="0.01" min="0" name="costo_estimado"
                                       class="ui-input @error('costo_estimado') is-invalid @enderror"
                                       value="{{ old('costo_estimado', $orden->costo_estimado) }}" placeholder="0.00">
                            </div>
                            @error('costo_estimado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="ui-label">Costo Final</label>
                            <div class="ui-input-group">
                                <span class="ui-input-group-text">RD$</span>
                                <input type="number" step="0.01" min="0" name="costo_final"
                                       class="ui-input @error('costo_final') is-invalid @enderror"
                                       value="{{ old('costo_final', $orden->costo_final) }}" placeholder="0.00">
                            </div>
                            @error('costo_final') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Información adicional --}}
                <div class="ui-card" style="--delay:.25s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-clock-history"></i> Auditoría
                    </div>
                    <div class="ui-card-body">
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Creado por</span>
                            <span class="ui-detail-value">{{ $orden->creadoPor?->name ?? '—' }}</span>
                        </div>
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Creado</span>
                            <span class="ui-detail-value">{{ $orden->created_at?->format('d/m/Y h:i A') ?? '—' }}</span>
                        </div>
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Actualizado</span>
                            <span class="ui-detail-value">{{ $orden->updated_at?->format('d/m/Y h:i A') ?? '—' }}</span>
                        </div>
                        @if($orden->sla_deadline)
                            <div class="ui-detail-row">
                                <span class="ui-detail-label">SLA Deadline</span>
                                <span class="ui-detail-value">{{ $orden->sla_deadline->format('d/m/Y h:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             STICKY BAR
             ============================================================ --}}
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('climatizacion.ordenes-emergencia.show', $orden) }}" class="ui-btn ui-btn-ghost rounded-pill">
                    Cancelar
                </a>
                <button type="submit" form="form-emergencia" class="ui-btn ui-btn-solid rounded-pill px-5">
                    <i class="bi bi-check-lg me-2"></i> Actualizar
                </button>
            </div>
        </div>

    </form>

    {{-- Spacer para el sticky bar --}}
    <div style="height:80px;"></div>

</div>
@endsection