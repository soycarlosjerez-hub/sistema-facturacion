<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acceso Denegado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .error-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px);
            border-radius: 28px;
            padding: 3rem 2.5rem;
            max-width: 520px;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        }
        .icon-bubble {
            width: 140px;
            height: 140px;
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        .icon-bubble i {
            font-size: 5rem;
            color: #dc2626;
        }
        .error-code {
            font-size: 4rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-bubble">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div class="error-code">403</div>
        <h2 class="fw-bold mb-2 mt-3">Acceso Denegado</h2>
        <p class="text-muted mb-4">{{ $message ?? 'No tienes permisos para acceder a esta sección del sistema.' }}</p>

        <div class="card border-0 shadow-sm rounded-4 mb-4 text-start" style="background: rgba(248, 250, 252, 0.7);">
            <div class="card-body p-3">
                <h6 class="fw-bold mb-2 small"><i class="bi bi-info-circle me-2"></i>¿Por qué veo esto?</h6>
                <ul class="mb-0 small text-muted">
                    <li>Tu rol actual no tiene acceso a esta sección.</li>
                    <li>Cada rol tiene permisos específicos para mantener la seguridad.</li>
                    <li>Si crees que deberías tener acceso, contacta al administrador.</li>
                </ul>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-center">
            <a href="/" class="btn btn-primary rounded-pill px-4 fw-bold">
                <i class="bi bi-house-door me-1"></i> Ir al Inicio
            </a>
            <button onclick="history.back()" class="btn btn-light rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </button>
        </div>
    </div>
</body>
</html>
