<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistema POS') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 50%, #084298 100%);
            min-height: 100vh;
        }
        .welcome-card {
            backdrop-filter: blur(20px);
            background: rgba(255,255,255,0.95);
            border: none;
        }
        .feature-icon {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-md navbar-dark bg-transparent px-3 px-md-5 py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 text-white" href="/">
            <i class="bi bi-box-seam me-2"></i>{{ config('app.name', 'Sistema POS') }}
        </a>
        <div class="ms-auto d-flex gap-2">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-light rounded-pill px-4 fw-semibold">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill px-4">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-light rounded-pill px-4 fw-semibold">
                            <i class="bi bi-person-plus me-1"></i> Registrarse
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</nav>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card welcome-card rounded-5 shadow-lg overflow-hidden">
                <div class="row g-0">
                    <div class="col-lg-6 p-4 p-lg-5 d-flex flex-column justify-content-center">
                        <h1 class="display-5 fw-bold mb-3">
                            Sistema de <span class="text-primary">Facturación</span> POS
                        </h1>
                        <p class="lead text-muted mb-4">
                            Gestión completa de ventas, inventario, facturación electrónica (e-CF) y restaurante.
                            Todo en un solo lugar.
                        </p>
                        @auth
                            <div>
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                    <i class="bi bi-speedometer2 me-2"></i> Ir al Dashboard
                                </a>
                            </div>
                        @else
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar Sesión
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg rounded-pill px-5 fw-bold">
                                        <i class="bi bi-person-plus me-2"></i> Registrarse
                                    </a>
                                @endif
                            </div>
                        @endauth
                    </div>
                    <div class="col-lg-6 bg-primary bg-gradient p-4 p-lg-5 d-flex align-items-center justify-content-center">
                        <div class="text-center text-white">
                            <i class="bi bi-box-seam display-1 mb-3 d-block"></i>
                            <h3 class="fw-bold">POS Multi-Caja</h3>
                            <p class="opacity-75 mb-4">RD$ · ITBIS 18% · e-CF DGII</p>
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="bg-white bg-opacity-20 rounded-4 p-3">
                                        <i class="bi bi-cart fs-4"></i>
                                        <div class="small mt-1">Ventas</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-white bg-opacity-20 rounded-4 p-3">
                                        <i class="bi bi-box fs-4"></i>
                                        <div class="small mt-1">Stock</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-white bg-opacity-20 rounded-4 p-3">
                                        <i class="bi bi-cup-straw fs-4"></i>
                                        <div class="small mt-1">Rest.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-primary bg-opacity-10 text-primary rounded-3 mx-auto mb-3">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <h5 class="fw-bold">Facturación Electrónica</h5>
                            <p class="text-muted small">e-CF (DGII) integrado con generación de reportes fiscales 606/607.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-success bg-opacity-10 text-success rounded-3 mx-auto mb-3">
                                <i class="bi bi-archive"></i>
                            </div>
                            <h5 class="fw-bold">Inventario Multi-Almacén</h5>
                            <p class="text-muted small">Control de stock por almacén, movimientos y alertas de stock bajo.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-warning bg-opacity-10 text-warning rounded-3 mx-auto mb-3">
                                <i class="bi bi-cup-straw"></i>
                            </div>
                            <h5 class="fw-bold">Terminal Restaurante</h5>
                            <p class="text-muted small">Gestión de mesas, categorías, reservaciones, ticket cocina y más.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
