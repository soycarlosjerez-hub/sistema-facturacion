<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de Error</title>
    <style>
        body {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #0f0f23; color: #c8d6e5; margin: 0; padding: 0;}
        .container {width: 100%; max-width: 680px; margin: 30px auto; background: #1a1a2e; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.4);}
        .header {padding: 30px; text-align: center; color: #fff;}
        .header.critical {background: linear-gradient(135deg, #e74c3c, #c0392b);}
        .header.error {background: linear-gradient(135deg, #e74c3c, #a93226);}
        .header.warning {background: linear-gradient(135deg, #f39c12, #d68910);}
        .header h1 {margin: 0; font-size: 22px; letter-spacing: 1px;}
        .header .icon {font-size: 40px; margin-bottom: 10px;}
        .badge {display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-top: 8px;}
        .badge.critical {background: #ff6b6b; color: #fff;}
        .badge.error {background: #ee5a6f; color: #fff;}
        .badge.warning {background: #ffc048; color: #333;}
        .content {padding: 30px;}
        .section {margin-bottom: 20px;}
        .section-title {font-size: 13px; text-transform: uppercase; color: #888; letter-spacing: 1px; margin-bottom: 8px; font-weight: 600;}
        .section-value {font-size: 15px; color: #e0e0e0; word-break: break-word;}
        .code-block {background: #0d0d1a; border: 1px solid #2a2a4a; border-radius: 8px; padding: 15px; font-family: 'Consolas', 'Courier New', monospace; font-size: 13px; color: #a8d8ea; white-space: pre-wrap; word-break: break-all; max-height: 300px; overflow-y: auto; margin: 10px 0;}
        .meta-grid {display: grid; grid-template-columns: 1fr 1fr; gap: 15px;}
        .meta-item {background: #12122a; border-radius: 8px; padding: 12px;}
        .meta-label {font-size: 11px; color: #666; text-transform: uppercase; letter-spacing: 0.5px;}
        .meta-value {font-size: 13px; color: #bbb; margin-top: 4px; word-break: break-all;}
        .context-table {width: 100%; border-collapse: collapse; margin-top: 10px;}
        .context-table th, .context-table td {padding: 8px 12px; text-align: left; border-bottom: 1px solid #2a2a4a; font-size: 13px;}
        .context-table th {color: #888; font-weight: 600; width: 140px;}
        .context-table td {color: #ccc;}
        .info-box {background: #12122a; border-radius: 8px; padding: 15px; margin-bottom: 10px;}
        .info-row {display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #1e1e3a;}
        .info-row:last-child {border-bottom: none;}
        .info-label {color: #888; font-size: 13px; font-weight: 600;}
        .info-value {color: #e0e0e0; font-size: 13px; word-break: break-all;}
        .input-table {width: 100%; border-collapse: collapse; margin-top: 10px;}
        .input-table th, .input-table td {padding: 8px 12px; text-align: left; border-bottom: 1px solid #2a2a4a; font-size: 12px;}
        .input-table th {color: #888; font-weight: 600; width: 200px; background: #0d0d1a;}
        .input-table td {color: #a8d8ea; font-family: 'Consolas', 'Courier New', monospace; word-break: break-all;}
        .footer {background: #12122a; color: #555; text-align: center; padding: 20px; font-size: 12px; border-top: 1px solid #2a2a4a;}
        .btn-view {display: inline-block; background: linear-gradient(135deg, #4e54c8, #8f94fb); color: #fff; text-decoration: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; font-size: 14px; margin-top: 10px;}
        @media (max-width: 500px) {.meta-grid {grid-template-columns: 1fr;}}
    </style>
</head>
<body>
<div class="container">
    <div class="header {{ $level }}">
        <div class="icon">{{ $level === 'critical' ? '🔴' : ($level === 'error' ? '⚠️' : '⚡') }}</div>
        <h1>{{ $title }}</h1>
        <span class="badge {{ $level }}">{{ $level }}</span>
    </div>
    <div class="content">

        @if($tenantName || $tenantId)
        <div class="section">
            <div class="section-title">Instancia</div>
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">ID</span>
                    <span class="info-value">{{ $tenantId ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nombre</span>
                    <span class="info-value">{{ $tenantName ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        @endif

        @if($userName || $userEmail || $userRole || $userId)
        <div class="section">
            <div class="section-title">Usuario</div>
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">ID</span>
                    <span class="info-value">{{ $userId ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nombre</span>
                    <span class="info-value">{{ $userName ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $userEmail ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Rol</span>
                    <span class="info-value">{{ $userRole ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        @endif

        @if($httpMethod || $url || $referer)
        <div class="section">
            <div class="section-title">Requerimiento</div>
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Método</span>
                    <span class="info-value">{{ $httpMethod ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">URL</span>
                    <span class="info-value">{{ $url ?? 'N/A' }}</span>
                </div>
                @if($referer)
                <div class="info-row">
                    <span class="info-label">Referer</span>
                    <span class="info-value">{{ $referer }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($inputs && count($inputs) > 0)
        <div class="section">
            <div class="section-title">Inputs Enviados</div>
            <table class="input-table">
                @foreach($inputs as $key => $value)
                <tr>
                    <th>{{ $key }}</th>
                    <td>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

        @if($sessionId || $ipAddress)
        <div class="section">
            <div class="section-title">Sesión</div>
            <div class="meta-grid">
                @if($sessionId)
                <div class="meta-item">
                    <div class="meta-label">Session ID</div>
                    <div class="meta-value">{{ $sessionId }}</div>
                </div>
                @endif
                @if($ipAddress)
                <div class="meta-item">
                    <div class="meta-label">IP</div>
                    <div class="meta-value">{{ $ipAddress }}</div>
                </div>
                @endif
                @if($userAgent)
                <div class="meta-item" style="grid-column: 1 / -1;">
                    <div class="meta-label">User-Agent</div>
                    <div class="meta-value">{{ $userAgent }}</div>
                </div>
                @endif
                @if($createdAt)
                <div class="meta-item">
                    <div class="meta-label">Fecha/Hora</div>
                    <div class="meta-value">{{ $createdAt }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($exceptionClass)
        <div class="section">
            <div class="section-title">Tipo de Excepción</div>
            <div class="section-value">{{ $exceptionClass }}</div>
        </div>
        @endif

        @if($file || $line)
        <div class="section">
            <div class="section-title">Ubicación</div>
            <div class="section-value">{{ $file }}:{{ $line }}</div>
        </div>
        @endif

        @if($source)
        <div class="section">
            <div class="section-title">Fuente</div>
            <div class="section-value">{{ $source }}</div>
        </div>
        @endif

        <div class="section">
            <div class="section-title">Stack Trace</div>
            <div class="code-block">{{ $errorMessage }}</div>
        </div>

        @if(!empty($context))
        <div class="section" style="margin-top: 20px;">
            <div class="section-title">Contexto Adicional</div>
            <table class="context-table">
                @foreach($context as $key => $value)
                <tr>
                    <th>{{ $key }}</th>
                    <td>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

        <div style="text-align: center; margin-top: 25px;">
            <a href="{{ url('/owner/errors') }}" class="btn-view">Ver Panel de Errores</a>
        </div>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }} — Sistema de Alertas Automático
    </div>
</div>
</body>
</html>
