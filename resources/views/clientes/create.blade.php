@extends('layouts.app')

@section('title', 'Nuevo Cliente')

@push('styles')
@include('partials.premium-ui')
<style>
.form-section-title {
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 1rem;
    padding-bottom: .5rem;
    border-bottom: 1px solid #e2e8f0;
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
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Nuevo Cliente</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registrar un nuevo cliente en el directorio
                    </small>
                </div>
            </div>
            <a href="{{ route('clientes.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
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

    <form id="clienteForm" action="{{ route('clientes.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="premium-card" style="animation-delay:.1s;">
                    <div class="card-accent green"></div>
                    <div class="premium-card-title">
                        <i class="bi bi-info-circle icon-green"></i>
                        Información General
                    </div>
                    <div class="premium-card-subtitle">Datos básicos del cliente</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">RNC / Cédula</label>
                                <input type="text" name="rnc_cedula" class="form-control @error('rnc_cedula') is-invalid @enderror" maxlength="11" id="rncInput" placeholder="RNC o Cédula" value="{{ old('rnc_cedula') }}">
                                <div id="rncFeedback" class="small mt-1"></div>
                                @error('rnc_cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tipo Documento</label>
                                <select name="tipo_documento" class="form-select @error('tipo_documento') is-invalid @enderror" id="tipoDoc">
                                    <option value="">Auto-detectar</option>
                                    <option value="rnc" {{ old('tipo_documento')=='rnc' ? 'selected' : '' }}>RNC</option>
                                    <option value="cedula" {{ old('tipo_documento')=='cedula' ? 'selected' : '' }}>Cédula</option>
                                    <option value="pasaporte" {{ old('tipo_documento')=='pasaporte' ? 'selected' : '' }}>Pasaporte</option>
                                    <option value="ninguno" {{ old('tipo_documento')=='ninguno' ? 'selected' : '' }}>Ninguno</option>
                                </select>
                                @error('tipo_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tipo de Cliente</label>
                                <select name="tipo_cliente" class="form-select @error('tipo_cliente') is-invalid @enderror">
                                    <option value="consumo" {{ old('tipo_cliente')=='consumo' ? 'selected' : '' }}>Consumo</option>
                                    <option value="credito_fiscal" {{ old('tipo_cliente')=='credito_fiscal' ? 'selected' : '' }}>Crédito Fiscal</option>
                                    <option value="gubernamental" {{ old('tipo_cliente')=='gubernamental' ? 'selected' : '' }}>Gubernamental</option>
                                    <option value="especial" {{ old('tipo_cliente')=='especial' ? 'selected' : '' }}>Especial</option>
                                </select>
                                @error('tipo_cliente') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}">
                                @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">WhatsApp</label>
                                <input type="text" name="whatsapp" class="form-control @error('whatsapp') is-invalid @enderror" value="{{ old('whatsapp') }}">
                                @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Dirección</label>
                                <textarea name="direccion" class="form-control @error('direccion') is-invalid @enderror" rows="2">{{ old('direccion') }}</textarea>
                                @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" class="form-control @error('ciudad') is-invalid @enderror" value="{{ old('ciudad') }}">
                                @error('ciudad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Provincia</label>
                                <input type="text" name="provincia" class="form-control @error('provincia') is-invalid @enderror" value="{{ old('provincia') }}">
                                @error('provincia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Código Postal</label>
                                <input type="text" name="codigo_postal" class="form-control @error('codigo_postal') is-invalid @enderror" value="{{ old('codigo_postal') }}">
                                @error('codigo_postal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="premium-card mt-4" style="animation-delay:.15s;">
                    <div class="card-accent blue"></div>
                    <div class="premium-card-title">
                        <i class="bi bi-credit-card icon-blue"></i>
                        Términos de Crédito
                    </div>
                    <div class="premium-card-subtitle">Configuración financiera del cliente</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Límite de Crédito (RD$)</label>
                                <input type="number" step="0.01" min="0" name="limite_credito" class="form-control @error('limite_credito') is-invalid @enderror" value="{{ old('limite_credito', 0) }}">
                                @error('limite_credito') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Plazo de Pago (días)</label>
                                <select name="plazo_pago_dias" class="form-select @error('plazo_pago_dias') is-invalid @enderror">
                                    <option value="0" {{ old('plazo_pago_dias')==0 ? 'selected' : '' }}>Contado</option>
                                    <option value="15" {{ old('plazo_pago_dias')==15 ? 'selected' : '' }}>Net 15</option>
                                    <option value="30" {{ old('plazo_pago_dias', 30)==30 ? 'selected' : '' }}>Net 30</option>
                                    <option value="45" {{ old('plazo_pago_dias')==45 ? 'selected' : '' }}>Net 45</option>
                                    <option value="60" {{ old('plazo_pago_dias')==60 ? 'selected' : '' }}>Net 60</option>
                                    <option value="90" {{ old('plazo_pago_dias')==90 ? 'selected' : '' }}>Net 90</option>
                                </select>
                                @error('plazo_pago_dias') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Descuento x Pronto Pago (%)</label>
                                <input type="number" step="0.01" min="0" max="100" name="tasa_descuento_pct" class="form-control @error('tasa_descuento_pct') is-invalid @enderror" value="{{ old('tasa_descuento_pct', 0) }}" placeholder="Ej: 2">
                                @error('tasa_descuento_pct') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Moneda</label>
                                <select name="moneda" class="form-select @error('moneda') is-invalid @enderror">
                                    <option value="RD" {{ old('moneda')=='RD' ? 'selected' : '' }}>RD$</option>
                                    <option value="USD" {{ old('moneda')=='USD' ? 'selected' : '' }}>US$</option>
                                    <option value="EUR" {{ old('moneda')=='EUR' ? 'selected' : '' }}>€</option>
                                </select>
                                @error('moneda') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-4">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="auto_bloquear_credito" value="1" id="chk-bloquear" {{ old('auto_bloquear_credito', '1') ? 'checked' : '' }} role="switch">
                                        <label class="form-check-label" for="chk-bloquear">Bloquear venta si excede el límite de crédito</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="premium-card mt-4" style="animation-delay:.2s;">
                    <div class="card-accent purple"></div>
                    <div class="premium-card-title">
                        <i class="bi bi-building icon-purple"></i>
                        Información Fiscal y Segmentación
                    </div>
                    <div class="premium-card-subtitle">Datos fiscales y perfil del cliente</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">NIT (Internacional)</label>
                                <input type="text" name="nit" class="form-control @error('nit') is-invalid @enderror" value="{{ old('nit') }}">
                                @error('nit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Régimen</label>
                                <select name="regimen_mensual" class="form-select @error('regimen_mensual') is-invalid @enderror">
                                    <option value="1" {{ old('regimen_mensual')!=='0' ? 'selected' : '' }}>Mensual</option>
                                    <option value="0" {{ old('regimen_mensual')==='0' ? 'selected' : '' }}>No Mensual</option>
                                </select>
                                @error('regimen_mensual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Segmento</label>
                                <select name="segmento" class="form-select @error('segmento') is-invalid @enderror">
                                    <option value="micro" {{ old('segmento')=='micro' ? 'selected' : '' }}>Micro</option>
                                    <option value="pequeno" {{ old('segmento')=='pequeno' ? 'selected' : '' }}>Pequeño</option>
                                    <option value="mediano" {{ old('segmento')=='mediano' ? 'selected' : '' }}>Mediano</option>
                                    <option value="grande" {{ old('segmento')=='grande' ? 'selected' : '' }}>Grande</option>
                                    <option value="gobierno" {{ old('segmento')=='gobierno' ? 'selected' : '' }}>Gobierno</option>
                                </select>
                                @error('segmento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Origen del Cliente</label>
                                <select name="origen_cliente" class="form-select @error('origen_cliente') is-invalid @enderror">
                                    <option value="walkin" {{ old('origen_cliente')=='walkin' ? 'selected' : '' }}>Presencial</option>
                                    <option value="referencia" {{ old('origen_cliente')=='referencia' ? 'selected' : '' }}>Referido</option>
                                    <option value="web" {{ old('origen_cliente')=='web' ? 'selected' : '' }}>Sitio Web</option>
                                    <option value="publicidad" {{ old('origen_cliente')=='publicidad' ? 'selected' : '' }}>Publicidad</option>
                                    <option value="otro" {{ old('origen_cliente')=='otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('origen_cliente') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Sector / Actividad</label>
                                <input type="text" name="sector_actividad" class="form-control @error('sector_actividad') is-invalid @enderror" value="{{ old('sector_actividad') }}" placeholder="Ej: Comercio, Servicios">
                                @error('sector_actividad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Persona de Contacto</label>
                                <input type="text" name="persona_contacto" class="form-control @error('persona_contacto') is-invalid @enderror" value="{{ old('persona_contacto') }}">
                                @error('persona_contacto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Cargo del Contacto</label>
                                <input type="text" name="cargo_contacto" class="form-control @error('cargo_contacto') is-invalid @enderror" value="{{ old('cargo_contacto') }}" placeholder="Ej: Gerente de Compras">
                                @error('cargo_contacto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notas Internas</label>
                                <textarea name="notas_internas" class="form-control @error('notas_internas') is-invalid @enderror" rows="2" placeholder="Observaciones visibles solo para el staff">{{ old('notas_internas') }}</textarea>
                                @error('notas_internas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="premium-card" style="animation-delay:.15s;">
                    <div class="card-accent green"></div>
                    <div class="premium-card-title">
                        <i class="bi bi-toggle-on icon-green"></i>
                        Estado del Cliente
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fw-bold">Cliente Activo</span>
                                <p class="text-muted small mb-0">Si está inactivo no aparecerá en las listas</p>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="activo" value="1" id="chk-activo" checked role="switch">
                                <label class="form-check-label" for="chk-activo"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="premium-card mt-3" style="animation-delay:.2s;">
                    <div class="card-accent blue"></div>
                    <div class="premium-card-title">
                        <i class="bi bi-info-circle icon-blue"></i>
                        Información
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Los campos marcados con * son obligatorios.
                        </p>
                        <hr>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-credit-card me-1"></i>
                            El RNC o Cédula se usa para facturación electrónica (e-CF).
                        </p>
                        <hr>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-shield-exclamation me-1"></i>
                            Si activas "Bloquear venta si excede crédito", el sistema rechazará ventas que superen el límite.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div style="height: 80px;"></div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-end align-items-center">
        <a href="{{ route('clientes.index') }}" class="btn-cancel me-2">Cancelar</a>
        <button type="submit" form="clienteForm" class="btn-save">
            <i class="bi bi-check-lg me-2"></i>Guardar Cliente
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
const rncInput = document.getElementById('rncInput');
const rncFeedback = document.getElementById('rncFeedback');
const tipoDoc = document.getElementById('tipoDoc');

function validarRNC() {
    const rnc = rncInput.value.replace(/[^0-9]/g, '');
    rncInput.value = rnc;
    const tipo = tipoDoc.value || 'auto';

    if (rnc.length < 9) {
        rncFeedback.innerHTML = '<span class="text-muted">Mínimo 9 dígitos</span>';
        return;
    }

    fetch('{{ route("ecf.validar-rnc") }}?rnc=' + encodeURIComponent(rnc) + '&tipo=' + tipo)
        .then(r => r.json())
        .then(data => {
            if (data.valido) {
                rncFeedback.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>' + data.mensaje + '</span>';
                if (!tipoDoc.value) {
                    const opcion = document.querySelector('#tipoDoc option[value="' + data.tipo_inferido + '"]');
                    if (opcion) { tipoDoc.value = data.tipo_inferido; }
                }
            } else {
                rncFeedback.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>' + data.mensaje + '</span>';
            }
        })
        .catch(() => {});
}

rncInput.addEventListener('input', validarRNC);
tipoDoc.addEventListener('change', validarRNC);
</script>
@endpush