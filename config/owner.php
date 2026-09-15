<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Owner Bootstrap Configuration
    |--------------------------------------------------------------------------
    |
    | Este archivo configura el "Owner Bootstrap": un mecanismo de respaldo
    | que permite al usuario Owner autenticarse incluso cuando la tabla de
    | usuarios estÃ¡ vacia o la BD no existe.
    |
    | La contraseÃ±a debe ser un hash bcrypt generado externamente con:
    |   php -r "echo password_hash('tu-password', PASSWORD_BCRYPT);"
    |
    | Nunca se almacena en texto plano en este archivo ni en cÃ³digo fuente.
    | Solo se lee desde variables de entorno (.env).
    */

    'enabled' => (bool) env('OWNER_ENABLED', false),

    'email' => env('OWNER_EMAIL', 'owner@sistema-facturacion.com'),

    'name' => env('OWNER_NAME', 'Owner'),

    'password_hash' => env('OWNER_PASSWORD', ''),

    'role' => env('OWNER_ROLE', 'owner'),

    'use_password_hash' => (bool) env('OWNER_USE_PASSWORD_HASH', false),

    'bypass_2fa' => (bool) env('OWNER_BYPASS_2FA', false),

    'max_attempts' => (int) env('OWNER_MAX_ATTEMPTS', 10),

    'lockout_minutes' => (int) env('OWNER_LOCKOUT_MINUTES', 5),
];
