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
                \App\Models\InstanceErrorLog::create([
                    'tenant_id' => \Illuminate\Support\Facades\Auth::user()?->business_instance_id,
                    'level' => 'error',
                    'title' => mb_substr($e->getMessage() ?: get_class($e), 0, 255),
                    'message' => $e->getMessage() . "\n\n" . $e->getTraceAsString(),
                    'context' => [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ],
                    'source' => 'exception',
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Throwable $dbEx) {
                // Si la tabla no existe aún o hay error de BD, ignorar
            }
        });
    })->create();
