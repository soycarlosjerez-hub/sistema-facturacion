@extends('layouts.app')
@section('title', 'Nueva Secuencia NCF')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#ef4444;--accent-rgb:239,68,68;--accent-hover:#dc2626;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nuevo NCF</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        <span>Registra una nueva secuencia de comprobante fiscal</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('ncf.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
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

    <div class="ui-card mb-5" style="--delay:.1s">
        <div class="ui-card-accent amber"></div>
        <form id="ncfForm" action="{{ route('ncf.store') }}" method="POST">
            @csrf
            <div class="ui-card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color: #f59e0b;">
                        <i class="bi bi-info-circle me-2"></i>Información del NCF
                    </h6>
                </div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="ui-label fw-bold">Nombre del Comprobante</label>
                        <input type="text" name="nombre" class="ui-input" placeholder="Ej: Crédito Fiscal" required>
                        <small class="text-muted">Descripción para identificar internamente.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">Prefijo (3 Letras)</label>
                        <input type="text" name="prefijo" class="ui-input" maxlength="3" placeholder="B01" required onkeyup="this.value = this.value.toUpperCase()">
                    </div>

                    <div class="col-md-4">
                        <label class="ui-label fw-bold">Desde (Número)</label>
                        <input type="number" name="desde" class="ui-input" value="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">Hasta (Límite)</label>
                        <input type="number" name="hasta" class="ui-input" placeholder="1000" required>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">Número Actual</label>
                        <input type="number" name="actual" class="ui-input" value="0" required>
                        <small class="text-muted">Último número emitido.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="ui-input" required>
                        <small class="text-muted">Fecha límite autorizada por DGII.</small>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:var(--accent);"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva secuencia NCF</span>
        </div>
        <a href="{{ route('ncf.index') }}" class="ui-btn ui-btn-ghost me-2">Cancelar</a>
        <button type="submit" form="ncfForm" class="ui-btn ui-btn-solid">
            <i class="bi bi-check-lg me-2"></i>Guardar Secuencia
        </button>
    </div>
</div>
@endsection
