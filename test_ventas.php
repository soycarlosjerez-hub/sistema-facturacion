<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simular el usuario logueado
$user = \App\Models\User::where('email', 'lavador@gatonegro.do')->first();
\Illuminate\Support\Facades\Auth::login($user);

echo "Usuario logueado: " . auth()->user()->email . PHP_EOL;
echo "Business Instance ID: " . auth()->user()->business_instance_id . PHP_EOL;

// Probar SaleService::getCreationData()
$saleService = app(\App\Services\SaleService::class);
$data = $saleService->getCreationData();

echo "facturacionModo: " . ($data['facturacionModo'] ?? 'no definido') . PHP_EOL;
echo "modoProductosYServicios: " . ($data['modoProductosYServicios'] ?? 'no definido') . PHP_EOL;
echo "productosJs count: " . count($data['productosJs'] ?? []) . PHP_EOL;
echo "serviciosJs count: " . count($data['serviciosJs'] ?? []) . PHP_EOL;

if (!empty($data['serviciosJs'])) {
    echo "Primer servicio: " . $data['serviciosJs'][0]['nombre'] . PHP_EOL;
}
?>