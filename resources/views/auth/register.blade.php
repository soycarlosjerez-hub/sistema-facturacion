<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erpipos — Crear tu Empresa</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect rx='20' width='100' height='100' fill='%233b82f6'/><text x='50' y='68' font-size='55' font-weight='bold' text-anchor='middle' fill='white'>EP</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --primary-light: #60a5fa;
            --accent: #8b5cf6;
            --surface: rgba(255,255,255,0.08);
            --glass-border: rgba(255,255,255,0.12);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 40%, #0f172a 100%);
            overflow-x: hidden;
            position: relative;
            padding: 40px 0;
        }

        /* ── Animated Background ── */
        .bg-orbs { position: fixed; inset: 0; z-index: 0; pointer-events: none; }
        .bg-orbs .orb {
            position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.4;
            animation: floatOrb 20s ease-in-out infinite alternate;
        }
        .bg-orbs .orb:nth-child(1) { width: 500px; height: 500px; background: radial-gradient(circle, #3b82f6, transparent 70%); top: -10%; left: -5%; animation-duration: 22s; }
        .bg-orbs .orb:nth-child(2) { width: 400px; height: 400px; background: radial-gradient(circle, #8b5cf6, transparent 70%); bottom: -10%; right: -5%; animation-duration: 18s; animation-delay: -5s; }
        .bg-orbs .orb:nth-child(3) { width: 300px; height: 300px; background: radial-gradient(circle, #06b6d4, transparent 70%); top: 50%; left: 50%; transform: translate(-50%, -50%); animation-duration: 25s; animation-delay: -10s; }

        @keyframes floatOrb {
            0%   { transform: translate(0, 0) scale(1); }
            33%  { transform: translate(30px, -40px) scale(1.05); }
            66%  { transform: translate(-20px, 20px) scale(0.95); }
            100% { transform: translate(10px, -10px) scale(1.02); }
        }

        .grid-pattern {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* ── Container ── */
        .register-wrapper { position: relative; z-index: 10; width: 100%; max-width: 780px; padding: 20px; }

        .register-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 44px 40px;
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.05) inset,
                0 25px 60px -12px rgba(0,0,0,0.5),
                0 0 120px -40px rgba(59,130,246,0.15);
            animation: cardAppear 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes cardAppear { to { opacity: 1; transform: translateY(0); } }

        /* ── Brand ── */
        .brand-section { text-align: center; margin-bottom: 28px; }
        .brand-logo {
            width: 64px; height: 64px; margin: 0 auto 14px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 32px rgba(59,130,246,0.3);
            animation: logoPulse 3s ease-in-out infinite;
            position: relative;
        }
        @keyframes logoPulse {
            0%, 100% { box-shadow: 0 8px 32px rgba(59,130,246,0.3); }
            50% { box-shadow: 0 8px 48px rgba(59,130,246,0.5); }
        }
        .brand-name {
            font-size: 1.6rem; font-weight: 800; letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff, #cbd5e1);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .brand-subtitle { color: rgba(148,163,184,0.8); font-size: 0.85rem; margin-top: 4px; }

        /* ── Section headers ── */
        .section-title {
            display: flex; align-items: center; gap: 12px; margin: 28px 0 18px;
        }
        .section-title .step-num {
            width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: #fff; font-weight: 800; font-size: 0.85rem;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 14px rgba(59,130,246,0.35);
        }
        .section-title h2 { color: #f1f5f9; font-size: 1rem; font-weight: 700; margin: 0; letter-spacing: 0.2px; }
        .section-title p { color: rgba(148,163,184,0.7); font-size: 0.78rem; margin: 2px 0 0; }
        .section-divider { height: 1px; background: rgba(255,255,255,0.06); margin: 6px 0 0; }

        /* ── Form elements ── */
        .form-floating-custom { position: relative; margin-bottom: 18px; }
        .form-floating-custom .input-icon {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
            color: rgba(148,163,184,0.6); font-size: 1.05rem; transition: color 0.3s; z-index: 2;
        }
        .form-floating-custom input, .form-floating-custom select {
            width: 100%; padding: 13px 16px 13px 48px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px; color: #f1f5f9; font-size: 0.93rem;
            transition: all 0.3s ease; outline: none; appearance: none; -webkit-appearance: none;
        }
        .form-floating-custom select {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 16px center;
        }
        .form-floating-custom select option { background: #1e293b; color: #f1f5f9; }
        .form-floating-custom input::placeholder { color: rgba(148,163,184,0.5); }
        .form-floating-custom input:focus, .form-floating-custom select:focus {
            border-color: var(--primary); background: rgba(59,130,246,0.06);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }
        .form-floating-custom input:focus ~ .input-icon, .form-floating-custom select:focus ~ .input-icon { color: var(--primary-light); }
        .form-floating-custom input.is-invalid, .form-floating-custom select.is-invalid {
            border-color: #ef4444; background: rgba(239,68,68,0.06);
        }
        .form-floating-custom .toggle-password {
            position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: rgba(148,163,184,0.5);
            cursor: pointer; padding: 4px; font-size: 1.05rem; transition: color 0.3s; z-index: 2;
        }
        .form-floating-custom .toggle-password:hover { color: rgba(148,163,184,0.9); }
        .invalid-feedback { color: #f87171; font-size: 0.8rem; margin-top: 6px; padding-left: 4px; }
        .field-label { display: block; color: rgba(203,213,225,0.9); font-size: 0.8rem; font-weight: 600; margin-bottom: 8px; }

        /* ── Plan cards ── */
        .plan-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(215px, 1fr)); gap: 14px; }
        .plan-card {
            position: relative; background: rgba(255,255,255,0.04);
            border: 1.5px solid rgba(255,255,255,0.08); border-radius: 16px;
            padding: 18px 16px; cursor: pointer; transition: all 0.25s ease; height: 100%;
            display: flex; flex-direction: column;
        }
        .plan-card:hover { border-color: rgba(96,165,250,0.4); transform: translateY(-2px); }
        .plan-card.selected {
            border-color: var(--primary);
            background: rgba(59,130,246,0.1);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.18), 0 10px 30px -10px rgba(59,130,246,0.35);
        }
        .plan-card input[type="radio"] { position: absolute; opacity: 0; pointer-events: none; }
        .plan-badge {
            position: absolute; top: -10px; right: 12px;
            background: linear-gradient(135deg, #f59e0b, #f97316); color: #fff;
            font-size: 0.62rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;
            padding: 4px 10px; border-radius: 999px; box-shadow: 0 4px 12px rgba(245,158,11,0.35);
        }
        .plan-name { color: #f1f5f9; font-weight: 800; font-size: 1rem; display: flex; align-items: center; gap: 8px; }
        .plan-price { margin: 10px 0 2px; }
        .plan-price .amount { color: #fff; font-size: 1.5rem; font-weight: 800; letter-spacing: -0.5px; }
        .plan-price .per { color: rgba(148,163,184,0.7); font-size: 0.78rem; }
        .plan-launch { color: rgba(148,163,184,0.65); font-size: 0.75rem; margin-bottom: 10px; }
        .plan-desc { color: rgba(148,163,184,0.75); font-size: 0.78rem; line-height: 1.5; margin-bottom: 12px; flex: 1; }
        .plan-features { list-style: none; padding: 0; margin: 0 0 12px; }
        .plan-features li { color: rgba(203,213,225,0.8); font-size: 0.75rem; padding: 3px 0; display: flex; align-items: center; gap: 7px; }
        .plan-features li i { color: #34d399; font-size: 0.7rem; flex-shrink: 0; }
        .plan-check {
            display: flex; align-items: center; gap: 8px; margin-top: auto;
            color: rgba(148,163,184,0.8); font-size: 0.78rem;
        }
        .plan-check .radio-dot {
            width: 18px; height: 18px; border-radius: 50%;
            border: 2px solid rgba(148,163,184,0.5); display: inline-flex;
            align-items: center; justify-content: center; transition: all 0.25s ease; flex-shrink: 0;
        }
        .plan-card.selected .radio-dot { border-color: var(--primary); }
        .plan-card.selected .radio-dot::after {
            content: ''; width: 8px; height: 8px; border-radius: 50%; background: var(--primary);
        }
        .plan-card.selected .plan-check { color: #c7d2fe; }

        /* ── Submit ── */
        .btn-register {
            width: 100%; padding: 14px; margin-top: 8px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none; border-radius: 14px; color: #fff; font-size: 1rem; font-weight: 700;
            letter-spacing: 0.3px; cursor: pointer; transition: all 0.3s ease; position: relative; overflow: hidden;
        }
        .btn-register::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity: 0; transition: opacity 0.3s;
        }
        .btn-register:hover { transform: translateY(-1px); box-shadow: 0 8px 32px rgba(59,130,246,0.4); }
        .btn-register:hover::before { opacity: 1; }
        .btn-register:active { transform: translateY(0); }
        .btn-register .spinner-border { display: none; width: 20px; height: 20px; border-width: 2px; }
        .btn-register.loading .btn-text { display: none; }
        .btn-register.loading .spinner-border { display: inline-block; }
        .btn-register.loading { pointer-events: none; opacity: 0.8; }

        /* ── Footer ── */
        .register-footer { text-align: center; margin-top: 22px; }
        .register-footer a { color: var(--primary-light); font-size: 0.85rem; text-decoration: none; transition: color 0.3s; }
        .register-footer a:hover { color: #fff; }
        .register-footer p { color: rgba(148,163,184,0.4); font-size: 0.75rem; margin-top: 10px; }

        /* ── Toast ── */
        .toast-container { position: fixed; top: 24px; right: 24px; z-index: 9999; }
        .custom-toast {
            background: rgba(15,23,42,0.9); backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.1); border-radius: 12px;
            padding: 14px 20px; color: #f1f5f9; font-size: 0.9rem;
            box-shadow: 0 12px 40px rgba(0,0,0,0.4); display: none;
            animation: slideInRight 0.3s ease forwards; max-width: 360px;
        }
        .custom-toast.error { border-left: 3px solid #ef4444; }
        .custom-toast.success { border-left: 3px solid #22c55e; }
        @keyframes slideInRight { from { opacity: 0; transform: translateX(40px); } to { opacity: 1; transform: translateX(0); } }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .register-card { padding: 32px 22px; border-radius: 20px; }
            .plan-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="bg-orbs">
        <div class="orb"></div>
        <div class="orb"></div>
        <div class="orb"></div>
    </div>
    <div class="grid-pattern"></div>

    <div class="toast-container">
        <div class="custom-toast" id="toast"></div>
    </div>

    <div class="register-wrapper">
        <div class="register-card">
            <div class="brand-section">
                <div class="brand-logo">
                    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <text x="18" y="25" font-size="18" font-weight="800" text-anchor="middle" fill="white" font-family="system-ui">EP</text>
                    </svg>
                </div>
                <h1 class="brand-name">Crea tu Empresa</h1>
                <p class="brand-subtitle">Registra tu negocio y comienza a operar en minutos</p>
            </div>

            @if (session('error'))
                <div class="mb-4 p-3 rounded-3" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:#f87171;">
                    <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
                </div>
            @endif

            <form id="registerForm" method="POST" action="{{ route('register') }}" autocomplete="off">
                @csrf

                {{-- ====== 1. CUENTA ADMINISTRADOR ====== --}}
                <div class="section-title">
                    <div class="step-num">1</div>
                    <div>
                        <h2>Cuenta de Administrador</h2>
                        <p>Tus datos de acceso a la plataforma</p>
                    </div>
                </div>
                <div class="row g-0">
                    <div class="col-md-6 pe-md-2">
                        <div class="form-floating-custom">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" id="name" name="name" class="@error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus placeholder="Nombre completo">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 ps-md-2">
                        <div class="form-floating-custom">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" id="email" name="email" class="@error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="Correo electrónico" autocomplete="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 pe-md-2">
                        <div class="form-floating-custom">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" id="password" name="password" class="@error('password') is-invalid @enderror" required placeholder="Contraseña (mín. 8 caracteres)">
                            <button type="button" class="toggle-password" onclick="togglePassword('password', 'eyeIcon')" aria-label="Mostrar contraseña">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 ps-md-2">
                        <div class="form-floating-custom">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Confirmar contraseña">
                            <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation', 'eyeIcon2')" aria-label="Mostrar contraseña">
                                <i class="bi bi-eye" id="eyeIcon2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ====== 2. TU NEGOCIO ====== --}}
                <div class="section-title">
                    <div class="step-num">2</div>
                    <div>
                        <h2>Tu Negocio</h2>
                        <p>Los datos de la empresa que vas a gestionar</p>
                    </div>
                </div>
                <div class="row g-0">
                    <div class="col-md-6 pe-md-2">
                        <div class="form-floating-custom">
                            <i class="bi bi-building input-icon"></i>
                            <input type="text" id="negocio_nombre" name="negocio_nombre" class="@error('negocio_nombre') is-invalid @enderror" value="{{ old('negocio_nombre') }}" required placeholder="Nombre del negocio">
                            @error('negocio_nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 ps-md-2">
                        <div class="form-floating-custom">
                            <i class="bi bi-grid input-icon"></i>
                            <select id="business_type_id" name="business_type_id" class="@error('business_type_id') is-invalid @enderror" required>
                                <option value="" disabled {{ old('business_type_id') ? '' : 'selected' }}>Selecciona el tipo de negocio</option>
                                @foreach ($businessTypes as $type)
                                    <option value="{{ $type->id }}" @selected(old('business_type_id') == $type->id)>{{ $type->nombre }}</option>
                                @endforeach
                            </select>
                            @error('business_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 pe-md-2">
                        <div class="form-floating-custom">
                            <i class="bi bi-person-vcard input-icon"></i>
                            <input type="text" id="rnc" name="rnc" class="@error('rnc') is-invalid @enderror" value="{{ old('rnc') }}" required placeholder="RNC">
                            @error('rnc')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 ps-md-2">
                        <div class="form-floating-custom">
                            <i class="bi bi-telephone input-icon"></i>
                            <input type="text" id="telefono" name="telefono" class="@error('telefono') is-invalid @enderror" value="{{ old('telefono') }}" required placeholder="Teléfono">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating-custom">
                            <i class="bi bi-geo-alt input-icon"></i>
                            <input type="text" id="direccion" name="direccion" class="@error('direccion') is-invalid @enderror" value="{{ old('direccion') }}" required placeholder="Dirección">
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ====== 3. ELIGE TU PLAN ====== --}}
                <div class="section-title">
                    <div class="step-num">3</div>
                    <div>
                        <h2>Elige tu Plan</h2>
                        <p>Precios mensuales en RD$ — primer mes al precio de lanzamiento</p>
                    </div>
                </div>
                <div class="plan-grid">
                    @php
                        $selectedPlan = old('plan_id') ?? $plans->firstWhere('recomendado', true)?->id ?? $plans->first()?->id;
                    @endphp
                    @foreach ($plans as $plan)
                        <label class="plan-card @if ((int) $selectedPlan === (int) $plan->id) selected @endif" for="plan_{{ $plan->id }}">
                            <input type="radio" name="plan_id" id="plan_{{ $plan->id }}" value="{{ $plan->id }}"
                                   @if ((int) $selectedPlan === (int) $plan->id) checked @endif required>
                            @if ($plan->recomendado)
                                <span class="plan-badge">Recomendado</span>
                            @endif
                            <div class="plan-name">{{ $plan->nombre }}</div>
                            <div class="plan-price">
                                <span class="amount">RD$ {{ number_format((float) $plan->precio_mensual, 2) }}</span>
                                <span class="per">/mes</span>
                            </div>
                            <div class="plan-launch">
                                Lanzamiento: RD$ {{ number_format((float) $plan->costoImplementacionEfectivo(), 2) }} (1er mes)
                            </div>
                            <div class="plan-desc">{{ $plan->descripcion }}</div>
                            <ul class="plan-features">
                                @forelse (collect($plan->features ?? [])->take(5) as $feature)
                                    <li><i class="bi bi-check-circle-fill"></i>{{ $feature }}</li>
                                @empty
                                    <li><i class="bi bi-check-circle-fill"></i>Sin límites de módulos</li>
                                @endforelse
                            </ul>
                            <div class="plan-check">
                                <span class="radio-dot"></span> Seleccionar este plan
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('plan_id')
                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                @enderror

                {{-- Submit --}}
                <button type="submit" class="btn-register" id="btnRegister">
                    <span class="btn-text">
                        <i class="bi bi-rocket-takeoff me-2"></i>Crear mi Empresa
                    </span>
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                </button>
            </form>

            <div class="register-footer">
                <a href="{{ route('login') }}">¿Ya tienes cuenta? Inicia sesión</a>
                <p>&copy; {{ date('Y') }} Erpipos — Todos los derechos reservados</p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(id, iconId) {
            const pwd = document.getElementById(id);
            const icon = document.getElementById(iconId);
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                pwd.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        document.querySelectorAll('.plan-card input[type="radio"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.plan-card').forEach(function (card) {
                    card.classList.remove('selected');
                });
                this.closest('.plan-card').classList.add('selected');
            });
        });

        document.getElementById('registerForm').addEventListener('submit', function () {
            document.getElementById('btnRegister').classList.add('loading');
        });

        @if ($errors->any())
            const msgs = {!! json_encode($errors->first()) !!};
            const toast = document.getElementById('toast');
            toast.textContent = msgs;
            toast.className = 'custom-toast error';
            toast.style.display = 'block';
            setTimeout(() => { toast.style.display = 'none'; }, 4000);
        @endif
    </script>
</body>
</html>