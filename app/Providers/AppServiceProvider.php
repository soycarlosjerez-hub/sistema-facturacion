<?php

namespace App\Providers;

use App\Models\Almacen;
use App\Models\BusinessInstance;
use App\Models\Category;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Conduce;
use App\Models\Cotizacion;
use App\Models\Caja;
use App\Models\Devolucion;
use App\Models\Gasto;
use App\Models\Mesa;
use App\Models\Orden;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Venta;
use App\Observers\PlanLimitObserver;
use App\Policies\BusinessInstancePolicy;
use App\Policies\BusinessTypePolicy;
use App\Policies\CategoryPolicy;
use App\Policies\TipoClimaPolicy;
use App\Policies\InstalacionPolicy;
use App\Policies\ContratoMantenimientoPolicy;
use App\Policies\MantenimientoPolicy;
use App\Policies\TicketGarantiaPolicy;
use App\Policies\OrdenEmergenciaPolicy;
use App\View\Composers\DashboardComposer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Schema::defaultStringLength(191);

        BusinessInstance::observe(\App\Observers\BusinessInstanceObserver::class);

        // Plan Limit Observers
        $planLimitModels = [
            User::class,
            Sucursal::class,
            Almacen::class,
            Producto::class,
            Cliente::class,
            Proveedor::class,
            Venta::class,
            Compra::class,
            Gasto::class,
            Caja::class,
            Cotizacion::class,
            Conduce::class,
            Devolucion::class,
            Orden::class,
            Mesa::class,
        ];

        foreach ($planLimitModels as $model) {
            $model::observe(PlanLimitObserver::class);
        }

        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        // Blade directive for checking module access by plan
        \Illuminate\Support\Facades\Blade::directive('canModulo', function ($expression) {
            return "<?php if (isset(\$modulosPermitidos) && (empty(\$modulosPermitidos) || in_array({$expression}, \$modulosPermitidos))): ?>";
        });
        \Illuminate\Support\Facades\Blade::directive('endcanModulo', function () {
            return "<?php endif; ?>";
        });

        // Blade directive for checking plan limit
        \Illuminate\Support\Facades\Blade::directive('planLimit', function ($expression) {
            return "<?php if (isset(\$planLimites) && (\$planLimites[{$expression}] ?? null) !== null): ?>";
        });
        \Illuminate\Support\Facades\Blade::directive('endplanLimit', function () {
            return "<?php endif; ?>";
        });

        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(BusinessInstance::class, BusinessInstancePolicy::class);
        Gate::policy(\App\Models\TipoClima::class, TipoClimaPolicy::class);
        Gate::policy(\App\Models\Instalacion::class, InstalacionPolicy::class);
        Gate::policy(\App\Models\ContratoMantenimiento::class, ContratoMantenimientoPolicy::class);
        Gate::policy(\App\Models\Mantenimiento::class, MantenimientoPolicy::class);
        Gate::policy(\App\Models\TicketGarantia::class, TicketGarantiaPolicy::class);
        Gate::policy(\App\Models\OrdenEmergencia::class, OrdenEmergenciaPolicy::class);

        // Fallback: apply global SMTP config for console/scheduled tasks (HTTP requests use TenantMailConfig middleware)
        try {
            if (Schema::hasTable('system_settings')) {
                $settings = Cache::rememberForever('system_settings_all_global', function () {
                    return SystemSetting::whereNull('tenant_id')
                        ->pluck('valor', 'clave')
                        ->toArray();
                });

                if (!empty($settings['mail_host'])) {
                    $mailer = $settings['mail_mailer'] ?? 'smtp';
                    if ($mailer === 'log') {
                        $mailer = 'smtp';
                    }
                    config([
                        'mail.default' => $mailer,
                        'mail.mailers.' . $mailer . '.host' => $settings['mail_host'],
                        'mail.mailers.' . $mailer . '.port' => (int)($settings['mail_port'] ?? 587),
                        'mail.mailers.' . $mailer . '.username' => $settings['mail_username'] ?? null,
                        'mail.mailers.' . $mailer . '.password' => isset($settings['mail_password']) && $settings['mail_password'] ? Crypt::decryptString($settings['mail_password']) : null,
                        'mail.mailers.' . $mailer . '.encryption' => ($settings['mail_encryption'] ?? 'null') !== 'null' ? $settings['mail_encryption'] : null,
                        'mail.from.address' => $settings['mail_from_address'] ?? 'no-reply@facturacion.local',
                        'mail.from.name' => $settings['mail_from_name'] ?? config('app.name'),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Table might not exist during fresh install / migrations
        }

        // El ITBIS por instancia debe estar disponible en TODAS las vistas
        // (los compositors por layout no alcanzan las secciones hijas).
        View::composer('*', function ($view) {
            static $itbis = null;
            if ($itbis === null) {
                try {
                    $itbis = SystemSetting::itbisDefault();
                } catch (\Throwable $e) {
                    $itbis = 18;
                }
            }
            $view->with('systemItbis', $itbis);
        });

        View::composer('layouts.app', function ($view) {
            $sesionesCajaGlobales = collect([]);
            $sucursales = collect([]);
            $sucursalActiva = null;
            $planLimites = [];
            $modulosPermitidos = [];
            $systemLogo = null;
            if (auth()->check()) {
                $sesionesCajaGlobales = \App\Models\SesionCaja::with('caja')
                    ->where('user_id', auth()->id())
                    ->where('estado', 'abierta')
                    ->latest('fecha_apertura')
                    ->get();
                $sucursales = \App\Models\Sucursal::orderBy('nombre')->get();
                $sucursalActiva = \App\Models\Sucursal::find(session('sucursal_id'));

                // Share plan limits and allowed modules
                $instance = auth()->user()->businessInstance;
                if ($instance && $instance->plan) {
                    $planLimites = $instance->plan->getLimites();
                    $modulosPermitidos = $instance->plan->modulosPermitidos();
                }

                // Logo de la instancia
                if ($instance) {
                    $systemLogo = $instance->logo_url;
                }
            }
            $view->with([
                'systemName'         => SystemSetting::empresaNombre(),
                'systemSlogan'       => SystemSetting::empresaSlogan(),
                'systemMoneda'       => SystemSetting::monedaSimbolo(),
                'systemItbis'        => SystemSetting::itbisDefault(),
                'sesionesCajaGlobales' => $sesionesCajaGlobales,
                'sucursales'         => $sucursales,
                'sucursalActiva'     => $sucursalActiva,
                'planLimites'        => $planLimites,
                'modulosPermitidos'  => $modulosPermitidos,
                'systemLogo'         => $systemLogo,
            ]);
        });

        View::composer('dashboard', DashboardComposer::class);

        // Pass logo to all PDF views
        $pdfViews = [
            'ventas.pdf', 'ventas.ticket', 'ventas.all-pdf', 'ventas.ecf-pdf',
            'cotizaciones.pdf', 'cotizaciones.ticket',
            'compras.all-pdf',
            'clientes.pdf', 'proveedores.pdf', 'categorias.pdf', 'productos.pdf',
            'almacenes.movimientos-pdf', 'kardex.pdf',
            'reportes.ventas-pdf', 'reportes.stock-pdf', 'reportes.gastos-pdf',
            'reportes.compras-pdf', 'reportes.caja-pdf', 'reportes.fiscales-pdf',
            'pdf.productos_bajos_stock',
            'libros.ventas.pdf', 'libros.compras.pdf',
            'conduces.pdf', 'conduces.ticket',
            'formularios.14-14.pdf',
            'restaurante.ticket',
            'ventas.show',
        ];
        View::composer($pdfViews, function ($view) {
            $pdfLogoUrl = null;
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user && $user->businessInstance) {
                $logoPath = $user->businessInstance->logo;
                if ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath)) {
                    $pdfLogoUrl = 'data:' . mime_content_type(\Illuminate\Support\Facades\Storage::disk('public')->path($logoPath)) . ';base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($logoPath));
                }
            }
            $view->with('pdfLogoUrl', $pdfLogoUrl);
        });

        Event::listen(\Illuminate\Support\MessageLogged::class, \App\Listeners\LogErrorToDatabase::class);
    }
}
