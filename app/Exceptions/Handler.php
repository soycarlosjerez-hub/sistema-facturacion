<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception): \Symfony\Component\HttpFoundation\Response
    {
        $statusCode = $this->getStatusCode($exception);

        // Log and render custom pages for 500+ errors
        if ($statusCode >= 500) {
            Log::error('Unhandled exception', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'user_id' => auth()->id() ?? null,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);

            // Save to instance error logs if tenant context exists
            if (auth()->check() && auth()->user()->business_instance_id) {
                try {
                    \App\Models\InstanceErrorLog::create([
                        'tenant_id' => auth()->user()->business_instance_id,
                        'user_id' => auth()->id(),
                        'user_name' => auth()->user()->name,
                        'user_role' => auth()->user()->roles->pluck('name')->join(', '),
                        'exception' => get_class($exception) . ': ' . $exception->getMessage(),
                        'http_method' => $request->method(),
                        'url' => $request->fullUrl(),
                        'ip_address' => $request->ip(),
                        'inputs' => json_encode($request->except(['password', 'password_confirmation'])),
                    ]);
                } catch (\Throwable $e) {
                    // Fail silently
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Ha ocurrido un error interno del servidor.',
                    'error' => app()->environment('production') ? 'Internal Server Error' : $exception->getMessage(),
                ], 500);
            }

            return response()->view('errors.500', ['message' => $exception->getMessage()], 500);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception): \Symfony\Component\HttpFoundation\Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        return redirect()->guest(route('login'));
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Get the status code for an exception.
     */
    protected function getStatusCode(Throwable $exception): int
    {
        if ($this->isHttpException($exception)) {
            return $exception->getStatusCode();
        }

        return 500;
    }
}
