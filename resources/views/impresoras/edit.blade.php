@extends('layouts.app')

@section('title', 'Editar ' . $impresora->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
    body.dark-mode .premium-header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-printer"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Editar Impresora</h3>
                    <p class="mb-0 opacity-75">{{ $impresora->nombre }}</p>
                </div>
            </div>
            <a href="{{ route('impresoras.index') }}" class="btn btn-light rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-accent blue"></div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('impresoras.update', $impresora) }}" id="instanceForm">
                @csrf @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input name="nombre" class="form-control border-0 bg-light" required value="{{ old('nombre', $impresora->nombre) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tipo Conexión</label>
                        <select name="tipo_conexion" class="form-select border-0 bg-light" id="tipoConexion">
                            @foreach(App\Models\Impresora::TIPOS_CONEXION as $k => $v)
                                <option value="{{ $k }}" {{ old('tipo_conexion', $impresora->tipo_conexion)==$k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Driver</label>
                        <select name="driver" class="form-select border-0 bg-light">
                            @foreach(App\Models\Impresora::DRIVERS as $k => $v)
                                <option value="{{ $k }}" {{ old('driver', $impresora->driver)==$k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 conexion-red" style="{{ $impresora->tipo_conexion !== 'red' ? 'display:none' : '' }}">
                        <label class="form-label fw-semibold">Dirección IP</label>
                        <input name="direccion_ip" class="form-control border-0 bg-light" value="{{ old('direccion_ip', $impresora->direccion_ip) }}">
                    </div>
                    <div class="col-md-2 conexion-red" style="{{ $impresora->tipo_conexion !== 'red' ? 'display:none' : '' }}">
                        <label class="form-label fw-semibold">Puerto</label>
                        <input name="puerto" type="number" class="form-control border-0 bg-light" value="{{ old('puerto', $impresora->puerto ?? 9100) }}">
                    </div>
                    <div class="col-md-6 conexion-local conexion-compartida" style="{{ !in_array($impresora->tipo_conexion, ['local','compartida']) ? 'display:none' : '' }}">
                        <label class="form-label fw-semibold">Ruta / Puerto</label>
                        <input name="ruta_compartida" class="form-control border-0 bg-light" value="{{ old('ruta_compartida', $impresora->ruta_compartida) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tamaño Papel</label>
                        <select name="papel_tamano" class="form-select border-0 bg-light">
                            @foreach(App\Models\Impresora::TAMANOS_PAPEL as $k => $v)
                                <option value="{{ $k }}" {{ old('papel_tamano', $impresora->papel_tamano)==$k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Caracteres/Línea</label>
                        <input name="caracteres_por_linea" type="number" class="form-control border-0 bg-light" value="{{ old('caracteres_por_linea', $impresora->caracteres_por_linea) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Orden</label>
                        <input name="orden" type="number" class="form-control border-0 bg-light" value="{{ old('orden', $impresora->orden) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control border-0 bg-light" rows="2">{{ old('descripcion', $impresora->descripcion) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Auto-Impresión</label>
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

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-info-circle me-1"></i> Editando impresora: {{ $impresora->nombre }}
        </span>
        <div class="d-flex gap-2 ms-auto">
            <a href="{{ route('impresoras.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
            <button type="submit" form="instanceForm" class="btn btn-save rounded-pill px-5 fw-bold">
                <i class="bi bi-save me-2"></i>Guardar Cambios
            </button>
        </div>
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