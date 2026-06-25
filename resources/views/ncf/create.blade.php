@extends('layouts.app')
@section('title', 'Nueva Secuencia NCF')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .premium-header { background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e); }
body.dark-mode .premium-sticky-bar { border-top-color: #f59e0b; }
body.dark-mode .premium-sticky-bar .btn-save { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,.3); }
body.dark-mode .premium-sticky-bar .btn-save:hover { box-shadow: 0 6px 20px rgba(245,158,11,.45); }
body.dark-mode .premium-card .form-control:focus,
body.dark-mode .premium-card .form-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.15); }
body.dark-mode .premium-card .btn-primary { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,.3); }
body.dark-mode .premium-card .btn-primary:hover { box-shadow: 0 6px 20px rgba(245,158,11,.45); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header d-flex justify-content-between align-items-center mb-4" style="background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e);">
        <div class="d-flex align-items-center gap-3">
            <div class="premium-avatar-circle">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1">Nuevo NCF</h3>
                <p class="mb-0 opacity-75">Registra una nueva secuencia de comprobante fiscal</p>
            </div>
        </div>
        <a href="{{ route('ncf.index') }}" class="btn btn-light rounded-pill text-dark fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

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

    <div class="premium-card mb-5">
        <div class="card-accent amber"></div>
        <form id="ncfForm" action="{{ route('ncf.store') }}" method="POST">
            @csrf
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color: #f59e0b;">
                        <i class="bi bi-info-circle me-2"></i>Información del NCF
                    </h6>
                </div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Nombre del Comprobante</label>
                        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Ej: Crédito Fiscal" required>
                        <small class="text-muted">Descripción para identificar internamente.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Prefijo (3 Letras)</label>
                        <input type="text" name="prefijo" class="form-control rounded-3" maxlength="3" placeholder="B01" required onkeyup="this.value = this.value.toUpperCase()">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Desde (Número)</label>
                        <input type="number" name="desde" class="form-control rounded-3" value="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Hasta (Límite)</label>
                        <input type="number" name="hasta" class="form-control rounded-3" placeholder="1000" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Número Actual</label>
                        <input type="number" name="actual" class="form-control rounded-3" value="0" required>
                        <small class="text-muted">Último número emitido.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="form-control rounded-3" required>
                        <small class="text-muted">Fecha límite autorizada por DGII.</small>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="stickySaveBar" class="premium-sticky-bar d-flex justify-content-between align-items-center">
    <div>
        <span class="fw-semibold" style="color: #f59e0b;"><i class="bi bi-shield-check me-1"></i> Creando nueva secuencia NCF</span>
    </div>
    <button type="submit" form="ncfForm" class="btn-save rounded-pill px-5 fw-bold shadow-sm">
        <i class="bi bi-check-circle me-1"></i> Guardar Secuencia
    </button>
</div>
@endsection
