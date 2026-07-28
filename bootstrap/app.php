<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'             => \App\Http\Middleware\RoleMiddleware::class,
            'permission'       => \App\Http\Middleware\PermissionMiddleware::class,
            'instance.blocked' => \App\Http\Middleware\CheckInstanceBlocked::class,
            'setup.wizard'     => \App\Http\Middleware\CheckSetupWizard::class,
            'tenant'           => \App\Http\Middleware\TenantMiddleware::class,
            'api-auth'         => \App\Http\Middleware\AuthenticateApiKey::class,
            'api.request.logger' => \App\Http\Middleware\ApiRequestLogger::class,
            'auth.cliente'       => \App\Http\Middleware\AuthenticateCliente::class,
        ]);

        $middleware->appendToGroup('web', \App\Http\Middleware\TrackLastSeen::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckInstanceBlocked::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckSetupWizard::class);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                return response()->view('errors.403', ['message' => $e->getMessage()], 403);
            }
            if ($e->getStatusCode() === 404) {
                return response()->view('errors.404', ['message' => $e->getMessage()], 404);
            }
        });

        $exceptions->reportable(function (\Throwable $e) {
            try {
                $request = request();
                $userId = \Illuminate\Support\Facades\Auth::id();
                $tenantId = $userId ? \App\Models\User::find($userId)?->business_instance_id : null;

                $errorLog = \App\Models\InstanceErrorLog::create([
                    'tenant_id' => $tenantId,
                    'level' => 'error',
                    'title' => mb_substr($e->getMessage() ?: get_class($e), 0, 255),
                    'message' => $e->getMessage() . "\n\n" . $e->getTraceAsString(),
                    'context' => [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ],
                    'source' => 'exception',
                    'user_id' => $userId,
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                $alertEmail = config('app.error_alert_email', env('ERROR_ALERT_EMAIL'));
                if ($alertEmail) {
                    $cacheKey = 'error_alert:' . md5(get_class($e) . $e->getMessage());
                    if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                        \Illuminate\Support\Facades\Cache::put($cacheKey, true, 300);
                        $tenantName = $errorLog->tenant->name ?? null;
                        \Illuminate\Support\Facades\Mail::to($alertEmail)
                            ->queue((new \App\Mail\ErrorAlertMail(
                                level: 'error',
                                title: $errorLog->title,
                                message: $errorLog->message,
                                exceptionClass: get_class($e),
                                file: $e->getFile(),
                                line: $e->getLine(),
                                ipAddress: $request->ip(),
                                userAgent: $request->userAgent(),
                                context: $errorLog->context,
                                source: 'exception',
                                createdAt: $errorLog->created_at->format('Y-m-d H:i:s'),
                                tenantName: $tenantName,
                            )))->onQueue('errors');
                    }
                }
            } catch (\Throwable $dbEx) {
                // Si la tabla no existe aún o hay error de BD, ignorar
            }
        });
    })->create();
