@extends('layouts.app')
@section('title', 'Nuevo Ajuste de Stock')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-box-arrow-up-right"></i></div>
                <div>
                    <h4 class="ui-header-title">Nuevo Ajuste de Stock</h4>
                    <div class="ui-header-meta"><i class="bi bi-plus-circle me-1"></i><span>Registra una variación en el inventario</span></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('stock-adjustments.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
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

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('stock-adjustments.store') }}" method="POST" id="ajusteForm">
                @csrf

                <!-- Sucursal + Almacén -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <label class="ui-label">Sucursal</label>
                        <select name="sucursal_id" class="ui-select" id="sucursalSelect">
                            <option value="">Global (sin sucursal)</option>
                            @foreach(\App\Models\Sucursal::where('activa', true)->orderBy('nombre')->get() as $s)
                                <option value="{{ $s->id }}" {{ old('sucursal_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Almacén</label>
                        <select name="almacen_id" class="ui-select" id="almacenSelect">
                            <option value="">Global (sin almacén)</option>
                            @foreach($almacenes as $almacen)
                                <option value="{{ $almacen->id }}" {{ old('almacen_id') == $almacen->id ? 'selected' : '' }}>{{ $almacen->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Producto + Tipo -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <label class="ui-label">Producto <span class="text-danger">*</span></label>
                        <select name="producto_id" id="productoSelect" class="ui-select" required>
                            <option value="" disabled selected>Seleccionar producto...</option>
                            @foreach($productos as $p)
                                <option value="{{ $p->id }}" data-stock="{{ $p->stock }}" {{ old('producto_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nombre }} (Stock: {{ $p->stock }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Tipo de Ajuste <span class="text-danger">*</span></label>
                        <select name="tipo" class="ui-select" required>
                            <option value="" disabled selected>Seleccionar tipo...</option>
                            <option value="ajuste" {{ old('tipo') == 'ajuste' ? 'selected' : '' }}>Ajuste General</option>
                            <option value="merma" {{ old('tipo') == 'merma' ? 'selected' : '' }}>Mermas / Pérdidas</option>
                            <option value="inventario" {{ old('tipo') == 'inventario' ? 'selected' : '' }}>Inventario Físico</option>
                            <option value="dacion" {{ old('tipo') == 'dacion' ? 'selected' : '' }}>Dación</option>
                            <option value="recepcion" {{ old('tipo') == 'recepcion' ? 'selected' : '' }}>Recepción Extra</option>
                        </select>
                    </div>
                </div>

                <!-- Cantidades -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label class="ui-label">Cantidad Anterior <span class="text-danger">*</span></label>
                        <input type="number" name="cantidad_anterior" class="ui-input" id="cantidadAnterior" value="{{ old('cantidad_anterior') }}" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Cantidad Nueva <span class="text-danger">*</span></label>
                        <input type="number" name="cantidad_nueva" class="ui-input" id="cantidadNueva" value="{{ old('cantidad_nueva') }}" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Diferencia</label>
                        <input type="text" class="ui-input text-center fw-bold" id="diferencia" readonly value="{{ old('diferencia', '0') }}" style="background:#f8fafc;">
                    </div>
                </div>

                <!-- Motivo y Notas -->
                <div class="row g-4">
                    <div class="col-lg-6">
                        <label class="ui-label">Motivo</label>
                        <input type="text" name="motivo" class="ui-input" value="{{ old('motivo') }}" placeholder="Motivo del ajuste..." maxlength="500">
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Notas</label>
                        <textarea name="notas" class="ui-input" rows="3" placeholder="Notas adicionales...">{{ old('notas') }}</textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('stock-adjustments.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
        <button type="submit" form="ajusteForm" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>Guardar Ajuste
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('productoSelect')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const stock = opt.getAttribute('data-stock');
    if (stock) {
        document.getElementById('cantidadAnterior').value = stock;
        calcDiferencia();
    }
});

function calcDiferencia() {
    const antes = parseInt(document.getElementById('cantidadAnterior').value) || 0;
    const despues = parseInt(document.getElementById('cantidadNueva').value) || 0;
    const d = despues - antes;
    const el = document.getElementById('diferencia');
    el.value = (d >= 0 ? '+' : '') + d;
    el.style.color = d >= 0 ? '#10b981' : '#ef4444';
}

document.getElementById('cantidadAnterior')?.addEventListener('input', calcDiferencia);
document.getElementById('cantidadNueva')?.addEventListener('input', calcDiferencia);
calcDiferencia();
</script>
@endpush
