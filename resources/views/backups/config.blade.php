@extends('layouts.app')

@section('title', 'Config. Backups')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-database"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Configuración de Backups</h4>
                    <small class="text-white opacity-75">Información del sistema de respaldo</small>
                </div>
            </div>
            <a href="{{ route('backups.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="premium-card h-100" style="animation-delay:.1s;">
                <div class="card-accent red"></div>
                <div class="premium-card-title">
                    <i class="bi bi-database icon-red"></i>
                    Información del Sistema
                </div>
                <div class="card-body pt-0">
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Directorio de backups</div>
                        <div class="premium-detail-value"><code class="small">{{ $backupDir }}</code></div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Ruta mysqldump</div>
                        <div class="premium-detail-value"><code class="small">{{ $mysqldumpPath }}</code></div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Base de datos</div>
                        <div class="premium-detail-value"><code>{{ config('database.connections.mysql.database') }}</code></div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Total backups</div>
                        <div class="premium-detail-value fw-semibold">{{ $backupCount }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Espacio total usado</div>
                        <div class="premium-detail-value fw-semibold">
                            @php
                                $bytes = $totalSize;
                                $units = ['B','KB','MB','GB'];
                                $i = 0;
                                while($bytes>=1024 && $i<3) { $bytes/=1024; $i++; }
                            @endphp
                            {{ round($bytes, 2) }} {{ $units[$i] }}
                        </div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Último backup</div>
                        <div class="premium-detail-value">
                            @if($lastBackup)
                                <span>{{ $lastBackup->created_at->format('d/m/Y h:i A') }}</span>
                                <br><small class="text-muted">{{ $lastBackup->filename }}</small>
                            @else
                                <span class="text-muted">Nunca</span>
                            @endif
                        </div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Retención automática</div>
                        <div class="premium-detail-value">30 días</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="premium-card mb-4" style="animation-delay:.15s;">
                <div class="card-accent red"></div>
                <div class="premium-card-title">
                    <i class="bi bi-clock icon-red"></i>
                    Backup Automático (Cron)
                </div>
                <div class="card-body pt-0">
                    <p class="text-muted small">Para activar backups automáticos diarios, agrega esta tarea al cron del servidor:</p>
                    <div class="bg-dark text-white rounded-3 p-3 mb-3">
                        <code class="small" style="color:#a5f3fc;">
* * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1
                        </code>
                    </div>
                    <p class="text-muted small mb-0">
                        Una vez configurado el cron, Laravel ejecutará <code>backup:run</code> automáticamente cada día a medianoche.
                        Los backups con más de 30 días se eliminarán automáticamente.
                    </p>
                </div>
            </div>

            <div class="premium-card" style="animation-delay:.2s;">
                <div class="card-accent red"></div>
                <div class="premium-card-title">
                    <i class="bi bi-question-circle icon-red"></i>
                    Comandos Artisan
                </div>
                <div class="card-body pt-0">
                    <div class="mb-3">
                        <label class="small fw-semibold text-muted d-block mb-1">Backup manual:</label>
                        <code class="small">php artisan backup:run --type=manual</code>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-semibold text-muted d-block mb-1">Backup automático:</label>
                        <code class="small">php artisan backup:run --type=automatico</code>
                    </div>
                    <div>
                        <label class="small fw-semibold text-muted d-block mb-1">Probar conexión mysqldump:</label>
                        <code class="small">"{{ $mysqldumpPath }}" --version</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection