<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\SesionCaja;
use App\Services\SaleService;
use Illuminate\Support\Facades\Auth;

// Find a user to simulate
$user = User::with(['businessInstance.businessType'])->whereNotNull('business_instance_id')->first();

if (!$user) {
    die("No user found with business_instance_id\n");
}

echo "User: {$user->email}\n";
echo "Business Instance: {$user->business_instance_id}\n";
echo "Role: {$user->role}\n";

// Simulate auth
Auth::shouldReceiving(function() use ($user) { return $user; });
Auth::shouldReceive('id')->andReturn($user->id);
Auth::shouldReceive('user')->andReturn($user);
Auth::shouldReceive('check')->andReturn(true);

// Get creation data
try {
    $saleService = app(SaleService::class);
    $data = $saleService->getCreationData();
    
    echo "\n=== Creation Data ===\n";
    echo "facturacionModo: " . json_encode($data['facturacionModo'] ?? 'MISSING') . "\n";
    echo "modoObras: " . json_encode($data['modoObras'] ?? 'MISSING') . "\n";
    echo "productos count: " . count($data['productosJs'] ?? []) . "\n";
    echo "clientes count: " . count($data['clientesJs'] ?? []) . "\n";
    echo "almacenes count: " . count($data['almacenes'] ?? []) . "\n";
    
    // Check if sesion exists
    if ($data['sesion']) {
        echo "Sesion ID: " . $data['sesion']->id . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "At: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
