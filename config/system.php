<?php

return [
    'default_itbis' => 18.00,

    'suscripcion' => [
        'grace_days' => (int) env('SUSCRIPCION_GRACE_DAYS', 3),
        'trial_days' => (int) env('SUSCRIPCION_TRIAL_DAYS', 15),
    ],
];
