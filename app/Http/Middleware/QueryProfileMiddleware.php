<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class QueryProfileMiddleware
{
    private array $queries = [];
    private float $startTime;
    private int $slowQueryThreshold = 100; // ms

    public function handle(Request $request, Closure $next): Response
    {
        $this->startTime = microtime(true);
        $this->queries = [];

        DB::listen(function ($query) {
            $this->queries[] = [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
                'connection' => $query->connectionName,
            ];
        });

        $response = $next($request);

        $totalTime = (microtime(true) - $this->startTime) * 1000;
        $slowQueries = array_filter($this->queries, fn($q) => $q['time'] > $this->slowQueryThreshold);

        if (config('app.debug')) {
            Log::channel('query_profile')->info("Query Profile", [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'total_time_ms' => round($totalTime, 2),
                'query_count' => count($this->queries),
                'slow_queries' => count($slowQueries),
                'queries' => $this->queries,
            ]);
        }

        $response->headers->set('X-Total-Queries', count($this->queries));
        $response->headers->set('X-Query-Time', round($totalTime, 2) . 'ms');

        return $response;
    }
}
