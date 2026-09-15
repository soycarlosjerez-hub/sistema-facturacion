@extends('layouts.app')

@section('title', 'Backups')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-database-fill-gear"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Respaldo de Base de Datos</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-shield-lock me-1"></i>
                        <span>Gestión de copias de seguridad</span>
                        <span class="divider">·</span>
                        <i class="bi bi-hdd-stack me-1"></i>
                        <span>{{ $backups->total() }} respaldo(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('backups.config') }}" class="ui-btn ui-btn-primary ui-btn-sm ui-btn-pill">
                    <i class="bi bi-gear me-1"></i> Configuración
                </a>
                <form method="POST" action="{{ route('backups.store') }}" style="display:inline;" id="backupForm">
                    @csrf
                    <button type="button" class="ui-btn ui-btn-solid ui-btn-pill" onclick="UI.confirm.action({title:'Crear Backup', text:'¿Deseas iniciar un nuevo backup de la base de datos?', icon:'question', color:'#06b6d4', confirmText:'Iniciar Backup', onSubmit:function(){ document.getElementById('backupForm').submit(); }})">
                        <i class="bi bi-shield-check me-1"></i> Crear Backup
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="ui-stat" style="--delay:.05s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Total Backups</div>
                    <div class="ui-stat-value">{{ $backups->total() }}</div>
                    <div class="ui-stat-sub">respaldos almacenados</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Espacio Total</div>
                    <div class="ui-stat-value" style="font-size:1.25rem;">
                        @php
                            $bytes = $totalSize;
                            $units = ['B','KB','MB','GB'];
                            $i = 0;
                            while($bytes>=1024 && $i<3) { $bytes/=1024; $i++; }
                        @endphp
                        {{ round($bytes, 2) }} {{ $units[$i] }}
                    </div>
                    <div class="ui-stat-sub">almacenamiento usado</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Manuales</div>
                    <div class="ui-stat-value text-warning" style="color:#f59e0b !important;">{{ $countManual }}</div>
                    <div class="ui-stat-sub">creados manualmente</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Automáticos</div>
                    <div class="ui-stat-value" style="color:#8b5cf6 !important;">{{ $countAuto }}</div>
                    <div class="ui-stat-sub">programados</div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.25s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title">
            <i class="bi bi-list-check"></i> Historial de Backups
        </div>
        <div class="ui-card-subtitle">Los backups se almacenan en <code>storage/app/backups/</code></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="table ui-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Archivo</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Tamaño</th>
                            <th>Creado por</th>
                            <th>Fecha</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-semibold small font-monospace">{{ $backup->filename }}</span>
                                    @if($backup->notes)
                                        <br><small class="text-muted">{{ $backup->notes }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($backup->type === 'automatico')
                                        <span class="ui-badge ui-badge-info"><i class="bi bi-clock-history me-1"></i>Automático</span>
                                    @else
                                        <span class="ui-badge ui-badge-primary"><i class="bi bi-person me-1"></i>Manual</span>
                                    @endif
                                </td>
                                <td>
                                    @if($backup->status === 'completado')
                                        <span class="ui-badge ui-badge-success"><i class="bi bi-check-circle me-1"></i>Completado</span>
                                    @else
                                        <span class="ui-badge ui-badge-danger"><i class="bi bi-x-circle me-1"></i>Fallido</span>
                                    @endif
                                </td>
                                <td><span class="fw-semibold">{{ $backup->sizeForHumans() }}</span></td>
                                <td><small class="text-muted">{{ $backup->user?->name ?? 'Sistema' }}</small></td>
                                <td><small>{{ $backup->created_at->format('d/m/Y h:i A') }}</small></td>
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-1 justify-content-end">
                                        @if($backup->status === 'completado')
                                            <a href="{{ route('backups.download', $backup) }}" class="ui-action ui-action-print" title="Descargar">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                        <button type="button" class="ui-action ui-action-delete"
                                                onclick="UI.confirm.delete('{{ route('backups.destroy', $backup) }}', '{{ $backup->filename }}')"
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="ui-empty-state py-5">
                                        <i class="bi bi-inbox"></i>
                                        <p>No hay backups aún</p>
                                        <form method="POST" action="{{ route('backups.store') }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="ui-btn ui-btn-solid ui-btn-pill mt-2">
                                                <i class="bi bi-shield-check me-1"></i> Crear primer backup
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="ui-card mt-3" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-4">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;background:rgba(6,182,212,.1);">
                    <i class="bi bi-info-circle text-primary fs-5" style="color:#06b6d4;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1" style="color:#06b6d4;">Acerca de los Backups</h6>
                    <p class="text-muted small mb-0">
                        Los backups se realizan con <code class="text-dark bg-light px-2 py-1 rounded">mysqldump</code> e incluyen: estructura de tablas, datos, procedimientos almacenados y eventos.
                        Se conservan automáticamente los últimos <strong>30 días</strong>. Los archivos se almacenan en <code class="text-dark bg-light px-2 py-1 rounded">storage/app/backups/</code>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
UI.confirm = {
    delete: function(url, label) {
        UI.confirm.action({
            title: '¿Eliminar backup?',
            text: `Se eliminará: "${label}"`,
            icon: 'warning',
            color: '#ef4444',
            confirmText: 'Sí, eliminar',
            onSubmit: function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    },
    action: function(opts) {
        Swal.fire({
            title: opts.title,
            text: opts.text || '',
            icon: opts.icon || 'info',
            showCancelButton: true,
            confirmButtonColor: opts.color || '#06b6d4',
            cancelButtonColor: '#64748b',
            confirmButtonText: opts.confirmText || 'Confirmar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed && opts.onSubmit) {
                opts.onSubmit();
            }
        });
    }
};
</script>
@endpush
