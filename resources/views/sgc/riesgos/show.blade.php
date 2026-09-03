@extends('layouts.app')

@section('title', 'Detalle del Riesgo')

@push('styles')
@include('partials.premium-ui')
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-identificado { background: #dbeafe; color: #2563eb; }
    .badge-en_tratamiento { background: #fef3c7; color: #d97706; }
    .badge-cerrado { background: #dcfce7; color: #16a34a; }
    .badge-bajo { background: #dcfce7; color: #16a34a; }
    .badge-medio { background: #dbeafe; color: #2563eb; }
    .badge-alto { background: #fef3c7; color: #d97706; }
    .badge-critico { background: #fee2e2; color: #dc2626; }
    .matrix-grid { display: grid; grid-template-columns: auto repeat(5,1fr); gap: 2px; max-width: 350px; }
    .matrix-cell { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: .5rem; font-size: .7rem; font-weight: 700; }
    .matrix-header { background: #f1f5f9; color: #64748b; font-size: .65rem; }
    .matrix-low { background: #dcfce7; color: #16a34a; }
    .matrix-med { background: #fef3c7; color: #d97706; }
    .matrix-high { background: #fed7aa; color: #ea580c; }
    .matrix-crit { background: #fee2e2; color: #dc2626; }
    .matrix-active { outline: 3px solid #1e293b; outline-offset: -1px; transform: scale(1.1); }
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
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $riesgo->codigo }}</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.riesgos.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Detalle del riesgo — {{ $riesgo->area }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <span class="badge-status badge-{{ $riesgo->clasificacion }} fs-6">{{ $riesgo->clasificacion_label }}</span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3">Información del Riesgo</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="detail-label">Código</div>
                            <div class="detail-value"><code>{{ $riesgo->codigo }}</code></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Área</div>
                            <div class="detail-value">{{ $riesgo->area }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value"><span class="badge-status badge-{{ $riesgo->estado }}">{{ $riesgo->estado_label }}</span></div>
                        </div>
                        <div class="col-12">
                            <div class="detail-label">Descripción</div>
                            <div class="detail-value">{{ $riesgo->descripcion }}</div>
                        </div>
                        @if($riesgo->causa)
                        <div class="col-md-6">
                            <div class="detail-label">Causa</div>
                            <div class="detail-value">{{ $riesgo->causa }}</div>
                        </div>
                        @endif
                        @if($riesgo->consecuencia)
                        <div class="col-md-6">
                            <div class="detail-label">Consecuencia</div>
                            <div class="detail-value">{{ $riesgo->consecuencia }}</div>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div class="detail-label">Probabilidad</div>
                            <div class="detail-value">{{ $riesgo->probabilidad }}/5</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Impacto</div>
                            <div class="detail-value">{{ $riesgo->impacto }}/5</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Nivel</div>
                            <div class="detail-value fw-bold">{{ $riesgo->nivel }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Responsable</div>
                            <div class="detail-value">{{ $riesgo->responsable ? $riesgo->responsable->name : 'Sin asignar' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Límite</div>
                            <div class="detail-value">{{ $riesgo->fecha_limite ? $riesgo->fecha_limite->format('d/m/Y') : 'Sin límite' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Controles y Plan --}}
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-gear me-2"></i>Tratamiento</h6>
                    <div class="row g-3">
                        @if($riesgo->controles_existentes)
                        <div class="col-md-6">
                            <div class="detail-label">Controles Existentes</div>
                            <div class="detail-value">{{ $riesgo->controles_existentes }}</div>
                        </div>
                        @endif
                        @if($riesgo->plan_accion)
                        <div class="col-md-6">
                            <div class="detail-label">Plan de Acción</div>
                            <div class="detail-value">{{ $riesgo->plan_accion }}</div>
                        </div>
                        @endif
                        @if($riesgo->plan_mitigacion)
                        <div class="col-12">
                            <div class="detail-label">Plan de Mitigación</div>
                            <div class="detail-value">{{ $riesgo->plan_mitigacion }}</div>
                        </div>
                        @endif
                        @if($riesgo->nivel_residual)
                        <div class="col-12">
                            <div class="detail-label">Riesgo Residual</div>
                            <div class="detail-value">{{ $riesgo->nivel_residual_label }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Matriz de Riesgo --}}
            <div class="ui-card mb-3" style="--delay:.25s">
                <div class="ui-card-accent" style="--accent:#ef4444;--accent-hover:#dc2626;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-grid-3x3 me-2"></i>Matriz de Riesgo</h6>
                    @php
                        $currentProb = $riesgo->probabilidad;
                        $currentImp = $riesgo->impacto;
                    @endphp
                    <div class="matrix-grid">
                        <div class="matrix-cell matrix-header"></div>
                        @for($imp = 1; $imp <= 5; $imp++)
                        <div class="matrix-cell matrix-header">{{ $imp }}</div>
                        @endfor

                        @for($prob = 5; $prob >= 1; $prob--)
                        <div class="matrix-cell matrix-header">{{ $prob }}</div>
                        @for($imp = 1; $imp <= 5; $imp++)
                            @php $nivel = $prob * $imp; @endphp
                            @php $cls = $nivel <= 4 ? 'matrix-low' : ($nivel <= 9 ? 'matrix-med' : ($nivel <= 15 ? 'matrix-high' : 'matrix-crit')); @endphp
                            @if($prob == $currentProb && $imp == $currentImp)
                                <div class="matrix-cell {{ $cls }} matrix-active">{{ $nivel }}</div>
                            @else
                                <div class="matrix-cell {{ $cls }}">{{ $nivel }}</div>
                            @endif
                        @endfor
                        @endfor
                    </div>
                    <div class="mt-2 small text-muted">
                        Prob.={{ $currentProb }} × Imp.={{ $currentImp }} = <strong>{{ $riesgo->nivel }}</strong>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="ui-card mb-3" style="--delay:.3s">
                <div class="ui-card-accent" style="--accent:#6366f1;--accent-hover:#4f46e5;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-gear me-2"></i>Acciones</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('sgc.riesgos.edit', $riesgo) }}" class="btn btn-sm btn-outline-warning rounded-pill">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                        @if($riesgo->estado !== 'cerrado')
                        <form action="{{ route('sgc.riesgos.eliminar', $riesgo) }}" method="POST" class="d-grid" onsubmit="return confirm('¿Eliminar este riesgo?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger rounded-pill">
                                <i class="bi bi-trash me-1"></i> Eliminar
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Audit --}}
            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent" style="--accent:#22c55e;--accent-hover:#16a34a;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Auditoría</h6>
                    <div class="mb-2">
                        <div class="detail-label">Creado Por</div>
                        <div class="detail-value">{{ $riesgo->creador ? $riesgo->creador->name : '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Creado</div>
                        <div class="detail-value">{{ $riesgo->created_at ? $riesgo->created_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Modificado</div>
                        <div class="detail-value">{{ $riesgo->updated_at ? $riesgo->updated_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
