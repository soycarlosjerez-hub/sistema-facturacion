<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erpipo — Iniciar Sesión</title>
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
            overflow: hidden;
            position: relative;
        }

        /* ── Animated Background ── */
        .bg-orbs {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
        }
        .bg-orbs .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: floatOrb 20s ease-in-out infinite alternate;
        }
        .bg-orbs .orb:nth-child(1) {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #3b82f6, transparent 70%);
            top: -10%; left: -5%;
            animation-duration: 22s;
        }
        .bg-orbs .orb:nth-child(2) {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #8b5cf6, transparent 70%);
            bottom: -10%; right: -5%;
            animation-duration: 18s;
            animation-delay: -5s;
        }
        .bg-orbs .orb:nth-child(3) {
            width: 300px; height: 300px;
            background: radial-gradient(circle, #06b6d4, transparent 70%);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation-duration: 25s;
            animation-delay: -10s;
        }

        @keyframes floatOrb {
            0%   { transform: translate(0, 0) scale(1); }
            33%  { transform: translate(30px, -40px) scale(1.05); }
            66%  { transform: translate(-20px, 20px) scale(0.95); }
            100% { transform: translate(10px, -10px) scale(1.02); }
        }

        /* ── Grid Pattern Overlay ── */
        .grid-pattern {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* ── Login Container ── */
        .login-wrapper {
            position: relative; z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .login-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.05) inset,
                0 25px 60px -12px rgba(0,0,0,0.5),
                0 0 120px -40px rgba(59,130,246,0.15);
            animation: cardAppear 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes cardAppear {
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── Logo / Brand ── */
        .brand-section {
            text-align: center;
            margin-bottom: 36px;
        }

        .brand-logo {
            width: 72px; height: 72px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px rgba(59,130,246,0.3);
            animation: logoPulse 3s ease-in-out infinite;
            position: relative;
        }

        .brand-logo::before {
            content: '';
            position: absolute; inset: -3px;
            border-radius: 22px;
            background: linear-gradient(135deg, var(--primary), var(--accent), var(--primary));
            z-index: -1;
            opacity: 0.5;
            filter: blur(8px);
            animation: logoPulse 3s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0%, 100% { box-shadow: 0 8px 32px rgba(59,130,246,0.3); }
            50% { box-shadow: 0 8px 48px rgba(59,130,246,0.5); }
        }

        .brand-logo svg {
            width: 36px; height: 36px;
        }

        .brand-name {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-subtitle {
            color: rgba(148,163,184,0.8);
            font-size: 0.85rem;
            margin-top: 4px;
        }

        /* ── Form Elements ── */
        .form-floating-custom {
            position: relative;
            margin-bottom: 20px;
        }

        .form-floating-custom .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(148,163,184,0.6);
            font-size: 1.1rem;
            transition: color 0.3s;
            z-index: 2;
        }

        .form-floating-custom input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            color: #f1f5f9;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-floating-custom input::placeholder {
            color: rgba(148,163,184,0.5);
        }

        .form-floating-custom input:focus {
            border-color: var(--primary);
            background: rgba(59,130,246,0.06);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }

        .form-floating-custom input:focus ~ .input-icon {
            color: var(--primary-light);
        }

        .form-floating-custom input.is-invalid {
            border-color: #ef4444;
            background: rgba(239,68,68,0.06);
        }

        .form-floating-custom .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(148,163,184,0.5);
            cursor: pointer;
            padding: 4px;
            font-size: 1.1rem;
            transition: color 0.3s;
            z-index: 2;
        }

        .form-floating-custom .toggle-password:hover {
            color: rgba(148,163,184,0.9);
        }

        .invalid-feedback {
            color: #f87171;
            font-size: 0.8rem;
            margin-top: 6px;
            padding-left: 4px;
        }

        /* ── Options Row ── */
        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .form-check-custom {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-check-custom input[type="checkbox"] {
            width: 18px; height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
            border-radius: 4px;
        }

        .form-check-custom label {
            color: rgba(148,163,184,0.8);
            font-size: 0.85rem;
            cursor: pointer;
            user-select: none;
        }

        .forgot-link {
            color: var(--primary-light);
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.3s;
        }

        .forgot-link:hover {
            color: #fff;
        }

        /* ── Submit Button ── */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 14px;
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 32px rgba(59,130,246,0.4);
        }

        .btn-login:hover::before {
            opacity: 1;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login .spinner-border {
            display: none;
            width: 20px; height: 20px;
            border-width: 2px;
        }

        .btn-login.loading .btn-text { display: none; }
        .btn-login.loading .spinner-border { display: inline-block; }
        .btn-login.loading { pointer-events: none; opacity: 0.8; }

        /* ── Divider ── */
        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 24px 0;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.06);
        }

        .divider span {
            color: rgba(148,163,184,0.4);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ── Footer ── */
        .login-footer {
            text-align: center;
            margin-top: 24px;
        }

        .login-footer p {
            color: rgba(148,163,184,0.4);
            font-size: 0.75rem;
        }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .login-card {
                padding: 36px 24px;
                border-radius: 20px;
            }
            .brand-name { font-size: 1.5rem; }
            .brand-logo { width: 60px; height: 60px; }
        }

        /* ── Toast Notification ── */
        .toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
        }

        .custom-toast {
            background: rgba(15,23,42,0.9);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 14px 20px;
            color: #f1f5f9;
            font-size: 0.9rem;
            box-shadow: 0 12px 40px rgba(0,0,0,0.4);
            display: none;
            animation: slideInRight 0.3s ease forwards;
            max-width: 360px;
        }

        .custom-toast.error { border-left: 3px solid #ef4444; }
        .custom-toast.success { border-left: 3px solid #22c55e; }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(40px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>
    <!-- Background Effects -->
    <div class="bg-orbs">
        <div class="orb"></div>
        <div class="orb"></div>
        <div class="orb"></div>
    </div>
    <div class="grid-pattern"></div>

    <!-- Toast -->
    <div class="toast-container">
        <div class="custom-toast" id="toast"></div>
    </div>

    <!-- Login Card -->
    <div class="login-wrapper">
        <div class="login-card">
            <!-- Brand -->
            <div class="brand-section">
                <div class="brand-logo">
                    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <text x="18" y="25" font-size="18" font-weight="800" text-anchor="middle" fill="white" font-family="system-ui">EP</text>
                    </svg>
                </div>
                <h1 class="brand-name">Erpipo</h1>
                <p class="brand-subtitle">Sistema de Gestión Empresarial</p>
            </div>

            <!-- Flash Messages -->
            @if (session('status'))
                <div class="mb-4 p-3 rounded-3" style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);color:#4ade80;">
                    <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
                </div>
            @endif

            <!-- Form -->
            <form id="loginForm" method="POST" action="{{ route('login') }}" autocomplete="off">
                @csrf

                <!-- Email -->
                <div class="form-floating-custom">
                    <i class="bi bi-envelope input-icon"></i>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="@error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="Correo electrónico"
                        autocomplete="email"
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-floating-custom">
                    <i class="bi bi-lock input-icon"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="@error('password') is-invalid @enderror"
                        required
                        placeholder="Contraseña"
                        autocomplete="current-password"
                    >
                    <button type="button" class="toggle-password" onclick="togglePassword()" aria-label="Mostrar contraseña">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Options -->
                <div class="options-row">
                    <div class="form-check-custom">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Recordarme</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">¿Olvidaste tu contraseña?</a>
                    @endif
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login" id="btnLogin">
                    <span class="btn-text">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                    </span>
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                </button>
            </form>

            <!-- Footer -->
            <div class="login-footer">
                <p>&copy; {{ date('Y') }} Erpipo — Todos los derechos reservados</p>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                pwd.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        // Loading state on submit
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('btnLogin');
            btn.classList.add('loading');
        });

        // Show flash errors via toast
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
