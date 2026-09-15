<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instancia Bloqueada — Erpipos ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 50%, rgba(239, 68, 68, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 70% 20%, rgba(59, 130, 246, 0.06) 0%, transparent 40%);
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(20px, -20px) rotate(2deg); }
            66% { transform: translate(-15px, 15px) rotate(-1deg); }
        }

        .error-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px);
            border-radius: 28px;
            padding: 3rem 2.5rem;
            max-width: 560px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
            animation: cardEntry 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes cardEntry {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.96);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .icon-container {
            position: relative;
            width: 160px;
            height: 160px;
            margin: 0 auto 2rem;
        }

        .icon-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 50%, #f87171 100%);
            animation: pulse-ring 2s ease-in-out infinite;
        }

        .icon-ring::after {
            content: '';
            position: absolute;
            inset: 6px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        }

        @keyframes pulse-ring {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.9; }
        }

        .icon-inner {
            position: absolute;
            inset: 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .icon-inner i {
            font-size: 4.5rem;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #dc2626;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.15);
        }

        .status-badge i {
            font-size: 0.9rem;
        }

        h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }

        .description {
            color: #64748b;
            font-size: 1.05rem;
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .info-box {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .info-box-title {
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .info-box-title i {
            color: #3b82f6;
        }

        .info-box ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-box ul li {
            color: #64748b;
            font-size: 0.875rem;
            padding: 0.4rem 0;
            padding-left: 1.5rem;
            position: relative;
            line-height: 1.5;
        }

        .info-box ul li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 6px;
            background: #94a3b8;
            border-radius: 50%;
        }

        .btn-group-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-premium {
            padding: 0.85rem 2rem;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.95rem;
            border: none;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .btn-premium:active {
            transform: translateY(0);
        }

        .btn-primary-action {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .btn-primary-action:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
        }

        .btn-secondary-action {
            background: rgba(255, 255, 255, 0.9);
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary-action:hover {
            background: #fff;
            color: #334155;
            border-color: #cbd5e1;
        }

        .btn-success-action {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-success-action:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #fff;
        }

        .brand-footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 0.8rem;
        }

        .brand-footer a {
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
        }

        .brand-footer a:hover {
            color: #3b82f6;
        }

        @media (max-width: 576px) {
            .error-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
                border-radius: 24px;
            }
            h1 {
                font-size: 1.6rem;
            }
            .icon-container {
                width: 130px;
                height: 130px;
            }
            .icon-inner i {
                font-size: 3.5rem;
            }
            .btn-group-actions {
                flex-direction: column;
            }
            .btn-premium {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-container">
            <div class="icon-ring"></div>
            <div class="icon-inner">
                <i class="bi bi-lock-fill"></i>
            </div>
        </div>

        <div class="status-badge">
            <i class="bi bi-exclamation-triangle-fill"></i>
            Acceso Restringido
        </div>

        <h1>Instancia Bloqueada</h1>

        <p class="description mb-0">
            @if(session('error'))
                {{ session('error') }}
            @else
                Esta instancia ha sido bloqueada temporalmente. Para restaurar el acceso,
                es necesario actualizar la suscripción o contactar al administrador del sistema.
            @endif
        </p>

        <div class="info-box">
            <div class="info-box-title">
                <i class="bi bi-lightbulb"></i>
                ¿Qué puedes hacer?
            </div>
            <ul>
                <li>Verifica el estado de tu suscripción activa</li>
                <li>Realiza el pago pendiente para desbloquear el acceso</li>
                <li>Si crees que esto es un error, contacta a soporte técnico</li>
            </ul>
        </div>

        <div class="btn-group-actions">
            @auth
                <a href="{{ route('suscripcion.index') }}" class="btn-premium btn-success-action">
                    <i class="bi bi-credit-card-2-front"></i>
                    Ver Suscripción
                </a>
            @endauth

            <a href="{{ url('/logout') }}" class="btn-premium btn-secondary-action">
                <i class="bi bi-box-arrow-left"></i>
                Cerrar Sesión y Login
            </a>
        </div>

        <div class="brand-footer">
            Powered by <a href="https://erpipos.com" target="_blank" rel="noopener">Erpipos ERP</a>
        </div>
    </div>
</body>
</html>
