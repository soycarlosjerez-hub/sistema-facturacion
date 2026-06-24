<?php

namespace App\Providers;

use App\Models\BusinessInstance;
use App\Models\Category;
use App\Models\SystemSetting;
use App\Policies\BusinessInstancePolicy;
use App\Policies\CategoryPolicy;
use App\View\Composers\DashboardComposer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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

        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(BusinessInstance::class, BusinessInstancePolicy::class);

        // Dynamic mail config from system settings
        try {
            if (Schema::hasTable('system_settings')) {
                $settings = Cache::rememberForever('system_settings_all', function () {
                    return SystemSetting::pluck('value', 'key')->toArray();
                });

                if (!empty($settings['mail_host'])) {
                    config([
                        'mail.default' => $settings['mail_mailer'] ?? 'log',
                        'mail.mailers.smtp.host' => $settings['mail_host'],
                        'mail.mailers.smtp.port' => (int)($settings['mail_port'] ?? 587),
                        'mail.mailers.smtp.username' => $settings['mail_username'] ?? null,
                        'mail.mailers.smtp.password' => isset($settings['mail_password']) && $settings['mail_password'] ? Crypt::decryptString($settings['mail_password']) : null,
                        'mail.mailers.smtp.encryption' => ($settings['mail_encryption'] ?? 'null') !== 'null' ? $settings['mail_encryption'] : null,
                        'mail.from.address' => $settings['mail_from_address'] ?? 'no-reply@facturacion.local',
                        'mail.from.name' => $settings['mail_from_name'] ?? config('app.name'),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Table might not exist during fresh install / migrations
        }

        View::composer('layouts.app', function ($view) {
            $sesionCajaGlobal = null;
            $sucursales = collect([]);
            $sucursalActiva = null;
            if (auth()->check()) {
                $sesionCajaGlobal = \App\Models\SesionCaja::with('caja')
                    ->where('user_id', auth()->id())
                    ->where('estado', 'abierta')
                    ->latest('fecha_apertura')
                    ->first();
                $sucursales = \App\Models\Sucursal::orderBy('nombre')->get();
                $sucursalActiva = \App\Models\Sucursal::find(session('sucursal_id'));
            }
            $view->with([
                'systemName'      => SystemSetting::empresaNombre(),
                'systemSlogan'    => SystemSetting::empresaSlogan(),
                'systemMoneda'    => SystemSetting::monedaSimbolo(),
                'systemItbis'     => SystemSetting::itbisDefault(),
                'sesionCajaGlobal'=> $sesionCajaGlobal,
                'sucursales'      => $sucursales,
                'sucursalActiva'  => $sucursalActiva,
            ]);
        });

        View::composer('dashboard', DashboardComposer::class);

        Event::listen(\Illuminate\Support\MessageLogged::class, \App\Listeners\LogErrorToDatabase::class);
    }
}
