<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiVersion
{
    protected array $deprecatedVersions = ['v3'];
    protected string $currentVersion = 'v4';

    public function handle(Request $request, Closure $next): Response
    {
        // Set API version header on response
        $response = $next($request);

        // Extract version from route prefix
        $path = $request->path();
        if (str_starts_with($path, 'api/v3/')) {
            $response->headers->set('X-API-Version', 'v3');
            $response->headers->set('X-API-Deprecation-Date', '2026-12-31');
            $response->headers->set('X-API-Sunset-Date', '2027-06-30');
            $response->headers->set('Link', '</api/v4/>; rel="successor-version"');
        } else {
            $response->headers->set('X-API-Version', $this->currentVersion);
        }

        // Set Accept-Version header from request or default
        $requestedVersion = $request->header('Accept-API-Version', $this->currentVersion);
        $response->headers->set('X-API-Accepted-Version', $requestedVersion);

        return $response;
    }
}
