<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'whilepon@sistema-facturacion.com')->first();
echo 'User: ' . ($user ? $user->name : 'NOT FOUND') . PHP_EOL;
if ($user) {
    echo 'Roles: ' . implode(', ', $user->getRoleNames()->toArray()) . PHP_EOL;
    echo 'Business Instance: ' . ($user->businessInstance ? $user->businessInstance->nombre : 'none') . PHP_EOL;
    echo 'Business Instance ID: ' . ($user->businessInstance ? $user->businessInstance->id : 'none') . PHP_EOL;
    if ($user->businessInstance) {
        echo 'Is listas-precio visible: ' . ($user->businessInstance->isModuloVisible('listas-precio') ? 'YES' : 'NO') . PHP_EOL;
        echo 'Is inventario visible: ' . ($user->businessInstance->isModuloVisible('inventario') ? 'YES' : 'NO') . PHP_EOL;
        echo 'Is compras visible: ' . ($user->businessInstance->isModuloVisible('compras') ? 'YES' : 'NO') . PHP_EOL;
    }
}