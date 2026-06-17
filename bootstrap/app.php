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
        ]);

        $middleware->appendToGroup('web', \App\Http\Middleware\CheckInstanceBlocked::class);
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
    })->create();
