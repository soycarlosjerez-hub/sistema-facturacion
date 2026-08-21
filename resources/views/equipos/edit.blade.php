@extends('layouts.app')
@section('title', 'Editar Equipo')

@push('styles')
@include('partials.premium-ui')
<style>
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#38bdf8;--accent-rgb:56,189,248;--accent-hover:#0ea5e9;">

    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-phone"></i>
                </div>
                <div>
                    <div class="ui-header-title">Editar Equipo</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-pencil me-1"></i>
                        Actualiza la información del equipo
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('equipos.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert rounded-4 shadow-sm border-0 mb-4" style="background:rgba(56,189,248,.05);border-left:4px solid #38bdf8 !important;">
        <div class="d-flex align-items-center">
            <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;color:#38bdf8;background:rgba(56,189,248,.1);">
                <i class="bi bi-info-circle fs-5"></i>
            </div>
            <div>
                <span class="text-muted">Editando el equipo:</span>
                <strong class="d-block" style="font-size:1.1rem;color:#1e293b;">{{ $equipo->serial_imei }} - {{ $equipo->marca }} {{ $equipo->modelo }}</strong>
            </div>
        </div>
    </div>

    <form id="equipoForm" action="{{ route('equipos.update', $equipo) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Sección 1: Identificación del Equipo --}}
        <div class="ui-card" style="--delay:.1s;">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <h6 class="fw-bold mb-3" style="color: #0891b2;">
                    <i class="bi bi-phone me-2"></i>Identificación del Equipo
                </h6>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Serial / IMEI <span class="text-danger">*</span></label>
                            <input type="text" name="serial_imei" value="{{ old('serial_imei', $equipo->serial_imei) }}" class="ui-input @error('serial_imei') is-invalid @enderror" required placeholder="Ej. ABC123456789" autocomplete="off">
                            @error('serial_imei')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Serial ESN</label>
                            <input type="text" name="serial_esn" value="{{ old('serial_esn', $equipo->serial_esn) }}" class="ui-input @error('serial_esn') is-invalid @enderror" placeholder="Ej. ESN001234">
                            @error('serial_esn')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Marca <span class="text-danger">*</span></label>
                            <input type="text" name="marca" value="{{ old('marca', $equipo->marca) }}" class="ui-input @error('marca') is-invalid @enderror" required placeholder="Ej. Apple, Samsung">
                            @error('marca')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Modelo <span class="text-danger">*</span></label>
                            <input type="text" name="modelo" value="{{ old('modelo', $equipo->modelo) }}" class="ui-input @error('modelo') is-invalid @enderror" required placeholder="Ej. iPhone 15 Pro, Galaxy S24">
                            @error('modelo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Color</label>
                            <input type="text" name="color" value="{{ old('color', $equipo->color) }}" class="ui-input @error('color') is-invalid @enderror" placeholder="Ej. Negro, Blanco">
                            @error('color')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Almacenamiento (GB)</label>
                            <input type="number" name="almacenamiento_gb" value="{{ old('almacenamiento_gb', $equipo->almacenamiento_gb) }}" class="ui-input @error('almacenamiento_gb') is-invalid @enderror" min="0" placeholder="128, 256...">
                            @error('almacenamiento_gb')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sección 2: Especificaciones Técnicas --}}
        <div class="ui-card" style="--delay:.15s;">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <h6 class="fw-bold mb-3" style="color: #7c3aed;">
                    <i class="bi bi-cpu me-2"></i>Especificaciones Técnicas
                </h6>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Tipo de Dispositivo</label>
                            <select name="tipo_dispositivo" class="ui-select">
                                <option value="">Sin especificar</option>
                                @foreach(['celular','laptop','desktop','tablet','servidor','impresora','monitor','router','switch','camara','ups','otro'] as $opt)
                                    <option value="{{ $opt }}" {{ old('tipo_dispositivo', $equipo->tipo_dispositivo ?? '') == $opt ? 'selected' : '' }}>{{ ucfirst($opt) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Procesador</label>
                            <input type="text" name="procesador" value="{{ old('procesador', $equipo->procesador) }}" class="ui-input" placeholder="Ej. M3 Pro, i7-13700K">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Memoria RAM</label>
                            <input type="text" name="memoria_ram" value="{{ old('memoria_ram', $equipo->memoria_ram) }}" class="ui-input" placeholder="Ej. 16GB, 32GB">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Tipo de Almacenamiento</label>
                            <select name="almacenamiento_tipo" class="ui-select">
                                <option value="">Sin especificar</option>
                                @foreach(['HDD','SSD','NVMe','hybrid'] as $opt)
                                    <option value="{{ $opt }}" {{ old('almacenamiento_tipo', $equipo->almacenamiento_tipo ?? '') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Capacidad de Almacenamiento</label>
                            <input type="text" name="almacenamiento_capacidad" value="{{ old('almacenamiento_capacidad', $equipo->almacenamiento_capacidad) }}" class="ui-input" placeholder="Ej. 512GB, 1TB">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Sistema Operativo</label>
                            <input type="text" name="sistema_operativo" value="{{ old('sistema_operativo', $equipo->sistema_operativo) }}" class="ui-input" placeholder="Ej. iOS 17, Windows 11">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Puertos</label>
                            <textarea name="puertos" class="ui-input" rows="2" placeholder="Ej. USB-C, HDMI, 3.5mm audio...">{{ old('puertos', $equipo->puertos) }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Peso (gramos)</label>
                            <input type="number" name="peso_gramos" value="{{ old('peso_gramos', $equipo->peso_gramos) }}" class="ui-input" min="0" step="0.01" placeholder="Ej. 187">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sección 3: Precios y Estado --}}
        <div class="ui-card" style="--delay:.2s;">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <h6 class="fw-bold mb-3" style="color: #059669;">
                    <i class="bi bi-cash-stack me-2"></i>Precios y Estado
                </h6>
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Estado <span class="text-danger">*</span></label>
                            <select name="estado" class="ui-select @error('estado') is-invalid @enderror" required>
                                <option value="">Seleccionar...</option>
                                @foreach(['disponible','vendido','en_reparacion','dañado','reservado','mantenimiento'] as $opt)
                                    <option value="{{ $opt }}" {{ old('estado', $equipo->estado) == $opt ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $opt)) }}</option>
                                @endforeach
                            </select>
                            @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Precio de Venta <span class="text-danger">*</span></label>
                            <div class="ui-input-group input-group-lg">
                                <span class="ui-input-group-text bg-light fw-bold">$</span>
                                <input type="number" name="precio_venta" value="{{ old('precio_venta', $equipo->precio_venta) }}" class="ui-input @error('precio_venta') is-invalid @enderror" step="0.01" min="0" required placeholder="0.00">
                            </div>
                            @error('precio_venta')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Precio de Compra</label>
                            <div class="ui-input-group input-group-lg">
                                <span class="ui-input-group-text bg-light fw-bold">$</span>
                                <input type="number" name="precio_compra" value="{{ old('precio_compra', $equipo->precio_compra) }}" class="ui-input" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Proveedor</label>
                            <select name="comprado_a_proveedor_id" class="ui-select">
                                <option value="">Sin proveedor</option>
                                @foreach($proveedores as $prov)
                                    <option value="{{ $prov->id }}" {{ old('comprado_a_proveedor_id', $equipo->comprado_a_proveedor_id) == $prov->id ? 'selected' : '' }}>{{ $prov->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Fecha de Compra</label>
                            <input type="date" name="fecha_compra" value="{{ old('fecha_compra', $equipo->fecha_compra?->format('Y-m-d')) }}" class="ui-input">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Factura de Compra</label>
                            <input type="text" name="factura_compra" value="{{ old('factura_compra', $equipo->factura_compra) }}" class="ui-input" placeholder="Ej. F-000123">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sección 4: Garantía --}}
        <div class="ui-card" style="--delay:.25s;">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <h6 class="fw-bold mb-3" style="color: #ca8a04;">
                    <i class="bi bi-shield-check me-2"></i>Garantía
                </h6>
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Tipo de Garantía</label>
                            <select name="garantia_tipo" class="ui-select">
                                <option value="">Sin garantía</option>
                                <option value="fabrica" {{ old('garantia_tipo', $equipo->garantia_tipo ?? '') == 'fabrica' ? 'selected' : '' }}>De Fábrica</option>
                                <option value="extendida" {{ old('garantia_tipo', $equipo->garantia_tipo ?? '') == 'extendida' ? 'selected' : '' }}>Extendida</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Garantía Desde</label>
                            <input type="date" name="garantia_desde" value="{{ old('garantia_desde', $equipo->garantia_desde?->format('Y-m-d')) }}" class="ui-input">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Garantía Hasta</label>
                            <input type="date" name="garantia_hasta" value="{{ old('garantia_hasta', $equipo->garantia_hasta?->format('Y-m-d')) }}" class="ui-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sección 5: Bloqueos y Observaciones --}}
        <div class="ui-card" style="--delay:.3s;">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <h6 class="fw-bold mb-3" style="color: #dc2626;">
                    <i class="bi bi-lock me-2"></i>Bloqueos y Observaciones
                </h6>
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(220,38,38,.04);">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="bloqueado_icloud" value="1" id="chk_bloqueado_icloud" role="switch" style="width:3em;height:1.5em;" {{ old('bloqueado_icloud', $equipo->bloqueado_icloud) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold ms-2" for="chk_bloqueado_icloud">Bloqueado iCloud</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(220,38,38,.04);">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="bloqueado_fr" value="1" id="chk_bloqueado_fr" role="switch" style="width:3em;height:1.5em;" {{ old('bloqueado_fr', $equipo->bloqueado_fr) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold ms-2" for="chk_bloqueado_fr">Bloqueado FR</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Observaciones</label>
                            <textarea name="observaciones" class="ui-input" rows="3" placeholder="Notas adicionales sobre el equipo...">{{ old('observaciones', $equipo->observaciones) }}</textarea>
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
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#38bdf8;"></i>
            <span class="fw-semibold d-none d-sm-inline">Editando: {{ $equipo->serial_imei }} - {{ $equipo->marca }} {{ $equipo->modelo }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('equipos.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="equipoForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-cloud-arrow-up me-1"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>
@endsection
