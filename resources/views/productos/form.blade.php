<div class="card-body p-4 p-md-5">

    {{-- Seleccionar tipo de servicio --}}
    <div class="mb-4 pb-3 border-bottom" style="border-bottom:2px solid #a855f7 !important;">
        <h6 class="fw-bold mb-0" style="color: #a855f7;">
            <i class="bi bi-tag me-2"></i>Tipo de Registro
        </h6>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Selecciona el tipo <span class="text-danger">*</span></label>
                <select name="tipo_servicio" id="tipo_servicio" class="ui-select @error('tipo_servicio') is-invalid @enderror" required>
                    <option value="producto" {{ old('tipo_servicio', isset($producto->tipo_servicio) ? $producto->tipo_servicio : 'producto') == 'producto' ? 'selected' : '' }}>
                        📦 Producto Físico (con inventario, stock y código de barras)
                    </option>
                    <option value="servicio" {{ old('tipo_servicio', isset($producto->tipo_servicio) ? $producto->tipo_servicio : 'producto') == 'servicio' ? 'selected' : '' }}>
                        ✂️ Servicio (sin inventario, sin stock, sin código)
                    </option>
                    <option value="general" {{ old('tipo_servicio', isset($producto->tipo_servicio) ? $producto->tipo_servicio : 'producto') == 'general' ? 'selected' : '' }}>
                        📋 General (solo nombre y precio, sin stock)
                    </option>
                </select>
                @error('tipo_servicio')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div id="tipo-servicio-hint" class="p-3 rounded-3" style="display:none;border:1px solid rgba(168,85,247,.2);background:rgba(168,85,247,.05);font-size:.85rem;">
                <i class="bi bi-info-circle me-2" style="color:#a855f7;"></i>
                <span id="hint-text"></span>
            </div>
        </div>
    </div>

    {{-- Section 1: Información Básica --}}
    <div class="mb-4 pb-3 border-bottom">
        <h6 class="fw-bold mb-0" style="color: #4f46e5;">
            <i class="bi bi-box-seam me-2"></i>Información Básica
        </h6>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}" class="ui-input @error('nombre') is-invalid @enderror" required placeholder="Ej. Arroz Campo 5lbs">
                @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3 campo-producto">
                <label class="ui-label small fw-semibold">Código de Barras</label>
                <div class="ui-input-group input-group-lg">
                    <input type="text" id="codigo_barras" name="codigo_barras" value="{{ old('codigo_barras', $producto->codigo_barras ?? '') }}" class="ui-input @error('codigo_barras') is-invalid @enderror" placeholder="Escanear o generar" autocomplete="off">
                    <button class="ui-btn ui-btn-ghost px-3" type="button" id="btnGenerarBarcode" title="Generar código de barras">
                        <i class="bi bi-magic"></i>
                    </button>
                </div>
                @error('codigo_barras')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <small class="text-muted">Escanea con tu lector o haz clic en <i class="bi bi-magic"></i> para generar uno único.</small>
            </div>

            <div class="mb-3">
                <label class="ui-label small fw-semibold">Descripción</label>
                <textarea name="descripcion" class="ui-input @error('descripcion') is-invalid @enderror" rows="3" placeholder="Detalles del producto...">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                @error('descripcion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Categoría <span class="text-muted">(opcional)</span></label>
                <select name="categoria_id" class="ui-select">
                    <option value="">Sin categoría</option>
                    @if(isset($categorias))
                        @foreach($categorias as $c)
                            <option value="{{ $c->id }}" {{ old('categoria_id', $producto->categoria_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mb-3 campo-producto">
                <label class="ui-label small fw-semibold">Unidad de Medida</label>
                <select name="unidad_medida" class="ui-select">
                    @php $unidad = old('unidad_medida', $producto->unidad_medida ?? 'Unidad'); @endphp
                    @foreach(['Unidad', 'Libra', 'Kilogramo', 'Litro', 'Galón', 'Caja', 'Paquete', 'Docena', 'Bulto'] as $op)
                        <option value="{{ $op }}" {{ $unidad == $op ? 'selected' : '' }}>{{ $op }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Section 2: Precios --}}
    <div class="mb-4 pb-3 border-bottom">
        <h6 class="fw-bold mb-0" style="color: #059669;">
            <i class="bi bi-currency-dollar me-2"></i>Precios e Impuestos
        </h6>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Precio de Venta <span class="text-danger">*</span></label>
                <div class="ui-input-group input-group-lg">
                    <span class="ui-input-group-text bg-light fw-bold">$</span>
                    <input type="number" name="precio" value="{{ old('precio', $producto->precio ?? '') }}" class="ui-input @error('precio') is-invalid @enderror" step="0.01" min="0" required placeholder="0.00">
                </div>
                @error('precio')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3 campo-producto">
                <label class="ui-label small fw-semibold">Precio de Compra</label>
                <div class="ui-input-group input-group-lg">
                    <span class="ui-input-group-text bg-light fw-bold">$</span>
                    <input type="number" name="precio_compra" value="{{ old('precio_compra', $producto->precio_compra ?? '') }}" class="ui-input @error('precio_compra') is-invalid @enderror" step="0.01" min="0" placeholder="0.00">
                </div>
                @error('precio_compra')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <small class="text-muted">Para calcular tu margen de ganancia.</small>
            </div>

            <div class="mb-3">
                <label class="ui-label small fw-semibold">ITBIS</label>
                <div class="ui-input-group input-group-lg">
                    <input type="number" name="itbis_porcentaje" id="itbis_porcentaje" value="{{ old('itbis_porcentaje', $producto->itbis_porcentaje ?? ($producto->tipo_servicio === 'servicio' ? '0' : ($systemItbis ?? 18))) }}" class="ui-input @error('itbis_porcentaje') is-invalid @enderror" step="0.01" min="0" max="100" placeholder="{{ $systemItbis ?? 18 }}">
                    <span class="ui-input-group-text bg-light fw-bold">%</span>
                </div>
                @error('itbis_porcentaje')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <small class="text-muted">ITBIS por defecto para servicios: 0%. Para productos exentos usa 0.</small>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3 campo-producto">
                <label class="ui-label small fw-semibold">Stock Actual</label>
                <input type="number" name="stock" id="stock" value="{{ old('stock', $producto->stock ?? '') }}" class="ui-input @error('stock') is-invalid @enderror" min="0" placeholder="0">
                @error('stock')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3 campo-producto">
                <label class="ui-label small fw-semibold">Stock Mínimo</label>
                <input type="number" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo ?? '0') }}" class="ui-input" min="0" placeholder="0">
                <small class="text-muted">Recibirás alerta cuando el stock baje de este valor.</small>
            </div>
        </div>
    </div>

    {{-- Section 3: Imagen (solo productos) --}}
    <div class="mb-4 pb-3 border-bottom">
        <h6 class="fw-bold mb-0" style="color: #0891b2;">
            <i class="bi bi-image me-2"></i>Imagen <span class="text-muted">(solo productos físicos)</span>
        </h6>
    </div>
    <div class="row g-4 campo-producto">
        <div class="col-md-6">
            <input type="file" name="imagen" class="ui-input @error('imagen') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
            @error('imagen')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            <small class="text-muted d-block">Formatos: JPG, PNG, WEBP. Máx. 10 MB.</small>
        </div>
        @if(isset($producto) && $producto->tipo_servicio === 'producto')
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                <img src="{{ $producto->imagen_url }}" width="100" height="100" style="object-fit: cover; border-radius: 10px;" class="shadow-sm border" alt="Imagen actual">
                <div>
                    <strong class="d-block small">{{ $producto->tiene_imagen ? 'Imagen actual' : 'Sin imagen' }}</strong>
                    <span class="text-muted small">Sube una nueva imagen para reemplazarla.</span>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Section 4: Estado --}}
    <div class="mb-4 pb-3 border-bottom">
        <h6 class="fw-bold mb-0" style="color: #059669;">
            <i class="bi bi-toggle-on me-2"></i>Estado
        </h6>
    </div>
    <div class="row g-4">
        <div class="col-md-12">
            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(5,150,105,.05);">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="chk-activo" {{ old('activo', !isset($producto->exists) || !$producto->exists ? true : $producto->activo) ? 'checked' : '' }} role="switch" style="width:3em;height:1.5em;">
                    <label class="form-check-label fw-semibold ms-2" for="chk-activo">
                        Activo
                    </label>
                </div>
                <small class="text-muted">Si está inactivo no aparecerá en las listas de ventas ni en el catálogo.</small>
            </div>
        </div>
    </div>

    {{-- Section 5: Cocina / KDS (solo restaurante) --}}
    @php
        $tipoNegocio = session('business_type_slug') ?? auth()->user()->businessInstance?->businessType?->slug;
        $esNegocioRestaurante = in_array($tipoNegocio, ['restaurante', 'mixto']);
    @endphp
    @if($esNegocioRestaurante && old('tipo_servicio', $producto->tipo_servicio ?? 'producto') === 'producto')
    <div class="mb-4 pb-3 border-bottom mt-4" style="border-bottom:2px solid #f97316 !important;">
        <h6 class="fw-bold mb-0" style="color: #f97316;">
            <i class="bi bi-fire me-2"></i>Cocina / KDS
        </h6>
    </div>
    <div class="row g-4 campo-producto">
        <div class="col-md-12">
            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(249,115,22,.06);">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="incluir_kds" value="1" id="chk-incluir-kds" {{ old('incluir_kds', !isset($producto->exists) || !$producto->exists ? true : $producto->incluir_kds) ? 'checked' : '' }} role="switch" style="width:3em;height:1.5em;">
                    <label class="form-check-label fw-semibold ms-2" for="chk-incluir-kds">
                        Enviar a Cocina (KDS)
                    </label>
                </div>
                <small class="text-muted">Con esta opción activa, el producto aparecerá en la pantalla de cocina cuando el mesero lo envíe. Desactívala para bebidas, panes, postres o productos que no necesitan preparación en cocina.</small>
            </div>
        </div>
    </div>
    @endif

    {{-- Section 6: Tecnología / Telecomunicaciones --}}
    @if(in_array($tipoNegocio, ['tecnologia', 'tienda', 'mixto']))
    @php $esServicio = in_array(old('tipo_servicio', $producto->tipo_servicio ?? ''), ['servicio','general']); @endphp
    <div class="mb-4 pb-3 border-bottom mt-4 seccion-tec" style="border-bottom:2px solid #3b82f6 !important;{{ $esServicio ? 'display:none;' : '' }}">
        <h6 class="fw-bold mb-0" style="color: #3b82f6;">
            <i class="bi bi-cpu me-2"></i>Tecnología / Telecomunicaciones
        </h6>
    </div>
    <div class="row g-4 mb-4 seccion-tec" style="{{ $esServicio ? 'display:none;' : '' }}">
        <div class="col-md-4">
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Especialización</label>
                <select name="especializacion" class="ui-select">
                    <option value="">Ninguna</option>
                    @foreach(['Laptops', 'PC de Escritorio', 'Tablets', 'Smartphones', 'Accesorios', 'Impresoras', 'Redes', 'Cámaras', 'Audio/Video', 'Gaming', 'Servidores', 'Almacenamiento', 'Software', 'Soporte Técnico'] as $op)
                        <option value="{{ $op }}" {{ old('especializacion', $producto->especializacion ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Marca Tecnológica</label>
                <input type="text"
                       class="ui-input"
                       id="marca_input"
                       list="marcas_list"
                       placeholder="Buscar marca..."
                       autocomplete="off"
                       oninput="syncMarcaId(this.value)">
                <datalist id="marcas_list">
                    @if(isset($marcasTecnicas))
                    @foreach($marcasTecnicas as $marca)
                    <option value="{{ $marca->nombre }}" data-id="{{ $marca->id }}">{{ $marca->nombre }}{{ $marca->pais ? ' (' . $marca->pais . ')' : '' }}</option>
                    @endforeach
                    @endif
                </datalist>
                <input type="hidden" name="marca_tecnologica_id" id="marca_tecnologica_id" value="{{ old('marca_tecnologica_id', $producto->marca_tecnologica_id ?? '') }}">
                <small class="text-muted">Escribe para buscar una marca</small>
            </div>
            <div class="mb-3">
                <label class="ui-label small fw-semibold text-muted">Marca libre <span class="text-muted">(opcional)</span></label>
                <input type="text" name="marca" value="{{ old('marca', $producto->marca ?? '') }}" class="ui-input small" placeholder="O ingresa una marca manualmente si no está en la lista">
            </div>
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Modelo</label>
                <input type="text" name="modelo" value="{{ old('modelo', $producto->modelo ?? '') }}" class="ui-input" placeholder="Ej. Galaxy S24, MacBook Pro">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Almacenamiento (GB)</label>
                <input type="number" name="almacenamiento_gb" value="{{ old('almacenamiento_gb', $producto->almacenamiento_gb ?? '') }}" class="ui-input" min="0" placeholder="Ej. 256, 512, 1024">
            </div>
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Color</label>
                <input type="text" name="color" value="{{ old('color', $producto->color ?? '') }}" class="ui-input" placeholder="Ej. Negro, Plateado">
            </div>
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Garantía (días)</label>
                <input type="number" name="garantia_dias" value="{{ old('garantia_dias', $producto->garantia_dias ?? '') }}" class="ui-input" min="0" placeholder="Ej. 365">
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background:rgba(59,130,246,.05);">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="vendible_imei" value="1" id="chk-vendible-imei" {{ old('vendible_imei', $producto->vendible_imei ?? false) ? 'checked' : '' }} role="switch" style="width:3em;height:1.5em;">
                    <label class="form-check-label fw-semibold ms-2" for="chk-vendible-imei">Control IMEI</label>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background:rgba(59,130,246,.05);">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="requiere_serial" value="1" id="chk-requiere-serial" {{ old('requiere_serial', $producto->requiere_serial ?? false) ? 'checked' : '' }} role="switch" style="width:3em;height:1.5em;">
                    <label class="form-check-label fw-semibold ms-2" for="chk-requiere-serial">Requiere Serial</label>
                </div>
            </div>
            <div id="campos-serial" style="{{ old('requiere_serial', $producto->requiere_serial ?? false) || old('vendible_imei', $producto->vendible_imei ?? false) ? '' : 'display:none;' }}">
                <div class="mb-3">
                    <label class="ui-label small fw-semibold">IMEI / Serial</label>
                    <input type="text" name="serial_imei" value="{{ old('serial_imei', $producto->serial_imei ?? '') }}" class="ui-input @error('serial_imei') is-invalid @enderror" placeholder="Ej. 35-209900-123456-7 o SN-ABC123456" maxlength="100">
                    @error('serial_imei')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    <small class="text-muted">Número de serie o IMEI del equipo para trazabilidad.</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background:rgba(59,130,246,.05);">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="es_licencia" value="1" id="chk-es-licencia" {{ old('es_licencia', $producto->es_licencia ?? false) ? 'checked' : '' }} role="switch" style="width:3em;height:1.5em;">
                    <label class="form-check-label fw-semibold ms-2" for="chk-es-licencia">Es Licencia</label>
                </div>
            </div>
            <div id="campos-licencia" style="{{ old('es_licencia', $producto->es_licencia ?? false) ? '' : 'display:none;' }}">
                <div class="mb-3">
                    <label class="ui-label small fw-semibold">Tipo Licencia</label>
                    <select name="tipo_licencia" class="ui-select">
                        <option value="">Seleccionar</option>
                        <option value="suscripcion" {{ old('tipo_licencia', $producto->tipo_licencia ?? '') == 'suscripcion' ? 'selected' : '' }}>Suscripción</option>
                        <option value="perpetua" {{ old('tipo_licencia', $producto->tipo_licencia ?? '') == 'perpetua' ? 'selected' : '' }}>Perpetua</option>
                        <option value="trial" {{ old('tipo_licencia', $producto->tipo_licencia ?? '') == 'trial' ? 'selected' : '' }}>Trial</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="ui-label small fw-semibold">Máx. Usuarios</label>
                    <input type="number" name="licencia_max_usuarios" value="{{ old('licencia_max_usuarios', $producto->licencia_max_usuarios ?? '') }}" class="ui-input" min="1" placeholder="Ej. 10">
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Section 7: Climatización --}}
    @if(in_array($tipoNegocio, ['clima', 'tecnologia', 'tienda', 'mixto']))
    <div class="mb-4 pb-3 border-bottom mt-4 seccion-clima" style="border-bottom:2px solid #06b6d4 !important;{{ $esServicio ? 'display:none;' : '' }}">
        <h6 class="fw-bold mb-0" style="color: #06b6d4;">
            <i class="bi bi-snow me-2"></i>Climatización
        </h6>
    </div>
    <div class="row g-4 mb-4 seccion-clima" style="{{ $esServicio ? 'display:none;' : '' }}">
        <div class="col-md-4">
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Tipo Equipo</label>
                <select name="tipo_equipo" class="ui-select">
                    <option value="">Seleccionar</option>
                    <option value="split" {{ old('tipo_equipo', $producto->tipo_equipo ?? '') == 'split' ? 'selected' : '' }}>Split</option>
                    <option value="central" {{ old('tipo_equipo', $producto->tipo_equipo ?? '') == 'central' ? 'selected' : '' }}>Central</option>
                    <option value="portatil" {{ old('tipo_equipo', $producto->tipo_equipo ?? '') == 'portatil' ? 'selected' : '' }}>Portátil</option>
                    <option value="ventana" {{ old('tipo_equipo', $producto->tipo_equipo ?? '') == 'ventana' ? 'selected' : '' }}>Ventana</option>
                    <option value="multisplit" {{ old('tipo_equipo', $producto->tipo_equipo ?? '') == 'multisplit' ? 'selected' : '' }}>Multisplit</option>
                    <option value="otro" {{ old('tipo_equipo', $producto->tipo_equipo ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Capacidad (BTU)</label>
                <input type="number" name="capacidad_btu" value="{{ old('capacidad_btu', $producto->capacidad_btu ?? '') }}" class="ui-input" min="0" placeholder="Ej. 12000, 24000">
            </div>
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Capacidad (Toneladas)</label>
                <input type="number" name="capacidad_toneladas" value="{{ old('capacidad_toneladas', $producto->capacidad_toneladas ?? '') }}" class="ui-input" step="0.1" min="0" placeholder="Ej. 1.5, 2.0">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Eficiencia SEER</label>
                <input type="number" name="eficiencia_seer" value="{{ old('eficiencia_seer', $producto->eficiencia_seer ?? '') }}" class="ui-input" step="0.1" min="0" placeholder="Ej. 16, 20">
            </div>
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Gas Refrigerante</label>
                <select name="gas_refrigerante" class="ui-select">
                    <option value="">Seleccionar</option>
                    <option value="R-410A" {{ old('gas_refrigerante', $producto->gas_refrigerante ?? '') == 'R-410A' ? 'selected' : '' }}>R-410A</option>
                    <option value="R-32" {{ old('gas_refrigerante', $producto->gas_refrigerante ?? '') == 'R-32' ? 'selected' : '' }}>R-32</option>
                    <option value="R-22" {{ old('gas_refrigerante', $producto->gas_refrigerante ?? '') == 'R-22' ? 'selected' : '' }}>R-22</option>
                    <option value="R-290" {{ old('gas_refrigerante', $producto->gas_refrigerante ?? '') == 'R-290' ? 'selected' : '' }}>R-290</option>
                    <option value="otro" {{ old('gas_refrigerante', $producto->gas_refrigerante ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Voltaje</label>
                <input type="text" name="voltaje" value="{{ old('voltaje', $producto->voltaje ?? '') }}" class="ui-input" placeholder="Ej. 110V, 220V">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Peso (KG)</label>
                <input type="number" name="peso_kg" value="{{ old('peso_kg', $producto->peso_kg ?? '') }}" class="ui-input" step="0.1" min="0" placeholder="Ej. 25.5">
            </div>
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Dimensiones</label>
                <input type="text" name="dimensiones" value="{{ old('dimensiones', $producto->dimensiones ?? '') }}" class="ui-input" placeholder="Ej. 80x30x55 cm">
            </div>
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Categoría</label>
                <select name="categoria_clima" class="ui-select">
                    <option value="">Seleccionar</option>
                    <option value="residencial" {{ old('categoria_clima', $producto->categoria_clima ?? '') == 'residencial' ? 'selected' : '' }}>Residencial</option>
                    <option value="comercial" {{ old('categoria_clima', $producto->categoria_clima ?? '') == 'comercial' ? 'selected' : '' }}>Comercial</option>
                    <option value="industrial" {{ old('categoria_clima', $producto->categoria_clima ?? '') == 'industrial' ? 'selected' : '' }}>Industrial</option>
                </select>
            </div>
        </div>
    </div>
    @endif

    {{-- Section 8: Arte --}}
    @if(in_array($tipoNegocio, ['tattoo', 'arte', 'mixto']))
    <div class="mb-4 pb-3 border-bottom mt-4 seccion-arte" style="border-bottom:2px solid #ec4899 !important;">
        <h6 class="fw-bold mb-0" style="color: #ec4899;">
            <i class="bi bi-brush me-2"></i>Arte / Tatuaje
        </h6>
    </div>
    <div class="row g-4 mb-4 seccion-arte">
        <div class="col-md-4">
            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(236,72,153,.05);">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="is_art_piece" value="1" id="chk-is-art" {{ old('is_art_piece', $producto->is_art_piece ?? false) ? 'checked' : '' }} role="switch" style="width:3em;height:1.5em;">
                    <label class="form-check-label fw-semibold ms-2" for="chk-is-art">Es Pieza de Arte / Tatuaje</label>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Precio Servicio</label>
                <div class="ui-input-group input-group-lg">
                    <span class="ui-input-group-text bg-light fw-bold">$</span>
                    <input type="number" name="precio_servicio" value="{{ old('precio_servicio', $producto->precio_servicio ?? '') }}" class="ui-input" step="0.01" min="0" placeholder="0.00">
                </div>
                <small class="text-muted">Precio para servicios de arte/tatuaje.</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label class="ui-label small fw-semibold">Duración (horas)</label>
                <input type="number" name="duracion_servicio_horas" value="{{ old('duracion_servicio_horas', $producto->duracion_servicio_horas ?? '') }}" class="ui-input" step="0.5" min="0" placeholder="Ej. 2.5">
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.campo-producto {
    transition: opacity 0.2s ease, max-height 0.3s ease;
}
.campo-producto.oculto {
    display: none;
}
</style>

@push('scripts')
<script>
    (function() {
        const tipoSelect = document.getElementById('tipo_servicio');
        const campos = document.querySelectorAll('.campo-producto');
        const stockInput = document.getElementById('stock');
        const itbisInput = document.getElementById('itbis_porcentaje');
        const hintDiv = document.getElementById('tipo-servicio-hint');
        const hintText = document.getElementById('hint-text');
        const btnBarcode = document.getElementById('btnGenerarBarcode');

        const hints = {
            producto: '<i class="bi bi-box-seam me-1" style="color:#6366f1;"></i><strong>Producto Físico:</strong> Se controla el inventario (stock, código de barras, precio de compra, unidad de medida, imagen y stock mínimo). Se usa para artículos de tienda.',
            servicio: '<i class="bi bi-scissors me-1" style="color:#a855f7;"></i><strong>Servicio:</strong> No necesita inventario. Solo pide nombre, precio e ITBIS. No pide código de barras, stock, imagen ni precio de compra. Se usa para lavados, encerados, etc.',
            general: '<i class="bi bi-card-heading me-1" style="color:#0ea5e9;"></i><strong>General:</strong> Solo pide nombre, precio e ITBIS. No pide stock ni código de barras. Se usa para artículos mixtos.'
        };

        function toggleCampos() {
            const val = tipoSelect.value;

            campos.forEach(el => {
                if (val === 'servicio' || val === 'general') {
                    el.classList.add('oculto');
                } else {
                    el.classList.remove('oculto');
                }
            });

            // Secciones de tecnología/clima: ocultar si servicio/general
            const seccionesTec = document.querySelectorAll('.seccion-tec');
            const seccionesClima = document.querySelectorAll('.seccion-clima');
            const vis = (val === 'servicio' || val === 'general') ? 'none' : '';
            seccionesTec.forEach(el => el.style.display = vis);
            seccionesClima.forEach(el => el.style.display = vis);

            // Stock: solo obligatorio para producto
            if (stockInput) {
                stockInput.required = (val === 'producto');
            }

            // ITBIS: default 0 para servicio
            if (itbisInput && val === 'servicio' && itbisInput.value !== '0') {
                itbisInput.value = '0';
            }

            // Botón de código de barras
            if (btnBarcode) {
                btnBarcode.disabled = (val === 'servicio' || val === 'general');
                btnBarcode.title = (val === 'servicio' || val === 'general') ? 'No aplica para servicios' : 'Generar código de barras';
            }

            // Hint
            if (hintDiv && hintText) {
                hintDiv.style.display = 'block';
                hintText.innerHTML = hints[val] || '';
            }
        }

        if (tipoSelect) {
            tipoSelect.addEventListener('change', toggleCampos);
            toggleCampos();
        }

        // Toggle licencia fields
        const chkLic = document.getElementById('chk-es-licencia');
        const camposLic = document.getElementById('campos-licencia');
        if (chkLic && camposLic) {
            chkLic.addEventListener('change', function() {
                camposLic.style.display = this.checked ? '' : 'none';
            });
        }

        // Toggle serial/IMEI fields
        const chkSerial = document.getElementById('chk-requiere-serial');
        const chkImei = document.getElementById('chk-vendible-imei');
        const camposSerial = document.getElementById('campos-serial');
        function toggleSerial() {
            camposSerial.style.display = (chkSerial && chkSerial.checked) || (chkImei && chkImei.checked) ? '' : 'none';
        }
        if (chkSerial) chkSerial.addEventListener('change', toggleSerial);
        if (chkImei) chkImei.addEventListener('change', toggleSerial);
        toggleSerial();
    })();

    document.getElementById('btnGenerarBarcode')?.addEventListener('click', function(e) {
        e.preventDefault();
        const input = document.getElementById('codigo_barras');
        if (!input) return;
        if (input.value && !confirm('¿Reemplazar el código de barras actual?')) return;
        let codigo = '200';
        for (let i = 0; i < 9; i++) codigo += Math.floor(Math.random() * 10);
        let suma = 0;
        for (let i = 0; i < 12; i++) { const digito = parseInt(codigo.charAt(i)); suma += (i % 2 === 0) ? digito : digito * 3; }
        const checkDigit = (10 - (suma % 10)) % 10;
        codigo += checkDigit;
        input.value = codigo;
    });

    document.querySelector('input[name="imagen"]')?.addEventListener('change', function() {
        if (this.files[0] && this.files[0].size > 10 * 1024 * 1024) {
            alert('La imagen no debe superar 10MB.');
            this.value = '';
        }
    });

    // Manejar respuesta del formulario "Nueva Marca"
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formNewMarca');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok) {
                    // Éxito: cerrar modal, notificar a Alpine, mostrar toast
                    const modalEl = document.getElementById('newMarcaModal');
                    if (modalEl) {
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                    }

                    // Disparar evento window para que el componente Alpine lo capture
                    window.dispatchEvent(new CustomEvent('marca-created', {
                        detail: {
                            id: null, // El servidor generará el ID
                            nombre: formData.get('nombre'),
                            logo_url: null
                        }
                    }));

                    showToast('Marca registrada correctamente', 'success');

                    // Recargar la página después de un momento para refrescar la lista
                    setTimeout(() => location.reload(), 1200);
                } else if (response.status === 422) {
                    // Errores de validación
                    response.json().then(errors => {
                        displayFormErrors(form, errors);
                    });
                } else {
                    showToast('Error al registrar la marca', 'error');
                }
            })
            .catch(err => {
                showToast('Error de conexión. Intenta de nuevo.', 'error');
            });
        });
    });

    function displayFormErrors(form, errors) {
        // Limpiar errores previos
        form.querySelectorAll('.is-invalid, .text-danger').forEach(el => {
            if (el.classList && el.classList.contains('text-danger')) el.remove();
            if (el.classList && el.classList.contains('is-invalid')) el.classList.remove('is-invalid');
        });

        // Mostrar nuevos errores
        Object.keys(errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-danger small mt-1';
                errorDiv.textContent = errors[field][0];
                input.parentNode.appendChild(errorDiv);
            }
        });
    }

    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toastContainer') || document.querySelector('.toast-container');
        if (!toastContainer) {
            // Crear container si no existe
            const container = document.createElement('div');
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.id = 'toastContainer';
            document.body.appendChild(container);
        }

        const bgClass = type === 'success' ? 'bg-success text-white' : (type === 'error' ? 'bg-danger text-white' : 'bg-info text-white');

        const toastHtml = `
            <div class="toast align-items-center ${bgClass} border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-semibold">
                        <i class="bi bi-${type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-circle' : 'info-circle')} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                </div>
            </div>
        `;

        const container = document.getElementById('toastContainer') || document.querySelector('.toast-container');
        container.insertAdjacentHTML('beforeend', toastHtml);

        setTimeout(() => {
            const toast = container.lastElementChild;
            if (toast) {
                toast.remove();
            }
        }, 4000);
    }
</script>
@endpush
