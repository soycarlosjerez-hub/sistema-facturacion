<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = DB::select("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'productos'");
echo "productos columns:\n";
print_r($columns);

echo "\n\nChecking tenant_id specifically:\n";
$hasTenantId = collect($columns)->contains(fn($c) => strtolower($c->COLUMN_NAME) === 'tenant_id');
echo "Has tenant_id: " . ($hasTenantId ? 'YES' : 'NO') . "\n";