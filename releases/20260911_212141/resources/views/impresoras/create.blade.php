@extends('layouts.app')
@section('title', 'Nueva Impresora')

@push('styles')
@include('partials.premium-ui')
<style>
.conn-section { display: none; }
.conn-section.active { display: block; }
.conn-section.active { animation: fadeIn 0.2s ease-in; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
.conn-radio:checked + .conn-radio-card { border-color: var(--accent); background: rgba(var(--accent-rgb), 0.08); box-shadow: 0 0 0 1px var(--accent); }
.conn-radio-card { transition: all 0.15s; cursor: pointer; border: 2px solid #e2e8f0; border-radius: 0.75rem; padding: 12px 16px; }
.conn-radio-card:hover { border-color: #cbd5e1; background: #f8fafc; }
body.dark-mode .conn-radio-card { border-color: #334155; background: rgba(15,23,42,0.4); }
body.dark-mode .conn-radio-card:hover { border-color: #475569; background: rgba(15,23,42,0.6); }
body.dark-mode .conn-radio:checked + .conn-radio-card { border-color: var(--accent); background: rgba(var(--accent-rgb), 0.12); }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-printer"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Impresora</h4>
                    <div class="ui-header-meta">Registra una nueva impresora para imprimir tickets y comprobantes</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('impresoras.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
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

    <form id="impresoraForm" action="{{ route('impresoras.store') }}" method="POST">
        @csrf
        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0 ui-card-title">
                        <i class="bi bi-info-circle me-2"></i>Informacion de la Impresora
                    </h6>
                    <small class="text-muted">Datos basicos de la impresora</small>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <label class="ui-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre" class="ui-input @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej. Caja 1 Termica, Cafeteria, Recepcion">
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted mt-1 d-block">Un nombre descriptivo para identificarla facilmente.</small>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Tipo</label>
                        <input type="text" name="tipo" id="tipo" class="ui-input" value="{{ old('tipo', 'general') }}" placeholder="general">
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Orden</label>
                        <input type="number" name="orden" id="orden" class="ui-input" value="{{ old('orden', 0) }}" min="0" placeholder="0">
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <label class="ui-label">Sucursal</label>
                        <select name="sucursal_id" id="sucursal_id" class="ui-select @error('sucursal_id') is-invalid @enderror">
                            <option value="">Sin sucursal (global)</option>
                            @foreach($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}" {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                                {{ $sucursal->nombre }}
                            </option>
                            @endforeach
                        </select>
                        @error('sucursal_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted mt-1 d-block">Asocia esta impresora a una sucursal especifica.</small>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Descripcion</label>
                        <input type="text" name="descripcion" id="descripcion" class="ui-input" value="{{ old('descripcion') }}" placeholder="Notas adicionales sobre la impresora">
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card" style="--delay:.15s">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0 ui-card-title">
                        <i class="bi bi-cpu me-2"></i>Conexion e Hardware
                    </h6>
                    <small class="text-muted">Como se conecta la impresora al sistema</small>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-lg-12">
                        <label class="ui-label">Tipo de Conexion <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="radio" name="tipo_conexion" id="conn_local" value="local" class="conn-radio d-none" {{ old('tipo_conexion') === 'local' || !old('tipo_conexion') ? 'checked' : '' }}>
                                <label for="conn_local" class="conn-radio-card text-center d-block mb-0">
                                    <i class="bi bi-display fs-4 mb-2 d-block" style="color: var(--accent);"></i>
                                    <span class="fw-semibold">Local</span>
                                </label>
                            </div>
                            <div class="col-md-3">
                                <input type="radio" name="tipo_conexion" id="conn_usb" value="usb" class="conn-radio d-none" {{ old('tipo_conexion') === 'usb' ? 'checked' : '' }}>
                                <label for="conn_usb" class="conn-radio-card text-center d-block mb-0">
                                    <i class="bi bi-usb fs-4 mb-2 d-block" style="color: var(--accent);"></i>
                                    <span class="fw-semibold">USB</span>
                                </label>
                            </div>
                            <div class="col-md-3">
                                <input type="radio" name="tipo_conexion" id="conn_red" value="red" class="conn-radio d-none" {{ old('tipo_conexion') === 'red' ? 'checked' : '' }}>
                                <label for="conn_red" class="conn-radio-card text-center d-block mb-0">
                                    <i class="bi bi-ethernet fs-4 mb-2 d-block" style="color: var(--accent);"></i>
                                    <span class="fw-semibold">Red</span>
                                </label>
                            </div>
                            <div class="col-md-3">
                                <input type="radio" name="tipo_conexion" id="conn_pdf" value="pdf" class="conn-radio d-none" {{ old('tipo_conexion') === 'pdf' ? 'checked' : '' }}>
                                <label for="conn_pdf" class="conn-radio-card text-center d-block mb-0">
                                    <i class="bi bi-file-earmark-pdf fs-4 mb-2 d-block" style="color: var(--accent);"></i>
                                    <span class="fw-semibold">PDF</span>
                                </label>
                            </div>
                        </div>
                        @error('tipo_conexion')<div class="invalid-feedback mt-2 d-block">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div id="conn_local" class="conn-section active">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label class="ui-label">Direccion IP</label>
                            <input type="text" name="direccion_ip" id="direccion_ip" class="ui-input" value="{{ old('direccion_ip') }}" placeholder="192.168.1.50">
                        </div>
                        <div class="col-lg-3">
                            <label class="ui-label">Puerto</label>
                            <input type="number" name="puerto" id="puerto" class="ui-input" value="{{ old('puerto', 9100) }}" min="1" max="65535">
                        </div>
                        <div class="col-lg-3">
                            <label class="ui-label">Driver</label>
                            <input type="text" name="driver" id="driver" class="ui-input" value="{{ old('driver', 'escpos') }}" placeholder="escpos">
                        </div>
                    </div>
                </div>

                <div id="conn_usb" class="conn-section">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label class="ui-label">Ruta Compartida</label>
                            <input type="text" name="ruta_compartida" id="ruta_compartida" class="ui-input" value="{{ old('ruta_compartida') }}" placeholder="USB001, LPT1">
                        </div>
                        <div class="col-lg-3">
                            <label class="ui-label">Driver</label>
                            <input type="text" name="driver" id="driver_usb" class="ui-input" value="{{ old('driver', 'escpos') }}" placeholder="escpos">
                        </div>
                        <div class="col-lg-3">
                            <label class="ui-label">Puerto</label>
                            <input type="number" name="puerto" id="puerto_usb" class="ui-input" value="{{ old('puerto', 9100) }}" min="1" max="65535">
                        </div>
                    </div>
                    <div class="alert alert-info small mt-3 mb-0" style="border-left: 3px solid #06b6d4; background: rgba(6,182,212,0.06);">
                        <i class="bi bi-info-circle me-1"></i> Para impresoras USB, el sistema usa las impresoras del sistema operativo. Chrome usa las impresoras instaladas en el PC de la caja.
                    </div>
                </div>

                <div id="conn_red" class="conn-section">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <label class="ui-label">Direccion IP <span class="text-danger">*</span></label>
                            <input type="text" name="direccion_ip" id="direccion_ip_red" class="ui-input @error('direccion_ip') is-invalid @enderror" value="{{ old('direccion_ip') }}" placeholder="192.168.1.50">
                            @error('direccion_ip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-3">
                            <label class="ui-label">Puerto</label>
                            <input type="number" name="puerto" id="puerto_red" class="ui-input" value="{{ old('puerto', 9100) }}" min="1" max="65535">
                        </div>
                        <div class="col-lg-3">
                            <label class="ui-label">Driver</label>
                            <input type="text" name="driver" id="driver_red" class="ui-input" value="{{ old('driver', 'escpos') }}" placeholder="escpos">
                        </div>
                        <div class="col-lg-2">
                            <label class="ui-label">Ruta Compartida</label>
                            <input type="text" name="ruta_compartida" id="ruta_compartida_red" class="ui-input" value="{{ old('ruta_compartida') }}" placeholder="opcional">
                        </div>
                    </div>
                    <div class="alert alert-info small mt-3 mb-0" style="border-left: 3px solid #06b6d4; background: rgba(6,182,212,0.06);">
                        <i class="bi bi-info-circle me-1"></i> Las impresoras de red deben estar conectadas al mismo LAN. El puerto 9100 es el estandar para impresoras termicas EPSON/ESCPOS.
                    </div>
                </div>

                <div id="conn_pdf" class="conn-section">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label class="ui-label">Driver</label>
                            <input type="text" name="driver" id="driver_pdf" class="ui-input" value="{{ old('driver', 'pdf') }}" placeholder="pdf">
                        </div>
                        <div class="col-lg-6">
                            <label class="ui-label">Descripcion</label>
                            <input type="text" name="descripcion" id="descripcion_pdf" class="ui-input" value="{{ old('descripcion', 'Genera PDF al imprimir') }}" placeholder="Genera PDF al imprimir">
                        </div>
                    </div>
                    <div class="alert alert-warning small mt-3 mb-0" style="border-left: 3px solid #f59e0b; background: rgba(245,158,11,0.06);">
                        <i class="bi bi-exclamation-triangle me-1"></i> PDF: Esta opcion genera un PDF en lugar de imprimir fisicamente. Util para comprobantes que requieren archivo digital.
                    </div>
                </div>

                <div class="row g-4 mt-3">
                    <div class="col-lg-4">
                        <label class="ui-label">Tamano de Papel <span class="text-danger">*</span></label>
                        <select name="papel_tamano" id="papel_tamano" class="ui-select @error('papel_tamano') is-invalid @enderror" required>
                            <option value="">Seleccionar...</option>
                            <option value="58mm" {{ old('papel_tamano') === '58mm' ? 'selected' : '' }}>58mm (Ticket pequeno)</option>
                            <option value="80mm" {{ old('papel_tamano') === '80mm' ? 'selected' : '' }}>80mm (Ticket estandar)</option>
                            <option value="A4" {{ old('papel_tamano') === 'A4' ? 'selected' : '' }}>A4 (Documento completo)</option>
                        </select>
                        @error('papel_tamano')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Caracteres por Linea</label>
                        <input type="number" name="caracteres_por_linea" id="caracteres_por_linea" class="ui-input" value="{{ old('caracteres_por_linea') }}" min="24" max="132" placeholder="Auto">
                        <small class="text-muted mt-1 d-block">58mm=42, 80mm=48, A4=80. El sistema puede auto-calculatelo.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card" style="--delay:.2s">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0 ui-card-title">
                        <i class="bi bi-gear me-2"></i>Configuracion de Impresion Automatica
                    </h6>
                    <small class="text-muted">Activa la impresion automatica para estos modulos</small>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="auto_imprimir_ventas" value="1" id="auto_imprimir_ventas" {{ old('auto_imprimir_ventas') ? 'checked' : '' }} role="switch" style="width: 3rem; height: 1.5rem; cursor: pointer;">
                                <label class="form-check-label fw-semibold ms-2" for="auto_imprimir_ventas" style="cursor: pointer;">
                                    <i class="bi bi-cart-check text-success me-1"></i>Auto-imprimir Ventas
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2 ms-1">Cada venta creada se imprimira automaticamente.</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="auto_imprimir_cotizaciones" value="1" id="auto_imprimir_cotizaciones" {{ old('auto_imprimir_cotizaciones') ? 'checked' : '' }} role="switch" style="width: 3rem; height: 1.5rem; cursor: pointer;">
                                <label class="form-check-label fw-semibold ms-2" for="auto_imprimir_cotizaciones" style="cursor: pointer;">
                                    <i class="bi bi-file-earmark-text text-info me-1"></i>Auto-imprimir Cotizaciones
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2 ms-1">Cada cotizacion se imprimira automaticamente.</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="auto_imprimir_conduces" value="1" id="auto_imprimir_conduces" {{ old('auto_imprimir_conduces') ? 'checked' : '' }} role="switch" style="width: 3rem; height: 1.5rem; cursor: pointer;">
                                <label class="form-check-label fw-semibold ms-2" for="auto_imprimir_conduces" style="cursor: pointer;">
                                    <i class="bi bi-truck text-warning me-1"></i>Auto-imprimir Conduces
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2 ms-1">Cada conduce (nota de entrega) se imprimira automaticamente.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card" style="--delay:.25s">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', true) ? 'checked' : '' }} role="switch" style="width: 3rem; height: 1.5rem; cursor: pointer;">
                                <label class="form-check-label fw-semibold ms-2" for="activo" style="cursor: pointer;">
                                    <i class="bi bi-check-circle text-success me-1"></i>Impresora activa
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2 ms-1">Si esta activa, sera usada para imprimir automaticamente.</small>
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
        <a href="{{ route('impresoras.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
        <button type="submit" form="impresoraForm" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>Guardar Impresora
        </button>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    const typeSelectors = ['tipo_conexion'];
    let currentType = '{{ old("tipo_conexion", "") }}';
    if (!currentType) currentType = 'local';

    $('input[name="tipo_conexion"]').on('change', function() {
        const val = $(this).val();
        $('.conn-section').removeClass('active');
        $('#conn_' + val).addClass('active');
    });

    $('#papel_tamano').on('change', function() {
        const val = $(this).val();
        if (val === '58mm') {
            $('#caracteres_por_linea').val(42);
        } else if (val === '80mm') {
            $('#caracteres_por_linea').val(48);
        } else if (val === 'A4') {
            $('#caracteres_por_linea').val(80);
        }
    });

    // Auto-fill characters based on paper size
    if ($('#papel_tamano').val()) {
        $('#papel_tamino').trigger('change');
    }
});
</script>
@endpush
@endsection
