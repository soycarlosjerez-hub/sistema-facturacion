<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'owner@sistema-facturacion.com')->first();
echo 'User: ' . $user->name . PHP_EOL;
echo 'Roles: ' . implode(', ', $user->getRoleNames()->toArray()) . PHP_EOL;

// Login the user for Auth facade
Auth::login($user);

$sidebar = new App\Support\Sidebar();
echo 'Menu items count: ' . count($sidebar->menu()) . PHP_EOL;
print_r($sidebar->menu());