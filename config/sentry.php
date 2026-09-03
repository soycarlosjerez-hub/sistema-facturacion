<?php

return [
    'dsn' => env('SENTRY_LARAVEL_DSN', ''),
    'release' => env('SENTRY_RELEASE', null),
    'environment' => env('SENTRY_ENVIRONMENT', null),
    'traces_sample_rate' => env('SENTRY_TRACES_SAMPLE_RATE', 0.0),
    'profiles_sample_rate' => env('SENTRY_PROFILES_SAMPLE_RATE', 0.0),
    'send_default_pii' => false,
    'default_integrations' => true,
    // Filter sensitive data via Sentry Dashboard or DSN settings
    // 'before_send' is disabled here because closures are not serializable for caching
];
