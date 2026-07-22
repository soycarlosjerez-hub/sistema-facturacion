@extends('layouts.app')

@section('title', 'Editar ' . $impresora->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
    body.dark-mode .ui-header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-printer"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Impresora</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-pencil me-1"></i>
                        <span>{{ $impresora->nombre }}</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('impresoras.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
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

    <div class="ui-card mb-5" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-4">
            <form method="POST" action="{{ route('impresoras.update', $impresora) }}" id="instanceForm">
                @csrf @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label">Nombre <span class="text-danger">*</span></label>
                        <input name="nombre" class="ui-input" required value="{{ old('nombre', $impresora->nombre) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="ui-label">Tipo Conexión</label>
                        <select name="tipo_conexion" class="ui-select" id="tipoConexion">
                            @foreach(App\Models\Impresora::TIPOS_CONEXION as $k => $v)
                                <option value="{{ $k }}" {{ old('tipo_conexion', $impresora->tipo_conexion)==$k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="ui-label">Driver</label>
                        <select name="driver" class="ui-select">
                            @foreach(App\Models\Impresora::DRIVERS as $k => $v)
                                <option value="{{ $k }}" {{ old('driver', $impresora->driver)==$k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 conexion-red" style="{{ $impresora->tipo_conexion !== 'red' ? 'display:none' : '' }}">
                        <label class="ui-label">Dirección IP</label>
                        <input name="direccion_ip" class="ui-input" value="{{ old('direccion_ip', $impresora->direccion_ip) }}">
                    </div>
                    <div class="col-md-2 conexion-red" style="{{ $impresora->tipo_conexion !== 'red' ? 'display:none' : '' }}">
                        <label class="ui-label">Puerto</label>
                        <input name="puerto" type="number" class="ui-input" value="{{ old('puerto', $impresora->puerto ?? 9100) }}">
                    </div>
                    <div class="col-md-6 conexion-local conexion-compartida" style="{{ !in_array($impresora->tipo_conexion, ['local','compartida']) ? 'display:none' : '' }}">
                        <label class="ui-label">Ruta / Puerto</label>
                        <input name="ruta_compartida" class="ui-input" value="{{ old('ruta_compartida', $impresora->ruta_compartida) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="ui-label">Tamaño Papel</label>
                        <select name="papel_tamano" class="ui-select">
                            @foreach(App\Models\Impresora::TAMANOS_PAPEL as $k => $v)
                                <option value="{{ $k }}" {{ old('papel_tamano', $impresora->papel_tamano)==$k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="ui-label">Caracteres/Línea</label>
                        <input name="caracteres_por_linea" type="number" class="ui-input" value="{{ old('caracteres_por_linea', $impresora->caracteres_por_linea) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="ui-label">Orden</label>
                        <input name="orden" type="number" class="ui-input" value="{{ old('orden', $impresora->orden) }}">
                    </div>

                    <div class="col-12">
                        <label class="ui-label">Descripción</label>
                        <textarea name="descripcion" class="ui-input" rows="2">{{ old('descripcion', $impresora->descripcion) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="ui-label">Auto-Impresión</label>
                        <div class="d-flex gap-4">
                            @foreach(['ventas','cotizaciones','conduces'] as $mod)
                            <div class="form-check">
                                <input type="checkbox" name="auto_imprimir_{{ $mod }}" class="form-check-input" value="1"
                                    id="auto{{ $mod }}" {{ $impresora->{'auto_imprimir_'.$mod} ? 'checked' : '' }}>
                                <label class="form-check-label" for="auto{{ $mod }}">{{ ucfirst($mod) }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" {{ $impresora->activo ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="activo">Activa</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('impresoras.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">Cancelar</a>
        <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
            <i class="bi bi-save me-2"></i>Guardar Cambios
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('tipoConexion')?.addEventListener('change', function() {
    document.querySelectorAll('.conexion-red, .conexion-local, .conexion-compartida').forEach(el => el.style.display = 'none');
    if (this.value === 'red') {
        document.querySelectorAll('.conexion-red').forEach(el => el.style.display = 'block');
    } else if (this.value === 'local' || this.value === 'compartida') {
        document.querySelectorAll('.conexion-' + this.value).forEach(el => el.style.display = 'block');
    }
});
</script>
@endpush