@extends('layouts.app')
@section('title', 'Dueños de Plataforma')

@push('styles')
@include('partials.premium-ui')

<style>
/* ============================================================
   OWNER MODULE — Custom Premium Styles
   ============================================================ */

/* Gradient stat cards */
.owner-stat-card {
    position: relative;
    overflow: hidden;
    border-radius: var(--radius-2xl);
    padding: 1.5rem 1.75rem;
    color: #fff;
    transition: all .35s cubic-bezier(.4,0,.2,1);
    animation: uiSlideUp .5s ease both;
    animation-delay: var(--delay, 0s);
    box-shadow: 0 8px 32px rgba(0,0,0,.12);
}
.owner-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 48px rgba(0,0,0,.18);
}
.owner-stat-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 80% 20%, rgba(255,255,255,.18) 0%, transparent 55%),
        radial-gradient(circle at 20% 80%, rgba(255,255,255,.08) 0%, transparent 50%);
    pointer-events: none;
}
.owner-stat-card .stat-icon {
    width: 56px; height: 56px;
    border-radius: var(--radius-lg);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
    background: rgba(255,255,255,.18);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.25);
    flex-shrink: 0;
}
.owner-stat-card .stat-number {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: .15rem;
}
.owner-stat-card .stat-label {
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .6px;
    font-weight: 600;
    opacity: .8;
    margin: 0;
}
.owner-stat-card .stat-sub {
    font-size: .78rem;
    opacity: .65;
    margin-top: .35rem;
}

/* Purple gradient variant */
.owner-stat-purple {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);
}
/* Blue gradient variant */
.owner-stat-blue {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
}
/* Green gradient variant */
.owner-stat-green {
    background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
}

/* Owner avatar in table */
.owner-avatar-cell {
    width: 40px; height: 40px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700;
    font-size: .85rem;
    color: #fff;
    flex-shrink: 0;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    box-shadow: 0 2px 8px rgba(139,92,246,.3);
    transition: transform .2s ease;
}
.owner-avatar-cell:hover {
    transform: scale(1.1);
}

/* Row hover highlight */
.owner-table tbody tr {
    transition: background .2s ease;
}
.owner-table tbody tr:hover {
    background: rgba(139,92,246,.04) !important;
}

/* Separator divider */
.owner-separator {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(139,92,246,.2), transparent);
    margin: 1.25rem 0;
    border: none;
}

/* Dark mode overrides */
body.dark-mode .owner-stat-card {
    box-shadow: 0 8px 32px rgba(0,0,0,.3);
}
body.dark-mode .owner-table tbody tr:hover {
    background: rgba(139,92,246,.08) !important;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    {{-- HEADER --}}
    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Dueños de Plataforma</h2>
                    <p class="mb-0 opacity-75">Administra los propietarios del sistema multi-tenant.</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.owners.create') }}" class="ui-btn ui-btn-solid">
                    <i class="bi bi-person-plus me-2"></i>Nuevo Dueño
                </a>
            </div>
        </div>
    </div>

    {{-- STAT CARDS CON GRADIENTES --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="owner-stat-card owner-stat-purple" style="--delay:.1s">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <div>
                        <p class="stat-label">Total Dueños</p>
                        <div class="stat-number">{{ $totalOwners }}</div>
                        <div class="stat-sub">Propietarios registrados</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="owner-stat-card owner-stat-blue" style="--delay:.15s">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <p class="stat-label">Total Instancias</p>
                        <div class="stat-number">{{ $totalInstances }}</div>
                        <div class="stat-sub">Negocios creados</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="owner-stat-card owner-stat-green" style="--delay:.2s">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <p class="stat-label">Instancias Activas</p>
                        <div class="stat-number">{{ $activeInstances }}</div>
                        <div class="stat-sub">Operando normalmente</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLA DE DUEÑOS --}}
    <div class="ui-card" style="--delay:.25s">
        <div class="ui-card-accent" style="background:linear-gradient(90deg,#8b5cf6,#a78bfa)"></div>
        <div class="card-header bg-transparent border-0 px-4 pt-4 pb-0">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-people" style="color:var(--accent);font-size:1.1rem;"></i>
                <h5 class="fw-bold mb-0">Lista de Dueños</h5>
                <span class="ui-badge ui-badge-primary rounded-pill ms-1">{{ $owners->total() }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table owner-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>Dueño</th>
                        <th>Email</th>
                        <th class="text-center">Inst. Vinculadas</th>
                        <th class="text-center">Activas</th>
                        <th class="text-end pe-4" style="width:140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($owners as $index => $owner)
                    <tr>
                        <td class="ps-4 text-muted fw-semibold small">{{ $owners->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="owner-avatar-cell">
                                    {{ strtoupper(substr(explode(' ', $owner->name)[0], 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $owner->name }}</div>
                                    <small class="text-muted">ID: {{ $owner->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-envelope" style="color:#94a3b8;font-size:.85rem;"></i>
                                <span class="text-break">{{ $owner->email }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="ui-badge ui-badge-primary rounded-pill px-3 py-2">
                                <i class="bi bi-box-seam me-1"></i>{{ $owner->business_instances_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="ui-badge ui-badge-success rounded-pill px-3 py-2">
                                <i class="bi bi-check2 me-1"></i>{{ $owner->assigned_instances_count }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('owner.owners.edit', $owner) }}"
                                   class="ui-action ui-action-edit"
                                   title="Editar dueño">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('owner.owners.destroy', $owner) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('¿Eliminar a &ldquo;{{ $owner->name }}&rdquo; como dueño de plataforma?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ui-action ui-action-delete" title="Eliminar dueño">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="ui-empty-state py-5">
                                <i class="bi bi-shield-lock"></i>
                                <p>No hay dueños de plataforma registrados</p>
                                <a href="{{ route('owner.owners.create') }}" class="ui-btn ui-btn-solid btn-sm mt-2">
                                    <i class="bi bi-person-plus me-1"></i>Crear primer dueño
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($owners->hasPages())
        <div class="border-top px-4 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted">
                    Mostrando <strong>{{ $owners->firstItem() }}</strong>–<strong>{{ $owners->lastItem() }}</strong> de <strong>{{ $owners->total() }}</strong>
                </small>
                <nav>
                    {{ $owners->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
        @endif
    </div>

</div>
</div>
@endsection
