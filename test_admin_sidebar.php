<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'whilepon@sistema-facturacion.com')->first();
Auth::login($user);

echo 'User: ' . $user->name . PHP_EOL;
echo 'Roles: ' . implode(', ', $user->getRoleNames()->toArray()) . PHP_EOL;
echo 'Business Instance: ' . ($user->businessInstance ? $user->businessInstance->nombre : 'none') . PHP_EOL;

$sidebar = new App\Support\Sidebar();
echo 'Menu items count: ' . count($sidebar->menu()) . PHP_EOL;
print_r($sidebar->menu());