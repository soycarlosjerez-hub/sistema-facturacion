@extends('layouts.app')

@section('title', 'Config. Backups')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-database"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Configuración de Backups</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-gear me-1"></i>
                        <span>Información del sistema de respaldo</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('backups.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-database me-2" style="color:#f59e0b;"></i> Información del Sistema</h5>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Directorio de backups</small>
                            <span><code class="small">{{ $backupDir }}</code></span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Ruta mysqldump</small>
                            <span><code class="small">{{ $mysqldumpPath }}</code></span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Base de datos</small>
                            <span><code>{{ config('database.connections.mysql.database') }}</code></span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Total backups</small>
                            <span class="fw-semibold">{{ $backupCount }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Espacio total usado</small>
                            <span class="fw-semibold">
                                @php
                                    $bytes = $totalSize;
                                    $units = ['B','KB','MB','GB'];
                                    $i = 0;
                                    while($bytes>=1024 && $i<3) { $bytes/=1024; $i++; }
                                @endphp
                                {{ round($bytes, 2) }} {{ $units[$i] }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Último backup</small>
                            <span>
                                @if($lastBackup)
                                    {{ $lastBackup->created_at->format('d/m/Y h:i A') }}
                                    <br><small class="text-muted">{{ $lastBackup->filename }}</small>
                                @else
                                    <span class="text-muted">Nunca</span>
                                @endif
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Retención automática</small>
                            <span>30 días</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="ui-card mb-4" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-clock me-2" style="color:#f59e0b;"></i> Backup Automático (Cron)</h5>
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

            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-question-circle me-2" style="color:#f59e0b;"></i> Comandos Artisan</h5>
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