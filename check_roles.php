<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = \App\Models\User::distinct()->pluck('role');
echo "Roles existentes:\n";
foreach ($roles as $r) {
    echo "  - " . $r . "\n";
}
?>