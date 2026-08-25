<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Asignar usuario a la instancia 11 (Lavadero & Tienda Demo) que tiene modo mixto
$user = \App\Models\User::where('email', 'lavador@gatonegro.do')->first();
$user->business_instance_id = 11;
$user->save();

echo "Usuario actualizado: business_instance_id = 11" . PHP_EOL;

// Verificar config de la instancia 11
$instance = \App\Models\BusinessInstance::find(11);
echo "Instancia: " . $instance->nombre . PHP_EOL;

$btype = \App\Models\BusinessType::find($instance->business_type_id);
echo "Business Type: " . $btype->nombre . " | Config: " . json_encode($btype->config) . PHP_EOL;

// Verificar servicios
$servicios = \App\Models\LavaderoServicio::where('tenant_id', 11)->where('activo', true)->get();
echo "Servicios activos para tenant 11: " . $servicios->count() . PHP_EOL;
?>