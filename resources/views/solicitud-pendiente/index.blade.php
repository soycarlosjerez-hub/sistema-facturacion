<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esperando Aprobación — Erpipos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
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
        .bg-orbs { position: fixed; inset: 0; z-index: 0; pointer-events: none; }
        .bg-orbs .orb {
            position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.4;
            animation: floatOrb 20s ease-in-out infinite alternate;
        }
        .bg-orbs .orb:nth-child(1) { width: 500px; height: 500px; background: radial-gradient(circle, #3b82f6, transparent 70%); top: -10%; left: -5%; animation-duration: 22s; }
        .bg-orbs .orb:nth-child(2) { width: 400px; height: 400px; background: radial-gradient(circle, #f59e0b, transparent 70%); bottom: -10%; right: -5%; animation-duration: 18s; animation-delay: -5s; }
        .bg-orbs .orb:nth-child(3) { width: 300px; height: 300px; background: radial-gradient(circle, #06b6d4, transparent 70%); top: 50%; left: 50%; transform: translate(-50%, -50%); animation-duration: 25s; animation-delay: -10s; }
        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -40px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
            100% { transform: translate(10px, -10px) scale(1.02); }
        }
        .waiting-wrapper { position: relative; z-index: 10; width: 100%; max-width: 600px; padding: 20px; }
        .waiting-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 25px 60px -12px rgba(0,0,0,0.5);
            animation: cardAppear 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
            text-align: center;
        }
        @keyframes cardAppear { to { opacity: 1; transform: translateY(0); } }
        .waiting-icon {
            width: 80px; height: 80px; margin: 0 auto 24px;
            background: linear-gradient(135deg, #f59e0b, #f97316);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 36px; color: #fff;
            box-shadow: 0 8px 32px rgba(245,158,11,0.35);
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 8px 32px rgba(245,158,11,0.35); transform: scale(1); }
            50% { box-shadow: 0 8px 48px rgba(245,158,11,0.5); transform: scale(1.05); }
        }
        .waiting-title { color: #f1f5f9; font-size: 1.5rem; font-weight: 800; margin-bottom: 8px; }
        .waiting-subtitle { color: rgba(148,163,184,0.7); font-size: 0.9rem; margin-bottom: 28px; }
        .waiting-details {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 28px;
        }
        .waiting-details .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .waiting-details .detail-row:last-child { border-bottom: none; }
        .waiting-details .detail-label { color: rgba(148,163,184,0.7); font-size: 0.85rem; }
        .waiting-details .detail-value { color: #f1f5f9; font-size: 0.85rem; font-weight: 600; }
        .waiting-message { color: rgba(148,163,184,0.6); font-size: 0.85rem; line-height: 1.6; margin-bottom: 28px; }
        .waiting-actions { display: flex; gap: 12px; justify-content: center; }
        .btn-wait {
            padding: 12px 28px; border-radius: 12px; font-weight: 700; font-size: 0.9rem;
            text-decoration: none; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-wait-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff; border: none;
        }
        .btn-wait-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 32px rgba(59,130,246,0.4); color: #fff; }
        .btn-wait-secondary {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            color: #cbd5e1;
        }
        .btn-wait-secondary:hover { background: rgba(255,255,255,0.1); color: #fff; }
        @media (max-width: 480px) {
            .waiting-card { padding: 32px 22px; }
            .waiting-actions { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="bg-orbs">
        <div class="orb"></div>
        <div class="orb"></div>
        <div class="orb"></div>
    </div>

    <div class="waiting-wrapper">
        @if(session('info'))
        <div class="mb-3" style="max-width:600px;margin:0 auto;">
            <div class="alert alert-warning border-0 rounded-3 shadow-sm d-flex align-items-center gap-2" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.3);color:#fbbf24;">
                <i class="bi bi-exclamation-triangle fs-5"></i>
                <span>{{ session('info') }}</span>
            </div>
        </div>
        @endif
        <div class="waiting-card">
            <div class="waiting-icon">
                <i class="bi bi-clock-history"></i>
            </div>
            <h1 class="waiting-title">Solicitud en Revisión</h1>
            <p class="waiting-subtitle">Tu solicitud está siendo evaluada por nuestro equipo</p>

            <div class="waiting-details">
                <div class="detail-row">
                    <span class="detail-label">Negocio</span>
                    <span class="detail-value">{{ $instance->nombre }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tipo de Negocio</span>
                    <span class="detail-value">{{ $instance->businessType?->nombre ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Registrado</span>
                    <span class="detail-value">{{ $instance->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Estado</span>
                    <span class="detail-value" style="color:#f59e0b;">
                        <i class="bi bi-hourglass-split"></i> Pendiente
                    </span>
                </div>
            </div>

            <p class="waiting-message">
                Nuestro equipo revisará tu solicitud y te notificará por correo electrónico cuando sea aprobada. El tiempo estimado de revisión es de 24 horas.
            </p>

            <div class="waiting-actions">
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-wait btn-wait-secondary">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </button>
                </form>
                <a href="{{ route('login') }}" class="btn-wait btn-wait-primary">
                    <i class="bi bi-envelope"></i> Recuperar Contraseña
                </a>
            </div>
        </div>
    </div>
</body>
</html>
