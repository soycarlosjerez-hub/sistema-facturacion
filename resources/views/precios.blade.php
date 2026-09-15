<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Precios - Erpipos ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary: #3b82f6; --dark: #0f172a; --light: #f8fafc; }
        body { font-family: system-ui, -apple-system, sans-serif; background: var(--light); }
        .hero { background: linear-gradient(135deg, var(--dark) 0%, #1e293b 100%); color: white; padding: 80px 0; }
        .pricing-card { border: none; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); transition: all 0.3s; }
        .pricing-card:hover { transform: translateY(-4px); box-shadow: 0 8px 32px rgba(59,130,246,0.15); }
        .pricing-card.featured { border: 2px solid var(--primary); }
        .badge-popular { background: var(--primary); }
        .feature-list li { padding: 6px 0; }
        .price { font-size: 2.5rem; font-weight: 800; }
        .price-period { font-size: 1rem; color: #64748b; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: rgba(15,23,42,0.95); backdrop-filter: blur(10px);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">Erpipos</a>
            <div class="ms-auto">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light me-2">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Registro</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="hero text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Planes y Precios</h1>
            <p class="lead mb-4">Elige el plan que mejor se adapte a tu negocio. Todos incluyen e-CF, soporte y actualizaciones.</p>
            <a href="{{ route('register') }}" class="btn btn-lg btn-primary px-5">Comenzar Gratis</a>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-4 justify-content-center">
            <!-- Plan Starter -->
            <div class="col-md-4">
                <div class="card pricing-card h-100 p-4">
                    <div class="card-body text-center">
                        <h4 class="fw-bold">Starter</h4>
                        <p class="text-muted">Para negocios pequeños</p>
                        <div class="my-4">
                            <span class="price">RD$ 999</span>
                            <span class="price-period">/mes</span>
                        </div>
                        <ul class="list-unstyled feature-list text-start">
                            <li class="feature"><i class="text-success me-2">✓</i> Hasta 100 ventas/mes</li>
                            <li class="feature"><i class="text-success me-2">✓</i> 1 usuario</li>
                            <li class="feature"><i class="text-success me-2">✓</i> e-CF básico</li>
                            <li class="feature"><i class="text-success me-2">✓</i> Soporte por email</li>
                            <li class="feature text-muted"><i class="me-2">✗</i> Multi-sucursal</li>
                            <li class="feature text-muted"><i class="me-2">✗</i> API</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 mt-3">Elegir Plan</a>
                    </div>
                </div>
            </div>

            <!-- Plan Profesional -->
            <div class="col-md-4">
                <div class="card pricing-card featured h-100 p-4 position-relative">
                    <span class="badge rounded-pill badge-popular position-absolute top-0 start-50 translate-middle px-3 py-2">Popular</span>
                    <div class="card-body text-center">
                        <h4 class="fw-bold mt-2">Profesional</h4>
                        <p class="text-muted">Para negocios en crecimiento</p>
                        <div class="my-4">
                            <span class="price">RD$ 2,499</span>
                            <span class="price-period">/mes</span>
                        </div>
                        <ul class="list-unstyled feature-list text-start">
                            <li class="feature"><i class="text-success me-2">✓</i> Ventas ilimitadas</li>
                            <li class="feature"><i class="text-success me-2">✓</i> Hasta 5 usuarios</li>
                            <li class="feature"><i class="text-success me-2">✓</i> e-CF completo</li>
                            <li class="feature"><i class="text-success me-2">✓</i> Soporte prioritario</li>
                            <li class="feature"><i class="text-success me-2">✓</i> Multi-sucursal</li>
                            <li class="feature text-muted"><i class="me-2">✗</i> API</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-primary w-100 mt-3">Elegir Plan</a>
                    </div>
                </div>
            </div>

            <!-- Plan Enterprise -->
            <div class="col-md-4">
                <div class="card pricing-card h-100 p-4">
                    <div class="card-body text-center">
                        <h4 class="fw-bold">Enterprise</h4>
                        <p class="text-muted">Para empresas grandes</p>
                        <div class="my-4">
                            <span class="price">RD$ 5,999</span>
                            <span class="price-period">/mes</span>
                        </div>
                        <ul class="list-unstyled feature-list text-start">
                            <li class="feature"><i class="text-success me-2">✓</i> Todo lo de Profesional</li>
                            <li class="feature"><i class="text-success me-2">✓</i> Usuarios ilimitados</li>
                            <li class="feature"><i class="text-success me-2">✓</i> API completa</li>
                            <li class="feature"><i class="text-success me-2">✓</i> Soporte 24/7</li>
                            <li class="feature"><i class="text-success me-2">✓</i> SLA 99.9%</li>
                            <li class="feature"><i class="text-success me-2">✓</i> Onboarding dedicado</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 mt-3">Contactar Ventas</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <p class="text-muted">¿Necesitas un plan personalizado? <a href="mailto:ventas@erpipos.com">Contáctanos</a></p>
        </div>
    </div>

    <footer class="text-center py-4 text-muted border-top mt-5">
        <div class="container">
            © {{ date('Y') }} Erpipos ERP. Todos los derechos reservados.
            <a href="/terminos">Términos</a> · <a href="/privacidad">Privacidad</a>
        </div>
    </footer>
</body>
</html>
