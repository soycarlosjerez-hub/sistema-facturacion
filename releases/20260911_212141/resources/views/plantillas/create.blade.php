@extends('layouts.app')
@section('title', 'Nueva Plantilla de Factura')

@push('styles')
@include('partials.premium-ui')
<style>
    .color-input-wrap {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .color-input-wrap input[type="color"] {
        width: 42px;
        height: 42px;
        border: none;
        padding: 0;
        border-radius: 0.5rem;
        cursor: pointer;
        flex-shrink: 0;
    }
    .color-input-wrap input[type="color"]::-webkit-color-swatch-wrapper { padding: 2px; }
    .color-input-wrap input[type="color"]::-webkit-color-swatch { border-radius: 0.375rem; border: none; }
    .color-hex { font-family: 'Courier New', monospace; font-size: 0.9rem; color: #64748b; }

    .toggle-switch {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        user-select: none;
    }
    .toggle-switch input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-switch .toggle-slider {
        width: 2.75rem;
        height: 1.5rem;
        background: #cbd5e1;
        border-radius: 1rem;
        position: relative;
        transition: background 0.2s;
    }
    .toggle-switch .toggle-slider::before {
        content: '';
        position: absolute;
        width: 1.25rem;
        height: 1.25rem;
        background: #fff;
        border-radius: 50%;
        top: 2px;
        left: 2px;
        transition: transform 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    }
    .toggle-switch input:checked + .toggle-slider { background: #7c3aed; }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(1.25rem); }
    .toggle-switch .toggle-label { font-weight: 500; }

    .preview-box {
        border: 2px dashed #e2e8f0;
        border-radius: 0.75rem;
        background: #fff;
        padding: 1.5rem;
        min-height: 400px;
        transition: border-color 0.2s;
    }
    body.dark-mode .preview-box {
        border-color: #334155;
        background: #0f172a;
    }
    .preview-box:hover { border-color: #8b5cf6; }

    .tab-content .tab-pane { padding-top: 1rem; }

    .nav-pills .nav-link {
        font-size: 0.85rem;
        padding: 0.6rem 1rem;
        border-radius: 0.5rem;
        color: #64748b;
        font-weight: 500;
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        color: #fff;
    }
    body.dark-mode .nav-pills .nav-link { color: #94a3b8; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-file-earmark-richtext"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Plantilla</h4>
                    <div class="ui-header-meta">Personaliza el diseno de facturas, tickets y comprobantes</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('plantillas.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #ef4444 !important;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('plantillas.store') }}" method="POST" enctype="multipart/form-data" id="plantillaForm">
        @csrf

        <div class="row g-4">
            <!-- Left column: form -->
            <div class="col-lg-8">
                <!-- General -->
                <div class="ui-card mb-4">
                    <div class="ui-card-accent"></div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-1"><i class="bi bi-info-circle me-2 text-primary"></i>General</h6>
                        <small class="text-muted mb-3 d-block">Datos basicos de la plantilla</small>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="ui-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="ui-input @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej. Factura A4 Premium">
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label">Codigo <span class="text-danger">*</span></label>
                                <input type="text" name="codigo" class="ui-input @error('codigo') is-invalid @enderror" value="{{ old('codigo') }}" required placeholder="Ej. factura_a4_premium">
                                @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted mt-1 d-block">Codigo unico en letras minisculas, sin espacios ni acentos.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label">Modulo <span class="text-danger">*</span></label>
                                <select name="modulo" id="modulo" class="ui-select @error('modulo') is-invalid @enderror" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach(\App\Models\PlantillaImpresion::MODULOS as $key => $label)
                                    <option value="{{ $key }}" {{ old('modulo') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('modulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="ui-label">Formato Papel</label>
                                <select name="formato_papel" id="formato_papel" class="ui-select">
                                    <option value="a4" {{ old('formato_papel', 'a4') == 'a4' ? 'selected' : '' }}>A4</option>
                                    <option value="letter" {{ old('formato_papel') == 'letter' ? 'selected' : '' }}>Carta</option>
                                    <option value="ticket_80" {{ old('formato_papel') == 'ticket_80' ? 'selected' : '' }}>Ticket 80mm</option>
                                    <option value="ticket_58" {{ old('formato_papel') == 'ticket_58' ? 'selected' : '' }}>Ticket 58mm</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="ui-label">Orientacion</label>
                                <select name="orientation" id="orientation" class="ui-select">
                                    <option value="portrait" {{ old('orientation', 'portrait') == 'portrait' ? 'selected' : '' }}>Vertical</option>
                                    <option value="landscape" {{ old('orientation') == 'landscape' ? 'selected' : '' }}>Horizontal</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label">Logo</label>
                                <input type="file" name="logo" class="ui-input" accept="image/*">
                                <small class="text-muted mt-1 d-block">PNG, JPG o SVG. Max 2MB. Se usara en el encabezado de la plantilla.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Design tabs -->
                <div class="ui-card mb-4">
                    <div class="ui-card-accent"></div>
                    <div class="card-body p-0">
                        <ul class="nav nav-pills mb-3 px-3" id="designTabs" role="tablist">
                            <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tabColores"><i class="bi bi-palette me-1"></i>Colores</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabHeader"><i class="bi bi-signature me-1"></i>Encabezado</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabItems"><i class="bi bi-list-ul me-1"></i>Items/Columnas</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabTotales"><i class="bi bi-calculator me-1"></i>Totales</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabFiscal"><i class="bi bi-shield-check me-1"></i>Fiscal/NCF</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabFooter"><i class="bi bi-signpost-split me-1"></i>Pie</button></li>
                        </ul>

                        <div class="tab-content px-3 pb-3">
                            <!-- Colors tab -->
                            <div class="tab-pane fade show active" id="tabColores">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="ui-label">Color Primario</label>
                                        <div class="color-input-wrap">
                                            <input type="color" id="colorPrimario" name="color_primario" value="{{ old('color_primario', '#8b5cf6') }}" data-hex="colorPrimarioHex">
                                            <input type="text" id="colorPrimarioHex" name="_color_primario" class="ui-input" value="{{ old('color_primario', '#8b5cf6') }}" style="width: 110px; font-family: monospace;">
                                        </div>
                                        <small class="text-muted d-block mt-1">Usado en titulos, bordes y elementos principales.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="ui-label">Color Secundario</label>
                                        <div class="color-input-wrap">
                                            <input type="color" id="colorSecundario" name="color_secundario" value="{{ old('color_secundario', '#1e293b') }}" data-hex="colorSecundarioHex">
                                            <input type="text" id="colorSecundarioHex" name="_color_secundario" class="ui-input" value="{{ old('color_secundario', '#1e293b') }}" style="width: 110px; font-family: monospace;">
                                        </div>
                                        <small class="text-muted d-block mt-1">Usado para fondos, secciones y detalles.</small>
                                    </div>
                                </div>
                                <div class="mt-3 p-3 rounded-3" style="background: linear-gradient(135deg, {{ old('color_primario', '#8b5cf6') }}15, {{ old('color_secundario', '#1e293b') }}10); border: 1px solid {{ old('color_primario', '#8b5cf6') }}30;">
                                    <small class="text-muted">Vista previa de colores:</small>
                                    <div class="d-flex gap-2 mt-2">
                                        <div class="rounded-3" style="width: 32px; height: 32px; background: {{ old('color_primario', '#8b5cf6') }};"></div>
                                        <div class="rounded-3" style="width: 32px; height: 32px; background: {{ old('color_secundario', '#1e293b') }};"></div>
                                        <div class="rounded-3" style="width: 32px; height: 32px; background: #f8fafc; border: 1px solid #e2e8f0;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Header tab -->
                            <div class="tab-pane fade" id="tabHeader">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="mostrar_logo" value="1" {{ old('mostrar_logo', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label">Mostrar logo</span>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="mostrar_encabezado" value="1" {{ old('mostrar_encabezado', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label">Mostrar encabezado</span>
                                        </label>
                                    </div>
                                    <div class="col-12">
                                        <label class="ui-label">Texto de Encabezado Personalizado</label>
                                        <textarea name="encabezado_texto" class="ui-input" rows="3" placeholder="Texto adicional que aparecera en el encabezado de la factura...">{{ old('encabezado_texto') }}</textarea>
                                        <small class="text-muted mt-1 d-block">Se mostrara debajo del logo y nombre de la empresa. Deja vacio para usar el texto por defecto.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Items/Columns tab -->
                            <div class="tab-pane fade" id="tabItems">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="mostrar_columna_cantidad" value="1" {{ old('mostrar_columna_cantidad', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label">Cantidad</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="mostrar_columna_precio" value="1" {{ old('mostrar_columna_precio', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label">Precio Unitario</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="mostrar_columna_subtotal" value="1" {{ old('mostrar_columna_subtotal', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label">Subtotal</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="mostrar_columna_itbis" value="1" {{ old('mostrar_columna_itbis', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label">ITBIS</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="mostrar_garantias" value="1" {{ old('mostrar_garantias', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label">Garantias</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Totales tab -->
                            <div class="tab-pane fade" id="tabTotales">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="mostrar_pagos" value="1" {{ old('mostrar_pagos', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label">Mostrar pagos/forma de pago</span>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="mostrar_notas" value="1" {{ old('mostrar_notas', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label">Mostrar notas</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Fiscal/NCF tab -->
                            <div class="tab-pane fade" id="tabFiscal">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="mostrar_datos_fiscales" value="1" {{ old('mostrar_datos_fiscales', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label">Mostrar datos fiscales (NCF/e-CF)</span>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="mostrar_datos_cliente" value="1" {{ old('mostrar_datos_cliente', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label">Mostrar datos del cliente</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer tab -->
                            <div class="tab-pane fade" id="tabFooter">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="mostrar_pie" value="1" {{ old('mostrar_pie', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label">Mostrar pie de pagina</span>
                                        </label>
                                    </div>
                                    <div class="col-12">
                                        <label class="ui-label">Texto de Pie de Pagina</label>
                                        <textarea name="pie_pagina_texto" class="ui-input" rows="3" placeholder="Gracias por su compra. Este documento es una representacion impresa de un NCF electronico.">{{ old('pie_pagina_texto') }}</textarea>
                                        <small class="text-muted mt-1 d-block">Se mostrara al final de la factura. Deja vacio para usar el texto por defecto.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sucursales -->
                <div class="ui-card mb-4">
                    <div class="ui-card-accent"></div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-1"><i class="bi bi-building me-2 text-primary"></i>Sucursales</h6>
                        <small class="text-muted mb-3 d-block">Asigna esta plantilla a sucursales especificas. Si no selecciona ninguna, sera global.</small>

                        <div class="row g-3">
                            @foreach($sucursales as $sucursal)
                            <div class="col-md-4 col-lg-3">
                                <label class="toggle-switch">
                                    <input type="checkbox" name="sucursal_ids[]" value="{{ $sucursal->id }}" data-toggle-name="suc_{{ $sucursal->id }}">
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label">{{ $sucursal->nombre }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <small class="text-muted mt-2 d-block">Deja todo desactivado para que la plantilla sea global.</small>
                    </div>
                </div>
            </div>

            <!-- Right column: preview -->
            <div class="col-lg-4">
                <div class="ui-card mb-4">
                    <div class="ui-card-accent"></div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-eye me-2 text-primary"></i>Vista Previa en Vivo</h6>

                        <div class="preview-box" id="previewBox">
                            <div id="previewContent">
                                <!-- Live preview rendered here via JS -->
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-file-earmark-richtext fs-1 d-block mb-2"></i>
                                    <small>Completa los campos para ver la vista previa</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Default toggle -->
                <div class="ui-card mb-4">
                    <div class="ui-card-accent"></div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="fw-bold mb-1"><i class="bi bi-star me-2 text-warning"></i>Predeterminada</h6>
                                <small class="text-muted">Esta sera la plantilla por defecto para su modulo.</small>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="es_default" value="1" {{ old('es_default') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('plantillas.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
        <button type="submit" form="plantillaForm" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>Guardar Plantilla
        </button>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    const primario = '{{ old("color_primario", "#8b5cf6") }}';
    const secundario = '{{ old("color_secundario", "#1e293b") }}';

    // Color pickers sync
    $('#colorPrimario').on('input', function() {
        $('#colorPrimarioHex').val($(this).val());
        updatePreview();
    });
    $('#colorSecundario').on('input', function() {
        $('#colorSecundarioHex').val($(this).val());
        updatePreview();
    });
    $('#colorPrimarioHex, #colorSecundarioHex').on('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test($(this).val())) {
            $(this).siblings('input[type="color"]').val($(this).val());
        }
        updatePreview();
    });

    // All toggle inputs
    $('input[name="mostrar_logo"], input[name="mostrar_encabezado"], input[name="mostrar_pie"], input[name="mostrar_datos_fiscales"], input[name="mostrar_datos_cliente"], input[name="mostrar_columna_cantidad"], input[name="mostrar_columna_precio"], input[name="mostrar_columna_subtotal"], input[name="mostrar_columna_itbis"], input[name="mostrar_garantias"], input[name="mostrar_pagos"], input[name="mostrar_notas"], input[name="modulo"]').on('change', updatePreview);

    // Text fields
    $('input[name="nombre"], textarea[name="encabezado_texto"], textarea[name="pie_pagina_texto"], select[name="formato_papel"]').on('input change', updatePreview);

    function updatePreview() {
        const mod = $('select[name="modulo"]').val() || 'ventas';
        const nombre = $('input[name="nombre"]').val() || 'Nombre de la Empresa';
        const rnc = 'RNC: 130-1234567-8';
        const direccion = 'Av. Principal #123, Santo Domingo';
        const telefono = 'Telefono: (809) 555-1234';
        const tituloDoc = mod === 'ventas' ? 'FACTURA' : (mod === 'compras' ? 'COMPROBANTE DE COMPRA' : 'DOCUMENTO');
        const numDoc = 'No. 00001';
        const ncf = 'NCF: B0100000001';
        const fecha = 'Fecha: ' + new Date().toLocaleDateString('es-DO');
        const cliente = 'Cliente: Juan Perez';
        const rncCliente = 'RNC: 402-1234567-8';
        const showItems = $('input[name="mostrar_columna_cantidad"]').is(':checked') &&
                         $('input[name="mostrar_columna_precio"]').is(':checked') &&
                         $('input[name="mostrar_columna_subtotal"]').is(':checked');
        const showItbis = $('input[name="mostrar_columna_itbis"]').is(':checked');
        const showHeader = $('input[name="mostrar_encabezado"]').is(':checked');
        const showLogo = $('input[name="mostrar_logo"]').is(':checked');
        const showFiscal = $('input[name="mostrar_datos_fiscales"]').is(':checked');
        const showCliente = $('input[name="mostrar_datos_cliente"]').is(':checked');
        const showFooter = $('input[name="mostrar_pie"]').is(':checked');
        const pieTexto = $('textarea[name="pie_pagina_texto"]').val() || 'Gracias por su compra.';
        const cabezaTexto = $('textarea[name="encabezado_texto"]').val();
        const fmt = $('select[name="formato_papel"]').val();
        const w = (fmt === 'ticket_58') ? 'max-width: 200px;' : (fmt === 'ticket_80' ? 'max-width: 300px;' : 'max-width: 500px;');

        let html = '<div style="' + w + ' font-family: sans-serif; font-size: 12px; color: #333;">';

        // Header
        if (showHeader) {
            html += '<div style="border-bottom: 2px solid ' + primario + '; padding-bottom: 8px; margin-bottom: 10px;">';
            if (showLogo) {
                html += '<div style="width: 40px; height: 40px; background: ' + primario + '; border-radius: 8px; margin-bottom: 4px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 14px;">LOGO</div>';
            }
            html += '<div style="font-size: 16px; font-weight: bold; color: ' + primario + ';">' + escapeHtml(nombre) + '</div>';
            html += '<div style="font-size: 10px; color: #666;">' + escapeHtml(rnc) + '</div>';
            html += '<div style="font-size: 10px; color: #666;">' + escapeHtml(direccion) + '</div>';
            html += '<div style="font-size: 10px; color: #666;">' + escapeHtml(telefono) + '</div>';
            if (cabezaTexto) {
                html += '<div style="font-size: 10px; color: ' + primario + '; margin-top: 4px; font-style: italic;">' + escapeHtml(cabezaTexto) + '</div>';
            }
            html += '</div>';
        }

        // Doc info
        html += '<div style="text-align: right; margin-bottom: 10px;">';
        html += '<div style="font-size: 18px; font-weight: bold; color: ' + primario + ';">' + tituloDoc + '</div>';
        html += '<div style="font-size: 12px; font-weight: bold;">' + numDoc + '</div>';
        html += '<div style="font-size: 10px;">' + fecha + '</div>';
        if (showFiscal) {
            html += '<div style="font-size: 10px;">' + ncf + '</div>';
        }
        html += '</div>';

        // Client
        if (showCliente) {
            html += '<div style="margin-bottom: 10px; padding: 6px; background: #f8fafc; border-radius: 4px; border: 1px solid #e2e8f0;">';
            html += '<div style="font-size: 10px; color: #666; font-weight: bold;">DATOS DEL CLIENTE</div>';
            html += '<div style="font-size: 11px;">' + escapeHtml(cliente) + '</div>';
            html += '<div style="font-size: 10px; color: #666;">' + escapeHtml(rncCliente) + '</div>';
            html += '</div>';
        }

        // Items table
        if (showItems) {
            html += '<table style="width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 10px;">';
            html += '<thead><tr style="background: ' + primario + '; color: #fff;">';
            html += '<th style="padding: 4px 6px; text-align: left; border: none;">Producto</th>';
            html += '<th style="padding: 4px 6px; text-align: center; border: none;">Cant.</th>';
            html += '<th style="padding: 4px 6px; text-align: right; border: none;">P. Unit.</th>';
            if (showItbis) html += '<th style="padding: 4px 6px; text-align: center; border: none;">ITBIS</th>';
            html += '<th style="padding: 4px 6px; text-align: right; border: none;">Total</th>';
            html += '</tr></thead>';
            html += '<tbody>';
            html += '<tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 4px 6px;">Producto Ejemplo 1</td><td style="padding: 4px 6px; text-align: center;">2.00</td><td style="padding: 4px 6px; text-align: right;">RD$ 500.00</td><td style="padding: 4px 6px; text-align: center;">18%</td><td style="padding: 4px 6px; text-align: right;">RD$ 1,000.00</td></tr>';
            html += '<tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 4px 6px;">Servicio Ejemplo</td><td style="padding: 4px 6px; text-align: center;">1.00</td><td style="padding: 4px 6px; text-align: right;">RD$ 2,500.00</td><td style="padding: 4px 6px; text-align: center;">18%</td><td style="padding: 4px 6px; text-align: right;">RD$ 2,500.00</td></tr>';
            html += '</tbody></table>';
        }

        // Totals
        html += '<div style="text-align: right; font-size: 11px;">';
        html += '<div style="display: flex; justify-content: flex-end; padding: 2px 0;"><span style="color: #666; margin-right: 20px;">Subtotal:</span><span>RD$ 3,500.00</span></div>';
        html += '<div style="display: flex; justify-content: flex-end; padding: 2px 0;"><span style="color: #666; margin-right: 20px;">ITBIS (18%):</span><span>RD$ 630.00</span></div>';
        html += '<div style="display: flex; justify-content: flex-end; padding: 2px 0; font-weight: bold; font-size: 14px; border-top: 2px solid ' + primario + '; margin-top: 4px; padding-top: 4px;"><span style="color: ' + primario + '; margin-right: 20px;">TOTAL:</span><span>RD$ 4,130.00</span></div>';
        html += '</div>';

        // Footer
        if (showFooter) {
            html += '<div style="margin-top: 12px; padding-top: 8px; border-top: 1px dashed #ccc; text-align: center; font-size: 10px; color: #888;">' + escapeHtml(pieTexto) + '</div>';
        }

        html += '</div>';

        $('#previewContent').html(html);
    }

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    updatePreview();
});
</script>
@endpush
@endsection
