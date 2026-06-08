@extends('layouts.app')

@section('title', 'Config. Backups')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-gear text-secondary me-2"></i>
                Configuración de Backups
            </h2>
            <p class="text-muted mb-0">Información del sistema de respaldo</p>
        </div>
        <a href="{{ route('backups.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Información del Sistema</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-5 text-muted small fw-semibold">Directorio de backups</div>
                        <div class="col-md-7"><code class="small">{{ $backupDir }}</code></div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-5 text-muted small fw-semibold">Ruta mysqldump</div>
                        <div class="col-md-7"><code class="small">{{ $mysqldumpPath }}</code></div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-5 text-muted small fw-semibold">Base de datos</div>
                        <div class="col-md-7"><code>{{ config('database.connections.mysql.database') }}</code></div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-5 text-muted small fw-semibold">Total backups</div>
                        <div class="col-md-7 fw-semibold">{{ $backupCount }}</div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-5 text-muted small fw-semibold">Espacio total usado</div>
                        <div class="col-md-7 fw-semibold">
                            @php
                                $bytes = $totalSize;
                                $units = ['B','KB','MB','GB'];
                                $i = 0;
                                while($bytes>=1024 && $i<3) { $bytes/=1024; $i++; }
                            @endphp
                            {{ round($bytes, 2) }} {{ $units[$i] }}
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-5 text-muted small fw-semibold">Último backup</div>
                        <div class="col-md-7">
                            @if($lastBackup)
                                <span>{{ $lastBackup->created_at->format('d/m/Y h:i A') }}</span>
                                <br><small class="text-muted">{{ $lastBackup->filename }}</small>
                            @else
                                <span class="text-muted">Nunca</span>
                            @endif
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-5 text-muted small fw-semibold">Retención automática</div>
                        <div class="col-md-7">30 días</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-clock me-2"></i>Backup Automático (Cron)</h5>
                </div>
                <div class="card-body p-4">
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

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-question-circle me-2"></i>Comandos Artisan</h5>
                </div>
                <div class="card-body p-4">
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
