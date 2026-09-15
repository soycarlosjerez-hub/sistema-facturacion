@extends('layouts.app')
@section('title', 'Editar Contrato: '.$contrato->codigo)

@push('styles')
@include('partials.premium-ui')
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; }
body.dark-mode .ui-page { --accent: #22d3ee; --accent-rgb: 34,211,238; --accent-hover: #06b6d4; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    {{-- Header --}}
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar: {{ $contrato->codigo }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-wind me-1"></i>Climatización
                        <span class="mx-2">·</span>
                        <i class="bi bi-person me-1"></i>{{ $contrato->cliente?->nombre ?? 'Sin cliente' }}
                        <span class="mx-2">·</span>
                        <a href="{{ route('climatizacion.contratos.show', $contrato) }}" class="text-white-50 text-decoration-none">
                            <i class="bi bi-eye me-1"></i>Ver contrato
                        </a>
                        <span class="mx-2">·</span>
                        <a href="{{ route('climatizacion.contratos.index') }}" class="text-white-50 text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <span class="ui-badge ui-badge-{{ $contrato->estado === 'activo' ? 'success' : ($contrato->estado === 'borrador' ? 'neutral' : 'danger') }} rounded-pill">
                    {{ \App\Models\ContratoMantenimiento::ESTADOS[$contrato->estado] ?? $contrato->estado }}
                </span>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('climatizacion.contratos.update', $contrato) }}" method="POST" id="contratoForm">
        @csrf
        @method('PUT')

        {{-- Datos Generales --}}
        <div class="ui-card" style="--delay:.1s">
            <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
            <div class="ui-card-body">
                <h5 class="ui-card-title" style="padding:0;margin-bottom:1.25rem;">
                    <i class="bi bi-info-circle"></i> Datos Generales
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label" for="cliente_id">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" id="cliente_id" class="ui-select @error('cliente_id') is-invalid @enderror" required>
                            <option value="">Seleccionar cliente...</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id', $contrato->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre }} {{ $cliente->identificacion ? '- '.$cliente->identificacion : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="ui-label" for="tipo_periodicidad">Periodicidad <span class="text-danger">*</span></label>
                        <select name="tipo_periodicidad" id="tipo_periodicidad" class="ui-select @error('tipo_periodicidad') is-invalid @enderror" required>
                            <option value="">Seleccionar...</option>
                            @foreach(\App\Models\ContratoMantenimiento::PERIODICIDADES as $val => $label)
                                <option value="{{ $val }}" {{ old('tipo_periodicidad', $contrato->tipo_periodicidad) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tipo_periodicidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="ui-label" for="valor_mensual">Valor Mensual (RD$) <span class="text-danger">*</span></label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">RD$</span>
                            <input type="number" step="0.01" min="0" name="valor_mensual" id="valor_mensual"
                                   class="ui-input @error('valor_mensual') is-invalid @enderror"
                                   value="{{ old('valor_mensual', $contrato->valor_mensual) }}" placeholder="0.00" required>
                        </div>
                        @error('valor_mensual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Vigencia --}}
        <div class="ui-card" style="--delay:.15s">
            <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
            <div class="ui-card-body">
                <h5 class="ui-card-title" style="padding:0;margin-bottom:1.25rem;">
                    <i class="bi bi-calendar-range"></i> Vigencia
                </h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="ui-label" for="vigencia_desde">Vigencia Desde <span class="text-danger">*</span></label>
                        <input type="date" name="vigencia_desde" id="vigencia_desde"
                               class="ui-input @error('vigencia_desde') is-invalid @enderror"
                               value="{{ old('vigencia_desde', $contrato->vigencia_desde?->format('Y-m-d')) }}" required>
                        @error('vigencia_desde') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="vigencia_hasta">Vigencia Hasta <span class="text-danger">*</span></label>
                        <input type="date" name="vigencia_hasta" id="vigencia_hasta"
                               class="ui-input @error('vigencia_hasta') is-invalid @enderror"
                               value="{{ old('vigencia_hasta', $contrato->vigencia_hasta?->format('Y-m-d')) }}" required>
                        @error('vigencia_hasta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Cobertura y Visitas --}}
        <div class="ui-card" style="--delay:.2s">
            <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
            <div class="ui-card-body">
                <h5 class="ui-card-title" style="padding:0;margin-bottom:1.25rem;">
                    <i class="bi bi-shield-check"></i> Cobertura y Visitas
                </h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="ui-label" for="deducible">Deducible (RD$)</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">RD$</span>
                            <input type="number" step="0.01" min="0" name="deducible" id="deducible"
                                   class="ui-input @error('deducible') is-invalid @enderror"
                                   value="{{ old('deducible', $contrato->deducible) }}" placeholder="0.00">
                        </div>
                        @error('deducible') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="cobertura_maxima">Cobertura Máxima (RD$)</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">RD$</span>
                            <input type="number" step="0.01" min="0" name="cobertura_maxima" id="cobertura_maxima"
                                   class="ui-input @error('cobertura_maxima') is-invalid @enderror"
                                   value="{{ old('cobertura_maxima', $contrato->cobertura_maxima) }}" placeholder="0.00">
                        </div>
                        @error('cobertura_maxima') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 d-flex align-items-end pb-2">
                        <div class="form-check">
                            <input type="hidden" name="incluye_visitas" value="0">
                            <input type="checkbox" name="incluye_visitas" id="incluye_visitas" class="form-check-input"
                                   value="1" {{ old('incluye_visitas', $contrato->incluye_visitas) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="incluye_visitas">
                                <i class="bi bi-tools me-1"></i> Incluye Visitas
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4" id="visitasAnualesGroup" style="{{ old('incluye_visitas', $contrato->incluye_visitas) ? '' : 'display:none;' }}">
                        <label class="ui-label" for="num_visitas_anuales">Visitas Anuales</label>
                        <input type="number" min="0" name="num_visitas_anuales" id="num_visitas_anuales"
                               class="ui-input @error('num_visitas_anuales') is-invalid @enderror"
                               value="{{ old('num_visitas_anuales', $contrato->num_visitas_anuales) }}" placeholder="0">
                        @error('num_visitas_anuales') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Equipos Cubiertos --}}
        <div class="ui-card" style="--delay:.25s">
            <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
            <div class="ui-card-body">
                <h5 class="ui-card-title" style="padding:0;margin-bottom:1.25rem;">
                    <i class="bi bi-cpu"></i> Equipos Cubiertos
                </h5>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="ui-label" for="equipos_cubiertos">Descripción de los equipos cubiertos</label>
                        @php
                            $equiposValue = old('equipos_cubiertos', $contrato->equipos_cubiertos);
                            if (is_array($equiposValue)) $equiposValue = implode("\n", $equiposValue);
                        @endphp
                        <textarea name="equipos_cubiertos" id="equipos_cubiertos"
                                  class="ui-textarea @error('equipos_cubiertos') is-invalid @enderror"
                                  rows="4" placeholder="Detalle aquí los equipos cubiertos por este contrato...">{{ $equiposValue }}</textarea>
                        @error('equipos_cubiertos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Describa los equipos, marcas, modelos incluidos en la cobertura.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sticky Bar --}}
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('climatizacion.contratos.show', $contrato) }}" class="ui-btn ui-btn-ghost rounded-pill">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill">
                    <i class="bi bi-check-lg"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const incluyeVisitas = document.getElementById('incluye_visitas');
    const visitasGroup = document.getElementById('visitasAnualesGroup');
    if (incluyeVisitas && visitasGroup) {
        incluyeVisitas.addEventListener('change', function() {
            visitasGroup.style.display = this.checked ? '' : 'none';
            if (!this.checked) {
                document.getElementById('num_visitas_anuales').value = 0;
            }
        });
    }
});
</script>
@endpush
@endsection
