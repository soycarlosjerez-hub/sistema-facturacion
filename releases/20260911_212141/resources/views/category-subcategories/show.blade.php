@extends('layouts.app')

@section('title', $categorySubcategory->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $categorySubcategory->nombre }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-tag me-1"></i>
                        Subcategoría de {{ $categorySubcategory->category?->nombre ?? 'Sin categoría' }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('category-subcategories.edit', $categorySubcategory) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" style="background:rgba(139,92,246,.2);border-color:rgba(139,92,246,.35);">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <a href="{{ route('category-subcategories.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-info-circle me-2" style="color:#8b5cf6;"></i> Información de la Subcategoría</h5>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="detail-label">Nombre</div>
                            <div class="detail-value fw-semibold">{{ $categorySubcategory->nombre }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Categoría Padre</div>
                            <div class="detail-value">{{ $categorySubcategory->category?->nombre ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Descripción</div>
                            <div class="detail-value">{{ $categorySubcategory->descripcion ?? 'Sin descripción' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value">
                                @if($categorySubcategory->activa ?? true)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Activa</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill"><i class="bi bi-x-circle-fill me-1"></i>Inactiva</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Fecha de Creación</div>
                            <div class="detail-value">{{ $categorySubcategory->created_at->format('d/m/Y h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:64px;height:64px;background:rgba(139,92,246,.1);">
                        <i class="bi bi-folder2-open fs-2" style="color:#8b5cf6;"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $categorySubcategory->nombre }}</h5>
                    <small class="text-muted">{{ $categorySubcategory->category?->nombre ?? 'Sin categoría padre' }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
