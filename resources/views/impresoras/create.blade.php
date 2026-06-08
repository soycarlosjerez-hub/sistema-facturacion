@extends('layouts.app')

@section('title', 'Nueva Impresora')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-printer text-primary me-2"></i>Nueva Impresora</h2>
                    <p class="text-muted mb-0">Registra una nueva impresora para facturación</p>
                </div>
                <a href="{{ route('impresoras.index') }}" class="btn btn-light rounded-pill"><i class="bi bi-arrow-left me-1"></i> Volver</a>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <form method="POST" action="{{ route('impresoras.store') }}">
                    @csrf
                    <div class="card-header bg-light border-bottom border-light p-4">
                        <h5 class="mb-0 fw-semibold"><i class="bi bi-printer me-2"></i>Configuración de Impresora</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                                <input name="nombre" class="form-control border-0 bg-light" required value="{{ old('nombre') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tipo Conexión <span class="text-danger">*</span></label>
                                <select name="tipo_conexion" class="form-select border-0 bg-light" id="tipoConexion">
                                    @foreach(App\Models\Impresora::TIPOS_CONEXION as $k => $v)
                                        <option value="{{ $k }}" {{ old('tipo_conexion', 'red')==$k ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Driver <span class="text-danger">*</span></label>
                                <select name="driver" class="form-select border-0 bg-light">
                                    @foreach(App\Models\Impresora::DRIVERS as $k => $v)
                                        <option value="{{ $k }}" {{ old('driver')==$k ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 conexion-red">
                                <label class="form-label fw-semibold">Dirección IP</label>
                                <input name="direccion_ip" class="form-control border-0 bg-light" value="{{ old('direccion_ip') }}" placeholder="192.168.1.100">
                            </div>
                            <div class="col-md-2 conexion-red">
                                <label class="form-label fw-semibold">Puerto</label>
                                <input name="puerto" type="number" class="form-control border-0 bg-light" value="{{ old('puerto', 9100) }}" placeholder="9100">
                            </div>
                            <div class="col-md-6 conexion-local conexion-compartida">
                                <label class="form-label fw-semibold">Ruta / Puerto</label>
                                <input name="ruta_compartida" class="form-control border-0 bg-light" value="{{ old('ruta_compartida') }}" placeholder="LPT1 o /dev/usb/lp0">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tamaño Papel <span class="text-danger">*</span></label>
                                <select name="papel_tamano" class="form-select border-0 bg-light">
                                    @foreach(App\Models\Impresora::TAMANOS_PAPEL as $k => $v)
                                        <option value="{{ $k }}" {{ old('papel_tamano', '80mm')==$k ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Caracteres por Línea</label>
                                <input name="caracteres_por_linea" type="number" class="form-control border-0 bg-light" value="{{ old('caracteres_por_linea', 42) }}" min="20" max="80">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Orden</label>
                                <input name="orden" type="number" class="form-control border-0 bg-light" value="{{ old('orden', 0) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Descripción</label>
                                <textarea name="descripcion" class="form-control border-0 bg-light" rows="2">{{ old('descripcion') }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Auto-Impresión</label>
                                <div class="d-flex gap-4">
                                    @foreach(['ventas','cotizaciones','conduces'] as $mod)
                                    <div class="form-check">
                                        <input type="checkbox" name="auto_imprimir_{{ $mod }}" class="form-check-input" value="1" id="auto{{ $mod }}">
                                        <label class="form-check-label" for="auto{{ $mod }}">
                                            <i class="bi bi-{{ $mod === 'ventas' ? 'cart' : ($mod === 'cotizaciones' ? 'file-text' : 'truck') }} me-1"></i>
                                            {{ ucfirst($mod) }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="activo" class="form-check-input" value="1" checked id="activo">
                                    <label class="form-check-label fw-semibold" for="activo">Activa</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <a href="{{ route('impresoras.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancelar</a>
                        <button class="btn btn-primary rounded-pill px-4"><i class="bi bi-check-lg me-1"></i>Guardar</button>
                    </div>
                </form>
            </div>
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
document.getElementById('tipoConexion')?.dispatchEvent(new Event('change'));
</script>
@endpush
