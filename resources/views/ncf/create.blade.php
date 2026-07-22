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
    <div class="premium-header mb-4" style="background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Nuevo NCF</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registra una nueva secuencia de comprobante fiscal
                    </small>
                </div>
            </div>
            <a href="{{ route('ncf.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
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

    <div style="height: 80px;"></div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#f59e0b;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva secuencia NCF</span>
        </div>
        <div>
            <a href="{{ route('ncf.index') }}" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="ncfForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Guardar Secuencia
            </button>
        </div>
    </div>
</div>
@endsection
