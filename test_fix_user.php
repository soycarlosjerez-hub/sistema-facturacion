<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'whilepon@sistema-facturacion.com')->first();
if ($user) {
    echo 'Before: ' . ($user->business_instance_id ?? 'NULL') . PHP_EOL;
    $user->business_instance_id = 1;
    $user->save();
    echo 'Updated to: ' . $user->business_instance_id . PHP_EOL;
    
    $user->refresh();
    if ($user->businessInstance) {
        echo 'Business Instance: ' . $user->businessInstance->nombre . PHP_EOL;
        echo 'Is listas-precio visible: ' . ($user->businessInstance->isModuloVisible('listas-precio') ? 'YES' : 'NO') . PHP_EOL;
        echo 'Is inventario visible: ' . ($user->businessInstance->isModuloVisible('inventario') ? 'YES' : 'NO') . PHP_EOL;
    }
}