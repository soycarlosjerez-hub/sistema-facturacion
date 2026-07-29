@if($onlineUsers->isEmpty())
    <div class="ui-card" style="--delay:.3s">
        <div class="card-body p-5 text-center">
            <div class="mb-3" style="font-size: 3rem; opacity: .3;">🌙</div>
            <h5 class="fw-bold text-muted">Ning&uacute;n usuario online en este momento</h5>
            <p class="text-muted small mb-0">Los usuarios aparecer&aacute;n aqu&iacute; cuando naveguen en el sistema.</p>
        </div>
    </div>
@else
    @foreach($byInstance as $instanceId => $users)
        @php $inst = $instancias[$instanceId] ?? null; @endphp
        <div class="ui-card mb-4" style="--delay:.{{ min(5, $loop->iteration + 3) }}s">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="online-dot"></div>
                        <div>
                            <h6 class="fw-bold mb-0">{{ $inst?->nombre ?? 'Instancia #'.$instanceId }}</h6>
                            <small class="text-muted">{{ $users->count() }} usuario(s) online
                                @if($inst) · {{ $totalByInstance[$instanceId] ?? 0 }} totales @endif
                            </small>
                        </div>
                    </div>
                    @if($inst)
                    <a href="{{ route('owner.instances.online', $inst) }}" class="ui-btn ui-btn-ghost btn-sm rounded-pill px-3">
                        <i class="bi bi-eye me-1"></i>Ver instancia
                    </a>
                    @endif
                </div>

                <div class="row g-2">
                    @foreach($users as $user)
                    <div class="col-md-6 col-xl-4">
                        <div class="user-card d-flex align-items-center gap-3">
                            <div class="avatar-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate">{{ $user->name }}</div>
                                <small class="text-muted text-truncate d-block">{{ $user->email }}</small>
                                @if($user->instanceRole)
                                    <span class="ui-badge ui-badge-primary rounded-pill px-2 py-0 small">
                                        {{ $user->instanceRole->nombre }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-end flex-shrink-0">
                                <div class="online-dot mb-1"></div>
                                <small class="text-muted d-block" style="font-size:.7rem;">
                                    {{ $user->last_seen_at?->diffForHumans(null, true) }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
@endif
