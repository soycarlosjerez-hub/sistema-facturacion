<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check business type modules
$bt = App\Models\BusinessType::where('slug', 'restaurante')->first();
if ($bt) {
    echo "Business Type: " . $bt->slug . PHP_EOL;
    $modules = $bt->visibleModules();
    foreach ($modules as $m) {
        echo $m->modulo_key . ' - visible: ' . ($m->visible ? 'yes' : 'no') . PHP_EOL;
    }
}

// Check business instance modules
$bi = App\Models\BusinessInstance::find(1);
if ($bi) {
    echo PHP_EOL . "Business Instance Modules:" . PHP_EOL;
    $modules = $bi->modules;
    foreach ($modules as $m) {
        echo $m->modulo_key . ' - visible: ' . ($m->visible ? 'yes' : 'no') . PHP_EOL;
    }
}

// Test isModuloVisible directly
$bi = App\Models\BusinessInstance::find(1);
if ($bi) {
    echo PHP_EOL . "Testing isModuloVisible:" . PHP_EOL;
    echo "listas-precio: " . ($bi->isModuloVisible('listas-precio') ? 'YES' : 'NO') . PHP_EOL;
    echo "inventario: " . ($bi->isModuloVisible('inventario') ? 'YES' : 'NO') . PHP_EOL;
    echo "compras: " . ($bi->isModuloVisible('compras') ? 'YES' : 'NO') . PHP_EOL;
}