<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Query Profile Configuration
    |--------------------------------------------------------------------------
    |
    | Este middleware registra todas las queries SQL ejecutadas y las
    | envía a un canal de log especial para profiling.
    |
    | solo en desarrollo/estaging - NO en producción.
    */

    'enabled' => env('QUERY_PROFILE_ENABLED', false),
    'slow_query_threshold_ms' => 100,
    'max_queries_to_log' => 1000,
    'log_level' => 'info',
];
