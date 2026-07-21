@extends('layouts.app')

@section('title', 'Backups')

@push('styles')
@include('partials.premium-ui')
<style>
.btn-icon-hover {
    width: 32px; height: 32px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 50% !important;
    padding: 0;
    transition: all 0.2s;
}
.btn-icon-hover:hover { transform: scale(1.15); }
.status-badge {
    padding: 0.4em 0.8em;
    border-radius: 2rem;
    font-weight: 500;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}
</style>
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
                    <h4 class="fw-bold mb-1 text-white">Respaldo de Base de Datos</h4>
                    <small class="text-white opacity-75">Gestión de copias de seguridad</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('backups.config') }}" class="btn btn-light rounded-pill px-3 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-gear me-1"></i> Configuración
                </a>
                <form method="POST" action="{{ route('backups.store') }}" style="display:inline;">
                    @csrf
                    <button type="button" class="btn btn-light text-success rounded-pill px-4 fw-bold shadow-sm" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);" onclick="confirmAction({title:'Backup', text:'¿Iniciar backup ahora?', icon:'info', color:'#3b82f6', confirmText:'Iniciar Backup', onSubmit:function(){ this.closest('form').submit(); }})">
                        <i class="bi bi-shield-check me-1"></i> Crear Backup
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="premium-stat-card p-3" style="animation-delay:.1s;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-hdd-stack text-success fs-5"></i>
                    </div>
                    <div>
                        <div class="stat-label">Total Backups</div>
                        <div class="stat-value text-success">{{ $backups->total() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card p-3" style="animation-delay:.15s;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-hard-drive text-info fs-5"></i>
                    </div>
                    <div>
                        <div class="stat-label">Espacio Total</div>
                        <div class="stat-value text-info">
                            @php
                                $bytes = $totalSize;
                                $units = ['B','KB','MB','GB'];
                                $i = 0;
                                while($bytes>=1024 && $i<3) { $bytes/=1024; $i++; }
                            @endphp
                            {{ round($bytes, 2) }} {{ $units[$i] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card p-3" style="animation-delay:.2s;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-person text-warning fs-5"></i>
                    </div>
                    <div>
                        <div class="stat-label">Manuales</div>
                        <div class="stat-value text-warning">{{ $countManual }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card p-3" style="animation-delay:.25s;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-clock-history text-secondary fs-5"></i>
                    </div>
                    <div>
                        <div class="stat-label">Automáticos</div>
                        <div class="stat-value">{{ $countAuto }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card overflow-hidden" style="animation-delay:.3s;">
        <div class="card-accent red"></div>
        <div class="premium-card-title">
            <i class="bi bi-list-check icon-red"></i>
            Historial de Backups
        </div>
        <div class="premium-card-subtitle">Los backups se almacenan en storage/app/backups/</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: rgba(15,23,42,0.03);">
                    <tr style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3 text-muted fw-bold">Archivo</th>
                        <th class="py-3 text-muted fw-bold">Tipo</th>
                        <th class="py-3 text-muted fw-bold">Estado</th>
                        <th class="py-3 text-muted fw-bold">Tamaño</th>
                        <th class="py-3 text-muted fw-bold">Creado por</th>
                        <th class="py-3 text-muted fw-bold">Fecha</th>
                        <th class="text-end pe-4 py-3 text-muted fw-bold">Acciones</th>
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
                                    <span class="premium-badge"><i class="bi bi-clock-history me-1"></i>Automático</span>
                                @else
                                    <span class="premium-badge active"><i class="bi bi-person me-1"></i>Manual</span>
                                @endif
                            </td>
                            <td>
                                @if($backup->status === 'completado')
                                    <span class="premium-badge active">Completado</span>
                                @else
                                    <span class="premium-badge">Fallido</span>
                                @endif
                            </td>
                            <td><span class="fw-semibold">{{ $backup->sizeForHumans() }}</span></td>
                            <td><small class="text-muted">{{ $backup->user?->name ?? 'Sistema' }}</small></td>
                            <td><small>{{ $backup->created_at->format('d/m/Y h:i A') }}</small></td>
                            <td class="text-end pe-4">
                                @if($backup->status === 'completado')
                                <a href="{{ route('backups.download', $backup) }}" class="btn btn-sm btn-outline-success rounded-pill btn-icon-hover" title="Descargar">
                                    <i class="bi bi-download"></i>
                                </a>
                                @endif
                                <button type="button" class="premium-btn-delete"
                                        onclick="confirmDelete('{{ route('backups.destroy', $backup) }}', '{{ $backup->filename }}')"
                                        title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2 mb-0">No hay backups aún</p>
                                <form method="POST" action="{{ route('backups.store') }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success rounded-pill mt-2">
                                        <i class="bi bi-shield-check me-1"></i> Crear primer backup
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-0 py-3 px-4" style="background:transparent;">
            {{ $backups->links() }}
        </div>
    </div>

    <div class="premium-card mt-4" style="animation-delay:.35s;">
        <div class="card-accent red"></div>
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" style="width:52px;height:52px;">
                    <i class="bi bi-info-circle text-info fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Acerca de los Backups</h6>
                    <p class="text-muted small mb-0">
                        Los backups se realizan con <code>mysqldump</code> e incluyen: estructura de tablas, datos, procedimientos almacenados y eventos.
                        Se conservan automáticamente los últimos 30 días. Los archivos se almacenan en <code>storage/app/backups/</code>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(url, filename) {
    Swal.fire({
        title: '¿Eliminar backup?',
        text: `Se eliminará: "${filename}"`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = '@csrf @method("DELETE")';
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush