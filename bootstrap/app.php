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
            'tenant.mail'      => \App\Http\Middleware\TenantMailConfig::class,
            'plan.limits'      => \App\Http\Middleware\EnforcePlanLimits::class,
            'ai'               => \App\Http\Middleware\AiMiddleware::class,
            'ai.chat.method'   => \App\Http\Middleware\AiChatMethodGuard::class,
        ]);

        $middleware->appendToGroup('web', \App\Http\Middleware\TrackLastSeen::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckInstanceBlocked::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckSetupWizard::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\TenantMailConfig::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\EnforcePlanLimits::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\TenantMailConfig::class);

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
                return response()->view('errors.404', ['message' => $e->getMessage()], 404);
            }
        });

        $exceptions->reportable(function (\Throwable $e) {
            try {
                // Apply global SMTP config from owner settings (never .env, never tenant-specific)
                \App\Services\ErrorMailer::applyGlobalSmtp();

                $request = request();
                $userId = \Illuminate\Support\Facades\Auth::id();
                
                // Obtener datos del usuario
                $user = null;
                $userName = null;
                $userEmail = null;
                $userRole = null;
                $userBusinessInstanceId = null;
                if ($userId) {
                    $user = \App\Models\User::find($userId);
                    $userName = $user?->name;
                    $userEmail = $user?->email;
                    $userRole = $user?->roles?->first()?->name ?? 'Sin rol';
                    $userBusinessInstanceId = $user?->business_instance_id;
                }

                $tenantId = $userBusinessInstanceId;
                $tenant = $tenantId ? \App\Models\BusinessInstance::find($tenantId) : null;

                $errorLog = \App\Models\InstanceErrorLog::create([
                    'tenant_id' => $tenantId,
                    'level' => 'error',
                    'title' => mb_substr($e->getMessage() ?: get_class($e), 0, 255),
                    'message' => $e->getMessage() . "\n\n" . $e->getTraceAsString(),
                    'context' => [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'http_method' => $request?->method(),
                        'url' => $request?->fullUrl(),
                        'referer' => $request?->headers->get('referer'),
                        'user_id' => $userId,
                        'user_name' => $userName,
                        'user_email' => $userEmail,
                        'user_role' => $userRole,
                        'tenant_id' => $tenantId,
                        'tenant_name' => $tenant?->name,
                        'session_id' => $request?->session()?->getId(),
                        'ip_address' => $request?->ip(),
                        'user_agent' => $request?->userAgent(),
                        'inputs' => $request?->except(['password', 'password_confirmation', '_token']),
                    ],
                    'source' => 'exception',
                    'user_id' => $userId,
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'ip_address' => $request?->ip(),
                    'user_agent' => $request?->userAgent(),
                ]);

                $alertEmail = \App\Services\ErrorMailer::getAlertEmail();
                if ($alertEmail) {
                    $cacheKey = 'error_alert:' . md5(get_class($e) . $e->getMessage());
                    if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                        \Illuminate\Support\Facades\Cache::put($cacheKey, true, 300);
                        \Illuminate\Support\Facades\Mail::to($alertEmail)
                            ->send(new \App\Mail\ErrorAlertMail(
                                level: 'error',
                                title: $errorLog->title,
                                errorMessage: $errorLog->message,
                                exceptionClass: get_class($e),
                                file: $e->getFile(),
                                line: $e->getLine(),
                                ipAddress: $request?->ip(),
                                userAgent: $request?->userAgent(),
                                context: $errorLog->context,
                                source: 'exception',
                                createdAt: $errorLog->created_at->format('Y-m-d H:i:s'),
                                tenantName: $tenant?->name,
                                tenantId: $tenantId,
                                userId: $userId,
                                userName: $userName,
                                userEmail: $userEmail,
                                userRole: $userRole,
                                httpMethod: $request?->method(),
                                url: $request?->fullUrl(),
                                referer: $request?->headers->get('referer'),
                                sessionId: $request?->session()?->getId(),
                                inputs: $request?->except(['password', 'password_confirmation', '_token']),
                            ));
                    }
                }
            } catch (\Throwable $dbEx) {
                // Si la tabla no existe aún o hay error de BD, ignorar
            }
        });
    })->create();
