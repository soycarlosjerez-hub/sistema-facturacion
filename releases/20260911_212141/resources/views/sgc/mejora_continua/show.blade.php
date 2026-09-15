@extends('layouts.app')

@section('title', 'Mejora Continua')

@push('styles')
@include('partials.premium-ui')
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-propuesta { background: #f1f5f9; color: #64748b; }
    .badge-evaluando { background: #dbeafe; color: #2563eb; }
    .badge-aprobada { background: #e0f2fe; color: #0284c7; }
    .badge-en_curso { background: #fef3c7; color: #d97706; }
    .badge-completada { background: #dcfce7; color: #16a34a; }
    .badge-verificada { background: #d1fae5; color: #059669; }
    .badge-cerrada { background: #f1f5f9; color: #475569; }
    .badge-baja { background: #dbeafe; color: #2563eb; }
    .badge-media { background: #fef3c7; color: #d97706; }
    .badge-alta { background: #fed7aa; color: #ea580c; }
    .badge-urgente { background: #fee2e2; color: #dc2626; }
</style>
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
                    <i class="bi bi-arrow-up-circle"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $mejora->numero_label }}</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.mejora.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        {{ $mejora->titulo }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <span class="badge-status badge-{{ $mejora->fase }} fs-6">{{ $mejora->fase_label }}</span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3">Información de la Mejora</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="detail-label">Número</div>
                            <div class="detail-value"><code>{{ $mejora->numero_label }}</code></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fase</div>
                            <div class="detail-value"><span class="badge-status badge-{{ $mejora->fase }}">{{ $mejora->fase_label }}</span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Prioridad</div>
                            <div class="detail-value"><span class="badge-status badge-{{ $mejora->prioridad }}">{{ $mejora->prioridad_label }}</span></div>
                        </div>
                        <div class="col-12">
                            <div class="detail-label">Descripción</div>
                            <div class="detail-value">{{ $mejora->descripcion }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Origen</div>
                            <div class="detail-value">{{ ucfirst($mejora->origen ?? '-') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Impacto</div>
                            <div class="detail-value">{{ $mejora->impacto_label }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Responsable</div>
                            <div class="detail-value">{{ $mejora->responsable_label }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Propuesta</div>
                            <div class="detail-value">{{ $mejora->fecha_propuesta ? $mejora->fecha_propuesta->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Límite</div>
                            <div class="detail-value">{{ $mejora->fecha_limite ? $mejora->fecha_limite->format('d/m/Y') : 'Sin límite' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Completar</div>
                            <div class="detail-value">{{ $mejora->fecha_completar ? $mejora->fecha_completar->format('d/m/Y') : '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Beneficios --}}
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="--accent:#22c55e;--accent-hover:#16a34a;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-currency-dollar me-2"></i>Beneficios</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-label">Beneficios Esperados</div>
                            <div class="detail-value">{{ $mejora->beneficios_esperados ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Beneficios Logrados</div>
                            <div class="detail-value">{{ $mejora->beneficios_logrados ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Ahorro Estimado</div>
                            <div class="detail-value">{{ $mejora->ahorro_estimado_label }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Costo Estimado</div>
                            <div class="detail-value">{{ $mejora->costo_estimado_label }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Propuestas --}}
            <div class="ui-card" style="--delay:.3s">
                <div class="ui-card-accent" style="--accent:#8b5cf6;--accent-hover:#7c3aed;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb me-2"></i>Propuestas ({{ $mejora->propuestas->count() }})</h6>
                    @if($mejora->propuestas->count())
                        @foreach($mejora->propuestas as $prop)
                        <div class="border-start border-3 border-primary ps-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <strong class="small">{{ $prop->titulo }}</strong>
                                <span class="badge-status badge-{{ $prop->estado }}">{{ $prop->estado_label }}</span>
                            </div>
                            <div class="text-muted small">{{ $prop->autor_label }} — {{ $prop->fecha_label }}</div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted small mb-0">No hay propuestas registradas.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card mb-3" style="--delay:.25s">
                <div class="ui-card-accent" style="--accent:#6366f1;--accent-hover:#4f46e5;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-gear me-2"></i>Acciones</h6>
                    <div class="d-grid gap-2">
                        @if(in_array($mejora->fase, ['propuesta', 'evaluando']))
                        <form action="{{ route('sgc.mejora.completar', $mejora) }}" method="POST" class="d-grid">
                            @csrf
                            <button class="btn btn-sm btn-outline-success rounded-pill">
                                <i class="bi bi-check-lg me-1"></i> Completar
                            </button>
                        </form>
                        @endif
                        @if(in_array($mejora->fase, ['completada', 'verificada']))
                        <form action="{{ route('sgc.mejora.cerrar', $mejora) }}" method="POST" class="d-grid" onsubmit="return confirm('¿Cerrar esta mejora?')">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary rounded-pill">
                                <i class="bi bi-archive me-1"></i> Cerrar
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Auditoría</h6>
                    <div class="mb-2">
                        <div class="detail-label">Creado Por</div>
                        <div class="detail-value">{{ $mejora->creador ? $mejora->creador->name : '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Creado</div>
                        <div class="detail-value">{{ $mejora->created_at ? $mejora->created_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Modificado</div>
                        <div class="detail-value">{{ $mejora->updated_at ? $mejora->updated_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
