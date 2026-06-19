<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'owner@sistema-facturacion.com')->first();
Auth::login($user);

echo 'User: ' . $user->name . PHP_EOL;
echo 'Roles: ' . implode(', ', $user->getRoleNames()->toArray()) . PHP_EOL;

// Manually test the $mod function
$tipoNegocio = session('business_type_slug');
echo "Session business_type_slug: " . ($tipoNegocio ?? 'NOT SET') . PHP_EOL;

if (!$tipoNegocio) {
    $tipoNegocio = 'restaurante'; // default
}
echo "Using tipoNegocio: $tipoNegocio" . PHP_EOL;

$visibles = App\Models\BusinessType::getModulosVisibles($tipoNegocio);
echo "Visible modules: " . implode(', ', $visibles) . PHP_EOL;

$mod = fn(string $key) => $user->businessInstance?->isModuloVisible($key) ?? in_array($key, $visibles);

echo "mod('listas-precio'): " . ($mod('listas-precio') ? 'YES' : 'NO') . PHP_EOL;
echo "mod('inventario'): " . ($mod('inventario') ? 'YES' : 'NO') . PHP_EOL;
echo "mod('compras'): " . ($mod('compras') ? 'YES' : 'NO') . PHP_EOL;

// Check the owner permissions
$can = fn(string $p) => $user->hasRole('admin') || $user->hasRole('admin-business') || $user->can($p);
echo "can('productos.view'): " . ($can('productos.view') ? 'YES' : 'NO') . PHP_EOL;
echo "can('listas-precio.view'): " . ($can('listas-precio.view') ? 'YES' : 'NO') . PHP_EOL;
echo "can('compras.view'): " . ($can('compras.view') ? 'YES' : 'NO') . PHP_EOL;

// Test the condition for Inventario section
$hasInventarioItems = false;
if ($mod('inventario') && $can('productos.view')) {
    echo "Inventario: productos.view = YES" . PHP_EOL;
    $hasInventarioItems = true;
}
if ($mod('listas-precio') && $can('listas-precio.view')) {
    echo "Inventario: listas-precio.view = YES" . PHP_EOL;
    $hasInventarioItems = true;
}
if ($mod('compras') && $can('compras.view')) {
    echo "Inventario: compras.view = YES" . PHP_EOL;
    $hasInventarioItems = true;
}
echo "hasInventarioItems: " . ($hasInventarioItems ? 'YES' : 'NO') . PHP_EOL;