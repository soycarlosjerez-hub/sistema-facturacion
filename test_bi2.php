<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'owner@sistema-facturacion.com')->first();
echo 'User: ' . $user->name . PHP_EOL;
echo 'Business Instance: ' . ($user->businessInstance ? $user->businessInstance->nombre : 'none') . PHP_EOL;
echo 'Business Instance ID: ' . ($user->businessInstance ? $user->businessInstance->id : 'none') . PHP_EOL;

echo "Session business_type_slug: " . (session('business_type_slug') ?? 'NOT SET') . PHP_EOL;

if ($user->businessInstance) {
    echo 'Is listas-precio visible: ' . ($user->businessInstance->isModuloVisible('listas-precio') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Is inventario visible: ' . ($user->businessInstance->isModuloVisible('inventario') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Is compras visible: ' . ($user->businessInstance->isModuloVisible('compras') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Is proveedores visible: ' . ($user->businessInstance->isModuloVisible('proveedores') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Is kardex visible: ' . ($user->businessInstance->isModuloVisible('kardex') ? 'YES' : 'NO') . PHP_EOL;
    
    echo "Business Type: " . ($user->businessInstance->businessType ? $user->businessInstance->businessType->slug : 'none') . PHP_EOL;
    
    // Check visible modules from business type
    if ($user->businessInstance->businessType) {
        $visibles = $user->businessInstance->businessType->isModuloVisible('listas-precio') ? 'YES' : 'NO';
        echo "BusinessType listas-precio visible: $visibles" . PHP_EOL;
        
        $visibles = $user->businessInstance->businessType->isModuloVisible('inventario') ? 'YES' : 'NO';
        echo "BusinessType inventario visible: $visibles" . PHP_EOL;
        
        // Get all visible modules from business type
        $visibles = App\Models\BusinessType::getModulosVisibles($user->businessInstance->businessType->slug);
        echo "All visible modules: " . implode(', ', $visibles) . PHP_EOL;
    }
} else {
    echo "Owner has no business instance. Using session fallback..." . PHP_EOL;
    $tipoNegocio = session('business_type_slug');
    echo "Session business_type_slug: " . ($tipoNegocio ?? 'NOT SET') . PHP_EOL;
    
    if (!$tipoNegocio) {
        $tipoNegocio = 'restaurante'; // default
        echo "Using default: $tipoNegocio" . PHP_EOL;
    }
    
    $visibles = App\Models\BusinessType::getModulosVisibles($tipoNegocio);
    echo "Visible modules for $tipoNegocio: " . implode(', ', $visibles) . PHP_EOL;
}