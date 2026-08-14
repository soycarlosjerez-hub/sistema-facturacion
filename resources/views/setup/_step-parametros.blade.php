@php
    $instance = Auth::user()?->businessInstance;
    $monedaSimbolo = \App\Models\SystemSetting::get('moneda_simbolo', 'RD$');
    $impuestoItbis = \App\Models\SystemSetting::get('impuesto_itbis', '18');
@endphp
<div class="ui-card-title">
    <i class="bi bi-gear"></i>Configuración de la Instancia
</div>
<div class="ui-card-subtitle">Configura la información de la empresa y los parámetros predeterminados del sistema.</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-4">
    @csrf
    <input type="hidden" name="step" value="parametros">

    <div class="col-md-6">
        <label class="ui-label">Nombre Comercial / Empresa <span class="text-danger">*</span></label>
        <input type="text" name="empresa_nombre" id="empresa_nombre" class="ui-input" placeholder="Mi Negocio" value="{{ old('empresa_nombre', $instance?->nombre) }}" required>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Teléfono</label>
        <input type="text" name="empresa_telefono" id="empresa_telefono" class="ui-input" placeholder="+1 (809) 000-0000" value="{{ old('empresa_telefono', $instance?->telefono) }}">
    </div>

    <div class="col-md-6">
        <label class="ui-label">RNC</label>
        <input type="text" name="empresa_rnc" id="empresa_rnc" class="ui-input" placeholder="000000000" value="{{ old('empresa_rnc', $instance?->rnc) }}">
    </div>

    <div class="col-md-6">
        <label class="ui-label">Correo de la empresa</label>
        <input type="email" name="empresa_email" id="empresa_email" class="ui-input" placeholder="correo@empresa.com" value="{{ old('empresa_email', $instance?->email) }}">
    </div>

    <div class="col-12">
        <label class="ui-label">Dirección</label>
        <input type="text" name="empresa_direccion" id="empresa_direccion" class="ui-input" placeholder="Dirección del negocio" value="{{ old('empresa_direccion', $instance?->direccion) }}">
    </div>

    <div class="col-md-6">
        <label class="ui-label">Símbolo de Moneda <span class="text-danger">*</span></label>
        <input type="text" name="moneda_simbolo" id="moneda_simbolo" class="ui-input" value="{{ old('moneda_simbolo', $monedaSimbolo) }}" required>
    </div>

    <div class="col-md-6">
        <label class="ui-label">ITBIS por defecto (%) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="impuesto_itbis" id="impuesto_itbis" class="ui-input" value="{{ old('impuesto_itbis', $impuestoItbis) }}" required>
    </div>

    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>