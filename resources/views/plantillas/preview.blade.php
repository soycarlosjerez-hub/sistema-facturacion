@extends('layouts.app')
@section('title', 'Vista Previa - Plantilla ' . $template->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
    .preview-frame {
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        background: #fff;
        width: 100%;
        min-height: 90vh;
    }
    body.dark-mode .preview-frame {
        border-color: #334155;
    }
    .preview-frame iframe {
        width: 100%;
        min-height: 90vh;
        border: none;
    }
    .badge-plantilla {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-default { background: rgba(139,92,246,0.15); color: #7c3aed; }
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
                    <i class="bi bi-file-earmark-richtext"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Vista Previa</h4>
                    <div class="ui-header-meta">
                        @if($template)
                            <span class="badge-template badge-default me-2"><i class="bi bi-star-fill"></i>{{ $template->nombre }}</span>
                        @endif
                        Vista previa con datos de ejemplo
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('plantillas.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
                @if($template)
                <a href="{{ route('plantillas.pdf-preview', $template) }}" target="_blank" class="ui-btn ui-btn-solid btn-sm rounded-pill">
                    <i class="bi bi-download me-1"></i>Descargar PDF
                </a>
                @endif
            </div>
        </div>
    </div>

    <div class="ui-card">
        <div class="ui-card-accent"></div>
        <div class="card-body p-4">
            <div class="d-flex justify-content-center">
                <div style="width: 100%;">
                    <iframe src="{{ route('plantillas.pdf-preview', $template) }}" class="preview-frame" id="previewIframe"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
