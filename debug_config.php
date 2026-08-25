<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Verificar configuración actual
$user = \App\Models\User::first();
echo "Usuario: " . $user->email . PHP_EOL;
echo "Business Instance ID: " . $user->business_instance_id . PHP_EOL;

$instance = \App\Models\BusinessInstance::find($user->business_instance_id);
echo "Instancia: " . $instance->nombre . PHP_EOL;
echo "Business Type ID: " . $instance->business_type_id . PHP_EOL;

$btype = \App\Models\BusinessType::find($instance->business_type_id);
echo "Business Type: " . $btype->nombre . PHP_EOL;
echo "Config: " . json_encode($btype->config) . PHP_EOL;

// Verificar servicios
$servicios = \App\Models\LavaderoServicio::where('tenant_id', $user->business_instance_id)->where('activo', true)->get();
echo "Servicios activos: " . $servicios->count() . PHP_EOL;
foreach ($servicios as $s) {
    echo "  - " . $s->nombre . " (ID: " . $s->id . ", activo: " . ($s->activo ? 'si' : 'no') . ")" . PHP_EOL;
}

// Verificar productos
$productos = \App\Models\Producto::where('tenant_id', $user->business_instance_id)->where('activo', true)->get();
echo "Productos activos: " . $productos->count() . PHP_EOL;
?>