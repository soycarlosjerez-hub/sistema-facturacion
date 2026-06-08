<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Cliente;
use App\Models\Venta;

$clientes = Cliente::all();
foreach ($clientes as $cliente) {
    $ventasPendientes = Venta::where('cliente_id', $cliente->id)
        ->whereIn('estado', ['pendiente', 'cuenta_abierta'])
        ->get();
    
    $totalDeudaReal = 0;
    foreach ($ventasPendientes as $v) {
        $pagado = $v->pagos()->sum('monto');
        $totalDeudaReal += ($v->total - $pagado);
    }
    
    if ($cliente->balance_pendiente != $totalDeudaReal) {
        echo "Corrigiendo balance de {$cliente->nombre}: {$cliente->balance_pendiente} -> {$totalDeudaReal}\n";
        $cliente->balance_pendiente = $totalDeudaReal;
        $cliente->save();
    }
}
echo "Sincronización completada.\n";
