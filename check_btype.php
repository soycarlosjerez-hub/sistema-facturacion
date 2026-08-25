<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$type = \App\Models\BusinessType::first();
echo "Business Type:\n";
echo "  ID: " . $type->id . "\n";
echo "  Nombre: " . $type->nombre . "\n";
echo "  Config: " . json_encode($type->config ?? null) . "\n";
?>