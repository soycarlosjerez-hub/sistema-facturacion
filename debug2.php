<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Ver todos los usuarios
$users = \App\Models\User::all();
foreach ($users as $user) {
    echo "Usuario: " . $user->email . " | Role: " . $user->role . " | Business Instance ID: " . ($user->business_instance_id ?? 'NULL') . PHP_EOL;
}

// Ver business instances
$instances = \App\Models\BusinessInstance::all();
foreach ($instances as $inst) {
    echo "Instancia: " . $inst->nombre . " (ID: " . $inst->id . ") | Business Type: " . $inst->business_type_id . PHP_EOL;
    if ($inst->business_type_id) {
        $btype = \App\Models\BusinessType::find($inst->business_type_id);
        if ($btype) {
            echo "  Business Type: " . $btype->nombre . " | Config: " . json_encode($btype->config) . PHP_EOL;
        }
    }
}

// Ver servicios por tenant
echo PHP_EOL . "=== SERVICIOS POR TENANT ===" . PHP_EOL;
$servicios = \App\Models\LavaderoServicio::all();
foreach ($servicios as $s) {
    echo "Tenant: " . $s->tenant_id . " | Servicio: " . $s->nombre . " | Activo: " . ($s->activo ? 'si' : 'no') . PHP_EOL;
}
?>