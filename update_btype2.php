<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$type = \App\Models\BusinessType::find(1);
$type->config = ['facturacion_modo' => 'productos'];
$type->save();
echo 'Config updated to: ' . json_encode($type->config) . PHP_EOL;

// Also update instance 10 (Gato Negro) to have the same config for consistency
$instance = \App\Models\BusinessInstance::where('slug', 'gato-negro-santiagodr')->first();
if ($instance) {
    // We can't directly change the business type config per instance, 
    // but the config is shared via the business type
    echo "Instance 10 business_type_id: " . $instance->business_type_id . PHP_EOL;
}
?>