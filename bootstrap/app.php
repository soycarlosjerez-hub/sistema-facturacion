<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'instance.blocked' => \App\Http\Middleware\CheckInstanceBlocked::class,
            'setup.wizard' => \App\Http\Middleware\CheckSetupWizard::class,
            'tenant' => \App\Http\Middleware\TenantMiddleware::class,
            'api-auth' => \App\Http\Middleware\AuthenticateApiKey::class,
            'api.request.logger' => \App\Http\Middleware\ApiRequestLogger::class,
            'auth.cliente' => \App\Http\Middleware\AuthenticateCliente::class,
            'tenant.mail' => \App\Http\Middleware\TenantMailConfig::class,
            'plan.limits' => \App\Http\Middleware\EnforcePlanLimits::class,
            'ai' => \App\Http\Middleware\AiMiddleware::class,
            'ai.chat.method' => \App\Http\Middleware\AiChatMethodGuard::class,
            'instance.aprobada' => \App\Http\Middleware\CheckInstanceAprobada::class,
            'owner.dangerous' => \App\Http\Middleware\RateLimiterMiddleware::class,
            'api.version' => \App\Http\Middleware\ApiVersion::class,
        ]);

        $middleware->web([], [], [], [
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class => \App\Http\Middleware\VerifyCsrfToken::class,
        ]);
        $middleware->appendToGroup('web', \App\Http\Middleware\TrustProxies::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\TrackLastSeen::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckInstanceBlocked::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\TenantMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckSetupWizard::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\TenantMailConfig::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\EnforcePlanLimits::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\TrustProxies::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\TenantMailConfig::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\ApiVersion::class);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReport([
            \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        ]);

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 405) {
                $allow = $e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
                    ? $e->getHeaders()['Allow'] ?? 'GET, HEAD'
                    : 'GET, HEAD';

                if ($request->is('api/*')) {
                    return response()->json(['error' => 'Method Not Allowed'], 405)
                        ->header('Allow', $allow);
                }

                return response('Method Not Allowed', 405)
                    ->header('Allow', $allow);
            }
            if ($e->getStatusCode() === 403) {
                return response()->view('errors.403', ['message' => $e->getMessage()], 403);
            }
            if ($e->getStatusCode() === 404) {
                if ($request->is('api/*') || $request->expectsJson()) {
                    return response()->json([
                        'message' => $e->getMessage() ?? 'Recurso no encontrado.',
                        'errors' => [['message' => $e->getMessage() ?? 'Not found']],
                    ], 404);
                }

                return response()->view('errors.404', ['message' => $e->getMessage()], 404);
            }
        });
    })->create();
