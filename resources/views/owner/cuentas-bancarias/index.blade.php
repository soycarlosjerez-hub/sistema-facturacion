@extends('layouts.app')

@section('title', 'Cuentas Bancarias - Owner')

@push('styles')
@include('partials.premium-ui')
<style>
.cuentas-table { --bs-table-bg: transparent; --bs-table-hover-bg: rgba(5,150,105,.04); margin: 0; }
.cuentas-table thead th { background: rgba(241,245,249,.8); color: #64748b; font-size: .7rem; text-transform: uppercase; letter-spacing: .5px; font-weight: 700; padding: .85rem 1rem; border-bottom: 1px solid #e2e8f0; }
.cuentas-table tbody td { padding: .85rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: .9rem; }
.cuentas-table tbody tr:last-child td { border-bottom: none; }
.cuentas-table tbody tr { transition: background .15s; }
.cuentas-table tbody tr:hover { background: rgba(5,150,105,.03); }
.avatar-circle { width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.2rem; transition: transform 0.2s; }
tr:hover .avatar-circle { transform: scale(1.1); }
.status-badge { padding: 0.4em 0.8em; border-radius: 2rem; font-weight: 500; font-size: 0.75rem; letter-spacing: 0.5px; }
.instance-badge { font-size: 0.75rem; padding: 0.3em 0.7em; border-radius: 2rem; }
body.dark-mode .cuentas-table thead th { background: rgba(15,23,42,.5); color: #94a3b8; border-color: #1e293b; }
body.dark-mode .cuentas-table tbody td { border-bottom-color: #1e293b; color: #cbd5e1; }
</style>
@endpush

@section('content')

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h4 fw-bold mb-1">Cuentas Bancarias</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb breadcrumb-custom mb-0">
        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cuentas Bancarias</li>
      </ol>
    </nav>
  </div>
</div>
<!-- Stats Cards -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card premium-card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar-circle bg-primary-subtle text-primary me-3">
            <i class="bi bi-bank fs-4"></i>
          </div>
          <div>
            <p class="text-muted mb-1">Total Cuentas</p>
            <h3 class="mb-0 fw-bold">{{ $stats['total'] ?? 0 }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card premium-card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar-circle bg-success-subtle text-success me-3">
            <i class="bi bi-check-circle fs-4"></i>
          </div>
          <div>
            <p class="text-muted mb-1">Activas</p>
            <h3 class="mb-0 fw-bold">{{ $stats['activas'] ?? 0 }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card premium-card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar-circle bg-info-subtle text-info me-3">
            <i class="bi bi-building fs-4"></i>
          </div>
          <div>
            <p class="text-muted mb-1">Instancias con Cuentas</p>
            <h3 class="mb-0 fw-bold">{{ $stats['instancias_con_cuentas'] ?? 0 }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Filter Bar -->
<div class="card premium-card mb-4">
  <div class="card-body">
    <form id="filterForm" method="GET" action="{{ route('owner.cuentas-bancarias.index') }}">
      <div class="row g-3 align-items-center">
        <div class="col-md-4">
          <label for="search" class="form-label small text-muted">Buscar</label>
          <input type="text" class="form-control form-control-sm" id="search" name="search" placeholder="Nombre, banco, nro. cuenta..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
          <label for="instance_id" class="form-label small text-muted">Instancia</label>
          <select class="form-select form-select-sm" id="instance_id" name="instance_id">
            <option value="">Todas las instancias</option>
            @foreach($instances as $inst)
              <option value="{{ $inst->id }}" {{ old('instance_id', request('instance_id')) == $inst->id ? 'selected' : '' }}>{{ $inst->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label d-block pt-2">Incluir Inactivas</label>
          <div class="form-check form-switch mt-1">
            <input class="form-check-input" type="checkbox" id="inactive" name="inactive" {{ request('inactive') ? 'checked' : '' }}>
            <label class="form-check-label small text-muted" for="inactive">Mostrar inactivas</label>
          </div>
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
          <button type="submit" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-search me-1"></i>Filtrar
          </button>
          <a href="{{ route('owner.cuentas-bancarias.index') }}" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-x-lg"></i>
          </a>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- Accounts Table -->
<div class="card premium-card">
  <div class="card-header bg-transparent border-0 pb-0">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0 fw-semibold">Lista de Cuentas Bancarias</h5>
      <span class="badge bg-light text-muted">{{ $cuentas->total() }} registro(s)</span>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table cuentas-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Cuenta</th>
          <th>Banco</th>
          <th>Nro. Cuenta</th>
          <th>Moneda</th>
          <th>Saldo</th>
          <th>Instancia</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($cuentas as $cuenta)
          <tr>
            <td class="fw-medium text-muted">{{ $loop->iteration + ($cuentas->currentPage() - 1) * $cuentas->perPage() }}</td>
            <td>
              <div class="d-flex align-items-center">
                <div class="avatar-circle bg-primary-subtle text-primary me-2">
                  <i class="bi bi-bank2"></i>
                </div>
                <div>
                  <div class="fw-semibold">{{ $cuenta->nombre }}</div>
                  <small class="text-muted">{{ $cuenta->tipo_cuenta }}</small>
                </div>
              </div>
            </td>
            <td><strong>{{ $cuenta->banco }}</strong></td>
            <td><code>{{ $cuenta->numero_cuenta }}</code></td>
            <td><span class="badge bg-light text-dark">{{ $cuenta->moneda }}</span></td>
            <td class="fw-bold">{{ number_format($cuenta->saldo, 2) }}</td>
            <td>
              <span class="instance-badge bg-light text-dark">
                <i class="bi bi-building me-1"></i>
                {{ $cuenta->businessInstance->nombre ?? 'N/A' }}
              </span>
            </td>
            <td>
              @if($cuenta->activo)
                <span class="status-badge bg-success-subtle text-success">Activa</span>
              @else
                <span class="status-badge bg-danger-subtle text-danger">Inactiva</span>
              @endif
            </td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('cuentas-bancarias.show', $cuenta->id) }}" class="btn btn-sm btn-outline-primary" title="Ver"><i class="bi bi-eye"></i></a>
                <a href="{{ route('cuentas-bancarias.edit', $cuenta->id) }}" class="btn btn-sm btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                <form action="{{ route('cuentas-bancarias.destroy', $cuenta->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Esta seguro que desea eliminar esta cuenta?')">
                  <input type="hidden" name="_method" value="DELETE">
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center py-5">
              <div class="text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                <p class="mb-0">No se encontraron cuentas bancarias.</p>
                <small>Intenta ajustar los filtros de búsqueda.</small>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <!-- Pagination -->
  <div class="card-footer bg-transparent border-top-0 pt-3">
    {{ $cuentas->links() }}
  </div>
</div>

@endsection

@push('scripts')
<script>
  // Live Search
  document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const form = document.getElementById('filterForm');
    let debounceTimer;
    searchInput.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => { form.submit(); }, 500);
    });
  });
</script>
@endpush
