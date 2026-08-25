@extends('layouts.app')

@section('title', 'Editar Ítem: ' . ($lavaderoPaqueteItem->tipo === 'servicio' ? $lavaderoPaqueteItem->servicio->nombre ?? '' : $lavaderoPaqueteItem->producto->nombre ?? ''))

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">
    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-list-ul"></i></div>
                <div>
                    <h4 class="ui-header-title">Editar Ítem de Paquete</h4>
                    <div class="ui-header-meta"><i class="bi bi-list-check me-1"></i><span>Actualizar servicio o producto del paquete</span></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('lavadero-paquete-items.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="alert rounded-4 shadow-sm border-0 mb-4" style="background:rgba(6,182,212,.05);border-left:4px solid #06b6d4 !important;">
        <div class="d-flex align-items-center">
            <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;color:#06b6d4;background:rgba(6,182,212,.1);">
                <i class="bi bi-info-circle fs-5"></i>
            </div>
            <div>
                <span class="text-muted">Editando ítem de paquete:</span>
                <strong class="d-block" style="font-size:1.1rem;color:#1e293b;">
                    {{ $lavaderoPaqueteItem->tipo === 'servicio' ? ($lavaderoPaqueteItem->servicio->nombre ?? 'Servicio') : ($lavaderoPaqueteItem->producto->nombre ?? 'Producto') }}
                </strong>
            </div>
        </div>
    </div>

    <form id="paquete-item-form" action="{{ route('lavadero-paquete-items.update', $lavaderoPaqueteItem) }}" method="POST">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="ui-card">
                    <div class="ui-card-accent"></div>
                    <div class="card-body p-4">
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="fw-bold mb-0" style="color:#06b6d4;"><i class="bi bi-box-seam me-2"></i>Datos del Ítem</h6>
                        </div>

                        <div class="mb-3">
                            <label class="ui-label">Paquete <span class="text-danger">*</span></label>
                            <select name="paquete_id" class="ui-select" required>
                                <option value="">Seleccionar paquete...</option>
                                @foreach($paquetes as $paquete)
                                <option value="{{ $paquete->id }}" {{ old('paquete_id', $lavaderoPaqueteItem->paquete_id) == $paquete->id ? 'selected' : '' }}>
                                    {{ $paquete->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="ui-label">Tipo <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input type="radio" name="tipo" id="tipo-servicio" value="servicio" class="form-check-input" {{ old('tipo', $lavaderoPaqueteItem->tipo) == 'servicio' ? 'checked' : '' }} onclick="toggleTipo()">
                                    <label class="form-check-label" for="tipo-servicio">Servicio</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="tipo" id="tipo-producto" value="producto" class="form-check-input" {{ old('tipo', $lavaderoPaqueteItem->tipo) == 'producto' ? 'checked' : '' }} onclick="toggleTipo()">
                                    <label class="form-check-label" for="tipo-producto">Producto</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="campo-servicio" style="{{ $lavaderoPaqueteItem->tipo === 'producto' ? 'display:none;' : '' }}">
                            <label class="ui-label">Servicio <span class="text-danger">*</span></label>
                            <select name="servicio_id" class="ui-select" id="select-servicio">
                                <option value="">Seleccionar servicio...</option>
                                @foreach($servicios as $s)
                                <option value="{{ $s->id }}" {{ old('servicio_id', $lavaderoPaqueteItem->servicio_id) == $s->id ? 'selected' : '' }}>
                                    {{ $s->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3" id="campo-producto" style="{{ $lavaderoPaqueteItem->tipo === 'servicio' ? 'display:none;' : '' }}">
                            <label class="ui-label">Producto <span class="text-danger">*</span></label>
                            <select name="producto_id" class="ui-select" id="select-producto">
                                <option value="">Seleccionar producto...</option>
                                @foreach($productos as $p)
                                <option value="{{ $p->id }}" {{ old('producto_id', $lavaderoPaqueteItem->producto_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ui-card">
                    <div class="ui-card-accent"></div>
                    <div class="card-body p-4">
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="fw-bold mb-0" style="color:#f59e0b;"><i class="bi bi-sliders me-2"></i>Configuración</h6>
                        </div>

                        <div class="mb-3">
                            <label class="ui-label">Cantidad</label>
                            <input type="number" name="cantidad" class="ui-input" value="{{ old('cantidad', $lavaderoPaqueteItem->cantidad) }}" min="0.01" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label class="ui-label">Orden</label>
                            <input type="number" name="orden" class="ui-input" value="{{ old('orden', $lavaderoPaqueteItem->orden) }}" min="0">
                        </div>

                        <div class="mt-4">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(6,182,212,0.05);">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="incluir_automatico" value="1" id="chk-incluir-automatico" {{ old('incluir_automatico', $lavaderoPaqueteItem->incluir_automatico) ? 'checked' : '' }} role="switch" style="width:3em;height:1.5em;">
                                    <label class="form-check-label fw-semibold ms-2" for="chk-incluir-automatico">Incluir Automático</label>
                                </div>
                                <small class="text-muted">Se agrega al servicio sin confirmación.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('lavadero-paquete-items.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
        <button type="submit" form="paquete-item-form" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>Actualizar
        </button>
    </div>
</div>

@endsection

@section('scripts')
<script>
function toggleTipo() {
    const isServicio = document.getElementById('tipo-servicio').checked;
    document.getElementById('campo-servicio').style.display = isServicio ? '' : 'none';
    document.getElementById('campo-producto').style.display = isServicio ? 'none' : '';
    if (!isServicio) document.getElementById('select-servicio').value = '';
    else document.getElementById('select-producto').value = '';
}
</script>
@endsection
