@extends('layouts.app')

@section('title', 'Confirmar Restauración de Backup')

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
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Confirmar Restauración</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-shield-exclamation me-1"></i>
                        Esta acción es irreversible
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.backups.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent" style="background:#ef4444"></div>
                <div class="ui-card-body p-4">
                    <div class="text-center mb-4">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:72px;height:72px;background:rgba(239,68,68,.1);">
                            <i class="bi bi-exclamation-triangle fs-1" style="color:#ef4444;"></i>
                        </div>
                        <h5 class="fw-bold">¿Restaurar este backup?</h5>
                        <p class="text-muted mb-0">Se reemplazará toda la información actual con los datos del backup.</p>
                    </div>

                    <div class="p-3 rounded-4 mb-4" style="background:rgba(239,68,68,.05);border:1px solid rgba(239,68,68,.15);">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-file-zip fs-3" style="color:#ef4444;"></i>
                            <div>
                                <div class="fw-bold">{{ $backup->nombre ?? $backup->filename ?? 'Backup' }}</div>
                                <small class="text-muted">{{ $backup->created_at ? $backup->created_at->format('d/m/Y h:i A') : 'Fecha desconocida' }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning border-0 rounded-3 small mb-4">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Importante:</strong> Se recomienda crear un backup actual antes de restaurar. Los datos no restaurados se perderán permanentemente.
                    </div>

                    <form method="POST" action="{{ route('owner.backups.restore', $backup) }}" id="restoreForm">
                        @csrf
                        <div class="mb-4">
                            <label class="ui-label fw-bold">Escriba <strong>RESTAURAR</strong> para confirmar</label>
                            <input type="text" name="confirmacion" class="ui-input" id="confirmInput" placeholder="RESTAURAR" required autocomplete="off">
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('owner.backups.index') }}" class="ui-btn ui-btn-ghost rounded-pill flex-fill">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="ui-btn ui-btn-danger rounded-pill flex-fill fw-bold" id="restoreBtn" disabled>
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Restaurar Backup
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('confirmInput');
    const btn = document.getElementById('restoreBtn');
    if (input && btn) {
        input.addEventListener('input', function() {
            btn.disabled = this.value !== 'RESTAURAR';
        });
    }
});
</script>
@endpush
@endsection
